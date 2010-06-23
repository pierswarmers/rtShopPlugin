<?php

/**
 * PluginrtShopOrder
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginrtShopOrder extends BasertShopOrder
{
  const STATUS_CANCELLED  = 'cancelled';
  const STATUS_COMPLETE   = 'complete';
  const STATUS_PENDING    = 'pending';
  const STATUS_PROCESSING = 'processing';
  const STATUS_SHIPPED    = 'shipped';
  const STATUS_PAID       = 'paid';
  const STATUS_BACKORDER  = 'backordered';

  private $_stock_info,
          $_price_without_tax,
          $_global_discount_value,
          $_shipping_info,
          $_address_info,
          $_address_shipping,
          $_address_billing,
          $_price_with_tax;

  /**
   * Title to string
   *
   * @return  string Title as string
   */
  public function construct ()
  {
    if($this->isNew()) {
      $this->setReference(rtShopNumberToolkit::getReferenceNumber());
      $this->setStatus(rtShopOrder::STATUS_PENDING);
      $this->setIsWholesale(false);
    }
  }

  /**
   * Title to string
   *
   * @return  string Title as string
   */
  public function __toString ()
  {
    return $this->getReference();
  }

  /**
   * Get total order price without tax
   *
   * @return float
   */
  public function getTotalPriceWithoutTax()
  {
    if(is_null($this->_price_without_tax))
    {
      $stocks = $this->getStockInfoArray();

      $price = 0;
      $global_discount_value = 0;

      foreach ($stocks as $stock)
      {
        $item_price = $stock['price_promotion'] > 0 ? $stock['price_promotion'] : $stock[$this->getPriceColumn()];

        $group_price = $stock['rtShopOrderToStock'][0]['quantity'] * $item_price;

        $price += $group_price;
      }
      $this->_global_discount_value = $global_discount_value;
      $this->_price_without_tax = $price;
    }

    return (float) $this->_price_without_tax;
  }

  /**
   * Get total order discount value which was used in totals.
   *
   * @return float
   */
  public function getTotalDiscount()
  {
    if(is_null($this->_global_discount_value))
    {
      $this->getTotalPriceWithoutTax();
    }

    return (float) $this->_global_discount_value;
  }

  /**
   * Get total order price with tax. Promotional items will have the corrosponding promotional
   * price used in this calculation.
   *
   * @return float
   */
  public function getTotalPriceWithTax ()
  {
    if(is_null($this->_price_with_tax))
    {
      $stocks = $this->getStockInfoArray();
      $total_price = 0;

      foreach ($stocks as $stock)
      {
        $item_price = $stock['price_promotion'] > 0 ? $stock['price_promotion'] : $stock[$this->getPriceColumn()];
        $line_price = $stock['rtShopOrderToStock'][0]['quantity'] * $item_price;

        $tax_inclusion = 0;
        $tax_rate = sfConfig::get('app_rt_shop_tax_rate' , '0');
        // Check if product is taxable
        if($stock['rtShopProduct']['is_taxable'])
        {
          $tax_inclusion += ( $tax_rate / 100 ) * $line_price;
        }
        $total_price = $total_price + $line_price + $tax_inclusion;
      }
      $this->_price_with_tax = $total_price;
    }

    return (float) $this->_price_with_tax;
  }

  /**
   * Get total tax of order
   *
   * @return float
   */
  public function getTotalTax()
  {
     return (float) $this->getTotalPriceWithTax() - $this->getTotalPriceWithoutTax();
  }

  /**
   * Get total weight of order
   *
   * @return float
   */
  public function getTotalWeight()
  {
    $weight = 0;
    foreach($this->getStockInfoArray() as $stock)
    {
      $weight += $stock['weight'] * $stock['rtShopOrderToStock'][0]['quantity'];
    }
    return (float) $weight;
  }

  /**
   * Get total volume of order
   *
   * @return float
   */
  public function getTotalVolume()
  {
    $volume = 0;
    foreach($this->getStockInfoArray() as $stock)
    {
      $volume += $stock['length'] * $stock['width'] * $stock['height'] * $stock['rtShopOrderToStock'][0]['quantity'];
    }
    return (float) $volume;
  }

  /**
   * Return a summary array of stock line items for this order. The returned
   * data is derived from order_to_stock, stock, and product.
   *
   * @return Array
   */
  public function getStockInfoArray()
  {
    if(is_null($this->_stock_info))
    {
      $q = Doctrine_Query::create()->from('rtShopStock s');

      $q->select('ots.*, stv.*, sv.*, a.*, s.*, p.*, t.*, tx.*')
        ->leftJoin('s.rtShopOrderToStock ots')
        ->leftJoin('s.rtShopVariations sv')
        ->leftJoin('sv.rtShopAttribute a')
        ->leftJoin('s.rtShopProduct p')
        ->andWhere('ots.order_id = ?', $this->getId());

      $this->_stock_info = $q->fetchArray();
    }
    return $this->_stock_info;
  }

  /**
   * Return a summary array of addresses for this order. The returned
   * data is derived from rt_shop_address.
   *
   * @return Array
   */
  public function getAddressInfoArray()
  {
    if(is_null($this->_address_info))
    {
      $q = Doctrine_Query::create()
          ->from('rtAddress a')
          ->andWhere('a.model = ?', 'rtShopOrder')
          ->andWhere('a.model_id = ?', $this->getId());
      $this->_address_info = $q->fetchArray();
    }

    return $this->_address_info;
  }

  /**
   * Get shipping address as array
   *
   * @return Array Shipping Address as array
   */
  public function getShippingAddressArray()
  {
    if(is_null($this->_address_shipping))
    {
      $q = Doctrine_Query::create()
        ->from('rtAddress a')
        ->andWhere('a.model = ?', 'rtShopOrder')
        ->andWhere('a.model_id = ?', $this->getId())
        ->andWhere('a.type = ?', 'shipping');
      $this->_address_shipping = $q->fetchArray();
    }

    return $this->_address_shipping;
  }

  /**
   * Get billing address as array
   *
   * @return Array Billing Address as array
   */
  public function getBillingAddressArray()
  {
    if(is_null($this->_address_billing))
    {
      $q = Doctrine_Query::create()
        ->from('rtAddress a')
        ->andWhere('a.model = ?', 'rtShopOrder')
        ->andWhere('a.model_id = ?', $this->getId())
        ->andWhere('a.type = ?', 'billing');
      $this->_address_billing = $q->fetchArray();
    }

    if(count($this->_address_billing) == 0)
    {
      return $this->getShippingAddressArray();
    }

    return $this->_address_billing;
  }

  /**
   * Set order status to PAID after archiving data
   *
   * @param String $status Order status
   * @return $status Status
   */
  public function setStatus($status)
  {
    if($status === $this->getStatus())
    {
      return $this;
    }

    parent::_set('status', $status);

    if($status === rtShopOrder::STATUS_PAID)
    {
      foreach($this->getStockInfoArray() as $stock)
      {
        $stock_object = Doctrine::getTable('rtShopStock')->find($stock['rtShopOrderToStock'][0]['stock_id']);

        if($stock_object)
        {
          $stock_object->setQuantity($stock_object->getQuantity() - $stock['rtShopOrderToStock'][0]['quantity']);
          $stock_object->save();
        }
      }
      try
      {
        $this->archive();
      } catch (Exception $exc)
      {
        //sfContext::getInstance()->getLogger()->crit('{rtShopOrderArchive} Archive failed: ' . $exc->getTraceAsString());
      }
    }
  }

   /**
    * Archive closed order values (total, tax, products)
    *
    * @param $order Order object
    */
   public function archive()
   {
     $products = array();
     $i=0;
     foreach ($this->getStockInfoArray() as $stock)
     {
       // String with all variations for that product
       $variations = '';
       if(count($stock['rtShopVariations']) > 0) {
         $deliminator = '';
         foreach ($stock['rtShopVariations'] as $variation) {
           $variations .= $deliminator.$variation['value'];
           $deliminator = ', ';
         }
       }
       // Put together the small products array
       $products[$i]['id'] = $stock['rtShopProduct']['id'];
       $products[$i]['sku'] = $stock['rtShopProduct']['sku'];
       $products[$i]['title'] = $stock['rtShopProduct']['Translation'][sfContext::getInstance()->getUser()->getCulture()]['title'];
       $products[$i]['variations'] = $variations;
       $products[$i]['summary'] = rtrim(ltrim(strip_tags($stock['rtShopProduct']['Translation'][sfContext::getInstance()->getUser()->getCulture()]['summary'])));
       $products[$i]['quantity'] = $stock['rtShopOrderToStock'][0]['quantity'];
       $products[$i]['charge_price'] = $stock['price_promotion'] != 0 ? $stock['price_promotion'] : $stock['price_retail'];
       $products[$i]['price_promotion'] = $stock['price_promotion'];
       $products[$i]['price_retail'] = $stock['price_retail'];
       $products[$i]['price_wholesale'] = $stock['price_wholesale'];
       $products[$i]['currency'] = rtShopConfiguration::get('app_rt_shop_payment_currency');
       $i++;
     }

     $this->setClosedShippingRate($this->getShippingCharge());
     $this->setClosedTaxes($this->getTotalTax());
     $this->setClosedPromotions(0);
     $this->setClosedProducts($products);
     $this->setClosedTotal($this->getGrandTotalPrice());
   }

  /**
   * Get shipping charge for order. Shipping charges are calculated by using
   * a configurable class.
   *
   * @return boolean or float
   */
  public function getShippingCharge()
  {
    $info = $this->getShippingInfo();
    return $info['charge'];
  }

  /**
   * Get shipping charge for order. Shipping charges are calculated by using
   * a configurable class.
   *
   * @return Array
   */
  public function getShippingInfo()
  {
    if(!is_null($this->_shipping_info))
    {
      return $this->_shipping_info;
    }

    $class = sfConfig::get('app_rt_shop_shipping_class','rtShopShipping');

    $shipping = new $class($this);

    $this->_shipping_info = $shipping->getShippingInfo();

    return $this->_shipping_info;
  }

  /**
   * Get grand total price for order
   *
   * @return float
   */
  public function getGrandTotalPrice()
  {
    $cm = new rtShopCartManager(sfContext::getInstance()->getUser());
    $grand_total = $cm->getTotal();

    return (float) round($grand_total, 2);
  }

  /**
   * Get price column name
   *
   * @return string
   */
  public function getPriceColumn()
  {
    return $this->getIsWholesale() ? 'price_wholesale' : 'price_retail';
  }
}