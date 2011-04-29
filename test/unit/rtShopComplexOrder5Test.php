<?php

/*
 * This file is part of the rtShopPlugin package.
 *
 * (c) 2006-2011 digital Wranglers <rtShop@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * To use it copy to test/unit:
 *
 * $ cp plugins/rtShopPlugin/test/unit/rtShopComplexOrder5Test.php test/unit/rtShopComplexOrder5Test.php
 *
 */

/**
 * rtShopComplexOrder5 Testing - Gift voucher testing
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
include(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(49, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
$configuration->loadHelpers('Number');
sfContext::createInstance($configuration);

new sfDatabaseManager($configuration);

$t->comment('/////////////////////////////////////////////////////////////////////////////');
$t->comment('/// Complex Order #5                                                      ///');
$t->comment('/////////////////////////////////////////////////////////////////////////////');

$t->comment('');
$t->comment('Case #1: Gift Voucher only (no taxes, promotions, etc.)');
$t->comment('Case #2: Gift Voucher & product (no taxes, promotions, etc.)');
$t->comment('Case #3: Gift Voucher & product (no taxes, promotions, etc.) | voucher set in cart (Special case: total order value comes to zero)');
$t->comment('Case #4: Gift Voucher & product (tax | shipping) | voucher set in cart');
$t->comment('Case #5: Gift Voucher & product (tax | shipping | cart promotion) | voucher set in cart');
$t->comment('Case #6: Gift Voucher & products (tax | shipping | cart promotion) | voucher set in cart');

// Add data
rtShopComplexOrder5TestTools::clean();

try {
  $prod1 = new rtShopProduct();
  $prod1->setTitle('Product A');
  $prod1->save();
  $prod2 = new rtShopProduct();
  $prod2->setTitle('Product B');
  $prod2->save();
  $prod3 = new rtShopProduct();
  $prod3->setTitle('Product C');
  $prod3->save();
} catch (Exception $e) {
  throw new sfException('Products could not be added! Please check.');
}

try {
  $cat1 = new rtShopCategory();
  $cat1->setTitle('Home');
  $cat1->setRootId(1);
  $cat1->setLft(1);
  $cat1->setRgt(4);
  $cat1->setLevel(0);
  $cat1->save();
  $cat2 = new rtShopCategory();
  $cat2->setTitle('Category A');
  $cat2->setRootId(1);
  $cat2->setLft(2);
  $cat2->setRgt(3);
  $cat2->setLevel(1);
  $cat2->save();
} catch (Exception $e) {
  throw new sfException('Categories could not be added! Please check.');
}

try {
  $prod1tocat2 = new rtShopProductToCategory();
  $prod1tocat2->setProductId($prod1->getId());
  $prod1tocat2->setCategoryId($cat2->getId());
  $prod1tocat2->save();
  $prod2tocat2 = new rtShopProductToCategory();
  $prod2tocat2->setProductId($prod2->getId());
  $prod2tocat2->setCategoryId($cat2->getId());
  $prod2tocat2->save();
  $prod3tocat2 = new rtShopProductToCategory();
  $prod3tocat2->setProductId($prod3->getId());
  $prod3tocat2->setCategoryId($cat2->getId());
  $prod3tocat2->save();
} catch (Exception $e) {
  throw new sfException('Products could not be added to categories! Please check.');
}

try {
  $att1 = new rtShopAttribute();
  $att1->setTitle('Attribute A');
  $att1->save();
  $att2 = new rtShopAttribute();
  $att2->setTitle('Attribute B');
  $att2->save();
} catch (Exception $e) {
  throw new sfException('Attributes could not be added! Please check.');
}

try {
  $prod1toatt1 = new rtShopProductToAttribute();
  $prod1toatt1->setProductId($prod1->getId());
  $prod1toatt1->setAttributeId($att1->getId());
  $prod1toatt1->save();
  $prod1toatt2 = new rtShopProductToAttribute();
  $prod1toatt2->setProductId($prod1->getId());
  $prod1toatt2->setAttributeId($att2->getId());
  $prod1toatt2->save();
  $prod2toatt1 = new rtShopProductToAttribute();
  $prod2toatt1->setProductId($prod2->getId());
  $prod2toatt1->setAttributeId($att1->getId());
  $prod2toatt1->save();
  $prod2toatt2 = new rtShopProductToAttribute();
  $prod2toatt2->setProductId($prod2->getId());
  $prod2toatt2->setAttributeId($att2->getId());
  $prod2toatt2->save();
  $prod3toatt1 = new rtShopProductToAttribute();
  $prod3toatt1->setProductId($prod3->getId());
  $prod3toatt1->setAttributeId($att1->getId());
  $prod3toatt1->save();
  $prod3toatt2 = new rtShopProductToAttribute();
  $prod3toatt2->setProductId($prod3->getId());
  $prod3toatt2->setAttributeId($att2->getId());
  $prod3toatt2->save();
} catch (Exception $e) {
  throw new sfException('Attributes could not be added to products! Please check.');
}

try {
  $var1 = new rtShopVariation();
  $var1->setTitle('A1');
  $var1->setAttributeId($att1->getId());
  $var1->setPosition(1);
  $var1->save();
  $var2 = new rtShopVariation();
  $var2->setTitle('B1');
  $var2->setAttributeId($att2->getId());
  $var2->setPosition(1);
  $var2->save();
} catch (Exception $e) {
  throw new sfException('Variations could not be added! Please check.');
}

try {
  $stock1 = new rtShopStock();
  $stock1->setProductId($prod1->getId());
  $stock1->setQuantity(5);
  $stock1->setSku(mt_rand(1,100000));
  $stock1->setPriceRetail(40);
  $stock1->save();
  $stock2 = new rtShopStock();
  $stock2->setProductId($prod2->getId());
  $stock2->setQuantity(5);
  $stock2->setSku(mt_rand(1,100000));
  $stock2->setPriceRetail(40);
  $stock2->setPricePromotion(30);
  $stock2->save();
  $stock3 = new rtShopStock();
  $stock3->setProductId($prod3->getId());
  $stock3->setQuantity(5);
  $stock3->setSku(mt_rand(1,100000));
  $stock3->setPriceRetail(40);
  $stock3->save();
} catch (Exception $e) {
  throw new sfException('Stocks could not be added! Please check.');
}

try {
  $stock1tovar1 = new rtShopStockToVariation();
  $stock1tovar1->setStockId($stock1->getId());
  $stock1tovar1->setVariationId($var1->getId());
  $stock1tovar1->save();
  $stock1tovar2 = new rtShopStockToVariation();
  $stock1tovar2->setStockId($stock1->getId());
  $stock1tovar2->setVariationId($var2->getId());
  $stock1tovar2->save();
  $stock2tovar1 = new rtShopStockToVariation();
  $stock2tovar1->setStockId($stock2->getId());
  $stock2tovar1->setVariationId($var1->getId());
  $stock2tovar1->save();
  $stock2tovar2 = new rtShopStockToVariation();
  $stock2tovar2->setStockId($stock2->getId());
  $stock2tovar2->setVariationId($var2->getId());
  $stock2tovar2->save();
  $stock3tovar1 = new rtShopStockToVariation();
  $stock3tovar1->setStockId($stock3->getId());
  $stock3tovar1->setVariationId($var1->getId());
  $stock3tovar1->save();
  $stock3tovar2 = new rtShopStockToVariation();
  $stock3tovar2->setStockId($stock3->getId());
  $stock3tovar2->setVariationId($var2->getId());
  $stock3tovar2->save();
} catch (Exception $e) {
  throw new sfException('Stocks could not be added to variations! Please check.');
}

$tools = new rtShopComplexOrder5TestTools;

// Set no taxes and shipping rates
sfConfig::set('app_rt_shop_tax_rate', 0);
sfConfig::set('app_rt_shop_tax_mode', 'exclusive');
sfConfig::set('app_rt_shop_shipping_charges', array('default' => 0, 'AU' => 0));

// ==============================================================================================================================

// Create new cart manager instance
try {
  $cm = new rtShopCartManager();
} catch (Exception $e) {
  throw new sfException('Cart manager instance could not be created! Please check.');
}

// Voucher manager
$vm = $cm->getVoucherManager();

// Add gift voucher to session
$welcome_voucher = $tools->createGiftVoucherDataArray(50);
$vm->setSessionVoucherArray($welcome_voucher);
$gift_voucher = $vm->persistSessionVoucher();

// Add addresses to order
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Loop through stocks in order
$t->diag('');
$t->diag('-----------------------------------------------------------------------------------------------------------------------------');
$t->diag('Case 1');
$t->comment('*****************************************************************************');
$tools->displayOrderItems($t, $cm->getStockInfoArray(), array(50), $gift_voucher);
$t->comment('*****************************************************************************');
// ItemsCharge
$t->is($cm->getItemsCharge(),50,'ItemsCharge: '.format_currency($cm->getItemsCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// SubTotal
$t->is($cm->getSubTotal(),50,'SubTotal:    '.format_currency($cm->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Pre total
$t->is($cm->getPreTotalCharge(),50,'PreTotal:    '.format_currency($cm->getPreTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');
// Total
$t->is($cm->getTotalCharge(),50,'Total:       '.format_currency($cm->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');

// ==============================================================================================================================

unset($cm);
rtShopComplexOrder5TestTools::cleanOrder();

// Create new cart manager instance
try {
  $cm = new rtShopCartManager();
} catch (Exception $e) {
  throw new sfException('Cart manager instance could not be created! Please check.');
}

// Voucher manager
$vm = $cm->getVoucherManager();

// Add gift voucher to session
$welcome_voucher = $tools->createGiftVoucherDataArray(50);
$vm->setSessionVoucherArray($welcome_voucher);
$gift_voucher = $vm->persistSessionVoucher();

// Add stocks to cart manager
$cm->addToCart($stock1, 2);

// Add addresses to order
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Loop through stocks in order
$t->diag('');
$t->diag('');
$t->diag('-----------------------------------------------------------------------------------------------------------------------------');
$t->diag('Case 2');
$t->comment('*****************************************************************************');
$tools->displayOrderItems($t, $cm->getStockInfoArray(), array(40,50), $gift_voucher);
$t->comment('*****************************************************************************');
// ItemsCharge
$t->is($cm->getItemsCharge(),130,'ItemsCharge: '.format_currency($cm->getItemsCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// SubTotal
$t->is($cm->getSubTotal(),130,'SubTotal:    '.format_currency($cm->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Pre total
$t->is($cm->getPreTotalCharge(),130.00,'PreTotal:   '.format_currency($cm->getPreTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');
// Total
$t->is($cm->getTotalCharge(),130.00,'Total:      '.format_currency($cm->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');

// ==============================================================================================================================

unset($cm);
rtShopComplexOrder5TestTools::cleanOrder();

// Create new cart manager instance
try {
  $cm = new rtShopCartManager();
} catch (Exception $e) {
  throw new sfException('Cart manager instance could not be created! Please check.');
}

// Add voucher
$voucher1 = $tools->createVoucher('Voucher $130', 130, 'dollarOff');

// Voucher manager
$vm = $cm->getVoucherManager();

// Add gift voucher to session
$welcome_voucher = $tools->createGiftVoucherDataArray(50);
$vm->setSessionVoucherArray($welcome_voucher);
$gift_voucher = $vm->persistSessionVoucher();

// Add stocks to cart manager
$cm->addToCart($stock1, 2);

// Add addresses to order
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Loop through stocks in order
$t->diag('');
$t->diag('');
$t->diag('-----------------------------------------------------------------------------------------------------------------------------');
$t->diag('Case 3');
$t->comment('*****************************************************************************');
$tools->displayOrderItems($t, $cm->getStockInfoArray(), array(40,50), $gift_voucher);
$t->comment('*****************************************************************************');
// ItemsCharge
$t->is($cm->getItemsCharge(),130,'ItemsCharge: '.format_currency($cm->getItemsCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// SubTotal
$t->is($cm->getSubTotal(),130,'SubTotal:    '.format_currency($cm->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Pre total
$t->is($cm->getPreTotalCharge(),130.00,'PreTotal:    '.format_currency($cm->getPreTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Voucher
$cm->setVoucherCode($voucher1->getCode());
$t->is($cm->getVoucherReduction(),130,'Voucher:    -'.format_currency($cm->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')).' (#'.$cm->getVoucherCode().')');
$t->comment('=============================================================================');
// Total
$t->is($cm->getTotalCharge(),0,'Total:        '.format_currency($cm->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');

// ==============================================================================================================================

unset($cm);
rtShopComplexOrder5TestTools::cleanOrder();

// Set no taxes and shipping rates
sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'exclusive');
sfConfig::set('app_rt_shop_shipping_charges', array('default' => 20, 'AU' => 10));

// Create new cart manager instance
try {
  $cm = new rtShopCartManager();
} catch (Exception $e) {
  throw new sfException('Cart manager instance could not be created! Please check.');
}

// Add voucher
$voucher1 = $tools->createVoucher('Voucher $130', 130, 'dollarOff');

// Voucher manager
$vm = $cm->getVoucherManager();

// Add gift voucher to session
$welcome_voucher = $tools->createGiftVoucherDataArray(50);
$vm->setSessionVoucherArray($welcome_voucher);
$gift_voucher = $vm->persistSessionVoucher();

// Add stocks to cart manager
$cm->addToCart($stock1, 2);

// Add addresses to order
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Loop through stocks in order
$t->diag('');
$t->diag('');
$t->diag('-----------------------------------------------------------------------------------------------------------------------------');
$t->diag('Case 4');
$t->comment('*****************************************************************************');
$tools->displayOrderItems($t, $cm->getStockInfoArray(), array(40,50), $gift_voucher);
$t->comment('*****************************************************************************');
// ItemsCharge
$t->is($cm->getItemsCharge(),130,'ItemsCharge: '.format_currency($cm->getItemsCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// SubTotal
$t->is($cm->getSubTotal(),130,'SubTotal:    '.format_currency($cm->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Tax
$t->is($cm->getTaxCharge(),8.00,'Tax:           '.format_currency($cm->getTaxCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// Shipping
$t->is($cm->getShippingCharge(),10,'Shipping:     '.format_currency($cm->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Pre total
$t->is($cm->getPreTotalCharge(),148.00,'PreTotal:    '.format_currency($cm->getPreTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Voucher
$cm->setVoucherCode($voucher1->getCode());
$t->is($cm->getVoucherReduction(),130,'Voucher:    -'.format_currency($cm->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')).' (#'.$cm->getVoucherCode().')');
$t->comment('=============================================================================');
// Total
$t->is($cm->getTotalCharge(),18.00,'Total:        '.format_currency($cm->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');

// ==============================================================================================================================

unset($cm);
rtShopComplexOrder5TestTools::cleanOrder();

// Create new cart manager instance
try {
  $cm = new rtShopCartManager();
} catch (Exception $e) {
  throw new sfException('Cart manager instance could not be created! Please check.');
}

// Add cart promotion
$cartpromo1 = $tools->createCartPromotion('Cart Promotion 10%', 10);

// Add voucher
$voucher1 = $tools->createVoucher('Voucher $130', 130, 'dollarOff');

// Voucher manager
$vm = $cm->getVoucherManager();

// Add gift voucher to session
$welcome_voucher = $tools->createGiftVoucherDataArray(50);
$vm->setSessionVoucherArray($welcome_voucher);
$gift_voucher = $vm->persistSessionVoucher();

// Add stocks to cart manager
$cm->addToCart($stock1, 2);

// Add addresses to order
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Loop through stocks in order
$t->diag('');
$t->diag('');
$t->diag('-----------------------------------------------------------------------------------------------------------------------------');
$t->diag('Case 5');
$t->comment('*****************************************************************************');
$tools->displayOrderItems($t, $cm->getStockInfoArray(), array(40,50), $gift_voucher);
$t->comment('*****************************************************************************');
// ItemsCharge
$t->is($cm->getItemsCharge(),130,'ItemsCharge: '.format_currency($cm->getItemsCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// SubTotal
$t->is($cm->getSubTotal(),130,'SubTotal:    '.format_currency($cm->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Tax
$t->is($cm->getTaxCharge(),8.00,'Tax:           '.format_currency($cm->getTaxCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// Promotion
$t->is($cm->getPromotionReduction(),8.00,'Promotion:    -'.format_currency($cm->getPromotionReduction(), sfConfig::get('app_rt_currency', 'AUD')));
// Shipping
$t->is($cm->getShippingCharge(),10,'Shipping:     '.format_currency($cm->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Pre total
$t->is($cm->getPreTotalCharge(),140.00,'PreTotal:    '.format_currency($cm->getPreTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Voucher
$cm->setVoucherCode($voucher1->getCode());
$t->is($cm->getVoucherReduction(),130,'Voucher:    -'.format_currency($cm->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')).' (#'.$cm->getVoucherCode().')');
$t->comment('=============================================================================');
// Total
$t->is($cm->getTotalCharge(),10.00,'Total:        '.format_currency($cm->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');

// ==============================================================================================================================

unset($cm);
rtShopComplexOrder5TestTools::cleanOrder();

// Create new cart manager instance
try {
  $cm = new rtShopCartManager();
} catch (Exception $e) {
  throw new sfException('Cart manager instance could not be created! Please check.');
}

// Add product promotions
$productpromo1 = $tools->createProductPromotion($stock3->getRtShopProduct()->getId(),'Promotion 10% - '.$stock3->getRtShopProduct()->getTitle(), 10, 'percentageOff', true);

// Add cart promotion
$cartpromo1 = $tools->createCartPromotion('Cart Promotion 20%', 20);

// Add voucher
$voucher1 = $tools->createVoucher('Voucher $130', 130, 'dollarOff');

// Voucher manager
$vm = $cm->getVoucherManager();

// Add gift voucher to session
$welcome_voucher = $tools->createGiftVoucherDataArray(50);
$vm->setSessionVoucherArray($welcome_voucher);
$gift_voucher = $vm->persistSessionVoucher();

// Add stocks to cart manager
$cm->addToCart($stock1, 2);
$cm->addToCart($stock2, 2);
$cm->addToCart($stock3, 2);

// Add addresses to order
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Loop through stocks in order
$t->diag('');
$t->diag('');
$t->diag('-----------------------------------------------------------------------------------------------------------------------------');
$t->diag('Case 6');
$t->comment('*****************************************************************************');
$tools->displayOrderItems($t, $cm->getStockInfoArray(), array(40,30,36,50), $gift_voucher);
$t->comment('*****************************************************************************');
// ItemsCharge
$t->is($cm->getItemsCharge(),262,'ItemsCharge: '.format_currency($cm->getItemsCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// SubTotal
$t->is($cm->getSubTotal(),262,'SubTotal:    '.format_currency($cm->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Tax
$t->is($cm->getTaxCharge(),21.20,'Tax:          '.format_currency($cm->getTaxCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// Promotion
$t->is($cm->getPromotionReduction(),42.40,'Promotion:   -'.format_currency($cm->getPromotionReduction(), sfConfig::get('app_rt_currency', 'AUD')));
// Shipping
$t->is($cm->getShippingCharge(),10,'Shipping:     '.format_currency($cm->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Pre total
$t->is($cm->getPreTotalCharge(),250.80,'PreTotal:    '.format_currency($cm->getPreTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Voucher
$cm->setVoucherCode($voucher1->getCode());
$t->is($cm->getVoucherReduction(),130,'Voucher:    -'.format_currency($cm->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')).' (#'.$cm->getVoucherCode().')');
$t->comment('=============================================================================');
// Total
$t->is($cm->getTotalCharge(),120.80,'Total:       '.format_currency($cm->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');

// ==============================================================================================================================

/**
 * rtShopComplexOrder5TestTools Class
 */
class rtShopComplexOrder5TestTools
{
  /**
   * Display order items in cart
   * 
   * @param lime_test     $t
   * @param array         $stock_info
   * @param array         $compare_charge
   * @param rtShopVoucher $gift_voucher
   */
  public static function displayOrderItems($t, $stock_info, $compare_charge, $gift_voucher = null)
  {
    $i=0;
    foreach($stock_info as $stock)
    {
      $prod_promo = Doctrine::getTable('rtShopPromotionProduct')->find($stock['rtShopPromotionProduct']['id']);
      $rt_shop_stock = Doctrine::getTable('rtShopStock')->find($stock['id']);

      $charge = ($stock['price_promotion'] > 0) ? $stock['price_promotion'] : $stock['price_retail'];

      $message = $stock['rtShopProduct']['title'];
      $message .= " || PR: ".format_currency($stock['price_retail'], sfConfig::get('app_rt_currency', 'AUD'));
      if($stock['sku'] !== 'VOUCHER')
      {
        $message .= " || PP: ".format_currency($rt_shop_stock->getPricePromotion(), sfConfig::get('app_rt_currency', 'AUD'));
      }
      $message .= " || QTY: ".$stock['rtShopOrderToStock'][0]['quantity'];
      $message .= " || Charge: ".format_currency($charge, sfConfig::get('app_rt_currency', 'AUD'));

      // Show promotion details where applicable
      if($prod_promo)
      {
        $message .= " || PromoId: ".$prod_promo->getId();
        if($prod_promo->getReductionType() == 'percentageOff')
        {
          $message .= " || Reduction: ".$prod_promo->getReductionValue().'%';
        }
        else
        {
          $message .= " || Reduction: $".$prod_promo->getReductionValue();
        }
        $message .= sprintf(" || Stackable: %s",($prod_promo->getStackable()) ? 'Yes':'No');
      }

      if($stock['sku'] == 'VOUCHER' && !is_null($gift_voucher))
      {
        $message .= sprintf(" || Stackable: %s",($gift_voucher->getStackable()) ? 'Yes':'No');
      }

      $message .= sprintf(" || Taxable: %s",($stock['rtShopProduct']['is_taxable']) ? 'Yes':'No');

      $t->is($charge,$compare_charge[$i],$message);
      $i++;
    }
  }

  /**
   * Make sure table is cleaned before testing
   */
  public static function clean()
  {
    $user = sfContext::getInstance()->getUser();

    $doctrine = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
    $doctrine->query('TRUNCATE table rt_address');
    $doctrine->query('TRUNCATE table rt_shop_attribute');
    $doctrine->query('TRUNCATE table rt_shop_category');
    $doctrine->query('TRUNCATE table rt_shop_category_version');
    $doctrine->query('TRUNCATE table rt_shop_order');
    $doctrine->query('TRUNCATE table rt_shop_order_to_stock');
    $doctrine->query('TRUNCATE table rt_shop_product');
    $doctrine->query('TRUNCATE table rt_shop_product_version');
    $doctrine->query('TRUNCATE table rt_shop_product_to_attribute');
    $doctrine->query('TRUNCATE table rt_shop_product_to_category');
    $doctrine->query('TRUNCATE table rt_shop_product_to_product');
    $doctrine->query('TRUNCATE table rt_shop_product_to_promotion');
    $doctrine->query('TRUNCATE table rt_shop_promotion');
    $doctrine->query('TRUNCATE table rt_shop_stock');
    $doctrine->query('TRUNCATE table rt_shop_stock_to_variation');
    $doctrine->query('TRUNCATE table rt_shop_variation');
    unset($doctrine);

    // Session variables
    $user->getAttributeHolder()->remove('rt_shop_frontend_gift_voucher');
  }

  /**
   * Make sure table is cleaned before testing
   */
  public static function cleanOrder()
  {
    $doctrine = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
    $doctrine->query('TRUNCATE table rt_shop_order');
    $doctrine->query('TRUNCATE table rt_shop_order_to_stock');
    $doctrine->query('TRUNCATE table rt_shop_promotion');
    unset($doctrine);
  }

  /**
   * Add address for order
   *
   * @param integer $order_id
   * @param string $type
   * @return integer
   */
  public function addAddressForOrder($order_id,$type = 'billing')
  {
    $address = new rtAddress();
    $address->setModelId($order_id);
    $address->setModel('rtShopOrder');
    $address->setFirstName('John');
    $address->setLastName('Doe');
    $address->setAddress_1('35 Doe Street');
    $address->setTown('Doetown');
    $address->setState('NSW');
    $address->setCountry('AU');
    $address->setPostcode(2010);
    $address->setPhone('0212345678');
    $address->setType($type);
    $address->save();

    return $address->getId();
  }

  /**
   * Create product promotion and connect to product
   *
   * @param integer $product_id
   * @param string $title
   * @param number $value
   * @param datetime $date_from
   * @param datetime $date_to
   * @param string $type
   * @param integer $count
   * @param boolean $stackable
   * @return integer
   */
  public function createProductPromotion($product_id,
                                         $title = 'Test Promotion',
                                         $value = 10,
                                         $type = 'percentageOff',
                                         $stackable = true,
                                         $date_from = NULL,
                                         $date_to = NULL,
                                         $count = 1)
  {
    // Create promotion
    $promotion = new rtShopPromotionProduct();
    $promotion->setTitle($title);
    $promotion->setDateFrom($date_from);
    $promotion->setDateTo($date_to);
    $promotion->setReductionType($type);
    $promotion->setReductionValue($value);
    $promotion->setCount($count);
    $promotion->setStackable($stackable);
    $promotion->save();

    // Connect promotion to product
    $promo_product = new rtShopProductToPromotion();
    $promo_product->setProductId($product_id);
    $promo_product->setPromotionId($promotion->getId());
    $promo_product->save();

    return $promotion->getId();
  }

  /**
   * Create cart promotion and connect to product
   *
   * @param string $title
   * @param number $value
   * @param float $total_from
   * @param float $total_to
   * @param datetime $date_from
   * @param datetime $date_to
   * @param string $type
   * @param integer $count
   * @param boolean $stackable
   * @return integer
   */
  public function createCartPromotion($title,
                                      $value = 10,
                                      $total_from = NULL,
                                      $total_to = NULL,
                                      $date_from = NULL,
                                      $date_to = NULL,
                                      $type = 'percentageOff',
                                      $count = 1,
                                      $stackable = true)
  {
    $promotion = new rtShopPromotionCart();
    $promotion->setTitle($title);
    $promotion->setDateFrom($date_from);
    $promotion->setDateTo($date_to);
    $promotion->setTotalFrom($total_from);
    $promotion->setTotalTo($total_to);
    $promotion->setReductionType($type);
    $promotion->setReductionValue($value);
    $promotion->setCount($count);
    $promotion->setStackable($stackable);
    $promotion->save();

    return $promotion->getId();
  }

  /**
   * Create voucher
   *
   * @param string $title
   * @param number $value
   * @param string $type
   * @param float $total_from
   * @param float $total_to
   * @param datetime $date_from
   * @param datetime $date_to
   * @param string $mode
   * @param integer $count
   * @param boolean $stackable
   * @return integer
   */
  public function createVoucher($title,
                                $value = 10,
                                $type = 'percentageOff',
                                $total_from = NULL,
                                $total_to = NULL,
                                $date_from = NULL,
                                $date_to = NULL,
                                $mode = 'Single',
                                $count = 1,
                                $stackable = true)
  {
    $voucher = new rtShopVoucher();
    $voucher->setTitle($title);
    $voucher->setDateFrom($date_from);
    $voucher->setDateTo($date_to);
    $voucher->setTotalFrom($total_from);
    $voucher->setTotalTo($total_to);
    $voucher->setReductionType($type);
    $voucher->setReductionValue($value);
    $voucher->setMode($mode);
    $voucher->setCount($count);
    $voucher->setStackable($stackable);
    $voucher->save();

    return $voucher;
  }

  public function createGiftVoucherDataArray($voucher_value)
  {
    $gift_voucher = array();
    
    $gift_voucher['reduction_value'] = $voucher_value;
    $gift_voucher['first_name']      = 'John';
    $gift_voucher['last_name']       = 'Doe';
    $gift_voucher['email_address']   = 'konny@wranglers.com.au';
    $gift_voucher['message']         = 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante.';
    $gift_voucher['title']           = 'Gift Voucher';
    
    return $gift_voucher;
  }
}