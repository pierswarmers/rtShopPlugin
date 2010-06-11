<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopPromotionToolkit
 *
 * @package    rtShop
 * @subpackage rtShopPluginTools
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

class rtShopPromotionToolkit
{
  /**
   * Returns best offer in available promotions
   *
   * @param String $date Order date
   * @param Float $total Order total
   * @return rtShopPromotion Object
   */
  public static function getBest($total, $date = NULL)
  {
    $promotions = Doctrine::getTable('rtShopPromotion')->findAvailable($total, $date);

    if (count($promotions) > 0) {
      $orders_by_saving = self::orderPromotionsBySaving($promotions, $total);
      return Doctrine::getTable('rtShopPromotion')->find($orders_by_saving[0]['id']);
    }
    
    return false;
  }

  /**
   * Returns next best offer in available promotions
   *
   * @param String $date Order date
   * @param Float $total Order total
   * @return rtShopPromotion Object
   */
  public static function getNextBest($total, $date = NULL)
  {
    $promotions = Doctrine::getTable('rtShopPromotion')->findNearlyAvailable($total, $date);

    if (count($promotions) > 0) {
      $orders_by_total_from = self::orderPromotionsByTotalFrom($promotions,$total);
      return Doctrine::getTable('rtShopPromotion')->find($orders_by_total_from[0]['id']);
    }

    return false;
  }

  /**
   * Returns price value needed to achieve next best promotion offer
   *
   * @param String $date Order date
   * @param Float $total Order total
   * @return float Distance to next price that offers a promotion
   */
  public static function getDistanceToNextBest($total, $date = NULL)
  {
    $promotions = Doctrine::getTable('rtShopPromotion')->findNearlyAvailable($total, $date);

    if (count($promotions) > 0) {
      $dist_to_next_promo = self::orderPromotionsByTotalFrom($promotions,$total);
      return $dist_to_next_promo[0]['distance_to'];
    }

    return false;
  }

  /**
   * Apply promotion to order total
   *
   * @param Float  $total Order total
   * @param String $date  Order date
   * @return Float        Total
   */
  public static function applyPromotion($total, $date = NULL)
  {
    $promotion = self::getBest($total, $date);

    if($promotion) {
      $reduction_type = $promotion->getReductionType();
      $reduction_value = $promotion->getReductionValue();
      switch ($reduction_type)
      {
        case 'percentageOff':
          $percentage = $reduction_value/100;
          $total = $total - ($total * $percentage);
          break;
        case 'dollarOff':
          $total = $total - $reduction_value;
          break;
      }
    }
    
    return $total;
  }

  /**
   * Order promotions. Have biggest saving as array key 0
   *
   * @param Array $array Promotions Array
   * @param Float $total Order total
   * @return Array Sorted array
   */
  public static function orderPromotionsBySaving($array, $total)
  {
    $sorted_array = array();

    foreach($array as $promotion)
    {
      switch ($promotion['reduction_type'])
      {
        case 'percentageOff':
          $percentage = $promotion['reduction_value']/100;
          $promotion['reduced_total'] = $total - ($total * $percentage);
          break;
        case 'dollarOff':
          $promotion['reduced_total'] = $total - $promotion['reduction_value'];
          break;
      }

      $best_promo = (count($sorted_array) == 0) ? 0 : $sorted_array[0]['reduced_total'];

      if($best_promo > $promotion['reduced_total'])
      {
        array_unshift($sorted_array, $promotion);
      }
      else
      {
        $sorted_array[] = $promotion;
      }
    }
    return (array) $sorted_array;
  }

  /**
   * Order nearly achieved promotions. Have lowest distance as array key 0
   *
   * @param Array $array Promotions Array
   * @param Float $total Order total
   * @return Array Sorted array
   */
  public static function orderPromotionsByTotalFrom($array, $total)
  {
    $sorted_array = array();

    foreach($array as $promotion)
    {
      $promotion['distance_to'] = $promotion['total_from'] - $total;

      $best_promo = (count($sorted_array) == 0) ? 0 : $sorted_array[0]['distance_to'];

      if($best_promo > $promotion['distance_to'] && $promotion['distance_to'] >= 0)
      {
        array_unshift($sorted_array, $promotion);
      }
      else
      {
        $sorted_array[] = $promotion;
      }
    }
    return (array) $sorted_array;
  }
}