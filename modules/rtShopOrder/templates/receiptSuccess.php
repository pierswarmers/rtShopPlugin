<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<div class="rt-shop-order rt-receipt rt-primary-container">

  <h1><?php echo ucwords(__(sfConfig::get('rt_shop_receipt_title', 'receipt'))) ?></h1>

  <div class="rt-container">
    <?php
      $order_received = '<h3>' . __('Thank you!') . '</h3>';
      $order_received .= '<p>'  . sprintf('%s%s%s',__('Your order has been received. The order reference is #'),$rt_shop_order->getReference(),__(' and a confirmation email has been sent to you.')) . '</p>';
    ?>
    <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-receipt-message','sf_cache_key' => 'shop-receipt-message', 'default' => $order_received)); ?>
  </div>
</div>