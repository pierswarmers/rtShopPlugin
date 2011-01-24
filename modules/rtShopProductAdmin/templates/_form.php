<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_javascript('/sfFormExtraPlugin/js/jquery.autocompleter.js') ?>
<?php use_stylesheet('/sfFormExtraPlugin/css/jquery.autocompleter.css') ?>
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
  <?php if(!$form->getObject()->isNew()): ?>
  <li><button class="stock"><?php echo __('Edit Stock Levels') ?></button></li>
  <?php endif; ?>
  <li><button class="cancel"><?php echo __('Cancel/List') ?></button></li>
  <?php if(!$form->getObject()->isNew()): ?>
  <li><button class="show"><?php echo __('Show') ?></button></li>
  <?php endif; ?>
</ul>

<?php if(!$form->getObject()->isNew() && false): ?>
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
    }).click(function(){ document.location.href='<?php echo url_for('rtShopProductAdmin/show?id='.$form->getObject()->getId()) ?>'; });
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

<?php include_partial('rtAdmin/flashes') ?>

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
        <?php echo render_form_row($form['rt_shop_promotions_list']); ?>
      </tbody>
    </table>
    <script type="text/javascript">
    $(function() {
      $("#rtSortableAttributes ul.checkbox_list").first().sortable({
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
    <table class="rt-admin-toggle-panel-content" id="rtSortableProducts">
      <tbody>
        <?php echo render_form_row($form['slug']); ?>
        <?php if(isset($form['site_id'])): ?>
          <?php echo render_form_row($form['site_id']); ?>
        <?php endif; ?>
        <tr class="rt-form-row standard">
          <th><?php echo $form['rt_shop_products_list']->renderLabel() ?></th>
          <td>
            <?php echo $form['rt_shop_products_list']->renderError() ?>
            <?php echo $form['rt_shop_products_list'] ?>
            <div class="help"><?php echo $form['rt_shop_products_list']->getParent()->getWidget()->getHelp('rt_shop_products_list') ?></div>
            <div id="rt-shop-product-product-search">
              <label for="rt_shop_product_product_search">Add another product:</label>
              <input type="text" id="rt_shop_product_product_search" name="rt_shop_product_product_search" />
              <div class="help"><?php echo __('Type in the title of a product. If a product is found it will be shown in a dropdown selection.') ?></div>
            </div>
          </td>
        </tr>
        <?php //echo render_form_row($form['rt_shop_products_list']); ?>
        <?php echo render_form_row($form['rt_shop_categories_list']); ?>
      </tbody>
    </table>
    <script type="text/javascript">
    $(function() {
      $("#rtSortableProducts ul.checkbox_list").first().sortable({
        revert: true
      });

        jQuery("#rt_shop_product_product_search")
        .autocomplete('/rtShopProductAdmin/productSelect?q=' + $("#rt_shop_product_product_search").val(), jQuery.extend({}, {
          dataType: 'json',
          parse:    function(data) {
            var parsed = [];
            for (key in data) {
              parsed[parsed.length] = { data: [ data[key], key ], value: data[key], result: data[key] };
            }
            return parsed;
          }
        }, { }))
        .result(function(event, data) {

          var checkboxId = '#rt_shop_product_rt_shop_products_list_' + data[1];

          // Check if list item does not exist, if exists highlight item
          if($(checkboxId).html() == null && <?php echo $form->getObject()->getId() ?> != data[1]) {
            var li_start    = '<li style="">';
            var input_field = '<input name="rt_shop_product[rt_shop_products_list][]" type="checkbox" value="' + data[1] + '" id="rt_shop_product_rt_shop_products_list_' + data[1] + '" checked="checked">';
            var label       = '<label for="rt_shop_product_rt_shop_products_list_' + data[1] + '">' + data[0] + '</label>';
            var li_end      = '</li>';

            var list = $("#rt_shop_product_product_search").parents('td').children('ul');

            if(list.html() == null)
            {
              $("#rt_shop_product_product_search").parents('td').prepend('<ul class="checkbox_list"></ul>');
              list = $("#rt_shop_product_product_search").parents('td').children('ul');
              list.sortable({
                revert: true
              });
            }

            list.append(li_start + input_field + '&nbsp;' + label + li_end);
          }
          $(checkboxId).parents('li').effect('highlight');
        });
    });
    </script>
  </div>
</form>
