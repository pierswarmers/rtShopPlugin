<?php
/*
 * This file is part of the Reditype package.
 *
 * (c) 2009-2010 digital Wranglers <info@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Include a mini cart
 *
 * @package    Reditype
 * @subpackage helper
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 * @return     string
 */
function rt_shop_get_mini_cart()
{
  return include_partial('rtShopOrder/cart_mini');
}

/**
 * Include a category listing
 *
 * @package    Reditype
 * @subpackage helper
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 * @return     string
 */
function rt_shop_get_category_list()
{
  return include_component('rtShopCategory', 'navigation');
}

/**
 * Include a featured products listing
 *
 * @package    Reditype
 * @subpackage helper
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 * @param      integer $number Number of featured product shown
 * @return     string
 */
function rt_shop_get_product_featured($number = 5)
{
  return include_component('rtShopProduct', 'ShopProductFeatured', array('options' => array('limit' => $number)));
}