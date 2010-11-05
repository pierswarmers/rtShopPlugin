<?php use_helper('I18N', 'Number', 'rtForm') ?>
<?php $routes = $sf_context->getRouting()->getRoutes() ?>

<?php slot('rt-title', __('Membership')) ?>

<?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
<div class="rt-shop-order-membership-panel rt-shop-checkout-member-login">
  <h2><?php echo __('Already a member?') ?></h2>
  <?php echo get_partial('rtGuardAuth/signin_form', array('form' => $form_user)) ?>
</div>
<?php if (isset($routes['sf_guard_register']) && !sfConfig::get('app_rt_registration_is_private', false)): ?>
<div class="rt-shop-order-membership-panel rt-shop-checkout-register">
  <?php
    $shop_membership_prompt  = '<h2>' . __('New to this site?') . '</h2>';
    $shop_membership_prompt .= '<p>'  . __('Creating an account is simple, free and opens the door to a range of member only benefits.') . '</p>';
  ?>
  <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-membership-prompt','sf_cache_key' => 'shop-membership-prompt', 'default' => $shop_membership_prompt)); ?>
  <p><a href="<?php echo url_for('sf_guard_register') ?>"><?php echo __('Want to register?', null, 'sf_guard') ?></a></p>
</div>
<?php endif; ?>
<div class="rt-shop-order-tools">
  <?php echo link_to(__('Proceed without an account'), 'rt_shop_order_address', array(), array('class' => 'button')) ?>
</div>