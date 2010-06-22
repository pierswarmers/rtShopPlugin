<?php use_helper('I18N', 'Date', 'Number', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<?php $heading_tag = sfConfig::has('app_rt_shop_address_title') ? 'h2' : 'h1' ?>
<div class="rt-shop-order-address">
  <?php if(sfConfig::has('app_rt_shop_address_title')): ?>
    <h1><?php echo __(sfConfig::get('app_rt_shop_address_title', 'address')) ?></h1>
  <?php endif; ?>

  <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
  <?php include_partial('rtShopOrder/flashes') ?>

  <?php echo form_tag(url_for('@rt_shop_order_address'), array('id' => 'order_address', 'name' => 'order_address')); ?>
    <h2><?php echo __('Email address for order') ?></h2>
    <?php include_partial('form', array('form' => $order_form)) ?>

    <h2><?php echo __('Shipping address') ?></h2>
    <?php include_partial('form', array('form' => $shipping_order_form)) ?>

    <h2><?php echo __('Billing address') ?></h2>
    <p>
      <label for="billing_address_shown"><?php echo __('Same As Shipping Address:') ?> <input id="billing_address_shown" type="checkbox" name="billing_address_shown" <?php echo ($billing_address_shown) ? 'checked' : '' ?> /></label>
    </p>

    <div id="steer_shop_billing_address" style="<?php echo ($billing_address_shown) ? 'display: none' : 'display: block'; ?>">
      <?php include_partial('form', array('form' => $billing_order_form)) ?>
    </div>

    <div class="rt-shop-address-actions">
      <button type="submit" class="steer_shop_address_actions_submit button"><?php echo __('Proceed to payment') ?></button>
    </div>
  </form>
</div>
<script type="text/javascript">
  $(document).ready(function()
  {
    $("#billing_address_shown").click(function(){
    if ($("#billing_address_shown").is(":checked"))
    {
      $("#steer_shop_billing_address").hide("fast");
    } else {
      $("#steer_shop_billing_address").show("fast");
    }
    });
   });
</script>