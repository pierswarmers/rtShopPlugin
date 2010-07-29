<?php use_helper('I18N') ?>
<p><?php echo __('Hi %first_name%', array('%first_name%' => $user->getFirstName()), 'sf_guard') ?>,</p>
<p><?php echo __("Thanks for registering! To show our appreciation, here's a voucher code to use with your next order.") ?></p>
<p><?php echo __('Voucher code', null, 'sf_guard') ?>: <code><?php echo $voucher->getCode() ?></code></p>