<?xml version="1.0" encoding="ISO-8859-1"?>
<?php $vouchers = $sf_data->getRaw('vouchers') ?>

<batchVoucherReport xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<?php foreach($vouchers as $voucher): ?>
  <stock>
    <?php foreach($voucher as $key => $value): ?>
      <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
    <?php endforeach; ?>
  </stock>
<?php endforeach; ?>
</batchVoucherReport>