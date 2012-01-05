<?php use_helper('I18N', 'rtAdmin') ?>

<h1><?php echo __('Editing Order:').' '.$rt_shop_order->getReference() ?></h1>

<?php include_partial('form', array('form' => $form)) ?>