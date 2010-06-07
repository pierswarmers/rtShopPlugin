<?php use_helper('I18N', 'rtAdmin') ?>

<h1><?php echo __('Editing Shop Product') ?> :: <?php echo __('Stock Levels') ?></h1>

<?php slot('rt-side') ?>
<p>
  <button type="submit" class="button positive" onclick="$('#rtAdminForm').submit()"><?php echo __('Save and close') ?></button>
  <?php $back_location = $form->getObject()->isNew() ? 'history.go(-1);' : 'document.location.href=\'' . url_for('rtShopProductAdmin/edit', $form->getObject()) . '\';'; ?>
  <?php echo button_to(__('Cancel'),'rtShopProductAdmin/edit?id='. $form->getObject()->getId(), array('class' => 'button cancel')) ?>
</p>
<?php end_slot(); ?>

<form id ="rtAdminForm" action="<?php echo url_for('rtShopProductAdmin/stock?id='. $form->getObject()->getId()) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <?php echo $form->renderHiddenFields(false) ?>
  <?php echo $form->renderGlobalErrors() ?>
  <?php echo $form['newRows'] ?>
  <table>
    <thead>
      <tr>
        <th style="width:10%;"><?php echo __('Delete') ?></th>
        <th><?php echo __('Qty.') ?></th>
        <th><?php echo __('SKU') ?></th>
        <th><?php echo __('$ Retail') ?></th>
        <th><?php echo __('$ Promo.') ?></th>
        <th><?php echo __('$ Whsle.') ?></th>
        <?php foreach($form->getAttributes() as $attribute): ?>
          <th><?php echo $attribute->getDisplayTitle() ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
    <?php if(!$form->isNew()): ?>
      <?php foreach ($form['currentStocks'] as $stock_form): ?>
      <tr>
        <td>
          <?php echo $stock_form['delete']->render() ?>
          <?php echo $stock_form->renderHiddenFields(false) ?>
        </td>
        <td><?php echo $stock_form['quantity']->render() ?><?php echo $stock_form['quantity']->renderError() ?></td>
        <td><?php echo $stock_form['sku']->render() ?><?php echo $stock_form['sku']->renderError() ?></td>
        <td><?php echo $stock_form['price_retail']->render() ?><?php echo $stock_form['price_retail']->renderError() ?></td>
        <td><?php echo $stock_form['price_wholesale']->render() ?><?php echo $stock_form['price_wholesale']->renderError() ?></td>
        <td><?php echo $stock_form['price_promotion']->render() ?><?php echo $stock_form['price_promotion']->renderError() ?></td>
        <?php foreach($form->getAttributes() as $attribute): ?>
          <td><?php echo $stock_form['rt_shop_variations_list_'.$attribute->getId()]->render() ?><?php echo $stock_form['rt_shop_variations_list_'.$attribute->getId()]->renderError() ?></td>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>

      <?php foreach ($form['newStocks'] as $stock_form): ?>
      <tr>
        <td>
          &nbsp;
          <?php echo $stock_form->renderHiddenFields(false) ?>
        </td>
        <td><?php echo $stock_form['quantity']->render() ?><?php echo $stock_form['quantity']->renderError() ?></td>
        <td><?php echo $stock_form['sku']->render() ?><?php echo $stock_form['sku']->renderError() ?></td>
        <td><?php echo $stock_form['price_retail']->render() ?><?php echo $stock_form['price_retail']->renderError() ?></td>
        <td><?php echo $stock_form['price_wholesale']->render() ?><?php echo $stock_form['price_wholesale']->renderError() ?></td>
        <td><?php echo $stock_form['price_promotion']->render() ?><?php echo $stock_form['price_promotion']->renderError() ?></td>
        <?php foreach($form->getAttributes() as $attribute): ?>
          <td><?php echo $stock_form['rt_shop_variations_list_'.$attribute->getId()]->render() ?><?php echo $stock_form['rt_shop_variations_list_'.$attribute->getId()]->renderError() ?></td>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</form>
