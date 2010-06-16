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
	public $_shipment,
					$_order,
					$_error_message,
					$_charge,
					$_tax;

  /**
   * Constructor
   *
   * @param SteerShopOrder Order object
   */
	public function __construct(rtShopOrder $order)
	{
		$this->_order = $order;

		if($this->_order->isNew())
		{
			throw new Exception('rtShopOrder object can\'t be new');
		}
	}

  public function getShippingInfo()
  {
    $response = array();

    $address = $this->_order->getShippingAddressArray();
    $handling_charge = sfConfig::get('app_rt_shop_shipping rate');

    if (!isset($address['country']) || is_null($address['country']) || $address['country'] == '') {
      return false;
    }

    if ($address['country'] == sfConfig::get('app_rt_shop_default_country', 'AU')) {
      $response['charge'] = $handling_charge['domestic'];
    } else {
      $response['charge'] = $handling_charge['international'];
    }

    return $response;
  }

  /**
   * If shipment complete save changes
   * 
   */
	public function complete()
	{

	}  
}
