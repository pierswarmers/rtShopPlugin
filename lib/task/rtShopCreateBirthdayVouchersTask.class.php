<?php
/*
 * This file is part of the reditype package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopCreateBirthdayVoucherTask
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

class rtShopCreateBirthdayVouchersTask extends sfDoctrineBaseTask
{
  private $_debug_verbose = false;
  private $_birthday_vouchers = 0;

  /**
   * Configure
   *
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('birthday-range-start', sfCommandArgument::OPTIONAL, 'Startdate of birthday range for which vouchers have to be generated'),
      new sfCommandArgument('birthday-range-end', sfCommandArgument::OPTIONAL, 'Enddate of birthday range for which vouchers have to be generated'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
    ));

    $this->namespace        = 'rt';
    $this->name             = 'shop-create-birthday-vouchers';
    $this->briefDescription = 'Create birthdays vouchers and send email to users with voucher code';
    $this->detailedDescription = <<<EOF
The [rt:shop-create-birthday-vouchers] task creates vouchers based on birthdays of registered users.

Configure in app.yml:

all:
  rt:
    shop_birthday_voucher_type:    dollarOff   # Either 'percentageOff' or 'dollarOff'
    shop_birthday_voucher_value:   10          # Voucher reduction value (e.g 10 = $10)

To generate vouchers for a specific birthday use:

[./symfony rt:shop-create-birthday-vouchers yyyy-mm-dd]

To generate vouchers for a range of birthdays use:

[./symfony rt:shop-create-birthday-vouchers yyyy-mm-dd yyyy-mm-dd]

Vouchers are then generated and an email sent to the users with the birthdays specified.
Another email is sent to the shop administrator with the details of the created vouchers.
EOF;
  }

  /**
   * Execute function
   *
   * @param array $arguments Arguments
   * @param array $options  Options
   */
  protected function execute($arguments = array(), $options = array())
  {
    $configuration = ProjectConfiguration::getApplicationConfiguration ('frontend', 'dev', false);
    sfContext::createInstance($configuration);
    $configuration->loadHelpers('Partial');
    $this->configuration->loadHelpers('Number','I18N');

    $range_from = $arguments['birthday-range-start'];
    $range_to = $arguments['birthday-range-end'];

    if($range_from == NULL && $range_to == NULL)
    {
      throw new sfCommandException('At least one date has to be defined. Format: yyyy-mm-dd (e.g. 2010-07-01)');
    }

    $date_pattern = '/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])?$/';
    if (!preg_match($date_pattern, $range_from))
    {
      throw new sfCommandException(sprintf('Range start: Invalid datetime pattern "%s". Format: yyyy-mm-dd (e.g. 2010-07-01)', $range_from));
    }

    if($range_to != NULL)
    {
      if (!preg_match($date_pattern, $range_to))
      {
        throw new sfCommandException(sprintf('Range end: Invalid datetime pattern "%s". Format: yyyy-mm-dd (e.g. 2010-07-01)', $range_to));
      }
    }

    if (!sfConfig::has('app_rt_shop_birthday_voucher_type'))
    {
      throw new sfCommandException('app_rt_shop_birthday_voucher_type has to be set');
    }

    if (!sfConfig::has('app_rt_shop_birthday_voucher_value'))
    {
      throw new sfCommandException('app_rt_shop_birthday_voucher_value has to be set');
    }

    // Database actions
    $databaseManager = new sfDatabaseManager($this->configuration);
    if($range_to == NULL)
    {
      $voucher_title = 'Birthday voucher. Date of birth:' . $range_from;
      $q = Doctrine_Query::create()->from('rtGuardUser u');
      $q->andWhere('DAY(u.date_of_birth) = ?', date("j",strtotime($range_from)));
      $q->andWhere('MONTH(u.date_of_birth) = ?', date("n",strtotime($range_from)));
    }
    else
    {
      $voucher_title = 'Birthday voucher. Range => From:' . $range_from . ' to' . $range_to;
      $q = Doctrine::getTable('rtGuardUser')->getBirthdayRestrictionQuery($range_from,$range_to);
    }
    $users = $q->execute();

    $batch_reference = rtShopVoucherToolkit::generateVoucherCode();
    $voucher_details = array('date_from' => NULL,
                    'date_to' => NULL,
                    'reduction_type' => sfConfig::has('app_rt_shop_birthday_voucher_type'),
                    'reduction_value' => sfConfig::has('app_rt_shop_birthday_voucher_value'),
                    'title' => $voucher_title,
                    'type' => 'rtShopVoucher',
                    'batch_reference' => $batch_reference,
                    'count' => 1,
                    'mode' => 'Single',
                    'total_from' => NULL,
                    'total_to' => NULL,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                    'code' => '');

    if(!$this->createBatchVouchers($voucher_details, count($users)))
    {
      throw new sfCommandException('Batch vouchers could not be created');
    }

    $q = Doctrine_Query::create()
         ->from('rtShopVoucher v')
         ->andWhere('v.batch_reference = ?', $batch_reference);
    $voucher = $q->fetchArray();
    
    $this->_birthday_vouchers = count($users);
    if(sfConfig::get('app_rt_shop_birthday_voucher_type') === 'percentageOff')
    {
      $formatted_value = sfConfig::get('app_rt_shop_birthday_voucher_value').'%';
    }
    else
    {
      $formatted_value = format_currency(sfConfig::get('app_rt_shop_birthday_voucher_value'), sfConfig::get('app_rt_currency', 'AUD'));
    }
    if ($this->_debug_verbose) {
      $this->log('----------------------------------------------');
      $this->log('--- Create vouchers for birthday specified ---');
      $this->log('----------------------------------------------');

      if (count($users) > 0)
      {
        $i = 0;
        foreach ($users as $user) {
          $this->logSection('shop-create-birthday-voucher', sprintf('Date of birth: [%s] // Code: [%s] // Last_name: [%s]',$user->getDateOfBirth(),$voucher[$i]['code'],$user->getLastName()));
          if($user->getEmailAddress() != '')
          {
            $subject = sprintf('Birthday present: Voucher code: #%s', $voucher[$i]['code'].' ['.$formatted_value.']');
            $body = 'Happy Birthday!'."<br/><br/>".'Voucher code: #'.$voucher[$i]['code'].' with a value of '.$formatted_value;
            $this->sendSwiftMail(sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com'),$user->getEmailAddress(),$subject,$body,$voucher[$i]['code']);
          }
          $i++;
        }
        $this->logSection('shop-create-birthday-voucher', sprintf('Total created birthday vouchers: [%s]',$this->_birthday_vouchers));
      } else {
        $this->logSection('shop-create-birthday-voucher', 'No users in chosen birthday range');
      }
    } else {
      if (count($users) > 0)
      {
        $i = 0;
        foreach ($users as $user) {
          if($user->getEmailAddress() != '')
          {
            $subject = sprintf('Birthday present: Voucher code: #%s', $voucher[$i]['code'].' ['.$formatted_value.']');
            $body = 'Happy Birthday!'."<br/><br/>".'Voucher code: #'.$voucher[$i]['code'].' with a value of '.$formatted_value;
            $this->sendSwiftMail(sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com'),$user->getEmailAddress(),$subject,$body,$voucher[$i]['code']);
          }
          $i++;
        }
      }
    }
    // Send email to admin
    if (count($users) > 0)
    {
      $admin_address = sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com');
      $subject = sprintf('Vouchers Generation: Birthday %s %s',$range_from,($range_to != NULL) ? 'to '.$range_to : '');
      $body = 'Vouchers created: '.count($users)."<br/>";
      $body .= 'Batch reference: #'.$batch_reference."<br/>";
      if($range_to == NULL)
      {
        $body .= 'For birthday: '.$range_from."<br/>";
      }
      else
      {
        $body .= 'For birthday range: '.$range_from.' to '.$range_to."<br/>";
      }
      $body .= 'Voucher value: '.$formatted_value."<br/>";
      $body .= 'Date created: '.date("Y-m-d");
      $this->sendSwiftMail($admin_address,$admin_address,$subject,$body,$voucher[$i]['code']);
    }
  }

  /**
   * Send email
   *
   * @param object $from From address
   * @param string $to To address
   * @param string $subject Email subject
   * @param string $body Email body content
   * @param string $code Voucher code
   */
  protected function sendSwiftMail($from,$to,$subject,$body,$code)
  {
    $message = Swift_Message::newInstance()
      ->setContentType('text/html')
      ->setFrom($from)
      ->setTo($to)
      ->setSubject($subject)
      ->setBody($body);
      //->setBody($this->getPartial('rtShopOrderAdmin/email_invoice', array('rt_shop_order' => $cm->getOrder())));
    $this->getMailer()->send($message);
  }

  /**
   * Batch create voucher for users in birthday range
   *
   * @param array $values Voucher details
   * @param integer $batchsize Voucher batch size
   * @return boolean True if successsful
   */
  protected function createBatchVouchers($values,$batchsize)
  {
    try {
      rtShopVoucherToolkit::generateBatch($values,$batchsize,false);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }
}