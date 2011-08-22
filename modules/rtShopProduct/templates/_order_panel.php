<?php
use_helper('Number', 'Url', 'I18N', 'rtShopProduct');
use_javascript('/rtCorePlugin/vendor/jquery/js/jquery.min.js');
use_javascript('/rtCorePlugin/vendor/jquery/js/jquery.ui.min.js');

/**
 * @var rtShopProduct $rt_shop_product
 * @var rtShopAttribute $rt_shop_attribute
 */

if($rt_shop_product->isPurchasable()): ?>

<form action="<?php echo url_for('@rt_shop_order_add_to_bag') ?>" method="post" class="rt-shop-product-order-panel">

  <input type="hidden" name="rt-shop-product-id" id="rt-shop-product-id" value="<?php echo $rt_shop_product->getId(); ?>" />

  <?php

  $i = 0;

  // Cycle through attribute

  foreach (Doctrine::getTable('rtShopAttribute')->findByProductId($rt_shop_product->getId()) as $rt_shop_attribute):

    // Get a list of variations for this attribute and product
    $variations = Doctrine::getTable('rtShopVariation')->findByAttributeIdAndProductId($rt_shop_attribute->getId(), $rt_shop_product->getId());
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
        if($rt_shop_stock->quantity > 0 || $rt_shop_product->getBackorderAllowed()) {
        $ref[] = 'rt-shop-stock-id-'. $rt_shop_stock->id;
        }
      }

      $available = $stock_level > 0;
      $file_location = sfConfig::get('sf_upload_dir') . '/variations/' . $variation->image;
      $image = $rt_shop_attribute->getDisplayImage() && is_file($file_location) ? ' style="background-image: url('.rtAssetToolkit::getThumbnailPath($file_location, array('maxWidth' => 30, 'maxHeight' => 30)).')"' : '';

      ?>

        <input name="rt-shop-variation-ids[<?php echo $i ?>]" title="<?php echo htmlentities($variation->getTitle()) ?>" id="rt-variation-<?php echo $variation->getId() ?>" class="<?php echo ($available ? 'available ' : 'unavailable') . ' '. implode(' ', $ref) ?>" type="radio" value="<?php echo $variation->getId() ?>" <?php echo count($variations) == 1 ? ' checked="checked"' : '' ?>/>
        <span class="ref" style="display:none">.<?php echo implode(', .', $ref) ?></span>
        <label for="rt-variation-<?php echo $variation->getId() ?>" class="<?php echo $stock_level > 0 ? '' : 'unavailable' ?> <?php echo $image !== '' ? 'image-swatch' : '' ?> <?php echo !$rt_shop_attribute->getDisplayLabel() ? 'label-hidden' : '' ?>" <?php echo $image ?>><?php echo $variation->getTitle() ?></label>

      <?php endforeach; // Finish cycle through each variation  ?>

    </span>
  </p>

  <?php $i++; endforeach; // Finish cycle through attributes ?>

  <p class="rt-shop-item-quantity">
    <label for="rt-shop-quantity"><?php echo __('Quantity') ?>:</label>
    <input name="rt-shop-quantity" id="rt-shop-quantity" class="rt-text-small" type="number" min="1" max="50" step="1" value="1" />
  </p>

  <?php if(sfConfig::get('app_rt_shop_ordering_enabled', true)): // Emergency kill for ordering... ?>

  <p><button type="submit" class="disabled" disabled><?php echo __('Add to cart') ?></button><span></span></p>

  <?php endif; ?>

</form>

<?php else: ?>

<p class="notice rt-flash-message"><?php echo __('Sorry, this product is out of stock.') ?></p>

<?php endif; // is $rt_shop_product->isPurchasable()? ?>