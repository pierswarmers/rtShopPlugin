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

  /**
   * Returns the shipping charge.
   *
   * @return float
   */
  public function getShippingCharge()
  {
    $address = $this->getAddress();
    
    $shipping_charge = 0.0;

    if(!$address || !isset($address['country']) || is_null($address['country']) || $address['country'] == '')
    {
      return $shipping_charge;
    }

    $charges_config = sfConfig::get('app_rt_shop_shipping_charges', array());

    if(isset($charges_config[$address['country']]))
    {
      $shipping_charge = $charges_config[$address['country']];
    }
    elseif(isset($charges_config['default']))
    {
      $shipping_charge = $charges_config['default'];
    }

    return $shipping_charge;
  }


  /**
   * Returns an address from the order object.
   *
   * @return array|false
   */
  public function getAddress()
  {
    $address = $this->getCartManager()->getShippingAddress();

    if(!$address)
    {
      $address = $this->getCartManager()->getBillingAddress();
    }

    return $address;
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
