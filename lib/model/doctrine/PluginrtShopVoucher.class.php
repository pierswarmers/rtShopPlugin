<?php

/**
 * PluginrtShopVoucher
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginrtShopVoucher extends BasertShopVoucher
{
  /**
   * Constructor
   *
   * @return void
   */
  public function construct ()
  {
    if($this->isNew()) {
      $this->setType('rtShopVoucher');
      $this->setCode(rtShopVoucherToolkit::generateVoucherCode());
    }
  }

  public function getTypeNice()
  {
    return 'Voucher';
  }


  public function getReductionValueFormatted()
  {
    if($this->getReductionType() === self::REDUCTION_TYPE_DOLLAR)
    {
      $numberFormat = new sfNumberFormat(sfContext::getInstance()->getUser()->getCulture());
      return $numberFormat->format($this->getReductionValue(), 'c', sfConfig::get('app_rt_shop_payment_currency','AUD'));
    }

    return $this->getReductionValue(). '%';
  }
  
  /**
   * Adjust voucher count
   *
   * @param Integer $deduction Count deduction value
   * @return Object
   */
  public function adjustCountBy($deduction)
  {
    $new_count = $this['count'] - $deduction;
    if ($new_count >= 0) {
      $this->_set('count', $new_count);
    } else {
      return $this;
    }
  }

  /**
   * Adjust reduction value
   *
   * @param Float $deduction Reduction value deduction
   * @return Object
   */
  public function adjustReductionValueBy($total)
  {
    $new_value = $this['reduction_value'] - $total;
    if ($new_value >= 0 && $this['reduction_value'] >= $total && $this['mode'] == 'Single' && $this['reduction_type'] == 'dollarOff') {
      $this->_set('reduction_value', $new_value);
    } else {
      return $this;
    }
  }
}