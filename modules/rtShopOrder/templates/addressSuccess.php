<?php

use_helper('I18N', 'Number', 'rtForm');

slot('rt-title', __(sfConfig::get('app_rt_shop_address_title', 'Address')));

?>

<div class="rt-section rt-shop-order rt-shop-order-address">

<!--  <div class="rt-section-tools-header rt-admin-tools"></div>-->
    
  <div class="rt-section-header">
    <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
      <h1><?php echo __(sfConfig::get('app_rt_shop_address_title', 'Address')) ?></h1>
    <?php endif; ?>

    <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
  </div>

  <div class="rt-section-content">
    
    <form action="<?php echo url_for('@rt_shop_order_address') ?>" method="post">
      <fieldset>
        <legend><?php echo __('Your Email Address') ?></legend>
        <ul class="rt-form-schema">
          <li class="rt-form-row"><?php echo $form['email_address']->renderLabel() ?><div class="rt-form-field"><?php echo $form['email_address']->renderError() ?><?php echo $form['email_address'] ?></div></li>
        </ul>
      </fieldset>

      <?php  echo $form->renderHiddenFields() ?>

      <fieldset>
        <legend><?php echo __('Billing Address') ?></legend>
        <?php include_partial('address_form', array('form' => $form_billing)) ?>
      </fieldset>

      <p><label for="shipping_toggle"><?php echo __('Shipping address is the same as billing address') ?>: <input id="shipping_toggle" type="checkbox" name="shipping_toggle" <?php echo ($show_shipping) ? '' : 'checked' ?> /></label></p>

      <div id="steer_shop_billing_address" style="<?php echo ($show_shipping) ? 'display: block' : 'display: none' ?>">
        <fieldset>
          <legend><?php echo __('Shipping Address') ?></legend>
          <?php include_partial('address_form', array('form' => $form_shipping)) ?>
        </fieldset>
      </div>

      <p class="rt-section-tools-submit">
        <button type="submit"><?php echo __('Proceed to payment') ?></button>
      </p>
    </form>    
    
  </div>

<!--  <div class="rt-section-tools-footer"></div>-->

</div>

<script type="text/javascript">
  $(function() {
    $("#shipping_toggle").click(function(){
      $("#steer_shop_billing_address").toggle("fast");
    });
  });
</script>