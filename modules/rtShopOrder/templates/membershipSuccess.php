<?php use_helper('I18N', 'Number', 'rtForm') ?>
<?php $routes = $sf_context->getRouting()->getRoutes() ?>
<div class="rt-shop-order rt-checkout rt-primary-container">
  <h1><?php echo __('Membership') ?></h1>
  <div class="rt-container">
    <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
    <div class="rt-shop-order-checkout-panel rt-shop-checkout-member-login">
      <h2><?php echo __('Already a member?') ?></h2>
      <?php echo get_partial('rtGuardAuth/signin_form', array('form' => $form_user)) ?>
    </div>
    <?php if (isset($routes['sf_guard_register']) && !sfConfig::get('app_rt_registration_is_private', false)): ?>
    <div class="rt-shop-order-checkout-panel rt-shop-checkout-register">
      <h2><?php echo __('New to this site?') ?></h2>
      <p><?php echo __('Creating an account is simple, free and opens the door to a range of member only benefits.') ?></p>
      <p><a href="<?php echo url_for('sf_guard_register') ?>"><?php echo __('Want to register?', null, 'sf_guard') ?></a></p>
    </div>
    <?php endif; ?>
    <div class="rt-shop-order-checkout-panel rt-shop-checkout-continue">
      <h2><?php echo __('Proceed without an account') ?></h2>
      <p><?php echo __('Of course you can purchase without membership') ?></p>
      <p><?php echo link_to(__('Go to next step'), 'rt_shop_order_address', array(), array('class' => 'button')) ?></p>
    </div>
  </div>
</div>