<?php

/**
 * PluginrtShopProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginrtShopProductForm extends BasertShopProductForm
{
  public function setup()
  {
    parent::setup();

    unset($this['comment_status']);

    $query = Doctrine_Query::create()->from('rtShopAttribute a');

    if(!$this->isNew())
    {
      $query->leftJoin('a.rtShopProductToAttribute pta')
        ->select(sprintf('a.id, a.title, (pta.product_id = %s) as current, (pta.position IS NULL) as has_position, pta.attribute_id, pta.position', $this->object->id))
        ->groupBy('pta.product_id, pta.attribute_id')
        ->orderBy('current DESC, has_position ASC, pta.position ASC');
    }
    else
    {
      $query->orderBy('a.title');
    }

    $this->setWidget('rt_shop_attributes_list', new sfWidgetFormDoctrineChoice(array('query' => $query, 'expanded' => true ,'multiple' => true, 'model' => 'rtShopAttribute')));
    $this->setWidget('rt_shop_promotions_list', new sfWidgetFormDoctrineChoice(array('expanded' => true ,'multiple' => true, 'model' => 'rtShopPromotionProduct')));

    //$b = new sfWidgetFormSelectCheckbox();

    $query = Doctrine::getTable('rtShopCategory')->getOrderQuery();

    $this->setWidget('rt_shop_categories_list', new rtWidgetFormTreeDoctrineChoice(array('query' => $query, 'expanded' => true ,'multiple' => true, 'model' => 'rtShopCategory')));

    $this->widgetSchema['rt_shop_attributes_list']->setLabel('Attributes');
    $this->widgetSchema['rt_shop_categories_list']->setLabel('Categories');
    $this->widgetSchema['rt_shop_promotions_list']->setLabel('Promotions');

    $this->widgetSchema->setHelp('rt_shop_attributes_list', 'Optional features this product is defined by. Dragging up or down changes the display order.');
    $this->widgetSchema->setHelp('rt_shop_categories_list', 'One or more categories can be linked to this product.');
    $this->widgetSchema->setHelp('rt_shop_promotions_list', 'One or more product promotions can be linked to this product.');

    $this->widgetSchema->setHelp('is_featured', 'Mark this product as featured.');
    $this->widgetSchema->setHelp('backorder_allowed', 'Should the stock/inventory level management be disabled.');
    $this->widgetSchema->setHelp('is_taxable', 'Should the configured tax rate of '.sfConfig::get('app_rt_shop_tax_rate' , '0').'% percent be applied to this product.');
  }

  protected function doSave($con = null)
  {
    $this->savertShopCategoriesList($con);
    $this->savertShopPromotionsList($con);

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $this->updateObject();

    $this->getObject()->save($con);

    // embedded forms
    $this->saveEmbeddedForms($con);
    
    $this->savertShopAttributesList($con);
  }
  
  public function savertShopAttributesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['rt_shop_attributes_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    Doctrine_Query::create()->from('rtShopProductToAttribute pta')
      ->andWhere('pta.product_id = ?', $this->object->id)
      ->delete()
      ->execute();
    

    $values = $this->getValue('rt_shop_attributes_list');
    if (!is_array($values))
    {
      $values = array();
    }
    
    if (count($values))
    {
      $i = 0;
      foreach($values as $v)
      {
        $rt_product_to_attribute = new rtShopProductToAttribute();
        $rt_product_to_attribute->setProductId($this->object->id);
        $rt_product_to_attribute->setAttributeId($v);
        $rt_product_to_attribute->setPosition($i);
        $rt_product_to_attribute->save();
        $i++;
      }
    }
  }
}
