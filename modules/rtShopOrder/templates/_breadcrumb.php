<?php $action = $sf_request->getParameter('action'); ?>
<ul class="rt-container rt-shop-order-breadcrumb">
  <li class="<?php
    if($action === 'cart') {
      echo 'here';
    } else if ($action === 'checkout') {
      echo 'past-present';
    } else {
      echo 'past';
    }
  ?>">
    <?php echo link_to_if($action !== 'cart', 'Cart','@rt_shop_order_cart') ?>
  </li>
  <li class="<?php
    switch ($action) {
      case 'checkout':
        echo "here";
        break;
      case 'address':
        echo "past-present";
        break;
      case 'cart':
        echo "";
        break;
      default:
        echo "past";
        break;
    }
  ?>">
    <?php echo link_to_if($action !== 'checkout', 'Checkout','@rt_shop_order_checkout') ?>
  </li>
  <li class="<?php
    switch ($action) {
      case 'address':
        echo "here";
        break;
      case 'payment':
        echo "past-present";
        break;
      default:
        echo "";
        break;
    }
  ?>">
    <?php echo link_to_if($action !== 'address', 'Address','@rt_shop_order_address') ?>
  </li>
  <li class="<?php
    switch ($action) {
      case 'payment':
        echo "present";
        break;
      default:
        echo "future";
        break;
    }
  ?>">
    <?php echo link_to_if($action !== 'payment', 'Payment','@rt_shop_order_payment') ?>
  </li>
</ul>