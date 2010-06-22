<?php use_helper('I18N', 'Date', 'Number', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<?php $heading_tag = sfConfig::has('app_rt_shop_checkout_title') ? 'h2' : 'h1' ?>
<div class="rt-shop-order-checkout">
  <?php if(sfConfig::has('app_rt_shop_checkout_title')): ?>
    <h1><?php echo __(sfConfig::get('app_rt_shop_checkout_title', 'Checkout')) ?></h1>
  <?php endif; ?>

  <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
  <?php include_partial('rtShopOrder/flashes') ?>

</div>