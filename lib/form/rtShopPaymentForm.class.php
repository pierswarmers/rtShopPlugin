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
 * @package    rtShopPaymentForm
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopPaymentForm extends BasertShopOrderForm
{
  private $_rt_shop_cart_manager;
  
  public function setup()
  {
    parent::setup();
    
    $this->useFields(array('id','voucher_code'));

    $this->_rt_shop_cart_manager = $this->getOption('rt_shop_cart_manager');
    
    if(!($this->_rt_shop_cart_manager instanceof rtShopCartManager))
    {
      throw new sfException('rtShopCartManager must be supplied.');
    }

    $options = array('required' => false);
    $options['model'] = 'rtShopVoucher';
    $options['column'] = 'code';
    $options['query'] = Doctrine::getTable('rtShopVoucher')->getValidityQuery($this->getOption('rt_shop_cart_manager')->getTotalWithoutVoucher());
    $this->setValidator('voucher_code', new sfValidatorDoctrineChoice($options));
    $this->widgetSchema->setNameFormat('rt_shop_order_voucher[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  public function getRtShopOrder()
  {
    return $this->getRtShopCartManager()->getOrder();
  }

  public function getRtShopCartManager()
  {
    return $this->_rt_shop_cart_manager;
  }
}