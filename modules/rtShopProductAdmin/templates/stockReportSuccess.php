<?php use_helper('I18N', 'rtAdmin', 'Number') ?>

<h1><?php echo __('Stock Report') ?></h1>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li class="button-group">
    <button class="download-csv"><?php echo __('Download CSV') ?></button>
    <button class="download-xml"><?php echo __('XML') ?></button>
    <button class="download-json"><?php echo __('JSON') ?></button>
  </li>
  <li><button class="cancel"><?php echo __('Cancel/List') ?></button></li>
</ul>
<script type="text/javascript">
	$(function() {
    $("#rtPrimaryTools .download-csv").button({
      icons: { primary: 'ui-icon-transfer-e-w' }
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_stock_report_download?sf_format=csv') ?>'; });

    $("#rtPrimaryTools .download-xml").button({
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_stock_report_download?sf_format=xml') ?>'; });

    $("#rtPrimaryTools .download-json").button({
    }).click(function(){ document.location.href='<?php echo url_for('@rt_shop_stock_report_download?sf_format=json') ?>'; });

    $("#rtPrimaryTools .cancel").button({
      icons: { primary: 'ui-icon-cancel' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopProductAdmin/index') ?>'; });

    $('.button-group').buttonset();

	});
</script>
<?php end_slot(); ?>

<?php $stocks = $sf_data->getRaw('pager') ?>
<?php $keys = $sf_data->getRaw('key_order') ?>
<table>
  <thead>
    <tr>
      <th><?php echo __('Product Title') ?></th>
      <th><?php echo __('Product SKU') ?></th>
      <th><?php echo __('Stock SKU') ?></th>
      <th><?php echo __('Quantity') ?></th>
      <th><?php echo __('Price Retail') ?></th>
      <th><?php echo __('Price Promotion') ?></th>
      <th><?php echo __('Price Wholesale') ?></th>
      <th><?php echo __('Variations') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($pager->getResults() as $stock): ?>
      <?php
        $variations = '';
        foreach($stock['rtShopStockToVariation'] as $variation)
        {
          $variations .= $variation['rtShopVariation']['title'].' / ';
        }
      ?>
      <tr>
        <td><?php echo $stock['rtShopProduct']['title'] ?></td>
        <td><code><?php echo $stock['rtShopProduct']['sku'] ?></code></td>
        <td><code><?php echo link_to($stock['sku'] === '' ? 'N/A' : $stock['sku'],'rtShopProductAdmin/stock?id='.$stock['rtShopProduct']['id']) ?></code></td>
        <td><?php echo $stock['quantity'] ?></td>
        <td><?php echo format_currency($stock['price_retail'], sfConfig::get('app_rt_currency', 'USD')) ?></td>
        <td><?php echo format_currency($stock['price_promotion'], sfConfig::get('app_rt_currency', 'USD')) ?></td>
        <td><?php echo format_currency($stock['price_wholesale'], sfConfig::get('app_rt_currency', 'USD')) ?></td>
        <td><?php echo substr($variations, 0, -3) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include_partial('rtAdmin/pagination', array('pager' => $pager)); ?>