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
  <p><button type="submit" class="disabled" disabled><?php echo __('Add to Cart') ?></button></p>
  <?php endif; ?>

  <script type="text/javascript">
  $("form.rt-shop-product-order-panel").hide();

  $(function() {
    $(".rt-shop-option-set").buttonset().find(':radio');
    $(".rt-shop-option-set").find(':radio').click(function() {
      var match = $(this).attr("title").toLowerCase().replace(/[^a-zA-Z0-9]/g, "");
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
    $("form.rt-shop-product-order-panel button").attr("disabled",true);
  });

  checkUserSelection = function() {
    var count_available_items   = 0;
    var count_selection_groups  = $("form.rt-shop-product-order-panel .rt-shop-option-set").size();
    var button                  = $('form.rt-shop-product-order-panel button');

    $(".rt-shop-option-set").each(function(){
      $(this).children('input:checked').each(function(){
        count_available_items++;
      });
    });
    if(count_available_items == count_selection_groups)
    {
      var availablity_check = true;
      $("form .rt-shop-option-set input:checked").each(function()
      {
        if(!$(this).button('widget').hasClass('available'))
        {
          availablity_check = false;
        }
      });
      if(availablity_check)
      {
        button.text("<?php echo __('Add to Cart') ?>").attr("disabled",false);
        button.removeClass("disabled");
      }
      else
      {
        button.text("<?php echo __('Selection not available') ?>").attr("disabled",true);
        button.addClass("disabled");
      }
    }
  }
  </script>
</form>
<?php else: ?>
<p><?php echo __('Sorry, this product is out of stock.') ?></p>
<?php endif; ?>