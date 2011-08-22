<?php

use_helper('I18N', 'Number', 'rtForm', 'rtTemplate');

slot('rt-title', __('Membership'));

$routes = $sf_context->getRouting()->getRoutes();

?>

<div class="rt-section rt-shop-order rt-shop-order-membership-panel">

  <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
    <div class="rt-section-header">
        <h1><?php echo __('Membership') ?></h1>
    </div>
  <?php endif; ?>

  <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>

  <div class="rt-section-content">

    <?php rt_get_snippet('rt-shop-membership-prefix'); ?>
    
    <div class="rt-shop-checkout-member-login">
      <form action="<?php echo url_for('@sf_guard_signin') ?>" method="post">
        <h2><?php echo __('Already a member?') ?></h2>
        <?php echo $form_user->renderHiddenFields() ?>
        <fieldset>
          <ul class="rt-form-schema">
            <?php echo $form_user; ?>
          </ul>
        </fieldset>
        <p class="rt-section-tools-submit">
          <button type="submit"><?php echo __('Sign in', null, 'sf_guard') ?></button>
        </p>
      </form>      
    </div>
    
    <?php if (isset($routes['sf_guard_register']) && !sfConfig::get('app_rt_registration_is_private', false)): ?>
      <div class="rt-shop-checkout-register">
        <?php
          $shop_membership_prompt .= '<p>'  . __('Would you like to create an account? Registering is simple, free and opens the door to a range of member only benefits.') . '</p>';
        ?>
        <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-membership-prompt','sf_cache_key' => 'shop-membership-prompt', 'default' => $shop_membership_prompt)); ?>

        <p class="rt-section-tools-submit">
          <button onclick="document.location.href='<?php echo url_for('sf_guard_register') ?>';"><?php echo __('Register a new account') ?></button> &nbsp;
          <?php echo __('Or') ?>, <?php echo link_to(__('proceed without an account'), 'rt_shop_order_address', array(), array('class' => '')) ?>
        </p>
      </div>
    <?php endif; ?>

    <?php rt_get_snippet('rt-shop-membership-suffix'); ?>
    
  </div>

<!--  <div class="rt-section-tools-footer"></div>-->

</div>