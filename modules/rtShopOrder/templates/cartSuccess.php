<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite', 'Number') ?>

<div class="rt-rtShopOrder rt-cart rt-primary-container">
  
  <h1><?php echo __(sfConfig::get('app_rt_shop_cart_title', 'Cart')) ?></h1>

  <div class="rt-container">
    <?php include_partial('breadcrumb', array()) ?>
    <?php include_partial('rtShopOrder/flashes') ?>

    <?php if(count($order->Stocks) > 0): ?>
      <?php echo form_tag(url_for('@rt_shop_order_update'), array('id' => 'order_cart', 'name' => 'order_cart')); ?>
        <table class="rt-shop-order-summary">
          <thead>
            <tr>
              <th><?php echo __('Preview'); ?></th>
              <th><?php echo __('Description'); ?></th>
              <th><?php echo __('Options'); ?></th>
              <th><?php echo __('Each'); ?></th>
              <th><?php echo __('Quantity'); ?></th>
              <th><?php echo __('Total'); ?></th>
              <th><?php echo __('Actions') ?></th>
            </tr>
          </thead>
          <?php include_partial('cart', array('rt_shop_order' => $order)) ?>
        </table>
        <div>
          <?php echo link_to(sprintf('%s', __('Continue Shopping')),'/rtShopProduct', 'class="rt_shop_cart_actions_continue button"') ?>
          <button type="submit" name="_update_quantities" class="button"><?php echo __('Update Quantities') ?></button>
          <button type="submit" name="_proceed_to_checkout" class="button"><?php echo __('Proceed to Checkout') ?></button>
        </div>
      </form>
    <?php else: ?>
      <p><?php echo sprintf('%s %s',__('You have nothing in your cart, '),link_to(__('go buy something!'),'/rtShopProduct')); ?></p>
    <?php endif; ?>
  </div>
</div>