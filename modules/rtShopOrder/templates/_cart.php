<?php
  $update_quantities = isset($update_quantities) ? $update_quantities : array();
  $stock_exceeded = isset($stock_exceeded) ? $stock_exceeded : array(); // needed for output escaping
?>
<tbody>
<?php $i = 0; foreach($rt_shop_order->getStockInfoArray() as $stock): ?>
  <?php
    $item_price = $stock['price_promotion'] != 0 ? $stock['price_promotion'] : $stock['price_retail'];
    $product = Doctrine::getTable('rtShopProduct')->find($stock['product_id']);
  ?>
  <tr class="<?php echo (isset($stock_exceeded[$stock['id']])) ? 'error' : '' ?>">
    <td class="rt-shop-cart-primary-image-thumb"><?php echo ($product->getPrimaryImage()) ? image_tag(rtAssetToolkit::getThumbnailPath($product->getPrimaryImage()->getSystemPath(), array('maxHeight' => 70, 'maxWidth' => 50))) : '' ?></td>
    <td class="rt-shop-cart-details">
      <input type="hidden" name="product_id[]" value="<?php echo $stock['rtShopProduct']['id']; ?>" />
      <?php echo link_to($stock['rtShopProduct']['title'], '@rt_shop_product_show?id='.$stock['rtShopProduct']['id'].'&slug='.$stock['rtShopProduct']['slug']) ?>
      <br />
      <span><?php

        if(count($stock['rtShopVariations']) > 0)
        {
          $comma = '';
          foreach($stock['rtShopVariations'] as $variation)
          {
            echo $comma . $variation['title'];
            $comma = ', ';
          }
        }

        ?></span>
    </td>
    <td class="rt-shop-cart-price-unit">
        <?php echo format_currency($item_price, sfConfig::get('app_rt_currency', 'USD')); ?>
    </td>
    <td class="rt-shop-cart-quantity">
      <input type="text" name="quantity[]" class="minitext" value="<?php echo isset($update_quantities[$stock['id']]) ? $update_quantities[$stock['id']] :$stock['rtShopOrderToStock'][0]['quantity']; ?>" />
      <?php if(isset($stock_exceeded[$stock['id']])): ?>
      <span>(<?php echo $stock_exceeded[$stock['id']] . ' ' . __('available') ?>)</span>
      <?php endif; ?>
      <input type="hidden" name="stock_id[]" value="<?php echo $stock['id']; ?>" />
    </td>
    <td class="rt-shop-cart-price-total">
      <?php echo format_currency($stock['rtShopOrderToStock'][0]['quantity'] * $item_price, sfConfig::get('app_rt_currency', 'USD')) ?>
    </td>
    <td class="rt-shop-cart-actions"><?php echo link_to(__('Delete'), '@rt_shop_order_stock_delete?id='.$stock['id']) ?></td>
  </tr>
<?php $i++; endforeach; ?>
</tbody>
<tfoot>
  <tr class="rt-shop-cart-sub-total">
    <th colspan="5"><?php echo __('Sub-Total'); ?>:</th>
    <td colspan="2"><?php echo format_currency($rt_shop_order->getTotalPriceWithoutTax(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
  </tr>
  <tr class="rt-shop-cart-tax">
    <th colspan="5"><?php echo __('Taxes'); ?>:</th>
    <td colspan="2"><?php echo format_currency($rt_shop_order->getTotalTax(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
  </tr>
  <tr class="rt-shop-cart-sub-total">
    <th colspan="5"><?php echo __('Sub-Total (including rates)'); ?>:</th>
    <td colspan="2"><?php echo format_currency($rt_shop_order->getTotalPriceWithTax(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
  </tr>
  <tr class="rt-shop-cart-total">
    <th colspan="5"><?php echo __('Grand-Total (including promotions)'); ?>:</th>
    <td colspan="2"><?php echo format_currency($rt_shop_order->getGrandTotalPrice(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
  </tr>
</tfoot>