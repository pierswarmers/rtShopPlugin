<?php

use_helper('I18N', 'Number', 'rtForm');

slot('rt-title', __('Membership'));

$routes = $sf_context->getRouting()->getRoutes();

?>

<div class="rt-section rt-shop-order rt-shop-order-membership-panel">

<!--  <div class="rt-section-tools-header rt-admin-tools"></div>-->
    
  <div class="rt-section-header">
    <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
      <h1><?php echo __('Membership') ?></h1>
    <?php endif; ?>
      
    <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
  </div>

  <div class="rt-section-content">
    
    <div class="rt-shop-checkout-member-login">
      <h2><?php echo __('Already a member?') ?></h2>
      <form action="<?php echo url_for('@sf_guard_signin') ?>" method="post">
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
          $shop_membership_prompt  = '<h2>' . __('New to this site?') . '</h2>';
          $shop_membership_prompt .= '<p>'  . __('Creating an account is simple, free and opens the door to a range of member only benefits.') . '</p>';
        ?>
        <?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-membership-prompt','sf_cache_key' => 'shop-membership-prompt', 'default' => $shop_membership_prompt)); ?>
      </div>
    <?php endif; ?>
    
    <p class="rt-section-tools-submit">
      <button onclick="document.location.href='<?php echo url_for('sf_guard_register') ?>';"><?php echo __('Register a new account') ?></button> &nbsp; 
      <?php echo __('Or') ?>, <?php echo link_to(__('proceed without an account'), 'rt_shop_order_address', array(), array('class' => 'button')) ?>
    </p>    
    
  </div>

<!--  <div class="rt-section-tools-footer"></div>-->

</div>