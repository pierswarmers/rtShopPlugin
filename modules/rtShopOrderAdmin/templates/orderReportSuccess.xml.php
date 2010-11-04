<?php $orders = $sf_data->getRaw('orders') ?>
<?xml version="1.0"?>
<orderReport xmlns="http://www.reditype.com">
  <?php foreach($orders as $order): ?>
    <order>
      <id><?php echo $order['o_id'] ?></id>
      <reference><?php echo $order['o_reference'] ?></reference>
      <status><?php echo $order['o_status'] ?></status>
      <is_wholesale><?php echo $order['o_is_wholesale'] ?></is_wholesale>
      <email_address><?php echo $order['o_email_address'] ?></email_address>
      <user_id><?php echo $order['o_user_id'] ?></user_id>
      <shipping_charge><?php echo $order['o_shipping_charge'] ?></shipping_charge>
      <taxes>
        <tax>
          <charge><?php echo $order['o_tax_charge'] ?></charge>
          <component><?php echo $order['o_tax_component'] ?></component>
          <mode><?php echo $order['o_tax_mode'] ?></mode>
          <rate><?php echo $order['o_tax_rate'] ?></rate>
        </tax>
      </taxes>
      <promotions>
        <?php if(isset($order['o_promotion_data'][0])): ?>
          <?php $i=0; foreach($order['o_promotion_data'] as $promotion): ?>
            <promotion>
              <id><?php echo $order['o_promotion_id'] ?></id>
              <reduction><?php echo $order['o_promotion_reduction'] ?></reduction>
              <data>
                <?php foreach($order['o_promotion_data'][$i] as $key => $value): ?>
                  <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
                <?php endforeach; ?>
              <data>
            </promotion>
          <?php $i++; endforeach; ?>
        <?php elseif(is_array($order['o_promotion_data'])): ?>
          <promotion>
              <id><?php echo $order['o_promotion_id'] ?></id>
              <reduction><?php echo $order['o_promotion_reduction'] ?></reduction>
              <data>
                <?php foreach($order['o_promotion_data'] as $key => $value): ?>
                  <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
                <?php endforeach; ?>
              </data>
          </promotion>
        <?php endif; ?>
      </promotions>
      <vouchers>
        <?php if(isset($order['o_voucher_data'][0])): ?>
          <?php $i=0; foreach($order['o_voucher_data'] as $voucher): ?>
            <voucher>
              <id><?php echo $order['o_voucher_id'] ?></id>
              <reduction><?php echo $order['o_voucher_reduction'] ?></reduction>
              <code><?php echo $order['o_voucher_reduction'] ?></code>
              <data>
                <?php foreach($order['o_voucher_data'][$i] as $key => $value): ?>
                  <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
                <?php endforeach; ?>
              </data>
            </voucher>
          <?php $i++; endforeach; ?>
        <?php elseif(is_array($order['o_voucher_data'])): ?>
          <voucher>
              <id><?php echo $order['o_voucher_id'] ?></id>
              <reduction><?php echo $order['o_voucher_reduction'] ?></reduction>
              <code><?php echo $order['o_voucher_reduction'] ?></code>
              <data>
                <?php foreach($order['o_voucher_data'] as $key => $value): ?>
                  <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
                <?php endforeach; ?>
              </data>
          </voucher>
        <?php endif; ?>
      </vouchers>
      <products>
        <?php if(isset($order['o_products_data'][0])): ?>
          <?php $i=0; foreach($order['o_products_data'] as $product): ?>
            <product>
              <data>
                <?php foreach($order['o_products_data'][$i] as $key => $value): ?>
                  <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
                <?php endforeach; ?>
              </data>
            </product>
          <?php $i++; endforeach; ?>
        <?php elseif(is_array($order['o_products_data'])): ?>
          <product>
            <product>
                <data>
                  <?php foreach($order['o_products_data'] as $key => $value): ?>
                    <<?php echo $key ?>><?php echo $value ?></<?php echo $key ?>>
                  <?php endforeach; ?>
                </data>
            </product>
          </product>
        <?php endif; ?>
      </products>
      <items_charge><?php echo $order['o_items_charge'] ?></items_charge>
      <total_charge><?php echo $order['o_total_charge'] ?></total_charge>
      <payment></payment>
      <created_at><?php echo $order['o_created_at'] ?></created_at>
      <updated_at><?php echo $order['o_updated_at'] ?></updated_at>
    </order>
  <?php endforeach; ?>
  
</orderReport>