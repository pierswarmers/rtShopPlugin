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
 * $ cp plugins/rtShopPlugin/test/unit/rtShopStockTest.php test/unit/rtShopStockTest.php
 *
 */

/**
 * rtShopStock Testing - Testing of product promotions
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
include(dirname(__FILE__).'/../bootstrap/unit.php');

$_debug_date_available = true;

$t = new lime_test(51, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
sfContext::createInstance($configuration);

new sfDatabaseManager($configuration);

$t->diag('/////////////////////////////////////////////////////////////////////////////');
$t->diag('/// rtShopStock Promotion Testing                                         ///');
$t->diag('/////////////////////////////////////////////////////////////////////////////');

// Make sure no taxes or shipping applied for testing
if(sfConfig::has('app_rt_shop_tax_rate'))
{
  sfConfig::set('app_rt_shop_tax_rate', 0);
}
if(sfConfig::has('app_rt_shop_shipping_charges'))
{
  sfConfig::set('app_rt_shop_shipping_charges', array());
}

$t->diag('-----------------------------------------------------------------------------');
$t->diag('0. Check toolkit class');
$t->diag('-----------------------------------------------------------------------------');

$o1 = new rtShopStockTestTools;
$t->isa_ok($o1, 'rtShopStockTestTools', '->class() rtShopStockTestTools object is available');

// Clean up
unset ($o1);

$t->diag('-----------------------------------------------------------------------------');
$t->diag('1. Empty save shouldn\'t fail');
$t->diag('-----------------------------------------------------------------------------');

try {
  $i1 = new rtShopStock();
  $i1->save();
  $t->pass('->save() rtShopStock allows empty saves');
  $i1->delete();
} catch (Exception $e) {
  $t->fail('->save() throws an Exception!');
}
// Clean up
unset ($i1);

// Fixtures path
$fixtures_path = sfConfig::get('sf_plugins_dir').'/rtShopPlugin/test/fixtures';

// Check that fixtures direcory abvailable
if(is_dir($fixtures_path))
{
  try {
    rtShopStockTestTools::clean();
  
    Doctrine_Core::loadData($fixtures_path);
    $t->pass('->loadData() of fixtures worked');
  } catch (Exception $e) {
    var_dump($e->getMessage());
    $t->fail('->loadData() of fixtures failed!');
  }

//  $t->diag('-----------------------------------------------------------------------------');
//  $t->diag('2. Test retrieval of stock, attribute, product, category and variation data');
//  $t->diag('-----------------------------------------------------------------------------');
//
//  // Retrieve by pk
//  $o1 = Doctrine::getTable('rtShopAttribute')->find(1);
//  $t->isa_ok($o1, 'rtShopAttribute', '->retrieve() rtShopAttribute was saved and retrieved successfully');
//
//  $o2 = Doctrine::getTable('rtShopProduct')->find(1);
//  $t->isa_ok($o2, 'rtShopProduct', '->retrieve() rtShopProduct was saved and retrieved successfully');
//
//  $o3 = Doctrine::getTable('rtShopVariation')->find(1);
//  $t->isa_ok($o3, 'rtShopVariation', '->retrieve() rtShopVariation was saved and retrieved successfully');
//
//  $o4 = Doctrine::getTable('rtShopCategory')->find(1);
//  $t->isa_ok($o4, 'rtShopCategory', '->retrieve() rtShopCategory was saved and retrieved successfully');
//
//  $o5 = Doctrine::getTable('rtShopStock')->find(1);
//  $t->isa_ok($o5, 'rtShopStock', '->retrieve() rtShopStock was saved and retrieved successfully');
//
//  $t->diag('-----------------------------------------------------------------------------');
//  $t->diag('3. Test retrieval of order and order to stock data');
//  $t->diag('-----------------------------------------------------------------------------');
//
//  // Order ID 1
//  $o11 = Doctrine::getTable('rtShopOrder')->find(1);
//  $t->isa_ok($o11, 'rtShopOrder', '->retrieve() rtShopOrder was saved and retrieved order 1 successfully');
//
//  $o12 = Doctrine::getTable('rtShopOrderToStock')->findByOrderId(1);
//  $t->is(count($o12), 2, '::findByOrderId() retrieved 2 objects for order 1 successfully');
//
//  // Order ID 2
//  $o13 = Doctrine::getTable('rtShopOrder')->find(2);
//  $t->isa_ok($o13, 'rtShopOrder', '->retrieve() rtShopOrder was saved and retrieved order 2 successfully');
//
//  $o14 = Doctrine::getTable('rtShopOrderToStock')->findByOrderId(2);
//  $t->is(count($o14), 2, '::findByOrderId() retrieved 2 objects for order 2 successfully');
//
//  // Order ID 3
//  $o15 = Doctrine::getTable('rtShopOrder')->find(3);
//  $t->isa_ok($o15, 'rtShopOrder', '->retrieve() rtShopOrder was saved and retrieved order 3 successfully');
//
//  $o16 = Doctrine::getTable('rtShopOrderToStock')->findByOrderId(3);
//  $t->is(count($o16), 2, '::findByOrderId() retrieved 2 objects for order 3 successfully');
//
//  unset ($o1,$o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9,$o10,$o11,$o12,$o13,$o14,$o15,$o16);
//
//  $t->diag('-----------------------------------------------------------------------------');
//  $t->diag('4. Check order data and totals');
//  $t->diag('-----------------------------------------------------------------------------');
//
//  $t->diag('-----------------------------------------------------------------------------');
//  $t->diag('4.1 Totals for order 1 (retail prices only) with 5% cart promotion');
//  $t->diag('-----------------------------------------------------------------------------');
//
//  $order_id = 1;
//
//  // Manually calculate sub_total for comparison
//  $calculated_sub_total = rtShopStockTestTools::getSubTotalOfOrderByStocks($order_id);
//
//  // Write order_id to user session
//  sfContext::getInstance()->getUser()->setAttribute('rt_shop_frontend_order_id', $order_id);
//
//  // Create cart manager object
//  $cm1 = new rtShopCartManager();
//
//  // Calculate reductions
//  $promotion_reduction = $cm1->getPromotionReduction();
//  $reduced_total = $calculated_sub_total - $promotion_reduction;
//
//  // Check totals
//  $t->is(get_class($cm1), rtShopCartManager, 'get_class() rtShopCartManager object is available');
//  $t->is($cm1->getSubTotal(), $calculated_sub_total, '::getSubTotal() sub total charge for order '.$order_id.' is '.$calculated_sub_total);
//  $t->is($cm1->getPreTotalCharge(), $reduced_total, '::getPreTotalCharge() pre total charge for order '.$order_id.' is '.$reduced_total);
//  $t->is($cm1->getTotalCharge(), $reduced_total, '::getTotalCharge() total charge for order '.$order_id.' is '.$reduced_total);
//
//  $t->diag('-----------------------------------------------------------------------------');
//  $t->diag('4.2 Totals for order 2 (retail prices only) with 10% cart promotion');
//  $t->diag('-----------------------------------------------------------------------------');
//
//  $order_id = 2;
//
//  // Manually calculate sub_total for comparison
//  $calculated_sub_total = rtShopStockTestTools::getSubTotalOfOrderByStocks($order_id);
//
//  // Write order_id to user session
//  sfContext::getInstance()->getUser()->setAttribute('rt_shop_frontend_order_id', $order_id);
//
//  // Create cart manager object
//  $cm2 = new rtShopCartManager();
//
//  // Calculate reductions
//  $promotion_reduction = $cm2->getPromotionReduction();
//  $reduced_total = $calculated_sub_total - $promotion_reduction;
//
//  // Check totals
//  $t->is(get_class($cm2), rtShopCartManager, 'get_class() rtShopCartManager object is available');
//  $t->is($cm2->getSubTotal(), $calculated_sub_total, '::getSubTotal() sub total charge for order '.$order_id.' is '.$calculated_sub_total);
//  $t->is($cm2->getPreTotalCharge(), $reduced_total, '::getPreTotalCharge() pre total charge for order '.$order_id.' is '.$reduced_total);
//  $t->is($cm2->getTotalCharge(), $reduced_total, '::getTotalCharge() total charge for order '.$order_id.' is '.$reduced_total);
//
//  $t->diag('-----------------------------------------------------------------------------');
//  $t->diag('4.3 Totals for order 3 (retail prices only) with 20% cart promotion');
//  $t->diag('-----------------------------------------------------------------------------');
//
//  $order_id = 3;
//
//  // Manually calculate sub_total for comparison
//  $calculated_sub_total = rtShopStockTestTools::getSubTotalOfOrderByStocks($order_id);
//
//  // Write order_id to user session
//  sfContext::getInstance()->getUser()->setAttribute('rt_shop_frontend_order_id', $order_id);
//
//  // Create cart manager object
//  $cm3 = new rtShopCartManager();
//
//  // Calculate reductions
//  $promotion_reduction = $cm3->getPromotionReduction();
//  $reduced_total = $calculated_sub_total - $promotion_reduction;
//
//  // Check totals
//  $t->is(get_class($cm3), rtShopCartManager, 'get_class() rtShopCartManager object is available');
//  $t->is($cm3->getSubTotal(), $calculated_sub_total, '::getSubTotal() sub total charge for order '.$order_id.' is '.$calculated_sub_total);
//  $t->is($cm3->getPreTotalCharge(), $reduced_total, '::getPreTotalCharge() pre total charge for order '.$order_id.' is '.$reduced_total);
//  $t->is($cm3->getTotalCharge(), $reduced_total, '::getTotalCharge() total charge for order '.$order_id.' is '.$reduced_total);
//
//  unset ($cm1,$cm2,$cm3);

  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('2. Add product promotion data');
  $t->diag('-----------------------------------------------------------------------------');

  // Add promotions data
  $data = new rtShopStockTestTools;

  // No date restriction - valid
  $product_id = 1;
  $promo1 = $data->createProductPromotion($product_id,'%10 off product');
  $p1 = Doctrine::getTable('rtShopPromotionProduct')->find($promo1);
  $t->isa_ok($p1, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo1);

  // Valid
  $product_id = 2;
  $date_from = date('Y-m-d H:i:s',strtotime(sprintf("-%s months",2)));
  $promo2 = $data->createProductPromotion($product_id,'%10 off product',$date_from);
  $p2 = Doctrine::getTable('rtShopPromotionProduct')->find($promo2);
  $t->isa_ok($p2, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo2);

  // Valid
  $product_id = 3;
  $date_to = date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2)));
  $promo3 = $data->createProductPromotion($product_id,'%10 off product',NULL,$date_to);
  $p3 = Doctrine::getTable('rtShopPromotionProduct')->find($promo3);
  $t->isa_ok($p3, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo3);

  // Valid
  $product_id = 4;
  $date_from = date('Y-m-d H:i:s',strtotime(sprintf("-%s months",2)));
  $date_to = date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2)));
  $promo4 = $data->createProductPromotion($product_id,'%10 off product',$date_from,$date_to);
  $p4 = Doctrine::getTable('rtShopPromotionProduct')->find($promo4);
  $t->isa_ok($p4, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo4);

  // Expired
  $product_id = 5;
  $date_from = date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3)));
  $date_to = date('Y-m-d H:i:s',strtotime(sprintf("-%s months",2)));
  $promo5 = $data->createProductPromotion($product_id,'%10 off product',$date_from,$date_to);
  $p5 = Doctrine::getTable('rtShopPromotionProduct')->find($promo5);
  $t->isa_ok($p5, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo5);

  // Valid
  $product_id = 6;
  $date_from = date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2)));
  $promo6 = $data->createProductPromotion($product_id,'%10 off product',$date_from);
  $p6 = Doctrine::getTable('rtShopPromotionProduct')->find($promo6);
  $t->isa_ok($p6, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo6);

  // Expired
  $product_id = 7;
  $date_to = date('Y-m-d H:i:s',strtotime(sprintf("-%s months",2)));
  $promo7 = $data->createProductPromotion($product_id,'%10 off product',NULL,$date_to);
  $p7 = Doctrine::getTable('rtShopPromotionProduct')->find($promo7);
  $t->isa_ok($p7, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo7);

  // No date restriction
  $product_id = 1;
  $promo8 = $data->createProductPromotion($product_id,'%20 off product',NULL,NULL,'percentageOff',20);
  $p8 = Doctrine::getTable('rtShopPromotionProduct')->find($promo8);
  $t->isa_ok($p8, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo8);

  // No date restriction - valid
  $product_id = 1;
  $promo9 = $data->createProductPromotion($product_id,'%30 off product',NULL,NULL,'percentageOff',30);
  $p9 = Doctrine::getTable('rtShopPromotionProduct')->find($promo9);
  $t->isa_ok($p9, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo9);

  // No date restriction - valid
  $product_id = 1;
  $date_to = date('Y-m-d H:i:s',strtotime(sprintf("-%s months",2)));
  $promo10 = $data->createProductPromotion($product_id,'%30 off product',NULL,$date_to,'percentageOff',30);
  $p10 = Doctrine::getTable('rtShopPromotionProduct')->find($promo10);
  $t->isa_ok($p10, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo10);

  // No date restriction - valid
  $product_id = 5;
  $promo11 = $data->createProductPromotion($product_id,'%5 off product',NULL,NULL,'percentageOff',5);
  $p11 = Doctrine::getTable('rtShopPromotionProduct')->find($promo11);
  $t->isa_ok($p11, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo11);

  // No date restriction - valid
  $product_id = 5;
  $promo12 = $data->createProductPromotion($product_id,'%15 off product',NULL,NULL,'percentageOff',15);
  $p12 = Doctrine::getTable('rtShopPromotionProduct')->find($promo12);
  $t->isa_ok($p12, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo12);

  // No date restriction - valid
  $product_id = 4;
  $promo13 = $data->createProductPromotion($product_id,'$6 off product',NULL,NULL,'dollarOff',6);
  $p13 = Doctrine::getTable('rtShopPromotionProduct')->find($promo13);
  $t->isa_ok($p13, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo13);

  // No date restriction - valid
  $product_id = 4;
  $promo14 = $data->createProductPromotion($product_id,'$5 off product',NULL,NULL,'dollarOff',5);
  $p14 = Doctrine::getTable('rtShopPromotionProduct')->find($promo14);
  $t->isa_ok($p14, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo14);

  // No date restriction - valid
  $product_id = 8;
  $promo15 = $data->createProductPromotion($product_id,'20% off product',NULL,NULL,'percentageOff',20,1,false);
  $p15 = Doctrine::getTable('rtShopPromotionProduct')->find($promo15);
  $t->isa_ok($p15, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with Id '.$promo15);

  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('2.1 Check product promotions availability');
  $t->diag('-----------------------------------------------------------------------------');

  $product_promotion = Doctrine::getTable('rtShopPromotionProduct')->find($promo1);
  $t->is($product_promotion->isAvailable(), true, '::isAvailable() product_promotion 1 is available (no date_from nor date_to)');

  $product_promotion = Doctrine::getTable('rtShopPromotionProduct')->find($promo2);
  $t->is($product_promotion->isAvailable(), true, '::isAvailable() product_promotion 2 is available (with date_from < date_now)');

  $product_promotion = Doctrine::getTable('rtShopPromotionProduct')->find($promo3);
  $t->is($product_promotion->isAvailable(), true, '::isAvailable() product_promotion 3 is available (with date_to > date_now)');

  $product_promotion = Doctrine::getTable('rtShopPromotionProduct')->find($promo4);
  $t->is($product_promotion->isAvailable(), true, '::isAvailable() product_promotion 4 is available (with date_from < date_now and date_to > date_now)');

  $product_promotion = Doctrine::getTable('rtShopPromotionProduct')->find($promo5);
  $t->is($product_promotion->isAvailable(), false, '::isAvailable() product_promotion 5 is expired');

  $product_promotion = Doctrine::getTable('rtShopPromotionProduct')->find($promo6);
  $t->is($product_promotion->isAvailable(), false, '::isAvailable() product_promotion 6 has not started yet');

  $product_promotion = Doctrine::getTable('rtShopPromotionProduct')->find($promo7);
  $t->is($product_promotion->isAvailable(), false, '::isAvailable() product_promotion 7 is expired');

  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('2. Check rtShopProduct::getrtShopPromotionsAvailableOnly() method');
  $t->diag('-----------------------------------------------------------------------------');

  $p1 = Doctrine::getTable('rtShopProduct')->find(1);
  $t->is(count($p1->getRtShopPromotions()),4,'::getRtShopPromotions() found a total of '.count($p1->getRtShopPromotions()).' promotions for product 1');
  $t->is(count($p1->getrtShopPromotionsAvailableOnly()),3,'::getrtShopPromotionsAvailableOnly() found '.count($p1->getrtShopPromotionsAvailableOnly()).' available promotions for product 1');

  $p3 = Doctrine::getTable('rtShopProduct')->find(3);
  $t->is(count($p3->getRtShopPromotions()),1,'::getRtShopPromotions() found a total of '.count($p3->getRtShopPromotions()).' promotions for product 3');
  $t->is(count($p3->getrtShopPromotionsAvailableOnly()),1,'::getrtShopPromotionsAvailableOnly() found '.count($p3->getrtShopPromotionsAvailableOnly()).' available promotions for product 3');

  $p5 = Doctrine::getTable('rtShopProduct')->find(5);
  $t->is(count($p5->getRtShopPromotions()),3,'::getRtShopPromotions() found a total of '.count($p5->getRtShopPromotions()).' promotions for product 5');
  $t->is(count($p5->getrtShopPromotionsAvailableOnly()),2,'::getrtShopPromotionsAvailableOnly() found '.count($p3->getrtShopPromotionsAvailableOnly()).' available promotions for product 5');

  $p6 = Doctrine::getTable('rtShopProduct')->find(6);
  $t->is(count($p6->getRtShopPromotions()),1,'::getRtShopPromotions() found a total of '.count($p6->getRtShopPromotions()).' promotions for product 6');
  $t->is(count($p6->getrtShopPromotionsAvailableOnly()),0,'::getrtShopPromotionsAvailableOnly() found no available promotions for product 6');

  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('3. Check isOnPromotion(), getBestPromotion() and getCharge() methods');
  $t->diag('-----------------------------------------------------------------------------');

  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('3.1 Charge for product 5');
  $t->diag('-----------------------------------------------------------------------------');
  $s1 = Doctrine::getTable('rtShopStock')->find(9);
  $t->is($s1->isOnPromotion(), true, '::isOnPromotion() product is on promotion');
  $s1_id = $s1->getBestPromotion()->getId();
  $t->is($s1_id,16,'::getBestPromotion() found correct best product promotion id:'.$s1_id.' for product 5');
  $t->is($s1->getCharge(),12.75,'::getCharge() returned the reduced price by promotion');

  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('3.2 Charge for product 1');
  $t->diag('-----------------------------------------------------------------------------');
  $s2 = Doctrine::getTable('rtShopStock')->find(1);
  $t->is($s2->isOnPromotion(), true, '::isOnPromotion() product is on promotion');
//  $this->log($s2->getStockInfo());
//  exit;
  $s2_id = $s2->getBestPromotion()->getId();
  $t->is($s2_id,13,'::getBestPromotion() found correct best product promotion id:'.$s2_id);
  $t->is($s2->getCharge(),17.5,'::getCharge() returned the reduced price by promotion');

  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('3.3 Charge for product 4');
  $t->diag('-----------------------------------------------------------------------------');
  $s3 = Doctrine::getTable('rtShopStock')->find(7);
  $t->is($s3->isOnPromotion(), true, '::isOnPromotion() product is on promotion');
  $s3_id = $s3->getBestPromotion()->getId();
  $t->is($s3_id,17,'::getBestPromotion() found correct best product promotion id:'.$s3_id);
  $t->is($s3->getCharge(),4,'::getCharge() returned the reduced price by promotion');

  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('3.4 Charge for product 7');
  $t->diag('-----------------------------------------------------------------------------');
  $s4 = Doctrine::getTable('rtShopStock')->find(13);
  $t->is($s4->isOnPromotion(), false, '::isOnPromotion() product is not on promotion');
  $t->is($s4->getBestPromotion(), false, '::getBestPromotion() returns false as expected');
  $t->is($s4->getCharge(),30,'::getCharge() returned the price_retail');

  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('3.5 Charge for product 8');
  $t->diag('-----------------------------------------------------------------------------');
  $s5 = Doctrine::getTable('rtShopStock')->find(15);
  $t->is($s5->isOnPromotion(), true, '::isOnPromotion() product is on promotion');
  $s5_id = $s5->getBestPromotion()->getId();
  $t->is($s5_id,19,'::getBestPromotion() found correct best product promotion id:'.$s5_id);
  $t->is($s5->getCharge(),32,'::getCharge() returned the reduced price by promotion');
          
  $t->diag('-----------------------------------------------------------------------------');
  $t->diag('3.6 Charge for product 8');
  $t->diag('-----------------------------------------------------------------------------');
  $s6 = Doctrine::getTable('rtShopStock')->find(16);
  $t->is($s6->isOnPromotion(), true, '::isOnPromotion() product is on promotion');
  $s6_id = $s6->getBestPromotion()->getId();
  $t->is($s6_id,19,'::getBestPromotion() found correct best product promotion id:'.$s6_id);
  $t->is($s6->getCharge(),30,'::getCharge() returned the price_promotion because lower than reduced price_retail');

  //rtShopStockTestTools::clean();
}
else
{
  throw new sfException('{rtShopStockTest} Fixtures diretory '.$fixtures_path.' does not exist. Unit test aborted.');
}

/**
 * rtShopStockTestTools Class
 */
class rtShopStockTestTools
{
  /**
   * Make sure table is cleaned before testing
   */
  public static function clean()
  {
    $doctrine = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
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
   * Return subtotal for order
   *
   * @param integer $order_id
   * @return float
   */
  public static function getSubTotalOfOrderByStocks($order_id)
  {
    $rt_shop_order_to_stocks = Doctrine::getTable('rtShopOrderToStock')->findByOrderId($order_id);

    $sub_total = 0;
    foreach($rt_shop_order_to_stocks as $rt_shop_order_to_stock)
    {
      $sub_total += $rt_shop_order_to_stock->getQuantity() * $rt_shop_order_to_stock->getRtShopStock()->getPriceRetail();
    }
    return $sub_total;
  }

  /**
   * Create product promotion and connect to product
   *
   * @param integer $product_id
   * @param string $title
   * @param datetime $date_from
   * @param datetime $date_to
   * @param string $type
   * @param number $value
   * @param integer $count
   */
  public function createProductPromotion($product_id,
                                         $title = 'Test Promotion',
                                         $date_from = NULL,
                                         $date_to = NULL,
                                         $type = 'percentageOff',
                                         $value = 10,
                                         $count = 1,
                                         $stackable = true)
  {
    // Create promotion
    $promo = new rtShopPromotionProduct();
    $promo->setTitle($title);
    $promo->setDateFrom($date_from);
    $promo->setDateTo($date_to);
    $promo->setReductionType($type);
    $promo->setReductionValue($value);
    $promo->setCount($count);
    $promo->setStackable($stackable);
    $promo->save();

    // Connect promotion to product
    $promo_product = new rtShopProductToPromotion();
    $promo_product->setProductId($product_id);
    $promo_product->setPromotionId($promo->getId());
    $promo_product->save();

    return $promo->getId();
  }
}