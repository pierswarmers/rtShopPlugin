<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite', 'Number') ?>
<div class="rt-shop-order rt-cart rt-primary-container">
  
  <h1><?php echo ucwords(__(sfConfig::get('rt_shop_cart_name', 'shopping bag'))) ?></h1>

  

  <?php if(is_object($rt_shop_order) && count($rt_shop_order->Stocks) > 0): ?>
  
  <form action="<?php echo url_for('@rt_shop_order_update') ?>" method="post">
    <div class="rt-container">
      <?php include_partial('breadcrumb', array()) ?>
    </div>
    <div class="rt-container rt-shop-order-prefix">
      <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-cart-prefix','sf_cache_key' => 'shop-cart-prefix')); ?>
    </div>
    <div class="rt-container">
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

         <?php if(!$rt_shop_cart_manager->isTaxModeInclusive()): ?>
          <tr class="rt-shop-cart-tax">
            <th colspan="5"><?php echo __('Tax'); ?>:</th>
            <td colspan="2"><?php echo format_currency($rt_shop_cart_manager->getTaxCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
          </tr>
          <?php endif; ?>
          
          <?php if($rt_shop_cart_manager->getPromotion()): ?>
          <tr class="rt-shop-cart-promotion">
            <th colspan="5"><?php echo $rt_shop_cart_manager->getPromotion()->getTitle(); ?>:</th>
            <td colspan="2">-<?php echo format_currency($rt_shop_cart_manager->getPromotionReduction(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
          </tr>
          <?php endif; ?>

          <?php if($rt_shop_cart_manager->getShippingCharge() > 0): ?>
          <tr class="rt-shop-cart-shipping">
            <th colspan="5"><?php echo __('Shipping') ?>:</th>
            <td colspan="2"><?php echo format_currency($rt_shop_cart_manager->getShippingCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
          </tr>
          <?php endif; ?>

 

          <?php
          $includes_message = '';
          
          if(sfConfig::get('app_rt_shop_tax_rate', 0) > 0 && sfConfig::get('app_rt_shop_tax_mode') == 'inclusive')
          {
            $includes_message = sprintf('(includes %s tax)',format_currency($rt_shop_cart_manager->getTaxCharge(), sfConfig::get('app_rt_currency', 'AUD')));
          }

          if($rt_shop_cart_manager->getPromotion())
          {
            $promo_message = sprintf('(Includes %s)',$rt_shop_cart_manager->getPromotion()->getTitle());
          }
          
          ?>
          <tr class="rt-shop-cart-total">
            <th colspan="5"><?php echo __('Total'); ?> <?php echo $includes_message  ?>:</th>
            <td colspan="2"><?php echo format_currency($rt_shop_cart_manager->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="rt-container rt-shop-order-tools">
      <?php echo link_to(__('Continue Shopping'),'rt_shop_category_index', array(), array('class' => 'rt_shop_cart_actions_continue button rt-shop-order-continue-shopping')) ?>
      <button type="submit" name="_update_quantities" class="button rt-shop-order-update"><?php echo __('Update Quantities') ?></button>
      <button type="submit" name="_proceed_to_checkout" class="button rt-shop-order-proceed"><?php echo __('Proceed to Checkout') ?></button>
    </div>
  </form>
  <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-cart-suffix','sf_cache_key' => 'shop-cart-suffix')); ?>
  <?php else: ?>

    <div class="rt-container rt-shop-order-suffix">
      <?php

      $shop_cart_empty_prompt = '<p>'  . sprintf('%s %s',__('You have nothing in your '.sfConfig::get('rt_shop_cart_name', 'shopping bag').' yet, '),link_to(__('time to start shopping!'),'rt_shop_category_index')) . '</p>';

      ?>
      <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-cart-empty-prompt','sf_cache_key' => 'shop-cart-empty-prompt', 'default' => $shop_cart_empty_prompt)); ?>
    </div>
  
  <?php endif; ?>
</div>