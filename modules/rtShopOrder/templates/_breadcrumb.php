<?php
$links = array(
    'cart'       => array('position' => 1,  'title' => ucwords(__(sfConfig::get('rt_shop_cart_name', 'shopping cart'))),    'route' => 'rt_shop_order_cart'),
    'membership' => array('position' => 2,  'title' => 'Membership','route' => 'rt_shop_order_membership'),
    'address'    => array('position' => 3,  'title' => 'Address', 'route' => 'rt_shop_order_address'),
    'payment'    => array('position' => 4,  'title' => 'Payment', 'route' => 'rt_shop_order_payment'),
    'receipt'    => array('position' => 5,  'title' => 'Receipt', 'route' => 'rt_shop_order_cart')
);
?>
<ol class="rt-shop-order-breadcrumb"><?php $class = ' class="past"'; foreach($links as $key => $link): ?><?php $class = ($key === $sf_request->getParameter('action')) ? ' class="here"' : $class; ?><li<?php echo $class ?>><?php echo link_to_if($sf_request->getParameter('action') !== $key && $class !== '', $link['title'],$link['route']) ?></li><?php $class = ($class == ' class="here"') ? '' : $class; ?><?php endforeach; ?></ol>