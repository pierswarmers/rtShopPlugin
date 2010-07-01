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
					$_sf_user,
					$_voucher,
					$_promotion;

  /**
   * Constructor
   *
   * @param sfUser $sf_user User object
   */
	public function __construct($sf_user = null)
	{
    if (get_class($sf_user) != 'myUser') {
      throw new Exception('No myUser object supplied.');
    }
		$this->_sf_user = $sf_user;
    $this->getOrder();
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

    $query = Doctrine_Query::create()
             ->from('rtShopOrderToStock os')
             ->addWhere('os.order_id = ?', $order->getId());
    $order_to_stock = $query->fetchArray();

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
   * Return a summary array of stock line items for this order. The returned
   * data is derived from order_to_stock, stock, and product.
   *
   * @return array
   */
  public function getStockInfoArray()
  {
    return $this->getOrder()->getStockInfoArray();
  }

  /**
   * Get shipping charge
   *
   * @return Float Charge
   */
	public function getShipping()
	{
		return $this->getOrder()->getShippingCharge();
	}
  
  /**
   * Get order subTotal with tax
   *
   * @return Float subTotal
   */
	public function getSubTotal()
	{
		return $this->getOrder()->getTotalPriceWithTax();
	}

  /**
   * Get promotion details
   * 
   */
	public function getPromotion()
	{
		$this->_promotion = rtShopPromotionToolkit::getBest($this->getSubTotal());
	}

  /**
   * Get voucher details
   *
   * @return Object rtShopVoucher object
   */
	public function getVoucher()
	{
    return $this->_voucher;
	}
  
  /**
   * Set voucher details
   *
   * @param Object $voucher Voucher object
   */
	public function setVoucher($voucher)
	{
		$this->_voucher = $voucher;
	}

  /**
   * Get order total
   *
   * @param Object $voucher Voucher object
   * @return Float Order total
   */
	public function getTotal($voucher = null)
	{
		// inc. tax.... pre-shipping
		$total = $this->getSubTotal();
		$total = $this->applyPromotion($total);
		$total = $this->applyVoucher($total);
    $total = $this->applyShipping($total);
		return $total;
	}

  /**
   * Apply shipping charges to order total
   *
   * @param Float $total Order total
   * @return Float Adjusted order total
   */
  private function applyShipping($total)
  {
    $total = $total + $this->getShipping();
    return $total;
  }

  /**
   * Apply promotion to order
   *
   * @param Float $total Order total
   * @return Float Adjusted order total
   */
	private function applyPromotion($total)
	{
		return rtShopPromotionToolkit::applyPromotion($total);
	}

  /**
   * Apply voucher to order
   *
   * @param Float $total Order total
   * @return Float Adjusted order total
   */
	private function applyVoucher($total)
	{
		if(is_null($this->getVoucher()))
		{
			return $total;
		}
		return rtShopVoucherToolkit::applyVoucher($this->getVoucher(), $total);
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
       $products[$i]['id'] = $stock['rtShopProduct']['id'];
       $products[$i]['sku'] = $stock['rtShopProduct']['sku'];
       $products[$i]['title'] = $stock['rtShopProduct']['title'];
       $products[$i]['variations'] = $variations;
       $products[$i]['summary'] = rtrim(ltrim(strip_tags($stock['rtShopProduct']['description'])));
       $products[$i]['quantity'] = $stock['rtShopOrderToStock'][0]['quantity'];
       $products[$i]['charge_price'] = $stock['price_promotion'] != 0 ? $stock['price_promotion'] : $stock['price_retail'];
       $products[$i]['price_promotion'] = $stock['price_promotion'];
       $products[$i]['price_retail'] = $stock['price_retail'];
       $products[$i]['price_wholesale'] = $stock['price_wholesale'];
       $products[$i]['currency'] = sfConfig::get('app_rt_shop_payment_currency','AU');
       $i++;
     }

     $order->setClosedProducts($products);
     $order->setClosedShippingRate($this->getShipping());
     $order->setClosedTaxes($order->getTotalTax());
     $order->setClosedPromotions($this->getPromotion());
     $order->setClosedTotal($this->getTotal());
   }

  /**
   * Get order object
   *
   * @return Object Order object
   */
	public function getOrder()
	{
		if(is_null($this->_order))
		{
			$order_id = $this->_sf_user->getAttribute('rt_shop_frontend_order_id');

			if(!is_null($order_id))
			{
				$order = Doctrine::getTable('rtShopOrder')->find($order_id);

				if($order)
				{
					sfContext::getInstance()->getLogger()->info('{rtShopCartManager} Found existing order with id: '.$order->getId());
				}
			}
			else
			{
				$order = new rtShopOrder();
				$order->save();
				$this->_sf_user->setAttribute('rt_shop_frontend_order_id', $order->getId());
				sfContext::getInstance()->getLogger()->info('{rtShopCartManager} Created new order with id: '.$order->getId());
			}

			$this->_order = $order;
		}
		return $this->_order;
	}
}