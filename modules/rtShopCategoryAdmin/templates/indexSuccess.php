<?php use_helper('I18N', 'rtAdmin') ?>

<h1><?php echo __('Listing Shop Categories') ?></h1>

<?php slot('rt-tools') ?>
<?php include_partial('rtAdmin/standard_modal_tools', array('object' => new rtShopCategory))?>
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
    <?php foreach ($rt_shop_categorys as $rt_shop_category): ?>
    <tr class="rt-admin-tree rt-admin-tree-level-<?php echo $rt_shop_category->level ?>">
      <td class="rt-admin-title"><a href="<?php echo url_for('rtShopCategoryAdmin/edit?id='.$rt_shop_category->getId()) ?>"><?php echo $rt_shop_category->getTitle() ?></a></td>
      <td><?php echo rt_nice_boolean($rt_shop_category->getPublished()) ?></td>
      <td><?php echo link_to_if($rt_shop_category->version > 1, $rt_shop_category->version, 'rtShopCategoryAdmin/versions?id='.$rt_shop_category->getId()) ?></td>
      <td><?php echo $rt_shop_category->getCreatedAt() ?></td>
      <td>
        <ul class="rt-admin-tools">
          <li><?php echo rt_button_show(url_for('rtShopCategoryAdmin/show?id='.$rt_shop_category->getId())) ?></li>
          <li><?php echo rt_button_edit(url_for('rtShopCategoryAdmin/edit?id='.$rt_shop_category->getId())) ?></li>
          <li><?php echo rt_button_delete(url_for('rtShopCategoryAdmin/delete?id='.$rt_shop_category->getId())) ?></li>
          <?php if($rt_shop_category->getNode()->isRoot()): ?>
          <li><?php echo rt_ui_button(__('tree'), 'rtShopCategoryAdmin/tree' . '?root=' . $rt_shop_category->getRootId(), 'arrow-1-e') ?></li>
          <?php endif; ?>
        </ul>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
