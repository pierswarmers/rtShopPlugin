<?php use_helper('I18N', 'Date', 'Number', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<div class="rt-shop-order rt-payment rt-primary-container">
  <h1><?php echo __(sfConfig::get('app_rt_shop_payment_title', 'Payment')) ?></h1>

  <form action="<?php echo url_for('@rt_shop_order_payment') ?>" method="post">
    <div class="rt-container">
      <?php include_partial('breadcrumb', array()) ?>

      <h2><?php echo __('Voucher Details') ?></h2>
      <?php include_partial('form', array('form' => $form)) ?>
      
      <h2><?php echo __('Creditcard Details') ?></h2>
      <?php include_partial('form', array('form' => $form_cc)) ?>

      <h3><?php echo __('Total to be charged to your credit card: '); ?> <?php echo format_currency($rt_shop_cart_manager->getTotalCharge(), sfConfig::get('app_rt_shop_payment_currency','AUD')); ?></h3>
    </div>

    <div class="rt-container rt-shop-order-tools">
      <button type="submit" class="rt_shop_payment_actions_submit button"><?php echo __('Place your order') ?></button>
    </div>
  </form>
</div>