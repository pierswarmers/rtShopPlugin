<?php use_helper('I18N', 'Date', 'Number', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<div class="rt-shop-order rt-address rt-primary-container">
  <h1><?php echo __(sfConfig::get('app_rt_shop_address_title', 'Address')) ?></h1>

  <form action="<?php echo url_for('@rt_shop_order_address') ?>" method="post">
    <div class="rt-container">
      <?php include_partial('breadcrumb', array()) ?>

      <h2><?php echo __('Email address for order') ?></h2>
      <?php  echo $form['email']->renderRow() ?>
      <?php  echo $form->renderHiddenFields() ?>


      <h2><?php echo __('Billing address') ?></h2>

        <?php include_partial('address_form', array('form' => $form['billing_address'])) ?>
 
      <p><label for="shipping_toggle"><?php echo __('Same As Shipping Address:') ?> <input id="shipping_toggle" type="checkbox" name="shipping_toggle" <?php echo ($show_shipping) ? '' : 'checked' ?> /></label></p>

      <div id="steer_shop_billing_address" style="<?php echo ($show_shipping) ? 'display: block' : 'display: none' ?>">
      <h2><?php echo __('Shipping address') ?></h2>
      <?php include_partial('address_form', array('form' => $form['shipping_address'])) ?>
      </div>
    </div>

    <div class="rt-container rt-shop-order-tools">
      <button type="submit" class="steer_shop_address_actions_submit button"><?php echo __('Proceed to payment') ?></button>
    </div>
  </form>
</div>
<script type="text/javascript">
  $(function() {
    $("#shipping_toggle").click(function(){
      $("#steer_shop_billing_address").toggle("fast");
    });
  });
</script>
