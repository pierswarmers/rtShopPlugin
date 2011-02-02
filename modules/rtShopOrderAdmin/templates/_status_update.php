<?php
$is_dispatch_user = $sf_user->hasCredential(sfConfig::get('app_rt_shop_order_dispatch_credential', 'admin_shop_order_dispatch'));

$status = array(rtShopOrder::STATUS_PAID, rtShopOrder::STATUS_PICKING, rtShopOrder::STATUS_SENDING, rtShopOrder::STATUS_SENT);

if($is_dispatch_user)
{
  array_shift($status);
}
?>
<form id ="rtShopOrderStatusUpdate" action="<?php echo url_for('rtShopOrderAdmin/statusUpdate') ?>?id=<?php echo $rt_shop_order->getId() ?>" method="post">
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