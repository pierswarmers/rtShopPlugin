<?php use_helper('I18N') ?>
<div class="rt-shop-wishlist rt-show rt-primary-container">
  <h1><?php echo __('Your Wishlist Items') ?></h1>
  <div class="rt-container">
  <?php

  $wishlist = $sf_user->getAttribute('rt_shop_wish_list', array());

  ?>
  <?php if(count($wishlist) > 0): ?>
    <div class="rt-container rt-collection">
    <?php $i = 1; foreach ($wishlist as $id): ?>
    <?php

    $product = Doctrine::getTable('rtShopProduct')->find($id);

    if($product): ?>
    <div class="rt-list-item rt-list-item-<?php echo $i ?>">
      <?php echo link_to(__('Delete item'), 'rt_shop_show_wishlist', array('delete' => $id), array('class' => 'delete  '))?>
      <?php include_partial('rtShopProduct/shopProductMini', array('rt_shop_product' => $product)); ?>
    </div>
    <?php endif; ?>
    <?php $i++; endforeach; ?>
    </div>
    <?php else: ?>
    <p><?php echo __('No items in your wishlist yet.') ?></p>
    <?php endif; ?>
  </div>

  <div class="rt-container rt-tools rt-shop-wishlist-tools">
    <button type="submit" class="button rt-shop-wishlist-email"><?php echo __('Email this wishlist') ?></button>
  </div>
</div>