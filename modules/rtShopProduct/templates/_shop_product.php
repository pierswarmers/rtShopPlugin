<h1><?php echo $rt_shop_product->getTitle() ?></h1>

<div class="rt-shop-product-content clearfix">
<?php echo markdown_to_html($rt_shop_product->getContent(), $rt_shop_product); ?>
</div>