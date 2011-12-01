<?php

/** @var rtShopCartManager $rt_shop_cart_manager */

use_helper('I18N', 'Date', 'Number', 'rtText', 'rtForm', 'rtDate', 'rtSite', 'rtTemplate');

slot('rt-title', __(sfConfig::get('app_rt_shop_payment_title', 'Payment')));

?>

<div class="rt-section rt-shop-order rt-shop-order-payment">

<!--  <div class="rt-section-tools-header rt-admin-tools"></div>-->

  <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
  <div class="rt-section-header">
      <h1><?php echo __(sfConfig::get('app_rt_shop_payment_title', 'Payment')) ?></h1>
  </div>
  <?php endif; ?>
  
  <?php include_partial('breadcrumb', array()) ?>

  <div class="rt-section-content">
    
    <form action="<?php echo url_for('@rt_shop_order_payment') ?>" method="post" id="rt-shop-order-payment-form">

      <?php echo $form->renderHiddenFields(); ?>

      <?php rt_get_snippet('rt-shop-payment-prefix'); ?>
      
      <h2><?php echo __('Order Summary') ?></h2>
      <table class="rt-shop-order-summary">
        <?php include_partial('cart', array('rt_shop_cart_manager' => $rt_shop_cart_manager, 'editable' => false)) ?>
        <tfoot>
          <tr class="rt-shop-cart-subtotal">
            <th colspan="3"><?php echo __('Subtotal'); ?>:</th>
            <td><?php echo format_currency($rt_shop_cart_manager->getSubTotal(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
          </tr>

         <?php if(!$rt_shop_cart_manager->isTaxModeInclusive()): ?>
          <tr class="rt-shop-cart-tax">
            <th colspan="3"><?php echo __('Tax'); ?>:</th>
            <td><?php echo format_currency($rt_shop_cart_manager->getTaxCharge(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
          </tr>
          <?php endif; ?>

          <?php if($rt_shop_cart_manager->getPromotion()): ?>
          <tr class="rt-shop-cart-promotion">
            <th colspan="3"><?php echo $rt_shop_cart_manager->getPromotion()->getTitle(); ?>:</th>
            <td>-<?php echo format_currency($rt_shop_cart_manager->getPromotionReduction(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
          </tr>
          <?php endif; ?>

          <?php if($rt_shop_cart_manager->getShippingCharge() > 0): ?>
          <tr class="rt-shop-cart-shipping">
            <th colspan="3"><?php echo __('Shipping') ?>:</th>
            <td><?php echo format_currency($rt_shop_cart_manager->getShippingCharge(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
          </tr>
          <?php endif; ?>

          <tr class="rt-shop-cart-voucher" <?php echo ($rt_shop_cart_manager->getVoucher()) ? "" : "style=\"display:none\""; ?>>
            <th colspan="3"><?php echo __('Voucher') ?> (<span class="rt-shop-voucher-title"><?php echo ($rt_shop_cart_manager->getVoucher() != false) ? $rt_shop_cart_manager->getVoucher()->getTitle() : ""; ?></span>):</th>
            <td>-<span class="rt-shop-voucher-reduction"><?php echo format_currency($rt_shop_cart_manager->getVoucherReduction(), sfConfig::get('app_rt_currency', 'USD')); ?></span></td>
          </tr>

          <?php
            $includes_message = '';

            if(sfConfig::get('app_rt_shop_tax_rate', 0) > 0 && sfConfig::get('app_rt_shop_tax_mode') == 'inclusive')
            {
              $includes_message = sprintf('(includes %s tax)',format_currency($rt_shop_cart_manager->getTaxComponent(), sfConfig::get('app_rt_currency', 'USD')));
            }

            if($rt_shop_cart_manager->getPromotion())
            {
              $promo_message = sprintf('(Includes %s)',$rt_shop_cart_manager->getPromotion()->getTitle());
            }
          ?>
          <tr class="rt-shop-cart-total">
            <th colspan="3"><?php echo __('Total'); ?> <?php echo $includes_message  ?>:</th>
            <td class="rt-shop-total"><?php echo format_currency($rt_shop_cart_manager->getTotalCharge(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
          </tr>
        </tfoot>
      </table>

      <?php include_partial('rtShopOrder/addresses', array('rt_shop_cart_manager' => $rt_shop_cart_manager)) ?>

      <div class="rt-shop-cart-voucher">
      <?php if($rt_shop_cart_manager->getTotalCharge() > 0): ?>
        <fieldset>
          <h2><?php echo __('Voucher or Promotion Details') ?></h2>
          <p>If applicable enter your voucher or promotion code to apply your discount.</p>
          <p>
            <?php if($form['voucher_code']->hasError()) : ?><span class="error"><?php echo $form['voucher_code']->getError(); ?></span> <br/><?php endif; ?>
            <?php echo $form['voucher_code'] ?>
            <button id="apply-voucher"><?php echo __('Apply Code') ?></button>
          </p>
          <div id="voucher-message"></div>
        </fieldset>
      <?php endif; ?>
      </div>

      <div class="rt-shop-payment-creditcard">
      <?php if($rt_shop_cart_manager->getTotalCharge() > 0): ?>
        <fieldset>
          <h2><?php echo __('Credit Card Details') ?></h2>
          <ul class="rt-form-schema">
            <?php echo $form_cc ?>
          </ul>
        </fieldset>
      <?php endif; ?>
      </div>

      <h3><?php echo __('Total to be charged to your credit card: '); ?> <span id="order-total-charge" class="rt-shop-total"><?php echo format_currency($rt_shop_cart_manager->getTotalCharge(), sfConfig::get('app_rt_currency','USD')); ?></span></h3>

      <p class="rt-section-tools-submit">
        <button id="rt-submit-order"><?php echo __('Place your order') ?></button>
      </p>
    </form>

    <?php rt_get_snippet('rt-shop-payment-suffix'); ?>

  </div>

<!--  <div class="rt-section-tools-footer"></div>-->

</div>

<script type="text/javascript">
  $(function() {

    $('#rt-submit-order').click(function(){ 
      $(this).attr("disabled",true).html("<?php echo __('Processing your order, please be patient') ?>...").addClass("disabled");
      $('#rt-shop-order-payment-form').submit();
    });

    $('#apply-voucher').click(function(){ return false;});

    $('#rt_shop_order_voucher_voucher_code').blur(function() {

      $('span.loading, span.success, span.error').hide();

      if($(this).attr('value') == '') {
        $('tr.rt-shop-cart-voucher').hide();
      }

      $(this).attr('disabled', true);

      $('#voucher-message').html('<span class="loading">Checking code...</span>');

      $.ajax({
        type: "POST",
        url: '<?php echo url_for('rt_shop_order_check_voucher', array('sf_format'=>'json')) ?>',
        data: ({
          code : $(this).attr('value')
        }),
        dataType: "json",
        success: function(data) {
          $('span.loading, span.success, span.error').hide();

          $('span.rt-shop-cart-total').html(data.total_charge_formatted);

          if(data['id'] != '') {
            $('#voucher-message').html('<span class="success">'+data['title']+'</span>');
            $('.rt-shop-voucher-title').html(data['title']);

            var voucher = $('#rt_shop_order_voucher_voucher_code').attr('value');
            $('#rt_shop_order_voucher_voucher_code').attr('value', $('#rt_shop_order_voucher_voucher_code').attr('value').replace('#', ''));
            $('.rt-shop-voucher-reduction').html(data['reduction_formatted']);
            $('.rt-shop-cart-voucher').show();
          } else if(data['error'] != '') {
            $('tr.rt-shop-cart-voucher').hide();
            $('#voucher-message').html('<span class="error">Code doesn\'t validate!</span>');
          }
          if(data['total_charge'] == 0)
          {
            $('.rt-shop-payment-creditcard').hide();
          }
          $('#rt_shop_order_voucher_voucher_code').removeAttr('disabled');
          $('.rt-shop-total').html(data['total_charge_formatted']);
        }
      });
    });
  });
</script>