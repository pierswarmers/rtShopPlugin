<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PluginrtShopVoucherTable
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class PluginrtShopVoucherTable extends rtShopPromotionTable
{
  /**
   * Returns an instance of this class.
   *
   * @return object PluginrtShopVoucherTable
   */
  public static function getInstance()
  {
    return Doctrine_Core::getTable('PluginrtShopVoucher');
  }

  /**
   * Returns vouchers which are not part of a batch
   *
   * @param  Doctrine_Query  $query  an optional query object
   * @return Doctrine_Query
   */
  public function getNonBatchVouchers(Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);

    $q->select('DISTINCT p.batch_reference');
    $q->andWhere("(p.batch_reference like ?)","");

    return $q;
  }

  /**
   * Returns distinct batch reference numbers
   *
   * @param  Doctrine_Query  $query  an optional query object
   * @return Doctrine_Query
   */
  public function getDistinctBatchReferenceNumbers(Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);

    $q->select('p.batch_reference, p.title, count(p.id) as count, p.created_at');
    $q->andWhere("(p.batch_reference not like ?)","");
    $q->groupBy("p.batch_reference");

    return $q;
  }

  /**
   * Returns distinct batch reference information
   *
   * @param  Doctrine_Query  $query  an optional query object
   * @return array
   */
  public function findInfoOnBatchReference($batch_reference, Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);

    $q->select('p.batch_reference, p.title, count(p.id) as count, p.created_at, p.reduction_type, p.reduction_value')
      ->andWhere('p.batch_reference = ?', $batch_reference)
      ->groupBy('p.batch_reference');

    return $q->fetchArray();
  }

  /**
   * Find valid vouchers
   *
   * @param String $code      Voucher code
   * @param Float $total      Order total
   * @param DateTime $date    Timestamp
   * @param Doctrine_Query $q An optional query object
   * @return Array            Found vouchers
   */
  public public function findValid($code, $total, $date = null, Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);
    $q = $this->getDateRestrictionQuery($date, $q);
    $q = $this->getCodeRestrictionQuery($code, $q);
    $q = $this->getTotalRestrictionQuery($total, $q);
    $q = $this->getCountRestrictionQuery($q);
    return $q->fetchArray();
  }

   /**
   * Return a query with code condition
   *
   * @param  float           $code   Voucher code
   * @param  Doctrine_Query  $query  An optional query object
   * @return Doctrine_Query
   */
  public function getCodeRestrictionQuery($code, Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);
    $q->andWhere('(p.code = ?)', $code);
    return $q;
  }

    /**
   * Return a query with count condition
   *
   * @param  float           $code   Voucher code
   * @param  Doctrine_Query  $query  An optional query object
   * @return Doctrine_Query
   */
  public function getCountRestrictionQuery(Doctrine_Query $q = null)
  {
    $q = $this->getQuery($q);
    $q->andWhere('(p.count > ?)', 0);
    return $q;
  }
}