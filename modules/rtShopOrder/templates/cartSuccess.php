<?php

/** @var rtShopOrder $rt_shop_order */
/** @var rtShopCartManager $rt_shop_cart_manager */

use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite', 'Number');

slot('rt-title', ucwords(__(sfConfig::get('rt_shop_cart_name', 'shopping bag'))));

$rt_shop_order = $rt_shop_cart_manager->getOrder();

$cart_used = (count($rt_shop_order->Stocks) > 0 || $rt_shop_cart_manager->getVoucherManager()->hasSessionVoucher()) ? true : false;
?>

<div class="rt-section rt-shop-order">

  <!-- <div class="rt-section-tools-header rt-admin-tools"></div> -->
      
  <div class="rt-section-header">
    
    <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
      <h1><?php echo ucwords(__(sfConfig::get('rt_shop_cart_name', 'shopping bag'))) ?></h1>
    <?php endif; ?>
    
    <?php if($cart_used): ?>
      <?php include_partial('breadcrumb', array()) ?>  
    <?php endif; ?>
      
  </div>

  <div class="rt-section-content">
    
    <?php if($cart_used): ?>

      <form action="<?php echo url_for('@rt_shop_order_update') ?>" method="post">

        <?php //include_partial('breadcrumb', array()) ?>

        <div class="rt-shop-order-prefix">
          <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-cart-prefix','sf_cache_key' => 'shop-cart-prefix')); ?>
        </div>

        <table class="rt-shop-order-summary">
          <thead>
            <tr>
              <th><?php echo __('Preview'); ?></th>
              <th><?php echo __('Description'); ?></th>
              <th><?php echo __('Actions') ?></th>
              <th><?php echo __('Each'); ?></th>
              <th><?php echo __('Quantity'); ?></th>
              <th><?php echo __('Total'); ?></th>
            </tr>
          </thead>
          <?php include_partial('cart', array('rt_shop_cart_manager' => $rt_shop_cart_manager, 'stock_exceeded' => isset($stock_exceeded) ? $stock_exceeded : array(), 'update_quantities' => isset($update_quantities) ? $update_quantities : array())) ?>
          <tfoot>

            <?php if(is_object($rt_shop_order) && count($rt_shop_order->Stocks) > 0): ?>
              <tr class="rt-shop-cart-update">
                <td colspan="4">&nbsp;</td>
                <td colspan="1"><button type="submit" name="_update_quantities" class="button rt-shop-order-update"><?php echo __('Update') ?></button></td>
                <td colspan="1">&nbsp;</td>
              </tr>
            <?php endif; ?>

            <tr class="rt-shop-cart-subtotal">
              <th colspan="5"><?php echo __('Subtotal'); ?>:</th>
              <td colspan="1"><?php echo format_currency($rt_shop_cart_manager->getSubTotal(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
            </tr>

           <?php if(!$rt_shop_cart_manager->isTaxModeInclusive()): ?>
            <tr class="rt-shop-cart-tax">
              <th colspan="5"><?php echo __('Tax'); ?>:</th>
              <td colspan="1"><?php echo format_currency($rt_shop_cart_manager->getTaxCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
            </tr>
            <?php endif; ?>

            <?php if($rt_shop_cart_manager->getPromotion()): ?>
            <tr class="rt-shop-cart-promotion">
              <th colspan="5"><?php echo $rt_shop_cart_manager->getPromotion()->getTitle(); ?>:</th>
              <td colspan="1">-<?php echo format_currency($rt_shop_cart_manager->getPromotionReduction(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
            </tr>
            <?php endif; ?>

            <?php if($rt_shop_cart_manager->getShippingCharge() > 0): ?>
            <tr class="rt-shop-cart-shipping">
              <th colspan="5"><?php echo __('Shipping') ?>:</th>
              <td colspan="1"><?php echo format_currency($rt_shop_cart_manager->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
            </tr>
            <?php endif; ?>

            <tr class="rt-shop-cart-voucher" <?php echo ($rt_shop_cart_manager->getVoucherReduction() > 0) ? "" : "style=\"display:none\""; ?>>
              <th colspan="5"><?php echo __('Voucher') ?> (<span id="rt-shop-voucher-title"><?php echo ($rt_shop_cart_manager->getVoucher() != false) ? $rt_shop_cart_manager->getVoucher()->getTitle() : ""; ?></span>):</th>
              <td colspan="1">-<?php echo format_currency($rt_shop_cart_manager->getVoucherReduction(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
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
              <th colspan="5"><?php echo __('Total'); ?> <?php echo $includes_message  ?>:</th>
              <td colspan="1"><?php echo format_currency($rt_shop_cart_manager->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
            </tr>
          </tfoot>
        </table>

        <p class="rt-section-tools-submit">
          
          <button type="submit" name="_proceed_to_checkout" class="button rt-shop-order-proceed"><?php echo __('Proceed to Checkout') ?></button>
          <?php echo __('Or') ?>, <?php echo link_to(__('continue shopping'),'rt_shop_category_index', array(), array('class' => 'rt_shop_cart_actions_continue button rt-shop-order-continue-shopping')) ?>
          
        </p>
        
      </form>
      <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-cart-suffix','sf_cache_key' => 'shop-cart-suffix')); ?>
    <?php else: ?>

      <div class="rt-shop-order-suffix">
        <?php
          $shop_cart_empty_prompt = '<p>'  . sprintf('%s %s',__('You have nothing in your '.sfConfig::get('rt_shop_cart_name', 'shopping bag').' yet, '),link_to(__('time to start shopping!'),'rt_shop_category_index')) . '</p>';
        ?>
        <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-cart-empty-prompt','sf_cache_key' => 'shop-cart-empty-prompt', 'default' => $shop_cart_empty_prompt)); ?>
      </div>

    <?php endif; ?>    
    
  </div>

<!--  <div class="rt-section-tools-footer"></div>-->

</div>