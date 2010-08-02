<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopEwayPayment
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

// Default values
define('EWAY_DEFAULT_CUSTOMER_ID','87654321');
define('EWAY_DEFAULT_PAYMENT_METHOD', 'REAL_TIME_CVN'); // possible values are: REAL_TIME, REAL_TIME_CVN, GEO_IP_ANTI_FRAUD
define('EWAY_DEFAULT_LIVE_GATEWAY', false);             //<false> sets to testing mode, <true> to live mode

// Define script constants
define('REAL_TIME', 'REAL-TIME');
define('REAL_TIME_CVN', 'REAL-TIME-CVN');
define('GEO_IP_ANTI_FRAUD', 'GEO-IP-ANTI-FRAUD');

// Define URLs for payment gateway
define('EWAY_PAYMENT_LIVE_REAL_TIME', 'https://www.eway.com.au/gateway/xmlpayment.asp');
define('EWAY_PAYMENT_LIVE_REAL_TIME_TESTING_MODE', 'https://www.eway.com.au/gateway/xmltest/testpage.asp');
define('EWAY_PAYMENT_LIVE_REAL_TIME_CVN', 'https://www.eway.com.au/gateway_cvn/xmlpayment.asp');
define('EWAY_PAYMENT_LIVE_REAL_TIME_CVN_TESTING_MODE', 'https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp');
define('EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD', 'https://www.eway.com.au/gateway_beagle/xmlbeagle.asp');
define('EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD_TESTING_MODE', 'https://www.eway.com.au/gateway_beagle/test/xmlbeagle_test.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME', 'https://www.eway.com.au/gateway/payment.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME_TESTING_MODE', 'https://www.eway.com.au/gateway/payment.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME_CVN', 'https://www.eway.com.au/gateway_cvn/payment.asp');
define('EWAY_PAYMENT_HOSTED_REAL_TIME_CVN_TESTING_MODE', 'https://www.eway.com.au/gateway_cvn/payment.asp');

class rtShopEwayPayment implements rtShopPaymentInterface
{
  private $_log,
          $_transaction_number,
          $_transaction_reference,
          $_response_code,
          $_response_message;

  private $_is_approved = false;

  /**
   * Proxy for doPayment method
   *
   * Note: For TESTING: Force certain error code => Use total = 1050 (=$10.50) where cents denote error code (e.g 50), etc.
   *
   * @param Integer $total Total in cents e.g 1 dollar = 100
   * @param Array   $credit_card Credit card details
   * @param Array   $customer Customer details
   * @param Array   $options Additional details
   * @return Boolean True if payment successful
   */
  public function doPayment($total, $credit_card, $customer = array(), $options = array())
  {
    $payment = new EwayPaymentLive(sfConfig::get('app_eway_customer_id', '87654321'), $this->getPaymentMethod(sfConfig::get('app_eway_payment_method', 'REAL_TIME_CVN')), sfConfig::get('app_eway_use_live', false));

    // Check if total in cents
    if (!is_int($total))
    {
      sfContext::getInstance()->getLogger()->err('{rtShopPaymentEway} Total value has to be an integer (e.g. cents).');
      return false;
    }

    // Check if mandatory fiels available
    $mandatory_fields = sfConfig::get('app_eway_cc_mandatory_fields', array("CardHoldersName","CardNumber","CardExpiryMonth","CardExpiryYear"));
    foreach($mandatory_fields as $key => $value)
    {
      if (!array_key_exists($value, $credit_card))
      {
        sfContext::getInstance()->getLogger()->err('{rtShopPayment} Missing mandatory field for payment: '.$value);
        return false;
      }
    }

    try {
      // Order total
      $payment->setTransactionData("TotalAmount", $total);                                  //mandatory field

      // Cr. card details
      $payment->setTransactionData("CardHoldersName", $credit_card['CardHoldersName']);     //mandatory field
      $payment->setTransactionData("CardNumber", $credit_card['CardNumber']);               //mandatory field
      $payment->setTransactionData("CardExpiryMonth", $credit_card['CardExpiryMonth']);     //mandatory field
      $payment->setTransactionData("CardExpiryYear", $credit_card['CardExpiryYear']);       //mandatory field
      $payment->setTransactionData("CVN", $credit_card['CVN']);

      // Transaction reference
      $payment->setTransactionData("TrxnNumber", strtoupper(md5(mt_rand(5, 10).date(microtime()).time()))); // Unique transaction reference

      // Customer details
      $payment->setTransactionData("CustomerFirstName", (isset($customer['CustomerFirstName'])) ? $customer['CustomerFirstName']: '');
      $payment->setTransactionData("CustomerLastName", (isset($customer['CustomerLastName'])) ? $customer['CustomerLastName']: '');
      $payment->setTransactionData("CustomerEmail", (isset($customer['CustomerEmail'])) ? $customer['CustomerEmail']: '');
      $payment->setTransactionData("CustomerAddress", (isset($customer['CustomerAddress'])) ? $customer['CustomerAddress']: '');
      $payment->setTransactionData("CustomerPostcode", (isset($customer['CustomerPostcode'])) ? $customer['CustomerPostcode']: '');
      $payment->setTransactionData("CustomerInvoiceDescription", (isset($customer['CustomerInvoiceDescription'])) ? $customer['CustomerInvoiceDescription']: '');
      $payment->setTransactionData("CustomerInvoiceRef", (isset($customer['CustomerInvoiceRef'])) ? $customer['CustomerInvoiceRef']: '');

      // Options
      $payment->setTransactionData("Option1", (isset($options['Option1'])) ? $options['Option1']: '');
      $payment->setTransactionData("Option2", (isset($options['Option2'])) ? $options['Option2']: '');
      $payment->setTransactionData("Option3", (isset($options['Option3'])) ? $options['Option3']: '');

      // Special preferences for php Curl
      $payment->setCurlPreferences(CURLOPT_SSL_VERIFYPEER, 0);

      $response = $payment->doPayment();

      $this->setLog($response);

      // Extract response code from error response string (i.e 00,Transaction Approved(Test CVN Gateway))
      if (isset($response['EWAYTRXNERROR'])) {
        $error_response = explode(',', $response['EWAYTRXNERROR']);
        if(count($error_response) == 2 && strlen($error_response[0]) == 2)
        {
          $this->_response_code = $error_response[0];
          $this->_response_message = $error_response[1];
        }
        else
        {
          $this->_response_message = $response['EWAYTRXNERROR'];
        }
      }

      // API errors
      if($this->isApiError($this->getResponseCode())) {
        sfContext::getInstance()->getLogger()->err('{rtShopPaymentEway} $this->isApiError() flagged an error : '.serialize($response));
        return false;
      }

      // Check status. Set isApproved to true if status is true
      if ($response['EWAYTRXNSTATUS'] === 'True')
      {
        $this->_is_approved = true;
        $this->_transaction_number = $response['EWAYTRXNNUMBER'];
        $this->_transaction_reference = $response['EWAYTRXNREFERENCE'];
      } else {
        $this->setLog($response);
        return false;
      }

      return true;
    } catch (Exception $e) {
      sfContext::getInstance()->getLogger()->err('{rtShopPaymentEway} Fatal exception caught while trying to process rtShopEwayPayment::doPayment() : '.serialize($response));
      return false;
    }
  }

  /**
   * Get payment method
   *
   * @param Constant $method Payment method
   */
  private function getPaymentMethod($method)
  {
    switch ($method) {
      case 'REAL_TIME_CVN':
        return REAL_TIME_CVN;
        break;
      case 'REAL_TIME':
        return REAL_TIME;
        break;
      case 'GEO_IP_ANTI_FRAUD':
        return GEO_IP_ANTI_FRAUD;
        break;
    }
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
    $string = 'Payment failed: ';
    if($this->getResponseCode() === '05')
    {
      $string .= 'Your credit card information seems to be incorrect.';
    }
    else
    {
      $string .= $this->_response_message;
    }
    return $string;
	}
  
  /**
   * Check for core API errors
   *
   * @return Boolean True if found
   */
  public function isApiError($string) {
    $api_errors = array(sfConfig::get('app_eway_api_error_codes',array(06,22,40,92,96)));
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