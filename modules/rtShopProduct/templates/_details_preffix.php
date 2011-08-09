<?php use_helper('I18N', 'Date', 'rtText') ?>

<h2>
  Price: <?php echo price_for($rt_shop_product) ?>

  <span class="sku"><code>#<?php echo $rt_shop_product->getSku() ?></code></span>
</h2>

<div class="description"><?php echo markdown_to_html($rt_shop_product->getContent(), $rt_shop_product); ?></div>