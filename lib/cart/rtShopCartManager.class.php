<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopCartManager
 *
 * @package    rtShop
 * @subpackage rtShopPluginCart
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopCartManager
{
	private $_order,
					$_user,
					$_promotion,
          $_is_wholesale;

  const FILTER_NON_STACKABLE = 'NONSTACKABLE';
  const FILTER_STACKABLE = 'STACKABLE';

/**
 * Construct the cart manager - initialising the user and order objects.
 *
 * @param array $options
 */
  public function __construct($options = array())
  {
    // is this a wholesale cart
    $this->_is_wholesale = isset($options['is_wholesale']) ? $options['is_wholesale'] : false;

    // set the user
    $this->_user = sfContext::getInstance()->getUser();

    // get and set the order
    $this->_order = Doctrine::getTable('rtShopOrder')->find($this->_user->getAttribute('rt_shop_frontend_order_id'));

    if(!$this->_order)
    {
      $this->_order = new rtShopOrder();
      $this->_order->save();
      $this->_user->setAttribute('rt_shop_frontend_order_id', $this->_order->getId());
    }
	}

  /**
   * Return the item charges, with tax if running in exclusive tax mode.
   *
   * @param string $filter
   * @return float
   */
  public function getItemsCharge($filter = null)
  {
    $charge = 0.0;
    
    foreach ($this->getStockInfoArray() as $stock)
    {
      if(($filter == self::FILTER_NON_STACKABLE && $stock['price_promotion'] > 0))
      {
        continue;
      }

      $charge += $stock['rtShopOrderToStock'][0]['quantity'] * $this->getItemCharge($stock);
    }

    return (float) $charge;
  }

  /**
   * Return tax component of the items charge.
   *
   * @return float
   */
  public function getTaxCharge()
  {
    if($this->isTaxModeInclusive())
    {
      return 0.00;
    }

    $taxable_items_charge = 0.0;
    $column = $this->isWholesale() ? 'price_wholesale' : 'price_retail';
    
    foreach ($this->getStockInfoArray() as $stock)
    {
      if($stock['rtShopProduct']['is_taxable'])
      {
        $item_charge = $stock['price_promotion'] > 0 ? $stock['price_promotion'] : $stock[$column];
        $taxable_items_charge += $stock['rtShopOrderToStock'][0]['quantity'] * $item_charge;
      }
    }
    
    return (float) $this->getTaxRate() / 100 * $taxable_items_charge;
  }

  /**
   * Returns the tax component of the total. Only applicatable for inclusive tax mode.
   *
   * @return float
   */
  public function getTaxComponent()
  {
    if($this->isTaxModeInclusive())
    {
      return (float) ($this->getTotalCharge() * 10) / ($this->getTaxRate() + 100);
    }

    return 0.00;
  }

  /**
   * Returns the tax component of the total. Only applicatable for inclusive tax mode.
   *
   * @return float
   */
  static function calcTaxComponent($total)
  {
    if(sfConfig::get('app_rt_shop_tax_mode', 'inclusive'))
    {
      return (float) ($total * 10) / (sfConfig::get('app_rt_shop_tax_rate', 0.0) + 100);
    }

    return 0.00;
  }

  public function getTotalCharge()
  {
    $charge = $this->getPreTotalCharge();

    // voucher reduction
    $charge -= $this->getVoucherReduction();

    if($charge <= 0)
    {
      return 0.0;
    }

    return $charge;
  }

  /**
   * Gets the total charge value, before vouchers are applied.
   *
   * @param string $filter
   * @return float
   */
  public function getPreTotalCharge($filter = null)
  {
    $filter = is_null($filter) ? self::FILTER_STACKABLE : $filter;

    $charge = $this->getItemsCharge($filter);

    if($charge == 0.0)
    {
      return 0.0;
    }

    if($filter == self::FILTER_STACKABLE)
    {
      // promotion reductions
      $charge -= $this->getPromotionReduction();
    }

    // sales tax for exclusive mode
    if(!$this->isTaxModeInclusive())
    {
      $charge += $this->getTaxCharge();
    }

    // shipping
    $charge += $this->getShippingCharge();

    return $charge;
  }

  /**
   * Return shipping charges.
   *
   * @return float
   */
  public function getShippingCharge()
  {
    $class = sfConfig::get('app_rt_shop_shipping_class','rtShopShipping');
    $shipping = new $class($this);
    return $shipping->getShippingCharge();
  }

  /**
   * Return shipping address - defaults to billing address if no shipping address found.
   *
   * @return array
   */
  public function getShippingAddress()
  {
    return $this->getOrder()->getShippingAddress();
  }

  /**
   * Return billing address.
   *
   * @return array
   */
  public function getBillingAddress()
  {
    return $this->getOrder()->getBillingAddress();
  }

  /**
   * Get the total reduction value applied by promotions.
   *
   * @return float
   */
  public function getPromotionReduction()
  {
    $promotion = $this->getPromotion();

    $total = 0.0;

    if($promotion)
    {
      $filter = $promotion->getStackable() ? self::FILTER_STACKABLE : self::FILTER_NON_STACKABLE;
      $total = $this->getItemsCharge($filter);

      if ($promotion->getReductionType() == rtShopPromotion::REDUCTION_TYPE_PERCENTAGE)
      {
          $percentage = $promotion->getReductionValue()/100;
          $total = $total * $percentage;
      }
      else
      {
        if($total > $promotion->getReductionValue())
        {
          $total = $promotion->getReductionValue();
        }
      }
    }

    return (float) $total;
  }

  /**
   * Get the total reduction value applied by a voucher.
   *
   * @return float
   */
  public function getVoucherReduction()
  {
    $filter = self::FILTER_STACKABLE;

    if($this->getVoucher())
    {
      $filter = $this->getVoucher()->getStackable() ? self::FILTER_STACKABLE : self::FILTER_NON_STACKABLE;
    }

    $charge = $this->getPreTotalCharge($filter);
    
		if(is_null($this->getVoucherCode()))
		{
			return 0.00;
		}
    
    return (float) $charge - rtShopVoucherToolkit::applyVoucher($this->getVoucherCode(), $charge);
  }
  
  /**
   * Proxy to getItemsCharge().
   *
   * @see rtShopCartManager::getItemsCharge()
   * @return float
   */
  public function getSubTotal()
  {
    $charge = 0;

    foreach($this->getStockInfoArray() as $item)
    {
      $charge += $this->getItemCharge($item);
    }

    return $charge;
  }

  /**
   * Return the charge for a single line item.
   * 
   * @param array $item 
   */
  public function getItemCharge(array $item)
  {
    $column = $this->isWholesale() ? 'price_wholesale' : 'price_retail';

    return $item['price_promotion'] > 0 ? $item['price_promotion'] : $item[$column];
  }
  
  /**
   * @return rtShopOrder
   */
  public function getOrder()
  {
    return $this->_order;
  }
  /**
   * @return sfUser
   */
  public function getUser()
  {
    return $this->_user;
  }
  
  /**
   * @return rtShopPromotion
   */
  public function getPromotion()
  {
    if(is_null($this->_promotion))
    {
      $this->_promotion = rtShopPromotionToolkit::getBest($this->getSubTotal());
    }
		return $this->_promotion;
  }

  /**
   * Is this a wholesale cart
   * 
   * @return boolean
   */
  public function isWholesale()
  {
    return $this->_is_wholesale;
  }

  /**
   * Get the taxation mode, either inclusive or exclusive.
   *
   * inclusive = Value Added Tax (VAT) or  Goods and Services Tax (GST)
   * exclusive = Sales Tax
   *
   * @return string
   */
  public function getTaxMode()
  {
    return sfConfig::get('app_rt_shop_tax_mode', 'inclusive');
  }

  /**
   * Is the taxation mode inclusive
   *
   * @return string
   */
  public function isTaxModeInclusive()
  {
    return $this->getTaxMode() == 'inclusive';
  }

  /**
   * Get the taxation rate
   *
   * @return float
   */
  public function getTaxRate()
  {
    return (float) sfConfig::get('app_rt_shop_tax_rate', 0.0);
  }
  
  /**
   * Return a summary array of stock line items for the order.
   *
   * @see rtShopOrder::getStockInfoArray()
   * @return array
   */
  public function getStockInfoArray()
  {
    return $this->getOrder()->getStockInfoArray();
  }

  /**
   * Add products to cart
   *
   * @param Integer $stock_id stock_id
   * @param Integer $quantity Ordered quantity
   */
  public function addToCart($stock_id, $quantity)
  {
		$order = $this->getOrder();
		sfContext::getInstance()->getLogger()->info(sprintf('{rtShopCartManager} Adding %s stock_id %s to order_id %s', $quantity, $stock_id, $order->getId()));

    if (!is_int($quantity))
		{
      throw new Exception('Supplied quantity must be an integer.');
    }

    $stock = Doctrine::getTable('rtShopStock')->find($stock_id);

    if (!$stock)
		{
      throw new Exception('No stock found for given id ' . $stock_id);
    }

    $product = $stock->rtShopProduct;

    // Check if backorder allowed and quantities available
    if ($product->getBackorderAllowed() === false && $quantity > $stock->getQuantity())
    {
      sfContext::getInstance()->getLogger()->info('{rtShopCartManager} Stock not added due to quantity to high.');
      return false;
    }

    // Remove existing order to stock element
    Doctrine_Query::create()
      ->delete('rtShopOrderToStock os')
      ->addWhere('os.stock_id = ?', $stock_id)
      ->addWhere('os.order_id = ?', $order->getId())
      ->execute();

    if($quantity == 0)
    {
      return true;
    }

    // Add new order_to_stock element
    $ots = new rtShopOrderToStock();
    $ots->setQuantity($quantity);
    $ots->setStockId($stock_id);
    $ots->setOrderId($order->getId());
    $ots->save();

    return true;
  }

  /**
   * Remove product from cart
   *
   * @param Integer $stock_id stock_id
   */
  public function removeFromCart($stock_id)
  {
    $order = $this->getOrder();

    if (!is_int($stock_id)) {
      throw new Exception('Supplied stock_id must be an integer.');
    }

    Doctrine_Query::create()
      ->delete('rtShopOrderToStock os')
      ->addWhere('os.stock_id = ?', $stock_id)
      ->addWhere('os.order_id = ?', $order->getId())
			->execute();

    sfContext::getInstance()->getLogger()->info('{rtShopCartManager} Unlinking stock_id '. $stock_id .' from order_id '. $order->getId());

    return true;
  }

  /**
   * Returns no of items in cart
   *
   * @return Integer
   */
  public function getItemsInCart()
  {
    $order = $this->getOrder();

    $query = Doctrine_Query::create()
             ->from('rtShopOrderToStock os')
             ->addWhere('os.order_id = ?', $order->getId());
    $order_to_stock = $query->fetchArray();

    if(!$order_to_stock) {
      return 0;
    }

    return count($order_to_stock);
  }

  /**
   * Added up cart item quantitites ordered
   *
   * @return Integer
   */
  public function getItemsQuantityInCart()
  {
    $order = $this->getOrder();

    $order_to_stock = Doctrine_Query::create()->from('rtShopOrderToStock os')
             ->addWhere('os.order_id = ?', $order->getId())
             ->fetchArray();

    if(!$order_to_stock) {
      return 0;
    }

    $quantity = 0;
    foreach($order_to_stock as $ots) {
      $quantity += $ots['quantity'];
    }

    return $quantity;
  }

  /**
   * Get voucher object
   *
   * @return object
   */
  public function getVoucher()
  {
    return Doctrine::getTable('rtShopVoucher')->findOneByCode(($this->getVoucherCode()) ? $this->getVoucherCode() : '');
  }

  /**
   * Get voucher code
   *
   * @return string
   */
	public function getVoucherCode()
	{
    return $this->getOrder()->getVoucherCode();
	}
  
  /**
   * Set voucher details
   */
	public function setVoucherCode($voucher_code)
	{
		$this->getOrder()->setVoucherCode($voucher_code);
	}

   /**
    * Archive closed order values (total, tax, products)
    *
    * @param $order Order object
    */
   public function archive()
   {
     $order = $this->getOrder();
     
     $products = array();
     $i=0;
     foreach ($order->getStockInfoArray() as $stock)
     {
       // String with all variations for that product
       $variations = '';
       if(count($stock['rtShopVariations']) > 0) {
         $deliminator = '';
         foreach ($stock['rtShopVariations'] as $variation) {
           $variations .= $deliminator.$variation['title'];
           $deliminator = ', ';
         }
       }

       // Put together the small products array
       $products[$i]['id'] = $stock['id'];
       $products[$i]['id_product'] = $stock['rtShopProduct']['id'];
       $products[$i]['sku'] = $stock['sku'];
       $products[$i]['sku_product'] = $stock['rtShopProduct']['sku'];
       $products[$i]['title'] = $stock['rtShopProduct']['title'];
       $products[$i]['variations'] = $variations;
       $products[$i]['summary'] = rtrim(ltrim(strip_tags($stock['rtShopProduct']['description'])));
       $products[$i]['quantity'] = $stock['rtShopOrderToStock'][0]['quantity'];
       $products[$i]['charge_price'] = $stock['price_promotion'] != 0 ? $stock['price_promotion'] : $stock['price_retail'];
       $products[$i]['price_promotion'] = $stock['price_promotion'];
       $products[$i]['price_retail'] = $stock['price_retail'];
       $products[$i]['price_wholesale'] = $stock['price_wholesale'];
       $products[$i]['currency'] = sfConfig::get('app_rt_shop_payment_currency','AUD');
       $i++;
     }

     $order->setProductsData($products);

     $order->setShippingCharge($this->getShippingCharge());
     
     $order->setTaxCharge($this->getTaxCharge());
     $order->setTaxComponent($this->getTaxComponent());
     $order->setTaxMode($this->getTaxMode());
     $order->setTaxRate($this->getTaxRate());

     $order->setVoucherCode($this->getVoucherCode());
     $order->setVoucherReduction($this->getVoucherReduction());
     $voucher = $this->getVoucher();
     if($voucher)
     {
       $order->setVoucherId($voucher->getId());
       $order->setVoucherData($voucher->toArray());
     }
     
     $promotion = $this->getPromotion();
     $order->setPromotionReduction($this->getPromotionReduction());
     if($promotion)
     {
       $order->setPromotionId($promotion->getId());
       $order->setPromotionData($promotion->toArray());
     }
     $order->setItemsCharge($this->getItemsCharge());
     $order->setTotalCharge($this->getTotalCharge());
   }

   /**
    * Adjust stock quantities
    */
  public function adjustStockQuantities()
  {
    if($this->getOrder()->getStatus() === rtShopOrder::STATUS_PAID)
    {
      foreach($this->getOrder()->getStockInfoArray() as $stock)
      {
        $stock_object = Doctrine::getTable('rtShopStock')->find($stock['rtShopOrderToStock'][0]['stock_id']);
        if($stock_object)
        {
          $quantity_before = $stock_object->getQuantity();
          $stock_object->setQuantity($stock_object->getQuantity() - $stock['rtShopOrderToStock'][0]['quantity']);
          $stock_object->save();
          sfContext::getInstance()->getLogger()->info(sprintf('{rtShopCartManager} Adjust stock Id = %s by quantity = %s. Quantity before = %s. Quantity after =  %s',$stock['rtShopOrderToStock'][0]['stock_id'],$stock['rtShopOrderToStock'][0]['quantity'],$quantity_before,$stock_object->getQuantity()));
        }
      }
    }
  }
  
  /**
   * Adjust voucher details such as count and value
   */
  public function adjustVoucherDetails()
  {
    $voucher = $this->getVoucher();
    if($voucher)
    {
      $value_before = $voucher->getReductionValue();
      $count_before = $voucher->getCount();

      $voucher->adjustReductionValueBy($this->getPreTotalCharge());
      $voucher->adjustCountBy(1);
      $voucher->save();

      sfContext::getInstance()->getLogger()->info(
              sprintf('{rtShopCartManager} Voucher (#%s): Value %s to %s, Count %s to %s',
                      $this->getVoucherCode(),
                      $value_before,
                      $voucher->getReductionValue(),
                      $count_before,
                      $voucher->getCount()
              )
      );
    }
  }

  /**
   * Remove Cache for products altered in the order submission
   *
   */
  public function clearCache($product_ids = array())
  {
    foreach($this->getOrder()->getStockInfoArray() as $stock)
    {
      $product_ids[$stock['product_id']] = $stock['product_id'];
    }

    foreach($product_ids as $id)
    {
      $product = Doctrine::getTable('rtShopProduct')->find($id);

      if($product)
      {
        rtShopProductCacheToolkit::clearCache($product);
      }
    }
  }

  /**
   * Does the cart contain any stock selections.
   *
   * @return boolean
   */
  public function isEmpty()
  {
    return $this->getOrder()->Stocks->count() == 0 ? true : false;
  }

  /**
   * For logging pricing data
   *
   * @return string
   */
  public function getPricingInfo()
  {
    $string = '{rtShopCartManager} ';

    $string .= sprintf('->getTaxRate() = %s, ',             $this->getTaxRate());
    $string .= sprintf('->getTaxMode() = %s, ',             $this->getTaxMode());
    $string .= sprintf('->getItemsCharge() = %s, ',         $this->getItemsCharge());
    $string .= sprintf('->getPromotionReduction() = %s, ',  $this->getPromotionReduction());
    $string .= sprintf('->getVoucherReduction() = %s, ',    $this->getVoucherReduction());
    $string .= sprintf('->getTaxCharge() = %s, ',           $this->getTaxCharge());
    $string .= sprintf('->getShippingCharge() = %s, ',      $this->getShippingCharge());
    $string .= sprintf('->getTaxCharge() = %s, ',           $this->getTaxCharge());
    $string .= sprintf('->getTaxComponent() = %s, ',        $this->getTaxComponent());
    $string .= sprintf('->getPreTotalCharge = %s, ',        $this->getPreTotalCharge());
    $string .= sprintf('->getTotalCharge = %s, ',           $this->getTotalCharge());
    
    return $string;
  }
}