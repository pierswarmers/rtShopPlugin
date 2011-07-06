<?php

use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite');

slot('rt-title', __(sfConfig::get('app_rt_shop_order_title', 'Order')));

?>

<div class="rt-section rt-shop-order">

<!--  <div class="rt-section-tools-header rt-admin-tools"></div>-->
    
  <div class="rt-section-header">
    <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
      <h1><?php echo __(sfConfig::get('app_rt_shop_order_title', 'Order')) ?></h1>
    <?php endif; ?>
      
    <?php include_partial('breadcrumb', array('sf_request' => $sf_request)) ?>
  </div>

  <div class="rt-section-content"></div>

<!--  <div class="rt-section-tools-footer"></div>-->

</div>