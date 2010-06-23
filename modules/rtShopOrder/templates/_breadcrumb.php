<?php $action = $sf_request->getParameter('action'); ?>
<p>
  <?php echo link_to_if($action !== 'cart', 'Cart','@rt_shop_order_cart') ?> >
  <?php echo link_to_if($action !== 'checkout', 'Checkout','@rt_shop_order_checkout') ?> >
  <?php echo link_to_if($action !== 'address', 'Address','@rt_shop_order_address') ?> >
  <?php echo link_to_if($action !== 'payment', 'Payment','@rt_shop_order_payment') ?>
</p>