<?php

function price_for($rt_shop_product, $config = array())
{
  $config['between_string'] = isset($config['between_string']) ? $config['between_string'] : ' &rarr; ';
  $config['format'] = isset($config['between_string']) ? $config['between_string'] : ' &rarr; ';

  $currency  = sfConfig::get('app_rt_currency', 'USD');

  $price_str = ' - ';

  $price_min = $rt_shop_product->isOnPromotion() ? $rt_shop_product->getMinPromotionPrice() : $rt_shop_product->getMinRetailPrice();
  $price_max = max($rt_shop_product->getMaxRetailPrice(), $rt_shop_product->getMaxPromotionPrice());

  if($rt_shop_product->isOnPromotion())
  {
    $price_str .=  '<span class="rt_shop_regular_price">' . format_currency($price_max, $currency) . '</span> - ' . format_currency($price_min, $currency);
  }
  elseif($price_min !== $price_max)
  {
    $price_str .=  format_currency($price_min, $currency) . $config['between_string'] . format_currency($price_max, $currency);
  }
  else
  {
    $price_str .=  format_currency($rt_shop_product->getMinRetailPrice(), $currency);
  }

  return  $price_str;
}
