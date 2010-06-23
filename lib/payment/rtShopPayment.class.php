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
class rtShopPayment
{
  private $_log,
    $_response_code,
    $_response_message,
    $_transaction_id;

  private $_session_token = 'rt_shop_order_transaction_id';
  private $_user_id_token = 'rt_shop_user_unique_id';
  private $_is_approved = false;
  private $_is_testing = false;
  
  /**
   * Constructor
   *
   * @param $order Order Object
   * @param $options Options
   */
  public function __construct()
  {
  }

  /**
   * Executes payment
   *
   * @param $options Options array
   */
  public function execute()
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
   * Return transaction ID
   *
   * @return _transaction_id
   */
  public function getTransactionID() {
    return $this->_transaction_id;
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
   * Check for core API errors
   */
  public function isApiError($string) {
    $api_errors = array(sfConfig::get('app_rt_shop_payment_api_error_codes'));
    return in_array($string, $api_errors) ? true : false;
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