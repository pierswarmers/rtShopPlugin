<?php
/*
 * This file is part of the rtShopPlugin package.
 * (c) 2006-2008 digital Wranglers <steercms@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopOrderReportDateForm
 *
 * @package    rtShopPlugin
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopOrderReportDateForm extends sfForm
{
  public function setup()
  {
    parent::setup();

    $years = range(date('Y') - 15, date('Y'));

    $options = array(
      'format' => '%day% / %month% / %year%',
      'years' => array_combine($years, $years)
    );

    // Widgets
    $this->widgetSchema['date_from'] = new sfWidgetFormJQueryDate(array('config' => '{}', 'date_widget' => new sfWidgetFormDate($options)));
    $this->widgetSchema['date_to']   = new sfWidgetFormJQueryDate(array('config' => '{}', 'date_widget' => new sfWidgetFormDate($options)));

    // Add labels
    $this->widgetSchema->setLabel('date_from',"Date From:");
    $this->widgetSchema->setLabel('date_to',"Date To:");

    // Validators
    $this->setValidators(array(
      'date_from'   => new sfValidatorDate(array('required' => false), array()),
      'date_to'   => new sfValidatorDate(array('required' => false), array())
    ));

    $this->widgetSchema->setNameFormat('rt_shop_order_report[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }
}