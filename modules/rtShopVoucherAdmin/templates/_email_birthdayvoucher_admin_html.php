<?php use_helper('I18N') ?>
<strong><?php echo __('Vouchers created') ?>:</strong> <?php echo count($users) ?><br />
<strong><?php echo __('Batch reference') ?>:</strong> <?php echo $batch_reference ?><br />
<?php if($range_to == NULL): ?>
<strong><?php echo __('For birthday') ?>:</strong> <?php echo $batch_reference ?><br />
<?php else: ?>
<strong><?php echo __('For birthday range') ?>:</strong> <?php echo $range_from ?> <?php echo __('to') ?> <?php echo $range_to ?><br />
<?php endif; ?>
<strong><?php echo __('Voucher value') ?>:</strong> <?php echo $value ?><br />
<strong><?php echo __('Date created') ?>:</strong> <?php echo date("Y-m-d") ?><br />