<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite'); ?>

<div class="rt-shop-category-show rt-admin-edit-tools-panel">
  <?php echo link_to(__('Edit'), 'rtShopCategoryAdmin/edit?id='.$rt_shop_category->getId(), array('class' => 'rt-admin-edit-tools-trigger')) ?>
  <h1><?php echo $rt_shop_category->getTitle() ?></h1>
  <div class="rt-page-content rt-panel clearfix">
    <?php echo markdown_to_html($rt_shop_category->getContent(), $rt_shop_category); ?>
  </div>
  <div class="rt-collection rt-panel clearfix">
    <?php foreach($pager as $rt_shop_product): ?>
    <?php include_partial('rtShopProduct/shopProductMini', array('rt_shop_product' => $rt_shop_product)); ?>
    <?php endforeach; ?>
  </div>
</div>

<?php include_partial('rtAdmin/pagination_public', array('pager' => $pager)); ?>