<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2011 digital Wranglers <rtShop@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * To use it copy to test/unit:
 *
 * $ cp plugins/rtShopPlugin/test/unit/rtShopVoucherRangeTest.php test/unit/rtShopVoucherRangeTest.php
 *
 */

/**
 * advanced rtShopVoucher Testing
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

include(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(16, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
$configuration->loadHelpers('Number');
sfContext::createInstance($configuration);

new sfDatabaseManager($configuration);

$t->comment('/////////////////////////////////////////////////////////////////////////////');
$t->comment('/// rtShopVoucher: Test range');
$t->comment('/////////////////////////////////////////////////////////////////////////////');

$numberFormat = new sfNumberFormat(sfContext::getInstance()->getUser()->getCulture());

// Tools
$tools = new rtShopVoucherRangeTestTools;

// Tax and shipping configurations
sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'exclusive');
sfConfig::set('app_rt_shop_shipping_charges', array('default' => 20, 'AU' => 10, 'NZ' => 10));

// Add some data to play with...
$tools->clean();

// Products
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

// Categories
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

// Product to category
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

// Attributes
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

// Product to attribute
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

// Variations
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

// Stocks
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
} catch (Exception $e) {
  throw new sfException('Stocks could not be added! Please check.');
}

// Stock to variation
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

// Create cart manager instance
try {
  $cm = new rtShopCartManager();
} catch (Exception $e) {
  throw new sfException('Cart manager instance could not be created! Please check.');
}

// Add stocks to cart manager
$cm->addToCart($stock1, 2);
$cm->addToCart($stock2, 2);

// Create voucher
$voucher1 = $tools->createVoucher('Voucher $100.00 // Valid if total >= $150', 100, 'dollarOff', 150);
$voucher2 = $tools->createVoucher('Voucher $100.00 // Valid if total >= $100', 100, 'dollarOff', 100);
$voucher3 = $tools->createVoucher('Voucher 10.00% // Valid if total >= $150', 10, 'percentageOff', 150);
$voucher4 = $tools->createVoucher('Voucher 10.00% // Valid if total >= $100', 10, 'percentageOff', 100);

$t->comment('');
$t->diag('***************************************');
$t->diag('*** Voucher #1 details');
$t->diag('***************************************');
$t->diag('Code:            '.$voucher1->getCode());
$t->diag(sprintf('Stackable:       %s',$voucher1->getStackable() ? 'Yes' : 'No'));
$t->diag('Reduction type:  '.$voucher1->getReductionType());
$t->diag('Reduction value: '.$voucher1->getReductionValue());
$t->diag('Count:           '.$voucher1->getCount());
$t->diag('Total_from:      '.$voucher1->getTotalFrom());
$t->diag('***************************************');
$t->diag('*** Voucher #2 details');
$t->diag('***************************************');
$t->diag('Code:            '.$voucher2->getCode());
$t->diag(sprintf('Stackable:       %s',$voucher2->getStackable() ? 'Yes' : 'No'));
$t->diag('Reduction type:  '.$voucher2->getReductionType());
$t->diag('Reduction value: '.$voucher2->getReductionValue());
$t->diag('Count:           '.$voucher2->getCount());
$t->diag('Total_from:      '.$voucher2->getTotalFrom());
$t->diag('***************************************');
$t->diag('*** Voucher #3 details');
$t->diag('***************************************');
$t->diag('Code:            '.$voucher3->getCode());
$t->diag(sprintf('Stackable:       %s',$voucher3->getStackable() ? 'Yes' : 'No'));
$t->diag('Reduction type:  '.$voucher3->getReductionType());
$t->diag('Reduction value: '.$voucher3->getReductionValue());
$t->diag('Count:           '.$voucher3->getCount());
$t->diag('Total_from:      '.$voucher3->getTotalFrom());
$t->diag('***************************************');
$t->diag('*** Voucher #4 details');
$t->diag('***************************************');
$t->diag('Code:            '.$voucher4->getCode());
$t->diag(sprintf('Stackable:       %s',$voucher4->getStackable() ? 'Yes' : 'No'));
$t->diag('Reduction type:  '.$voucher4->getReductionType());
$t->diag('Reduction value: '.$voucher4->getReductionValue());
$t->diag('Count:           '.$voucher4->getCount());
$t->diag('Total_from:      '.$voucher4->getTotalFrom());
$t->diag('***************************************');

$t->comment('');
$t->diag('***************************************');
$t->diag('*** Order cart');
$t->diag('***************************************');

// Add addresses to order
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Loop through stocks in order
$t->comment('');
$t->comment('*****************************************************************************');
$stock_info = $cm->getOrder()->getStockInfoArray();
$i=0;
$compare_charge = array($stock1->getPriceRetail(),$stock2->getPricePromotion());
foreach($stock_info as $stock)
{
  $rt_shop_stock = Doctrine::getTable('rtShopStock')->find($stock['id']);
  
  $charge = ($stock['price_promotion'] > 0) ? $stock['price_promotion'] : $stock['price_retail'];
  
  $message = $stock['rtShopProduct']['title'].
             " || PR: ".format_currency($stock['price_retail'], sfConfig::get('app_rt_currency', 'USD')).
             " || PP: ".format_currency($rt_shop_stock->getPricePromotion(), sfConfig::get('app_rt_currency', 'USD')).
             " || QTY: ".$stock['rtShopOrderToStock'][0]['quantity'].
             " || Charge: ".format_currency($charge, sfConfig::get('app_rt_currency', 'USD'));

  $t->is($charge,$compare_charge[$i],$message);
  $i++;
}
$t->comment('*****************************************************************************');
// ItemsCharge
$t->is($cm->getItemsCharge(),140,'ItemsCharge: '.format_currency($cm->getItemsCharge(), sfConfig::get('app_rt_currency', 'USD')));
// SubTotal
$t->is($cm->getSubTotal(),140,'SubTotal:    '.format_currency($cm->getSubTotal(), sfConfig::get('app_rt_currency', 'USD')));
$t->comment('-----------------------------------------------------------------------------');
// Tax
$t->is($cm->getTaxCharge(),14.00,'Tax:          '.format_currency($cm->getTaxCharge(), sfConfig::get('app_rt_currency', 'USD')));
// Shipping
$t->is($cm->getShippingCharge(),10,'Shipping:     '.format_currency($cm->getShippingCharge(), sfConfig::get('app_rt_currency', 'USD')));
$t->comment('-----------------------------------------------------------------------------');
// Pre total
$t->is($cm->getPreTotalCharge(),164.00,'PreTotal:    '.format_currency($cm->getPreTotalCharge(), sfConfig::get('app_rt_currency', 'USD')));
$t->comment('=============================================================================');
// Total
$t->is($cm->getTotalCharge(),164.00,'Total:       '.format_currency($cm->getTotalCharge(), sfConfig::get('app_rt_currency', 'USD')));
$t->comment('=============================================================================');

$t->comment('');
$t->diag('***************************************');
$t->diag('*** Voucher check');
$t->diag('***************************************');
$t->comment('');

$t->comment('--- BasertShopOrderActions::executeCheckVoucher() // dollarOff ----------------------------------------');

$t->diag('*** Case #1.0: Voucher #1: Non-applicable voucher');

$check_voucher_array1 = $cm->getCheckVoucherArray($voucher1->getCode());
$t->is($check_voucher_array1['error'], true, '->checkVoucher() has no applicable rtShopVoucher.');

$t->diag('*** Case #1.1: Voucher #2: Applicable voucher');

$check_voucher_array2 = $cm->getCheckVoucherArray($voucher2->getCode());
$t->is($check_voucher_array2['error'], false, '->checkVoucher() has applicable rtShopVoucher where code #'.$voucher2->getCode());

$t->comment('--- BasertShopOrderActions::executeCheckVoucher() // percentageOff ------------------------------------');

$t->diag('*** Case #1.2: Voucher #3: Non-applicable voucher');

$check_voucher_array3 = $cm->getCheckVoucherArray($voucher3->getCode());
$t->is($check_voucher_array3['error'], true, '->checkVoucher() has no applicable rtShopVoucher.');

$t->diag('*** Case #1.3: Voucher #4: Applicable voucher');

$check_voucher_array4 = $cm->getCheckVoucherArray($voucher4->getCode());
$t->is($check_voucher_array4['error'], false, '->checkVoucher() has applicable rtShopVoucher where code #'.$voucher4->getCode());

$t->comment('--- rtShopCartManager::getVoucherReduction() // dollarOff ---------------------------------------------');

$t->diag('*** Case #2.0: Voucher #1: Non-applicable voucher');

$cm->setVoucherCode($voucher1->getCode());
$t->is($cm->getVoucherReduction(), 0, '->getVoucherReduction() is returning correct voucher reduction value of '.$numberFormat->format($cm->getVoucherReduction(), 'c', sfConfig::get('app_rt_shop_payment_currency','USD')).')');

$t->diag('*** Case #2.1: Voucher #2: Applicable voucher');

$cm->setVoucherCode($voucher2->getCode());
$t->is($cm->getVoucherReduction(), 100, '->getVoucherReduction() is returning correct voucher reduction value of '.$numberFormat->format($cm->getVoucherReduction(), 'c', sfConfig::get('app_rt_shop_payment_currency','USD')).')');

$t->comment('--- rtShopCartManager::getVoucherReduction() // percentageOff -----------------------------------------');

$t->diag('*** Case #2.2: Voucher #3: Non-applicable voucher');

$cm->setVoucherCode($voucher3->getCode());
$t->is($cm->getVoucherReduction(), 0, '->getVoucherReduction() is returning correct voucher reduction value of '.$numberFormat->format($cm->getVoucherReduction(), 'c', sfConfig::get('app_rt_shop_payment_currency','USD')).')');

$t->diag('*** Case #2.3: Voucher #4: Applicable voucher');

$cm->setVoucherCode($voucher4->getCode());
$t->is($cm->getVoucherReduction(), 14, '->getVoucherReduction() is returning correct voucher reduction value of '.$numberFormat->format($cm->getVoucherReduction(), 'c', sfConfig::get('app_rt_shop_payment_currency','USD')).')');

/**
 * rtShopVoucherRangeTestTools Class
 */
class rtShopVoucherRangeTestTools
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
}