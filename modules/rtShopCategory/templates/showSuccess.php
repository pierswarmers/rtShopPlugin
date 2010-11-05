<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite'); ?>

<?php slot('rt-title', $rt_shop_category->getTitle()) ?>

<?php echo link_to(__('Edit'), 'rtShopCategoryAdmin/edit?id='.$rt_shop_category->getId(), array('class' => 'rt-admin-edit-tools-trigger')) ?>

<?php $content = markdown_to_html($rt_shop_category->getContent(), $rt_shop_category); ?>
<?php if(trim($content) !== ''): ?>
  <?php echo $content; ?>
<?php endif; ?>
<div class="rt-container rt-collection">
  <?php $i = 1; foreach($pager as $rt_shop_product): ?>
  <div class="rt-list-item rt-list-item-<?php echo $i ?>">
  <?php include_partial('rtShopProduct/shopProductMini', array('rt_shop_product' => $rt_shop_product)); ?>
  </div>
  <?php $i++; endforeach; ?>
</div>

<?php include_partial('rtAdmin/pagination_public', array('pager' => $pager)); ?>