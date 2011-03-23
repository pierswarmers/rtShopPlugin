<?php use_helper('I18N', 'Number') ?>

<p><?php echo sprintf('Hi %s,',$voucher_session_array['first_name']) ?></p>

<p><?php echo sprintf('You have received a %s gift voucher from %s %s.',format_currency($rt_shop_voucher->getReductionValue(), sfConfig::get('app_rt_currency', 'AUD')),$sender_fname,$sender_lname) ?></p>

<?php if($voucher_session_array['message'] !== ''): ?>
<p><?php echo $sender_fname ?> <?php echo __('said') ?>:</p>
<blockquote><p><?php echo $voucher_session_array['message'] ?></p></blockquote>
<?php endif; ?>

<p><?php echo __('You can use this voucher at our online store') ?>: <?php echo link_to(rtSiteToolkit::getCurrentDomain(null, true),url_for('@rt_shop_order_voucher_redeem?code='.$rt_shop_voucher->getCode().'&redirect='.str_replace('/frontend_dev.php', '', url_for('@homepage',true)), true)) ?>.</p>

<p><?php echo __('Simply enter the voucher code') ?> (<code><?php echo $rt_shop_voucher->getCode() ?></code>) <?php echo __('in the payment step of ordering, to use your') ?> <?php echo format_currency($rt_shop_voucher->getReductionValue(), sfConfig::get('app_rt_currency', 'AUD')) ?> <?php echo __('gift voucher') ?>.</p>