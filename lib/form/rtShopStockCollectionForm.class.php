<?php

/**
 * PluginrtShopAttribute form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rtShopStockCollectionForm extends BasertShopProductForm
{
  private $attributes;
  
  public function setup()
  {
    parent::setup();

    $newRows = $this->getOption('newRows', 1);

    $this->useFields(array('id'));

    $this->setWidget('newRows', new sfWidgetFormInputHidden(array('default' => '1')));
    $this->setValidator('newRows', new sfValidatorInteger());

    $form = new rtShopStockCollectionNewForm(null, array('product' => $this->getObject(), 'size' => $newRows));

    $this->embedForm('newStocks', $form);

    if(!$this->isNew())
    {
      $form = new rtShopStockCollectionCurrentForm(null, array('product' => $this->getObject()));
      $this->embedForm('currentStocks', $form);
    }

//    $this->embedRelation('rtShopStocks');
  }
  
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms)
    {
      $stocks = $this->getValue('newStocks');
      $forms = $this->embeddedForms;
      
      foreach ($this->embeddedForms['newStocks'] as $name => $form)
      {
        if (!isset($stocks[$name]))
        {
          unset($forms['newStocks'][$name]);
        }
      }

      if(isset($forms['currentStocks']))
      {
        $varations = $this->getValue('currentStocks');

        foreach ($varations as $name => $form)
        {
          if (isset($form['delete']) && $form['delete'])
          {
            $variation = Doctrine::getTable('rtShopStock')->find($form['id']);
            $variation->delete();
            unset($forms['currentStocks'][$name]);
          }
        }
      }
    }

    $this->saveRelatedAttributeSelections('currentStocks');
    $this->saveRelatedAttributeSelections('newStocks');

    return parent::saveEmbeddedForms($con, $forms);
  }

  public function getAttributes()
  {
    if(is_null($this->attributes))
    {
      $this->attributes = Doctrine::getTable('rtShopAttribute')->findByProductId($this->getObject()->getId());
    }
    return $this->attributes;
  }

  private function saveRelatedAttributeSelections($embed_token)
  {
    // Get the relevant form
    $forms = $this->embeddedForms;

    if(isset($forms[$embed_token]))
    {
      $stock_forms = $forms[$embed_token]->getEmbeddedForms();
      // Get the stock values from request
      $request_data = $this->getValue($embed_token);

      foreach ($request_data as $name => $form)
      {
        // Save form object
        if(!isset($stock_forms[$name]))
        {
          continue;
        }
        $stock_object = $stock_forms[$name]->getObject();
        $stock_object->save();

        $query = new Doctrine_Query();

        $query->delete('rtShopStockToVariation s2v')
              ->where('s2v.stock_id = ?', $stock_object->getId())
              ->execute();

//        echo $stock_object->getId();
//        exit;
        
        foreach($this->getAttributes() as $attribute)
        {
          $tmp_val = $form['rt_shop_variations_list_'.$attribute->getId()];
          $values[$tmp_val[0]] = $tmp_val[0];
        }
//        echo $stock_object->getId();
//        var_dump($values);
//        exit;
//
        $stock_object->link('rtShopVariations', array_values($values), true);
      }
    }
  }
}
