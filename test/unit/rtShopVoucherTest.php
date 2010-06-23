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
 * $ cp plugins/rtShopPlugin/test/unit/rtShopVoucherTest.php test/unit/rtShopVoucherTest.php
 *
 */

/**
 * rtShopVoucher Testing
 *
 * @package    rtShopPlugin
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */

include(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(95, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);

new sfDatabaseManager($configuration);

$table = Doctrine::getTable('rtShopVoucher');

$t->diag('---------------------');
$t->diag('rtShopVoucher Testing');
$t->diag('---------------------');

$t->diag('*** General saving test ***');

$t->diag('1. Empty save should fail (title is required)');
try {
  $i1 = new rtShopVoucher();
  $i1->save();
  $t->fail('->save() should throw a rtShopException on fresh rtShopVouchers');
} catch (Exception $e) {
  $t->pass('->save() throws an Exception');
}

// Clean up
$i1->delete();
unset ($i1);

$t->diag('2. Enter voucher and retrieve it');
try {
  $i2 = new rtShopVoucher();
  $i2->setTitle('Voucher');
  $i2->setCode(rtShopVoucherToolkit::generateVoucherCode());
	$i2->save();
	$t->pass('->save() on a rtShopVoucher object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopVoucher failed!');
}

// Retrieve by pk
$o1 = Doctrine::getTable('rtShopVoucher')->find($i2->getId());
$t->isa_ok($o1, 'rtShopVoucher', '->retrieve() rtShopVoucher object was saved to database and retrieved successfully');

// Clean up
$i2->delete();
unset ($i2);
unset ($o1);

$t->diag('*************************');
$t->diag('*** Availability only ***');
$t->diag('*************************');

// Add vouchers data
try {
  rtShopVoucherTestTools::clean();

  // Add 3 vouchers
  $i3 = new rtShopVoucher();
  $i3->setTitle('Date only');
  $i3->setDateFrom(NULL);
  $i3->setDateTo(NULL);
  $i3->setReductionValue(10);
  $i3->save();
  $i4 = new rtShopVoucher();
  $i4->setTitle('Date only');
  $i4->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i4->setDateTo(NULL);
  $i4->setReductionValue(10);
  $i4->save();
  $i5 = new rtShopVoucher();
  $i5->setTitle('Date only');
  $i5->setDateFrom(NULL);
  $i5->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i5->setReductionValue(10);
  $i5->save();
  $i6 = new rtShopVoucher();
  $i6->setTitle('Date only');
  $i6->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i6->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i6->setReductionValue(10);
  $i6->save();
  $t->pass('->save() on a rtShopVoucher object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopVoucher failed!');
}

$t->diag('date_from <= date < date_to');

// Date
$o2 = Doctrine::getTable('rtShopVoucher')->getDateRestrictionQuery(date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s"))));
$t->is(count($o2), 4, '::getDateRestrictionQuery() retrieved 4 object successfully');

// Date
$o3 = Doctrine::getTable('rtShopVoucher')->getDateRestrictionQuery(date("Y-m-d H:i:s",strtotime(sprintf("-%s months",4))));
$t->is(count($o3), 2, '::getDateRestrictionQuery() retrieved 2 object successfully');

// Date
$o4 = Doctrine::getTable('rtShopVoucher')->getDateRestrictionQuery(date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
$t->is(count($o4), 2, '::getDateRestrictionQuery() retrieved 2 object successfully');

// Date
$o5 = Doctrine::getTable('rtShopVoucher')->getDateRestrictionQuery(NULL);
$t->is(count($o5), 4, '::getDateRestrictionQuery() retrieved 4 object successfully');

// Clean up
$i3->delete();
$i4->delete();
$i5->delete();
$i6->delete();
unset ($i3,$i4,$i5,$i6);
unset ($o2,$o3,$o4,$o5);

$t->diag('*******************************************************');
$t->diag('*** Combination: date_from/date_to and voucher code ***');
$t->diag('*******************************************************');

// Generate voucher codes
$code1 = rtShopVoucherToolkit::generateVoucherCode();
$code2 = rtShopVoucherToolkit::generateVoucherCode();
$code3 = rtShopVoucherToolkit::generateVoucherCode();
$code4 = rtShopVoucherToolkit::generateVoucherCode();
$code5 = rtShopVoucherToolkit::generateVoucherCode();
$code6 = rtShopVoucherToolkit::generateVoucherCode();
$code7 = rtShopVoucherToolkit::generateVoucherCode();
$code8 = rtShopVoucherToolkit::generateVoucherCode();

// Add vouchers data
$set_date_from = date("Y-m-d H:i:s");
$set_date_to = sprintf("+%s months",3);

// Add vouchers data
try {
  rtShopVoucherTestTools::clean();

  // Add 3 vouchers
  $i3 = new rtShopVoucher();
  $i3->setTitle('Voucher');
  $i3->setCode($code1);
  $i3->setReductionValue(10);
  $i3->save();
  $i4 = new rtShopVoucher();
  $i4->setTitle('Voucher');
  $i4->setCode($code2);
  $i4->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i4->setReductionValue(10);
  $i4->save();
  $i5 = new rtShopVoucher();
  $i5->setTitle('Voucher');
  $i5->setCode($code3);
  $i5->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i5->setReductionValue(10);
  $i5->save();
  $i6 = new rtShopVoucher();
  $i6->setTitle('Voucher');
  $i6->setCode($code4);
  $i6->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i6->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i6->setReductionValue(10);
  $i6->save();
  $i7 = new rtShopVoucher();
  $i7->setTitle('Voucher');
  $i7->setCode($code5);
  $i7->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i7->setReductionValue(10);
  $i7->save();
  $i8 = new rtShopVoucher();
  $i8->setTitle('Voucher');
  $i8->setCode($code6);
  $i8->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i8->setReductionValue(10);
  $i8->save();
  $i9 = new rtShopVoucher();
  $i9->setTitle('Voucher');
  $i9->setCode($code7);
  $i9->setDateFrom($set_date_from);
  $i9->setReductionValue(10);
  $i9->save();
  $i10 = new rtShopVoucher();
  $i10->setTitle('Voucher');
  $i10->setCode($code8);
  $i10->setDateTo(date('Y-m-d H:i:s',strtotime($set_date_to)));
  $i10->setReductionValue(10);
  $i10->save();
  $t->pass('->save() on a rtShopVoucher object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopVoucher failed!');
}

$t->diag('Date < date_from');
$o2 = Doctrine::getTable('rtShopVoucher')->findValid($code1,180,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
$t->is(count($o2), 1, '::findValid() with [code1] and [Date < date_from] retrieved 1 object successfully');
$o3 = Doctrine::getTable('rtShopVoucher')->findValid($code2,180,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
$t->is(count($o3), 1, '::findValid() with [code2] and [Date < date_from] retrieved 1 object successfully');
$o4 = Doctrine::getTable('rtShopVoucher')->findValid($code3,180,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
$t->is(count($o4), 1, '::findValid() with [code3] and [Date < date_from] retrieved 1 object successfully');
$o5 = Doctrine::getTable('rtShopVoucher')->findValid($code4,180,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
$t->is(count($o5), 1, '::findValid() with [code4] and [Date < date_from] retrieved 1 object successfully');
$o6 = Doctrine::getTable('rtShopVoucher')->findValid($code5,180,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
$t->is(count($o6), 1, '::findValid() with [code5] and [Date < date_from] retrieved 1 object successfully');
$o7 = Doctrine::getTable('rtShopVoucher')->findValid($code6,180,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
$t->is(count($o7), 1, '::findValid() with [code6] and [Date < date_from] retrieved 1 object successfully');
$o8 = Doctrine::getTable('rtShopVoucher')->findValid($code7,180,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
$t->is(count($o8), 0, '::findValid() with [code7] and [Date < date_from] retrieved 0 object successfully');
$o9 = Doctrine::getTable('rtShopVoucher')->findValid($code8,180,date("Y-m-d H:i:s",strtotime(sprintf("-%s months",2))));
$t->is(count($o9), 1, '::findValid() with [code8] and [Date < date_from] retrieved 1 object successfully');

unset ($o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9);

$t->diag('date_from <= Date < date_to');
$o2 = Doctrine::getTable('rtShopVoucher')->findValid($code1,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
$t->is(count($o2), 1, '::findValid() with [code1] and [date_from <= Date < date_to] retrieved 1 object successfully');
$o3 = Doctrine::getTable('rtShopVoucher')->findValid($code2,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
$t->is(count($o3), 1, '::findValid() with [code2] and [date_from <= Date < date_to] retrieved 1 object successfully');
$o4 = Doctrine::getTable('rtShopVoucher')->findValid($code3,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
$t->is(count($o4), 0, '::findValid() with [code3] and [date_from <= Date < date_to] retrieved 0 object successfully');
$o5 = Doctrine::getTable('rtShopVoucher')->findValid($code4,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
$t->is(count($o5), 0, '::findValid() with [code4] and [date_from <= Date < date_to] retrieved 0 object successfully');
$o6 = Doctrine::getTable('rtShopVoucher')->findValid($code5,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
$t->is(count($o6), 1, '::findValid() with [code5] and [date_from <= Date < date_to] retrieved 1 object successfully');
$o7 = Doctrine::getTable('rtShopVoucher')->findValid($code6,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
$t->is(count($o7), 0, '::findValid() with [code6] and [date_from <= Date < date_to] retrieved 0 object successfully');
$o8 = Doctrine::getTable('rtShopVoucher')->findValid($code7,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
$t->is(count($o8), 1, '::findValid() with [code7] and [date_from <= Date < date_to] retrieved 1 object successfully');
$o9 = Doctrine::getTable('rtShopVoucher')->findValid($code8,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",2))));
$t->is(count($o9), 1, '::findValid() with [code8] and [date_from <= Date < date_to] retrieved 1 object successfully');

unset ($o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9);

$t->diag('Date > date_to');
$o2 = Doctrine::getTable('rtShopVoucher')->findValid($code1,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
$t->is(count($o2), 1, '::findValid() with [code1] and [Date > date_to] retrieved 1 object successfully');
$o3 = Doctrine::getTable('rtShopVoucher')->findValid($code2,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
$t->is(count($o3), 1, '::findValid() with [code2] and [Date > date_to] retrieved 1 object successfully');
$o4 = Doctrine::getTable('rtShopVoucher')->findValid($code3,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
$t->is(count($o4), 0, '::findValid() with [code3] and [Date > date_to] retrieved 0 object successfully');
$o5 = Doctrine::getTable('rtShopVoucher')->findValid($code4,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
$t->is(count($o5), 0, '::findValid() with [code4] and [Date > date_to] retrieved 0 object successfully');
$o6 = Doctrine::getTable('rtShopVoucher')->findValid($code5,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
$t->is(count($o6), 1, '::findValid() with [code5] and [Date > date_to] retrieved 1 object successfully');
$o7 = Doctrine::getTable('rtShopVoucher')->findValid($code6,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
$t->is(count($o7), 0, '::findValid() with [code6] and [Date > date_to] retrieved 0 object successfully');
$o8 = Doctrine::getTable('rtShopVoucher')->findValid($code7,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
$t->is(count($o8), 1, '::findValid() with [code7] and [Date > date_to] retrieved 1 object successfully');
$o9 = Doctrine::getTable('rtShopVoucher')->findValid($code8,180,date("Y-m-d H:i:s",strtotime(sprintf("+%s months",4))));
$t->is(count($o9), 0, '::findValid() with [code8] and [Date > date_to] retrieved 0 object successfully');

unset ($o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9);

$t->diag('Date = date_from');
$o2 = Doctrine::getTable('rtShopVoucher')->findValid($code1,180,date("Y-m-d H:i:s",strtotime($set_date_from)));
$t->is(count($o2), 1, '::findValid() with [code1] and [Date = date_from] retrieved 1 object successfully');
$o3 = Doctrine::getTable('rtShopVoucher')->findValid($code2,180,date("Y-m-d H:i:s",strtotime($set_date_from)));
$t->is(count($o3), 1, '::findValid() with [code2] and [Date = date_from] retrieved 1 object successfully');
$o4 = Doctrine::getTable('rtShopVoucher')->findValid($code3,180,date("Y-m-d H:i:s",strtotime($set_date_from)));
$t->is(count($o4), 1, '::findValid() with [code3] and [Date = date_from] retrieved 1 object successfully');
$o5 = Doctrine::getTable('rtShopVoucher')->findValid($code4,180,date("Y-m-d H:i:s",strtotime($set_date_from)));
$t->is(count($o5), 1, '::findValid() with [code4] and [Date = date_from] retrieved 1 object successfully');
$o6 = Doctrine::getTable('rtShopVoucher')->findValid($code5,180,date("Y-m-d H:i:s",strtotime($set_date_from)));
$t->is(count($o6), 1, '::findValid() with [code5] and [Date = date_from] retrieved 1 object successfully');
$o7 = Doctrine::getTable('rtShopVoucher')->findValid($code6,180,date("Y-m-d H:i:s",strtotime($set_date_from)));
$t->is(count($o7), 1, '::findValid() with [code6] and [Date = date_from] retrieved 1 object successfully');
$o8 = Doctrine::getTable('rtShopVoucher')->findValid($code7,180,date("Y-m-d H:i:s",strtotime($set_date_from)));
$t->is(count($o8), 1, '::findValid() with [code7] and [Date = date_from] retrieved 1 object successfully');
$o9 = Doctrine::getTable('rtShopVoucher')->findValid($code8,180,date("Y-m-d H:i:s",strtotime($set_date_from)));
$t->is(count($o9), 1, '::findValid() with [code8] and [Date = date_from] retrieved 1 object successfully');

unset ($o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9);

$t->diag('Date = date_to');
$o2 = Doctrine::getTable('rtShopVoucher')->findValid($code1,180,date("Y-m-d H:i:s",strtotime($set_date_to)));
$t->is(count($o2), 1, '::findValid() with [code1] and [Date = date_to] retrieved 1 object successfully');
$o3 = Doctrine::getTable('rtShopVoucher')->findValid($code2,180,date("Y-m-d H:i:s",strtotime($set_date_to)));
$t->is(count($o3), 1, '::findValid() with [code2] and [Date = date_to] retrieved 1 object successfully');
$o4 = Doctrine::getTable('rtShopVoucher')->findValid($code3,180,date("Y-m-d H:i:s",strtotime($set_date_to)));
$t->is(count($o4), 0, '::findValid() with [code3] and [Date = date_to] retrieved 0 object successfully');
$o5 = Doctrine::getTable('rtShopVoucher')->findValid($code4,180,date("Y-m-d H:i:s",strtotime($set_date_to)));
$t->is(count($o5), 0, '::findValid() with [code4] and [Date = date_to] retrieved 0 object successfully');
$o6 = Doctrine::getTable('rtShopVoucher')->findValid($code5,180,date("Y-m-d H:i:s",strtotime($set_date_to)));
$t->is(count($o6), 1, '::findValid() with [code5] and [Date = date_to] retrieved 1 object successfully');
$o7 = Doctrine::getTable('rtShopVoucher')->findValid($code6,180,date("Y-m-d H:i:s",strtotime($set_date_to)));
$t->is(count($o7), 0, '::findValid() with [code6] and [Date = date_to] retrieved 0 object successfully');
$o8 = Doctrine::getTable('rtShopVoucher')->findValid($code7,180,date("Y-m-d H:i:s",strtotime($set_date_to)));
$t->is(count($o8), 1, '::findValid() with [code7] and [Date = date_to] retrieved 1 object successfully');
$o9 = Doctrine::getTable('rtShopVoucher')->findValid($code8,180,date("Y-m-d H:i:s",strtotime($set_date_to)));
$t->is(count($o9), 0, '::findValid() with [code8] and [Date = date_to] retrieved 0 object successfully');

unset ($o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9);

$t->diag('No date = now');
$o2 = Doctrine::getTable('rtShopVoucher')->findValid($code1,180);
$t->is(count($o2), 1, '::findValid() with [code1] retrieved 1 object successfully');
$o3 = Doctrine::getTable('rtShopVoucher')->findValid($code2,180);
$t->is(count($o3), 1, '::findValid() with [code2] retrieved 1 object successfully');
$o4 = Doctrine::getTable('rtShopVoucher')->findValid($code3,180);
$t->is(count($o4), 1, '::findValid() with [code3] retrieved 1 object successfully');
$o5 = Doctrine::getTable('rtShopVoucher')->findValid($code4,180);
$t->is(count($o5), 1, '::findValid() with [code4] retrieved 1 object successfully');
$o6 = Doctrine::getTable('rtShopVoucher')->findValid($code5,180);
$t->is(count($o6), 1, '::findValid() with [code5] retrieved 1 object successfully');
$o7 = Doctrine::getTable('rtShopVoucher')->findValid($code6,180);
$t->is(count($o7), 1, '::findValid() with [code6] retrieved 1 object successfully');
$o8 = Doctrine::getTable('rtShopVoucher')->findValid($code7,180);
$t->is(count($o8), 1, '::findValid() with [code7] retrieved 1 object successfully');
$o9 = Doctrine::getTable('rtShopVoucher')->findValid($code8,180);
$t->is(count($o9), 1, '::findValid() with [code8] retrieved 1 object successfully');

unset ($o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9);

$t->diag('*****************************************************');
$t->diag('*** Voucher: Find applicable based on date + code ***');
$t->diag('*****************************************************');

$t->diag('Ask for non-existing voucher');

$voucher = rtShopVoucherToolkit::getApplicable($code3,180,date('Y-m-d H:i:s',strtotime(sprintf("+%s months",5))));
$t->is($voucher, false, '::getApplicable() Found no applicable voucher with provided date/code.');

$t->diag('Get applicable voucher');

$voucher = rtShopVoucherToolkit::getApplicable($code1,180,date("Y-m-d H:i:s"));
$t->is(get_class($voucher), 'rtShopVoucher', '::getApplicable() Returns a rtShopVoucher object correctly.');
$t->is($voucher->getId(), $i3->getId(), '::getApplicable() Found applicable voucher.');

// Clean up
$i3->delete();
$i4->delete();
$i5->delete();
$i6->delete();
$i7->delete();
$i8->delete();
$i9->delete();
$i10->delete();
unset($voucher,$set_date_from,$set_date_to);
unset($i3,$i4,$i5,$i6,$i7,$i8,$i9,$i10);

$t->diag('**********************************');
$t->diag('*** Voucher: Count adjustments ***');
$t->diag('**********************************');

// Add vouchers data
try {
  rtShopVoucherTestTools::clean();

  // Add 3 vouchers
  $i3 = new rtShopVoucher();
  $i3->setTitle('Voucher');
  $i3->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i3->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i3->setTotalFrom(150);
  $i3->setTotalTo(250);
  $i3->setCode($code1);
  $i3->setReductionType('dollarOff');
  $i3->setReductionValue(10);
  $i3->setMode('Group');
  $i3->setCount(100);
  $i3->save();
  $i4 = new rtShopVoucher();
  $i4->setTitle('Voucher');
  $i4->setCode($code2);
  $i4->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i4->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i4->setTotalFrom(0);
  $i4->setTotalTo(150);
  $i4->setCode($code2);
  $i4->setReductionType('percentageOff');
  $i4->setReductionValue(15);
  $i4->setMode('Group');
  $i4->setCount(10);
  $i4->save();
  $i5 = new rtShopVoucher();
  $i5->setTitle('Voucher');
  $i5->setCode($code3);
  $i5->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i5->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i5->setTotalFrom(250);
  $i5->setTotalTo(500);
  $i5->setCode($code3);
  $i5->setReductionType('percentageOff');
  $i5->setReductionValue(20);
  $i5->setMode('Group');
  $i5->setCount(0);
  $i5->save();
  $i6 = new rtShopVoucher();
  $i6->setTitle('Voucher');
  $i6->setCode($code4);
  $i6->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i6->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i6->setTotalFrom(0);
  $i6->setTotalTo(150);
  $i6->setReductionType('dollarOff');
  $i6->setReductionValue(60);
  $i6->setMode('Single');
  $i6->setCount(1);
  $i6->save();
  $i7 = new rtShopVoucher();
  $i7->setTitle('Voucher');
  $i7->setCode($code5);
  $i7->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i7->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i7->setTotalFrom(150);
  $i7->setTotalTo(250);
  $i7->setReductionType('percentageOff');
  $i7->setReductionValue(12);
  $i7->setMode('Single');
  $i7->setCount(1);
  $i7->save();
  $i8 = new rtShopVoucher();
  $i8->setTitle('Voucher');
  $i8->setCode($code6);
  $i8->setDateFrom(date('Y-m-d H:i:s',strtotime(sprintf("-%s months",3))));
  $i8->setDateTo(date('Y-m-d H:i:s',strtotime(sprintf("+%s months",1))));
  $i8->setTotalFrom(250);
  $i8->setTotalTo(400);
  $i8->setReductionType('percentageOff');
  $i8->setReductionValue(12);
  $i8->setMode('Single');
  $i8->setCount(0);
  $i8->save();
  $t->pass('->save() on a rtShopVoucher object works');
} catch (Exception $e) {
  $t->fail('->save() on a rtShopVoucher failed!');
}

$t->diag('Get applicable voucher based on count, total and date');

$voucher = rtShopVoucherToolkit::getApplicable($code4,130,date("Y-m-d H:i:s"));
$t->is(get_class($voucher), 'rtShopVoucher', '::getApplicable() Returns a rtShopVoucher object correctly.');
$t->is($voucher->getId(), $i6->getId(), '::getApplicable() Found applicable voucher.');

$t->diag('Set count of single voucher from 1 to 0');

$t->is($voucher->getCount(), $i6->getCount(), '::getApplicable() Returned original count 1 is corect.');
$voucher->adjustCountBy(1);
$t->is($voucher->getCount(), 0, '::getCount() Returned new count 0 is corect.');

$t->diag('Set count of group voucher from 10 to 9');

$voucher = rtShopVoucherToolkit::getApplicable($code2,130,date("Y-m-d H:i:s"));
$t->is($voucher->getCount(), $i4->getCount(), '::getCount() Returned original count 10 is corect.');
$voucher->adjustCountBy(1);
$t->is($voucher->getCount(), 9, '::getCount() Returned new count 9 is corect.');

$t->diag('**********************************');
$t->diag('*** Voucher: Value adjustments ***');
$t->diag('**********************************');

$t->diag('Voucher type: Single, Reduction_type: dollarOff and reduction_value > order total');

$order_total = 40;
$voucher = rtShopVoucherToolkit::getApplicable($code4,$order_total,date("Y-m-d H:i:s"));
$t->is(get_class($voucher), 'rtShopVoucher', '::getApplicable() Returns a rtShopVoucher object correctly.');
$t->is($voucher->getId(), $i6->getId(), '::getApplicable() Found applicable voucher.');

$t->is($voucher->getMode(), 'Single', '::getMode() Voucher type is Single.');
$t->is($voucher->getReductionType(), 'dollarOff', '::getReductionType() Voucher reduction_type is dollarOff.');
$t->is($voucher->getReductionValue() >= $order_total, True, '::getReductionValue() Voucher value is greater/equal to order total.');
$voucher->adjustReductionValueBy($order_total);
$t->is($voucher->getReductionValue(), 20, '::getReductionValue() New reduction value is correct.');

$t->diag('Voucher type: Single, Reduction_type: dollarOff and reduction_value = order total');

$order_total = 60;
$voucher = rtShopVoucherToolkit::getApplicable($code4,$order_total,date("Y-m-d H:i:s"));
$t->is(get_class($voucher), 'rtShopVoucher', '::getApplicable() Returns a rtShopVoucher object correctly.');
$t->is($voucher->getId(), $i6->getId(), '::getApplicable() Found applicable voucher.');

$t->is($voucher->getMode(), 'Single', '::getMode() Voucher type is Single.');
$t->is($voucher->getReductionType(), 'dollarOff', '::getReductionType() Voucher reduction_type is dollarOff.');
$t->is($voucher->getReductionValue() >= $order_total, True, '::getReductionValue() Voucher value is greater/equal to order total.');
$voucher->adjustReductionValueBy($order_total);
$t->is($voucher->getReductionValue(), 0, '::getReductionValue() New reduction value ZERO is correct.');

$t->diag('Voucher type: Single, Reduction_type: percentageOff');

$order_total = 180;
$voucher = rtShopVoucherToolkit::getApplicable($code5,$order_total,date("Y-m-d H:i:s"));
$t->is(get_class($voucher), 'rtShopVoucher', '::getApplicable() Returns a rtShopVoucher object correctly.');
$t->is($voucher->getId(), $i7->getId(), '::getApplicable() Found applicable voucher.');

$t->is($voucher->getMode(), 'Single', '::getMode() Voucher type is Single.');
$t->is($voucher->getReductionType(), 'percentageOff', '::getReductionType() Voucher reduction_type is percentageOff.');
$voucher->adjustReductionValueBy($order_total);
$t->is($voucher->getReductionValue(), 12, '::getReductionValue() Untouched reduction_value is correct.');

$t->diag('Voucher type: Group, Reduction_type: dollarOff');

$order_total = 180;
$voucher = rtShopVoucherToolkit::getApplicable($code1,$order_total,date("Y-m-d H:i:s"));
$t->is(get_class($voucher), 'rtShopVoucher', '::getApplicable() Returns a rtShopVoucher object correctly.');
$t->is($voucher->getId(), $i3->getId(), '::getApplicable() Found applicable voucher.');

$t->is($voucher->getMode(), 'Group', '::getMode() Voucher type is Group.');
$t->is($voucher->getReductionType(), 'dollarOff', '::getReductionType() Voucher reduction_type is dollarOff.');
$voucher->adjustReductionValueBy($order_total);
$t->is($voucher->getReductionValue(), 10, '::getReductionValue() Untouched reduction_value is correct.');

$t->diag('Voucher type: Group, Reduction_type: percentageOff');

$order_total = 130;
$voucher = rtShopVoucherToolkit::getApplicable($code2,$order_total,date("Y-m-d H:i:s"));
$t->is(get_class($voucher), 'rtShopVoucher', '::getApplicable() Returns a rtShopVoucher object correctly.');
$t->is($voucher->getId(), $i4->getId(), '::getApplicable() Found applicable voucher.');

$t->is($voucher->getMode(), 'Group', '::getMode() Voucher type is Group.');
$t->is($voucher->getReductionType(), 'percentageOff', '::getReductionType() Voucher reduction_type is percentageOff.');
$voucher->adjustReductionValueBy($order_total);
$t->is($voucher->getReductionValue(), 15, '::getReductionValue() Untouched reduction_value is correct.');

$t->diag('Apply voucher to order total');

$order_total = 100;
$voucher_total = rtShopVoucherToolkit::applyVoucher($code4,$order_total,date("Y-m-d H:i:s"));
$t->is($voucher_total, 40, '::applyVoucher() Applied voucher correctly and returned new total.');

// Clean up
$i3->delete();
$i4->delete();
$i5->delete();
$i6->delete();
$i7->delete();
$i8->delete();
unset($voucher);
unset($i3,$i4,$i5,$i6,$i7,$i8);

/**
 * rtShopVoucherToolkit Class
 */
class rtShopVoucherTestTools
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