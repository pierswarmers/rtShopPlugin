<?php use_helper('I18N', 'Number', 'rtAdmin') ?>

<h1><?php echo __('Listing Orders') ?></h1>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li><button class="reports"><?php echo __('View order report') ?></button></li>
  <li><button class="graphs-quarterly"><?php echo __('View sales charts') ?></button></li>
</ul>
<script type="text/javascript">
	$(function() {
    $("#rtPrimaryTools .reports").button({
      icons: { primary: 'ui-icon-transfer-e-w' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopOrderAdmin/orderReport') ?>'; });
    $("#rtPrimaryTools .graphs-quarterly").button({
      icons: { primary: 'ui-icon-image' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopOrderAdmin/graph') ?>'; });
	});
</script>
<h2><?php echo __('Sales Summary') ?></h2>
<dl class="rt-admin-summary-panel clearfix">
  <dt class="rt-admin-primary"><?php echo __('Today') ?> (<?php echo $stats['today']['count'] ?>)</dt>
  <dd class="rt-admin-primary"><?php echo format_currency($stats['today']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
  <dt><?php echo __('Yesterday') ?> (<?php echo $stats['yesterday']['count'] ?>)</dt>
  <dd><?php echo format_currency($stats['yesterday']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
  <dt><?php echo __('This Month') ?> (<?php echo $stats['month_current']['count'] ?>)</dt>
  <dd><?php echo format_currency($stats['month_current']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
  <dt><?php echo __('Last Month') ?> (<?php echo $stats['month_last']['count'] ?>)</dt>
  <dd><?php echo format_currency($stats['month_last']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
  <dt><?php echo __('Total') ?> (<?php echo $stats['total']['count'] ?>)</dt>
  <dd><?php echo format_currency($stats['total']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
  <dt><?php echo __('Average Order Value') ?></dt>
  <dd><?php echo format_currency(($stats['total']['count'] > 0) ? $stats['total']['revenue']/$stats['total']['count'] : 0.0, sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
</dl>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<table>
  <thead>
    <tr>
      <th><?php echo __('Details') ?></th>
      <th><?php echo __('Total') ?></th>
      <th><?php echo __('Status') ?></th>
      <th><?php echo __('Email address') ?></th>
      <th><?php echo __('Created at') ?></th>
      <th><?php echo __('Actions') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($pager->getResults() as $rt_shop_order): ?>
      <tr>
        <td><a href="<?php echo url_for('rtShopOrderAdmin/show?id='.$rt_shop_order->getId()) ?>"><code><?php echo $rt_shop_order->getReference() ?></code></a></td>
        <td><?php echo format_currency($rt_shop_order->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
        <td><?php echo strtoupper($rt_shop_order->getStatus()) ?></td>
        <td><?php echo $rt_shop_order->getEmailAddress(); ?></td>
        <td><?php echo $rt_shop_order->getCreatedAt() ?></td>
        <td>
        <ul class="rt-admin-tools">
          <li><?php echo rt_button_show(url_for('rtShopOrderAdmin/show?id='.$rt_shop_order->getId())) ?></li>
          <!--- <li><?php echo rt_button_edit(url_for('rtShopOrderAdmin/edit?id='.$rt_shop_order->getId())) ?></li> --->
          <li><?php echo rt_button_delete(url_for('rtShopOrderAdmin/delete?id='.$rt_shop_order->getId())) ?></li>
        </ul>
      </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include_partial('rtAdmin/pagination', array('pager' => $pager)); ?>