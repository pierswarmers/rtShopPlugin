<tr>
  <td>
    <?php echo $stock_form['delete']->render() ?>
    <?php echo $stock_form['id']->render() ?>
    <?php echo $stock_form['product_id']->render() ?>
  </td>
  <td>
    <input type="checkbox" name="batch[]" class="batch" value="1" />
  </td>
  <td><?php echo $stock_form['quantity']->render() ?><?php echo $stock_form['quantity']->renderError() ?></td>
  <td><?php echo $stock_form['sku']->render() ?><?php echo $stock_form['sku']->renderError() ?></td>
  <td><?php echo $stock_form['price_retail']->render() ?><?php echo $stock_form['price_retail']->renderError() ?></td>
  <td><?php echo $stock_form['price_promotion']->render() ?><?php echo $stock_form['price_promotion']->renderError() ?></td>
  <td><?php echo $stock_form['price_wholesale']->render() ?><?php echo $stock_form['price_wholesale']->renderError() ?></td>
  <td class="advanced-panel"><?php echo $stock_form['length']->render() ?><?php echo $stock_form['length']->renderError() ?></td>
  <td class="advanced-panel"><?php echo $stock_form['width']->render() ?><?php echo $stock_form['width']->renderError() ?></td>
  <td class="advanced-panel"><?php echo $stock_form['height']->render() ?><?php echo $stock_form['height']->renderError() ?></td>
  <td class="advanced-panel"><?php echo $stock_form['weight']->render() ?><?php echo $stock_form['weight']->renderError() ?></td>
  <?php foreach($attributes as $attribute): ?>
    <td><?php echo $stock_form['rt_shop_variations_list_'.$attribute->getId()]->render() ?><?php echo $stock_form['rt_shop_variations_list_'.$attribute->getId()]->renderError() ?></td>
  <?php endforeach; ?>
</tr>