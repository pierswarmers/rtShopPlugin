<?php

/*
 * This file is part of the gumnut package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopProductActions
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 */
class BasertShopProductActions extends sfActions
{
  /**
   * Executes an application defined process prior to execution of this sfAction object.
   *
   * By default, this method is empty.
   */
  public function preExecute()
  {
    sfConfig::set('app_rt_node_title', 'Shop');
    rtTemplateToolkit::setFrontendTemplateDir();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->rt_shop_product = $this->getRoute()->getObject();
    
    $this->forward404Unless($this->rt_shop_product);

    if(!$this->rt_shop_product->isPublished() && !$this->isAdmin())
    {
      $this->forward404('Product isn\'t published.');
    }

    $query = Doctrine::getTable('rtShopProduct')->addRelatedProductQuery($this->rt_shop_product);

    $this->related_products = $query->execute();

    rtSiteToolkit::checkSiteReference($this->rt_shop_product);
    
    $this->updateResponse($this->rt_shop_product);
  }

  public function executeAddToWishlist(sfWebRequest $request)
  {
    $wishlist = $this->getUser()->getAttribute('rt_shop_wish_list', array());
    $wishlist[$request->getParameter('id')] = $request->getParameter('id');
    $this->getUser()->setAttribute('rt_shop_wish_list', $wishlist);
  }

  public function executeShowWishlist(sfWebRequest $request)
  {

  }

  public function executeSendToAFriend(sfWebRequest $request)
  {
    
  }

  private function updateResponse(rtShopProduct $page)
  {
    rtResponseToolkit::setCommonMetasFromPage($page, $this->getUser(), $this->getResponse());
  }

  private function isAdmin()
  {
    return $this->getUser()->hasCredential(sfConfig::get('app_rt_shop_product_admin_credential', 'admin_product'));
  }
}