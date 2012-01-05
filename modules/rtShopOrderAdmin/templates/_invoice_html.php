<?php use_helper('I18N', 'Number', 'rtAdmin') ?>
<?php

$billing_address = $rt_shop_order->getBillingAddress();
$shipping_address = $rt_shop_order->getShippingAddress();

if(!$shipping_address)
{
  $shipping_address = $billing_address;
}

?>
<table>
  <tbody>
    <tr>
      <th style="width:25%"><?php echo __('Order reference') ?>:</th>
      <td style="width:25%"><?php echo $rt_shop_order->getReference() ?></td>
      <td style="width:50%" rowspan="<?php echo $rt_shop_order->getVoucherCode() ? '7' : '6' ?>"><?php echo sfConfig::get('app_rt_invoice_company_address_html',sfConfig::get('app_rt_email_signature_html','')) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Payment transaction ID') ?>:</th>
      <td><?php echo $rt_shop_order->getPaymentTransactionId() ?></td>
    </tr>
    <tr>
      <th><?php echo __('Payment charge') ?>:</th>
      <td><?php echo format_currency($rt_shop_order->getPaymentCharge(), sfConfig::get('app_rt_currency', 'USD')) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Status') ?>:</th>
      <td><?php echo strtoupper($rt_shop_order->getStatus()) ?></td>
    </tr>
    <?php if($rt_shop_order->getVoucherCode()): ?>
      <tr>
        <th><?php echo __('Voucher code') ?>:</th>
        <td><?php echo $rt_shop_order->getVoucherCode() ?></td>
      </tr>
    <?php endif; ?>
    <tr>
      <th><?php echo __('Date') ?>:</th>
      <td><?php echo date("d F Y", strtotime($rt_shop_order->getCreatedAt())) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Email') ?>:</th>
      <td><?php echo mail_to($rt_shop_order->getEmailAddress()) ?></td>
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
      <td style="width:50%">
        <?php if($billing_address): ?>
        <?php echo $billing_address['first_name'] . " " . $billing_address['last_name'] ?><br/>
        <?php echo $billing_address['address_1'] ?><br/>
        <?php echo ($billing_address['address_2'] != '') ? $billing_address['address_2'].'<br/>' : '' ?>
        <?php echo $billing_address['town'] . " " . $billing_address['postcode'] . " " . $billing_address['state'] ?><br/>
        <?php echo format_country($billing_address['country']) ?>
        <?php endif; ?>
      </td>
      <td style="width:50%">
        <?php if($shipping_address): ?>
        <?php echo $shipping_address['first_name'] . " " . $shipping_address['last_name'] ?><br/>
        <?php echo $shipping_address['address_1'] ?><br/>
        <?php echo ($shipping_address['address_2'] != '') ? $shipping_address['address_2'].'<br/>' : '' ?>
        <?php echo $shipping_address['town'] . " " . $shipping_address['postcode'] . " " . $shipping_address['state'] ?><br/>
        <?php echo format_country($shipping_address['country']) ?>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <td>
        <?php if($billing_address && $billing_address['phone'] != ''): ?>
          <?php echo __('Phone') ?>: <?php echo $billing_address['phone'] ?>
        <?php endif; ?>
      </td>
      <td>
        <?php if($shipping_address && $shipping_address['phone'] != ''): ?>
          <?php echo __('Phone') ?>: <?php echo $shipping_address['phone'] ?>
        <?php endif; ?>
      </td>
    </tr>
    <?php if(($billing_address && $billing_address['instructions'] != '') || ($shipping_address && $shipping_address['instructions'] != '')): ?>
      <tr>
        <td>
          <?php if($billing_address['instructions'] != ''): ?>
            <?php echo __('Instructions') ?>: <?php echo $billing_address['instructions'] ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if($shipping_address['instructions'] != ''): ?>
            <?php echo __('Instructions') ?>: <?php echo $shipping_address['instructions'] ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>
<?php include_partial('rtShopOrderAdmin/archive', array('rt_shop_order' => $rt_shop_order)) ?>