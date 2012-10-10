<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * eWayPaymentLive
 *
 * Simple list and display controller for products.
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
// eWayPaymentLive

class EwayPaymentLive {
  var $myGatewayURL;
  var $myCustomerID;
  var $myTransactionData = array();
  var $myCurlPreferences = array();

  /**
   * Class Constructor
   * 
   * @param String $customerID
   * @param String $method
   * @param String $liveGateway
   */
  public function EwayPaymentLive($customerID = EWAY_DEFAULT_CUSTOMER_ID, $method = EWAY_DEFAULT_PAYMENT_METHOD ,$liveGateway  = EWAY_DEFAULT_LIVE_GATEWAY)
  {
    $this->myCustomerID = $customerID;
    switch($method) {
      case REAL_TIME;
        if($liveGateway)
          $this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME;
        else
          $this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME_TESTING_MODE;
        break;
      case REAL_TIME_CVN;
        if($liveGateway)
          $this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME_CVN;
        else
          $this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME_CVN_TESTING_MODE;
        break;
      case GEO_IP_ANTI_FRAUD;
        if($liveGateway)
          $this->myGatewayURL = EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD;
        else
        //in testing mode process with REAL-TIME
          $this->myGatewayURL = EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD_TESTING_MODE;
        break;
    }
  }

  /**
   * Payment public function
   * 
   * @return XML Response
   */
  public function doPayment()
  {
    $xmlRequest = "<ewaygateway><ewayCustomerID>" . $this->myCustomerID . "</ewayCustomerID>";

    foreach($this->myTransactionData as $key=>$value)
    {
      $xmlRequest .= "<$key>$value</$key>";
    }
    $xmlRequest .= "</ewaygateway>";

    $xmlResponse = $this->sendTransactionToEway($xmlRequest);

    if($xmlResponse!="")
    {
      $responseFields = $this->parseResponse($xmlResponse);
      return $responseFields;
    }
    else die("Error in XML response from eWAY: " + $xmlResponse);
  }

  /**
   * Send XML Transaction Data and receive XML response
   * 
   * @param XML $xmlRequest XML Request
   * @return Boolean
   */
  public function sendTransactionToEway($xmlRequest)
  {
    $ch = curl_init($this->myGatewayURL);

    curl_setopt( $ch, CURLOPT_TIMEOUT, 240 );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    foreach($this->myCurlPreferences as $key=>$value)
    {
      curl_setopt($ch, $key, $value);
    }
    
    $xmlResponse = curl_exec($ch);

    if(curl_errno( $ch ) == CURLE_OK)
    {
      return $xmlResponse;
    }
  }

  /**
   * Parse XML response from eway and place them into an array
   * 
   * @param XML $xmlRequest XML Response
   * @return Array
   */
  public function parseResponse($xmlResponse)
  {
    $xml_parser = xml_parser_create();
    xml_parse_into_struct($xml_parser,  $xmlResponse, $xmlData, $index);
    //var_dump($xmlData);
    $responseFields = array();
    foreach($xmlData as $data)
    {
      if($data["level"] == 2 && isset ($data["value"]))
      {
        $responseFields[$data["tag"]] = $data["value"];
      }
    }
    return $responseFields;
  }

  /**
   * Set Transaction Data
   * 
   */
  //Possible fields: "TotalAmount", "CustomerFirstName", "CustomerLastName", "CustomerEmail", "CustomerAddress", "CustomerPostcode", "CustomerInvoiceDescription", "CustomerInvoiceRef",
  //"CardHoldersName", "CardNumber", "CardExpiryMonth", "CardExpiryYear", "TrxnNumber", "Option1", "Option2", "Option3", "CVN", "CustomerIPAddress", "CustomerBillingCountry"
  public function setTransactionData($field, $value)
  {
    //if($field=="TotalAmount")
    //	$value = round($value*100);
    $this->myTransactionData["eway" . $field] = htmlentities(trim($value));
  }

  /**
   * Receive special preferences for Curl
   * 
   * @param String $field
   * @param String $value
   */
  public function setCurlPreferences($field, $value)
  {
    $this->myCurlPreferences[$field] = $value;
  }

  /**
   * Obtain visitor IP even if is under a proxy
   *
   * NOTE: The HTTP_X_FORWARDED_FOR header is only provided when httpd is acting in a reverse-proxy mode
   *  e.g. when using the ProxyPass directive
   */
  public function getVisitorIP()
  {
    $ip = $_SERVER["REMOTE_ADDR"];
    $proxy = $_SERVER["HTTP_X_FORWARDED_FOR"];
    if(ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$",$proxy))
    {
      $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    return $ip;
  }
}