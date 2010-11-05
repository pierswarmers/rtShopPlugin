<?php use_helper('I18N') ?>

<?php slot('rt-title',__('Send To A Friend')) ?>

<form action="<?php echo url_for('rt_shop_send_to_friend') ?>" method="post">
  <?php echo $form->renderHiddenFields() ?>
  <fieldset>
  <legend><?php echo __('Add details') ?></legend>
    <ul class="rt-form-schema">
      <?php echo $form; ?>
    </ul>
  </fieldset>
  <p class="rt-form-tools"><button><?php echo __('Send email') ?></button></p>
</form>