<?php

/** @var rtShopProduct $rt_shop_product */

use_helper('I18N', 'rtTemplate');

slot('rt-title', __('Your Wishlist Items'));

$wishlist = $sf_user->getAttribute('rt_shop_wish_list', array());

?>

<div class="rt-section rt-shop-product rt-shop-product-wishlist">
    
  <?php if(sfConfig::get('app_rt_templates_headers_embedded', true)): ?>
    <div class="rt-section-header">
      <h1><?php echo __('Your Wishlist Items') ?></h1>
    </div>
  <?php endif; ?>

  <div class="rt-section-content">

    <?php rt_get_snippet('rt-shop-wishlist-prefix'); ?>

    <?php if(count($wishlist) > 0): ?>

    <div class="rt-container rt-collection clearfix">
      <?php $i = 1; foreach($wishlist as $id): ?>
        <?php $rt_shop_product = Doctrine::getTable('rtShopProduct')->find($id); if($rt_shop_product): ?>
        <div class="product-item item-<?php echo $i ?>">
          <?php include_partial('rtShopProduct/shopProductMini', array('rt_shop_product' => $rt_shop_product, 'mode' => 'wishlist')); ?>
        </div>
        <?php endif; ?>
      <?php $i++; endforeach; ?>
    </div>

    <h2><?php echo __('Email your wishlist') ?></h2>
    <form action="<?php echo url_for('@rt_shop_show_wishlist') ?>" method="post">
      <?php echo $form->renderHiddenFields() ?>
      <ul class="rt-form-schema">
        <li class="rt-form-row">
          <?php echo $form['email_address']->renderLabel() ?>
          <div class="rt-form-field"><?php echo $form['email_address']->renderError() ?><?php echo $form['email_address'] ?></div>
        </li>
      </ul>
      <p class="rt-section-tools-submit"><button type="submit"><?php echo __('Email this wishlist') ?></button></p>
    </form>
    
    <?php else: ?>

      <p><?php echo __('No items in your wishlist yet.') ?></p>
    
    <?php endif; ?>

    <?php rt_get_snippet('rt-shop-wishlist-suffix'); ?>

  </div>

</div>