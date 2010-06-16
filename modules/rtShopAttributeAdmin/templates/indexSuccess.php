<?php use_helper('I18N', 'rtAdmin') ?>

<h1><?php echo __('Listing Attributes') ?></h1>

<?php slot('rt-tools') ?>
<?php include_partial('rtAdmin/standard_modal_tools', array('object' => new rtShopAttribute))?>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<table>
  <thead>
    <tr>
      <th><?php echo __('Title') ?></th>
      <th><?php echo __('Display Title') ?></th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rt_shop_attributes as $rt_shop_attribute): ?>
    <tr>
      <td><a href="<?php echo url_for('rtShopAttributeAdmin/edit?id='.$rt_shop_attribute->getId()) ?>"><?php echo $rt_shop_attribute->getTitle() ?></a></td>
      <td><?php echo $rt_shop_attribute->getDisplayTitle() ?></td>
      <td>
        <ul class="rt-admin-tools">
          <li><?php echo rt_button_edit(url_for('rtShopAttributeAdmin/edit?id='.$rt_shop_attribute->getId())) ?></li>
          <li><?php echo rt_button_delete(url_for('rtShopAttributeAdmin/delete?id='.$rt_shop_attribute->getId())) ?></li>
        </ul>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
