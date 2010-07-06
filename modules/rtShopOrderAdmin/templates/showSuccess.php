<?php use_helper('I18N', 'Number', 'rtAdmin') ?>

<h1><?php echo __('Show Shop Order') ?></h1>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li><button class="cancel"><?php echo __('Cancel/List') ?></button></li>
</ul>

<script type="text/javascript">
	$(function() {
    $("#rtPrimaryTools .cancel").button({
      icons: { primary: 'ui-icon-cancel' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopOrderAdmin/index') ?>'; });
	});
</script>

<?php end_slot(); ?>

<?php
  $billing_address = $rt_shop_order->getBillingAddressArray();
  $shipping_address = $rt_shop_order->getShippingAddressArray();
?>

<table>
  <tbody>
    <tr>
      <th style="width:25%"><?php echo __('Order') ?></th>
      <td style="width:25%"><?php echo "#".$rt_shop_order->getReference() ?></td>
      <td style="width:50%" rowspan="4"><?php echo nl2br(sfConfig::get('app_rt_address','')) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Date') ?></th>
      <td><?php echo date("M d Y H:i", strtotime($rt_shop_order->getCreatedAt())) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Status') ?></th>
      <td><?php echo strtoupper($rt_shop_order->getStatus()) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Email') ?></th>
      <td><?php echo mail_to($rt_shop_order->getEmail()) ?></td>
    </tr>
    <!--- <tr>
      <th><?php echo __('Payment Method') ?></th>
      <td><?php //echo sfConfig::get('app_rt_shop_payment_class','rtShopPayment') ?></td>
    </tr>
    <tr>
      <th><?php echo __('Shipping Method') ?></th>
      <td><?php //echo sfConfig::get('app_rt_shop_shipping_class','rtShopShipping') ?></td>
    </tr> --->
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
      <td style="width:50%"><?php echo $billing_address[0]['first_name'] . " " . $billing_address[0]['last_name'] ?><br/>
          <?php echo $billing_address[0]['address_1'] ?><br/>
          <?php echo ($billing_address[0]['address_2'] != '') ? $billing_address[0]['address_2'].'<br/>' : '' ?>
          <?php echo $billing_address[0]['town'] . " " . $billing_address[0]['postcode'] . " " . $billing_address[0]['state'] ?>  <br/>
          <?php echo $billing_address[0]['country'] ?></td>
      <td style="width:50%"><?php if(count($shipping_address) > 0): ?>
        <?php echo $shipping_address[0]['first_name'] . " " . $shipping_address[0]['last_name'] ?><br/>
        <?php echo $shipping_address[0]['address_1'] ?><br/>
        <?php echo ($shipping_address[0]['address_2'] != '') ? $shipping_address[0]['address_2'].'<br/>' : '' ?>
        <?php echo $shipping_address[0]['town'] . " " . $shipping_address[0]['postcode'] . " " . $shipping_address[0]['state'] ?>  <br/>
        <?php echo $shipping_address[0]['country'] ?>
      <?php else: ?>
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
      <th colspan="5"><?php echo sprintf(__('Products [%s]'), count($rt_shop_order->Stocks)); ?></th>
    </tr>
  </thead>
  <?php include_partial('rtShopOrderAdmin/archive', array('rt_shop_order' => $rt_shop_order)) ?>
</table>