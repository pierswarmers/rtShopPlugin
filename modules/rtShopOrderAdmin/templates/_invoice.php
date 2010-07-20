<?php use_helper('I18N', 'Number', 'rtAdmin') ?>
<?php $addresses = $rt_shop_order->getAddressInfoArray(); ?>
<?php $shipping = (count($addresses) == 2) ? 1 : 0; ?>
<table>
  <tbody>
    <tr>
      <th style="width:25%"><?php echo __('Order reference') ?>:</th>
      <td style="width:25%"><?php echo $rt_shop_order->getReference() ?></td>
      <td style="width:50%" rowspan="7"><?php echo nl2br(sfConfig::get('app_rt_company_address','')) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Payment transaction ID') ?>:</th>
      <td><?php echo $rt_shop_order->getPaymentTransactionId() ?></td>
    </tr>
    <tr>
      <th><?php echo __('Payment charge') ?>:</th>
      <td><?php echo format_currency($rt_shop_order->getPaymentCharge(), 'AUD') ?></td>
    </tr>
    <tr>
      <th><?php echo __('Status') ?>:</th>
      <td><?php echo strtoupper($rt_shop_order->getStatus()) ?></td>
    </tr>
    <?php if($rt_shop_order->getVoucherCode()): ?>
      <tr>
        <th><?php echo __('Voucher code') ?>:</th>
        <td><?php echo $rt_shop_order->getVoucherCode() ?></td>
      </tr>
    <?php endif; ?>
    <tr>
      <th><?php echo __('Date') ?>:</th>
      <td><?php echo date("d F Y", strtotime($rt_shop_order->getCreatedAt())) ?></td>
    </tr>
    <tr>
      <th><?php echo __('Email') ?>:</th>
      <td><?php echo mail_to($rt_shop_order->getEmailAddress()) ?></td>
    </tr>
  </tbody>
</table>
<table>
  <thead>
    <tr>
      <th><?php echo __('Billing Address') ?></th>
      <th><?php echo __('Shipping Address') ?></th>
    </tr>
  </thead>
  <?php if(count($addresses) > 0): ?>
  <tbody>
    <tr>
      <td style="width:50%"><?php echo $addresses[0]['first_name'] . " " . $addresses[0]['last_name'] ?><br/>
        <?php echo $addresses[0]['address_1'] ?><br/>
        <?php echo ($addresses[0]['address_2'] != '') ? $addresses[0]['address_2'].'<br/>' : '' ?>
        <?php echo $addresses[0]['town'] . " " . $addresses[0]['postcode'] . " " . $addresses[0]['state'] ?><br/>
        <?php echo $addresses[0]['country'] ?></td>
      <td style="width:50%"><?php echo $addresses[$shipping]['first_name'] . " " . $addresses[$shipping]['last_name'] ?><br/>
        <?php echo $addresses[$shipping]['address_1'] ?><br/>
        <?php echo ($addresses[$shipping]['address_2'] != '') ? $addresses[$shipping]['address_2'].'<br/>' : '' ?>
        <?php echo $addresses[$shipping]['town'] . " " . $addresses[$shipping]['postcode'] . " " . $addresses[$shipping]['state'] ?><br/>
        <?php echo $addresses[$shipping]['country'] ?></td>
    </tr>
    <tr>
      <td><?php if($addresses[0]['phone'] != ''): ?>
          <?php echo __('Phone') ?>: <?php echo $addresses[0]['phone'] ?>
        <?php endif; ?></td>
      <td><?php if($addresses[$shipping]['phone'] != ''): ?>
        <?php echo __('Phone') ?>: <?php echo $addresses[$shipping]['phone'] ?>
        <?php endif; ?></td>
    </tr>
    <tr>
      <td><?php if($addresses[0]['instructions'] != ''): ?>
          <?php echo __('Instructions') ?>: <?php echo $addresses[0]['instructions'] ?>
        <?php endif; ?></td>
      <td><?php if($addresses[$shipping]['instructions'] != ''): ?>
        <?php echo __('Instructions') ?>: <?php echo $addresses[$shipping]['instructions'] ?>
        <?php endif; ?></td>
    </tr>
  </tbody>
  <?php endif; ?>
</table>
<?php include_partial('rtShopOrderAdmin/archive', array('rt_shop_order' => $rt_shop_order)) ?>