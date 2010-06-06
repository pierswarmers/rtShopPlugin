<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class rtShopVariationCollectionCurrentForm extends sfForm
{
  public function configure()
  {
    $attribute = $this->getOption('attribute');
    
    if (!$attribute || $attribute->isNew())
    {
      throw new InvalidArgumentException('You must provide a attribute object and it can\'t be "new".');
    }

    $i = 0;

    $variations = Doctrine::getTable('rtShopVariation')->findByAttributeId($attribute->id);

    if($variations)
    {
      foreach ($variations as $variation)
      {
        $form = new rtShopVariationForm($variation);
        $this->embedForm($i, $form);
        $i++;
      }
    }
  }
}