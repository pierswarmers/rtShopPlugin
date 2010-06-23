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
    $this->rt_shop_order = $this->getOrder();
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
    $this->rt_shop_order = $this->getOrder();
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

    $this->rt_shop_order = $this->getOrder();

    $this->_cart->addToCart($rt_shop_stock->getId(),(int) $request->getParameter('rt-shop-quantity'));

    $this->getUser()->setFlash('notice', "Item was added to cart.");

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
    $this->rt_shop_order = $this->getOrder();
    $this->redirectIf(count($this->getOrder()->Stocks) == 0, '@rt_shop_order_cart');

    $this->billing_address_shown = $request->getParameter('billing_address_shown', false);

    $this->order_form = new rtShopOrderEmailForm($this->getOrder());

    // Shipping address object
    $q = Doctrine_Query::create()
        ->from('rtAddress a')
        ->andWhere('a.model = ?', 'rtShopOrder')
        ->andWhere('a.model_id = ?', $this->getOrder()->getId())
        ->andWhere('a.type = ?', 'shipping');
    $address_shipping = $q->fetchOne();

    if(!$address_shipping)
    {
      $address_shipping = new rtAddress;
    }

    // Billing address object
    $address_shipping->setModel('rtShopOrder');
    $address_shipping->setModelId($this->getOrder()->getId());
    $address_shipping->setType('shipping');
    $this->shipping_order_form = new rtShopShippingAddressForm($address_shipping);

    $q = Doctrine_Query::create()
        ->from('rtAddress a')
        ->andWhere('a.model = ?', 'rtShopOrder')
        ->andWhere('a.model_id = ?', $this->getOrder()->getId())
        ->andWhere('a.type = ?', (count($this->getOrder()->getBillingAddressArray()) == 0) ? 'shipping' : 'billing');
    $address_billing = $q->fetchOne();

    if(!$address_billing)
    {
      $address_billing = new rtAddress;
    }

    $address_billing->setModel('rtShopOrder');
    $address_billing->setModelId($this->getOrder()->getId());
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
        $this->getOrder()->setEmail($this->order_form->getValue('email'));
        $this->getOrder()->save();
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
    $this->rt_shop_order = $this->getOrder();

    $this->voucher_form = new rtShopVoucherCodeForm();
    $this->creditcard_form = new rtShopCreditcardForm();

    if ($this->getRequest()->isMethod('PUT') || $this->getRequest()->isMethod('POST'))
    {
      $this->processForm($request, $this->voucher_form);
      $this->processForm($request, $this->creditcard_form);

      if(!$this->voucher_form->isValid() || !$this->creditcard_form->isValid())
      {
        $this->getUser()->setFlash('error', 'Some form data is missing or incorrect. Please check.');
      }

      // Apply voucher to order total
      $voucher_code = $this->voucher_form->getValue('code');
      $this->_cart->setVoucher($voucher_code);
      $this->total = (isset($voucher_code)) ? $this->_cart->getTotal() : $this->getOrder()->getGrandTotalPrice();
    }

    $cc_values = $this->creditcard_form->getValues();
    $billing_address = $this->getOrder()->getBillingAddressArray();

    //$this->redirect('@rt_shop_order_receipt');
  }

  /**
   * Show order receipt
   *
   * @param sfWebRequest $request
   */
  public function executeReceipt(sfWebRequest $request)
  {
    // Show receipt and send mail
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
  public function getOrder()
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