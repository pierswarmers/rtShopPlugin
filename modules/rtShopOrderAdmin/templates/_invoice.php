<?php use_helper('I18N', 'Number', 'rtAdmin') ?>
<?php $billing_address = $rt_shop_order->getBillingAddressArray(); ?>
<?php $shipping_address = $rt_shop_order->getShippingAddressArray(); ?>
<table>
  <tbody>
    <tr>
      <th style="width:25%"><?php echo __('Order reference') ?>:</th>
      <td style="width:25%"><?php echo "#".$rt_shop_order->getReference() ?></td>
      <td style="width:50%" rowspan="7"><?php echo nl2br(sfConfig::get('app_rt_company_address','')) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Payment transaction ID') ?>:</th>
      <td><?php echo $rt_shop_order->getPaymentTransactionId() ?></td>
    </tr>
    <tr>
      <th><?php echo __('Payment charge') ?>:</th>
      <td><?php echo format_currency($rt_shop_order->getPaymentCharge(), 'AUD') ?></td>
    </tr>
    <tr>
      <th><?php echo __('Status') ?>:</th>
      <td><?php echo strtoupper($rt_shop_order->getStatus()) ?></td>
    </tr>
    <?php if($rt_shop_order->getVoucherCode()): ?>
      <tr>
        <th><?php echo __('Voucher code') ?>:</th>
        <td><code><?php echo $rt_shop_order->getVoucherCode() ?></code></td>
      </tr>
    <?php endif; ?>
    <tr>
      <th><?php echo __('Date') ?>:</th>
      <td><?php echo date("d F Y", strtotime($rt_shop_order->getCreatedAt())) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Email') ?>:</th>
      <td><?php echo mail_to($rt_shop_order->getEmail()) ?></td>
    </tr>
  </tbody>
</table>

<table>
  <thead>
    <tr>
      <th><?php echo __('Billing Address') ?></th>
      <th><?php echo __('Shipping Address') ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td style="width:50%"><?php if(count($billing_address) > 0): ?><?php echo $billing_address[0]['first_name'] . " " . $billing_address[0]['last_name'] ?><br/>
          <?php echo $billing_address[0]['address_1'] ?><br/>
          <?php echo ($billing_address[0]['address_2'] != '') ? $billing_address[0]['address_2'].'<br/>' : '' ?>
          <?php echo $billing_address[0]['town'] . " " . $billing_address[0]['postcode'] . " " . $billing_address[0]['state'] ?>  <br/>
          <?php echo $billing_address[0]['country'] ?><?php endif; ?></td>
      <td style="width:50%"><?php if(count($shipping_address) > 0): ?>
        <?php echo $shipping_address[0]['first_name'] . " " . $shipping_address[0]['last_name'] ?><br/>
        <?php echo $shipping_address[0]['address_1'] ?><br/>
        <?php echo ($shipping_address[0]['address_2'] != '') ? $shipping_address[0]['address_2'].'<br/>' : '' ?>
        <?php echo $shipping_address[0]['town'] . " " . $shipping_address[0]['postcode'] . " " . $shipping_address[0]['state'] ?>  <br/>
        <?php echo $shipping_address[0]['country'] ?>
      <?php elseif(count($billing_address) > 0): ?>
        <?php echo $billing_address[0]['first_name'] . " " . $billing_address[0]['last_name'] ?><br/>
        <?php echo $billing_address[0]['address_1'] ?><br/>
        <?php echo ($billing_address[0]['address_2'] != '') ? $billing_address[0]['address_2'].'<br/>' : '' ?>
        <?php echo $billing_address[0]['town'] . " " . $billing_address[0]['postcode'] . " " . $billing_address[0]['state'] ?>  <br/>
        <?php echo $billing_address[0]['country'] ?>
      <?php endif; ?></td>
    </tr>
  </tbody>
</table>
<table>
  <thead>
    <tr>
      <th colspan="5"><?php echo sprintf(__('Products ordered [%s]'), count($rt_shop_order->getClosedProducts())); ?></th>
    </tr>
  </thead>
  <?php include_partial('rtShopOrderAdmin/archive', array('rt_shop_order' => $rt_shop_order)) ?>
</table>