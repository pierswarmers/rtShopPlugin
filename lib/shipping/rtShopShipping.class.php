<?php
/*
 * This file is part of the reditype package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopShipping
 *
 * @package    reditype
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopShipping
{
	private $_cart_manager;

  /**
   * Constructor
   *
   * @param SteerShopOrder Order object
   */
	public function __construct(rtShopCartManager $cm)
	{
		if($cm->getOrder()->isNew())
		{
			throw new Exception('rtShopOrder object can\'t be new');
		}
    
    $this->_cart_manager = $cm;
	}

  public function getShippingInfo()
  {
    $address = $this->getAddress();
    $response = array('charge' => 0);

    if(!$address || !isset($address['country']) || is_null($address['country']) || $address['country'] == '')
    {
      return false;
    }

    if(!isset($address['country']) || is_null($address['country']) || $address['country'] == '')
    {
      return false;
    }

    $shipping_charges = sfConfig::get('app_rt_shop_shipping_charges', array());

    if(isset($shipping_charges[$address['country']]))
    {
      $response['charge'] = $shipping_charges[$address['country']];
    }
    elseif(isset($shipping_charges['default']))
    {
      $response['charge'] = $shipping_charges['default'];
    }

    return $response;
  }


  /**
   * Returns an address from the order object.
   *
   * @return array|false
   */
  public function getAddress()
  {
    $address = $this->getCartManager()->getOrder()->getShippingAddressArray();

    if(count($address) == 0)
    {
      $address = $this->getCartManager()->getOrder()->getBillingAddressArray();
    }

    return isset($address[0]) ? $address[0] : false;
  }

  /**
   * Get the cart manager object.
   *
   * @return rtShopCartManager
   */
  public function getCartManager()
  {
    return $this->_cart_manager;
  }
}
