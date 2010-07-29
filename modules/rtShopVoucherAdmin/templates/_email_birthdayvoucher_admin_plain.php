<?php use_helper('I18N') ?>
<?php echo __('Vouchers created') ?>: <?php echo count($users) ?>

<?php echo __('Batch reference') ?>: <?php echo $batch_reference ?>

<?php if($range_to == NULL): ?>
<?php echo __('For birthday') ?>: <?php echo $batch_reference ?>
<?php else: ?>
<?php echo __('For birthday range') ?>: <?php echo $range_from ?> <?php echo __('to') ?> <?php echo $range_to ?>
<?php endif; ?>

<?php echo __('Voucher value') ?>: <?php echo $value ?>

<?php echo __('Date created') ?>: <?php echo date("Y-m-d") ?>