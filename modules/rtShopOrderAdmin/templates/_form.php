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
      <tr>
        <th><?php echo $form['reference']->renderLabel() ?></th>
        <td>
          <?php echo $form['reference']->renderError() ?>
          <?php echo $form['reference'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['status']->renderLabel() ?></th>
        <td>
          <?php echo $form['status']->renderError() ?>
          <?php echo $form['status'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['email']->renderLabel() ?></th>
        <td>
          <?php echo $form['email']->renderError() ?>
          <?php echo $form['email'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['notes_user']->renderLabel() ?></th>
        <td>
          <?php echo $form['notes_user']->renderError() ?>
          <?php echo $form['notes_user'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['notes_admin']->renderLabel() ?></th>
        <td>
          <?php echo $form['notes_admin']->renderError() ?>
          <?php echo $form['notes_admin'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['user_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['user_id']->renderError() ?>
          <?php echo $form['user_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['is_wholesale']->renderLabel() ?></th>
        <td>
          <?php echo $form['is_wholesale']->renderError() ?>
          <?php echo $form['is_wholesale'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['closed_shipping_rate']->renderLabel() ?></th>
        <td>
          <?php echo $form['closed_shipping_rate']->renderError() ?>
          <?php echo $form['closed_shipping_rate'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['closed_taxes']->renderLabel() ?></th>
        <td>
          <?php echo $form['closed_taxes']->renderError() ?>
          <?php echo $form['closed_taxes'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['closed_promotions']->renderLabel() ?></th>
        <td>
          <?php echo $form['closed_promotions']->renderError() ?>
          <?php echo $form['closed_promotions'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['closed_products']->renderLabel() ?></th>
        <td>
          <?php echo $form['closed_products']->renderError() ?>
          <?php echo $form['closed_products'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['closed_total']->renderLabel() ?></th>
        <td>
          <?php echo $form['closed_total']->renderError() ?>
          <?php echo $form['closed_total'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['created_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['created_at']->renderError() ?>
          <?php echo $form['created_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['updated_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['updated_at']->renderError() ?>
          <?php echo $form['updated_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['stocks_list']->renderLabel() ?></th>
        <td>
          <?php echo $form['stocks_list']->renderError() ?>
          <?php echo $form['stocks_list'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
