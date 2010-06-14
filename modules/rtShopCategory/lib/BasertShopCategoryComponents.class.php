<?php

/*
 * This file is part of the gumnut package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopCategoryComponents
 *
 * @package    rtSitePlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 */
class BasertShopCategoryComponents extends sfComponents
{
  public function executeNavigation(sfWebRequest $request)
  {
    $module = $request->getParameter('module');
    $action = $request->getParameter('action');

    if($module === 'rtShopCategory' && ($action === 'show' || $action === 'index'))
    {
      if($action === 'index')
      {
        $rt_shop_category = Doctrine::getTable('rtShopCategory')->findRoot();
      }
      else
      {
        $rt_shop_category = Doctrine::getTable('rtShopCategory')->findOnePublishedById($request->getParameter('id'));
      }
      if($rt_shop_category)
      {
        $this->rt_shop_category= $rt_shop_category;
      }
    }
  }
}

