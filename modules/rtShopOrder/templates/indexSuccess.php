<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<?php $heading_tag = sfConfig::has('app_rt_shop_order_title') ? 'h2' : 'h1' ?>
<div class="rt-blog-page-index">
  <?php if(sfConfig::has('app_rt_shop_order_title')): ?>
  <h1><?php echo __(sfConfig::get('app_rt_shop_order_title', 'Order')) ?></h1>
  <?php endif; ?>
  <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
</div>