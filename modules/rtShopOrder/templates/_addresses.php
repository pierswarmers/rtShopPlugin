<?php

$billing_address = $rt_shop_cart_manager->getBillingAddress(); 
$shipping_address = $rt_shop_cart_manager->getShippingAddress();

if(!$shipping_address)
{
  $shipping_address = $billing_address;
}

?>
<h2><?php echo __('Address Details') ?></h2>
<table class="rt-shop-order-addresses">
  <thead>
    <tr>
      <th><?php echo __('Billing Address'); ?><span><?php echo link_to(__('Change'),'rt_shop_order_address') ?></span></th>
      <th><?php echo __('Shipping Address'); ?><span><?php echo link_to(__('Change'),'rt_shop_order_address') ?></span></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php if($billing_address): ?>
        <?php echo $billing_address['first_name'] . " " . $billing_address['last_name'] ?><br/>
        <?php echo $billing_address['address_1'] ?><br/>
        <?php echo ($billing_address['address_2'] != '') ? $billing_address['address_2'].'<br/>' : '' ?>
        <?php echo $billing_address['town'] . " " . $billing_address['postcode'] . " " . $billing_address['state'] ?><br/>
        <?php echo format_country($billing_address['country']) ?><br/>
        <?php if($billing_address['phone'] != ''): ?><?php echo __('Phone') ?>: <?php echo $billing_address['phone'] ?><br/><?php endif; ?>
        <?php if($billing_address['instructions'] != ''): ?><?php echo __('Instructions') ?>: <?php echo $billing_address['instructions'] ?><?php endif; ?>
      <?php endif; ?></td>
      <td><?php if($shipping_address): ?>
        <?php echo $shipping_address['first_name'] . " " . $shipping_address['last_name'] ?><br/>
        <?php echo $shipping_address['address_1'] ?><br/>
        <?php echo ($shipping_address['address_2'] != '') ? $shipping_address['address_2'].'<br/>' : '' ?>
        <?php echo $shipping_address['town'] . " " . $shipping_address['postcode'] . " " . $shipping_address['state'] ?><br/>
        <?php echo format_country($shipping_address['country']) ?><br/>
        <?php if($shipping_address['phone'] != ''): ?><?php echo __('Phone') ?>: <?php echo $shipping_address['phone'] ?><br/><?php endif; ?>
        <?php if($shipping_address['instructions'] != ''): ?><?php echo $shipping_address['instructions'] ?><?php endif; ?>
      <?php endif; ?></td>
    </tr>
  </tbody>
</table>