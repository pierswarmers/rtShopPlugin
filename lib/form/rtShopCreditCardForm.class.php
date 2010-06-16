<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopPlugin
 *
 * @package    rtShopPlugin
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopCreditcardForm extends sfForm
{
  public function setup()
  {
    $this->widgetSchema->setFormFormatterName('table'); // or list

    // Credit card input fields
    $this->widgetSchema['cc_type']    = new sfWidgetFormSelect(array('choices' => sfConfig::get('app_payment_methods')));
    $this->widgetSchema['cc_number']  = new sfWidgetFormInput(array(), array('class'=>'text'));
    $this->widgetSchema['cc_name']    = new sfWidgetFormInput(array(), array('class'=>'text'));
    $this->widgetSchema['cc_expire']  = new sfWidgetFormDate(array('format' => '%month%/%year%','default' => date("n/j/Y", mktime(0,0,0,date("m"),0,date("Y")+5))),array('style' => 'width:60px'));
    $this->widgetSchema['cc_verification']  = new sfWidgetFormInput(array(),array('size' => 3, 'maxlength' => 3, 'class'=>'medium text'));

    // Add labels
    $this->widgetSchema->setLabel('cc_type',"Card type:");
    $this->widgetSchema->setLabel('cc_name',"Name on Card:");
    $this->widgetSchema->setLabel('cc_number',"Credit Card number:");
    $this->widgetSchema->setLabel('cc_expire',"Expiry Date:");
    $this->widgetSchema->setLabel('cc_verification',"Verification Number (CCV):");

    $this->widgetSchema->setHelp('cc_verification', '<i style="font-size:10px;">The CCV is a three or four-digit number on the back or front of your cc</i>');

    // Validators
    $this->setValidators(array(
      'cc_type'   => new sfValidatorString(array('max_length' => 16, 'required' => true), array('required' => 'Please provide a credit card type')),
      'cc_name'   => new sfValidatorString(array('max_length' => 50, 'required' => true), array('required' => 'Please enter the name as shown on the credit card')),
      'cc_number' => new sfValidatorString(array('required' => true), array('required' => 'Please provide a credit card number')),
      'cc_expire' => new sfValidatorPass(),
      'cc_verification' => new sfValidatorNumber(array('min' => 0,'max' => 999,'required' => true), array('required' => 'Please provide a verification number')),
    ));

    $this->widgetSchema->setNameFormat('rt_shop_order_creditcard[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }
}