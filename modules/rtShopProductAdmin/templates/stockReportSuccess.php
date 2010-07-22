<?php use_helper('I18N', 'rtAdmin') ?>

<h1><?php echo __('Stock Report') ?></h1>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li><button class="download-csv"><?php echo __('Download batch as CSV') ?></button></li>
  <li><button class="download-xml"><?php echo __('Download batch as XML') ?></button></li>
  <li><button class="download-json"><?php echo __('Download batch as JSON') ?></button></li>
  <li><button class="cancel"><?php echo __('Cancel/List') ?></button></li>
</ul>
<script type="text/javascript">
	$(function() {
    $("#rtPrimaryTools .download-csv").button({
      icons: { primary: 'ui-icon-transfer-e-w' }
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_stock_report_download?sf_format=csv') ?>'; });

    $("#rtPrimaryTools .download-xml").button({
      icons: { primary: 'ui-icon-transfer-e-w' }
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_stock_report_download?sf_format=xml') ?>'; });

    $("#rtPrimaryTools .download-json").button({
      icons: { primary: 'ui-icon-transfer-e-w' }
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_stock_report_download?sf_format=json') ?>'; });

    $("#rtPrimaryTools .cancel").button({
      icons: { primary: 'ui-icon-cancel' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopProductAdmin/index') ?>'; });

	});
</script>
<?php end_slot(); ?>

<?php $stocks = $sf_data->getRaw('pager') ?>
<?php $keys = $sf_data->getRaw('key_order') ?>
<table>
  <thead>
    <tr>
      <th><?php echo __('Product title') ?></th>
      <th><?php echo __('Product sku') ?></th>
      <th><?php echo __('Stock sku') ?></th>
      <th><?php echo __('Quantity') ?></th>
      <th><?php echo __('Stock Id') ?></th>
      <th><?php echo __('Product Id') ?></th>
      <th><?php echo __('Price retail') ?></th>
      <th><?php echo __('Price promotion') ?></th>
      <th><?php echo __('Price wholesale') ?></th>
      <th><?php echo __('Length') ?></th>
      <th><?php echo __('Width') ?></th>
      <th><?php echo __('Height') ?></th>
      <th><?php echo __('Weight') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($stocks as $stock): ?>
      <tr>
        <?php foreach($keys as $key => $value): ?>
          <td><?php echo ($value == 'p_sku' || $value == 's_sku') ? '<code>' : ''; ?><?php echo $stock[$value] ?><?php echo ($value == 'p_sku' || $value == 's_sku') ? '</code>' : ''; ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include_partial('rtAdmin/pagination', array('pager' => $pager)); ?>