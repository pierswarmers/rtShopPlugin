<?php if(count($rt_shop_order->getClosedProducts()) > 0): ?>
  <tbody>
    <tr>
      <th><?php echo __('Description'); ?></th>
      <th><?php echo __('SKU'); ?></th>
      <th><?php echo __('Price (each)'); ?></th>
      <th><?php echo __('Quantity'); ?></th>
      <th><?php echo __('Price'); ?></th>
    </tr>
    <?php $sub_total = 0; ?>
    <?php foreach($rt_shop_order->getClosedProducts() as $product): ?>
      <tr>
        <td><?php echo $product['title'] ?> <?php echo ($product['variations'] != '' && !empty ($product['variations'])) ? sprintf('(%s)',$product['variations']) : ''; ?></td>
        <td><?php echo $product['sku']; ?></td>
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
    <?php if(sfConfig::get('app_rt_shop_tax_mode','inclusive') == 'exclusive'): ?>
      <tr>
        <td colspan="4"><?php echo __('Taxes'); ?>:</td>
        <td><?php echo format_currency($rt_shop_order->getClosedTaxes(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
    <?php endif; ?>

    <?php if($rt_shop_order->getClosedPromotions() > 0): ?>
      <tr>
        <td colspan="4"><?php echo __('Promotion'); ?>:</td>
        <td>-<?php echo format_currency($rt_shop_order->getClosedPromotions(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
    <?php endif; ?>
      
    <tr>
      <td colspan="4"><?php echo __('Shipping rate'); ?>:</td>
      <td><?php echo ($rt_shop_order->getClosedShippingRate() != false) ? format_currency($rt_shop_order->getClosedShippingRate(), sfConfig::get('app_rt_currency', 'AUD')) : __('undefined'); ?></td>
    </tr>
    <?php
    $includes_message = '';
    if(sfConfig::get('app_rt_shop_tax_rate', 0) > 0 && sfConfig::get('app_rt_shop_tax_mode') == 'inclusive')
    {
      $includes_message = sprintf('(includes %s tax)',format_currency(rtShopCartManager::calcTaxComponent($rt_shop_order->getClosedTotal()), sfConfig::get('app_rt_currency', 'AUD')));
    }
    ?>
    <tr>
      <td colspan="4"><?php echo __('Total'); ?> <?php echo $includes_message  ?>:</td>
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