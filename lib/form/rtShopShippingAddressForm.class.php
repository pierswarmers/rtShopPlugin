<?php
/*
 * This file is part of the reditype package.
 * (c) 2009-2010 Piers Warmers <piers@wranglers.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * rtShopShippingAddressForm
 *
 * @package    rtShopPlugin
 * @subpackage forms
 * @author     Piers Warmers <piers@wranglers.com.au>
 * @author     Konny Zurcher <konny@wranglers.com.au>
 */
class rtShopShippingAddressForm extends BasertAddressForm
{
  public function setup()
  {
    parent::setup();

    unset(
      $this['model'],
      $this['model_id'],
      $this['type'],
      $this['created_at'],
      $this['updated_at']
    );

    $this->setWidget('type', new sfWidgetFormInputHidden());
    if(class_exists("sfWidgetFormI18nChoiceCountry"))
    {
      $this->widgetSchema["country"] = new sfWidgetFormI18nChoiceCountry(array('culture' => sfContext::getInstance()->getUser()->getCulture()));
    }
    else
    {
      $this->widgetSchema["country"] = new sfWidgetFormI18nSelectCountry(array('culture' => sfContext::getInstance()->getUser()->getCulture()));
    }
    $this->setDefault('country', sfConfig::get('app_rt_shop_default_country','AU'));

    $this->widgetSchema['care_of']->setLabel('Name');

    $this->setValidator('type', new sfValidatorChoice(array('choices' => array(0 => 'billing', 1 => 'shipping'), 'required' => true)));
    $this->setValidator('care_of', new sfValidatorString(array('max_length' => 255, 'required' => true), array('required' => 'Please provide a name')));
    $this->setValidator('address_1', new sfValidatorString(array('max_length' => 255, 'required' => true), array('required' => 'Please provide an address')));
    $this->setValidator('town', new sfValidatorString(array('max_length' => 255, 'required' => true), array('required' => 'Please provide a town')));
    $this->setValidator('state', new sfValidatorString(array('max_length' => 255, 'required' => true), array('required' => 'Please provide a state')));
    $this->setValidator('postcode', new sfValidatorInteger(array('required' => true), array('required' => 'Please provide a postcode')));

    $this->widgetSchema->setNameFormat('rt_address[%s]');
    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  public function getModelName()
  {
    return 'rtAddress';
  }}