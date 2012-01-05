<?php use_helper('I18N', 'Number') ?>
<p>
  <span class="rt-shop-cart-items"><?php echo $sf_user->getAttribute('rt_shop_order_cart_items', 0) ?></span>

  Items  in your  <?php echo sfConfig::get('rt_shop_cart_name', 'shopping cart') ?> -

  <span class="rt-shop-cart-total"><?php echo format_currency($sf_user->getAttribute('rt_shop_order_cart_total', 0), sfConfig::get('app_rt_currency', 'USD')) ?></span>
  
  <?php echo link_to(__('view ' . sfConfig::get('rt_shop_cart_name', 'shopping cart')), 'rt_shop_order_cart') ?>
</p>