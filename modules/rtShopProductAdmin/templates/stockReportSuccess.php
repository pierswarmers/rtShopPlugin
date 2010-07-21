<?php $stocks = $sf_data->getRaw('stocks') ?>
<?php foreach($stocks as $stock): ?>
<?php echo implode(', ',$stock); ?><br/>
<?php endforeach; ?>