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
 * @method     sfUser        getUser()               Return the session user
 * @method     void          redirect()              Redirect the response
 * @method     boolean       hasRequestParameter()   Test for a request parameter
 * @method     boolean       hasParameter()   Test for a request parameter
 * @method     string       getRequestParameter()   Return request paramater
 * @method     string       getParameter()   Return request paramater
 */
class BasertShopOrderActions extends sfActions
{
  private $_session_token = 'rt_shop_frontend_order_id';
  private $_rt_shop_cart_manager;

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
   * Executes the index page, essentually a redirect to the cart page
   *
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->redirect('rt_shop_order_cart');
  }

  /**
   * Executes the cart page - first step in order completion.
   *
   * @param sfWebRequest $request
   */
  public function executeCart(sfWebRequest $request)
  {
    $this->rt_shop_cart_manager = $this->getCartManager();
    $this->updateUserSession();
  }
  
  /**
   * Add to shopping bag function then redirect to cart, or in the case of an
   * error redirect to product details screen
   *
   * @param sfWebRequest $request
   */
  public function executeAddToBag(sfWebRequest $request)
  {
    $rt_shop_product = Doctrine::getTable('rtShopProduct')->find($request->getParameter('rt-shop-product-id'));
    $this->forward404Unless($rt_shop_product);
    $stock_id = null;

    if($request->hasParameter('rt-shop-stock-id'))
    {
      $stock_id = $request->getParameter('rt-shop-stock-id');
    }
    else
    {
      if(count($rt_shop_product->rtShopAttributes) > 0 && !$request->hasParameter('rt-shop-variation-ids'))
      {
        $this->getUser()->setFlash('error', 'Please select one of each product options.');
        $this->redirect('rt_shop_product_show', $rt_shop_product);
      }

      $variation_ids = $request->getParameter('rt-shop-variation-ids');

      if(count($rt_shop_product->rtShopAttributes) != count($variation_ids))
      {
        $this->getUser()->setFlash('error', 'Please select one of each product options. It looks like you missed one or more.');
        $this->redirect('rt_shop_product_show', $rt_shop_product);
      }

      if(is_null($variation_ids) || $variation_ids == '')
      {
        // No variation on stock. Get stock object through product id
        $rt_shop_stock = Doctrine::getTable('rtShopStock')->findByProductId($rt_shop_product->getId());
        
        if(count($rt_shop_stock) > 1)
        {
          throw new sfException('Products without variations can only have one stock item. More than one found.');
        }
        
        $rt_shop_stock = $rt_shop_stock[0];

        $this->checkIfStockIsAvailable($rt_shop_stock);

        $stock_id = $rt_shop_stock->id;
      }
      else
      {
        // Has variations
        $rt_shop_stock = Doctrine::getTable('rtShopStock')->findOneByVariationsAndProductId($variation_ids, $rt_shop_product);

        $this->checkIfStockIsAvailable($rt_shop_stock);

        $stock_id = $rt_shop_stock->getId();
      }
    }

    if(!$this->getCartManager()->addToCart($stock_id,(int) $request->getParameter('rt-shop-quantity')))
    {
      $this->getUser()->setFlash('error', 'We don\'t seem to have enough stock available for that selection.');
      $this->redirect('rt_shop_product_show', $rt_shop_product);
    }

    $this->updateUserSession();

    $this->getUser()->setFlash('notice', 'Product added to ' . sfConfig::get('rt_shop_cart_name', 'shopping bag') . '.');
    $this->redirect('rt_shop_product_show', $rt_shop_product);
  }

  /**
   * Check if stock exists, and redirect back to product if there are any issues.
   *
   * @param rtShopStock $rt_shop_stock
   */
  private function checkIfStockIsAvailable(rtShopStock $rt_shop_stock)
  {
    if(!$rt_shop_stock)
    {
      $this->getUser()->setFlash('error', 'We don\'t seem to have any stock available for that selection.');
      $this->redirect('rt_shop_product_show', $rt_shop_stock->getRtShopProduct());
    }
  }

  /**
   * Delete a stock item from the order and redirects back to either the cart or the checkout.
   *
   * @param sfWebRequest $request
   */
  public function executeDeleteStock(sfWebRequest $request)
  {
    $this->getCartManager()->removeFromCart((int)$request->getParameter('id'));
    $this->getUser()->setFlash('notice', 'Product was removed from ' . sfConfig::get('rt_shop_cart_name', 'shopping bag') . '.');
    $this->updateUserSession();
    $this->redirect('rt_shop_order_cart');
  }

  /**
   * Update the cart quantity levels for each stock item
   *
   * @param sfWebRequest $request
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->redirectIf($this->cartIsPopulatedByVoucherOnly(), 'rt_shop_order_membership');
    
    $this->rt_shop_cart_manager = $this->getCartManager();

    if($request->getMethod() !== 'POST')
    {
      $this->redirect('rt_shop_order_cart');
    }

    $stock_exceeded = array();
    $stock_error = false;

    // array_combine(stock_id, quantity)
    $comb_array = array_combine($request->getParameter('stock_id'), $request->getParameter('quantity'));

    foreach($comb_array as $key => $value)
    {
      $stock = Doctrine::getTable('rtShopStock')->find($key);
      if(!$stock)
      {
        return false;
      }

      if($value == 0) {
        $this->getCartManager()->removeFromCart($key);
      }
      else
      {
        // Check if quantity ordered is available when backorder is not allowed
        if($stock->rtShopProduct->getBackorderAllowed() == false && $value > $stock->getQuantity()) {
          $stock_error = true;
          $stock_exceeded[$stock->getId()] = $stock->getQuantity();
        } else {
          $this->getCartManager()->removeFromCart($key);
          $this->getCartManager()->addToCart($key,(int)$value);
        }
      }
    }

    $this->getCartManager()->resetShippingCharge();
    $this->updateUserSession();

    // Only go to checkout when no quantity errors
    if(count($stock_exceeded) == 0 && $request->hasParameter('_proceed_to_checkout'))
    {
      $this->redirect('rt_shop_order_membership');
    }

    if($stock_error)
    {
      $this->getUser()->setFlash('error', ucfirst(sfConfig::get('rt_shop_cart_name', 'shopping bag')) . ' was updated, but some items didn\'t have enough stock');
    }
    else
    {
      $this->getUser()->setFlash('notice', ucfirst(sfConfig::get('rt_shop_cart_name', 'shopping bag')) . ' was updated');
    }

    $this->rt_shop_order = $this->getOrder();
    $this->update_quantities = $comb_array;
    $this->stock_exceeded = $stock_exceeded;

    $this->redirect('rt_shop_order_cart');
  }

  /**
   * This action primarily prompts the user to either create an account or login.
   *
   * @param sfWebRequest $request
   */
  public function executeMembership(sfWebRequest $request)
  {
    // Are there items in the cart? If no, redirect backwards...
    $this->redirectUnless($this->cartIsPopulated(), 'rt_shop_order_cart');

    if($this->getUser()->getAttribute('registration_success', false))
    {
      $this->getUser()->setFlash('notice', 'You are registered and signed in!');
    }

    // Is this user already logged in? If yes, redirect forwards...
    $this->redirectIf($this->getUser()->isAuthenticated(), 'rt_shop_order_address');

    $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'rtGuardFormSignin');
    $this->form_user = new $class();
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
    $this->redirectUnless($this->cartIsPopulated(), '@rt_shop_order_cart');

    $this->show_shipping = false;
    $order = $this->getOrder();
    
    if(Doctrine::getTable('rtAddress')->getAddressForObjectAndType($order, 'shipping'))
    {
      $this->show_shipping = true;
    }

    $rt_guard_user = false;

    if($this->getUser()->isAuthenticated())
    {
      $rt_guard_user = Doctrine::getTable('rtGuardUser')->find($this->getUser()->getGuardUser()->getId());
    }
    
    if($rt_guard_user && is_null($order->getEmailAddress()))
    {
      $this->getOrder()->setEmailAddress($rt_guard_user->getEmailAddress());
    }

    $this->form = new rtShopOrderEmailForm($order);

    $this->form_billing = new rtAddressForm($this->getAddressByType('billing'), array('object' => $order,'is_optional' => false, 'use_names' => true));
    $this->form_billing->getWidgetSchema()->setNameFormat('rt_address_billing[%s]');
    
    $this->form_shipping = new rtAddressForm($this->getAddressByType('shipping'), array('object' => $order,'is_optional' => true, 'use_names' => true));
    $this->form_shipping->getWidgetSchema()->setNameFormat('rt_address_shipping[%s]');

    if($this->getRequest()->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      $this->form_billing->bind($request->getParameter($this->form_billing->getName()));

      if($this->form->isValid() && $this->form_billing->isValid())
      {
        $valid = true;
        
        $this->getCartManager()->resetShippingCharge();

        if(!$request->hasParameter('shipping_toggle'))
        {
          // We need to verify the shipping...
          $this->form_shipping->bind($request->getParameter($this->form_shipping->getName()));
          
          if($this->form_shipping->isValid())
          {
            $this->form_shipping->save();
            if($rt_guard_user)
            {
              $this->updateUserAddressInfo($rt_guard_user, 'shipping', $this->form_shipping->getObject());
            }
          }
          else
          {
            $valid = false;
            $this->show_shipping = true;
          }
        }
        else
        {
          // Try to clean any existing shipping addresses...
          $this->form_shipping->getObject()->delete();
        }

        if($valid)
        {
          $this->form->save();
          $this->form_billing->save();
          if($rt_guard_user)
          {
            $this->updateUserAddressInfo($rt_guard_user, 'billing', $this->form_billing->getObject());
          }
          $this->redirect('rt_shop_order_payment');
        }
      }
      $this->getUser()->setFlash('default_error', true, false);
    }
  }

  /**
   * Return an address for a given type.
   * 
   * @param  $type
   * @return rtAddress
   */
  protected function getAddressByType($type)
  {
    $address = Doctrine::getTable('rtAddress')->getAddressForObjectAndType($this->getOrder(), $type);

    if(!$address && $this->getUser()->isAuthenticated())
    {
      $user = $this->getUser()->getGuardUser();
      $address = Doctrine::getTable('rtAddress')->getAddressForModelAndIdAndType('rtGuardUser', $user->getId(), $type);
      if($address)
      {
        $address = $address->copy(false);
        $address->setFirstName($user->getFirstName());
        $address->setLastName($user->getLastName());
      }
    }

    if(!$address)
    {
      $address = new rtAddress;
    }

    $address->setModelId($this->getOrder()->getId());
    $address->setType($type);
    $address->setModel('rtShopOrder');
    
    return $address;
  }

  /**
   * Redeem voucher function, this adds the voucher to a session via a passed voucher code.
   *
   * @example: http://example.com/order/voucher/redeem?code=123456
   * @example: http://example.com/order/voucher/redeem?code=123456&redirect=http://example.com/some/page
   *
   * @param sfWebRequest $request
   */
  public function executeRedeemVoucher(sfWebRequest $request)
  {
    if(!$this->hasRequestParameter('code'))
    {
      $this->getUser()->setFlash('error', 'No voucher code found.', true);
      $this->redirect('@homepage');
    }

    // Stall brute force testing
    $attempts = $this->getUser()->getAttribute('rt_shop_voucher_redeem_attempt', 0);
    if($attempts > 10)
    {
      $this->getUser()->setFlash('success', 'Excess number of bad voucher codes found... please try again later.', true);
    }
    $this->getUser()->setAttribute('rt_shop_voucher_redeem_attempt', $attempts + 1);

    // Get rtShopVoucher
    $rt_shop_voucher = Doctrine::getTable('rtShopVoucher')->findOneByCode($this->getRequestParameter('code'));

    // Set voucher code
    if($rt_shop_voucher)
    {
      $cm = $this->getCartManager();
      $cm->setVoucherCode($rt_shop_voucher->getCode());
      $this->getUser()->setFlash('notice', 'Voucher has been added.', true);
    }
    else
    {
      $this->getUser()->setFlash('error', 'Voucher not found. Probably a bad voucher code.', true);
    }

    // Allow for redirect parameter
    if($request->hasParameter('redirect'))
    {
      $this->redirect($this->getRequestParameter('redirect'));
    }

    $this->redirect('@homepage');
  }

  /**
   * Can be used with an ajax request to validate and return the voucher.
   *
   * @param sfWebRequest $request
   */
  public function executeCheckVoucher(sfWebRequest $request)
  {
    $rt_shop_cart_manager = $this->getCartManager();

    $this->voucher = array('error' => '', 'id' => '');

    if($request->getParameter('code', '') !== '')
    {
      $voucher = rtShopVoucherToolkit::getApplicable($request->getParameter('code'), $rt_shop_cart_manager->getTotalCharge());

      if($voucher)
      {
        $rt_shop_cart_manager->getOrder()->setVoucherCode($voucher->getCode());
        $this->voucher = $voucher->getData();
      }
      else
      {
        $rt_shop_cart_manager->getOrder()->setVoucherCode(null);
        $this->voucher['error'] = true;
      }
    }
    else
    {
      $rt_shop_cart_manager->getOrder()->setVoucherCode(null);
    }

    $rt_shop_cart_manager->getOrder()->save();
    $this->voucher['shipping_charge'] = $rt_shop_cart_manager->getShippingCharge();
    $this->voucher['total_charge'] = $rt_shop_cart_manager->getTotalCharge();
    $this->voucher['reduction'] = $rt_shop_cart_manager->getVoucherReduction();
    $numberFormat = new sfNumberFormat(sfContext::getInstance()->getUser()->getCulture());
    $this->voucher['reduction_formatted'] = $numberFormat->format($rt_shop_cart_manager->getVoucherReduction(), 'c', sfConfig::get('app_rt_shop_payment_currency','AUD'));
    $this->voucher['total_charge_formatted'] = $numberFormat->format($rt_shop_cart_manager->getTotalCharge(), 'c', sfConfig::get('app_rt_shop_payment_currency','AUD'));
  }

  /**
   * Executes the payment page
   *
   * @param sfWebRequest $request
   */
  public function executePayment(sfWebRequest $request)
  {
    $this->redirectUnless($this->cartIsPopulated(), 'rt_shop_order_cart');

    $rt_shop_cart_manager = $this->getCartManager();

    // If order placed is placed and total charge = 0
    if($this->getCartManager()->getTotalCharge() == 0 && $rt_shop_cart_manager->getVoucherCode() != '')
    {
      $this->closeOrder($request);
      $this->redirect('rt_shop_order_receipt');
    }

    if(!Doctrine::getTable('rtAddress')->getAddressForObjectAndType($rt_shop_cart_manager->getOrder(), 'billing'))
    {
      $this->redirect('rt_shop_order_address');
    }

    $this->updateUserSession();

    $this->form = new rtShopPaymentForm($rt_shop_cart_manager->getOrder(), array('rt_shop_cart_manager' => $rt_shop_cart_manager));
    $this->form_cc = new rtShopCreditCardPaymentForm();

    if($this->getRequest()->isMethod('PUT') || $this->getRequest()->isMethod('POST'))
    {
      // If order placed is placed and total charge = 0
      if($this->getCartManager()->getTotalCharge() == 0)
      {
        $this->closeOrder($request);
        $this->redirect('rt_shop_order_receipt');
      }

      $this->form    ->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      $this->form_cc ->bind($request->getParameter($this->form_cc->getName()), $request->getFiles($this->form_cc->getName()));

      // Has local validation passed?
      if(!$this->form->isValid() || !$this->form_cc->isValid())
      {
        // Validation failed... stop script here
        $this->getUser()->setFlash('default_error', '', false);
        $this->rt_shop_cart_manager = $rt_shop_cart_manager;
        return sfView::SUCCESS;
      }

      // Validation passed... continue with script
      $voucher_code = $this->form->getValue('voucher_code');

      if($voucher_code != '')
      {
        $rt_shop_cart_manager->setVoucherCode($voucher_code);
        $rt_shop_cart_manager->getOrder()->save();
      }

      // Charge is > 0... so process payment
      if($rt_shop_cart_manager->getTotalCharge() > 0)
      {
        $this->logMessage('{rtShopPayment} Order: '.$rt_shop_cart_manager->getOrder()->getReference().'. Proceeding to charge credit card with: ' . $rt_shop_cart_manager->getTotalCharge());

        $cc_array = $this->FormatCcInfoArray($this->form_cc->getValues());
        $address = $rt_shop_cart_manager->getBillingAddress();
        $customer_array = $this->FormatCustomerInfoArray($address, $rt_shop_cart_manager->getOrder()->getEmailAddress());

        $payment = rtShopPaymentToolkit::getPaymentObject(sfConfig::get('app_rt_shop_payment_class','rtShopPayment'));

        $this->logMessage($this->getCartManager()->getPricingInfo());

        if($payment->doPayment((int) bcmul($rt_shop_cart_manager->getTotalCharge(), 100), $cc_array, $customer_array))
        {
          if($payment->isApproved())
          {
            $this->closeOrder($request, $payment);

            $this->logMessage('{rtShopPayment} Payment success for order #'.$rt_shop_cart_manager->getOrder()->getReference().': ' . $payment->getLog());
            $this->redirect('rt_shop_order_receipt');
          }
        }

        $this->logMessage('{rtShopPayment} Payment failure for order #'.$rt_shop_cart_manager->getOrder()->getReference().': '.$payment->getLog());
        $this->getUser()->setFlash('error', $payment->getResponseMessage(), false);
      }
    }
    $this->rt_shop_cart_manager = $rt_shop_cart_manager;
  }

  /**
   * Close order and archive
   *
   * @param sfWebRequest $request
   * @param object $payment Payment object
   */
  public function closeOrder($request, $payment = null)
  {
    $rt_shop_cart_manager = $this->getCartManager();

    $rt_shop_cart_manager->getOrder()->setStatus(rtShopOrder::STATUS_PAID);
    
    if(($rt_shop_cart_manager->getTotalCharge() > 0) && !is_null($payment))
    {
      $rt_shop_cart_manager->getOrder()->setPaymentType(get_class($payment));
      $rt_shop_cart_manager->getOrder()->setPaymentTransactionId($payment->getTransactionNumber());
      $rt_shop_cart_manager->getOrder()->setPaymentCharge($rt_shop_cart_manager->getTotalCharge());
      $rt_shop_cart_manager->getOrder()->setPaymentData(array('response' => $payment->getLog()));
    }
    
    $rt_shop_cart_manager->archive();
    $rt_shop_cart_manager->getOrder()->save();

    exit;

    $this->getDispatcher($request)->notify(new sfEvent($this, 'doctrine.admin.save_object', array('object' => $rt_shop_cart_manager->getOrder())));

    // Adjust stock quantities
    $rt_shop_cart_manager->adjustStockQuantities();
    $rt_shop_cart_manager->adjustVoucherDetails();

    // Reset the products page cache
    $rt_shop_cart_manager->clearCache();

    $this->logMessage('{rtShopCloseOrder} Closed order #'.$rt_shop_cart_manager->getOrder()->getReference().': '.date("Y-m-d H:i:s"));
  }

  /**
   * Show order receipt
   *
   * @param sfWebRequest $request
   */
  public function executeReceipt(sfWebRequest $request)
  {
    $this->redirectUnless($this->cartIsPopulated(), '@rt_shop_order_cart');

    $rt_shop_cart_manager = $this->getCartManager();
    $this->rt_shop_order = $rt_shop_cart_manager->getOrder();

    // Send mail to admin and user
    $order_reference = $rt_shop_cart_manager->getOrder()->getReference();

    // Multipart mail content
    $invoice_html  = $this->getPartial('rtShopOrderAdmin/invoice_html', array('rt_shop_order' => $rt_shop_cart_manager->getOrder()));
    $message_html  = $this->getPartial('rtEmail/layout_html', array('content' => $invoice_html));
    $message_plain = $this->getPartial('rtShopOrderAdmin/invoice_plain', array('rt_shop_order' => $rt_shop_cart_manager->getOrder()));
    
    // Send confirmation mail to customer
    $message = Swift_Message::newInstance()
            ->setContentType('text/html')
            ->setFrom(sfConfig::get('app_rt_shop_order_admin_email', 'from@noreply.com'))
            ->setTo($rt_shop_cart_manager->getOrder()->getEmailAddress())
            ->setSubject(sprintf('Order confirmation: #%s', $order_reference))
            ->setBody($message_html,'text/html')
            ->addPart($message_plain, 'text/plain');;

    if(sfConfig::get('app_rt_shop_order_admin_email'))
    {
      $message->setBcc(sfConfig::get('app_rt_shop_order_admin_email'));
    }

    if(!$this->getMailer()->send($message))
    {
      $this->logMessage('{rtShopReceipt} Email for order #'.$rt_shop_cart_manager->getOrder()->getReference().' could not be sent.');
    }

    $this->cleanSession();
  }

  /**
   * Clean order session
   */
  private function cleanSession()
  {
    $rt_shop_cart_manager = $this->getCartManager();

    // Overwrite session token for order id
    $this->getUser()->setAttribute($this->_session_token, '');

    // Reset transaction token
    if($this->getUser()->hasAttribute($this->_transaction_token)) {
      $this->getUser()->setAttribute($this->_transaction_token, '');
    }

    // Reset unique user token
    if($this->getUser()->hasAttribute($this->_user_id_token)) {
      $this->getUser()->setAttribute($this->_user_id_token, '');
    }

    // Mini cart - items
    if($this->getUser()->hasAttribute('rt_shop_order_cart_items')) {
      $this->getUser()->setAttribute('rt_shop_order_cart_items', '');
    }

    // Mini cart - total
    if($this->getUser()->hasAttribute('rt_shop_order_cart_total')) {
      $this->getUser()->setAttribute('rt_shop_order_cart_total', '');
    }

    // Remove gift voucher from session
    $rt_shop_cart_manager->removeGiftVoucherFromSession();
  }

  /**
   * Proxy method
   *
   * @see rtShopCartManager::getOrder()
   * @return rtShopOrder
   */
  public function getOrder()
  {
    return $this->getCartManager()->getOrder();
  }

  /**
   * Get cart manager object
   *
   * @return rtShopCartManager
   */
  public function getCartManager()
  {
    if(is_null($this->_rt_shop_cart_manager))
    {
      $this->_rt_shop_cart_manager = new rtShopCartManager();
    }

    return $this->_rt_shop_cart_manager;
  }

  /**
   * Updates user session details with latest cart info
   */
  private function updateUserSession()
  { 
    $this->logMessage($this->getCartManager()->getPricingInfo());
    $this->getUser()->setAttribute('rt_shop_order_cart_items', $this->getCartManager()->getItemsInCart());
    $this->getUser()->setAttribute('rt_shop_order_cart_total', $this->getCartManager()->getTotalCharge());
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
                     'CardNumber'       => str_replace(' ', '', $cc_values['cc_number']),  //TODO: Clean number if not done by validator
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

    $options = array('CustomerFirstName' => $address['first_name'],
                     'CustomerLastName' => $address['last_name'],
                     'CustomerEmail' => $email,
                     'CustomerAddress' => $adr,
                     'CustomerPostcode' => (isset($address['postcode'])) ? $address['postcode']: '',
                     'CustomerInvoiceDescription' => $invoice_desc,
                     'CustomerInvoiceRef' => $invoice_ref);

    return $options;
  }

  /**
   * Run a test, and update if nescessary on the user address data.
   *
   * @param rtGuardUser $user
   * @param string      $type
   * @param rtAddress   $address
   */
  private function updateUserAddressInfo(rtGuardUser $user, $type, rtAddress $new_address)
  {
    $address = Doctrine::getTable('rtAddress')->getAddressForObjectAndType($user, $type);
    if(!$address)
    {
      $address = $new_address->copy(false);
      $address->setModel('rtGuardUser');
      $address->setModelId($user->getId());
      $address->save();
    }
  }

  /**
   * Get the admin email address
   *
   * @return string
   */
  private function getAdminEmail()
  {
    return sfConfig::get('app_rt_registration_admin_email', sfConfig::get('app_rt_admin_email'));
  }

  /**
   * Checks if the cart has any items in it. This can include a single voucher.
   *
   * @return boolean
   */
  protected function cartIsPopulated()
  {
    return count($this->getCartManager()->getStockInfoArray()) > 0;
  }

  /**
   * Checks if the cart has only a single voucher in it
   *
   * @return boolean
   */
  protected function cartIsPopulatedByVoucherOnly()
  {
    return count($this->getCartManager()->getStockInfoArray()) === 1 && !$this->getCartManager()->hasRealItems();
  }

  /**
   * @return sfEventDispatcher
   */
  protected function getDispatcher(sfWebRequest $request)
  {
    return ProjectConfiguration::getActive()->getEventDispatcher(array('request' => $request));
  }
}