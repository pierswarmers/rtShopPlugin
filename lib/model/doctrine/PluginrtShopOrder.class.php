<?php

/*
 * This file is part of the reditype package.
 * 
 * (c) 2009-2010 digital Wranglers <info@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PluginrtShopOrder
 *
 * @package    rtShopPlugin
 * @subpackage model
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
abstract class PluginrtShopOrder extends BasertShopOrder
{
  const STATUS_CANCELLED  = 'cancelled';
  const STATUS_PENDING    = 'pending';
  const STATUS_PAID       = 'paid';
  const STATUS_PICKING    = 'picking';
  const STATUS_SENDING    = 'sending';
  const STATUS_SENT       = 'sent';

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

  public function save(Doctrine_Connection $conn = null)
  {
    if($this->getVoucherId() && ($this->getVoucherCode() === '' || is_null($this->getVoucherCode())))
    {
      $voucher  = Doctrine_Core::getTable('rtShopVoucher')->findOneById($this->getVoucherId());
      if($voucher)
      {
        $this->setVoucherCode($voucher->getCode());
      }
    }
    
    parent::save($conn);
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

  public function getTypeNice()
  {
    return 'Order';
  }

  public function getExtendedSearchData()
  {
    $string = '';
    $addresses = $this->getAddressInfoArray();

    foreach($addresses as $address)
    {
      $string .= implode(' ', $address);
    }
    
    return $string;
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

      $this->_stock_info = $this->adjustWithProductPromotions($q->fetchArray());
    }
    return $this->_stock_info;
  }

  private function adjustWithProductPromotions(array $stock_info)
  {
    $tmp_stock_info = array();

    $i=0;
    foreach($stock_info as $stock)
    {
      $rt_shop_stock = Doctrine::getTable('rtShopStock')->find($stock['id']);
      $rt_shop_promotion_product = Doctrine::getTable('rtShopPromotionProduct')->find(($rt_shop_stock->getBestPromotion()) ? $rt_shop_stock->getBestPromotion()->getId() : '',Doctrine_Core::HYDRATE_ARRAY);

      $tmp_stock_info[$i]                           = $stock;
      $tmp_stock_info[$i]['price_promotion']        = $rt_shop_stock->isOnPromotion() ? $rt_shop_stock->getCharge() : '0.00'; // Only add charge when on promotion
      $tmp_stock_info[$i]['rtShopPromotionProduct'] = $rt_shop_promotion_product;

      $i++;
    }
    
    return $tmp_stock_info;
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
   * Get shipping address
   *
   * @return rtAddress Shipping address
   */
  public function getShippingAddress()
  {
    if(is_null($this->_address_shipping))
    {
      $q = Doctrine_Query::create()
        ->from('rtAddress a')
        ->andWhere('a.model = ?', 'rtShopOrder')
        ->andWhere('a.model_id = ?', $this->getId())
        ->andWhere('a.type = ?', 'shipping');
      $this->_address_shipping = $q->fetchOne();
    }

    return $this->_address_shipping;
  }

  /**
   * Get billing address
   *
   * @return rtAddress Billing address
   */
  public function getBillingAddress()
  {
    if(is_null($this->_address_billing))
    {
      $q = Doctrine_Query::create()
        ->from('rtAddress a')
        ->andWhere('a.model = ?', 'rtShopOrder')
        ->andWhere('a.model_id = ?', $this->getId())
        ->andWhere('a.type = ?', 'billing');
      $this->_address_billing = $q->fetchOne();
    }

    return $this->_address_billing;
  }
}