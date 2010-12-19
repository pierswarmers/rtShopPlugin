<?php use_helper('I18N', 'rtAdmin') ?>

<h1><?php echo __('Listing Products') ?></h1>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li><button class="create"><?php echo __('Create new product') ?></button></li>
  <li><button class="reports"><?php echo __('View stock report') ?></button></li>
</ul>
<script type="text/javascript">
	$(function() {
      $("#rtPrimaryTools .create").button({
        icons: { primary: 'ui-icon-transfer-e-w' }
      }).click(function(){ document.location.href='<?php echo url_for('rtShopProductAdmin/new') ?>'; });

      $("#rtPrimaryTools .reports").button({
        icons: { primary: 'ui-icon-transfer-e-w' }
      }).click(function(){ document.location.href='<?php echo url_for('rtShopProductAdmin/stockReport') ?>'; });

      enablePublishToggle('<?php echo url_for('rtShopProductAdmin/toggle') ?>');
	});
</script>
<h2><?php echo __('Products Summary') ?></h2>
<dl class="rt-admin-summary-panel clearfix">
  <dt class="rt-admin-primary"><?php echo __('Total') ?></dt>
  <dd class="rt-admin-primary"><?php echo $stats['total']['count'] ?></dd>
  <dt><?php echo __('Published') ?></dt>
  <dd><?php echo $stats['total_published']['count'] ?></dd>
</dl>
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
    <?php foreach ($pager->getResults() as $rt_shop_product): ?>
    <tr>
      <td><a href="<?php echo url_for('rtShopProductAdmin/edit?id='.$rt_shop_product->getId()) ?>"><?php echo $rt_shop_product->getTitle() ?></a></td>
      <td class="rt-admin-publish-toggle">
        <?php echo rt_nice_boolean($rt_shop_product->getPublished()) ?>
        <div style="display:none;"><?php echo $rt_shop_product->getId() ?></div>
      </td>
      <td><?php echo link_to_if($rt_shop_product->version > 1, $rt_shop_product->version, 'rtShopProductAdmin/versions?id='.$rt_shop_product->getId()) ?></td>
      <td><?php echo $rt_shop_product->getCreatedAt() ?></td>
      <td>
        <ul class="rt-admin-tools">
          <li><?php echo rt_button_show(url_for('rtShopProductAdmin/show?id='.$rt_shop_product->getId())) ?></li>
          <li><?php echo rt_button_edit(url_for('rtShopProductAdmin/edit?id='.$rt_shop_product->getId())) ?></li>
          <li><?php echo rt_button_delete(url_for('rtShopProductAdmin/delete?id='.$rt_shop_product->getId())) ?></li>
        </ul>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include_partial('rtAdmin/pagination', array('pager' => $pager)); ?>