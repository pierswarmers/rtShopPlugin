<?php

/**
 * PluginrtShopAttribute form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginrtShopAttributeForm extends BasertShopAttributeForm
{
  public function setup()
  {
    parent::setup();

    unset($this['rt_shop_products_list']);

    $form = new rtShopVariationCollectionNewForm(null, array('attribute' => $this->getObject()));

    $this->embedForm('newVariations', $form);

    if(!$this->isNew())
    {
      $form = new rtShopVariationCollectionCurrentForm(null, array('attribute' => $this->getObject(), 'say' => 'hello'));
      $this->embedForm('currentVariations', $form);
    }

//    $this->embedRelation('rtShopVariations');
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms)
    {
      $forms = $this->embeddedForms;

      $varations = $this->getValue('newVariations');

      foreach ($this->embeddedForms['newVariations'] as $name => $form)
      {
        if (!isset($varations[$name]))
        {
          unset($forms['newVariations'][$name]);
        }
      }

      if(isset($forms['currentVariations']))
      {
        $varations = $this->getValue('currentVariations');

        foreach ($varations as $name => $form)
        {
          if (isset($form['delete']) && $form['delete'])
          {
            $variation = Doctrine::getTable('rtShopVariation')->find($form['id']);
            $variation->delete();
            unset($forms['currentVariations'][$name]);
          }
        }
      }
    }

    return parent::saveEmbeddedForms($con, $forms);
  }
}