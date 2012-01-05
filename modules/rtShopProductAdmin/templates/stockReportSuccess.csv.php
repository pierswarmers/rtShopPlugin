<?php $stocks = $sf_data->getRaw('stocks') ?>
<?php $keys = $sf_data->getRaw('key_order') ?>
<?php echo implode(', ',$keys) . "\r\n"; ?>
<?php foreach($stocks as $stock): ?>
<?php echo implode(', ',$stock) . "\r\n"; ?>
<?php endforeach; ?>