<?php

/** @var rtShopCategory $rt_shop_category */

use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite');

slot('rt-title', $rt_shop_category->getTitle());

?>

<div class="rt-section rt-shop-category">

  <!--RTAS
  <div class="rt-section-tools-header rt-admin-tools">
    <?php echo link_to(__('Edit Category'), 'rtShopCategoryAdmin/edit?id='.$rt_shop_category->getId(), array('class' => 'rt-admin-edit-tools-trigger')) ?>
  </div>
  RTAS-->
    
  <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
    <div class="rt-section-header">
      <h1><?php echo $rt_shop_category->getTitle() ?></h1>
    </div>
  <?php endif; ?>

  <div class="rt-section-content">
    
    <?php $content = markdown_to_html($rt_shop_category->getContent(), $rt_shop_category); ?>

    <?php if(trim($content) !== ''): ?>
      <?php echo $content; ?>
    <?php endif; ?>      
    
    <div class="rt-container rt-collection">
      <?php $i = 1; foreach($pager as $rt_shop_product): ?>
      <div class="item-<?php echo $i ?>">
        <?php include_partial('rtShopProduct/shopProductMini', array('rt_shop_product' => $rt_shop_product)); ?>
      </div>
      <?php $i++; endforeach; ?>
    </div>
    
  </div>

  <?php if($pager->haveToPaginate()): ?>
  <div class="rt-section-tools-footer">
    <?php include_partial('rtAdmin/pagination_public', array('pager' => $pager)); ?>
  </div>
  <?php endif; ?>

</div>