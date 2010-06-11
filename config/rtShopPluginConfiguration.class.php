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
  }
}