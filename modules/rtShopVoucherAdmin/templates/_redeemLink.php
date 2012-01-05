<?php use_helper('I18N', 'Text') ?>

<script type="text/javascript">
$(document).ready(function() {
  $('input[name=rt_shop_voucher_redeem_link]').click(function() {
    $('input[name=rt_shop_voucher_redeem_link]').select();
  });
});
</script>

<?php
  //Example link: http://localhost/order/voucher/redeem?code=VA14CFC9&redirect=http://localhost/
  // Current domain
  $domain        = rtSiteToolkit::getCurrentDomain(null, true);
  // Redeem voucher url
  $redeem_route  = str_replace('/frontend_dev.php', '', url_for('rt_shop_order_voucher_redeem'));
  // Query string: code
  $query_string  = '?code='.$rt_shop_voucher->getCode();
  // add redirect to query string
  $query_string .= '&redirect='.str_replace('/frontend_dev.php', '', url_for('@homepage',true));
  // Complete link
  $redeem_link   = $domain.$redeem_route.$query_string;
?>

<input type="text" name="rt_shop_voucher_redeem_link" class="rt-shop-voucher-redeem-link" value="<?php echo $redeem_link ?>">