<?php $status = sfConfig::get('app_rt_shop_order_status_types',array('pending', 'paid', 'picking', 'dispatch', 'sent')); ?>
<form id ="rtShopOrderStatusUpdate" action="/rtShopOrderAdmin/statusUpdate-?id=<?php echo $rt_shop_order->getId() ?>" method="post">
  <p><strong><?php echo __('Adjust order status') ?>:</strong><br/>
    <select name="rt-shop-order-status">
    <?php foreach($status as $key => $value): ?>
      <option value="<?php echo $value ?>" <?php echo ($rt_shop_order->getStatus() == $value) ? "selected" : '' ?>><?php echo ucfirst($value) ?></option>
    <?php endforeach; ?>
  </select> <button class="status"><?php echo __('Update Status') ?></button></p>
</form>
<script type="text/javascript">
	$(function() {
    $("#rtShopOrderStatusUpdate .status").button({
      icons: { primary: 'ui-icon-refresh' }
    }).submit();
	});
</script>