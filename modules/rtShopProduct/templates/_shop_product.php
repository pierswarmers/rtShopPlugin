<?php
use_helper('Number', 'Url', 'I18N', 'rtShopProduct');

$assets = $rt_shop_product->getAssets();

?>

<h1><?php echo $rt_shop_product->getTitle() ?></h1>

<p><?php echo price_for($rt_shop_product) ?></p>

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