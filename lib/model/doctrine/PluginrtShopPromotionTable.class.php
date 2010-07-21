<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PluginrtShopPromotionTable
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class PluginrtShopPromotionTable extends Doctrine_Table
{
  /**
   * Returns an instance of this class.
   *
   * @return object PluginrtShopPromotionTable
   */
  public static function getInstance()
  {
    return Doctrine_Core::getTable('PluginrtShopPromotion');
  }

  /**
   * Find available promotions
   *
   * @param Float $total      Order total
   * @param DateTime $date    Timestamp
   * @param Doctrine_Query $q An optional query object
   * @return Array            Found promotions
   */
  public function findAvailable($total, $date = null, Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);
    $q = $this->getDateRestrictionQuery($date, $q);
    $q = $this->getTotalRestrictionQuery($total, $q);
    return $q->fetchArray();
  }

  /**
   * Find nearly available promotions
   *
   * @param Float $total      Order total
   * @param DateTime $date    Timestamp
   * @param Doctrine_Query $q An optional query object
   * @return Array            Found promotions
   */
  public function findNearlyAvailable($total, $date = null, Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);
    $q = $this->getDateRestrictionQuery($date, $q);
    $q = $this->getTotalRestrictionUpperQuery($total, $q);
    return $q->fetchArray();
  }

  /**
   * Return a query that checks for availability
   *
   * @param Doctrine_Query $query
   * @return Doctrine_Query
   */
  public function getDateRestrictionQuery($date = NULL, Doctrine_Query $q = null)
  {
    if(is_null($date))
    {
      $date = date("Y-m-d H:i:s");
    }

    $q = $this->getQuery($q);
    $q->andWhere('(p.date_from <= ? OR p.date_from IS NULL)', date('Y-m-d H:i:s', strtotime($date)));
    $q->andWhere('(p.date_to > ? OR p.date_to IS NULL)', date('Y-m-d H:i:s', strtotime($date)));
    return $q;
  }

  /**
   * Return a query with total condition
   *
   * @param  float           $total  Total price
   * @param  Doctrine_Query  $query  an optional query object
   * @return Doctrine_Query
   */
  public function getTotalRestrictionQuery($total, Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);
    $q->andWhere('(p.total_from <= ? OR p.total_from IS NULL)', $total);
    $q->andWhere('(p.total_to > ? OR p.total_to IS NULL)', $total);
    return $q;
  }

   /**
   * Return a query with total condition
   *
   * @param  float           $total  Total price
   * @param  Doctrine_Query  $query  an optional query object
   * @return Doctrine_Query
   */
  public function getTotalRestrictionUpperQuery($total, Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);
    $q->andWhere('p.total_from > ?', $total);
    return $q;
  }

  /**
   * Returns a Doctrine_Query object.
   *
   * @param Doctrine_Query $q
   * @return Doctrine_Query
   */
  public function getQuery(Doctrine_Query $q = null)
  {
    if (is_null($q))
    {
      $q = $this->getQueryObject()->from($this->getComponentName() .' p');
    }

    return $q;
  }
}