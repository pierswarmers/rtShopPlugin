<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite'); ?>

<div class="rt-shop-category rt-show rt-primary-container rt-admin-edit-tools-panel">
  <?php echo link_to(__('Edit'), 'rtShopCategoryAdmin/edit?id='.$rt_shop_category->getId(), array('class' => 'rt-admin-edit-tools-trigger')) ?>
  <h1><?php echo $rt_shop_category->getTitle() ?></h1>
  <div class="rt-container">
    <?php echo markdown_to_html($rt_shop_category->getContent(), $rt_shop_category); ?>
  </div>
  <div class="rt-container rt-collection">
    <?php $i = 1; foreach($pager as $rt_shop_product): ?>
    <div class="rt-list-item rt-list-item-<?php echo $i ?>">
    <?php include_partial('rtShopProduct/shopProductMini', array('rt_shop_product' => $rt_shop_product)); ?>
    </div>
    <?php $i++; endforeach; ?>
  </div>
</div>

<?php include_partial('rtAdmin/pagination_public', array('pager' => $pager)); ?>