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
  private $_debug_verbose = true;
  private $_birthday_vouchers = 0;
  private $_formatted_value = '';
  private $_batch_reference = '';
  private $_range_from;
  private $_range_to;

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
    $this->configuration->loadHelpers('Number','I18N','Partial');

    $range_from = $arguments['birthday-range-start'];
    $this->_range_from = $range_from;
    $range_to = $arguments['birthday-range-end'];
    $this->_range_to = $range_to;
    
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

    $this->_batch_reference = rtShopVoucherToolkit::generateVoucherCode();
    $voucher_details = array('date_from' => NULL,
                    'date_to' => NULL,
                    'reduction_type' => sfConfig::has('app_rt_shop_birthday_voucher_type'),
                    'reduction_value' => sfConfig::has('app_rt_shop_birthday_voucher_value'),
                    'title' => $voucher_title,
                    'type' => 'rtShopVoucher',
                    'batch_reference' => $this->_batch_reference,
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
         ->andWhere('v.batch_reference = ?', $this->_batch_reference);
    $voucher = $q->fetchArray();
    
    $this->_birthday_vouchers = count($users);
    if(sfConfig::get('app_rt_shop_birthday_voucher_type') === 'percentageOff')
    {
      $this->_formatted_value = sfConfig::get('app_rt_shop_birthday_voucher_value').'%';
    }
    else
    {
      $this->_formatted_value = format_currency(sfConfig::get('app_rt_shop_birthday_voucher_value'), sfConfig::get('app_rt_currency', 'AUD'));
    }

    if (count($users) > 0)
    {
      if ($this->_debug_verbose) {
        $this->log('--------------------------------');
        $this->log('--- Create birthday vouchers ---');
        $this->log('--------------------------------');
      }
      $i = 0;
      foreach ($users as $user) {
        if ($this->_debug_verbose) {
          $this->logSection('shop-create-birthday-voucher', sprintf('Date of birth: [%s] // Code: [%s] // Last_name: [%s]',$user->getDateOfBirth(),$voucher[$i]['code'],$user->getLastName()));
        }
        if($user->getEmailAddress() != '')
        {
          // Send mail to user
          $this->notifyUser($user, $voucher[$i]['code']);
        }
        $i++;
      }
      if ($this->_debug_verbose) {
        $this->logSection('shop-create-birthday-voucher', sprintf('Total created birthday vouchers: [%s]',$this->_birthday_vouchers));
      }
    }
    else {
      if ($this->_debug_verbose) {
        $this->logSection('shop-create-birthday-voucher', 'No users in chosen birthday range');
      }
    }
    // Send mail to administrator
    if(sfConfig::get('app_rt_shop_order_admin_email'))
    {
      $this->notifyAdministrator($users);
    }
  }

  /**
   * Notify the user about birthday voucher
   *
   * @param sfGuardUser $user
   */
  protected function notifyUser(sfGuardUser $user, $code)
  {
    $vars = array('user' => $user);
    $vars['code'] = $code;
    $vars['value'] = $this->_formatted_value;

    $message_html = get_partial('rtShopVoucherAdmin/email_birthdayvoucher_user_html', $vars);
    $message_html = get_partial('rtEmail/layout_html', array('content' => $message_html));

    $message_plain = get_partial('rtShopVoucherAdmin/email_birthdayvoucher_user_plain', $vars);
    $message_plain = get_partial('rtEmail/layout_plain', array('content' => html_entity_decode($message_plain)));

    $from = sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com');

    $message = Swift_Message::newInstance()
            ->setFrom($from)
            ->setTo($user->getEmailAddress())
            ->setSubject(sprintf('Birthday voucher: Code: #%s', $code.' with a value of: '.$this->_formatted_value))
            ->setBody($message_html, 'text/html')
            ->addPart($message_plain, 'text/plain');

    $this->getMailer()->send($message);
  }

  /**
   * Notify the administrator about birthday vouchers
   *
   * @param sfGuardUser $user
   */
  protected function notifyAdministrator($users)
  {
    $vars = array('users' => $users);
    $vars['range_from'] = $this->_range_from;
    $vars['range_to'] = $this->_range_to;
    $vars['batch_reference'] = $this->_batch_reference;
    $vars['value'] = $this->_formatted_value;

    $message_html = get_partial('rtShopVoucherAdmin/email_birthdayvoucher_admin_html', $vars);
    $message_html = get_partial('rtEmail/layout_html', array('content' => $message_html));

    $message_plain = get_partial('rtShopVoucherAdmin/email_birthdayvoucher_admin_plain', $vars);
    $message_plain = get_partial('rtEmail/layout_plain', array('content' => html_entity_decode($message_plain)));

    $admin_address = sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com');

    $message = Swift_Message::newInstance()
            ->setFrom($admin_address)
            ->setTo($admin_address)
            ->setSubject(sprintf('Birthday vouchers generation: Birthday %s %s',$this->_range_from,($this->_range_to != NULL) ? 'to '.$this->_range_to : ''))
            ->setBody($message_html, 'text/html')
            ->addPart($message_plain, 'text/plain');

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