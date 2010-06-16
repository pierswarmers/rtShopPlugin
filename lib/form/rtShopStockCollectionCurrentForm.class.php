<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class rtShopStockCollectionCurrentForm extends sfForm
{
  public function configure()
  {
    $product = $this->getOption('product');
    
    if (!$product || $product->isNew())
    {
      throw new InvalidArgumentException('You must provide a product object and it can\'t be "new".');
    }

    $i = 0;

    $stocks = $product->rtShopStocks;

    if($stocks)
    {
      foreach ($stocks as $stock)
      {
        $form = new rtShopStockForm($stock);
        $this->embedForm($i, $form);
        $i++;
      }
    }

    //$this->mergePostValidator(new rtShopStockValidatorSchema(null, array('attributes' => $product->rtShopAttributes)));
  }

}