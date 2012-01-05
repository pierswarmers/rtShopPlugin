<?php

/** @var rtShopProduct $rt_shop_product */

use_helper('I18N');

slot('rt-title', $rt_shop_product->getTitle());

?>

<?php include_partial('shop_product', array('rt_shop_product' => $rt_shop_product, 'related_products' => $related_products, 'sf_cache_key' => $rt_shop_product->getId())) ?>