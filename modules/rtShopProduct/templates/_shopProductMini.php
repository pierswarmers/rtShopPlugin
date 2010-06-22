<?php use_helper('I18N', 'rtShopProduct') ?>
<div class="rt-shop-product-mini">
  <div class="rt-shop-product-mini-image">
    <?php $promo_span = $rt_shop_product->isOnPromotion() ? '<span class="rt-shop-product-promotion">'.__('On Sale Now').'</span>' : ''; ?>
    <?php echo link_to($promo_span.image_tag(rtAssetToolkit::getThumbnailPath($rt_shop_product->getPrimaryImage() ? $rt_shop_product->getPrimaryImage()->getSystemPath() : '', array('maxHeight' => 200, 'maxWidth' => 160)), array('class' => 'primary-image')), 'rt_shop_product_show', $rt_shop_product) ?>
  </div>
  <div class="rt-shop-product-mini-details">
    <h3><?php echo link_to($rt_shop_product->getTitle(), 'rt_shop_product_show', $rt_shop_product) ?></h3>
    <p class="rt-shop-product-price"><?php echo price_for($rt_shop_product); ?></p>
  </div>
</div>