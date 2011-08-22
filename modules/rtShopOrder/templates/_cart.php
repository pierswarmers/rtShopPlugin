<?php
  // Distinguish between editable or not, default is true
  $editable = isset($editable) ? false : true;
  // Stock update
  $update_quantities = isset($update_quantities) ? $update_quantities : array();
  $stock_exceeded = isset($stock_exceeded) ? $stock_exceeded : array(); // needed for output escaping
?>
<tbody>
<?php $i = 0; foreach($rt_shop_cart_manager->getOrder()->getStockInfoArray() as $stock): ?>
  <?php // Cart item row START ?>
  <?php
    $item_price = $stock['price_promotion'] != 0 ? $stock['price_promotion'] : $stock['price_retail'];
    $product = Doctrine::getTable('rtShopProduct')->find($stock['product_id']);

    $match = null;
    $variations = '';

    if(count($stock['rtShopVariations']) > 0)
    {
      $comma = '';
      $or = '';
      
      foreach($stock['rtShopVariations'] as $variation)
      {
        // build the variation list to display
        $variations .= $comma . $variation['title'];
        $comma = ', ';

        $cleaned_title = rtAssetToolkit::cleanFilename($variation['title'], true);

        if(!is_numeric($cleaned_title))
        {
          // avoid simple numbers when building the $match regex.
          $match .= $or . rtAssetToolkit::cleanFilename($cleaned_title, true);
          $or = '|';
        }
      }

      $match = '/('.$match.')/i';

      $image = $product->getPrimaryImage($match) ? image_tag(rtAssetToolkit::getThumbnailPath($product->getPrimaryImage($match)->getSystemPath(), array('maxHeight' => 500, 'maxWidth' => 150))) : 'xx';
    }
    else
    {
      $image = $product->getPrimaryImage() ? image_tag(rtAssetToolkit::getThumbnailPath($product->getPrimaryImage()->getSystemPath(), array('maxHeight' => 500, 'maxWidth' => 150))) : 'xx';
    }
    
  ?>
  <tr class="<?php echo (isset($stock_exceeded[$stock['id']])) ? 'error' : '' ?>">
    <td class="rt-shop-cart-primary-image-thumb">
      <?php echo link_to($image, '@rt_shop_product_show?id='.$stock['rtShopProduct']['id'].'&slug='.$stock['rtShopProduct']['slug']) ?>
    </td>
    <td class="rt-shop-cart-details">
      <input type="hidden" name="product_id[]" value="<?php echo $stock['rtShopProduct']['id']; ?>" />
      <?php echo link_to($stock['rtShopProduct']['title'], '@rt_shop_product_show?id='.$stock['rtShopProduct']['id'].'&slug='.$stock['rtShopProduct']['slug'], array('class' => 'title')) ?>
      <?php echo link_to(__('delete'), '@rt_shop_order_stock_delete?id='.$stock['id'], array('class' => 'delete')) ?>

      <div class="rt-shop-cart-variations"><?php echo $variations ?></div>
      <?php if(rtSiteToolkit::isMultiSiteEnabled()): ?>
      <?php include_partial('rtAdmin/site_reference_key', array('id' => $product->getSiteId()))?>
      <?php endif; ?>

      <div class="rt-shop-cart-price-unit">
          <?php echo format_currency($item_price, sfConfig::get('app_rt_currency', 'USD')). ' ' . __('each'); ?>
      </div>
      
    </td>
    <?php if($editable == true): ?>
    <?php endif; ?>
    <td class="rt-shop-cart-quantity">
      <?php if($editable == false): ?>
        <?php echo $stock['rtShopOrderToStock'][0]['quantity'] ?>
      <?php else: ?>
        <input name="quantity[]" id="rt-shop-quantity" class="rt-text-small" type="number" min="1" max="50" step="1" value="<?php echo isset($update_quantities[$stock['id']]) ? $update_quantities[$stock['id']] :$stock['rtShopOrderToStock'][0]['quantity']; ?>" />
        <?php if(isset($stock_exceeded[$stock['id']])): ?>
        <span>(<?php echo $stock_exceeded[$stock['id']] . ' ' . __('available') ?>)</span>
        <?php endif; ?>
        <input type="hidden" name="stock_id[]" value="<?php echo $stock['id']; ?>" />
      <?php endif; ?>
    </td>
    <td class="rt-shop-cart-price-total">
      <?php echo format_currency($stock['rtShopOrderToStock'][0]['quantity'] * $item_price, sfConfig::get('app_rt_currency', 'USD')) ?>
    </td>
  </tr>
  <?php // Cart item row END ?>
<?php $i++; endforeach; ?>

<!-- Gift voucher START -->
<?php $vm = $rt_shop_cart_manager->getVoucherManager(); ?>
<?php if ($vm->hasSessionVoucher()): ?>
  <?php $voucher = $rt_shop_cart_manager->getVoucherManager()->getSessionVoucherArray(); ?>
  <?php $options =array('rt-voucher-referer' => urlencode(rtSiteToolkit::getRequestUri())); ?>
  <tr class="rt-shop-cart-voucher">
    <td class="rt-shop-cart-primary-image-thumb"><div>&nbsp;</div></td>
    <td class="rt-shop-cart-details"><?php echo link_to($voucher['title'], 'rt_shop_voucher_edit', $options) ?>
    <br />
    <span><?php echo __('For') ?>: <?php echo $voucher['first_name'] ?> <?php echo $voucher['last_name'] ?></span></td>
    <?php if($editable == true): ?>
      <td class="rt-shop-cart-actions"><?php echo link_to(__('Edit'), 'rt_shop_voucher_edit', $options) ?> | <?php echo link_to(__('Delete'), 'rt_shop_voucher_delete', $options) ?></td>
    <?php endif; ?>
    <td class="rt-shop-cart-price-unit"><?php echo format_currency($voucher['reduction_value'], sfConfig::get('app_rt_currency', 'USD')) ?></td>
    <td class="rt-shop-cart-quantity">1</td>
    <td class="rt-shop-cart-price-total"><?php echo format_currency($voucher['reduction_value'], sfConfig::get('app_rt_currency', 'USD')) ?></td>
  </tr>
<?php endif; ?>
<!-- Gift voucher END -->

</tbody>