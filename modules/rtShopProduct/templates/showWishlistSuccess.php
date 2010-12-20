<?php use_helper('I18N') ?>

<?php slot('rt-title', __('Your Wishlist Items')) ?>

<?php $wishlist = $sf_user->getAttribute('rt_shop_wish_list', array()); ?>

<?php if(count($wishlist) > 0): ?>
<div class="rt-collection">
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

  <div class="rt-tools rt-shop-wishlist-tools">
    <form action="<?php echo url_for('@rt_shop_show_wishlist') ?>" method="post">
      <?php echo $form->renderHiddenFields() ?>
      <ul class="rt-form-schema">
        <li class="rt-form-row">
          <?php echo $form['email_address']->renderLabel() ?>
          <div class="rt-form-field">
            <?php echo $form['email_address']->renderError() ?><?php echo $form['email_address'] ?>
          </div>
      </li>
      </ul>
      <p class="rt-form-tools">
        <button type="submit" class="button rt-shop-wishlist-email"><?php echo __('Email this wishlist') ?></button>
      </p>
    </form>
  </div>
<?php else: ?>
  <p><?php echo __('No items in your wishlist yet.') ?></p>
<?php endif; ?>