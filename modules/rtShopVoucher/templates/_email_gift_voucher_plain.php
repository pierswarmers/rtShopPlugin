<?php use_helper('I18N', 'Number') ?>

<?php echo sprintf('Hi %s,',$voucher_session_array['first_name']) ?>  

<?php echo sprintf('You have received a %s gift voucher from %s %s.',format_currency($rt_shop_voucher->getReductionValue(), sfConfig::get('app_rt_currency', 'AUD')),$sender_fname,$sender_lname) ?>  

<?php if($voucher_session_array['message'] !== ''): ?>
<?php echo $sender_fname ?> <?php echo __('said') ?>:  
-----------------------------------------------------------------------  
<?php echo str_replace("&#039;", "`", $voucher_session_array['message']) ?>  
-----------------------------------------------------------------------
<?php endif; ?>

<?php echo __('You can use this voucher at our online store') ?>: <?php echo str_replace('/frontend_dev.php', '', url_for('@rt_shop_order_voucher_redeem?code='.$rt_shop_voucher->getCode().'&redirect='.urldecode(url_for('@homepage',true)),true)) ?>.

<?php echo __('Simply enter the voucher code') ?> (<code><?php echo $rt_shop_voucher->getCode() ?></code>) <?php echo __('in the payment step of ordering, to use your') ?> <?php echo format_currency($rt_shop_voucher->getReductionValue(), sfConfig::get('app_rt_currency', 'AUD')) ?> <?php echo __('gift voucher') ?>.