<table>
  <thead>
    <tr>
      <th colspan="5"><?php echo __('Products ordered')  ?></th>
    </tr>
  </thead>
  <?php if(count($rt_shop_order->getProductsData()) > 0): ?>
  <tbody>
    <tr>
      <th><?php echo __('Description'); ?></th>
      <th><?php echo __('SKU'); ?></th>
      <th><?php echo __('Price (each)'); ?></th>
      <th><?php echo __('Quantity'); ?></th>
      <th><?php echo __('Price'); ?></th>
    </tr>
    <?php $sub_total = 0; ?>
    <?php foreach($rt_shop_order->getProductsData() as $product): ?>
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
        <td><?php echo format_currency($rt_shop_order->getTaxCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
    <?php endif; ?>

    <?php if($rt_shop_order->getPromotionReduction() > 0): ?>
    <?php $promotion = $rt_shop_order->getPromotionData(); ?>
      <tr>
        <td colspan="4"><?php echo __('Promotion'); ?> (<?php echo $promotion['title'] ?>):</td>
        <td>-<?php echo format_currency($rt_shop_order->getPromotionReduction(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
    <?php endif; ?>

    <?php if($rt_shop_order->getVoucherReduction() > 0): ?>
    <?php $voucher = $rt_shop_order->getVoucherData(); ?>
      <tr>
        <td colspan="4"><?php echo __('Voucher'); ?> (<?php echo $voucher['title'] ?>):</td>
        <td>-<?php echo format_currency($rt_shop_order->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
    <?php endif; ?>
      
    <tr>
      <td colspan="4"><?php echo __('Shipping rate'); ?>:</td>
      <td><?php echo ($rt_shop_order->getShippingCharge() != false) ? format_currency($rt_shop_order->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')) : __('undefined'); ?></td>
    </tr>
    <?php
    $includes_message = '';
    if(sfConfig::get('app_rt_shop_tax_rate', 0) > 0 && sfConfig::get('app_rt_shop_tax_mode') == 'inclusive')
    {
      $includes_message = sprintf('(includes %s tax)',format_currency(rtShopCartManager::calcTaxComponent($rt_shop_order->getTotalCharge()), sfConfig::get('app_rt_currency', 'AUD')));
    }
    ?>
    <tr>
      <td colspan="4"><?php echo __('Total'); ?> <?php echo $includes_message  ?>:</td>
      <td><?php echo format_currency($rt_shop_order->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
    </tr>
  </tfoot>
<?php else: ?>
  <tbody>
    <tr>
      <td colspan="5"><?php echo __('No products added to cart'); ?></td>
    </tr>
  </tbody>
<?php endif; ?>
</table>