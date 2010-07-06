<?php use_helper('I18N', 'Number', 'Text', 'rtAdmin') ?>

<h1><?php echo __('Listing Orders') ?></h1>

<?php slot('rt-tools') ?>
<?php include_partial('rtAdmin/standard_modal_tools', array('object' => new rtShopOrder))?>
<?php end_slot(); ?>

<table>
  <thead>
    <tr>
      <th><?php echo __('Id') ?></th>
      <th><?php echo __('Details') ?></th>
      <th><?php echo __('Total') ?></th>
      <th><?php echo __('Status') ?></th>
      <th><?php echo __('Created at') ?></th>
      <th><?php echo __('Actions') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rt_shop_orders as $rt_shop_order): ?>
      <tr>
        <td><a href="<?php echo url_for('rtShopOrderAdmin/edit?id='.$rt_shop_order->getId()) ?>"><?php echo $rt_shop_order->getId() ?></a></td>
        <td><code><?php echo $rt_shop_order->getReference() ?> (<?php echo truncate_text($rt_shop_order->getEmail(),14,'...',true) ?>)</code></td>
        <td><?php echo format_currency($rt_shop_order->getClosedTotal(), sfConfig::get('app_rt_currency', 'USD')); ?></td>
        <td><?php echo strtoupper($rt_shop_order->getStatus()) ?></td>
        <td><?php echo $rt_shop_order->getCreatedAt() ?></td>
        <td>
        <ul class="rt-admin-tools">
          <li><?php echo rt_button_show(url_for('rtShopOrderAdmin/show?id='.$rt_shop_order->getId())) ?></li>
          <!--- <li><?php echo rt_button_edit(url_for('rtShopOrderAdmin/edit?id='.$rt_shop_order->getId())) ?></li> --->
          <li><?php echo rt_button_delete(url_for('rtShopOrderAdmin/delete?id='.$rt_shop_order->getId())) ?></li>
        </ul>
      </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>