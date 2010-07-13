<?php use_helper('I18N', 'Date', 'Number', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>

<div class="rt-shop-order rt-payment rt-primary-container">
  <h1><?php echo __(sfConfig::get('app_rt_shop_payment_title', 'Payment')) ?></h1>

  <form action="<?php echo url_for('@rt_shop_order_payment') ?>" method="post">
    <div class="rt-container">
      <?php include_partial('breadcrumb', array()) ?>
      <?php echo $form->renderHiddenFields(); ?>

      <h2><?php echo __('Products Ordered') ?></h2>
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
            <td><?php echo format_currency($rt_shop_cart_manager->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
          </tr>
        </tfoot>
      </table>

      <h2><?php echo __('Voucher Details') ?></h2>
      <table>
        <tbody>
          <tr>
            <th><label for="rt_shop_order_voucher_voucher_code"><?php echo __('Voucher code') ?>:</label></th>
            <td><?php echo $form['voucher_code']->render() ?></td>
          </tr>
        </tbody>
      </table>

      <script type="text/javascript">
        $(function() {
          $('#rt_shop_order_voucher_voucher_code').blur(function() {

            $('span.loading, span.success, span.error').hide();
            
            if($(this).attr('value') == '') {
              //return false;
            }

            $(this).attr('disabled', true);

            $(this).after('<span class="loading">Checking voucher, updating totals...</span>');

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
                  $('#rt_shop_order_voucher_voucher_code').after('<span class="success">'+data['title']+'</span>');
                } else if(data['error'] != '') {
                  $('#rt_shop_order_voucher_voucher_code').after('<span class="error">Voucher could not be added!</span>');
                }
                $('#rt_shop_order_voucher_voucher_code').removeAttr('disabled');
                $('#order-total-charge').html(data['total_charge_formatted']);
              }
            });
          });
        });
      </script>
      
      <h2><?php echo __('Creditcard Details') ?></h2>
      <table>
        <tbody>
          <?php echo $form_cc ?>
        </tbody>
      </table>

      <h3><?php echo __('Total to be charged to your credit card: '); ?> <span id="order-total-charge"><?php echo format_currency($rt_shop_cart_manager->getTotalCharge(), sfConfig::get('app_rt_shop_payment_currency','AUD')); ?></span></h3>
    </div>

    <div class="rt-container rt-shop-order-tools">
      <button type="submit" class="rt_shop_payment_actions_submit button"><?php echo __('Place your order') ?></button>
    </div>
  </form>
</div>