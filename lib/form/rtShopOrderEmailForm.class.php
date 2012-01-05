<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopOrderEmailFormclass
 *
 * @package    rtShopPlugin
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopOrderEmailForm extends BasertShopOrderForm
{
  public function setup()
  {
    parent::setup();

    $this->enableCSRFProtection();

    $this->widgetSchema->setFormFormatterName('table');

    $this->useFields(array('id','email_address'));

    $this->widgetSchema->setLabel('email_address',"Email Address");
    $this->widgetSchema['email_address'] = new sfWidgetFormInput(array(), array('class'=>'text'));
    $this->setValidator('email_address', new sfValidatorEmail(array('max_length' => 255, 'required' => true)));

    $this->widgetSchema->setNameFormat('rt_shop_order[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->getWidgetSchema()->setFormFormatterName(sfConfig::get('app_rt_public_form_formatter_name', 'RtList'));
  }
}