<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopBillingAddressForm
 *
 * @package    rtShopPlugin
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopBillingAddressForm extends rtShopShippingAddressForm
{
  public function setup()
  {
    parent::setup();

    $this->setWidget('type', new sfWidgetFormInputHidden());

    $this->disableLocalCSRFProtection();
    $this->setValidator('_csrf_token', new sfValidatorString(array('max_length' => 100, 'required' => false)));

    $this->widgetSchema->setNameFormat('rt_address_2[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  public function getModelName()
  {
    return 'rtAddress';
  }
}