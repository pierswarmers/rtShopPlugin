<?php

/*
 * This file is part of the steercms package.
 * (c) digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtAssetToolkit provides a generic set of file system tools.
 *
 * @package    gumnut
 * @subpackage toolkit
 * @author     Piers Warmers <piers@wranglers.com.au>
 */
class rtShopCategoryCacheToolkit
{
  public static function clearCache($rt_shop_category = null)
  {
    $cache = sfContext::getInstance()->getViewCacheManager();
    
    if ($cache)
    {
      rtGlobalCacheToolkit::clearCache();
      
      $cache->remove('rtShopCategory/index');

      $file_cache = new sfFileCache(
      array( 'cache_dir' => sfConfig::get('sf_cache_dir') . DIRECTORY_SEPARATOR . 'frontend' ) );
      $file_cache->removePattern('**/rtShopCategory/_navigation/*');

      $cache->remove('@sf_cache_partial?module=rtShopCategory&action=_navigation&sf_cache_key=*');
      $cache->remove('@sf_cache_partial?module=rtShopCategory&action=_navigation&sf_cache_key=40cd750bba9870f18aada2478b24840a');
      $cache->remove('rtShopCategory/_navigation?module=rtShopCategory&action=*&sf_cache_key=*');
      
      //   '/1_mysite_com/all/rtShopCategory/index/page/1';

      if(!is_null($rt_shop_category))
      {
        if($rt_shop_category->getNode()->isRoot())
        {
          $cache->remove('rtShopCategory/index?page=*');
        }
        $cache->remove(sprintf('rtShopCategory/show?id=%s&slug=%s', $rt_shop_category->getId(), $rt_shop_category->getSlug())); // show page
        $cache->remove(sprintf('rtShopCategory/show?id=%s&slug=%s&page=*', $rt_shop_category->getId(), $rt_shop_category->getSlug())); // show page

        $cache->remove('@sf_cache_partial?module=rtShopCategory&action=_shop_category&sf_cache_key='.$rt_shop_category->getId()); // show page partial.
      }
    }
  }
}