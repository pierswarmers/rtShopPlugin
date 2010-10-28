<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<?php slot('rt-title') ?>
<?php echo __(sfConfig::get('app_rt_shop_order_title', 'Order')) ?>
<?php end_slot(); ?>

<?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>