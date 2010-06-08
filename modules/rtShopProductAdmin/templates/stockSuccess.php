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
    <tfoot>
      <tr>
        <th>&nbsp;</th>
        <th><input type="checkbox" id="batchToggle" value="-" /></th>
        <th><input type="text" name="batch-quantity" value="-" /></th>
        <th><input type="text" name="batch-sku" value="-" /></th>
        <th><input type="text" name="batch-price_retail" value="-" /></th>
        <th><input type="text" name="batch-price_promotion" value="-" /></th>
        <th><input type="text" name="batch-price_wholesale" value="-" /></th>
        <th class="advanced-panel"><input type="text" name="batch-length" value="-" /></th>
        <th class="advanced-panel"><input type="text" name="batch-width" value="-" /></th>
        <th class="advanced-panel"><input type="text" name="batch-height" value="-" /></th>
        <th class="advanced-panel"><input type="text" name="batch-weight" value="-" /></th>
        <th colspan="<?php echo $attributes->count() ?>">
          <ul class="rt-admin-tools">
            
          </ul>
        </th>
      </tr>
    </tfoot>
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
  <li><button id="expandAdvancedCols"><?php echo __('Expand metrics options') ?></button></li>
  <li><button id="runBatch"><?php echo __('Run batch changes') ?></button></li>
</ul>

<script type="text/javascript">

  $("#batchToggle").click(function(){
     $("input[name=batch\\[\\]]").each(function(){
       $(this).attr('checked', $("#batchToggle").is(':checked') ? true : false);
       if($("#batchToggle").is(':checked'))
       {
         $(this).parent().parent().addClass('batch-row');
       }
       else
       {
         $(this).parent().parent().removeClass('batch-row');
       }
     });
  });

  $('#runBatch').button({
    icons: { primary: 'ui-icon-transfer-e-w'}
  }).click(function(){
    
    var tmpInputValues = new Array();

    tmpInputValues[0] = 'quantity';
    tmpInputValues[1] = 'sku';
    tmpInputValues[2] = 'price_retail';
    tmpInputValues[3] = 'price_wholesale';
    tmpInputValues[4] = 'price_promotion';
    tmpInputValues[5] = 'height';
    tmpInputValues[6] = 'width';
    tmpInputValues[7] = 'length';
    tmpInputValues[8] = 'weight';

    $.each(tmpInputValues,function(i){
        if($("input[name=batch-"+tmpInputValues[i]+"]").attr('value') != '-')
          $("tr.batch-row input[name*='"+tmpInputValues[i]+"']").each(function(){$(this).attr('value', $("input[name=batch-"+tmpInputValues[i]+"]").attr('value'))});
    });
  
    return false;
  });
  
  $("input[name=batch\\[\\]]").each(function(){
    $(this).change(function(){
      $(this).parent().parent().toggleClass('batch-row');
    });
  });

  $('#expandAdvancedCols').button({
    icons: { primary: 'ui-icon-arrowstop-1-e'}
  }).click(function(){

    if($(this).hasClass('hide-options')) {
      $(this).removeClass('hide-options');
      $('#rtStockTable .advanced-panel').css('display', 'none');
      $(this).button({ label: "<?php echo __('Show metrics options') ?>", icons: { primary: 'ui-icon-arrowstop-1-e'} });
    } else {
      $(this).addClass('hide-options');
      $('#rtStockTable .advanced-panel').css('display', 'table-cell');
      $(this).button({ label: "<?php echo __('Hide metrics options') ?>", icons: { primary: 'ui-icon-arrowstop-1-w'} });
    }

    return false;
  });
  
  
  $('#newRow').button({
    icons: { primary: 'ui-icon-plus'}
  }).click(function(){
    
    var tableBody  = $("#rtStockRows");
    var newRows = $('#rt_shop_product_newRows').attr('value');

    $.get("<?php echo url_for('rtShopProductAdmin/stockRow') ?>", { id: <?php echo $rt_shop_product->getId() ?>, count: newRows }, function(data){
      tableBody.append(data);
    });
    
    // update the new row count.
    $('#rt_shop_product_newRows').attr('value', parseInt(newRows) + 1);

    return false;
  });

  $("#rtStockRows").ajaxSuccess(function() {
    var tmpInputValues = new Array();
    var lastRow = $(this).children().last().prev();

    // Run copy of values from row above.
    lastRow.find('input[type=text], select').each(function(i) {
      tmpInputValues[i] = $(this).attr("value");
    });

    var newRow = $("#rtStockRows").children().last();

    newRow.find('input[type=text], select').each(function(i) {
      $(this).attr("value", tmpInputValues[i]);
    });

    newRow.find("input.batch").each(function(){
      $(this).change(function(){
        $(this).parent().parent().toggleClass('batch-row');
      });
    });
  });

</script>

