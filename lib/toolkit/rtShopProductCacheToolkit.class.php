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
class rtShopProductCacheToolkit
{
  public static function clearCache($rt_shop_product = null, $stop_here = false)
  {
    $cache = sfContext::getInstance()->getViewCacheManager();

    if ($cache)
    {
      rtGlobalCacheToolkit::clearCache();
      
      $cache->remove('rtShopProduct/index');         // index page
      $cache->remove('rtShopProduct/index?page=*');  // index with page
      $cache->remove('rtShopProduct/feed?format=*'); // feed
      $cache->remove('@sf_cache_partial?module=rtShopProduct&action=_latest&sf_cache_key=*');
      $cache->remove('@sf_cache_partial?module=rtShopProduct&action=_shopProductFeatured&sf_cache_key=*');
      
      if($rt_shop_product)
      {
        // Remove caches for categories.
        foreach($rt_shop_product->rtShopCategories as $rt_shop_category)
        {
          rtShopCategoryCacheToolkit::clearCache($rt_shop_category);
        }

        if(!$stop_here)
        {
          // remove caches for categories.
          foreach($rt_shop_product->rtShopProductsLinked as $rt_shop_product_linked)
          {
            self::clearCache($rt_shop_product_linked, true);
          }
        }
        $cache->remove(sprintf('rtShopProduct/show?id=%s&slug=%s', $rt_shop_product->getId(), $rt_shop_product->getSlug()));   // show page
        $cache->remove('@sf_cache_partial?module=rtShopProduct&action=_shop_product&sf_cache_key='.$rt_shop_product->getId()); // show page partial.
      }
    }
  }
}