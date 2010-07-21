<?php

/**
 * PluginrtShopStockTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginrtShopStockTable extends Doctrine_Table
{
  /**
   * Return an array of stock and associated variation for a given product.
   *
   * @param int $product_id
   * @param Doctrine_Query $query
   * @return array
   */
  public function getForProductAsArray($product_id, Doctrine_Query $query = null)
  {
    $query_1 = $this->getQuery($query);
    $query_1->andWhere('s.product_id = ?', $product_id);
    $query_1->innerJoin('s.rtShopVariations v');
    $result = $query_1->fetchArray();

    if(count($result) == 0)
    {
      $query_2 = $this->getQuery($query);
      $query_2->andWhere('s.product_id = ?', $product_id);
      $result = $query_2->fetchArray();
    }

    return $result;
  }

    /**
   * Return the stock for a given set of variations.
   *
   * @param array $variation_ids
   * @param int $rt_shop_product
   * @param Doctrine_Query $query
   * @return Doctrine_Collection|boolean
   */
  public function findOneByVariationsAndProductId($variation_ids, $rt_shop_product, Doctrine_Query $query = null)
  {
    $query = $this->getQuery($query);

    $rt_shop_product_id = $rt_shop_product;

    if($rt_shop_product instanceof rtShopProduct)
    {
      $rt_shop_product_id = $rt_shop_product->getId();
    }

    $query->leftJoin('s.rtShopVariations v')
          ->andWhereIn('v.id', $variation_ids)
          ->andWhere('s.product_id = ?', $rt_shop_product_id);
    //      ->groupBy('s.id');

    $results = $query->execute();

    if(count($results[0]) !== 0)
    {
      $expected_attribute_count = count($rt_shop_product->rtShopAttributes);

      foreach($results as $stock)
      {
        if(count($stock->rtShopVariations) == $expected_attribute_count)
        {
          return $stock;
        }
      }
    }
    return false;
  }
  
  public function getForProductIdAndVariationId($product_id, $variation_id, Doctrine_Query $query = null)
  {
    $query = $this->getQuery($query);

   $query->leftJoin('s.rtShopVariations v')
         ->andWhere('v.id = ?', $variation_id)
         ->andWhere('s.product_id = ?', $product_id)
         ->groupBy('s.id')
    ;

    return $query->execute();
  }

  
  /**
   * Returns an instance of this class.
   *
   * @return object PluginrtShopStockTable
   */
  public static function getInstance()
  {
      return Doctrine_Core::getTable('PluginrtShopStock');
  }

  /**
   * Returns a Doctrine_Query object.
   *
   * @param Doctrine_Query $query
   * @return Doctrine_Query
   */
  public function getQuery(Doctrine_Query $query = null)
  {
    if (is_null($query))
    {
      $query = $this->getQueryObject()->from($this->getComponentName() .' s');
    }
    return $query;
  }
}