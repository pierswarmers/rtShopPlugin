<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopPaymentToolkit
 *
 * @package    rtShopPlugin
 * @subpackage toolkits
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

class rtShopPaymentToolkit
{
  /**
   * Retrieve payment class
   *
   * @return rtShopPayment Object
   */
	public static function getPaymentObject()
	{
    $class = sfConfig::get('app_rt_shop_payment_class','rtPaymentShipping');
    if (is_null($class) || !class_exists($class))
    {
      throw new sfException('No valid payment class specified.');
    }

		$payment = new $class($this);

		return $payment;
	}
}