<?php use_helper('I18N', 'rtAdmin', 'Number') ?>

<h1><?php echo __('Listing Shop Promotions') ?></h1>

<?php slot('rt-tools') ?>
<?php include_partial('rtAdmin/standard_modal_tools', array('object' => new rtShopPromotion))?>
<h2><?php echo __('Promotions Summary') ?></h2>
<dl class="rt-admin-summary-panel clearfix">
  <dt class="rt-admin-primary"><?php echo __('Total') ?></dt>
  <dd class="rt-admin-primary"><?php echo $stats['total']['count'] ?></dd>
  <dt><?php echo __('Active') ?></dt>
  <dd><?php echo $stats['total_active']['count'] ?></dd>
  <dt><?php echo __('Cart') ?></dt>
  <dd><?php echo $stats['total_cart']['count'] ?></dd>
  <dt><?php echo __('Product') ?></dt>
  <dd><?php echo $stats['total_product']['count'] ?></dd>
</dl>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<table>
  <thead>
    <tr>
      <th><?php echo __('Title') ?></th>
      <th><?php echo __('Type') ?></th>
      <th><?php echo __('Reduction') ?></th>
      <th><?php echo __('Created at') ?></th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($pager->getResults() as $rt_shop_promotion): ?>
      <tr>
        <td><a href="<?php echo url_for('rtShopPromotionAdmin/edit?id='.$rt_shop_promotion->getId()) ?>"><?php echo $rt_shop_promotion->getTitle() ?></a></td>
        <td><?php echo $rt_shop_promotion->getType() == 'rtShopPromotionCart' ? __('Cart') : __('Product')  ?></td>
        <td><?php echo $rt_shop_promotion->isPercentageOff() ? $rt_shop_promotion->getReductionValue() . '%' : format_currency($rt_shop_promotion->getReductionValue(), sfConfig::get('app_rt_currency', 'USD'), $sf_user->getCulture()) ?></td>
        <td><?php echo $rt_shop_promotion->getCreatedAt() ?></td>
        <td>
        <ul class="rt-admin-tools">
          <li><?php echo rt_button_edit(url_for('rtShopPromotionAdmin/edit?id='.$rt_shop_promotion->getId())) ?></li>
          <li><?php echo rt_button_delete(url_for('rtShopPromotionAdmin/delete?id='.$rt_shop_promotion->getId())) ?></li>
        </ul>
      </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include_partial('rtAdmin/pagination', array('pager' => $pager)); ?>
