<?php

/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <rtShop@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * To use it copy to test/unit:
 *
 * $ cp plugins/rtShopPlugin/test/unit/rtShopComplexOrder3Test.php test/unit/rtShopComplexOrder3Test.php
 *
 */

/**
 * rtShopComplexOrder3 Testing - Order testing
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
include(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(18, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
$configuration->loadHelpers('Number');
sfContext::createInstance($configuration);

new sfDatabaseManager($configuration);

$t->comment('/////////////////////////////////////////////////////////////////////////////');
$t->comment('/// Complex Order #3                                                      ///');
$t->comment('/////////////////////////////////////////////////////////////////////////////');

// Acronyms:
$t->comment('');
$t->comment('Acronyms:');
$t->comment('QTY = Quantity');
$t->comment('PR  = Price Retail');
$t->comment('PP  = Price Promotion');
$t->comment('TI  = Tax inclusive');
$t->comment('TE  = Tax exclusive');
$t->comment('PS  = Promotion, stackable');
$t->comment('PNS = Promotion, non-stackable');
$t->comment('VS  = Voucher, stackable');
$t->comment('VNS = Voucher, non-stackable');
$t->comment('PromoId = Promotion ID');
$t->comment('RedType = Promotion, reduction type');
$t->comment('RedValue = Promotion, reduction value');

// Add data
rtShopComplexOrder3TestTools::clean();

try {
  $prod1 = new rtShopProduct();
  $prod1->setTitle('Product A');
  $prod1->save();
  $prod2 = new rtShopProduct();
  $prod2->setTitle('Product B');
  $prod2->save();
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
  $stock1->setPriceRetail(50);
  $stock1->save();
  $stock2 = new rtShopStock();
  $stock2->setProductId($prod2->getId());
  $stock2->setQuantity(5);
  $stock2->setSku(mt_rand(1,100000));
  $stock2->setPriceRetail(50);
  $stock2->setPricePromotion(30);
  $stock2->save();
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
} catch (Exception $e) {
  throw new sfException('Stocks could not be added to variations! Please check.');
}

// Set taxes and shipping rate
sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'inclusive');
sfConfig::set('app_rt_shop_shipping_charges', array('default' => 20, 'AU' => 10));

// Create tools instance
$tools = new rtShopComplexOrder3TestTools;

$t->diag('');
$t->diag('/////////////////////////////////////////////////////////////////////////////');
$t->diag('1. Voucher: $100, non-stackable');
$t->diag('/////////////////////////////////////////////////////////////////////////////');

// Add voucher
$voucher1 = $tools->createVoucher('Voucher $100', 100, 'dollarOff', false);

// Create cart manager instance
try {
  $cm = new rtShopCartManager();
} catch (Exception $e) {
  throw new sfException('Cart manager instance could not be created! Please check.');
}

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Add addresses to order
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Loop through stocks in order
$t->comment('');
$t->comment('*****************************************************************************');
$stock_info = $cm->getOrder()->getStockInfoArray();
$i=0;
$compare_charge = array(50,30);
foreach($stock_info as $stock)
{
  $rt_shop_stock = Doctrine::getTable('rtShopStock')->find($stock['id']);

  $charge = ($stock['price_promotion'] > 0) ? $stock['price_promotion'] : $stock['price_retail'];
  
  $message = $stock['rtShopProduct']['title'].
             " || PR: ".format_currency($stock['price_retail'], sfConfig::get('app_rt_currency', 'AUD')).
             " || PP: ".format_currency($rt_shop_stock->getPricePromotion(), sfConfig::get('app_rt_currency', 'AUD')).
             " || QTY: ".$stock['rtShopOrderToStock'][0]['quantity'].
             " || Total: ".format_currency($charge, sfConfig::get('app_rt_currency', 'AUD'));

  $t->is($charge,$compare_charge[$i],$message);
  $i++;
}
$t->comment('*****************************************************************************');
// ItemsCharge
$t->is($cm->getItemsCharge(),80,'ItemsCharge: '.format_currency($cm->getItemsCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// SubTotal
$t->is($cm->getSubTotal(),80,'SubTotal:    '.format_currency($cm->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Shipping
$t->is($cm->getShippingCharge(),10,'Shipping:    '.format_currency($cm->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Pre total
$t->is($cm->getPreTotalCharge(),90,'PreTotal:    '.format_currency($cm->getPreTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Voucher
$cm->setVoucherCode($voucher1->getCode());
$t->is($cm->getVoucherReduction(),100,'Voucher:    -'.format_currency($cm->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')).' (#'.$cm->getVoucherCode().')');
$t->comment('=============================================================================');
// Total
$t->is($cm->getTotalCharge(),0,'Total (includes $'.format_currency($cm->getTaxComponent(), sfConfig::get('app_rt_currency', 'AUD')).' tax): '.format_currency($cm->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');

$cm->adjustVoucherDetails($cm->getPreTotalCharge());
$t->is($cm->getVoucher()->getReductionValue(),10,'>>> Voucher leftover reduction value: '.format_currency($cm->getVoucher()->getReductionValue(), sfConfig::get('app_rt_currency', 'AUD')).' <<<');

$t->diag('');
$t->diag('/////////////////////////////////////////////////////////////////////////////');
$t->diag('2. Voucher: 10%, non-stackable');
$t->diag('/////////////////////////////////////////////////////////////////////////////');

// Clean order and reset cart manager
unset($cm);
rtShopComplexOrder3TestTools::cleanOrder();

// Add voucher
$voucher1 = $tools->createVoucher('Voucher 10%', 10, 'percentageOff', false);

// Create cart manager instance
try {
  $cm = new rtShopCartManager();
} catch (Exception $e) {
  throw new sfException('Cart manager instance could not be created! Please check.');
}

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Add addresses to order
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Loop through stocks in order
$t->comment('');
$t->comment('*****************************************************************************');
$stock_info = $cm->getOrder()->getStockInfoArray();
$i=0;
$compare_charge = array(50,30);
foreach($stock_info as $stock)
{
  $rt_shop_stock = Doctrine::getTable('rtShopStock')->find($stock['id']);

  $charge = ($stock['price_promotion'] > 0) ? $stock['price_promotion'] : $stock['price_retail'];

  $message = $stock['rtShopProduct']['title'].
             " || PR: ".format_currency($stock['price_retail'], sfConfig::get('app_rt_currency', 'AUD')).
             " || PP: ".format_currency($rt_shop_stock->getPricePromotion(), sfConfig::get('app_rt_currency', 'AUD')).
             " || QTY: ".$stock['rtShopOrderToStock'][0]['quantity'].
             " || Total: ".format_currency($charge, sfConfig::get('app_rt_currency', 'AUD'));

  $t->is($charge,$compare_charge[$i],$message);
  $i++;
}
$t->comment('*****************************************************************************');
// ItemsCharge
$t->is($cm->getItemsCharge(),80,'ItemsCharge: '.format_currency($cm->getItemsCharge(), sfConfig::get('app_rt_currency', 'AUD')));
// SubTotal
$t->is($cm->getSubTotal(),80,'SubTotal:    '.format_currency($cm->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Shipping
$t->is($cm->getShippingCharge(),10,'Shipping:    '.format_currency($cm->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Pre total
$t->is($cm->getPreTotalCharge(),90,'PreTotal:    '.format_currency($cm->getPreTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('-----------------------------------------------------------------------------');
// Voucher
$cm->setVoucherCode($voucher1->getCode());
$t->is($cm->getVoucherReduction(),6,'Voucher:    -'.format_currency($cm->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')).' (#'.$cm->getVoucherCode().')');
$t->comment('=============================================================================');
// Total
$t->is($cm->getTotalCharge(),84,'Total (includes $'.format_currency($cm->getTaxComponent(), sfConfig::get('app_rt_currency', 'AUD')).' tax): '.format_currency($cm->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')));
$t->comment('=============================================================================');

$t->is($cm->getVoucher()->getReductionValue(),10,'>>> Voucher leftover reduction value: '.$cm->getVoucher()->getReductionValue().'% <<<');

/**
 * rtShopComplexOrder3TestTools Class
 */
class rtShopComplexOrder3TestTools
{
  /**
   * Make sure table is cleaned before testing
   */
  public static function clean()
  {
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
                                $stackable = true,
                                $total_from = NULL,
                                $total_to = NULL,
                                $date_from = NULL,
                                $date_to = NULL,
                                $mode = 'Single',
                                $count = 1)
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
}