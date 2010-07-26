<?php use_helper('I18N', 'rtAdmin', 'Number') ?>

<h1><?php echo __('Order Report') ?></h1>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li class="button-group">
    <button class="download-xml"><?php echo __('Download XML') ?></button>
  </li>
  <li><button class="cancel"><?php echo __('Cancel/List') ?></button></li>
</ul>
<script type="text/javascript">
	$(function() {
    $("#rtPrimaryTools .download-xml").button({
      icons: { primary: 'ui-icon-transfer-e-w' }
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_order_report_download?sf_format=xml') ?>'; });

    $("#rtPrimaryTools .cancel").button({
      icons: { primary: 'ui-icon-cancel' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopOrderAdmin/index') ?>'; });

    $('.button-group').buttonset();

	});
</script>
<?php end_slot(); ?>

<?php $orders = $sf_data->getRaw('orders') ?>
<table>
  <thead>
    <tr>
      <th><?php echo __('Reference') ?></th>
      <th><?php echo __('Status') ?></th>
      <th><?php echo __('Sub') ?></th>
      <th><?php echo __('Ship') ?></th>
      <th colspan="2"><?php echo __('Tax') ?></th>
      <th><?php echo __('Promo') ?></th>
      <th><?php echo __('Voucher') ?></th>
      <th><?php echo __('Total') ?></th>
      <th><?php echo __('Created at') ?></th>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th><?php echo __('Charge') ?></th>
      <th><?php echo __('Comp') ?></th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($pager->getResults() as $order): ?>
      <tr>
        <td><code><?php echo $order['o_reference'] ?></code></td>
        <td><?php echo strtoupper($order['o_status']) ?></td>
        <td><?php echo format_currency($order['o_items_charge'], 'AUD') ?></td>
        <td><?php echo format_currency($order['o_shipping_charge'], 'AUD') ?></td>
        <td><?php echo format_currency($order['o_tax_charge'], 'AUD') ?></td>
        <td><?php echo format_currency($order['o_tax_component'], 'AUD') ?></td>
        <td><?php echo format_currency($order['o_promotion_reduction'], 'AUD') ?></td>
        <td><?php echo format_currency($order['o_voucher_reduction'], 'AUD') ?></td>
        <td><?php echo format_currency($order['o_total_charge'], 'AUD') ?></td>
        <td><?php echo $order['o_created_at'] ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include_partial('rtAdmin/pagination', array('pager' => $pager)); ?>