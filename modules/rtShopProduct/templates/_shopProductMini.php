<?php use_helper('I18N', 'rtShopProduct') ?>
<div class="rt-shop-product-mini">
  <div>
    <?php echo link_to(image_tag(rtAssetToolkit::getThumbnailPath($rt_shop_product->getPrimaryImage() ? $rt_shop_product->getPrimaryImage()->getSystemPath() : '', array('maxHeight' => 160, 'maxWidth' => 140)), array('class' => 'primary-image')), 'rt_shop_product_show', $rt_shop_product) ?>
  </div>
  <p>
    <?php echo link_to($rt_shop_product->getTitle(), 'rt_shop_product_show', $rt_shop_product) ?><br/>
    <?php echo price_for($rt_shop_product); ?>
  </p>
</div>