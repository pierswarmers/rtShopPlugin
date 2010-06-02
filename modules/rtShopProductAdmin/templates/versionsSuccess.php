<?php use_helper('I18N', 'Date', 'rtAdmin') ?>
        
<h1><?php echo __('Listing Versions') ?></h1>

<?php slot('rt-side') ?>
<p>
  <button type="submit" class="button positive" onclick="$('#rtShopProductForm').submit()"><?php echo __('Compare selection') ?></button>
  <?php echo button_to(__('Cancel'),'rtShopProductAdmin/index', array('class' => 'button cancel')) ?>
</p>
<?php end_slot(); ?>

<form id="rtShopProductForm" action="<?php echo url_for('rtShopProductAdmin/compare?id='.$rt_shop_product->getId()) ?>">
  <table class="stretch">
    <thead>
      <tr>
        <th style="width:30px;">#</th>
        <th><?php echo __('Title') ?></th>
        <th><?php echo __('Date') ?></th>
        <th style="width:30px;">1</th>
        <th style="width:30px;">2</th>
        <th style="width:50px;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($rt_shop_product_versions as $version): ?>
      <tr>
        <td><?php echo $version->version ?></td>
        <td><?php echo $version->title ?></td>
        <td><?php echo $version->updated_at ?></td>
        <td><input type="radio" name="version1" value="<?php echo $version->version ?>" /></td>
        <td><input type="radio" name="version2" value="<?php echo $version->version ?>" /></td>
        <td>
          <ul class="rt-admin-tools">
            <li><?php echo rt_ui_button('revert', 'rtShopProductAdmin/Revert?id='.$rt_shop_product->getId().'&revert_to='.$version->version, 'arrowrefresh-1-e'); ?></li>
          </ul>
        </td>
      <?php endforeach; ?>
    </tbody>
  </table>
</form>
