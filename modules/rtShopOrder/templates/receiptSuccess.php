<?php use_helper('I18N') ?>
<?php

$address = $rt_shop_order->getShippingAddress();

if(!$address)
{
  $address = $rt_shop_order->getBillingAddress();
}

?>

<?php slot('rt-title') ?>
<?php echo ucwords(__(sfConfig::get('rt_shop_receipt_title', 'receipt'))) ?>
<?php end_slot(); ?>

<?php
  $order_received = '<h3>' . __('Thank you!') . '</h3>';
  $order_received .= '<p>'  . sprintf('%s%s%s',__('Your order has been received. The order reference is #'),$rt_shop_order->getReference(),__(' and a confirmation email has been sent to you.')) . '</p>';
?>

<?php include_component('rtSnippet','snippetPanel', array('collection' => 'shop-receipt-message','sf_cache_key' => 'shop-receipt-message', 'default' => $order_received)); ?>

<?php if(sfConfig::get('app_rt_analytics_ecommerce_enabled', false)): ?>
<script type="text/javascript">

  _gaq.push(['_addTrans',
    '<?php echo $rt_shop_order->getReference() ?>',
    '<?php echo sfConfig::get('app_rt_title') ?>',
    '<?php echo $rt_shop_order->getTotalCharge() ?>',
    '<?php echo $rt_shop_order->getTaxCharge() ?>',
    '<?php echo $rt_shop_order->getShippingCharge() ?>'<?php if($address): ?>,
    '<?php echo $address->getTown() ?>',
    '<?php echo $address->getState() ?>',
    '<?php echo $address->getCountry() ?>'<?php endif; ?>
  ]);
  <?php foreach($rt_shop_order->getProductsData() as $product): ?>
  _gaq.push(['_addItem',
    '<?php echo $rt_shop_order->getReference() ?>',
    '<?php echo $product['sku'] ?>',
    '<?php echo $product['title'] ?>',
    '<?php echo ($product['variations'] != '' && !empty ($product['variations'])) ? sprintf('%s',$product['variations']) : ''; ?>',
    '<?php echo $product['charge_price'] ?>',
    '<?php echo $product['quantity'] ?>'
  ]);
  <?php endforeach; ?>
  _gaq.push(['_trackTrans']);

</script>
<?php endif; ?>