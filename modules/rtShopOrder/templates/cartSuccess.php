<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite', 'Number') ?>

<div class="rt-shop-order rt-cart rt-primary-container">
  
  <h1><?php echo ucwords(__(sfConfig::get('rt_shop_cart_name', 'shopping bag'))) ?></h1>

  <?php if(is_object($rt_shop_order) && count($rt_shop_order->Stocks) > 0): ?>
  
  <form action="<?php echo url_for('@rt_shop_order_update') ?>" method="post">
    <div class="rt-container">
      <?php include_partial('breadcrumb', array()) ?>
      <table class="rt-shop-order-summary">
        <thead>
          <tr>
            <th><?php echo __('Preview'); ?></th>
            <th><?php echo __('Description'); ?></th>
            <th><?php echo __('Each'); ?></th>
            <th><?php echo __('Quantity'); ?></th>
            <th><?php echo __('Total'); ?></th>
            <th><?php echo __('Actions') ?></th>
          </tr>
        </thead>
        <?php include_partial('cart', array('rt_shop_cart_manager' => $rt_shop_cart_manager, 'stock_exceeded' => isset($stock_exceeded) ? $stock_exceeded : array(), 'update_quantities' => isset($update_quantities) ? $update_quantities : array())) ?>
      </table>
    </div>

    <div class="rt-container rt-shop-order-tools">
      <?php echo link_to(__('Continue Shopping'),'rt_shop_category_index', array(), array('class' => 'rt_shop_cart_actions_continue button rt-shop-order-continue-shopping')) ?>
      <button type="submit" name="_update_quantities" class="button rt-shop-order-update"><?php echo __('Update Quantities') ?></button>
      <button type="submit" name="_proceed_to_checkout" class="button rt-shop-order-proceed"><?php echo __('Proceed to Checkout') ?></button>
    </div>
  </form>

  <?php else: ?>

    <div class="rt-container">
      <p><?php echo sprintf('%s %s',__('You have nothing in your '.sfConfig::get('rt_shop_cart_name', 'shopping bag').' yet, '),link_to(__('go buy something!'),'rt_shop_category_index')); ?></p>
    </div>
  
  <?php endif; ?>
</div>