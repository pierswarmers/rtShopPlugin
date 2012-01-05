<?php

/**
 * PluginrtShopCategory form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginrtShopCategoryForm extends BasertShopCategoryForm
{
  public function setup()
  {
    parent::setup();
    
    unset($this['level'], $this['comment_status'], $this['lft'], $this['rgt'], $this['root_id']);

    // Query
    $query = Doctrine_Query::create()->from('rtShopProduct p')
             ->leftJoin('p.rtShopProductToCategory as ptc')
             ->andWhere('ptc.category_id = ?',$this->object->id);

    if(!$this->isNew())
    {
      $query->select(sprintf('
        p.id,
        p.title,
        (select position from rt_shop_product_to_category where category_id = %s and product_id = p.id) as position,
        ((select position from rt_shop_product_to_category where category_id = %s and product_id = p.id) IS NULL) as has_position
        ', $this->object->id, $this->object->id))
        ->orderBy('has_position ASC, position ASC');
    }
    else
    {
      $query->orderBy('p.title');
    }

    // Widgets
    $this->setWidget('rt_shop_products_list', new sfWidgetFormDoctrineChoice(array('query' => $query, 'expanded' => true ,'multiple' => true, 'model' => 'rtShopProduct')));

    // Labels
    $this->widgetSchema['rt_shop_products_list']->setLabel('Products');

    // Help texts
    $this->widgetSchema->setHelp('rt_shop_products_list', 'Optional features this product is defined by. Dragging up or down changes the display order.');
  }

  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $this->updateObject();

    $this->getObject()->save($con);

    // Embedded forms
    $this->saveEmbeddedForms($con);

    $this->savertShopProductsList($con);
  }

  public function savertShopProductsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['rt_shop_products_list']))
    {
      // Widget has been unset
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    Doctrine_Query::create()->from('rtShopProductToCategory ptc')
      ->andWhere('ptc.category_id = ?', $this->object->id)
      ->delete()
      ->execute();

    $values = $this->getValue('rt_shop_products_list');
    if (!is_array($values))
    {
      $values = array();
    }

    if(count($values))
    {
      $i = 0;
      foreach($values as $v)
      {
        $rt_product_to_category = new rtShopProductToCategory();
        $rt_product_to_category->setCategoryId($this->object->id);
        $rt_product_to_category->setProductId($v);
        $rt_product_to_category->setPosition($i);
        $rt_product_to_category->save();
        $i++;
      }
    }
  }
}
