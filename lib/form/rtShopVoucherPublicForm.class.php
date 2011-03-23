<?php
/*
 * This file is part of the rtShopPlugin package.
 * 
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopVoucherPublicForm
 *
 * @package    rtShopPlugin
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopVoucherPublicForm extends sfForm
{
  public function setup()
  {
    parent::setup();

    // Helpers
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('Number'));

    // Form format
    $this->getWidgetSchema()->setFormFormatterName(sfConfig::get('app_rt_public_form_formatter_name', 'RtList'));

    // Voucher amount selection
    $options = sfConfig::get('app_rt_shop_gift_voucher_amount', array(10,20,25,30,40,50,75,100,150,200,300));

    // Add currency definition to amount
    $amounts = array();
    $amounts[''] = '--';
    foreach ($options as $key => $item)
    {
      $amounts[$item] = format_currency($item, sfConfig::get('app_rt_currency', 'AUD'));
    }

    // Widgets
    $this->widgetSchema['reduction_value'] = new sfWidgetFormSelect(array('choices' => $amounts));
    $this->widgetSchema['first_name']      = new sfWidgetFormInput(array(), array());
    $this->widgetSchema['last_name']       = new sfWidgetFormInput(array(), array());
    $this->widgetSchema['email_address']   = new sfWidgetFormInput(array(), array());
    $this->widgetSchema['message']         = new sfWidgetFormTextarea(array(),array());

    // Validators
    $this->setValidator('reduction_value', new sfValidatorChoice(array('choices' => array_keys($amounts), 'required' => true),array('required' =>'Please make a selection')));
    $this->setValidator('first_name',      new sfValidatorString(array('required' => true),array('required' => 'Please provide a first name')));
    $this->setValidator('last_name',       new sfValidatorString(array('required' => true),array('required' => 'Please provide a last name')));
    $this->setValidator('email_address',   new sfValidatorEmail(array('required' => true),array('required' => 'Please provide a valid email address')));
    $this->setValidator('message',         new sfValidatorString(array('required' => false, 'max_length' => 255),array('max_length' => 'Message is too long (%max_length% characters max.)')));

    // Add labels
    $this->widgetSchema->setLabel('reduction_value', "Gift Voucher Amount");
    $this->widgetSchema->setLabel('first_name', "Recipient First Name");
    $this->widgetSchema->setLabel('last_name', "Recipient Last Name");
    $this->widgetSchema->setLabel('email_address', "Recipient Email");

    // Help texts
    $this->widgetSchema->setHelp('first_name', 'Required');
    $this->widgetSchema->setHelp('last_name', 'Required');
    $this->widgetSchema->setHelp('email_address', 'Required - The voucher will be sent here');

    $this->widgetSchema->setNameFormat('rt_shop_voucher[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }
}