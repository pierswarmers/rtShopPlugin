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
 * $ cp plugins/rtShopPlugin/test/unit/rtShopOrderTest.php test/unit/rtShopOrderTest.php
 *
 */

/**
 * rtShopOrder Testing - Order testing
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
include(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(134, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);
sfContext::createInstance($configuration);

new sfDatabaseManager($configuration);

$t->diag('/////////////////////////////////////////////////////////////////////////////');
$t->diag('/// Shopping cart testing                                                 ///');
$t->diag('/////////////////////////////////////////////////////////////////////////////');

// Disable tax and shipping costs
if(sfConfig::has('app_rt_shop_tax_rate'))
{
  sfConfig::set('app_rt_shop_tax_rate', 0);
}
if(sfConfig::has('app_rt_shop_shipping_charges'))
{
  sfConfig::set('app_rt_shop_shipping_charges', array());
}

$t->diag('-----------------------------------------------------------------------------');
$t->diag('0. Check classes');
$t->diag('-----------------------------------------------------------------------------');

// Cart Manager
$o1 = new rtShopCartManager;
$t->isa_ok($o1, 'rtShopCartManager', '->class() rtShopCartManager object is available');

// Promotion Toolkit
$o2 = new rtShopPromotionToolkit;
$t->isa_ok($o2, 'rtShopPromotionToolkit', '->class() rtShopPromotionToolkit class is available');

// Voucher Toolkit
$o3 = new rtShopVoucherToolkit;
$t->isa_ok($o3, 'rtShopVoucherToolkit', '->class() rtShopVoucherToolkit class is available');

unset($o1,$o3,$o3);

$t->diag('-----------------------------------------------------------------------------');
$t->diag('1. Check models');
$t->diag('-----------------------------------------------------------------------------');

rtShopOrderTestTools::clean();

try {
  $i1 = new rtShopOrder();
  $i1->save();
  $t->pass('->save() rtShopOrder allows empty saves');
  $i1->delete();
} catch (Exception $e) {
  $t->fail('->save() rtShopOrder throws an Exception!');
}
// Clean up
unset($i1);

try {
  $i1 = new rtShopProduct();
  $i1->save();
  $t->pass('->save() rtShopProduct allows empty saves');
  $i1->delete();
} catch (Exception $e) {
  $t->fail('->save() rtShopProduct throws an Exception!');
}
// Clean up
unset($i1);

try {
  $i1 = new rtShopPromotionProduct();
  $i1->save();
  $t->fail('->save() rtShopPromotionProduct allows empty saves');
  $i1->delete();
} catch (Exception $e) {
  $t->pass('->save() rtShopPromotionProduct throws an Exception!');
}
// Clean up
unset($i1);

try {
  $i1 = new rtShopPromotionCart();
  $i1->save();
  $t->fail('->save() rtShopPromotionCart allows empty saves');
  $i1->delete();
} catch (Exception $e) {
  $t->pass('->save() rtShopPromotionCart throws an Exception!');
}
// Clean up
unset($i1);

try {
  $i1 = new rtShopVoucher();
  $i1->save();
  $t->fail('->save() rtShopVoucher allows empty saves');
  $i1->delete();
} catch (Exception $e) {
  $t->pass('->save() rtShopVoucher throws an Exception!');
}

// Clean up
unset($i1);

try {
  $i1 = new rtShopStock();
  $i1->save();
  $t->pass('->save() rtShopStock allows empty saves');
  $i1->delete();
} catch (Exception $e) {
  $t->fail('->save() rtShopStock throws an Exception!');
}

// Clean up
unset($i1);

try {
  $i1 = new rtShopAttribute();
  $i1->save();
  $t->pass('->save() rtShopAttribute allows empty saves');
  $i1->delete();
} catch (Exception $e) {
  $t->fail('->save() rtShopAttribute throws an Exception!');
}

// Clean up
unset($i1);

try {
  $i1 = new rtShopVariation();
  $i1->save();
  $t->pass('->save() rtShopVariation allows empty saves');
  $i1->delete();
} catch (Exception $e) {
  $t->fail('->save() rtShopVariation throws an Exception!');
}

// Clean up
unset($i1);

$t->diag('-----------------------------------------------------------------------------');
$t->diag('2. Add test data');
$t->diag('-----------------------------------------------------------------------------');

rtShopOrderTestTools::clean();

// Add products
try {
  $prod1 = new rtShopProduct();
  $prod1->setTitle('Product A');
  $prod1->save();
  $prod2 = new rtShopProduct();
  $prod2->setTitle('Product B');
  $prod2->save();
  $t->pass('->save() on a rtShopProduct object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopProduct failed!');
}

// Add category
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
  $t->pass('->save() on a rtShopCategory object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopCategory failed!');
}

// Add product to category
try {
  $prod1tocat2 = new rtShopProductToCategory();
  $prod1tocat2->setProductId($prod1->getId());
  $prod1tocat2->setCategoryId($cat2->getId());
  $prod1tocat2->save();
  $prod2tocat2 = new rtShopProductToCategory();
  $prod2tocat2->setProductId($prod2->getId());
  $prod2tocat2->setCategoryId($cat2->getId());
  $prod2tocat2->save();
  $t->pass('->save() on a rtShopProductToCategory object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopProductToCategory failed!');
}

// Add attributes
try {
  $att1 = new rtShopAttribute();
  $att1->setTitle('Attribute A');
  $att1->save();
  $att2 = new rtShopAttribute();
  $att2->setTitle('Attribute B');
  $att2->save();
  $t->pass('->save() on a rtShopAttribute object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopAttribute failed!');
}

// Add product to attributes
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
  $t->pass('->save() on a rtShopProductToAttribute object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopProductToAttribute failed!');
}

// Add variations
try {
  $var1 = new rtShopVariation();
  $var1->setTitle('A1');
  $var1->setAttributeId($att1->getId());
  $var1->setPosition(1);
  $var1->save();
  $var2 = new rtShopVariation();
  $var2->setTitle('A2');
  $var2->setAttributeId($att1->getId());
  $var2->setPosition(2);
  $var2->save();
  $var3 = new rtShopVariation();
  $var3->setTitle('B1');
  $var3->setAttributeId($att2->getId());
  $var3->setPosition(1);
  $var3->save();
  $var4 = new rtShopVariation();
  $var4->setTitle('B2');
  $var4->setAttributeId($att2->getId());
  $var4->setPosition(2);
  $var4->save();
  $t->pass('->save() on a rtShopVariation object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopVariation failed!');
}

// Add stocks
try {
  $stock1 = new rtShopStock();
  $stock1->setProductId($prod1->getId());
  $stock1->setQuantity(5);
  $stock1->setSku(mt_rand(1,10000));
  $stock1->setPriceRetail(100);
  $stock1->save();
  $stock2 = new rtShopStock();
  $stock2->setProductId($prod2->getId());
  $stock2->setQuantity(5);
  $stock2->setSku(mt_rand(1,10000));
  $stock2->setPriceRetail(200);
  $stock2->save();
  $t->pass('->save() on a rtShopStock object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopStock failed!');
}

// Add stock to variation
try {
  $stock1tovar1 = new rtShopStockToVariation();
  $stock1tovar1->setStockId($stock1->getId());
  $stock1tovar1->setVariationId($var1->getId());
  $stock1tovar1->save();
  $stock1tovar3 = new rtShopStockToVariation();
  $stock1tovar3->setStockId($stock1->getId());
  $stock1tovar3->setVariationId($var3->getId());
  $stock1tovar3->save();
  $stock2tovar2 = new rtShopStockToVariation();
  $stock2tovar2->setStockId($stock2->getId());
  $stock2tovar2->setVariationId($var2->getId());
  $stock2tovar2->save();
  $stock2tovar4 = new rtShopStockToVariation();
  $stock2tovar4->setStockId($stock2->getId());
  $stock2tovar4->setVariationId($var4->getId());
  $stock2tovar4->save();
  $t->pass('->save() on a rtShopStockToVariation object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopStockToVariation failed!');
}

$t->diag('-----------------------------------------------------------------------------');
$t->diag('4. Check simple cart functionality');
$t->diag('-----------------------------------------------------------------------------');

$t->diag('-----------------------------------------------------------------------------');
$t->diag('4.1 Two products, just retail prices (no taxes, promotions,etc.)');
$t->diag('-----------------------------------------------------------------------------');

// Instanciate cart manager
try {
  $cm = new rtShopCartManager();
  $t->pass('->create() on a rtShopCartManager object works');
} catch (Exception $e) {
  $t->fail('->create() on a rtShopCartManager failed!');
}

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

$t->is($cm->getItemsInCart(), 2, '::getItemsInCart() returns the correct number of items in cart');
$t->is($cm->getItemsQuantityInCart(), 2, '::getItemsQuantityInCart() returns the correct items quantity in cart');
$t->isa_ok($cm->getOrder(), 'rtShopOrder', '->class() rtShopOrder was created successfully');

$total1 = 300;
$t->is($cm->getItemsCharge(), $total1, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), $total1, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), $total1, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), $total1, '::getTotalCharge() returns the correct value');

$t->diag('-----------------------------------------------------------------------------');
$t->diag('4.2 Two products, one product is on promotion (no taxes, promotions,etc.)');
$t->diag('-----------------------------------------------------------------------------');

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();

// Add promotion price to stock 2
$stock2->setPricePromotion(150);
$stock2->save();

$t->is($stock2->getPricePromotion(), 150, '::getPricePromotion() returns the correct adjusted promotion price on stock '.$stock2->getPricePromotion());

$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

$t->is($cm->getItemsInCart(), 2, '::getItemsInCart() returns the correct number of items in cart');
$t->is($cm->getItemsQuantityInCart(), 2, '::getItemsQuantityInCart() returns the correct items quantity in cart');
$t->isa_ok($cm->getOrder(), 'rtShopOrder', '->class() rtShopOrder was created successfully');

$total2 = 250;
$t->is($cm->getItemsCharge(), $total2, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), $total2, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), $total2, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), $total2, '::getTotalCharge() returns the correct value');

$t->diag('-----------------------------------------------------------------------------');
$t->diag('4.3 Two products, just retail prices + taxes (exclusive or inclusive)');
$t->diag('-----------------------------------------------------------------------------');

$t->diag('4.3.1 Tax mode: Exclusive *******************************************************');

sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'exclusive');

// Check config values
$t->is(sfConfig::get('app_rt_shop_tax_rate'), 10, 'sfConfig::app_rt_shop_tax_rate was set correctly to '.sfConfig::get('app_rt_shop_tax_rate'));
$t->is(sfConfig::get('app_rt_shop_tax_mode'), 'exclusive', 'sfConfig::app_rt_shop_tax_mode was set correctly to '.sfConfig::get('app_rt_shop_tax_mode'));

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();

// Add promotion price to stock 2
$stock2->setPricePromotion(NULL);
$stock2->save();

$cm = new rtShopCartManager();

$t->is($cm->getTaxMode(), sfConfig::get('app_rt_shop_tax_mode'), '::getTaxMode() returns the same tax mode as sfConfig::app_rt_shop_tax_mode');
$t->is($cm->getTaxRate(), sfConfig::get('app_rt_shop_tax_rate'), '::getTaxRate() returns the same tax rate as sfConfig::app_rt_shop_tax_rate');

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Check items in cart
$t->is($cm->getItemsInCart(), 2, '::getItemsInCart() returns the correct number of items in cart');
$t->is($cm->getItemsQuantityInCart(), 2, '::getItemsQuantityInCart() returns the correct items quantity in cart');
$t->isa_ok($cm->getOrder(), 'rtShopOrder', '->class() rtShopOrder was created successfully');

//In exclusive mode, inclusive mode should return 0
$t->is($cm->getTaxComponent(), 0, '::getTaxComponent() returns the correct value');

// Taxes
$t->is($cm->getTaxCharge(), 30, '::getTaxCharge() returns the correct value');

// Totals
$t->is($cm->getItemsCharge(), 300, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 300, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 330, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 330, '::getTotalCharge() returns the correct value');

$t->diag('4.3.2 Tax mode: Inclusive *******************************************************');

sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'inclusive');

// Check config values
$t->is(sfConfig::get('app_rt_shop_tax_rate'), 10, 'sfConfig::app_rt_shop_tax_rate was set correctly to '.sfConfig::get('app_rt_shop_tax_rate'));
$t->is(sfConfig::get('app_rt_shop_tax_mode'), 'inclusive', 'sfConfig::app_rt_shop_tax_mode was set correctly to '.sfConfig::get('app_rt_shop_tax_mode'));

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();

// Add promotion price to stock 2
$stock2->setPricePromotion(NULL);
$stock2->save();

$cm = new rtShopCartManager();

$t->is($cm->getTaxMode(), sfConfig::get('app_rt_shop_tax_mode'), '::getTaxMode() returns the same tax mode as sfConfig::app_rt_shop_tax_mode');
$t->is($cm->getTaxRate(), sfConfig::get('app_rt_shop_tax_rate'), '::getTaxRate() returns the same tax rate as sfConfig::app_rt_shop_tax_rate');

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Check items in cart
$t->is($cm->getItemsInCart(), 2, '::getItemsInCart() returns the correct number of items in cart');
$t->is($cm->getItemsQuantityInCart(), 2, '::getItemsQuantityInCart() returns the correct items quantity in cart');
$t->isa_ok($cm->getOrder(), 'rtShopOrder', '->class() rtShopOrder was created successfully');

//In inclusive mode, exclusive mode should return 0
$t->is($cm->getTaxCharge(), 0, '::getTaxCharge() returns the correct value');

// Taxes
$tax_component = ($cm->getTotalCharge() * 10) / ($cm->getTaxRate() + 100);;
$t->is($cm->getTaxComponent(), $tax_component, '::getTaxComponent() returns the correct value');

// Totals
$t->is($cm->getItemsCharge(), 300, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 300, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 300, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 300, '::getTotalCharge() returns the correct value');

$t->diag('-----------------------------------------------------------------------------');
$t->diag('4.4 Two products + taxes + domestic/international shipping');
$t->diag('-----------------------------------------------------------------------------');

$t->diag('4.4.1 Tax mode: Exclusive (international shipping) **************************');

sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'exclusive');
sfConfig::set('app_rt_shop_shipping_charges', array('default' => 20, 'AU' => 10));

$shipping_charges = sfConfig::get('app_rt_shop_shipping_charges');
$t->is($shipping_charges['default'], 20, 'sfConfig::app_rt_shop_shipping_charges returns correct default shipping charge');
$t->is($shipping_charges['AU'], 10, 'sfConfig::app_rt_shop_shipping_charges returns correct AU shipping charge');

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Add addresses to order
$tools = new rtShopOrderTestTools;
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Shipping charge
$t->is($cm->getShippingCharge(), 10, '::getShippingCharge() returns the correct value');

// Totals
$t->is($cm->getItemsCharge(), 300, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 300, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 340, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 340, '::getTotalCharge() returns the correct value');

$t->diag('4.4.2 Tax mode: Inclusive (international shipping) **************************');

sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'inclusive');
sfConfig::set('app_rt_shop_shipping_charges', array('default' => 20, 'AU' => 10));

$shipping_charges = sfConfig::get('app_rt_shop_shipping_charges');
$t->is($shipping_charges['default'], 20, 'sfConfig::app_rt_shop_shipping_charges returns correct default shipping charge');
$t->is($shipping_charges['AU'], 10, 'sfConfig::app_rt_shop_shipping_charges returns correct AU shipping charge');

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Add addresses to order
$tools = new rtShopOrderTestTools;
$tools->addAddressForOrder($cm->getOrder()->getId());
$tools->addAddressForOrder($cm->getOrder()->getId(),'shipping');

// Shipping charge
$t->is($cm->getShippingCharge(), 10, '::getShippingCharge() returns the correct value');

// Totals
$t->is($cm->getItemsCharge(), 300, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 300, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 310, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 310, '::getTotalCharge() returns the correct value');

$t->diag('4.4.3 Tax mode: Exclusive (default shipping) ********************************');

sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'exclusive');
sfConfig::set('app_rt_shop_shipping_charges', array('default' => 20, 'NZ' => 10));

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Shipping charge
$t->is($cm->getShippingCharge(), 20, '::getShippingCharge() returns the correct value');

// Totals
$t->is($cm->getItemsCharge(), 300, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 300, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 350, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 350, '::getTotalCharge() returns the correct value');

$t->diag('4.4.4 Tax mode: Inclusive (default shipping) ********************************');

sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'inclusive');
sfConfig::set('app_rt_shop_shipping_charges', array('default' => 20, 'NZ' => 10));

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Shipping charge
$t->is($cm->getShippingCharge(), 20, '::getShippingCharge() returns the correct value');

// Totals
$t->is($cm->getItemsCharge(), 300, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 300, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 320, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 320, '::getTotalCharge() returns the correct value');

$t->diag('-----------------------------------------------------------------------------');
$t->diag('4.5 Two products + taxes + shipping + product promotion');
$t->diag('-----------------------------------------------------------------------------');

sfConfig::set('app_rt_shop_tax_rate', 10);
sfConfig::set('app_rt_shop_tax_mode', 'exclusive');
sfConfig::set('app_rt_shop_shipping_charges', array('default' => 20, 'NZ' => 10));

$t->diag('4.5.1 Add one product promotion *********************************************');

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

$tools = new rtShopOrderTestTools;
$promo1 = $tools->createProductPromotion($prod1->getId(),'10% off product');
$promo = Doctrine::getTable('rtShopPromotionProduct')->find($promo1);
$t->isa_ok($promo, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with ID '.$promo1);
$t->is($promo->isAvailable(), true, '::isAvailable() product_promotion is available');

$p1 = Doctrine::getTable('rtShopProduct')->find($prod1->getId());
$p1->refresh(true);
//$p1->refreshRelated();
$t->is(count($p1->getRtShopPromotions()),1,'::getRtShopPromotions() found a total of '.count($p1->getRtShopPromotions()).' promotions for product 1');
$t->is(count($p1->getrtShopPromotionsAvailableOnly()),1,'::getrtShopPromotionsAvailableOnly() found '.count($p1->getrtShopPromotionsAvailableOnly()).' available promotions for product 1');

//$stock = Doctrine::getTable('rtShopStock')->find($stock1->getId());
$t->is($stock1->isOnPromotion(), true, '::isOnPromotion() product is on promotion');

// Totals
$t->is($cm->getItemsCharge(), 290, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 290, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 339, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 339, '::getTotalCharge() returns the correct value');

$t->diag('4.5.2 Add second product promotion, use getBest() ***************************');

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Add better promotion (10% is already available for product 1)
$tools = new rtShopOrderTestTools;
$promo2 = $tools->createProductPromotion($prod1->getId(),'20% off product',20);
$promo = Doctrine::getTable('rtShopPromotionProduct')->find($promo2);
$t->isa_ok($promo, 'rtShopPromotionProduct', '::find() created and retrieved product promotion with ID '.$promo2);
$t->is($promo->isAvailable(), true, '::isAvailable() product_promotion is available');

$p1 = Doctrine::getTable('rtShopProduct')->find($prod1->getId());
$p1->refresh(true);
$t->is(count($p1->getRtShopPromotions()),2,'::getRtShopPromotions() found a total of '.count($p1->getRtShopPromotions()).' promotions for product 1');
$t->is(count($p1->getrtShopPromotionsAvailableOnly()),2,'::getrtShopPromotionsAvailableOnly() found '.count($p1->getrtShopPromotionsAvailableOnly()).' available promotions for product 1');

//$stock = Doctrine::getTable('rtShopStock')->find($stock1->getId());
$t->is($stock1->isOnPromotion(), true, '::isOnPromotion() product is on promotion');

// Totals
$t->is($cm->getItemsCharge(), 280, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 280, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 328, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 328, '::getTotalCharge() returns the correct value');

$t->diag('-----------------------------------------------------------------------------');
$t->diag('4.6 Two products + taxes + shipping + product promotion + cart promotion');
$t->diag('-----------------------------------------------------------------------------');

// Add cart promotion
$cartpromo1 = $tools->createCartPromotion('Test Promotion 5% - 200 to 300', 5, 200, 300);
$cartpromo2 = $tools->createCartPromotion('Test Promotion 10% - 300 to 400', 10, 100, 300);
$cartpromo3 = $tools->createCartPromotion('Test Promotion 15% - 300 to 400', 15, 300, 400);

// Check that cart promotions added
$cp1 = Doctrine::getTable('rtShopPromotionCart')->find($cartpromo1);
$cp2 = Doctrine::getTable('rtShopPromotionCart')->find($cartpromo2);
$cp3 = Doctrine::getTable('rtShopPromotionCart')->find($cartpromo3);
$t->isa_ok($cp1, 'rtShopPromotionCart', '::find() created and retrieved rtShopPromotionCart item');
$t->isa_ok($cp2, 'rtShopPromotionCart', '::find() created and retrieved rtShopPromotionCart item');
$t->isa_ok($cp3, 'rtShopPromotionCart', '::find() created and retrieved rtShopPromotionCart item');

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Promotions
$t->is($cm->getPromotion()->getId(), 4, '::getPromotion()->getId() returns the correct best cart promotion object');
$t->is($cm->getPromotionReduction(), 28, '::getPromotionReduction() returns the correct best cart promotion reduction');

// Totals
$t->is($cm->getItemsCharge(), 280, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 280, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 300, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 300, '::getTotalCharge() returns the correct value');

$t->diag('-----------------------------------------------------------------------------');
$t->diag('4.7 Order + taxes + shipping + product promotion + cart promotion + voucher');
$t->diag('-----------------------------------------------------------------------------');

// Add voucher
$voucher1 = $tools->createVoucher('Test Voucher $10 - 150 to 250', 10, 'dollarOff', 150, 250);  // Valid voucher
$voucher2 = $tools->createVoucher('Test Voucher $20 - 250 to 400', 10, 'dollarOff', 250, 400);  // Valid voucher
$voucher3 = $tools->createVoucher('Test Voucher $30 - 400 to 500', 20, 'dollarOff', 400, 500);  // Valid voucher
$voucher4 = $tools->createVoucher('Test Voucher $30 - 400 to 500', 20, 'dollarOff', 400, 500, date('Y-m-d H:i:s',strtotime(sprintf("-%s months",4))), date('Y-m-d H:i:s',strtotime(sprintf("-%s months",2)))); // Expired voucher

// Check that cart promotions added
$v1 = Doctrine::getTable('rtShopVoucher')->find($voucher1->getId());
$v2 = Doctrine::getTable('rtShopVoucher')->find($voucher2->getId());
$v3 = Doctrine::getTable('rtShopVoucher')->find($voucher3->getId());
$v4 = Doctrine::getTable('rtShopVoucher')->find($voucher4->getId());
$t->isa_ok($v1, 'rtShopVoucher', '::find() created and retrieved rtShopVoucher successfully');
$t->isa_ok($v2, 'rtShopVoucher', '::find() created and retrieved rtShopVoucher successfully');
$t->isa_ok($v3, 'rtShopVoucher', '::find() created and retrieved rtShopVoucher successfully');
$t->isa_ok($v4, 'rtShopVoucher', '::find() created and retrieved rtShopVoucher successfully');

$t->diag('4.7.1 Use voucher which does not apply **************************************');

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Voucher
$cm->setVoucherCode($voucher1->getCode());
$t->is($cm->getVoucherCode(), $voucher1->getCode(), '::getVoucherCode() returns correct voucher code');
$t->is($cm->getVoucherReduction(), 0.0, '::getVoucherReduction() returns 0.0 for no voucher applied');

//// Totals
$t->is($cm->getItemsCharge(), 280, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 280, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 300, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 300, '::getTotalCharge() returns the correct value');

$t->diag('4.7.2 Use expired voucher ***************************************************');

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Voucher
$cm->setVoucherCode($voucher4->getCode());
$t->is($cm->getVoucherCode(), $voucher4->getCode(), '::getVoucherCode() returns correct voucher code');
$t->is($cm->getVoucherReduction(), 0.0, '::getVoucherReduction() returns 0.0 for no voucher applied');

//// Totals
$t->is($cm->getItemsCharge(), 280, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 280, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 300, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 300, '::getTotalCharge() returns the correct value');

$t->diag('4.7.3 Use valid voucher *****************************************************');

// Clean order and reset cart manager
unset($cm);
rtShopOrderTestTools::cleanOrder();
$cm = new rtShopCartManager();

// Add stocks to cart manager
$cm->addToCart($stock1, 1);
$cm->addToCart($stock2, 1);

// Voucher
$cm->setVoucherCode($voucher2->getCode());
$t->is($cm->getVoucherCode(), $voucher2->getCode(), '::getVoucherCode() returns correct voucher code');
$t->is($cm->getVoucherReduction(), 10, '::getVoucherReduction() returns correct voucher reduction');

//// Totals
$t->is($cm->getItemsCharge(), 280, '::getItemsCharge() returns the correct value');
$t->is($cm->getSubTotal(), 280, '::getSubTotal() returns the correct value');
$t->is($cm->getPreTotalCharge(), 300, '::getPreTotalCharge() returns the correct value');
$t->is($cm->getTotalCharge(), 290, '::getTotalCharge() returns the correct value');

/**
 * rtShopOrderTestTools Class
 */
class rtShopOrderTestTools
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
    $address->setFirstName('Konny');
    $address->setLastName('Zurcher');
    $address->setAddress_1('70 Mary Street');
    $address->setTown('Surry Hills');
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
                                         $date_from = NULL,
                                         $date_to = NULL,
                                         $type = 'percentageOff',
                                         $count = 1,
                                         $stackable = true)
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