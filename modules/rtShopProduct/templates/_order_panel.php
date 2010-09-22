<?php
include_partial('order_panel_assets');

use_helper('Number', 'Url', 'I18N', 'rtShopProduct');
if($rt_shop_product->isPurchasable()):

?>

<form action="<?php echo url_for('@rt_shop_order_add_to_bag') ?>" method="post" class="rt-shop-product-order-panel">

  <input type="hidden" name="rt-shop-product-id" value="<?php echo $rt_shop_product->getId(); ?>" />
  
  <?php 
  
  $i = 0;

  // Cycle through attribute

  foreach (Doctrine::getTable('rtShopAttribute')->findByProductId($rt_shop_product->getId()) as $rt_shop_attribute):

    // Get a list of variations for this attribute and product

    $variations = Doctrine::getTable('rtShopVariation')->findByAttributeIdAndProductId(
                    $rt_shop_attribute->getId(),
                    $rt_shop_product->getId()
                  );
  ?>

  <p class="clearfix rt-shop-selection-group">

    <strong><?php echo __('Select') . ' ' . $rt_shop_attribute->getDisplayTitle() ?>: </strong>
    
    <span class="rt-shop-option-set">
    
      <?php

      // Cycle through each variation

      foreach ($variations as $variation):

      $ref = array();
      $stock_level = 0;

      foreach(Doctrine::getTable('rtShopStock')->getForProductIdAndVariationId($rt_shop_product->getId(), $variation->getId()) as $rt_shop_stock)
      {
        $stock_level += $rt_shop_stock->quantity;
        $ref[] = 'rt-shop-stock-id-'. $rt_shop_stock->id;
      }

      $available = $stock_level > 0;
      $image = '';
      $is_image = false;
      $file_location =sfConfig::get('sf_upload_dir') . '/variations/'.$variation->image;

      if(is_file($file_location))
      {
        $image = ' style="background: url('.rtAssetToolkit::getThumbnailPath($file_location, array('maxWidth' => 30, 'maxHeight' => 30)).')"';
        $is_image = true;
      }

      $class = ($available ? 'available ' : 'unavailable') . ' '. implode(' ', $ref);

      // Clean entities from title

      $title = htmlentities($variation->getTitle());

      ?>
      
        <input name="rt-shop-variation-ids[<?php echo $i ?>]" title="<?php echo $title ?>" id="rt-variation-<?php echo $variation->getId() ?>" class="<?php echo $class ?>" type="radio" value="<?php echo $variation->getId() ?>" <?php echo count($variations) == 1 ? ' checked="checked"' : '' ?>/>
        <span class="ref" style="display:none">.<?php echo implode(', .', $ref) ?></span>
        <label for="rt-variation-<?php echo $variation->getId() ?>" class="<?php echo $stock_level > 0 ? '' : 'unavailable' ?> <?php echo $is_image ? 'image-swatch' : '' ?>" <?php echo $image ?>><?php echo $variation->getTitle() ?></label>

      <?php endforeach; // Finish cycle through each variation  ?>

    </span>
  </p>

  <?php
  
  $i++;
  
  endforeach; // Finish cycle through attribute

  ?>

  <p class="rt-shop-item-quantity">
    <label for="rt-shop-quantity"><?php echo __('Quantity') ?>:</label>
    <input type="text" name="rt-shop-quantity" id="rt-shop-quantity" class="rt-text-small" value="1" />
  </p>

  <?php if(sfConfig::get('app_rt_shop_ordering_enabled', true)): ?>
  <p>
    <button type="submit" class="disabled" disabled><?php echo __('Please make your selection') ?></button>
    <span id="rt-shop-add-to-wishlist"><a href="#"><?php echo __('Add to wishlist') ?></a></span> |
    <span id="rt-shop-send-to-friend"><a href="<?php echo url_for('rt_shop_send_to_friend', array('product_id' => $rt_shop_product->getId())) ?>"><?php echo __('Send to a friend') ?></a></span>
  </p>
  <?php endif; ?>

</form>
<?php else: ?>
<p><?php echo __('Sorry, this product is out of stock.') ?></p>
<?php endif; ?>