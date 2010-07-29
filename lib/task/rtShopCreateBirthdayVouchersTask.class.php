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
  private $_batch_reference = '';

  /**
   * Configure
   *
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('day', sfCommandArgument::OPTIONAL, 'Startdate of birthday range for which vouchers have to be generated'),
      new sfCommandArgument('month', sfCommandArgument::OPTIONAL, 'Enddate of birthday range for which vouchers have to be generated'),
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
    shop_birthday_voucher:
      reduction_type:                  dollarOff               # Either 'percentageOff' or 'dollarOff'
      reduction_value:                 10                      # Voucher reduction value (e.g 10 = $10)

To generate vouchers for a specific birthday use:

[./symfony rt:shop-create-birthday-vouchers day month]

Note: If [day] and [month] is not specified than the current date is used!

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

    // If no day/month set use current date
    $day = is_null($arguments['day']) ? date("j",strtotime(date("Y-m-d H:i:s"))) : $arguments['day'];
    $month = is_null($arguments['month']) ? date("n",strtotime(date("Y-m-d H:i:s"))) : $arguments['month'];

    if (!sfConfig::has('app_rt_shop_birthday_voucher'))
    {
      throw new sfCommandException('app_rt_shop_birthday_voucher has to be set');
    }
    
    $config = sfConfig::get('app_rt_shop_birthday_voucher');

    $config['title'] = isset($config['title']) ? $config['title'] : 'Birthday Voucher';
    $config['reduction_type'] = isset($config['reduction_type']) ? $config['reduction_type'] : 'percentageOff';
    $config['reduction_value'] = isset($config['reduction_value']) ? $config['reduction_value'] : '0';

    // Database actions
    $databaseManager = new sfDatabaseManager($this->configuration);

    $q = Doctrine_Query::create()->from('rtGuardUser u')
          ->andWhere('DAY(u.date_of_birth) = ?', $day)
          ->andWhere('MONTH(u.date_of_birth) = ?', $month);
    $users = $q->fetchArray();
    
    $this->_batch_reference = rtShopVoucherToolkit::generateVoucherCode();
    $voucher_details = array('date_from' => NULL,
                    'date_to' => isset($config['date_to']) ? $config['date_to'] : NULL,
                    'reduction_type' =>  $config['reduction_type'],
                    'reduction_value' =>  $config['reduction_value'],
                    'title' =>  $config['title'],
                    'type' => 'rtShopVoucher',
                    'batch_reference' => $this->_batch_reference,
                    'count' => 1,
                    'mode' => 'Single',
                    'total_from' =>  isset($config['total_from']) ? $config['total_from'] : NULL,
                    'total_to' =>  isset($config['total_to']) ? $config['total_to'] : NULL,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                    'code' => '');

    $this->createBatchVouchers($voucher_details, count($users));

    $q = Doctrine_Query::create()
         ->from('rtShopVoucher v')
         ->andWhere('v.batch_reference = ?', $this->_batch_reference);
    
    $voucher = $q->fetchArray();

    if (count($users) > 0)
    {
      $this->log('--------------------------------');
      $this->log('--- Create birthday vouchers ---');
      $this->log('--------------------------------');
      $i = 0;
      foreach ($users as $user) {
        $this->logSection('shop-create-birthday-voucher', sprintf('Date of birth: [%s] // Code: [%s] // Last_name: [%s]',$user['date_of_birth'],$voucher[$i]['code'],$user['last_name']));
        
        if($user['email_address'] != '')
        {
          $this->notifyUser($user, $voucher[$i]['code'], $config);
        }
        $i++;
      }
      $this->logSection('shop-create-birthday-voucher', sprintf('Total created birthday vouchers: [%s]',count($users)));
    }
    else
    {
      $this->logSection('shop-create-birthday-voucher', 'No users in chosen birthday range');
    }
    $this->notifyAdministrator($users, $config);
  }

  /**
   * Notify the user about birthday voucher
   *
   * @param sfGuardUser $user
   */
  protected function notifyUser($user, $code, $config)
  {
    $vars = array('user' => $user);
    
    $vars['code'] = $code;
    $vars['value'] =  $this->getFormattedReduction($config);
    $vars['voucher_config'] =  $config;
    $vars['formatted_reduction'] = $this->getFormattedReduction($config);
    
    $message_html = get_partial('rtShopVoucherAdmin/email_birthdayvoucher_user_html', $vars);
    $message_html = get_partial('rtEmail/layout_html', array('content' => $message_html));

    $message_plain = get_partial('rtShopVoucherAdmin/email_birthdayvoucher_user_plain', $vars);
    $message_plain = get_partial('rtEmail/layout_plain', array('content' => html_entity_decode($message_plain)));

    $from = sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com');

    $message = Swift_Message::newInstance()
            ->setFrom($from)
            ->setTo($user['email_address'])
            ->setSubject(sprintf('Happy Birthday %s! Here\'s your %s off voucher - #%s', $user['first_name'],  $this->getFormattedReduction($config), $code))
            ->setBody($message_html, 'text/html')
            ->addPart($message_plain, 'text/plain');

    $this->getMailer()->send($message);
  }

  /**
   * Notify the administrator about birthday vouchers
   *
   * @param sfGuardUser $user
   */
  protected function notifyAdministrator($users, $config)
  {
    if(!sfConfig::has('app_rt_shop_order_admin_email'))
    {
      return;
    }

    $vars = array('users' => $users);
    
    $vars['batch_reference'] = $this->_batch_reference;
    $vars['value'] = $this->getFormattedReduction($config);
    $vars['voucher_config'] =  $config;

    $message_html = get_partial('rtShopVoucherAdmin/email_birthdayvoucher_admin_html', $vars);
    $message_html = get_partial('rtEmail/layout_html', array('content' => $message_html));

    $message_plain = get_partial('rtShopVoucherAdmin/email_birthdayvoucher_admin_plain', $vars);
    $message_plain = get_partial('rtEmail/layout_plain', array('content' => html_entity_decode($message_plain)));

    $admin_address = sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com');

    $message = Swift_Message::newInstance()
            ->setFrom($admin_address)
            ->setTo($admin_address)
            ->setSubject('Birthday Vouchers Sent')
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
    } catch (Exception $e) {
      throw new sfCommandException('generateBatch() failed.');
    }
  }

  private function getFormattedReduction($config)
  {
    if($config['reduction_type'] === 'percentageOff')
    {
      return $config['reduction_value'].'%';
    }
    else
    {
      return format_currency($config['reduction_value'], sfConfig::get('app_rt_currency', 'AUD'));
    }
  }
}