<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_helper('I18N', 'rtForm') ?>
<?php use_javascript('/rtCorePlugin/js/admin-main.js', 'last') ?>

<?php slot('rt-tools') ?>
<?php include_partial('rtAdmin/standard_modal_tools', array('object' => $form->getObject()))?>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<form id ="rtAdminForm" action="<?php echo url_for('rtShopAttributeAdmin/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php echo $form->renderHiddenFields(false) ?>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
<input type="hidden" name="rt_post_save_action" value="edit" />
  <?php echo $form->renderGlobalErrors() ?>
  <table>
    <tbody>
      <?php echo render_form_row($form['title']); ?>
      <?php echo render_form_row($form['display_title']); ?>
      <?php echo render_form_row($form['description']); ?>
    </tbody>
  </table>

  <?php if(!$form->isNew()): ?>
  <div class="rt-admin-toggle-panel-plain">
    <h2><?php echo __('Current Variations') ?></h2>
    <table class="">
      <thead>
        <tr>
          <th style="width:10%;"><?php echo __('Delete') ?></th>
          <th style="width:10%;"><?php echo __('Order') ?></th>
          <th><?php echo __('Title') ?></th>
          <th><?php echo __('Image') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($form['currentVariations'] as $variation_form): ?>
        <tr>
          <td><?php echo $variation_form['delete']->render() ?><?php echo $variation_form->renderHiddenFields(false) ?></td>
          <td><?php echo $variation_form['position']->render() ?><?php echo $variation_form['position']->renderError() ?></td>
          <td><?php echo $variation_form['title']->render() ?><?php echo $variation_form['title']->renderError() ?></td>
          <td><?php echo $variation_form['image']->render() ?><?php echo $variation_form['image']->renderError() ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <div class="rt-admin-toggle-panel<?php echo $form->isNew() ? '-plain' : '' ?>">
    <h2><?php echo __('New Variations To Add') ?></h2>
    <table<?php echo !$form->isNew() ? ' class="rt-admin-toggle-panel-content"' : '' ?>>
      <thead>
        <tr>
          <th style="width:70px;"><?php echo __('Order') ?></th>
          <th><?php echo __('Title') ?></th>
          <th><?php echo __('Image') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($form['newVariations'] as $variation_form): ?>
        <tr>
          <td><?php echo $variation_form['position']->render() ?><?php echo $variation_form['position']->renderError() ?></td>
          <td><?php echo $variation_form['title']->render() ?><?php echo $variation_form['title']->renderError() ?></td>
          <td><?php echo $variation_form['image']->render() ?><?php echo $variation_form['image']->renderError() ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</form>

