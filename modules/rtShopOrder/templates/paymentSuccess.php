<?php use_helper('I18N', 'Date', 'Number', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<?php slot('rt-title') ?>
<?php echo __(sfConfig::get('app_rt_shop_payment_title', 'Payment')) ?>
<?php end_slot(); ?>

<form action="<?php echo url_for('@rt_shop_order_payment') ?>" method="post" id="rt-shop-order-payment-form">

  <?php include_partial('breadcrumb', array()) ?>

  <?php echo $form->renderHiddenFields(); ?>

  <div class="rt-shop-payment-prefix">
    <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-payment-prefix','sf_cache_key' => 'shop-payment-prefix')); ?>
  </div>

  <?php if($rt_shop_cart_manager->getTotalCharge() > 0): ?>
    <fieldset>
      <legend><?php echo __('Voucher Details') ?></legend>
      <ul class="rt-form-schema">
        <li class="rt-form-row"><?php echo $form['voucher_code']->renderLabel() ?><div class="rt-form-field"><?php echo $form['voucher_code'] ?></div></li>
      </ul>
      <p class="rt-form-tools"><button id="apply-voucher"><?php echo __('Apply Voucher') ?></button><span id="voucher-message"></span></p>
    </fieldset>
  <?php endif; ?>

  <h2><?php echo __('Order Summary') ?></h2>
  <table class="rt-shop-order-summary">
    <thead>
      <tr>
        <th><?php echo __('Preview'); ?></th>
        <th><?php echo __('Description'); ?></th>
        <th><?php echo __('Each'); ?></th>
        <th><?php echo __('Quantity'); ?></th>
        <th><?php echo __('Total'); ?></th>
      </tr>
    </thead>
    <?php include_partial('cart', array('rt_shop_cart_manager' => $rt_shop_cart_manager, 'editable' => false)) ?>
    <tfoot>
      <tr class="rt-shop-cart-subtotal">
        <th colspan="4"><?php echo __('Subtotal'); ?>:</th>
        <td><?php echo format_currency($rt_shop_cart_manager->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
      
     <?php if(!$rt_shop_cart_manager->isTaxModeInclusive()): ?>
      <tr class="rt-shop-cart-tax">
        <th colspan="4"><?php echo __('Tax'); ?>:</th>
        <td><?php echo format_currency($rt_shop_cart_manager->getTaxCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
      <?php endif; ?>

      <?php if($rt_shop_cart_manager->getPromotion()): ?>
      <tr class="rt-shop-cart-promotion">
        <th colspan="4"><?php echo $rt_shop_cart_manager->getPromotion()->getTitle(); ?>:</th>
        <td>-<?php echo format_currency($rt_shop_cart_manager->getPromotionReduction(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
      <?php endif; ?>

      <?php if($rt_shop_cart_manager->getShippingCharge() > 0): ?>
      <tr class="rt-shop-cart-shipping">
        <th colspan="4"><?php echo __('Shipping') ?>:</th>
        <td><?php echo format_currency($rt_shop_cart_manager->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
      <?php endif; ?>

      <tr class="rt-shop-cart-voucher" <?php echo ($rt_shop_cart_manager->getVoucherReduction() > 0) ? "" : "style=\"display:none\""; ?>>
        <th colspan="4"><?php echo __('Voucher') ?> (<span class="rt-shop-voucher-title"><?php echo ($rt_shop_cart_manager->getVoucher() != false) ? $rt_shop_cart_manager->getVoucher()->getTitle() : ""; ?></span>):</th>
        <td>-<span class="rt-shop-voucher-reduction"><?php echo format_currency($rt_shop_cart_manager->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')); ?></span></td>
      </tr>

      <?php
        $includes_message = '';

        if(sfConfig::get('app_rt_shop_tax_rate', 0) > 0 && sfConfig::get('app_rt_shop_tax_mode') == 'inclusive')
        {
          $includes_message = sprintf('(includes %s tax)',format_currency($rt_shop_cart_manager->getTaxComponent(), sfConfig::get('app_rt_currency', 'AUD')));
        }

        if($rt_shop_cart_manager->getPromotion())
        {
          $promo_message = sprintf('(Includes %s)',$rt_shop_cart_manager->getPromotion()->getTitle());
        }
      ?>
      <tr class="rt-shop-cart-total">
        <th colspan="4"><?php echo __('Total'); ?> <?php echo $includes_message  ?>:</th>
        <td class="rt-shop-total"><?php echo format_currency($rt_shop_cart_manager->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
      </tr>
    </tfoot>
  </table>

    <?php include_partial('rtShopOrder/addresses', array('rt_shop_cart_manager' => $rt_shop_cart_manager)) ?>

    <span class="rt-shop-payment-creditcard" style="display:block">
      <?php if($rt_shop_cart_manager->getTotalCharge() > 0): ?>
        <fieldset>
          <legend><?php echo __('Credit Card Details') ?></legend>
          <ul class="rt-form-schema">
            <?php echo $form_cc ?>
          </ul>
        </fieldset>
      <?php endif; ?>
    </span>

    <h3><?php echo __('Total to be charged to your credit card: '); ?> <span id="order-total-charge" class="rt-shop-total"><?php echo format_currency($rt_shop_cart_manager->getTotalCharge(), sfConfig::get('app_rt_shop_payment_currency','AUD')); ?></span></h3>

    <div class="rt-shop-payment-suffix">
      <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-payment-suffix','sf_cache_key' => 'shop-payment-suffix')); ?>
    </div>

    <p class="rt-shop-order-tools"><button id="rt-submit-order"><?php echo __('Place your order') ?></button></p>
</form>

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
        $('.rt-shop-cart-voucher').hide();
      }

      $(this).attr('disabled', true);

      $('#voucher-message').html('<span class="loading">Checking voucher, updating totals...</span>');

      $.ajax({
        type: "POST",
        url: '<?php echo url_for('rt_shop_order_check_voucher', array('sf_format'=>'json')) ?>',
        data: ({
          code : $(this).attr('value')
        }),
        dataType: "json",
        success: function(data) {
          $('span.loading, span.success, span.error').hide();
          if(data['id'] != '') {
            $('#voucher-message').html('<span class="success">'+data['title']+'</span>');
            $('.rt-shop-voucher-title').html(data['title']);
            $('.rt-shop-voucher-reduction').html(data['reduction_formatted']);
            $('.rt-shop-cart-voucher').show();
          } else if(data['error'] != '') {
            $('.rt-shop-cart-voucher').hide();
            $('#voucher-message').html('<span class="error">Voucher could not be added!</span>');
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