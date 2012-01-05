<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2010 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopPayment
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopPayment implements rtShopPaymentInterface
{
  private $_log,
          $_transaction_number,
          $_transaction_reference,
          $_response_code,
          $_response_message;

  private $_is_approved = false;
  private $_is_testing = false;

  /**
   * Proxy for doPayment method
   *
   * @param Integer $total Total in cents e.g 1 dollar = 100
   * @param Array   $credit_card Credit card details
   * @param Array   $customer Customer details
   * @param Array   $options Additional details
   * @return Boolean True if payment successful
   */
  public function doPayment($total, $credit_card, $customer = array(), $options = array())
  {
    return true;
  }

  /**
   * Check if payment is set to testing
   *
   * @return boolean True if payment is set to testing
   */
  public function isTesting()
  {
    return $this->_is_testing;
  }

  /**
   * Check if payment was approved
   *
   * @return boolean True if approved
   */
	public function isApproved() {
		return $this->_is_approved;
	}

  /**
   * Return transaction number
   *
   * @return _transaction_number
   */
  public function getTransactionNumber() {
    return $this->_transaction_number;
  }

  /**
   * Return transaction reference
   *
   * @return _transaction_reference
   */
  public function getTransactionReference() {
    return $this->_transaction_reference;
  }

  /**
   * Get response code
   *
   * @return Code
   */
  public function getResponseCode () {
    return $this->_response_code;
  }

  /**
   * Set response code
   *
   * @param  String $string
   * @return void
   */
  public function setResponseCode ($string) {
    $this->_response_code = $string;
  }

  /**
   * Get response message
   *
   * @return Message
   */
	public function getResponseMessage(){
    return $this->_response_message;
	}

  /**
   * Check for core API errors
   *
   * @return Boolean True if found
   */
  public function isApiError($string) {
    $api_errors = array();
    return in_array($string, $api_errors) ? true : false;
  }

  /**
   * Get log data
   *
   * @return mixed Log data
   */
	public function getLog($serialized = true) {
    return $serialized ? serialize($this->_log) : $this->_log;
	}

  /**
   * Set log data
   *
   * @param mixed Log data
   */
	public function setLog(Array $value) {
		$this->_log = $value;
	}
}