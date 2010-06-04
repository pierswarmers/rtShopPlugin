<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class rtShopNewVariationCollectionForm extends sfForm
{
  public function configure()
  {
    if (!$attribute = $this->getOption('attribute'))
    {
      throw new InvalidArgumentException('You must provide a attribute object.');
    }

    for ($i = 0; $i < $this->getOption('size', 5); $i++)
    {
      $variation = new rtShopVariation();
      $variation->rtShopAttribute = $attribute;

      $form = new rtShopVariationForm($variation);

      $this->embedForm($i, $form);
    }

    $this->mergePostValidator(new rtShopVariationValidatorSchema());
  }
}