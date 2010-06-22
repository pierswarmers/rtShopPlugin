<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of rtShopPluginConfiguration
 *
 * @author pierswarmers
 */
class rtShopPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->dispatcher->connect('routing.load_configuration', array($this, 'listenToRoutingLoadConfiguration'));
  }

  /**
   * Enable the required routes, carefully checking that no customisation are present.
   * 
   * @param sfEvent $event
   */
  public function listenToRoutingLoadConfiguration(sfEvent $event)
  {
    $routing = $event->getSubject();

    $routing->prependRoute(
      'rt_shop_product_show',
      new sfDoctrineRoute(
        '/shop/product/:id/:slug',
          array('module' => 'rtShopProduct', 'action' => 'show'),
          array('id' => '\d+', 'sf_method' => array('get')),
          array('model' => 'rtShopProduct', 'type' => 'object')
      )
    );

    $routing->prependRoute(
      'rt_shop_category_show',
      new sfDoctrineRoute(
        '/shop/category/:id/:slug',
          array('module' => 'rtShopCategory', 'action' => 'show'),
          array('id' => '\d+', 'sf_method' => array('get')),
          array('model' => 'rtShopCategory', 'type' => 'object')
      )
    );

    $routing->prependRoute(
      'rt_shop_category_index',
      new sfRoute('/shop/category',array('module' => 'rtShopCategory', 'action' => 'index'))
    );

    $routing->prependRoute(
      'rt_shop_vouchure_download',
       new sfRoute('/rtShopVoucherAdmin/download/:id.:sf_format', array('module' => 'rtShopVoucherAdmin', 'action' => 'BatchDownload'))
    );

    $routing->prependRoute(
      'rt_shop_promotion_product_correction',
       new sfRoute('/rtShopPromotionProductAdmin/:action/id/:id', array('module' => 'rtShopPromotionAdmin'))
    );
    
    $routing->prependRoute(
      'rt_shop_promotion_cart_correction',
       new sfRoute('/rtShopPromotionCartAdmin/:action/id/:id', array('module' => 'rtShopPromotionAdmin'))
    );

    $routing->prependRoute('rt_shop_order_add_to_bag', new sfRoute('/order/add-to-bag', array('module' => 'rtShopOrder', 'action' => 'addToBag')));
    $routing->prependRoute('rt_shop_order_cart', new sfRoute('/rtShopOrderCart/:action', array('module' => 'rtShopOrder', 'action' => 'cart')));
    $routing->prependRoute('rt_shop_order_checkout', new sfRoute('/rtShopOrderCheckout/:action', array('module' => 'rtShopOrder', 'action' => 'checkout')));
    $routing->prependRoute('rt_shop_order_address', new sfRoute('/rtShopOrderAddress/:action', array('module' => 'rtShopOrder', 'action' => 'address')));
    $routing->prependRoute('rt_shop_order_payment', new sfRoute('/rtShopOrderPayment/:action', array('module' => 'rtShopOrder', 'action' => 'payment')));
    $routing->prependRoute('rt_shop_order_update', new sfRoute('/rtShopOrderUpdate/:action', array('module' => 'rtShopOrder', 'action' => 'update')));
    $routing->prependRoute('rt_shop_order_stock_delete', new sfRoute('/rtShopOrderStockDelete/:action/:id/:act', array('module' => 'rtShopOrder', 'action' => 'deleteStock', 'act' => 'cart')));
    //$routing->prependRoute('rt_shop_order_receipt', new sfRoute('/rtShopOrderReceipt/:action', array('module' => 'rtShopOrder', 'action' => 'receipt')));
    //$routing->prependRoute('rt_shop_order_invoice', new sfRoute('/rtShopOrderInvoice/:action', array('module' => 'rtShopOrder', 'action' => 'invoice')));
  }
}