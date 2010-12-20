<?php use_helper('I18N') ?>
<?php echo __('My wishlist items') ?>:

<?php if(count($wishlist) > 0): ?>
<?php $i = 1; foreach ($wishlist as $id): ?>
<?php
$product = Doctrine::getTable('rtShopProduct')->find($id);
if($product): ?>
<?php echo url_for('rt_shop_product_show', $product, true); ?>

<?php endif; ?>
<?php $i++; endforeach; ?>
<?php endif; ?>