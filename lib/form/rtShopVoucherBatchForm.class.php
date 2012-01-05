<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtBatchVoucherForm
 *
 * @package    reditype
 * @subpackage form
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopVoucherBatchForm extends BasertShopVoucherForm
{
  public function setup()
  {
    parent::setup();
    $this->widgetSchema['batchsize'] = new sfWidgetFormInput(array(), array('class'=>'text'));
    $this->setValidator('batchsize', new sfValidatorInteger(array('min' => 1,'max' => sfConfig::get('app_rt_voucher_batch_max', 10000), 'required' => true), array('required' => 'Please provide a batch size','min' => 'Minimum batch size is 1','max' => 'Maximum batch size is '.sfConfig::get('app_rt_voucher_batch_max', 10000))));
    $this->setDefault('batchsize', 1);
    $this->widgetSchema->setLabel('batchsize',"Batch size");
    $this->widgetSchema->setHelp('batchsize', 'The total number of unique vouchers to be created.');
    $this->setDefault('mode', 'Single');
  }
}