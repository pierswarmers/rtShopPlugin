<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate') ?>
<div class="rt-shop-product-show rt-admin-edit-tools-panel">
  <?php echo link_to(__('Edit'), 'rtShopProductAdmin/edit?id='.$rt_shop_product->getId(), array('class' => 'rt-admin-edit-tools-trigger')) ?>
  <?php include_partial('shop_product', array('rt_shop_product' => $rt_shop_product, 'sf_cache_key' => $rt_shop_product->getId())) ?>
</div>