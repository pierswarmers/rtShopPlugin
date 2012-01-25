<?php

use_helper('I18N', 'Number');

function price_for($rt_shop_product, $config = array())
{
  $format_was = '<span class="price-before">%s<em>%s</em></span>';
  $format_now = '<span class="price-now">%s<em>%s</em></span>';
  
  $config['format_was'] = isset($config['format_was']) ? $config['format_was'] : $format_was;
  $config['format_now'] = isset($config['format_now']) ? $config['format_now'] : $format_now;
  $config['format_now_preffix_from'] = isset($config['format_now_preffix_from']) ? $config['format_now_preffix_from'] : __('From') .' ';
  $config['format_now_preffix_only'] = isset($config['format_now_preffix_only']) ? $config['format_now_preffix_only'] : '';
  $config['format_now_preffix_now_only'] = isset($config['format_now_preffix_now_only']) ? $config['format_now_preffix_now_only'] : __('Now') .' ';
  $config['format_now_preffix_now_from_only'] = isset($config['format_now_preffix_now_from_only']) ? $config['format_now_preffix_now_from_only'] : __('Now from') .' ';
  $config['format_was_preffix'] = isset($config['format_was_preffix']) ? $config['format_was_preffix'] : __('Was') .' ';

  $currency  = sfConfig::get('app_rt_currency', 'USD');

  $price_min = $rt_shop_product->isOnPromotion() ? $rt_shop_product->getMinimumPrice() : $rt_shop_product->getMinRetailPrice();
  $price_max = max($rt_shop_product->getMaxRetailPrice(), $rt_shop_product->getMaxPromotionPrice());

  if(!$rt_shop_product->isOnPromotion())
  {
    return sprintf($config['format_now'], $price_min != $price_max ? $config['format_now_preffix_from'] : $config['format_now_preffix_only'], format_currency($price_min, $currency));
  }

  $string = '';

  $retail_prices_match = ($rt_shop_product->getMaxRetailPrice() == $rt_shop_product->getMinRetailPrice());
  $promo_prices_match =  ($rt_shop_product->getMaxPromotionPrice() == $rt_shop_product->getMinPromotionPrice());

  if($retail_prices_match)
  {
    $string = sprintf($config['format_was'], $config['format_was_preffix'], format_currency($price_max, $currency));
  }
  
  $string .= ' '. sprintf($config['format_now'], $promo_prices_match ? $config['format_now_preffix_now_only'] : $config['format_now_preffix_now_from_only'], format_currency($price_min, $currency));

  return $string;
}
