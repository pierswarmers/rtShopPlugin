<?php use_helper('I18N', 'Date', 'Number', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<div class="rt-rtShopOrder rt-payment rt-primary-container">

  <h1><?php echo __(sfConfig::get('app_rt_shop_payment_title', 'Payment')) ?></h1>

  <div class="rt-container">

    <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
    <?php include_partial('rtShopOrder/flashes') ?>

    <?php echo form_tag(url_for('@rt_shop_order_payment'), array('id' => 'order_payment', 'name' => 'order_payment')); ?>
      <h2><?php echo __('Voucher Details') ?></h2>
      <?php include_partial('form', array('form' => $voucher_form)) ?>

      <h2><?php echo __('Creditcard Details') ?></h2>
      <?php include_partial('form', array('form' => $creditcard_form)) ?>

      <h3><?php echo __('Total to be charged to your credit card: '); ?> <?php echo format_currency((isset($total)) ? $total :$rt_shop_order->getGrandTotalPrice(), sfConfig::get('app_rt_shop_payment_currency','AU')); ?></h3>

      <div class="rt-shop-address-actions">
        <button type="submit" class="rt_shop_payment_actions_submit button"><?php echo __('Place your order') ?></button>
      </div>
    </form>
  </div>

</div>