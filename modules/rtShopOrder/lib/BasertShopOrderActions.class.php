<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasertShopOrderActions
 *
 * @package    rtShopPlugin
 * @subpackage modules
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class BasertShopOrderActions extends sfActions
{
  private $_session_token = 'rt_shop_frontend_order_id';
  private $_cart;
  private $_order;

  /**
   * Executes an application defined process prior to execution of this sfAction object.
   *
   * By default, this method is empty.
   */
  public function preExecute()
  {
    sfConfig::set('app_rt_node_title', 'Order');
    rtTemplateToolkit::setFrontendTemplateDir();
  }

  /**
   * Executes the index page
   *
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->redirect('@rt_shop_order_cart');
  }

  /**
   * Executes the cart page
   *
   * @param sfWebRequest $request
   */
  public function executeCart(sfWebRequest $request)
  {
    $this->order = $this->getOrder();
  }

  /**
   * Add to shopping bag function then redirect to cart, or in the case of an
   * error redirect to product details screen
   *
   * @param sfWebRequest $request
   */
  public function executeAddToBag(sfWebRequest $request)
  {
    if($request->hasParameter('rt-shop-stock-id'))
    {
      $stock_id = $request->getParameter('rt-shop-stock-id');
    }
    else
    {
      if(!$request->hasParameter('rt-shop-variation-ids'))
      {
        throw new sfException('Missing parameter: rt-shop-variation-ids');
      }
      
      $variation_ids = $request->getParameter('rt-shop-variation-ids');
      
      $rt_shop_product = Doctrine::getTable('rtShopProduct')->find($request->getParameter('rt-shop-product-id'));
      $this->forward404Unless($rt_shop_product);

      if(count($rt_shop_product->rtShopAttributes) != count($variation_ids))
      {
        throw new sfException('Attribute count incorrect.');
      }

      $rt_shop_stock = Doctrine::getTable('rtShopStock')->findOneByVariationsAndProductId($variation_ids, $rt_shop_product->getId());

      if(!$rt_shop_stock)
      {
        throw new sfException('Stock error, no match found for variations: ' . implode(', ', $variation_ids));
      }
    }

    $this->order = $this->getOrder();

    if($this->_cart->addToCart($rt_shop_stock->getId(),(int) $request->getParameter('rt-shop-quantity')))
    {
      $this->getUser()->setFlash('notice', "Item was added to cart.");
    }
    else
    {
      $this->getUser()->setFlash('error', "Item was not added to cart The quantity ordered is not in stock.");
    }

    $this->redirect('@rt_shop_order_cart');
  }

  /**
   * Delete a stock item from the order and redirects back to either the cart or the checkout.
   *
   * @param sfWebRequest $request
   */
  public function executeDeleteStock(sfWebRequest $request)
  {
    $stock_id = (int)$request->getParameter('id');
    $stock = Doctrine::getTable('rtShopStock')->find($stock_id);
    
    if($this->_cart->removeFromCart($stock_id))
    {
      $this->getUser()->setFlash('notice', "Item was removed from cart.");
    }
    else
    {
      $this->getUser()->setFlash('error', "Item could not be removed from cart.");
    }

    $this->redirect('@rt_shop_order_cart');
  }

  /**
   * Update the cart quantity levels for each stock item
   *
   * @param sfWebRequest $request
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->order = $this->getOrder();
    $stock_exceeded = array();

    // array_combine(stock_id, quantity)
    $comb_array = array_combine($request->getParameter('stock_id'), $request->getParameter('quantity'));

    foreach($comb_array as $key => $value)
    {
      $stock = Doctrine::getTable('rtShopStock')->find($key);

      if(!$stock)
      {
        return false;
      }

      if ($value == 0) {
        $this->_cart->removeFromCart($key);
      }
      else
      {
        // Check if quantity ordered is available when backorder is not allowed
        if ($stock->rtShopProduct->getBackorderAllowed() == false && $value > $stock->getQuantity()) {
          $this->getUser()->setFlash('error', "Sorry, but we only have a few items left of that product. Please reduce the requested quantity.");
          $stock_exceeded[$stock->getId()] = $stock->getQuantity();
        } else {
          $this->_cart->removeFromCart($key);
          $this->_cart->addToCart($key,(int)$value);
        }
      }
    }

    // Only go to checkout when no quantity errors
    if (count($stock_exceeded) == 0 && $request->hasParameter('_proceed_to_checkout')) {
      $this->redirect('@rt_shop_order_checkout');
    }

    $this->getUser()->setAttribute('update_quantities', $comb_array);
    $this->getUser()->setFlash('rtShopStock',$stock_exceeded);
    $this->getUser()->setFlash('notice', 'Cart was updated!');
    $this->redirect('@rt_shop_order_cart');
  }
  
  /**
   * Executes the checkout page
   *
   * @param sfWebRequest $request
   */
  public function executeCheckout(sfWebRequest $request)
  {
    $this->order = $this->getOrder();
    $this->redirectUnless(count($this->_order->Stocks) > 0, '@rt_shop_order_cart');
    
    $this->redirect('@rt_shop_order_address');
  }

  /**
   * Process form
   *
   * @param $request Request Data
   * @param $form Form data
   */
  protected function processForm(sfWebRequest $request, sfForm $form, $form_name = null)
  {
    if(is_null($form_name))
    {
      $form_name = $form->getName();
    }

    $request_params = $request->getParameter($form_name);

    $form->bind($request_params);
  }

  /**
   * Executes the address page
   *
   * @param sfWebRequest $request
   */
  public function executeAddress(sfWebRequest $request)
  {
    $this->order = $this->getOrder();

    $this->redirectUnless(count($this->order->Stocks) > 0, '@rt_shop_order_cart');

    $this->billing_address_shown = $request->getParameter('billing_address_shown', false);

    $this->order_form = new rtShopOrderEmailForm($this->order);

    // Shipping address object
    $q = Doctrine_Query::create()
        ->from('rtAddress a')
        ->andWhere('a.model = ?', 'rtShopOrder')
        ->andWhere('a.model_id = ?', $this->order->getId())
        ->andWhere('a.type = ?', 'shipping');
    $address_shipping = $q->fetchOne();

    if(!$address_shipping)
    {
      $address_shipping = new rtAddress;
    }

    // Billing address object
    $address_shipping->setModel('rtShopOrder');
    $address_shipping->setModelId($this->order->getId());
    $address_shipping->setType('shipping');
    $this->shipping_order_form = new rtShopShippingAddressForm($address_shipping);

    $q = Doctrine_Query::create()
        ->from('rtAddress a')
        ->andWhere('a.model = ?', 'rtShopOrder')
        ->andWhere('a.model_id = ?', $this->order->getId())
        ->andWhere('a.type = ?', (count($this->order->getBillingAddressArray()) == 0) ? 'shipping' : 'billing');
    $address_billing = $q->fetchOne();

    if(!$address_billing)
    {
      $address_billing = new rtAddress;
    }

    $address_billing->setModel('rtShopOrder');
    $address_billing->setModelId($this->order->getId());
    $address_billing->setType('billing');
    $this->billing_order_form = new rtShopBillingAddressForm($address_billing);

    if ($this->getRequest()->isMethod('PUT') || $this->getRequest()->isMethod('POST'))
    {
      $this->processForm($request, $this->order_form);
      $this->processForm($request, $this->shipping_order_form);

      // The values of the billing address are sometimes the same as the shipping address
      $billing_form_name = $this->billing_address_shown ? $this->shipping_order_form->getName() : $this->billing_order_form->getName();
      $this->processForm($request, $this->billing_order_form, $billing_form_name);

      // Save email address in order
      if($this->order_form->isValid())
      {
        $this->order->setEmail($this->order_form->getValue('email'));
        $this->order->save();
      }

      if(!$this->order_form->isValid() || !$this->shipping_order_form->isValid() || !$this->billing_order_form->isValid())
      {
        $this->getUser()->setFlash('error', 'Some form data is missing or incorrect. Please check.',false);
        return;
      }

      $this->shipping_order_form->save();
      $this->billing_order_form->save();

      $this->redirect('@rt_shop_order_payment');
    }
  }

  /**
   * Executes the payment page
   *
   * @param sfWebRequest $request
   */
  public function executePayment(sfWebRequest $request)
  {
    $this->order = $this->getOrder();
    
    $this->redirectUnless(count($this->order->Stocks) > 0, '@rt_shop_order_cart');

    $this->voucher_form = new rtShopVoucherCodeForm();
    $this->creditcard_form = new rtShopCreditcardForm();

    if ($this->getRequest()->isMethod('PUT') || $this->getRequest()->isMethod('POST'))
    {
      $this->processForm($request, $this->voucher_form);
      $this->processForm($request, $this->creditcard_form);

      if(!$this->voucher_form->isValid() || !$this->creditcard_form->isValid())
      {
        $this->getUser()->setFlash('error', 'Some form data is missing or incorrect. Please check.');
        return;
      }

      // Apply voucher to order total
      $voucher_code = $this->voucher_form->getValue('code');
      $this->_cart->setVoucher($voucher_code);
      $this->total = (isset($voucher_code)) ? $this->_cart->getTotal() : $this->order->getGrandTotalPrice();

      $cc_array = $this->FormatCcInfoArray($this->creditcard_form->getValues());
      $address = (count($this->order->getBillingAddressArray()) > 0) ? $this->order->getBillingAddressArray() : $this->order->getShippingAddressArray();
      $address = (count($address) > 1) ? $address[0] : $address;
      $customer_array = $this->FormatCustomerInfoArray($address, $this->order->getEmail());

      $payment = rtShopPaymentToolkit::getPaymentObject(sfConfig::get('app_rt_shop_payment_class','rtShopPayment'));
      if($payment->doPayment((int) $this->total*100, $cc_array, $customer_array)) //$total, $credit_card, $customer = array(), $options = array()
      {
        if($payment->isApproved()) {
          $this->order->setPaymentApproved($payment->isApproved());
          $this->order->setPaymentTransactionId($payment->getTransactionNumber());
          $this->order->setPaymentCharge($this->total);
          $this->order->setPaymentResponse($payment->getLog());
          $this->order->setStatus(rtShopOrder::STATUS_PAID); // Set status to paid
          $this->order->save();

          $this->getUser()->setFlash('notice', 'Payment approved. Order was saved.');
        }
        else
        {
          $this->order->setPaymentCharge($this->total);
          $this->order->setPaymentResponse($payment->getLog());
          $this->order->save();

          $this->getUser()->setFlash('error', sprintf('%s',$payment->getResponseMessage()));
          return;
        }
      }
      else
      {
        throw new sfException("Something went very wrong - our technicians are looking into it right now.");
      }

      //send mail

      //$this->redirect('@rt_shop_order_receipt');
    }
  }

  /**
   * Get formatted info array for payment
   *
   * @param array $cc_values Credit card details
   * @return Array
   */
  protected function FormatCcInfoArray($cc_values)
  {
    $options = array('CardHoldersName'  => $cc_values['cc_name'],
                     'CardType'         => $cc_values['cc_type'],
                     'CardNumber'       => $cc_values['cc_number'],
                     'CardExpiryMonth'  => $cc_values['cc_expire']['month'],
                     'CardExpiryYear'   => $cc_values['cc_expire']['year'],
                     'CVN'              => $cc_values['cc_verification']);

    return $options;
  }

  /**
   * Get formatted customer array for payment
   *
   * @param array $customer  Customer details
   * @return Array
   */
  protected function FormatCustomerInfoArray($address, $email, $invoice_desc = '', $invoice_ref = '')
  {
    $adr = sprintf('%s%s%s', (isset($address['address_1'])) ? $address['address_1']:'',(isset($address['town'])) ? ', '.$address['town']:'',(isset($address['state'])) ? ', '.$address['state']:'');

    $options = array('CustomerFirstName' => '',
                     'CustomerLastName' => (isset($address['care_of'])) ? $address['care_of']: '',
                     'CustomerEmail' => $email,
                     'CustomerAddress' => $adr,
                     'CustomerPostcode' => (isset($address['postcode'])) ? $address['postcode']: '',
                     'CustomerInvoiceDescription' => $invoice_desc,
                     'CustomerInvoiceRef' => $invoice_ref);

    return $options;
  }

  /**
   * Show order receipt
   *
   * @param sfWebRequest $request
   */
  public function executeReceipt(sfWebRequest $request)
  {
    // Show receipt and send mail

    //$this->cleanSession();
  }

  /**
   * Clean defined session variables
   *
   */
  private function cleanSession() {
    // Overwrite session token for order id
    $this->getUser()->setAttribute($this->_session_token, '');

    // Reset transaction token
    if ($this->getUser()->hasAttribute($this->_transaction_token)) {
      $this->getUser()->setAttribute($this->_transaction_token, '');
    }

    // Reset unique user token
    if ($this->getUser()->hasAttribute($this->_user_id_token)) {
      $this->getUser()->setAttribute($this->_user_id_token, '');
    }
  }

  /**
   * Get order object
   *
   * This object will be set into the databases, so an ID value is always guaranteed.
   *
   * @param $request Request Data
   * @param $session_token Session_token
   * @return rtShopOrder A rtShopOrder object with id
   */
  public function getOrder($session_token = null)
  {
    if(!is_null($this->_order))
    {
      return $this->_order;
    }

    $cm = new rtShopCartManager($this->getUser());
    $order = $cm->getOrder();
    
    $this->_cart = $cm;
    $this->_order = $order;
    return $this->_order;
  }
}