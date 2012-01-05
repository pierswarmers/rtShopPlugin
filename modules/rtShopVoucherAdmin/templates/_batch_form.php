<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_helper('I18N', 'rtForm') ?>
<?php use_javascript('/rtCorePlugin/js/admin-main.js', 'last') ?>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li><span class="positive save-set"><button class="save"><?php echo __('Save Batch of New Vouchers') ?></button></span></li>
  <li><button class="cancel"><?php echo __('Cancel/List') ?></button></li>
</ul>
<script type="text/javascript">
	$(function() {

    $("#rtPrimaryTools .save").button({
      icons: { primary: 'ui-icon-disk' }
    }).click(function(){ $('#rtAdminForm').submit(); });

    $("#rtPrimaryTools .cancel").button({
      icons: { primary: 'ui-icon-cancel' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopVoucherAdmin/index') ?>'; });

	});
</script>

<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<form id ="rtAdminForm" action="<?php echo url_for('rtShopVoucherAdmin/batchCreate') ?>" method="post">
<?php echo $form->renderHiddenFields(false) ?>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <?php echo render_form_row($form['batchsize']); ?>
      <?php echo render_form_row($form['title']); ?>
      <?php echo render_form_row($form['reduction_type']); ?>
      <?php echo render_form_row($form['reduction_value']); ?>
    </tbody>
  </table>

  <?php include_partial('form_main', array('form' => $form)) ?>
</form>