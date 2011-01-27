<?php

/*
 * This file is part of the gumnut package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopProductComponents
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 */
class BasertShopProductComponents extends sfComponents
{
  /**
   * Return mini product
   *
   * @param sfWebRequest $request
   */
  public function executeShopProductMini(sfWebRequest $request)
  {
    $this->rt_shop_product = Doctrine::getTable('rtShopProduct')->find($this->id);
  }

  /**
   * Return array with ID's of featured products
   *
   * @example Usage: include_component('rtShopProduct', 'shopProductFeatured', array('options' => array('limit' => 10)))
   * @param sfWebRequest $request
   */
  public function executeShopProductFeatured(sfWebRequest $request)
  {
    $limit = 5;
    if($this->getVar('options'))
    {
      $options = $this->getVar('options');
      $limit = $options['limit'];
    }

    // Return all published product IDs
    $query = Doctrine::getTable('rtShopProduct')->addPublishedQuery()
             ->select('page.id')
             ->andWhere('page.is_featured = ?', true);
    $raw_data = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    // Randomize raw data array
    shuffle($raw_data);

    // Take the first x elements in array and simplify array
    $product_ids = array();
    foreach (array_slice($raw_data, 0, $limit) as $data)
    {
      $product_ids[] = $data['page_id'];
    }
    
    // Get product objects
    $q = Doctrine_Query::create()->from('rtShopProduct page');
    $q->andWhereIn('page.id', $product_ids);
    $rt_shop_products = $q->execute();

    $this->rt_shop_products = (count($product_ids) > 0) ? $rt_shop_products : array();
  }
}

