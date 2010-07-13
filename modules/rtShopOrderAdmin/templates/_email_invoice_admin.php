<?php use_helper('I18N') ?>

<?php include_partial('rtShopOrderAdmin/email_invoice', array('rt_shop_order' => $rt_shop_order)) ?>

<p><?php echo __('See this order online') ?> <?php echo link_to('See this order online ', 'rtShopOrderAdmin/show?id='.$rt_shop_order->getId(), array()) ?></p>

