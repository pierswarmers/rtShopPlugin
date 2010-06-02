<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate') ?>
<div class="rt-shop-product-show">
  <?php include_partial('shop_product', array('rt_shop_product' => $rt_shop_product, 'sf_cache_key' => $rt_shop_product->getId())) ?>
  <dl class="rt-meta-data">
    <dt><?php echo __('Created') ?>:</dt>
    <dd><?php echo time_ago_in_words_abbr($rt_shop_product->getCreatedAt(), $sf_user->getCulture()) ?></dd>
    <dt><?php echo __('Updated') ?>:</dt>
    <dd><?php echo time_ago_in_words_abbr($rt_shop_product->getUpdatedAt(), $sf_user->getCulture()) ?></dd>
    <dt><?php echo __('Version') ?>:</dt>
    <dd><?php echo $rt_shop_product->version ?></dd>
  </dl>
</div>