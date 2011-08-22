<?php use_helper('I18N', 'Text', 'rtShopProduct') ?>
<?php

$title = truncate_text (
        str_replace('&quot;', '"', $rt_shop_product->getTitle()),
        sfConfig::get('rt_shop_product_title_truncate_length', 29),
        sfConfig::get('rt_shop_product_title_truncate_string', '...'),
        true
        );

$title = str_replace('"', '&quot;', $title);

$mode = isset($mode) ? $mode : '';

?>
<div class="rt-shop-product-mini">
  <!--RTAS
  <div class="rt-admin-tools">
    <?php echo link_to(__('Edit Product'), 'rtShopProductAdmin/edit?id='.$rt_shop_product->getId()) ?>
  </div>
  RTAS-->
  <?php echo link_to_if($mode === 'wishlist', __('Remove from wishlist'), 'rt_shop_show_wishlist', array('delete' => $rt_shop_product->getId()), array('class' => 'delete'))?>
  <div class="image">
    <?php $promo_span = $rt_shop_product->isOnPromotion() ? '<span class="promotion">'.__('On Sale Now').'</span>' : ''; ?>
    <?php echo link_to($promo_span.image_tag(rtAssetToolkit::getThumbnailPath($rt_shop_product->getPrimaryImage() ? $rt_shop_product->getPrimaryImage()->getSystemPath() : '', array('maxHeight' => 450, 'maxWidth' => 350)), array('class' => 'primary-image')), 'rt_shop_product_show', $rt_shop_product) ?>
  </div>
  <div class="details">
    <h3><?php echo link_to($title, 'rt_shop_product_show', $rt_shop_product) ?></h3>
    <p class="price"><?php echo price_for($rt_shop_product); ?></p>
  </div>
</div>