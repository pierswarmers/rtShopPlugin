<?php

/**
 * PluginrtShopStock form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginrtShopStockForm extends BasertShopStockForm
{
  protected $selected_variations = array();
  protected $attributes;
  
  public function setup()
  {
    parent::setup();


    $name_format = $this->getOption('name_format', 'rt_shop_stock[%s]');
    $this->widgetSchema->setNameFormat($name_format);

    $this->setWidget('quantity', new sfWidgetFormInputText(array(), array('class' => 'small-text')));
    $this->setDefault('quantity', '');
    $this->setWidget('product_id', new sfWidgetFormInputHidden());
    $this->setWidget('price_retail', new sfWidgetFormInputText(array(), array('class' => 'small-text')));
    $this->setWidget('price_promotion', new sfWidgetFormInputText(array(), array('class' => 'small-text')));
    $this->setWidget('price_wholesale', new sfWidgetFormInputText(array(), array('class' => 'small-text')));
    $this->setWidget('length', new sfWidgetFormInputText(array(), array('class' => 'small-text')));
    $this->setWidget('width', new sfWidgetFormInputText(array(), array('class' => 'small-text')));
    $this->setWidget('height', new sfWidgetFormInputText(array(), array('class' => 'small-text')));
    $this->setWidget('weight', new sfWidgetFormInputText(array(), array('class' => 'small-text')));
    $this->setWidget('sku', new sfWidgetFormInputText(array(), array('class' => 'small-text')));
    
    
    $this->setWidget('sku', new sfWidgetFormInputText(array(), array('class' => 'small-text')));



    $this->widgetSchema->moveField('quantity',sfWidgetFormSchema::FIRST);
    $this->widgetSchema->moveField('sku',sfWidgetFormSchema::AFTER, 'quantity');

    unset($this['rt_shop_variations_list'], $this['rt_shop_orders_list'], $this['uuid']);

    $this->setrtShopVariationsWidgets();

    $this->setWidget('delete', new sfWidgetFormInputCheckbox());
    $this->validatorSchema['delete'] = new sfValidatorBoolean();
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    $attributes = $this->getAttributes();

    foreach($attributes as $a)
    {
      if (isset($this->widgetSchema['rt_shop_variations_list_'.$a->getId()]))
      {
        $this->setDefault('rt_shop_variations_list_'.$a->getId(), $this->object->rtShopVariations->getPrimaryKeys());
      }
    }
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    parent::saveEmbeddedForms($con, $forms);
    //$this->saveVariationSelections();
  }

  public function saveVariationSelections()
  {
    return;
    $request = sfContext::getInstance()->getRequest();
    $full_data = $request->getParameter('rt_shop_product');

    foreach($full_data['currentStocks'] as $stock)
    {
      if($stock['id'] == $this->getObject()->getId())
      {
        $attributes = $this->getAttributes();

        $values = array();

        $query = new Doctrine_Query();

        $query->delete('rtShopStockToVariation s2v')
              ->where('s2v.stock_id = ?', $this->object->getId())
              ->execute();

        foreach($attributes as $a)
        {
          $tmp_val = $stock['rt_shop_variations_list_'.$a->getId()];
          $values[] = $tmp_val[0];
        }

        $this->object->link('rtShopVariations', array_values($values), true);
      }
    }
  }

  /**
   * Embed the linking form to stock variation selections. This logic is only triggered if
   * the parent rtShopStock object has an ID, i.e. isn't "new" and some attributes have
   * been configured with variations.
   */
  public function setrtShopVariationsWidgets()
  {
    $attributes = $this->getAttributes();

    foreach($attributes as $a)
    {
      $query = Doctrine::getTable('rtShopVariation')->createQuery('v');
      $query->where('v.attribute_id = ?', $a->getId());
      $this->setWidget('rt_shop_variations_list_'.$a->getId(), new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'rtShopVariation', 'query' => $query, 'add_empty' => true)));
      $this->setValidator('rt_shop_variations_list_'.$a->getId(), new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'rtShopVariation', 'required' => false)));
    }
  }

  /**
   * Return an array of attributes
   *
   * @return array
   */
  public function getAttributes()
  {
    if(is_null($this->attributes))
    {
      $this->attributes = Doctrine::getTable('rtShopAttribute')->findByProductId($this->getObject()->rtShopProduct->getId());
    }
    return $this->attributes;
  }



}
