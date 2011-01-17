<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopVoucherCodeForm
 *
 * @package    rtShopPlugin
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopCreditCardPaymentForm extends sfForm
{
  
  public function setup()
  {
    parent::setup();
    
    // Credit card input fields
    $this->widgetSchema['cc_type']    = new sfWidgetFormSelect(array('choices' => sfConfig::get('app_rt_shop_payment_methods',array('Mastercard' => 'Mastercard', 'Visa' => 'Visa'))));
    $this->widgetSchema['cc_number']  = new sfWidgetFormInput(array(), array('class'=>'text'));
    $this->widgetSchema['cc_name']    = new sfWidgetFormInput(array(), array('class'=>'text'));
    $this->widgetSchema['cc_expire']  = new sfWidgetFormDate(array('format' => '%month%/%year%','years' => range(date('Y'), date('Y') + 10)),array('style' => 'width:60px'));
    $this->widgetSchema['cc_verification']  = new sfWidgetFormInput(array(),array('size' => 4, 'maxlength' => 4, 'class'=>'medium text'));

    // Add labels
    $this->widgetSchema->setLabel('cc_type',"Card type:");
    $this->widgetSchema->setLabel('cc_name',"Name on Card:");
    $this->widgetSchema->setLabel('cc_number',"Credit Card number:");
    $this->widgetSchema->setLabel('cc_expire',"Expiry Date:");
    $this->widgetSchema->setLabel('cc_verification',"Verification Number:");

    // Help text
    $this->widgetSchema->setHelp('cc_number', 'Example: 4100 0000 0000 0000.');
    $this->widgetSchema->setHelp('cc_verification', 'The verification Number is a three or four-digit number on the back or front of your credit card.');

    // Validators
    $this->setValidators(array(
      'cc_type'   => new sfValidatorString(array('required' => true), array('required' => 'Please provide a credit card type.')),
      'cc_name'   => new sfValidatorString(array('max_length' => 100, 'required' => true), array('required' => 'Please enter the name as shown on the credit card.')),
      'cc_number' => new sfValidatorString(array('required' => true), array('required' => 'Please provide a credit card number.')),
      'cc_expire' => new sfValidatorPass(),
      'cc_verification' => new sfValidatorString(array('min_length' => 3,'max_length' => 4,'required' => true), array('required' => 'Please provide a verification number.')),
    ));

    $this->widgetSchema->setNameFormat('rt_shop_order_creditcard[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->validatorSchema->setPostValidator(new rtShopCreditCardValidator());

    $this->getWidgetSchema()->setFormFormatterName(sfConfig::get('app_rt_public_form_formatter_name', 'RtList'));
  }
}