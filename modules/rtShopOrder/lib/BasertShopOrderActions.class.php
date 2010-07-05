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

    $this->form = new rtShopOrderEmailForm($this->getOrder());

    if ($this->getRequest()->isMethod('PUT') || $this->getRequest()->isMethod('POST'))
    {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if($this->form->isValid())
      {
        $this->form->save();
        $this->redirect('rt_shop_order_payment');
      }
    }
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
        $cm->setVoucher($voucher_code);
        $cm->getOrder()->setVoucherCode($voucher_code);
        $cm->getOrder()->save();
      }
      
      if($cm->getTotal() > 0)
      {
        $this->logMessage('{rtShopPayment} Order: '.$cm->getOrder()->getReference().'. Proceeding to charge credit card with: ' . $cm->getTotal());

        $cc_array = $this->FormatCcInfoArray($this->form_cc->getValues());
        $address = $cm->getOrder()->getBillingAddressArray();
        $customer_array = $this->FormatCustomerInfoArray($address[0], $cm->getOrder()->getEmail());

        $payment = rtShopPaymentToolkit::getPaymentObject(sfConfig::get('app_rt_shop_payment_class','rtShopPayment'));

        if($payment->doPayment((int) $cm->getTotal()*100, $cc_array, $customer_array))
        {
          if($payment->isApproved())
          {
            $cm->getOrder()->setStatus(rtShopOrder::STATUS_PAID);
            $cm->getOrder()->setPaymentType(get_class($payment));
            $cm->getOrder()->setPaymentApproved($payment->isApproved());
            $cm->getOrder()->setPaymentTransactionId($payment->getTransactionNumber());
            $cm->getOrder()->setPaymentCharge($cm->getTotal());
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
    //Show receipt and send mail

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
      $this->_rt_shop_cart_manager = new rtShopCartManager($this->getUser());
    }
    
    return $this->_rt_shop_cart_manager;
  }

  /**
   * Updates user session details with latest cart info
   */
  private function updateUserSession()
  {
    $this->getUser()->setAttribute('rt_shop_order_cart_items', count($this->getOrder()->getStockInfoArray()));
    $this->getUser()->setAttribute('rt_shop_order_cart_total', $this->getCartManager()->getTotal());
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
}