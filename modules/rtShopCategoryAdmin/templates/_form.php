<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_javascript('/sfFormExtraPlugin/js/jquery.autocompleter.js') ?>
<?php use_stylesheet('/sfFormExtraPlugin/css/jquery.autocompleter.css') ?>
<?php use_helper('I18N', 'rtForm') ?>

<?php slot('rt-tools') ?>
<?php include_partial('rtAdmin/standard_modal_tools', array('show_route_handle' => 'admin', 'object' => $form->getObject()))?>
<?php end_slot(); ?>

<?php slot('rt-side') ?>
<?php include_component('rtAsset', 'form', array('object' => $form->getObject())) ?>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<form id ="rtAdminForm" action="<?php echo url_for('rtShopCategoryAdmin/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php echo $form->renderHiddenFields(false) ?>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
<input type="hidden" name="rt_post_save_action" value="edit" />
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <?php echo render_form_row($form['title']); ?>
      <?php echo render_form_row($form['content']); ?>
    </tbody>
  </table>

  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Product Selection') ?></h2>
    <table class="rt-admin-toggle-panel-content" id="rtSortableProducts">
      <tbody>
        <tr class="rt-form-row standard">
          <th><?php echo $form['rt_shop_products_list']->renderLabel() ?></th>
          <td>
            <?php echo $form['rt_shop_products_list']->renderError() ?>
            <?php echo $form['rt_shop_products_list'] ?>
            <div class="help"><?php echo $form['rt_shop_products_list']->getParent()->getWidget()->getHelp('rt_shop_products_list') ?></div>
            <div id="rt-shop-category-product-search">
              <label for="rt_shop_category_product_search">Add another product:</label>
              <input type="text" id="rt_shop_category_product_search" name="rt_shop_category_product_search" />
              <div class="help"><?php echo __('Type in the title of a product. If a product is found it will be shown in a dropdown selection.') ?></div>
            </div>
          </td>
        </tr>
        <?php //echo render_form_row($form['rt_shop_products_list']); ?>
      </tbody>
    </table>
    
    <script type="text/javascript">
      $(function() {
        $("#rtSortableProducts ul.checkbox_list").first().sortable({
          revert: true
        });

        jQuery("#rt_shop_category_product_search")
        .autocomplete('/rtShopProductAdmin/productSelect?q=' + $("#rt_shop_category_product_search").val(), jQuery.extend({}, {
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

          var checkboxId = '#rt_shop_category_rt_shop_products_list_' + data[1];

          // Check if list item does not exist, if exists highlight item
          if($(checkboxId).html() == null) {
            var li_start    = '<li style="">';
            var input_field = '<input name="rt_shop_category[rt_shop_products_list][]" type="checkbox" value="' + data[1] + '" id="rt_shop_category_rt_shop_products_list_' + data[1] + '" checked="checked">';
            var label       = '<label for="rt_shop_category_rt_shop_products_list_' + data[1] + '">' + data[0] + '</label>';
            var li_end      = '</li>';

            var list = $("#rt_shop_category_product_search").parents('td').children('ul');

            if(list.html() == null)
            {
              $("#rt_shop_category_product_search").parents('td').prepend('<ul class="checkbox_list"></ul>');
              list = $("#rt_shop_category_product_search").parents('td').children('ul');
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

  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Publish Status') ?></h2>
    <table class="rt-admin-toggle-panel-content">
      <tbody>
        <tr>
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
    <h2><?php echo __('Menu and Navigation') ?></h2>
    <table class="rt-admin-toggle-panel-content">
      <tbody>
        <?php echo render_form_row($form['display_in_menu']); ?>
        <?php echo render_form_row($form['menu_title']); ?>
      </tbody>
    </table>
  </div>

  <div class="rt-admin-toggle-panel">
    <h2><?php echo __('Location and Referencing') ?></h2>
    <table class="rt-admin-toggle-panel-content">
      <tbody>
        <?php echo render_form_row($form['slug']); ?>
        <?php if(isset($form['site_id'])): ?>
          <?php echo render_form_row($form['site_id']); ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</form>