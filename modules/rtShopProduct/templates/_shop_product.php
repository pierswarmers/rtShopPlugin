<?php
use_helper('Number', 'Url', 'I18N');

$assets = $rt_shop_product->getAssets();

$currency  = sfConfig::get('app_rt_currency', 'USD');

$price_str = ' - ';

//$price_min = $rt_shop_product->isOnPromotion() ? $rt_shop_product->getMinPromotionPrice() : $rt_shop_product->getMinRetailPrice();
//$price_max = max($rt_shop_product->getMaxRetailPrice(), $rt_shop_product->getMaxPromotionPrice());
//
//if($rt_shop_product->isOnPromotion())
//{
//  $price_str .=  '<span class="steer_shop_regular_price">' . format_currency($price_max, $currency) . '</span> ' . format_currency($price_min, $currency);
//}
//elseif($price_min !== $price_max)
//{
//  $price_str .=  format_currency($price_min, $currency) . ' &rarr; ' . format_currency($price_max, $currency);
//}
//else
//{
//  $price_str .=  format_currency($rt_shop_product->getMinRetailPrice(), $currency);
//}
?>

<h1><?php echo $rt_shop_product->getTitle() ?></h1>

<div class="rt-shop-product-content clearfix">
  <?php echo image_tag(rtAssetToolkit::getThumbnailPath($rt_shop_product->getPrimaryImage() ? $rt_shop_product->getPrimaryImage()->getSystemPath() : '', array('maxHeight' => 250, 'maxWidth' => 200)), array('class' => 'primary-image')) ?>
  <?php echo markdown_to_html($rt_shop_product->getContent(), $rt_shop_product); ?>
  <?php if($rt_shop_product->rtShopProducts): ?>
  <div class="rt-shop-product-collection">
  <?php foreach($rt_shop_product->rtShopProducts as $linked_rt_shop_product): ?>
    <?php include_component('rtShopProduct', 'shopProductMini', array('id' => $linked_rt_shop_product->getId())) ?>
  <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>