<?php use_helper('I18N') ?>
<p><?php echo __('Hi') . ' ' . $user['first_name'] ?>,</p>
<p><strong><?php echo __('Happy Birthday!!') ?></strong></p>
<p><?php echo __('A voucher with code: #') ?><?php echo $code ?> <?php echo __(' and a value of ') ?> <?php echo $value ?> <?php echo __('was created for you') ?>.</p>