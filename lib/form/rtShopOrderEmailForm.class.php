<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopOrderEmailFormclass
 *
 * @package    rtShopPlugin
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopOrderEmailForm extends BasertShopOrderForm
{
  public function setup()
  {
    parent::setup();

    $this->widgetSchema->setFormFormatterName('table');

    unset(
      $this['is_wholesale'],
      $this['reference'],
      $this['status'],
      $this['notes_user'],
      $this['notes_admin'],
      $this['user_id'],
      $this['created_at'],
      $this['updated_at'],
      $this['stocks_list'],
      $this['closed_shipping_rate'],
      $this['closed_taxes'],
      $this['closed_promotions'],
      $this['closed_products'],
      $this['closed_total']
    );
    
    $order_id = sfContext::getInstance()->getUser()->getAttribute('rt_shop_frontend_order_id');
    $order = Doctrine::getTable('rtShopOrder')->find($order_id);

    $this->widgetSchema->setLabel('email',"Email Address");
    $this->widgetSchema['email'] = new sfWidgetFormInput(array(), array('class'=>'text'));
    $this->setValidator('email', new sfValidatorEmail(array('max_length' => 255, 'required' => true), array('required' => 'Please provide a valid email address')));

    $this->widgetSchema->setNameFormat('rt_shop_order[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  public function getModelName()
  {
    return 'rtShopOrder';
  }}