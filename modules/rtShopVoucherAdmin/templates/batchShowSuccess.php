<?php use_helper('I18N', 'rtAdmin', 'Number') ?>

<h1><?php echo __('Shop Voucher Batch Details') ?></h1>

<?php $batch = $batch[0] ?>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li class="button-group">
    <button class="download-csv"><?php echo __('Download CSV') ?></button>
    <button class="download-xml"><?php echo __('XML') ?></button>
    <button class="download-json"><?php echo __('JSON') ?></button>
  </li>
  <li><button class="cancel"><?php echo __('Cancel/List') ?></button></li>
</ul>

<p><?php echo __('Or') ?>, <?php echo link_to('delete all vouchers in this batch', 'rtShopVoucherAdmin/batchDelete?id='.$batch['batch_reference'], array('method' => 'delete', 'confirm' => 'Are you sure?')) ?></p>

<script type="text/javascript">
	$(function() {

    $("#rtPrimaryTools .download-csv").button({
      icons: { primary: 'ui-icon-transfer-e-w' }
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_voucher_report_download?sf_format=csv&id='.$batch['batch_reference']) ?>'; });

    $("#rtPrimaryTools .download-xml").button({
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_voucher_report_download?sf_format=xml&id='.$batch['batch_reference']) ?>'; });

    $("#rtPrimaryTools .download-json").button({
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_voucher_report_download?sf_format=json&id='.$batch['batch_reference']) ?>'; });

    $("#rtPrimaryTools .cancel").button({
      icons: { primary: 'ui-icon-cancel' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopVoucherAdmin/index') ?>'; });
    $('.button-group').buttonset();
	});
</script>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<table>
  <tbody>
    <tr>
      <th><?php echo __('Title') ?></th>
      <td><?php echo $batch['title'] ?></td>
    </tr>
    <tr>
      <th><?php echo __('Batch') ?></th>
      <td><code><?php echo $batch['batch_reference'] ?></code></td>
    </tr>
    <tr>
      <th><?php echo __('Batch Size') ?></th>
      <td><?php echo $batch['count'] ?></td>
    </tr>
    <tr>
      <th><?php echo __('Reduction') ?></th>
      <td><?php echo $batch['reduction_type'] == rtShopPromotion::REDUCTION_TYPE_PERCENTAGE ? $batch['reduction_value'] . '%' : format_currency($batch['reduction_value'], sfConfig::get('app_rt_currency', 'USD'), $sf_user->getCulture()) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Created at') ?></th>
      <td><?php echo $batch['created_at'] ?></td>
    </tr>
  </tbody>
</table>
