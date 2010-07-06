<?php if(count($rt_shop_order->getClosedProducts()) > 0): ?>
  <tbody>
    <tr>
      <th><?php echo __('SKU'); ?></th>
      <th><?php echo __('Description'); ?></th>
      <th><?php echo __('Price (each)'); ?></th>
      <th><?php echo __('Qty.'); ?></th>
      <th><?php echo __('Price'); ?></th>
    </tr>
    <?php $sub_total = 0; ?>
    <?php foreach($rt_shop_order->getClosedProducts() as $product): ?>
      <tr>
        <td><?php echo $product['sku']; ?></td>
        <td><?php echo (sfConfig::get('sf_app') == 'backend') ? link_to($product['title'],'@rt_shop_product_edit?id='.$product['id']) : $product['title']; ?> <?php echo ($product['variations'] != '' && !empty ($product['variations'])) ? sprintf('[%s]',$product['variations']) : ''; ?></td>
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
      <td><?php echo format_currency($sub_total, 'AUD'); ?></td>
    </tr>
    <tr>
      <td colspan="4"><?php echo __('Taxes'); ?>:</td>
      <td><?php echo format_currency($rt_shop_order->getClosedTaxes(), 'AUD'); ?></td>
    </tr>
    <tr>
      <td colspan="4"><?php echo __('Shipping rate'); ?>:</td>
      <td><?php echo ($rt_shop_order->getClosedShippingRate() != false) ? format_currency($rt_shop_order->getClosedShippingRate(), 'AUD') : __('undefined'); ?></td>
    </tr>
    <tr>
      <td colspan="4"><?php echo __('Grand Total (including rates)'); ?>:</td>
      <td><?php echo format_currency($rt_shop_order->getClosedTotal(), 'AUD'); ?></td>
    </tr>
  </tfoot>
<?php else: ?>
  <tbody>
    <tr>
      <td colspan="5"><?php echo __('No products added to cart'); ?></td>
    </tr>
  </tbody>
<?php endif; ?>