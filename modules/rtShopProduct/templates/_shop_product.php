<?php

use_helper('Number', 'Url', 'I18N', 'rtShopProduct');
use_stylesheet('/rtShopPlugin/css/main.css', 'last');

?>
<h1><?php echo $rt_shop_product->getTitle() ?></h1>

<div class="rt-container">
  <div class="rt-shop-product-gallery">
  <?php include_partial('rtShopProduct/gallery', array('rt_shop_product' => $rt_shop_product)) ?>
  </div>
  <div class="rt-shop-product-details">
    <?php include_partial('details_preffix', array('rt_shop_product' => $rt_shop_product)) ?>
    <p class="rt-shop-product-price"><?php echo price_for($rt_shop_product) ?></p>
    <?php echo markdown_to_html($rt_shop_product->getContent(), $rt_shop_product); ?>
    <?php include_partial('rtShopProduct/order_panel', array('rt_shop_product' => $rt_shop_product)) ?>
    <?php include_partial('details_suffix', array('rt_shop_product' => $rt_shop_product)) ?>
  </div>
</div>

<?php if($rt_shop_product->getRtShopProducts()->count() > 0): ?>
<h2><?php echo __('Related Products') ?></h2>
<div class="rt-container rt-collection">
  <?php $i=1; foreach($rt_shop_product->rtShopProducts as $linked_rt_shop_product): ?>
    <div class="rt-list-item rt-list-item-<?php echo $i ?>">
    <?php include_component('rtShopProduct', 'shopProductMini', array('id' => $linked_rt_shop_product->getId())) ?>
    </div>
  <?php $i++; endforeach; ?>
</div>
<?php endif; ?>