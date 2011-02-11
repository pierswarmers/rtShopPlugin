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

    $shop_route_token = sfConfig::get('app_rt_shop_route_prefix', 'shop');

    $routing->prependRoute(
      'rt_shop_product_show',
      new sfDoctrineRoute(
        sprintf('/%s/product/:id/:slug',$shop_route_token),
          array('module' => 'rtShopProduct', 'action' => 'show'),
          array('id' => '\d+', 'sf_method' => array('get')),
          array('model' => 'rtShopProduct', 'type' => 'object')
      )
    );

    $routing->prependRoute(
      'rt_shop_category_show',
      new sfDoctrineRoute(
        sprintf('/%s/category/:id/:slug',$shop_route_token),
          array('module' => 'rtShopCategory', 'action' => 'show'),
          array('id' => '\d+', 'sf_method' => array('get')),
          array('model' => 'rtShopCategory', 'type' => 'object')
      )
    );

    $routing->prependRoute(
      'rt_shop_category_index',
      new sfRoute(sprintf('/%s/category',$shop_route_token),array('module' => 'rtShopCategory', 'action' => 'index'))
    );

    $routing->prependRoute(
      'rt_shop_voucher_report_download',
       new sfRoute('/rtShopVoucherAdmin/voucherReport/voucher_report.:sf_format', array('module' => 'rtShopVoucherAdmin', 'action' => 'batchReport'))
    );

    $routing->prependRoute(
      'rt_shop_stock_report_download',
       new sfRoute('/rtShopProductAdmin/stockReport/stock_report.:sf_format', array('module' => 'rtShopProductAdmin', 'action' => 'stockReport'))
    );

    $routing->prependRoute(
      'rt_shop_order_report_download',
       new sfRoute('/rtShopOrderAdmin/orderReport/order_report.:sf_format', array('module' => 'rtShopOrderAdmin', 'action' => 'orderReport'))
    );

    $routing->prependRoute(
      'rt_shop_order_xsd_download',
       new sfRoute('/rtShopOrderAdmin/orderReport/order_xsd.:sf_format', array('module' => 'rtShopOrderAdmin', 'action' => 'orderXsd'))
    );

    $routing->prependRoute(
      'rt_shop_promotion_product_correction',
       new sfRoute('/rtShopPromotionProductAdmin/:action/id/:id', array('module' => 'rtShopPromotionAdmin'))
    );
    
    $routing->prependRoute(
      'rt_shop_promotion_cart_correction',
       new sfRoute('/rtShopPromotionCartAdmin/:action/id/:id', array('module' => 'rtShopPromotionAdmin'))
    );

    $routing->prependRoute('rt_shop_order_show', new sfRoute('/rtShopOrderAdmin/show', array('module' => 'rtShopOrderAdmin', 'action' => 'show')));
    $routing->prependRoute('rt_shop_send_to_friend', new sfRoute(sprintf('/%s/send_to_friend',$shop_route_token), array('module' => 'rtShopProduct', 'action' => 'sendToFriend')));
    $routing->prependRoute('rt_shop_add_to_wishlist', new sfRoute('/add-to-wishlist', array('module' => 'rtShopProduct', 'action' => 'addToWishlist')));
    $routing->prependRoute('rt_shop_show_wishlist', new sfRoute(sprintf('/%s/wishlist',$shop_route_token), array('module' => 'rtShopProduct', 'action' => 'showWishlist')));
    $routing->prependRoute('rt_shop_order_check_voucher', new sfRoute('/order/check-voucher.:sf_format', array('module' => 'rtShopOrder', 'action' => 'checkVoucher')));
    $routing->prependRoute('rt_shop_order_add_to_bag', new sfRoute('/order/add-to-bag', array('module' => 'rtShopOrder', 'action' => 'addToBag')));
    $routing->prependRoute('rt_shop_order_cart', new sfRoute('/order/cart', array('module' => 'rtShopOrder', 'action' => 'cart')));
    $routing->prependRoute('rt_shop_order_membership', new sfRoute('/order/membership', array('module' => 'rtShopOrder', 'action' => 'membership')));
    $routing->prependRoute('rt_shop_order_address', new sfRoute('/order/address', array('module' => 'rtShopOrder', 'action' => 'address')));
    $routing->prependRoute('rt_shop_order_payment', new sfRoute('/order/payment', array('module' => 'rtShopOrder', 'action' => 'payment')));
    $routing->prependRoute('rt_shop_order_update', new sfRoute('/order/update', array('module' => 'rtShopOrder', 'action' => 'update')));
    $routing->prependRoute('rt_shop_order_stock_delete', new sfRoute('/order/delete-stock', array('module' => 'rtShopOrder', 'action' => 'deleteStock', 'act' => 'cart')));
    $routing->prependRoute('rt_shop_order_receipt', new sfRoute('/order/receipt', array('module' => 'rtShopOrder', 'action' => 'receipt')));

    // API routes
    $routing->prependRoute('rt_api_shop_order_download', new sfRoute('/api/orders/get/order_report.:sf_format/*', array('module' => 'rtShopOrderAdmin', 'action' => 'downloadReport')));
  }
}