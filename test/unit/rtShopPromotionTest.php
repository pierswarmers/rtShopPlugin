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
 * $ cp plugins/rtShopPlugin/test/unit/rtShopPromotionTest.php test/unit/rtShopPromotionTest.php
 *
 */

/**
 * rtShopPromotion Testing
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

include(dirname(__FILE__).'/../bootstrap/unit.php');

$_debug_date_available = true;

$t = new lime_test(71, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);

new sfDatabaseManager($configuration);

$t->diag('-----------------------');
$t->diag('rtShopPromotion Testing');
$t->diag('-----------------------');

$t->diag('*** General saving test ***');

$t->diag('1. Empty save should fail (title is required)');
try {
  $i1 = new rtShopPromotion();
  $i1->save();
  $t->fail('->save() should throw a rtShopException on fresh rtShopPromotions');
  $i1->delete();
} catch (Exception $e) {
  $t->pass('->save() throws an Exception');
}

// Clean up
//$i1->delete();
unset ($i1);

$t->diag('2. Enter promotion and retrieve it');
try {
  $i2 = new rtShopPromotion();
  $i2->setTitle('Summer Sale');
	$i2->save();
	$t->pass('->save() on a rtShopPromotion object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopPromotion failed!');
}

// Retrieve by pk
$o1 = Doctrine::getTable('rtShopPromotion')->find($i2->getId());
$t->isa_ok($o1, 'rtShopPromotion', '->retrieve() rtShopPromotion object was saved to database and retrieved successfully');

// Clean up
$i2->delete();
unset ($i2);
unset ($o1);

// Show date and availability tests
if ($_debug_date_available)
{
  $t->diag('*************************');
  $t->diag('*** Availability only ***');
  $t->diag('*************************');

  // Add promotions data
  try {
    rtShopPromotionTestTools::clean();

    // Add 3 promotions
    $i3 = new rtShopPromotion();
    $i3->setTitle('Date only');
    $i3->setDateFrom(NULL);
    $i3->setDateTo(NULL);
    $i3->save();
    $i4 = new rtShopPromotion();
    $i4->setTitle('Date only');
    $i4->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
    $i4->setDateTo(NULL);
    $i4->save();
    $i5 = new rtShopPromotion();
    $i5->setTitle('Date only');
    $i5->setDateFrom(NULL);
    $i5->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
    $i5->save();
    $i6 = new rtShopPromotion();
    $i6->setTitle('Date only');
    $i6->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
    $i6->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
    $i6->save();
    $t->pass('->save() on a rtShopPromotion object works');
  } catch (Exception $e) {
    $t->fail('->save() on a rtShopPromotion failed!');
  }

  $t->diag('date_from <= date < date_to');

  // Date
  $o2 = Doctrine::getTable('rtShopPromotion')->getDateRestrictionQuery(date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
  $t->is(count($o2), 4, '::getDateRestrictionQuery() retrieved 4 object successfully');

  // Date
  $o3 = Doctrine::getTable('rtShopPromotion')->getDateRestrictionQuery(date("Y-m-d H:i:s",strtotime(sprintf("-%s months",4))));
  $t->is(count($o3), 2, '::getDateRestrictionQuery() retrieved 2 object successfully');

  // Date
  $o4 = Doctrine::getTable('rtShopPromotion')->getDateRestrictionQuery(date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
  $t->is(count($o4), 2, '::getDateRestrictionQuery() retrieved 2 object successfully');

  // Date
  $o5 = Doctrine::getTable('rtShopPromotion')->getDateRestrictionQuery(NULL);
  $t->is(count($o5), 4, '::getDateRestrictionQuery() retrieved 4 object successfully');

  // Clean up
  $i3->delete();
  $i4->delete();
  $i5->delete();
  $i6->delete();
  unset ($i3,$i4,$i5,$i6);
  unset ($o2,$o3,$o4,$o5);

  $t->diag('*******************');
  $t->diag('*** Totals only ***');
  $t->diag('*******************');

  // Add promotions data
  try {
    rtShopPromotionTestTools::clean();

    // Add 3 promotions
    $i3 = new rtShopPromotion();
    $i3->setTitle('Total only');
    $i3->setDateFrom(NULL);
    $i3->setDateTo(NULL);
    $i3->setTotalFrom(100);
    $i3->setReductionType('dollarOff');
    $i3->setReductionValue(10);
    $i3->save();
    $i4 = new rtShopPromotion();
    $i4->setTitle('Total only');
    $i4->setDateFrom(NULL);
    $i4->setDateTo(NULL);
    $i4->setTotalTo(100);
    $i4->setReductionType('dollarOff');
    $i4->setReductionValue(10);
    $i4->save();
    $i5 = new rtShopPromotion();
    $i5->setTitle('Total only');
    $i5->setDateFrom(NULL);
    $i5->setDateTo(NULL);
    $i5->setTotalFrom(100);
    $i5->setTotalTo(200);
    $i5->setReductionType('dollarOff');
    $i5->setReductionValue(10);
    $i5->save();
    $t->pass('->save() on a rtShopPromotion object works');
  } catch (Exception $e) {
    $t->fail('->save() on a rtShopPromotion failed!');
  }

  $t->diag('total_from <= total < total_to');

  // Total
  $o2 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,NULL);
  $t->is(count($o2), 1, '::findAvailable() retrieved 1 object successfully');

  // Total
  $o3 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,NULL);
  $t->is(count($o3), 2, '::findAvailable() retrieved 2 objects successfully');

  // Total
  $o4 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,NULL);
  $t->is(count($o4), 1, '::findAvailable() retrieved 1 object successfully');

  // Total
  $o5 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,NULL);
  $t->is(count($o5), 1, '::findAvailable() retrieved 1 object successfully');

  // Clean up
  $i3->delete();
  $i4->delete();
  $i5->delete();
  unset($i3,$i4,$i5);
  unset ($o2,$o3,$o4,$o5);

  $t->diag('**********************************************');
  $t->diag('*** Combination: date_from and order total ***');
  $t->diag('**********************************************');

  // Add promotions data
  try {
    rtShopPromotionTestTools::clean();

    // Add 3 promotions
    $i3 = new rtShopPromotion();
    $i3->setTitle('Date_from and total');
    $i3->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2))));
    $i3->setDateTo(NULL);
    $i3->setTotalFrom(100);
    $i3->setReductionType('dollarOff');
    $i3->setReductionValue(10);
    $i3->save();
    $i4 = new rtShopPromotion();
    $i4->setTitle('Date_from and total');
    $i4->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2))));
    $i4->setDateTo(NULL);
    $i4->setTotalTo(100);
    $i4->setReductionType('dollarOff');
    $i4->setReductionValue(10);
    $i4->save();
    $i5 = new rtShopPromotion();
    $i5->setTitle('Date_from and total');
    $i5->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2))));
    $i5->setDateTo(NULL);
    $i5->setTotalFrom(100);
    $i5->setTotalTo(200);
    $i5->setReductionType('dollarOff');
    $i5->setReductionValue(10);
    $i5->save();
    $t->pass('->save() on a rtShopPromotion object works');
  } catch (Exception $e) {
    $t->fail('->save() on a rtShopPromotion failed!');
  }

  $t->diag('Date < date_from');
  $o2 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
  $t->is(count($o2), 0, '::findAvailable() retrieved 0 object successfully');
  $o3 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
  $t->is(count($o3), 0, '::findAvailable() retrieved 0 object successfully');
  $o4 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
  $t->is(count($o4), 0, '::findAvailable() retrieved 0 object successfully');
  $o5 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
  $t->is(count($o5), 0, '::findAvailable() retrieved 0 object successfully');

  $t->diag('Date > date_from');
  $o6 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",3))));
  $t->is(count($o6), 1, '::findAvailable() retrieved 1 object successfully');
  $o7 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",3))));
  $t->is(count($o7), 2, '::findAvailable() retrieved 2 objects successfully');
  $o8 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",3))));
  $t->is(count($o8), 1, '::findAvailable() retrieved 1 object successfully');
  $o9 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",3))));
  $t->is(count($o9), 1, '::findAvailable() retrieved 1 object successfully');

  // Clean up
  $i3->delete();
  $i4->delete();
  $i5->delete();
  unset($i3,$i4,$i5);
  unset ($o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9);

  $t->diag('********************************************');
  $t->diag('*** Combination: date_to and order total ***');
  $t->diag('********************************************');

  // Add promotions data
  try {
    rtShopPromotionTestTools::clean();

    // Add 3 promotions
    $i3 = new rtShopPromotion();
    $i3->setTitle('Date_to and total');
    $i3->setDateFrom(NULL);
    $i3->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2))));
    $i3->setTotalFrom(100);
    $i3->setReductionType('dollarOff');
    $i3->setReductionValue(10);
    $i3->save();
    $i4 = new rtShopPromotion();
    $i4->setTitle('Date_to and total');
    $i4->setDateFrom(NULL);
    $i4->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2))));
    $i4->setTotalTo(100);
    $i4->setReductionType('dollarOff');
    $i4->setReductionValue(10);
    $i4->save();
    $i5 = new rtShopPromotion();
    $i5->setTitle('Date_to and total');
    $i5->setDateFrom(NULL);
    $i5->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2))));
    $i5->setTotalFrom(100);
    $i5->setTotalTo(200);
    $i5->setReductionType('dollarOff');
    $i5->setReductionValue(10);
    $i5->save();
    $t->pass('->save() on a rtShopPromotion object works');
  } catch (Exception $e) {
    $t->fail('->save() on a rtShopPromotion failed!');
  }

  $t->diag('Date < date_to');
  $o2 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
  $t->is(count($o2), 1, '::findAvailable() retrieved 1 object successfully');
  $o3 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
  $t->is(count($o3), 2, '::findAvailable() retrieved 2 objects successfully');
  $o4 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
  $t->is(count($o4), 1, '::findAvailable() retrieved 1 object successfully');
  $o5 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
  $t->is(count($o5), 1, '::findAvailable() retrieved 1 object successfully');

  $t->diag('Date > date_to');
  $o6 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
  $t->is(count($o6), 0, '::findAvailable() retrieved 0 object successfully');
  $o7 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
  $t->is(count($o7), 0, '::findAvailable() retrieved 0 object successfully');
  $o8 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
  $t->is(count($o8), 0, '::findAvailable() retrieved 0 object successfully');
  $o9 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
  $t->is(count($o9), 0, '::findAvailable() retrieved 0 object successfully');

  // Clean up
  $i3->delete();
  $i4->delete();
  $i5->delete();
  unset($i3,$i4,$i5);
  unset ($o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9);

  $t->diag('******************************************************');
  $t->diag('*** Combination: date_from/date_to and order total ***');
  $t->diag('******************************************************');

  // Add promotions data
  $set_date_from = date("Y-m-d H:i:s");
  $set_date_to = sprintf("+%s months",3);
  try {
    rtShopPromotionTestTools::clean();

    // Add 3 promotions
    $i3 = new rtShopPromotion();
    $i3->setTitle('Combinations');
    $i3->setDateFrom(date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s"))));
    $i3->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",3))));
    $i3->setTotalFrom(100);
    $i3->setReductionType('dollarOff');
    $i3->setReductionValue(10);
    $i3->save();
    $i4 = new rtShopPromotion();
    $i4->setTitle('Combinations');
    $i4->setDateFrom(date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s"))));
    $i4->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",3))));
    $i4->setTotalTo(100);
    $i4->setReductionType('dollarOff');
    $i4->setReductionValue(10);
    $i4->save();
    $i5 = new rtShopPromotion();
    $i5->setTitle('Combinations');
    $i5->setDateFrom(date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s"))));
    $i5->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",3))));
    $i5->setTotalFrom(100);
    $i5->setTotalTo(200);
    $i5->setReductionType('dollarOff');
    $i5->setReductionValue(10);
    $i5->save();
    $t->pass('->save() on a rtShopPromotion object works');
  } catch (Exception $e) {
    $t->fail('->save() on a rtShopPromotion failed!');
  }

  $t->diag('Date < date_from');
  $o2 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2)))); // -2 to FROM_DATE
  $t->is(count($o2), 0, '::findAvailable() retrieved 0 object successfully');
  $o3 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
  $t->is(count($o3), 0, '::findAvailable() retrieved 0 object successfully');
  $o4 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
  $t->is(count($o4), 0, '::findAvailable() retrieved 0 object successfully');
  $o5 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
  $t->is(count($o5), 0, '::findAvailable() retrieved 0 object successfully');

  $t->diag('date_from <= Date < date_to');
  $o6 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2)))); // +2
  $t->is(count($o6), 1, '::findAvailable() retrieved 1 object successfully');
  $o7 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
  $t->is(count($o7), 2, '::findAvailable() retrieved 2 objects successfully');
  $o8 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
  $t->is(count($o8), 1, '::findAvailable() retrieved 1 object successfully');
  $o9 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
  $t->is(count($o9), 1, '::findAvailable() retrieved 1 object successfully');

  $t->diag('Date > date_to');
  $o10 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4)))); // +6
  $t->is(count($o10), 0, '::findAvailable() retrieved 0 object successfully');
  $o11 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
  $t->is(count($o11), 0, '::findAvailable() retrieved 0 object successfully');
  $o12 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
  $t->is(count($o12), 0, '::findAvailable() retrieved 0 object successfully');
  $o13 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
  $t->is(count($o13), 0, '::findAvailable() retrieved 0 object successfully');

  $t->diag('Date = date_from');
  $o14 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,date("Y-m-d H:i:s",strtotime($set_date_from)));
  $t->is(count($o14), 1, '::findAvailable() retrieved 1 object successfully');
  $o15 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,date("Y-m-d H:i:s",strtotime($set_date_from)));
  $t->is(count($o15), 2, '::findAvailable() retrieved 2 object successfully');
  $o16 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,date("Y-m-d H:i:s",strtotime($set_date_from)));
  $t->is(count($o16), 1, '::findAvailable() retrieved 1 object successfully');
  $o17 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,date("Y-m-d H:i:s",strtotime($set_date_from)));
  $t->is(count($o17), 1, '::findAvailable() retrieved 1 object successfully');

  $t->diag('Date = date_to');
  $o18 = Doctrine::getTable('rtShopPromotion')->findAvailable(70,date("Y-m-d H:i:s",strtotime($set_date_to)));
  $t->is(count($o18), 0, '::findAvailable() retrieved 0 object successfully');
  $o19 = Doctrine::getTable('rtShopPromotion')->findAvailable(130,date("Y-m-d H:i:s",strtotime($set_date_to)));
  $t->is(count($o19), 0, '::findAvailable() retrieved 0 object successfully');
  $o20 = Doctrine::getTable('rtShopPromotion')->findAvailable(30,date("Y-m-d H:i:s",strtotime($set_date_to)));
  $t->is(count($o20), 0, '::findAvailable() retrieved 0 object successfully');
  $o21 = Doctrine::getTable('rtShopPromotion')->findAvailable(0,date("Y-m-d H:i:s",strtotime($set_date_to)));
  $t->is(count($o21), 0, '::findAvailable() retrieved 0 object successfully');

  // Clean up
  $i3->delete();
  $i4->delete();
  $i5->delete();
  unset($set_date_from,$set_date_to);
  unset($i3,$i4,$i5);
  unset ($o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9,$o10,$o11,$o12,$o13,$o14,$o15,$o16,$o17,$o18,$o19,$o20,$o21);
}

$t->diag('************************');
$t->diag('*** Promotions tools ***');
$t->diag('************************');

$promotions1 = array(
  'id' => 1,
  'stackable' => false,
  'date_from' => date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))),
  'date_to' => date("Y-m-d H:i:s",strtotime(sprintf("+%s months",1))),
  'total_from' => 100.00,
  'total_to' => 200.00,
  'reduction_type' => 'percentageOff',
  'reduction_value' => 10.00,
  'title' => 'Summer Sale',
  'comment' => ''
);
$promotions2 = array(
  'id' => 2,
  'stackable' => false,
  'date_from' => date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))),
  'date_to' => date("Y-m-d H:i:s",strtotime(sprintf("+%s months",1))),
  'total_from' => 150.00,
  'total_to' => 200.00,
  'reduction_type' => 'percentageOff',
  'reduction_value' => 15.00,
  'title' => 'Summer Sale',
  'comment' => ''
);
$promotions3 = array(
  'id' => 3,
  'stackable' => false,
  'date_from' => date("Y-m-d H:i:s",strtotime(sprintf("+%s months",1))),
  'date_to' => date("Y-m-d H:i:s",strtotime(sprintf("+%s months",3))),
  'total_from' => 120.00,
  'total_to' => 200.00,
  'reduction_type' => 'percentageOff',
  'reduction_value' => 8.00,
  'title' => 'Summer Sale',
  'comment' => ''
);
$promotions4 = array(
  'id' => 3,
  'stackable' => false,
  'date_from' => date("Y-m-d H:i:s",strtotime(sprintf("-%s months",1))),
  'date_to' => date("Y-m-d H:i:s",strtotime(sprintf("+%s months",1))),
  'total_from' => null,
  'total_to' => 100.00,
  'reduction_type' => 'dollarOff',
  'reduction_value' => 5.00,
  'title' => 'Summer Sale',
  'comment' => ''
);
$promotions = array($promotions2, $promotions1, $promotions3, $promotions4);

$t->diag('Array - Ordered promotions by saving');

$promotions = rtShopPromotionToolkit::orderPromotionsBySaving($promotions, 80);
//print_r($promotions);

$t->is(isset($promotions[0]['reduced_total']), true, '::orderPromotionsBySaving() Array has a reduced_total attribute.');
$t->is($promotions[0]['id'], 2, '::orderPromotionsBySaving() Array appears to sorted correctly by reduced_total.');
$t->is($promotions[0]['reduced_total'], 68, '::orderPromotionsBySaving() Array appears to sorted correctly ascending by reduced_total.');

$t->diag('Array - Ordered promotions by total');

$promotions = rtShopPromotionToolkit::orderPromotionsByTotalFrom($promotions, 80);
//print_r($promotions);

$t->is(isset($promotions[0]['distance_to']), true, '::orderPromotionsByTotalFrom() Array has a distance_to attribute.');
$t->is($promotions[0]['id'], 1, '::orderPromotionsByTotalFrom() Array appears to sorted correctly by distance_to.');
$t->is($promotions[0]['distance_to'], 20, '::orderPromotionsByTotalFrom() Array appears to sorted correctly ascending by distance_to.');

unset($promotions,$promotions1,$promotions2,$promotions3,$promotions4);

// Add promotions data
try {
  rtShopPromotionTestTools::clean();

  $i3 = new rtShopPromotion();
  $i3->setTitle('Combinations');
  $i3->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",2))));
  $i3->setTotalFrom(100);
  $i3->setTotalTo(200);
  $i3->setReductionType('percentageOff');
  $i3->setReductionValue(10);
  $i3->save();
  $i4 = new rtShopPromotion();
  $i4->setTitle('Combinations');
  $i4->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",2))));
  $i4->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",1))));
  $i4->setTotalTo(50);
  $i4->setReductionType('dollarOff');
  $i4->setReductionValue(10);
  $i4->save();
  $i5 = new rtShopPromotion();
  $i5->setTitle('Combinations');
  $i5->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",1))));
  $i5->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",3))));
  $i5->setTotalFrom(50);
  $i5->setReductionType('dollarOff');
  $i5->setReductionValue(7);
  $i5->save();
  $i6 = new rtShopPromotion();
  $i6->setTitle('Combinations');
  $i6->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",1))));
  $i6->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i6->setTotalFrom(100);
  $i6->setReductionType('percentageOff');
  $i6->setReductionValue(8);
  $i6->save();
  $i7 = new rtShopPromotion();
  $i7->setTitle('Combinations');
  $i7->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i7->setTotalTo(100);
  $i7->setReductionType('dollarOff');
  $i7->setReductionValue(15);
  $i7->save();
  $i8 = new rtShopPromotion();
  $i8->setTitle('Combinations');
  $i8->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",1))));
  $i8->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",3))));
  $i8->setTotalFrom(75);
  $i8->setReductionType('dollarOff');
  $i8->setReductionValue(100);
  $i8->save();
  $i9 = new rtShopPromotion();
  $i9->setTitle('Combinations');
  $i9->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",1))));
  $i9->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",3))));
  $i9->setTotalTo(100);
  $i9->setReductionType('dollarOff');
  $i9->setReductionValue(10);
  $i9->save();
  $i10 = new rtShopPromotion();
  $i10->setTitle('Combinations');
  $i10->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",1))));
  $i10->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",3))));
  $i10->setTotalFrom(120);
  $i10->setTotalTo(180);
  $i10->setReductionType('dollarOff');
  $i10->setReductionValue(10);
  $i10->save();
  $i11 = new rtShopPromotion();
  $i11->setTitle('Combinations');
  $i11->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",1))));
  $i11->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",3))));
  $i11->setTotalFrom(140);
  $i11->setTotalTo(200);
  $i11->setReductionType('dollarOff');
  $i11->setReductionValue(10);
  $i11->save();
  $i12 = new rtShopPromotion();
  $i12->setTitle('Combinations');
  $i12->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",1))));
  $i12->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",3))));
  $i12->setTotalFrom(200);
  $i12->setTotalTo(300);
  $i12->setReductionType('dollarOff');
  $i12->setReductionValue(25);
  $i12->save();
	$t->pass('->save() on a rtShopPromotion object works');
} catch (Exception $e) {
 	$t->fail('->save() on a rtShopPromotion failed!');
}

$t->diag('Get best promotion offer');

$order_total = 130;
$promotions = rtShopPromotionToolkit::getBest($order_total, date("Y-m-d H:i:s"));
$t->is(get_class($promotions), 'rtShopPromotion', '::getBest() Returns a rtShopPromotion object correctly.');
$t->is($promotions->getId(), $i8->getId(), '::getBest() Best offer found.');

$t->diag('Apply best promotion offer');

$promotion_total = rtShopPromotionToolkit::applyPromotion($order_total, date("Y-m-d H:i:s"));
$t->is($promotion_total, 30, '::applyPromotion() Applied best promotion correctly and returned new total.');

$t->diag('Get next best promotion offer');

$promotions = rtShopPromotionToolkit::getNextBest(130, date("Y-m-d H:i:s"));
$t->is(get_class($promotions), 'rtShopPromotion', '::getNextBest() Returns a rtShopPromotion object correctly.');
$t->is($promotions->getId(), $i11->getId(), '::getNextBest() Next best offer found.');

$t->diag('Get distance to next best promotion offer');

$order_total = 130;
$promotions = rtShopPromotionToolkit::getDistanceToNextBest($order_total, date("Y-m-d H:i:s"));
$t->is($promotions, $i11->getTotalFrom()-$order_total, '::getDistanceToNextBest() Distance to next best offer found.');

// Clean up
$i3->delete();
$i4->delete();
$i5->delete();
$i6->delete();
$i7->delete();
$i8->delete();
$i9->delete();
$i10->delete();
$i11->delete();
$i12->delete();
unset($i3,$i4,$i5,$i6,$i7,$i8,$i9,$i10,$i11,$i12);
unset($promotions);

$t->diag('**************************');
$t->diag('*** Promotion: Product ***');
$t->diag('**************************');

// Add vouchers data
try {
  rtShopPromotionTestTools::clean();

  // Add 3 vouchers
  $i3 = new rtShopPromotionProduct();
  $i3->setTitle('Voucher');
  $i3->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i3->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i3->setQuantityTo(20);
  $i3->setReductionType('dollarOff');
  $i3->setReductionValue(10);
  $i3->setMode('Group');
  $i3->setCount(100);
  $i3->save();
  $i4 = new rtShopPromotionProduct();
  $i4->setTitle('Voucher');
  $i4->setCode($code2);
  $i4->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i4->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i4->setQuantityFrom(10);
  $i4->setQuantityTo(30);
  $i4->setReductionType('percentageOff');
  $i4->setReductionValue(15);
  $i4->setMode('Single');
  $i4->setCount(1);
  $i4->save();
  $t->pass('->save() on a rtShopPromotionProduct object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopPromotionProduct failed!');
}

$o1 = Doctrine::getTable('rtShopPromotionProduct')->find($i3->getId());
$t->isa_ok($o1, 'rtShopPromotionProduct', '->retrieve() rtShopPromotionProduct object was saved to database and retrieved successfully');

$o2 = Doctrine::getTable('rtShopPromotionProduct')->findAvailable(8,NULL);
$t->is(count($o2), 1, '::findAvailable() retrieved 1 object successfully');

// Clean up
$i3->delete();
$i4->delete();
unset($o1,$o2);

$t->diag('***********************');
$t->diag('*** Promotion: Cart ***');
$t->diag('***********************');

// Add vouchers data
try {
  rtShopPromotionTestTools::clean();

  // Add 3 vouchers
  $i3 = new rtShopPromotionCart();
  $i3->setTitle('Voucher');
  $i3->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i3->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i3->setTotalFrom(150);
  $i3->setTotalTo(250);
  $i3->setReductionType('dollarOff');
  $i3->setReductionValue(10);
  $i3->setMode('Group');
  $i3->setCount(100);
  $i3->save();
  $i4 = new rtShopPromotionCart();
  $i4->setTitle('Voucher');
  $i4->setCode($code2);
  $i4->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i4->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i4->setTotalTo(150);
  $i4->setReductionType('percentageOff');
  $i4->setReductionValue(15);
  $i4->setMode('Single');
  $i4->setCount(1);
  $i4->save();
  $t->pass('->save() on a rtShopPromotionCart object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopPromotionCart failed!');
}

$o1 = Doctrine::getTable('rtShopPromotionCart')->find($i3->getId());
$t->isa_ok($o1, 'rtShopPromotionCart', '->retrieve() rtShopPromotionCart object was saved to database and retrieved successfully');

$o2 = Doctrine::getTable('rtShopPromotionCart')->findAvailable(70,NULL);
$t->is(count($o2), 1, '::findAvailable() retrieved 1 object successfully');

// Clean up
$i3->delete();
$i4->delete();
unset($o1,$o2);
/**
 * rtShopPromotionToolkit Class
 */
class rtShopPromotionTestTools
{
  /**
   * Make sure table is cleaned before testing
   */
  public static function clean()
  {
    $doctrine = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
    $doctrine->query('TRUNCATE table rt_shop_promotion');
    unset($doctrine);
  }
}