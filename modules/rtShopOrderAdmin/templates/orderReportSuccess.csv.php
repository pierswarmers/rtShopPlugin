<?php $orders = $sf_data->getRaw('orders') ?>
<?php $keys = $sf_data->getRaw('key_order') ?>
<?php echo implode(', ',$keys) . "\r\n"; ?>
<?php foreach($orders as $order): ?>
<?php echo implode(', ',$order) . "\r\n"; ?>
<?php endforeach; ?>