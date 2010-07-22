<?xml version="1.0" encoding="ISO-8859-1"?>
<?php $stocks = $sf_data->getRaw('stocks') ?>

<stockReport xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<?php foreach($stocks as $stock): ?>
  <stock>
    <?php foreach($stock as $key => $value): ?>
      <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
    <?php endforeach; ?>
  </stock>
<?php endforeach; ?>
</stockReport>