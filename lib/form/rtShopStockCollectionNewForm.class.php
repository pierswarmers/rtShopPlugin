<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class rtShopStockCollectionNewForm extends sfForm
{
  public function configure()
  {
    if (!$product = $this->getOption('product'))
    {
      throw new InvalidArgumentException('You must provide a product object.');
    }

    for ($i = 0; $i < $this->getOption('size', 5); $i++)
    {
      $stock = new rtShopStock();
      $stock->rtShopProduct = $product;

      $form = new rtShopStockForm($stock);

      $this->embedForm($i, $form);
    }

    $this->mergePostValidator(new rtShopStockValidatorSchema(null, array('attributes' => $product->rtShopAttributes)));
  }
}