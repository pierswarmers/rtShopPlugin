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

  public function executeIndex(sfWebRequest $request)
  {
    $this->rt_shop_product = Doctrine::getTable('rtShopProduct')->findRoot();
    $this->forward404Unless($this->rt_shop_product);
    $this->setTemplate('show');
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->rt_shop_product = $this->getRoute()->getObject();
    
    $this->forward404Unless($this->rt_shop_product);

    if(!$this->rt_shop_product->isPublished() && !$this->isAdmin())
    {
      $this->forward404('Page isn\'t published.');
    }

    $this->updateResponse($this->rt_shop_product);
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