<?php use_helper('I18N') ?>
<div class="rt-shop-product-mini">
  <h2><?php echo $rt_shop_product->getTitle() ?></h2>
  <?php echo image_tag(rtAssetToolkit::getThumbnailPath($rt_shop_product->getPrimaryImage() ? $rt_shop_product->getPrimaryImage()->getSystemPath() : '', array('maxHeight' => 150, 'maxWidth' => 100)), array('class' => 'primary-image')) ?>
  <?php echo markdown_to_html($rt_shop_product->getContent(), $rt_shop_product); ?>
</div>