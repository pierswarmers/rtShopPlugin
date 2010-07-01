<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_javascript('/rtCorePlugin/vendor/jquery/js/jquery.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/jquery/js/jquery.tools.min.js', 'last'); ?>
<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate') ?>

<?php slot('rt-tools') ?>
<?php include_partial('rtAdmin/standard_modal_tools', array('object' => $form->getObject()))?>
<?php end_slot(); ?>

<form id ="rtAdminForm" action="<?php echo url_for('rtShopOrderAdmin/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php echo $form->renderHiddenFields(false) ?>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <?php echo $form['status']->renderRow() ?>
      <?php echo $form['email']->renderRow() ?>
      <?php echo $form['notes_user']->renderRow() ?>
      <?php echo $form['notes_admin']->renderRow() ?>
      <?php //echo $form['is_wholesale']->renderRow() ?>
    </tbody>
  </table>
</form>
