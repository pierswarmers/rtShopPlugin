<?php use_helper('I18N', 'Number'); $addresses = $rt_shop_order->getAddressInfoArray(); $shipping = (count($addresses) == 2) ? 1 : 0; $promotion = $rt_shop_order->getPromotionData(); $voucher = $rt_shop_order->getVoucherData(); ?>
------------------------------------------------------------
<?php echo strtoupper(__('Shop Details')) ?><?php echo "\r\n" ?>
------------------------------------------------------------
<?php echo sfConfig::get('app_rt_address','') ?>

------------------------------------------------------------
<?php echo strtoupper(__('Order Details')) ?><?php echo "\r\n" ?>
------------------------------------------------------------
<?php echo __('Payment transaction ID') ?>: <?php echo $rt_shop_order->getReference() ?><?php echo "\r\n" ?>
<?php echo __('Order reference') ?>: <?php echo $rt_shop_order->getPaymentTransactionId() ?><?php echo "\r\n" ?>
<?php echo __('Payment charge') ?>: <?php echo format_currency($rt_shop_order->getPaymentCharge(), 'AUD') ?><?php echo "\r\n" ?>
<?php echo __('Status') ?>: <?php echo strtoupper($rt_shop_order->getStatus()) ?><?php echo "\r\n" ?>
<?php if($rt_shop_order->getVoucherCode()): ?>
<?php echo __('Voucher code') ?>: <?php echo $rt_shop_order->getVoucherCode() ?><?php echo "\r\n" ?>
<?php endif; ?>
<?php echo __('Date') ?>: <?php echo date("d F Y", strtotime($rt_shop_order->getCreatedAt())) ?><?php echo "\r\n" ?>
<?php echo __('Email') ?>: <?php echo $rt_shop_order->getEmailAddress() ?><?php echo "\r\n" ?>

------------------------------------------------------------
<?php echo strtoupper(__('Address Details')) ?><?php echo "\r\n" ?>
------------------------------------------------------------
<?php if(count($addresses) > 0): ?>
** <?php echo strtoupper(__('Billing'))." **\r\n" ?>
<?php echo $addresses[0]['first_name'] . " " . $addresses[0]['last_name'] ?><?php echo "\r\n" ?>
<?php echo $addresses[0]['address_1'] ?><?php echo "\r\n" ?>
<?php echo ($addresses[0]['address_2'] != '') ? $addresses[0]['address_2'].'<?php echo "\r\n" ?>' : '' ?>
<?php echo $addresses[0]['town'] . " " . $addresses[0]['postcode'] . " " . $addresses[0]['state'] ?><?php echo "\r\n" ?>
<?php echo format_country($addresses[0]['country']) ?><?php echo "\r\n" ?>
<?php if($addresses[0]['phone'] != ''): ?>
<?php echo __('Phone') ?>: <?php echo $addresses[0]['phone'] ?><?php echo "\r\n" ?>
<?php endif; ?>
<?php if($addresses[0]['instructions'] != ''): ?>
<?php echo __('Instructions') ?>: <?php echo $addresses[0]['instructions'] ?><?php echo "\r\n" ?>
<?php endif; ?>
<?php echo "\r\n" ?>** <?php echo strtoupper(__('Shipping'))." **\r\n" ?>
<?php echo $addresses[$shipping]['first_name'] . " " . $addresses[$shipping]['last_name'] ?><?php echo "\r\n" ?>
<?php echo $addresses[$shipping]['address_1'] ?><?php echo "\r\n" ?>
<?php echo ($addresses[$shipping]['address_2'] != '') ? $addresses[$shipping]['address_2'].'<?php echo "\r\n" ?>' : '' ?>
<?php echo $addresses[$shipping]['town'] . " " . $addresses[$shipping]['postcode'] . " " . $addresses[$shipping]['state'] ?><?php echo "\r\n" ?>
<?php echo format_country($addresses[$shipping]['country']) ?><?php echo "\r\n" ?>
<?php if($addresses[$shipping]['phone'] != ''): ?>
<?php echo __('Phone') ?>: <?php echo $addresses[$shipping]['phone'] ?><?php echo "\r\n" ?>
<?php endif; ?>
<?php if($addresses[$shipping]['instructions'] != ''): ?>
<?php echo __('Instructions') ?>: <?php echo $addresses[$shipping]['instructions'] ?><?php echo "\r\n" ?>
<?php endif; ?>
<?php endif; ?>

------------------------------------------------------------
<?php echo strtoupper(__('Products Ordered')) ?><?php echo "\r\n" ?>
------------------------------------------------------------
<?php if(count($rt_shop_order->getProductsData()) > 0): ?>
<?php $sub_total = 0; ?>
<?php $i=1; foreach($rt_shop_order->getProductsData() as $product): ?>
<?php echo __('Description'); ?>: <?php echo $product['title'] ?> <?php echo ($product['variations'] != '' && !empty ($product['variations'])) ? sprintf('(%s)',$product['variations']) : ''; ?><?php echo "\r\n" ?>
<?php echo __('SKU'); ?>: <?php echo $product['sku']; ?><?php echo "\r\n" ?>
<?php echo __('Price (each)'); ?>: <?php echo format_currency($product['charge_price'], $product['currency']); ?><?php echo "\r\n" ?>
<?php echo __('Quantity'); ?>: <?php echo $product['quantity']; ?><?php echo "\r\n" ?>
<?php echo __('Price'); ?>: <?php echo format_currency($product['quantity']*$product['charge_price'], $product['currency']); ?><?php echo "\r\n" ?>
<?php if($i < count($rt_shop_order->getProductsData())): ?>
.................................................................................
<?php endif; ?>
<?php $sub_total += $product['charge_price']*$product['quantity']; ?>
<?php $i++; endforeach; ?>
------------------------------------------------------------
<?php echo strtoupper(__('Sub-Total')); ?>: <?php echo format_currency($sub_total, sfConfig::get('app_rt_currency', 'AUD')); ?><?php echo "\r\n" ?>
<?php if(sfConfig::get('app_rt_shop_tax_mode','inclusive') == 'exclusive'): ?>
<?php echo strtoupper(__('Taxes')); ?>: <?php echo format_currency($rt_shop_order->getTaxCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?><?php echo "\r\n" ?>
<?php endif; ?>
<?php if($rt_shop_order->getPromotionReduction() > 0): ?>
<?php echo strtoupper(__('Promotion')); ?> (<?php echo $promotion['title'] ?>): -<?php echo format_currency($rt_shop_order->getPromotionReduction(), sfConfig::get('app_rt_currency', 'AUD')); ?><?php echo "\r\n" ?>
<?php endif; ?>
<?php if($rt_shop_order->getVoucherReduction() > 0): ?>
<?php echo strtoupper(__('Voucher')); ?> (<?php echo $voucher['title'] ?>): -<?php echo format_currency($rt_shop_order->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')); ?><?php echo "\r\n" ?>
<?php endif; ?>
<?php echo strtoupper(__('Shipping rate')); ?>: <?php echo ($rt_shop_order->getShippingCharge() != false) ? format_currency($rt_shop_order->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')) : __('undefined'); ?><?php echo "\r\n" ?>
<?php
$includes_message = '';
if(sfConfig::get('app_rt_shop_tax_rate', 0) > 0 && sfConfig::get('app_rt_shop_tax_mode') == 'inclusive')
{
  $includes_message = sprintf('(includes %s tax)',format_currency(rtShopCartManager::calcTaxComponent($rt_shop_order->getTotalCharge()), sfConfig::get('app_rt_currency', 'AUD')));
}
?>
<?php echo strtoupper(__('Total')); ?> <?php echo $includes_message  ?>: <?php echo format_currency($rt_shop_order->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?>
<?php else: ?>
<?php echo __('No products added to cart'); ?>
<?php endif; ?>