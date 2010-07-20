<?php use_helper('I18N', 'rtAdmin', 'Number') ?>

<h1><?php echo __('Listing Shop Vouchers') ?></h1>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li><button class="new"><?php echo __('Create New Voucher') ?></button></li>
  <li><button class="new-batch"><?php echo __('Create Batch of New Vouchers') ?></button></li>
</ul>

<script type="text/javascript">
	$(function() {

    $("#rtPrimaryTools .new").button({
      icons: { primary: 'ui-icon-plus' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopVoucherAdmin/new') ?>'; });

    $("#rtPrimaryTools .new-batch").button({
      icons: { primary: 'ui-icon-plusthick' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopVoucherAdmin/batchCreate') ?>'; });

	});
</script>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<table>
  <thead>
    <tr>
      <th><?php echo __('Title') ?></th>
      <th><?php echo __('Code') ?></th>
      <th><?php echo __('Reduction') ?></th>
      <th><?php echo __('Count') ?></th>
      <th><?php echo __('Mode') ?></th>
      <th><?php echo __('Batch') ?></th>
      <th><?php echo __('Created at') ?></th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($pager->getResults() as $rt_shop_voucher): ?>
    <tr>
      <td><a href="<?php echo url_for('rtShopVoucherAdmin/edit?id='.$rt_shop_voucher->getId()) ?>"><?php echo $rt_shop_voucher->getTitle() ?></a></td>
      <td><code><?php echo $rt_shop_voucher->getCode() ?></code></td>
      <td><?php echo $rt_shop_voucher->isPercentageOff() ? $rt_shop_voucher->getReductionValue() . '%' : format_currency($rt_shop_voucher->getReductionValue(), sfConfig::get('app_rt_currency', 'USD'), $sf_user->getCulture()) ?></td>
      <td><?php echo $rt_shop_voucher->getCount() ?></td>
      <td><?php echo $rt_shop_voucher->getMode() ?></td>
      <td><code><?php echo $rt_shop_voucher->getBatchReference() ? link_to($rt_shop_voucher->getBatchReference(), 'rtShopVoucherAdmin/batchShow?id='.$rt_shop_voucher->getBatchReference()) : '--' ?></code></td>
      <td><?php echo $rt_shop_voucher->getCreatedAt() ?></td>
      <td>
        <ul class="rt-admin-tools">
          <li><?php echo rt_button_edit(url_for('rtShopVoucherAdmin/edit?id='.$rt_shop_voucher->getId())) ?></li>
          <li><?php echo rt_button_delete(url_for('rtShopVoucherAdmin/delete?id='.$rt_shop_voucher->getId())) ?></li>
        </ul>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include_partial('rtAdmin/pagination', array('pager' => $pager)); ?>