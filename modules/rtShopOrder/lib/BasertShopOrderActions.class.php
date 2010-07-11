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
   * Executes the index page
   *
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->redirect('rt_shop_order_cart');
  }

  /**
   * Executes the cart page
   *
   * @param sfWebRequest $request
   */
  public function executeCart(sfWebRequest $request)
  {
    $this->rt_shop_cart_manager = $this->getCartManager();
    $this->rt_shop_order = $this->getOrder();

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

      $rt_shop_stock = Doctrine::getTable('rtShopStock')->findOneByVariationsAndProductId($variation_ids, $rt_shop_product);

      if(!$rt_shop_stock)
      {
        $this->getUser()->setFlash('error', 'We don\'t seem to have any stock available for that selection.');
        $this->redirect('rt_shop_product_show', $rt_shop_product);
      }

      $stock_id = $rt_shop_stock->getId();
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

      if ($value == 0) {
        $this->getCartManager()->removeFromCart($key);
      }
      else
      {
        // Check if quantity ordered is available when backorder is not allowed
        if ($stock->rtShopProduct->getBackorderAllowed() == false && $value > $stock->getQuantity()) {
          $stock_error = true;
          $stock_exceeded[$stock->getId()] = $stock->getQuantity();
        } else {
          $this->getCartManager()->removeFromCart($key);
          $this->getCartManager()->addToCart($key,(int)$value);
        }
      }
    }

    $this->updateUserSession();
    
    // Only go to checkout when no quantity errors
    if (count($stock_exceeded) == 0 && $request->hasParameter('_proceed_to_checkout'))
    {
      $this->redirect('rt_shop_order_membership');
    }

    if($stock_error)
    {
      $this->getUser()->setFlash('error', ucfirst(sfConfig::get('rt_shop_cart_name', 'shopping bag')) . ' was updated, but some items didn\'t have enough stock');
    }
    else
    {
      $this->getUser()->setFlash('notice', ucfirst(sfConfig::get('rt_shop_cart_name', 'shopping bag')) . ' was updated.');
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
    $this->redirectIf($this->getCartManager()->isEmpty(), 'rt_shop_order_cart');

    if($this->getUser()->getFlash('registration_success'))
    {
      $this->getUser()->setFlash('notice', 'You are registered and signed in!');
      $this->generateVouchure();
    }

    // Is this user already logged in? If yes, redirect forwards...
    $this->redirectIf($this->getUser()->isAuthenticated(), 'rt_shop_order_address');

    $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin');
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
    $this->redirectUnless(count($this->getOrder()->Stocks) > 0, '@rt_shop_order_cart');

    $this->show_shipping = false;

    if(Doctrine::getTable('rtAddress')->getAddressForObjectAndType($this->getOrder(), 'shipping'))
    {
      $this->show_shipping = true;
    }

    if($this->getUser()->isAuthenticated())
    {
      $user = $this->getUser()->getGuardUser();

      if(is_null($this->getOrder()->getEmail()))
      {
        $this->getOrder()->setEmail($user->getEmailAddress());
      }
    }

    $this->form = new rtShopOrderEmailForm($this->getOrder());

    $billing_address = new rtAddress;

    $shipping_address = new rtAddress;

    $user = $this->getUser()->getGuardUser();

    //$this->form->setDefault('email',$user->getEmailAddress());

    $tmp_address_1 = Doctrine::getTable('rtAddress')->getAddressForObjectAndType($this->getOrder(), 'shipping');
    if($tmp_address_1)
    {
      $shipping_address = $tmp_address_1;
    }
    elseif($this->getUser()->isAuthenticated())
    {
      $rt_user = Doctrine::getTable('rtGuardUser')->find($user->getId());
      $tmp_address_1 = Doctrine::getTable('rtAddress')->getAddressForObjectAndType($rt_user, 'shipping');
      if($tmp_address_1)
      {
        $shipping_address = $tmp_address_1->copy(false);
        $shipping_address->setFirstName($rt_user->getFirstName());
        $shipping_address->setLastName($rt_user->getLastName());
      }
    }
    $tmp_address_2 = Doctrine::getTable('rtAddress')->getAddressForObjectAndType($this->getOrder(), 'billing');
    if($tmp_address_2)
    {
      $billing_address = $tmp_address_2;
    }
    elseif($this->getUser()->isAuthenticated())
    {
      $rt_user = Doctrine::getTable('rtGuardUser')->find($user->getId());
      $tmp_address_2 = Doctrine::getTable('rtAddress')->getAddressForObjectAndType($rt_user, 'billing');
      if($tmp_address_2)
      {
        $billing_address = $tmp_address_2->copy(false);
        $billing_address->setFirstName($rt_user->getFirstName());
        $billing_address->setLastName($rt_user->getLastName());
      }
    }
    
    $shipping_address->setType('shipping');
    $shipping_address->setModel('rtShopOrder');
    $shipping_address->setModelId($this->getOrder()->getId());
    $billing_address->setType('billing');
    $billing_address->setModel('rtShopOrder');
    $billing_address->setModelId($this->getOrder()->getId());

    $this->form_billing = new rtAddressForm($billing_address, array('object' => $this->getOrder(),'is_optional' => false, 'use_names' => true));
    $this->form_billing->getWidgetSchema()->setNameFormat('rt_address_billing[%s]');
    $this->form_shipping = new rtAddressForm($shipping_address, array('object' => $this->getOrder(),'is_optional' => true, 'use_names' => true));
    $this->form_shipping->getWidgetSchema()->setNameFormat('rt_address_shipping[%s]');

    // Run save logic...

    if ($this->getRequest()->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      $this->form_billing->bind($request->getParameter($this->form_billing->getName()), $request->getFiles($this->form_billing->getName()));
      $this->form_shipping->bind($request->getParameter($this->form_shipping->getName()), $request->getFiles($this->form_shipping->getName()));
      
      if($this->form->isValid() && $this->form_billing->isValid())
      {
        // At this point we have the two primary forms vaild...
        $valid = true;

        if(!$request->hasParameter('shipping_toggle'))
        {
          // We need to verify the shipping...
          if($this->form_shipping->isValid())
          {
            $this->form_shipping->save();
            if(isset($rt_user))
            {
              $this->updateUserAddressInfo($rt_user, 'shipping', $this->form_shipping->getObject());
            }
          }
          else
          {
            $valid = false;
          }
        }
        else
        {
          // try to clean any existing shipping addresses...
          $this->form_shipping->getObject()->delete();
        }

        if($valid)
        {
          $this->form->save();
          $this->form_billing->save();
          if(isset($rt_user))
          {
            $this->updateUserAddressInfo($rt_user, 'billing', $this->form_billing->getObject());
          }
          $this->redirect('rt_shop_order_payment');
        }
      }
    }
  }

  /**
   * Can be used with an ajax request to validate and return the voucher.
   * 
   * @param sfWebRequest $request
   */
  public function executeCheckVoucher(sfWebRequest $request)
  {
    $cm = $this->getCartManager();

    $this->voucher = array('error' => '', 'id' => '');

    if($request->getParameter('code', '') !== '')
    {
      $voucher = rtShopVoucherToolkit::getApplicable($request->getParameter('code'), $cm->getTotalCharge());

      if($voucher)
      {
        $cm->getOrder()->setVoucherCode($voucher->getCode());
        $this->voucher = $voucher->getData();
      }
      else
      {
        $cm->getOrder()->setVoucherCode(null);
        $this->voucher['error'] = true;
      }
    }
    else
    {
      $cm->getOrder()->setVoucherCode(null);
    }
    
    $cm->getOrder()->save();
    $this->voucher['shipping_charge'] = $cm->getShippingCharge();
    $this->voucher['total_charge'] = $cm->getTotalCharge();
    $numberFormat = new sfNumberFormat(sfContext::getInstance()->getUser()->getCulture());
    $this->voucher['total_charge_formatted'] = $numberFormat->format($cm->getTotalCharge(), 'c', sfConfig::get('app_rt_shop_payment_currency','AUD'));
  }

  /**
   * Executes the payment page
   *
   * @param sfWebRequest $request
   */
  public function executePayment(sfWebRequest $request)
  {
    // Get the cart manager... all access to order will be through the cart manager.

    $cm = $this->getCartManager();

    $this->redirectUnless(count($cm->getOrder()->Stocks) > 0, 'rt_shop_order_cart');

    if(!Doctrine::getTable('rtAddress')->getAddressForObjectAndType($cm->getOrder(), 'billing'))
    {
      $this->redirect('rt_shop_order_address');
    }

    $this->updateUserSession();

    $this->form = new rtShopPaymentForm($cm->getOrder(), array('rt_shop_cart_manager' => $cm));

    //$this->form->setDefault('voucher_code', $this->getUser()->getAttribute('rt_shop_vouchure_code', ''));

    $this->form_cc = new rtShopCreditCardPaymentForm();

    if ($this->getRequest()->isMethod('PUT') || $this->getRequest()->isMethod('POST'))
    {
      $this->form    ->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      $this->form_cc ->bind($request->getParameter($this->form_cc->getName()), $request->getFiles($this->form_cc->getName()));

      // Has local validation passed?

      if(!$this->form->isValid() || !$this->form_cc->isValid())
      {
        // Validation failed... stop script here
        $this->getUser()->setFlash('default_error', '', false);
        $this->rt_shop_cart_manager = $cm;
        return sfView::SUCCESS;
      }

      // Validation passed... continue with script
      $voucher_code = $this->form->getValue('voucher_code');

      if($voucher_code != '')
      {
        $cm->setVoucherCode($voucher_code);
        $cm->getOrder()->save();
      }
      
      if($cm->getTotalCharge() > 0)
      {
        $this->logMessage('{rtShopPayment} Order: '.$cm->getOrder()->getReference().'. Proceeding to charge credit card with: ' . $cm->getTotalCharge());

        $cc_array = $this->FormatCcInfoArray($this->form_cc->getValues());
        $address = $cm->getOrder()->getBillingAddressArray();
        $customer_array = $this->FormatCustomerInfoArray($address[0], $cm->getOrder()->getEmail());

        $payment = rtShopPaymentToolkit::getPaymentObject(sfConfig::get('app_rt_shop_payment_class','rtShopPayment'));

        $this->logMessage($this->getCartManager()->getPricingInfo());

        if($payment->doPayment((int) $cm->getTotalCharge()*100, $cc_array, $customer_array))
        {
          if($payment->isApproved())
          {
            $cm->getOrder()->setStatus(rtShopOrder::STATUS_PAID);
            $cm->getOrder()->setPaymentType(get_class($payment));
            $cm->getOrder()->setPaymentApproved($payment->isApproved());
            $cm->getOrder()->setPaymentTransactionId($payment->getTransactionNumber());
            $cm->getOrder()->setPaymentCharge($cm->getTotalCharge());
            $cm->getOrder()->setPaymentResponse($payment->getLog());
            $cm->archive();
            $cm->getOrder()->save();

            // Adjust stock quantities
            $cm->adjustStockQuantities();
            $cm->adjustVoucherCount();

            // Reset the products page cache
            $cm->clearCache();

            $this->logMessage('{rtShopPayment} Payment success for order ('.$cm->getOrder()->getReference().'): ' . $payment->getLog());
            $this->redirect('rt_shop_order_receipt');
          }
        }
        
        $this->logMessage('{rtShopPayment} Payment failure for order ('.$cm->getOrder()->getReference().'): '.$payment->getLog());
        $this->getUser()->setFlash('error', $payment->getResponseMessage(), false);
      }
    }
    $this->rt_shop_cart_manager = $cm;
  }

  /**
   * Show order receipt
   *
   * @param sfWebRequest $request
   */
  public function executeReceipt(sfWebRequest $request)
  {
    $cm = $this->getCartManager();
    $this->redirectUnless(count($cm->getOrder()->Stocks) > 0, '@rt_shop_order_cart');

    $this->rt_shop_order = $cm->getOrder();

    if(sfConfig::get('app_rt_shop_order_admin_email'))
    {
      $order_reference = $cm->getOrder()->getReference();
      $from = sfConfig::get('app_rt_shop_order_admin_email');
      $to = $cm->getOrder()->getEmail();
      $subject = sprintf('Order confirmation: #%s', $order_reference);
      $body  = 'Hi,'."\n\n";
      $body  = 'Thank you for your order.'."\n\n";
      $body .= 'Your order with reference #'.$order_reference.' has been received.'."\n\n";
      $body .= 'Sincerely yours,'."\n\n";
      $body .= sfConfig::get('app_rt_email_signature','');
      if (!$this->getMailer()->composeAndSend($from, $to, $subject, $body))
      {
        $this->logMessage('{rtShopReceipt} Email for order #'.$cm->getOrder()->getReference().' could not be sent.');
      }
    }
    else
    {
      $this->logMessage('{rtShopReceipt} Order #'.$cm->getOrder()->getReference().' was successful but confirmation email could not be sent due to missing admin email in configuration.');
    }

    $this->cleanSession();
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

    // Mini cart - items
    if ($this->getUser()->hasAttribute('rt_shop_order_cart_items')) {
      $this->getUser()->setAttribute('rt_shop_order_cart_items', '');
    }
    
    // Mini cart - total
    if ($this->getUser()->hasAttribute('rt_shop_order_cart_total')) {
      $this->getUser()->setAttribute('rt_shop_order_cart_total', '');
    }
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
    $items = 0;
    
    foreach($this->getOrder()->getStockInfoArray() as $stock)
    {
      $items += $stock['rtShopOrderToStock'][0]['quantity'];
    }
    $this->logMessage($this->getCartManager()->getPricingInfo());
    $this->getUser()->setAttribute('rt_shop_order_cart_items', $items);
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
                     'CardNumber'       => $cc_values['cc_number'],  //TODO: Clean number if not done by validator
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
    $address = Doctrine::getTable('rtAddress')->getAddressForObjectAndType($user, 'shipping');
    if(!$address)
    {
      $address = $new_address->copy(false);
      $address->setModel('rtGuardUser');
      $address->setModelId($user->getId());
      $address->save();
    }
  }

  private function generateVouchure()
  {
    if(sfConfig::get('app_rt_shop_registration_voucher_reduction_value', false))
    {
      $voucher = new rtShopVoucher;
      $voucher->setCount(1);
      $voucher->setTitle(sfConfig::get('app_rt_shop_registration_voucher_title', 'Welcome Gift Voucher'));
      $voucher->setReductionType(sfConfig::get('app_rt_shop_registration_voucher_reduction_type', 'dollarOff'));
      $voucher->setReductionValue(sfConfig::get('app_rt_shop_registration_voucher_reduction_value'));
      $user = $this->getUser()->getGuardUser();
      $voucher->setComment(sprintf('Created for: %s %s (%s)', $user->getFirstName(), $user->getLastName(), $user->getEmailAddress()));
      $voucher->save();
      $this->getUser()->setAttribute('rt_shop_vouchure_code', $voucher->getCode());
    }
  }
}