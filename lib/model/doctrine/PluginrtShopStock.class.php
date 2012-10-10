<?php

/**
 * PluginrtShopStock
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginrtShopStock extends BasertShopStock
{
  public function getTidyVariationsList()
  {
    //exit;
    //return $this->rtShopVariations;
  }

  /**
   * Adjust stock quantity
   *
   * @param Integer $quantity Ordered stock quanity
   * @return Object
   */
  public function adjustQuantityBy($quantity)
  {
    if($quantity > 0)
    {
      $adjusted_quantity = $this['quantity'] - $quantity;
      $this->_set('quantity', $adjusted_quantity);
    }

    return $this;
  }

  /**
   * Return product charge considering product promotions
   *
   * @return float
   */
  public function getCharge()
  {
    return $this->isOnPromotion() ? $this->getPricePromotionFinal() : $this->getPriceRetail();
  }

  public function getPricePromotionFinal()
  {
    $price_retail    = $this->getPriceRetail();
    $price_promotion = $this->getPricePromotion();

    if(!$this->isOnPromotion())
    {
      return 0.0;
    }

    // Either price_promotion or product promotion available
    $promotion = $this->getBestPromotion();
    if($promotion)
    {
      // Has product promotion
      if($promotion->getStackable() && $price_promotion != 0)
      {
        // Is stackable and has price_promotion
        $reduced_price = $this->getPromotionReducedPrice($price_promotion, $promotion->getReductionType(), $promotion->getReductionValue());
      }
      elseif($promotion->getStackable())
      {
        // Is stackable but does not have price_promotion
        $reduced_price = $this->getPromotionReducedPrice($price_retail, $promotion->getReductionType(), $promotion->getReductionValue());
      }
      else
      {
        // Is non-stackable, use price_retail
        $reduced_price = $this->getPromotionReducedPrice($price_retail, $promotion->getReductionType(), $promotion->getReductionValue());

        // Compare reduced_price and price_promotion and use lower one
        if($price_promotion != 0 && ($price_promotion < $reduced_price))
        {
          $reduced_price = $price_promotion;
        }
      }
      return $reduced_price;
    }
    return $price_promotion;
  }

  /**
   * Calculate reduced price by reduction type and reduction value
   *
   * @param float $price
   * @param string $reduction_type
   * @param float $reduction_value
   * @return float
   */
  private function getPromotionReducedPrice($price,$reduction_type,$reduction_value)
  {
    switch($reduction_type)
    {
      case 'percentageOff':
        $percentage = $reduction_value/100;
        $reduced_price = $price - ($price * $percentage);
        break;
      case 'dollarOff':
        $reduced_price = $price - $reduction_value;
        break;
    }

    return $reduced_price;
  }

  /**
   * Return promotion status
   *
   * @return boolean
   */
  public function isOnPromotion()
  {
    return ($this->getRtShopProduct()->isOnPromotion() || $this->getPricePromotion() != 0);
  }

  /**
   * Return best promotion for product
   *
   * @return rtShopPromotion
   */
  public function getBestPromotion()
  {
    $promotions = $this->getRtShopProduct()->getrtShopPromotionsAvailableOnly();

    if(count($promotions) > 0)
    {
      $sorted_array = array();
      $i=0;
      foreach($promotions as $promotion)
      {
        // Product promotion rule 1:
        // If promotion is stackable and price_promotion available use price_promotion, otherwise use price_retail
        $price = ($promotion->getStackable() && $this->getPricePromotion() != 0) ? $this->getPricePromotion() : $this->getPriceRetail();
        
        switch ($promotion->getReductionType())
        {
          case 'percentageOff':
            $percentage = $promotion->getReductionValue()/100;
            $reduced_price = $price - ($price * $percentage);
            break;
          case 'dollarOff':
            $reduced_price = $price - $promotion->getReductionValue();
            break;
        }

        $sorted_array[$i]['id']            = $promotion->getId();
        $sorted_array[$i]['reduced_price'] = $reduced_price;

        if($reduced_price < $sorted_array[0]['reduced_price'])
        {
          $best = array();
          $best['id']            = $promotion->getId();
          $best['reduced_price'] = $reduced_price;
          array_unshift($sorted_array, $best);
        }
        $i++;
      }
      return Doctrine::getTable('rtShopPromotionProduct')->find($sorted_array[0]['id']);
    }
    return false;
  }

  /**
   * For logging stock data
   *
   * @return string
   */
  public function getStockInfo()
  {
    $string = '{rtShopStock} ';

    $string .= sprintf('->getId() = %s, ',                        $this->getId());
    $string .= sprintf('->getRtShopProduct()->getId() = %s, ',    $this->getRtShopProduct()->getId());
    $string .= sprintf('->getPriceRetail() = %s, ',               $this->getPriceRetail());
    $string .= sprintf('->getPricePromotion() = %s, ',            $this->getPricePromotion());
    $string .= sprintf('->isOnPromotion() = %s, ',                ($this->isOnPromotion()) ? 'Yes':'No');
    $string .= sprintf('->getBestPromotion() = %s, ',             ($this->getBestPromotion())? $this->getBestPromotion()->getId() : 'False');
    if($this->getBestPromotion())
    {
      $string .= sprintf('->getBestPromotion()->getStackable() = %s, ',        ($this->getBestPromotion()->getStackable()) ? 'Yes':'No');
      $string .= sprintf('->getBestPromotion()->getReductionType() = %s, ',    $this->getBestPromotion()->getReductionType());
      $string .= sprintf('->getBestPromotion()->getReductionValue() = %s, ',   $this->getBestPromotion()->getReductionValue());
      if(!is_null($this->getBestPromotion()->getDateFrom()))
      {
        $string .= sprintf('->getBestPromotion()->getDateFrom() = %s, ',       $this->getBestPromotion()->getDateFrom());
      }
      if(!is_null($this->getBestPromotion()->getDateTo()))
      {
        $string .= sprintf('->getBestPromotion()->getDateTo() = %s, ',         $this->getBestPromotion()->getDateTo());
      }
    }
    $string .= sprintf('->getCharge = %s',           $this->getCharge());

    return $string;
  }
}