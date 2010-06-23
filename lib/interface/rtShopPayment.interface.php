<?php
/*
 * This file is part of the rtShoppingPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopEwayPaymentInterface
 *
 * @package    rtShoppingPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

interface rtShopPaymentInterface
{
  public function doPayment($total, $credit_card, $customer = array(), $options = array());
	public function isApproved();
  public function getTransactionNumber();
  public function getTransactionReference();
  public function getResponseCode ();
  public function setResponseCode ($string);
	public function getResponseMessage();
  public function isApiError($string);
	public function getLog($serialized = true);
	public function setLog(Array $value);
}