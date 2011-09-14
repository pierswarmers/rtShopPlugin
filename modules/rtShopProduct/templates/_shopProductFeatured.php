<?php use_helper('I18N', 'Text', 'rtShopProduct') ?>

<div class="rt-shop-product-featured rt-container rt-collection clearfix">
  <?php foreach($rt_shop_products as $rt_shop_product): ?>
    <?php include_partial('rtShopProduct/shopProductMini', array('rt_shop_product' => $rt_shop_product)); ?>
  <?php  endforeach; ?>
</div>