<?php

use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtShopCategory');

$options = isset($options) ? $options->getRawValue() : array();
$rt_shop_category = isset($rt_shop_category) ? $rt_shop_category : null;

?>

<?php echo rt_shop_category_map($rt_shop_category, $options) ?>
