<?php

use_helper('I18N', 'rtAdmin');

slot('rt-title', __('New Gift Voucher'));

?>

<div class="rt-section rt-shop-voucher rt-shop-voucher-new">
    
  <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
    <div class="rt-section-header">
      <h1><?php echo __('New Gift Voucher') ?></h1>
    </div>
  <?php endif; ?>

  <div class="rt-section-content">
    
    <?php include_partial('form', array('form' => $form)) ?>    
    
  </div>

</div>