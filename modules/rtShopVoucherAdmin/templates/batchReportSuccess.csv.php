<?php $vouchers = $sf_data->getRaw('vouchers') ?>
<?php $keys = $sf_data->getRaw('key_order') ?>
<?php echo implode(', ',$keys) . "\r\n"; ?>
<?php foreach($vouchers as $voucher): ?>
<?php echo implode(', ',$voucher) . "\r\n"; ?>
<?php endforeach; ?>