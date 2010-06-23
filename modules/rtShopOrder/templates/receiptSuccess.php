<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<div class="rt-rtShopOrder rt-receipt rt-primary-container">

  <?php $heading_tag = sfConfig::has('app_rt_shop_receipt_title') ? 'h2' : 'h1' ?>
  <?php if(sfConfig::has('app_rt_shop_receipt_title')): ?>
    <h1><?php echo __(sfConfig::get('app_rt_shop_receipt_title', 'receipt')) ?></h1>
  <?php endif; ?>

  <div class="rt-container">


  </div>
</div>