<?php

/**
 * PluginrtShopPromotion form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginrtShopPromotionForm extends BasertShopPromotionForm
{
  public function setup()
  {
    parent::setup();

    unset(
      $this['updated_at'],
      $this['created_at'],
      $this['quantity_from'],
      $this['quantity_to']
    );

    $rt_shop_promotion_types = array('rtShopPromotionCart' => 'Cart level promotion', 'rtShopPromotionProduct' => 'Product level promotion');

    // Widgets
    $this->setWidget('reduction_type', new sfWidgetFormChoice(array('choices' => array('percentageOff' => 'Percentage Off', 'dollarOff' => 'Value Off'))));
    $this->setWidget('type', new sfWidgetFormChoice(array('choices' => $rt_shop_promotion_types)));
    $this->setWidget('code', new sfWidgetFormInputHidden());

    // Help texts
    $this->widgetSchema->setHelp('type', 'Where will this promotion be applied - to the whole cart, or to a single product.');
    $this->widgetSchema->setHelp('stackable', 'Can this promotion be applied to line items with an existing promotion value.');
    $this->widgetSchema->setHelp('reduction_type', 'Refers to how the promotion will be applied. Either as a percentage or a value reduction.');
    $this->widgetSchema->setHelp('reduction_value', 'The value of the promotion - i.e. enter "50" for a 50% reduction, or 20 for a $20.00 reduction.');
    $this->widgetSchema->setHelp('date_from', 'Availabilty based on date.  Optional "from" and "to" values can be set.');
    $this->widgetSchema->setHelp('total_from', 'Availabilty restricted to cart values. Optional "from" and "to" values can be set.');

    // Validators
    $this->validatorSchema['title'] = new sfValidatorString(array('max_length' => 255, 'required' => true),array('required' => 'Please provide a title'));
    $this->validatorSchema['reduction_value'] = new sfValidatorNumber(array('required' => true), array('required' => 'Please provide a reduction value'));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('choices' => array_keys($rt_shop_promotion_types),'required' => false));

    $this->widgetSchema->setNameFormat('rt_shop_promotion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  public function getModelName()
  {
    return 'rtShopPromotion';
  }
}