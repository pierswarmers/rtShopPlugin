<?php use_helper('I18N', 'Text', 'rtShopProduct') ?>
<?php

$title = truncate_text (
        str_replace('&quot;', '"', $rt_shop_product->getTitle()),
        sfConfig::get('rt_shop_product_title_truncate_length', 29),
        sfConfig::get('rt_shop_product_title_truncate_string', '...'),
        true
        );

$title = str_replace('"', '&quot;', $title);

?>
<div class="rt-shop-product-mini rt-admin-edit-tools-panel rt-admin-edit-tools-panel-small rt-admin-edit-tools-panel-left">
  <?php echo link_to(__('Edit'), 'rtShopProductAdmin/edit?id='.$rt_shop_product->getId(), array('class' => 'rt-admin-edit-tools-trigger')) ?>
  <div class="rt-shop-product-mini-image">
    <?php $promo_span = $rt_shop_product->isOnPromotion() ? '<span class="rt-shop-product-promotion">'.__('On Sale Now').'</span>' : ''; ?>
    <?php echo link_to($promo_span.image_tag(rtAssetToolkit::getThumbnailPath($rt_shop_product->getPrimaryImage() ? $rt_shop_product->getPrimaryImage()->getSystemPath() : '', array('maxHeight' => 200, 'maxWidth' => 160)), array('class' => 'primary-image')), 'rt_shop_product_show', $rt_shop_product) ?>
  </div>
  <div class="rt-shop-product-mini-details">
    <h3><?php echo link_to($title, 'rt_shop_product_show', $rt_shop_product) ?></h3>
    <p class="rt-shop-product-price"><?php echo price_for($rt_shop_product); ?></p>
  </div>
</div>