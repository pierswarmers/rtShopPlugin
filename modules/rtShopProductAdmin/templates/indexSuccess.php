<?php use_helper('I18N', 'rtAdmin') ?>

<h1><?php echo __('Listing Products') ?></h1>

<?php slot('rt-tools') ?>
<?php include_partial('rtAdmin/standard_modal_tools', array('object' => new rtShopProduct))?>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<table>
  <thead>
    <tr>
      <th><?php echo __('Title') ?></th>
      <th><?php echo __('Published') ?></th>
      <th><?php echo __('Version') ?></th>
      <th><?php echo __('Created at') ?></th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rt_shop_products as $rt_shop_product): ?>
    <tr>
      <td><a href="<?php echo url_for('rtShopProductAdmin/edit?id='.$rt_shop_product->getId()) ?>"><?php echo $rt_shop_product->getTitle() ?></a></td>
      <td><?php echo rt_nice_boolean($rt_shop_product->getPublished()) ?></td>
      <td><?php echo link_to_if($rt_shop_product->version > 1, $rt_shop_product->version, 'rtShopProductAdmin/versions?id='.$rt_shop_product->getId()) ?></td>
      <td><?php echo $rt_shop_product->getCreatedAt() ?></td>
      <td>
        <ul class="rt-admin-tools">
          <li><?php echo rt_button_show(url_for('rt_shop_product_show', $rt_shop_product)) ?></li>
          <li><?php echo rt_button_edit(url_for('rtShopProductAdmin/edit?id='.$rt_shop_product->getId())) ?></li>
          <li><?php echo rt_button_delete(url_for('rtShopProductAdmin/delete?id='.$rt_shop_product->getId())) ?></li>
        </ul>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
