<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_helper('I18N', 'rtForm') ?>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li>
    <span class="positive save-set">
      <button class="save"><?php echo __('Save Changes') ?></button>
      <button class="save-list"><?php echo __('Save &amp; Close') ?></button>
      <button class="save-show"><?php echo __('Save &amp; Show') ?></button>
    </span>
  </li>
  <li><button class="stock"><?php echo __('Edit Stock Levels') ?></button></li>
  <li><button class="cancel"><?php echo __('Cancel/List') ?></button></li>
  <li><button class="show"><?php echo __('Show') ?></button></li>
</ul>

<?php if(!$form->getObject()->isNew()): ?>
<p><?php echo __('Or') ?>, <?php echo link_to('delete this product', 'rtShopProductAdmin/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?></p>
<?php endif; ?>

<script type="text/javascript">
	$(function() {

    $("#rtPrimaryTools .save").button({
      icons: { primary: 'ui-icon-disk' }
    }).click(function(){ $('#rtAdminForm').submit(); }).next().button({
      text: false,
      icons: { secondary: 'ui-icon-close' }
    }).click(function(){ $('input[name=rt_post_save_action]').attr('value', 'index'); $('#rtAdminForm').submit(); }).next().button({
      text: false,
      icons: { secondary: 'ui-icon-extlink' }
    }).click(function(){ $('input[name=rt_post_save_action]').attr('value', 'show'); $('#rtAdminForm').submit(); });

    $("#rtPrimaryTools .save").parent().buttonset();

   $("#rtPrimaryTools .stock").button({
      icons: { primary: 'ui-icon-pencil' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopProductAdmin/stock?id='.$form->getObject()->getId()) ?>'; });
    
    <?php if(!$form->getObject()->isNew()): ?>
    $("#rtPrimaryTools .show").button({
      icons: { primary: 'ui-icon-extlink' }
    }).click(function(){ document.location.href='<?php echo url_for('rt_shop_product_show', $form->getObject()) ?>'; });
    <?php endif; ?>

    $("#rtPrimaryTools .cancel").button({
      icons: { primary: 'ui-icon-cancel' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopProductAdmin/index') ?>'; });
	});
</script>
<?php end_slot(); ?>



<?php slot('rt-side') ?>
<?php include_component('rtAsset', 'form', array('object' => $form->getObject())) ?>
<?php end_slot(); ?>

<form id ="rtAdminForm" action="<?php echo url_for('rtShopProductAdmin/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php echo $form->renderHiddenFields(false) ?>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php echo $form->renderGlobalErrors() ?>
<?php endif; ?>
<input type="hidden" name="rt_post_save_action" value="edit" />
  <table>
    <tbody>
      <?php echo render_form_row($form['title']); ?>
      <?php echo render_form_row($form['content']); ?>
    </tbody>
  </table>

  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Advanced Options') ?></h2>
    <table class="rt-admin-toggle-panel-content" id="rtSortableAttributes">
      <tbody>
        <?php echo render_form_row($form['sku']); ?>
        <?php echo render_form_row($form['is_featured']); ?>
        <?php echo render_form_row($form['backorder_allowed']); ?>
        <?php echo render_form_row($form['is_taxable']); ?>
        <?php echo render_form_row($form['rt_shop_attributes_list']); ?>
      </tbody>
    </table>
    <script type="text/javascript">
    $(function() {
      $("#rtSortableAttributes ul.checkbox_list").sortable({
        revert: true
      });
    });
    </script>
  </div>

  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Publish Status') ?></h2>
    <table class="rt-admin-toggle-panel-content">
      <tbody>
        <?php echo render_form_row($form['published']); ?>
        <?php echo render_form_row($form['published_from']); ?>
        <?php echo render_form_row($form['published_to']); ?>
      </tbody>
    </table>
  </div>


  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Metadata and SEO') ?></h2>
    <table class="rt-admin-toggle-panel-content">
      <tbody>
        <?php echo render_form_row($form['description']); ?>
        <?php echo render_form_row($form['tags']); ?>
        <?php echo render_form_row($form['searchable']); ?>
      </tbody>
    </table>
  </div>

  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Location and Referencing') ?></h2>
    <table class="rt-admin-toggle-panel-content">
      <tbody>
        <?php echo render_form_row($form['slug']); ?>
        <?php echo render_form_row($form['rt_shop_categories_list']); ?>
      <?php if(isset($form['site_id'])): ?>
        <?php echo render_form_row($form['site_id']); ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</form>
