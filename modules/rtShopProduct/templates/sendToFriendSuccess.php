<?php

use_helper('I18N');

slot('rt-title',__('Send To A Friend'));

?>

<div class="rt-section rt-shop-product rt-shop-product-sent-to-friend">
    
  <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
    <div class="rt-section-header">
      <h1><?php echo __('Send To A Friend') ?></h1>
    </div>
  <?php endif; ?>

  <div class="rt-section-content">
    
    <form action="<?php echo url_for('rt_shop_send_to_friend') ?>" method="post">
      <?php echo $form->renderHiddenFields() ?>
      <fieldset>
      <legend><?php echo __('Add details') ?></legend>
        <ul class="rt-form-schema">
          <?php echo $form; ?>
        </ul>
      </fieldset>
      <p class="rt-form-tools"><button><?php echo __('Send email') ?></button></p>
    </form>
    
  </div>

  <?php if($rt_shop_product): ?>
    <div class="rt-section-tools-footer">
      <?php echo link_to(__('Back to product'), 'rt_shop_product_show', $rt_shop_product) ?>
    </div>
  <?php endif; ?>
  
</div>