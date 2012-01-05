<?php use_helper('I18N') ?>
<?php echo __('Order for') ?> <?php echo $user ? sprintf('%s %s',$user['first_name'],$user['last_name']): '' ?> <?php echo $rt_shop_order->getEmailAddress() ?>


<?php echo url_for('@rt_shop_order_show?id='.$rt_shop_order->getId(),true) ?>