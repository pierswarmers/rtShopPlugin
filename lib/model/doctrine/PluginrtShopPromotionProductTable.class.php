<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PluginrtShopPromotionProductTable
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class PluginrtShopPromotionProductTable extends rtShopPromotionTable
{
  /**
   * Returns an instance of this class.
   *
   * @return object PluginrtShopPromotionProductTable
   */
  public static function getInstance()
  {
    return Doctrine_Core::getTable('PluginrtShopPromotionProduct');
  }

  /**
   * Find available promotions
   *
   * @param Integer $quantity Quantity
   * @param DateTime $date    Timestamp
   * @param Doctrine_Query $q An optional query object
   * @return Array            Found promotions
   */
  public function findAvailable($quantity, $date = null, Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);
    $q = $this->getDateRestrictionQuery($date, $q);
    $q = $this->getQuantityRestrictionQuery($quantity, $q);
    return $q->fetchArray();
  }

   /**
   * Return a query with quantity condition
   *
   * @param  float           $quantity  Quantity
   * @param  Doctrine_Query  $query     An optional query object
   * @return Doctrine_Query
   */
  public function getQuantityRestrictionQuery($quantity, Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);
    $q->andWhere('(p.quantity_from <= ? OR p.quantity_from IS NULL)', $quantity);
    $q->andWhere('(p.quantity_to > ? OR p.quantity_to IS NULL)', $quantity);
    return $q;
  }
}