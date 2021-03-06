<?php

/** @var rtShopProduct $rt_shop_product */

use_helper('Number', 'Url', 'I18N', 'rtShopProduct', 'rtTemplate', 'rtText')

?>

<div class="rt-section rt-shop-product">

  <!--RTAS
  <div class="rt-section-tools-header rt-admin-tools">
    <?php echo link_to(__('Edit Product'), 'rtShopProductAdmin/edit?id='.$rt_shop_product->getId(), array('class' => 'rt-admin-edit-tools-trigger')) ?>
  </div>
  RTAS-->
  <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
    <div class="rt-section-header">
      <h1><?php echo $rt_shop_product->getTitle() ?></h1>
    </div>
  <?php endif; ?>
  
  <div  id="rtProductHolder"  class="rt-section-content clearfix">

    <div class="rt-shop-product-details">


    <ul class="tabs">
      <!-- Give href an ID value of corresponding "tabs-content" <li>'s -->
      <li><a class="active" href="#order">Details</a></li>
      <li><a href="#comments">Reviews</a></li>
      <li><a href="#fittings">Fittings</a></li>
      <li><a href="#shipping">Delivery &amp; Returns</a></li>
    </ul>

    <ul class="tabs-content">
      <li class="active" id="orderTab">

          <h2>
            Price: <?php echo price_for($rt_shop_product) ?>
            <?php if($rt_shop_product->getSku()): ?>
            <span class="sku"><code>#<?php echo $rt_shop_product->getSku() ?></code></span>
            <?php endif; ?>
          </h2>

          <div class="rt-metas">
            <?php if($rt_shop_product->getNumberOfComments() > 0): ?>
            Average Rating: <?php if($rt_shop_product->getOverallRating() > 0) { include_partial('rtComment/rating', array('rating_value' => $rt_shop_product->getOverallRating(), 'show_items' => array('text','graph'))); } ?>
            <?php endif; ?>
            <span class="tabs"><a id="commentsTrigger"><?php echo __($rt_shop_product->getNumberOfComments() > 0 ? 'See reviews' : 'Be the first to review') ?></a></span>
          </div>


        <?php include_partial('details_preffix', array('rt_shop_product' => $rt_shop_product)) ?>

        <?php include_partial('rtShopProduct/order_panel', array('rt_shop_product' => $rt_shop_product)) ?>

        <p class="rt-shop-product-tools">
          <span class="rt-shop-add-to-wishlist"><a href="#"><?php echo __('Add to wishlist') ?></a></span> |
          <span class="rt-shop-send-to-friend"><a href="<?php echo url_for('rt_shop_send_to_friend', array('product_id' => $rt_shop_product->getId())) ?>"><?php echo __('Send to a friend') ?></a></span>
        </p>


          <div class="description"><?php echo markdown_to_html($rt_shop_product->getContent(), $rt_shop_product); ?></div>

          <script type="text/javascript">
            $(function() {
              $('#commentsTrigger').click(function(){ $('a[href="#comments"]').trigger('click'); });
            });
          </script>

          <?php include_partial('details_suffix', array('rt_shop_product' => $rt_shop_product)) ?>

          <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-product-suffix','sf_cache_key' => 'shop-product-suffix')); ?>
      </li>
      
      <li id="commentsTab">
          <?php if(in_array($rt_shop_product->getCommentStatus(), array('open', 'user'))): ?>
            <?php include_component('rtComment', 'panel', array('model' => 'rtShopProduct', 'model_id' => $rt_shop_product->getId(), 'title' => $rt_shop_product->getTitle(), 'rating_enabled' => true)) ?>
          <?php endif; ?>
      </li>

      <li id="fittingsTab"><?php echo rt_get_global_snippet('rt-shop-product-fittings') ?></li>
      <li id="shippingTab"><?php echo rt_get_global_snippet('rt-shop-product-shipping') ?></li>
    </ul>


    </div>


    <div class="rt-shop-product-gallery">
      <?php include_partial('rtShopProduct/gallery', array('rt_shop_product' => $rt_shop_product)) ?>
    </div>




    
  </div>
  
  <div class="rt-section-tools-footer">
    
    <?php if($related_products): ?>
      <div class="rt-shp-products-related rt-collection clearfix">
        <h2><?php echo __('Related Products') ?></h2>
        <?php $i=1; foreach($related_products as $linked_rt_shop_product): ?>
          <div class="rt-list-item item-<?php echo $i ?>">
          <?php include_component('rtShopProduct', 'shopProductMini', array('id' => $linked_rt_shop_product->getId())) ?>
          </div>
        <?php $i++; endforeach; ?>
      </div>
    <?php endif; ?>    
    
  </div>

</div>

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