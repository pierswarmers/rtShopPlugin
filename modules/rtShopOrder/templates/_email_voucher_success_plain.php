<?php use_helper('I18N') ?>
<?php echo __('Hi %first_name%', array('%first_name%' => $user->getFirstName()), 'sf_guard') ?>, 
 
<?php echo html_entity_decode(__("Thanks for registering! To show our appreciation, here is a voucher code to use with your next order.")) ?> 
 
<?php echo __('Voucher code', null, 'sf_guard') ?>: <?php echo $voucher->getCode() ?>