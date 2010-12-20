<?php use_helper('I18N') ?>
<p><?php echo __('My wishlist items') ?>:</p>
<?php if(count($wishlist) > 0): ?>
  <?php $i = 1; foreach ($wishlist as $id): ?>
  <?php
  $product = Doctrine::getTable('rtShopProduct')->find($id);
  if($product): ?>
    <p><?php echo link_to($product->getTitle(), url_for('rt_shop_product_show', $product, true)); ?></p>
  <?php endif; ?>
  <?php $i++; endforeach; ?>
<?php endif; ?>