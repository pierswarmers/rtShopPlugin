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
class rtShopVoucherCodeForm extends sfForm
{
  public function setup()
  {
    // Voucher input field
    $this->widgetSchema['code']  = new sfWidgetFormInput(array(), array('class'=>'text'));

    // Add labels
    $this->widgetSchema->setLabel('code',"Voucher Code (Optional):");

    $order_id = sfContext::getInstance()->getUser()->getAttribute('rt_shop_frontend_order_id');
    $order = Doctrine::getTable('rtShopOrder')->find($order_id);
    $form_data = sfContext::getInstance()->getRequest();

    // Validators
    $this->setValidators(array(
      'code'   => new rtShopVoucherValidator(array('total' => $order->getGrandTotalPrice(), 'code' => $form_data['rt_shop_order_voucher']['code'], 'required' => false),
                                             array('code' => 'Voucher code is invalid. Please check.'))
    ));

    $this->widgetSchema->setNameFormat('rt_shop_order_voucher[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }
}