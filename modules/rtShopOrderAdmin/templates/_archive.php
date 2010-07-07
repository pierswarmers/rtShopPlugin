<?php if(count($rt_shop_order->getClosedProducts()) > 0): ?>
  <tbody>
    <tr>
      <th><?php echo __('SKU'); ?></th>
      <th><?php echo __('Description'); ?></th>
      <th><?php echo __('Price (each)'); ?></th>
      <th><?php echo __('Quantity'); ?></th>
      <th><?php echo __('Price'); ?></th>
    </tr>
    <?php $sub_total = 0; ?>
    <?php foreach($rt_shop_order->getClosedProducts() as $product): ?>
      <tr>
        <td><code><?php echo link_to_if($product['sku'] != '',$product['sku'],'/rtShopProductAdmin/stock?id='.$product['id_product']); ?></code></td>
        <td><?php echo link_to($product['title'],'/rtShopProductAdmin/edit?id='.$product['id_product']); ?> <?php echo ($product['variations'] != '' && !empty ($product['variations'])) ? sprintf('[%s]',$product['variations']) : ''; ?></td>
        <td><?php echo format_currency($product['charge_price'], $product['currency']); ?></td>
        <td><?php echo $product['quantity']; ?></td>
        <td><?php echo format_currency($product['quantity']*$product['charge_price'], $product['currency']); ?></td>
      </tr>
      <?php $sub_total += $product['charge_price']*$product['quantity']; ?>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="4"><?php echo __('Sub-Total'); ?>:</td>
      <td><?php echo format_currency($sub_total, sfConfig::get('app_rt_currency', 'AUD')); ?></td>
    </tr>
    <tr>
      <td colspan="4"><?php echo __('Taxes'); ?>:</td>
      <td><?php echo format_currency($rt_shop_order->getClosedTaxes(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
    </tr>
    <tr>
      <td colspan="4"><?php echo __('Shipping rate'); ?>:</td>
      <td><?php echo ($rt_shop_order->getClosedShippingRate() != false) ? format_currency($rt_shop_order->getClosedShippingRate(), sfConfig::get('app_rt_currency', 'AUD')) : __('undefined'); ?></td>
    </tr>
    <tr>
      <td colspan="4"><?php echo __('Grand Total (including rates)'); ?>:</td>
      <td><?php echo format_currency($rt_shop_order->getClosedTotal(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
    </tr>
  </tfoot>
<?php else: ?>
  <tbody>
    <tr>
      <td colspan="5"><?php echo __('No products added to cart'); ?></td>
    </tr>
  </tbody>
<?php endif; ?>