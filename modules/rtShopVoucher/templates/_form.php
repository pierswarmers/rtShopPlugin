<?php use_helper('I18N', 'rtForm', 'rtTemplate') ?>
<?php echo rt_get_snippet('rt-shop-frontend-gift-voucher-prefix', 'Fill out the information below to create an e-mail a gift voucher.')?>
<form action="<?php echo url_for(($sf_user->hasAttribute('rt_shop_frontend_gift_voucher') ? '@rt_shop_voucher_update' : '@rt_shop_voucher_create')) ?>" method="post" class="rt-compact">
<?php echo $form->renderHiddenFields() ?>
  <fieldset>
    <legend><?php echo __('Your Gift Voucher Details') ?></legend>    
    <ul class="rt-form-schema">
      <?php echo $form; ?>
    </ul>
  </fieldset>
  <p class="rt-form-tools">
    <?php $button_text = $sf_user->hasAttribute('rt_shop_frontend_gift_voucher') ? __('Update Voucher') : __('Save Voucher') ; ?>
    <button type="submit"><?php echo $button_text ?></button>
    <?php if($sf_user->hasAttribute('rt_shop_frontend_gift_voucher')): ?>
      <?php echo __('or') ?> <?php echo link_to(__('delete Voucher'), '@rt_shop_voucher_delete') ?>
    <?php endif; ?>
  </p>
</form>
<?php echo rt_get_snippet('rt-shop-frontend-gift-voucher-suffix')?>