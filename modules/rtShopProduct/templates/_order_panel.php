<?php

use_helper('Number', 'Url', 'I18N', 'rtShopProduct');

use_javascript('/rtCorePlugin/vendor/jquery/js/jquery.min.js');
use_javascript('/rtCorePlugin/vendor/jquery/js/jquery.ui.min.js');
use_stylesheet('/rtCorePlugin/vendor/jquery/css/ui/jquery.ui.css');
use_stylesheet('/rtShopPlugin/css/main.css', 'last');

?>
<?php if($rt_shop_product->isPurchasable()): ?>
<form method="post" class="rt-shop-product-order-panel" action="<?php echo url_for('@rt_shop_order_add_to_bag', $rt_shop_product); ?>">

  <input type="hidden" name="rt-shop-product-id" value="<?php echo $rt_shop_product->getId(); ?>" />
  
  <?php $i = 0; foreach (Doctrine::getTable('rtShopAttribute')->findByProductId($rt_shop_product->getId()) as $rt_shop_attribute): ?>
  <?php $variations = Doctrine::getTable('rtShopVariation')->findByAttributeIdAndProductId($rt_shop_attribute->getId(), $rt_shop_product->getId());  ?>

  <p class="clearfix selectionGroup">

    <strong><?php echo $rt_shop_attribute->getDisplayTitle() ?>: </strong>
    <span class="rt-shop-option-set">
    <?php foreach ($variations as $variation): ?>

    <?php
    $ref = array();
    $stock_level = 0;

    foreach(Doctrine::getTable('rtShopStock')->getForProductIdAndVariationId($rt_shop_product->getId(), $variation->getId()) as $rt_shop_stock)
    {
      $stock_level += $rt_shop_stock->quantity;
      $ref[] = 'rt-shop-stock-id-'. $rt_shop_stock->id;
    }

    $available = $stock_level > 0  || $rt_shop_product->backorder_allowed;
    $class = $available ? 'available ' : 'unavailable';

    $image = '';
    $is_image = false;
    $file_location =sfConfig::get('sf_upload_dir') . '/variations/'.$variation->image;

    if(is_file($file_location))
    {
      $image = ' style="background: url('.rtAssetToolkit::getThumbnailPath($file_location, array('maxWidth' => 30, 'maxHeight' => 30)).')"';
      $is_image = true;
    }

    $class .= ' '. implode(' ', $ref);
    ?>
      <input  name="rt-shop-variation-ids[<?php echo $i ?>]" title="<?php echo $variation->getTitle() ?>" id="rt-variation-<?php echo $variation->getId() ?>" class="<?php echo $class ?>" type="radio" value="<?php echo $variation->getId() ?>" />
      <span class="ref" style="display:none">.<?php echo implode(', .', $ref) ?></span>
      <label for="rt-variation-<?php echo $variation->getId() ?>" class="<?php echo $available ? '' : 'unavailable' ?> <?php echo $is_image ? 'image-swatch' : '' ?>" <?php echo $image ?>><?php echo $variation->getTitle() ?></label>
    <?php endforeach; ?>
    </span>
  </p>

  <?php $i++; endforeach; ?>

  <p class="rt-shop-item-quantity">
    <label for="rt-shop-quantity"><?php echo __('Quantity') ?>:</label>
    <input type="text" name="rt-shop-quantity" class="text minitext" value="1" />
  </p>

  <?php if(sfConfig::get('rt_shop_ordering_enabled', true)): ?>
    <p><button type="submit"><?php echo __('Add to Cart') ?></button></p>
  <?php endif; ?>

  <script type="text/javascript">
  $("form.rt-shop-product-order-panel").hide();

  $(function() {
    $(".rt-shop-option-set").buttonset().find(':radio');
    $(".rt-shop-option-set").find(':radio').click(function() {
      // run image selection switch
      var match = $(this).attr("title").toLowerCase().replace(/ /g, "").replace(/-/g, "");
      $(".rt-shop-product-primary-image a[class*=rt-image-ref-"+match+"]").css("display","inline").siblings('a').css("display","none");
      // de-focus all options
      $(".rt-shop-option-set input[type=radio]").each(function(){
        
        $(this).button( "widget" ).fadeTo(1, 0.3).removeClass('available');
      });
      // focus available options based on stock id matrix
      $($(this).next('.ref').html()).each(function(){
        
        $(this).button( "widget" ).fadeTo(1, 1).addClass('available')
      });
      checkUserSelection();
    }).each(function(){
      if($(this).button( "widget" ).hasClass('unavailable')) {
        $(this).button('disable', true);
      }
    });
    $("form.rt-shop-product-order-panel").show();
    disableSubmitButton("form.rt-shop-product-order-panel button");
  });

  disableSubmitButton = function(id) {
    $(id).text("Selection not available");
    $(id).attr("disabled","disabled");
    $(id).css("color","#CCC");
    $(id).css("cursor","default");
  }
  enableSubmitButton = function(id) {
    $(id).text("Add to Cart");
    $(id).attr("disabled",false);
    $(id).css("color","#000");
    $(id).css("cursor","pointer");
  }
  checkUserSelection = function() {
    var count_available_items   = 0;
    var count_selection_groups  = 0;
    // count the ammount of selection groups
    $("form.rt-shop-product-order-panel p.selectionGroup").each(function()
    {
      count_selection_groups++;
    })
    // each input with an stock class
    $("form.rt-shop-product-order-panel input[class*=rt-shop-stock-id]").each(function()
    {
      // get the stock ID of the input field
      var id = $(this).attr('id');
      // each label for a stock input field, which has the classes "available" as well as "ui-state-active"
      $("form.rt-shop-product-order-panel label[for="+id+"][class*=available][class*=ui-state-active]").each(function()
      {
        // count those labels
        count_available_items++;
        // if the amount of these labels is the same with the selection groups, we have an available combination
        if(count_available_items == count_selection_groups)
        {
          enableSubmitButton("form.rt-shop-product-order-panel button");
        } else {
          disableSubmitButton("form.rt-shop-product-order-panel button");
        }
      })
    })
  }
  </script>
</form>
<?php else: ?>
<p><?php echo __('Sorry, this product is out of stock.') ?></p>
<?php endif; ?>