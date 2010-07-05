<p class="rt-shop-product-price"><?php echo price_for($rt_shop_product) ?></p>
<p class="rt-shop-product-sku"><?php echo __('Code') ?>: <span><?php echo $rt_shop_product->getSku() ?></span></p>
<?php echo markdown_to_html($rt_shop_product->getContent(), $rt_shop_product); ?>