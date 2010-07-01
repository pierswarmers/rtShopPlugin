<?php use_helper('I18N', 'Number', 'rtForm') ?>

<?php $heading_tag = sfConfig::has('app_rt_shop_checkout_title') ? 'h2' : 'h1' ?>
<div class="rt-shop-order rt-checkout rt-primary-container">
  <h1><?php echo __(sfConfig::get('app_rt_shop_checkout_title', 'Checkout')) ?></h1>

  <div class="rt-container">
    <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
  </div>
</div>