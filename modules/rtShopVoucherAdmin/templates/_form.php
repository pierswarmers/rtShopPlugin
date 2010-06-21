<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_helper('I18N', 'rtForm') ?>
<?php use_javascript('/rtCorePlugin/js/main.js', 'last') ?>

<?php slot('rt-tools') ?>
<?php include_partial('rtAdmin/standard_modal_tools', array('object' => $form->getObject()))?>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<form id ="rtAdminForm" action="<?php echo url_for('rtShopVoucherAdmin/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php echo $form->renderHiddenFields(false) ?>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
<input type="hidden" name="rt_post_save_action" value="edit" />
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <?php echo render_form_row($form['title']); ?>
      <?php echo render_form_row($form['reduction_type']); ?>
      <?php echo render_form_row($form['reduction_value']); ?>
    </tbody>
  </table>

  <?php include_partial('form_main', array('form' => $form)) ?>
</form>