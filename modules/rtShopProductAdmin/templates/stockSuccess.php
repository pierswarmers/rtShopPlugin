<?php use_helper('I18N', 'rtAdmin') ?>
<?php $attributes = $form->getAttributes() ?>

<h1><?php echo __('Editing Shop Product') ?> :: <?php echo __('Stock Levels') ?></h1>

<?php slot('rt-side') ?>
<p>
  <button type="submit" class="button positive" onclick="$('#rtAdminForm').submit()"><?php echo __('Save and close') ?></button>
  <?php $back_location = $form->getObject()->isNew() ? 'history.go(-1);' : 'document.location.href=\'' . url_for('rtShopProductAdmin/edit', $form->getObject()) . '\';'; ?>
  <?php echo button_to(__('Cancel'),'rtShopProductAdmin/edit?id='. $form->getObject()->getId(), array('class' => 'button cancel')) ?>
</p>
<?php end_slot(); ?>

<form id="rtAdminForm" class="compressed" action="<?php echo url_for('rtShopProductAdmin/stock?id='. $form->getObject()->getId()) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <?php echo $form->renderHiddenFields(false) ?>
  <?php echo $form->renderGlobalErrors() ?>
  <table id="rtStockTable">
    <thead>
      <tr>
        <th><?php echo __('Delete') ?></th>
        <th><?php echo __('Batch') ?></th>
        <th><?php echo __('Qty.') ?></th>
        <th><?php echo __('SKU') ?></th>
        <th><?php echo __('$ Retail') ?></th>
        <th><?php echo __('$ Promo.') ?></th>
        <th><?php echo __('$ Whsle.') ?></th>
        <th class="advanced-panel"><?php echo __('Lgth.') ?></th>
        <th class="advanced-panel"><?php echo __('Wdth.') ?></th>
        <th class="advanced-panel"><?php echo __('Hght.') ?></th>
        <th class="advanced-panel"><?php echo __('Wght.') ?></th>
        <?php foreach($attributes as $attribute): ?>
          <th><?php echo $attribute->getDisplayTitle() ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody id="rtStockRows">
    <?php if(!$form->isNew()): ?>
      <?php foreach ($form['currentStocks'] as $stock_form): ?>
        <?php include_partial('stock_row', array('stock_form' => $stock_form, 'attributes' => $attributes))?>
      <?php endforeach; ?>
    <?php endif; ?>

      <?php foreach ($form['newStocks'] as $stock_form): ?>
        <?php include_partial('stock_row', array('stock_form' => $stock_form, 'attributes' => $attributes))?>
      <?php endforeach; ?>
    </tbody>
  </table>
</form>

<ul class="rt-admin-tools">
  <li><button id="newRow"><?php echo __('Add new stock row') ?></button></li>
  <li><button id="expandAdvancedCols"><?php echo __('Expand advanced options') ?></button></li>
</ul>

<script type="text/javascript">

  $('#expandAdvancedCols').button({
    icons: { primary: 'ui-icon-arrowstop-1-e'}
  }).click(function(){

    if($(this).hasClass('hide-options')) {
      $(this).removeClass('hide-options');
      $('#rtStockTable .advanced-panel').css('display', 'none');
      $(this).button({ label: "<?php echo __('Show advanced options') ?>", icons: { primary: 'ui-icon-arrowstop-1-e'} });
    } else {
      $(this).addClass('hide-options');
      $('#rtStockTable .advanced-panel').css('display', 'table-cell');
      $(this).button({ label: "<?php echo __('Hide advanced options') ?>", icons: { primary: 'ui-icon-arrowstop-1-w'} });
    }
  });
  
  
  $('#newRow').button({
    icons: { primary: 'ui-icon-plus'}
  }).click(function(){
    
    var table  = $("#rtStockRows");
//    var clonedRow = $("#rtStockRows tr").last().clone();
    var newRows = $('#rt_shop_product_newRows').attr('value');

    var iPref = "rt_shop_product_currentStocks_";
    var nPref = "rt_shop_product[currentStocks][";

//    clonedRow.children('td').first().html('&nbsp;');


    $.get("<?php echo url_for('rtShopProductAdmin/stockRow') ?>", { id: <?php echo $rt_shop_product->getId() ?>, count: newRows }, function(data){
      table.append(data);
    });

    

    // update the new row count.
    $('#rt_shop_product_newRows').attr('value', parseInt(newRows) + 1);
  });

</script>

