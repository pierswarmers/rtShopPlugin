<?php use_helper('I18N') ?>
<?php echo $message == '' ? __('Thought you might be interested in this') : $message ?>:<br />
<br />
<?php echo link_to($rt_shop_product->getTitle(), 'rt_shop_product_show', $rt_shop_product, array('absolute' => true)) ?>