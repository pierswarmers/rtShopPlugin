<?php
  $has_quantity_errors = false;
  if (sfContext::getInstance()->getUser()->hasAttribute('update_quantities')) {
    $update_quantities = sfContext::getInstance()->getUser()->getAttribute('update_quantities');
  }
  $stock_errors = $sf_user->getFlash('rtShopStock');
  $stock_errors = (is_object($stock_errors)) ? $stock_errors->getRawValue() : array(); // needed for output escaping
  $stock_keys = (count($stock_errors) > 0) ? array_keys($stock_errors) : array();
  if ($sf_user->hasFlash('rtShopStock') && count($stock_errors) > 0) {
    $has_quantity_errors = true;
  }
?>
<tbody>
<?php $i = 0; foreach($rt_shop_order->getStockInfoArray() as $stock): ?>
  <?php
    $item_price = $stock['price_promotion'] != 0 ? $stock['price_promotion'] : $stock['price_retail'];
    $class = ($has_quantity_errors && in_array($stock['id'], $stock_keys)) ? 'error' : '';
  ?>
  <tr class="<?php echo $class; ?>">
    <td><?php echo image_tag(rtAssetToolkit::getThumbnailPath(Doctrine::getTable('rtShopProduct')->find($stock['product_id'])->getPrimaryImage()->getSystemPath(), array('maxHeight' => 70, 'maxWidth' => 50))) ?></td>
    <td><?php echo link_to($stock['rtShopProduct']['title'], '@rt_shop_product_show?id='.$stock['rtShopProduct']['id'].'&slug='.$stock['rtShopProduct']['slug']) ?>
        <p><?php echo $stock['rtShopProduct']['description'] ?></p>
        <input type="hidden" name="product_id[]" value="<?php echo $stock['rtShopProduct']['id']; ?>" />
    </td>
    <td><?php if(count($stock['rtShopVariations']) > 0): ?>
          <?php foreach($stock['rtShopVariations'] as $variation): ?>
              <p><strong><?php echo $variation['rtShopAttribute']['title'] ?>:</strong> <?php echo $variation['title'] ?></p>
          <?php endforeach; ?>
        <?php endif; ?></td>
    <td><?php echo format_currency($item_price, sfConfig::get('app_rt_shop_payment_currency','AU')); ?></td>
    <td>
      <input style="width:20px" type="text" name="quantity[]" value="<?php echo ($sf_user->getFlash('rtShopStock') && isset($update_quantities)) ? $update_quantities[$stock['id']] :$stock['rtShopOrderToStock'][0]['quantity']; ?>" />
      <input type="hidden" name="stock_id[]" value="<?php echo $stock['id']; ?>" />
      <?php echo ($has_quantity_errors && in_array($stock['id'], $stock_keys)) ? "[ max. ".$stock_errors[$stock['id']]." ]" : ""; ?>
    </td>
    <td><?php echo format_currency($stock['rtShopOrderToStock'][0]['quantity'] * $item_price, sfConfig::get('app_rt_shop_payment_currency','AU')) ?></td>
    <td><?php echo link_to(__('Delete'), '@rt_shop_order_stock_delete?id='.$stock['id']) ?></td>
  </tr>
<?php $i++; endforeach; ?>
</tbody>
<tfoot>
  <tr>
    <td colspan="5"><?php echo __('Sub-Total'); ?>:</td>
    <td colspan="2"><?php echo format_currency($rt_shop_order->getTotalPriceWithoutTax(), sfConfig::get('app_rt_shop_payment_currency','AU')); ?></td>
  </tr>
  <tr>
    <td colspan="5"><?php echo __('Taxes'); ?>:</td>
    <td colspan="2"><?php echo format_currency($rt_shop_order->getTotalTax(), sfConfig::get('app_rt_shop_payment_currency','AU')); ?></td>
  </tr>
  <tr>
    <td colspan="5"><?php echo __('Sub-Total (including rates)'); ?>:</td>
    <td colspan="2"><?php echo format_currency($rt_shop_order->getTotalPriceWithTax(), sfConfig::get('app_rt_shop_payment_currency','AU')); ?></td>
  </tr>
  <tr>
    <td colspan="5"><?php echo __('Grand-Total (including promotions)'); ?>:</td>
    <td colspan="2"><?php echo format_currency($rt_shop_order->getGrandTotalPrice(), sfConfig::get('app_rt_shop_payment_currency','AU')); ?></td>
  </tr>
</tfoot>