<?php
use_helper('Number', 'Url', 'I18N', 'rtShopProduct');
use_stylesheet('/rtCorePlugin/vendor/jquery/css/ui/jquery.ui.css');
$assets = $rt_shop_product->getAssets();
?>

<h1><?php echo $rt_shop_product->getTitle() ?></h1>

<div class="rt-panel clearfix">
  
  <div class="rt-shop-product-primary-image">
  <?php $style = ''; foreach($rt_shop_product->getImages() as $image): ?>
  <?php echo image_tag(rtAssetToolkit::getThumbnailPath($image ? $image->getSystemPath() : '', array('maxHeight' => 250, 'maxWidth' => 200)), array('class' => 'primary-image', 'style' => $style, 'title' => $image->getOriginalFilename())) ?>
  <?php $style = 'display:none'; endforeach; ?>
  </div>

  <div class="rt-shop-product-content">
    <p class="rt-shop-product-price"><?php echo price_for($rt_shop_product) ?></p>
    <?php echo markdown_to_html($rt_shop_product->getContent(), $rt_shop_product); ?>
    <?php include_partial('rtShopProduct/order_panel', array('rt_shop_product' => $rt_shop_product)) ?>
  </div>

</div>

<?php if($rt_shop_product->getRtShopProducts()->count() > 0): ?>
<div class="rt-shop-product-related rt-collection rt-panel">
  <h2><?php echo __('Related Products') ?></h2>
  <?php foreach($rt_shop_product->rtShopProducts as $linked_rt_shop_product): ?>
    <?php include_component('rtShopProduct', 'shopProductMini', array('id' => $linked_rt_shop_product->getId())) ?>
  <?php endforeach; ?>
</div>
<?php endif; ?>