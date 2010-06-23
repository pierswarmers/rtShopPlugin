<?php use_helper('I18N', 'Number') ?>
<p>
  <?php echo $sf_user->getAttribute('rt_shop_order_cart_items', 0) ?> Items in your cart -
  <?php echo format_currency($sf_user->getAttribute('rt_shop_order_cart_total', 0), sfConfig::get('app_rt_currency', 'USD')) ?> |
  <?php echo link_to(__('view cart'), 'rt_shop_order_cart') ?>
</p>