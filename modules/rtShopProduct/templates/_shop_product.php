<?php use_helper('Number', 'Url', 'I18N', 'rtShopProduct'); ?>

<?php slot('rt-title', $rt_shop_product->getTitle()) ?>

<div class="clearfix">
<div class="rt-shop-product-gallery">
<?php include_partial('rtShopProduct/gallery', array('rt_shop_product' => $rt_shop_product)) ?>
</div>
<div class="rt-shop-product-details">
  <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-product-prefix','sf_cache_key' => 'shop-product-prefix')); ?>
  <?php include_partial('details_preffix', array('rt_shop_product' => $rt_shop_product)) ?>
  <?php include_partial('rtShopProduct/order_panel', array('rt_shop_product' => $rt_shop_product)) ?>
  <?php include_partial('details_suffix', array('rt_shop_product' => $rt_shop_product)) ?>
  <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-product-suffix','sf_cache_key' => 'shop-product-suffix')); ?>
</div>
</div>
<?php if($related_products): ?>
<div class="rt-shp-products-related rt-collection clearfix">
  <h2><?php echo __('Related Products') ?></h2>
  <?php $i=1; foreach($related_products as $linked_rt_shop_product): ?>
    <div class="rt-list-item rt-list-item-<?php echo $i ?>">
    <?php include_component('rtShopProduct', 'shopProductMini', array('id' => $linked_rt_shop_product->getId())) ?>
    </div>
  <?php $i++; endforeach; ?>
</div>
<?php endif; ?>

<script type="text/javascript">
  // No product variations, only quantities
  var count_available_items   = 0;
  var count_selection_groups  = $("form.rt-shop-product-order-panel .rt-shop-option-set").size();
  var button                  = $('form.rt-shop-product-order-panel button');

  $(".rt-shop-option-set").each(function(){
    $(this).children('input:checked').each(function(){
      count_available_items++;
    });
  });

  if(count_selection_groups == 0)
  {
    button.text("Add to Cart").attr("disabled",false);
    button.removeClass("disabled");
  }
</script>