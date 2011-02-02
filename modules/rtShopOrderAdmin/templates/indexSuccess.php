<?php use_javascript('/rtCorePlugin/vendor/raphael/raphael.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/raphael/g.raphael.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/raphael/g.bar.min.js') ?>
<?php use_helper('I18N', 'Number', 'rtAdmin') ?>
<?php $is_dispatch_user = $sf_user->hasCredential(sfConfig::get('app_rt_shop_order_dispatch_credential', 'admin_shop_order_dispatch')); ?>

<h1><?php echo __('Listing Orders') ?> <?php echo $is_dispatch_user ? __('for Dispatch') : '' ?></h1>

<?php slot('rt-tools') ?>
<?php if($is_dispatch_user && !$sf_user->isSuperAdmin()): ?>
  <h2><?php echo __('Dispatch Summary') ?></h2>
  <dl class="rt-admin-summary-panel clearfix">
    <dt class="rt-admin-primary"><?php echo __('Picking') ?></dt>
    <dd class="rt-admin-primary"><?php echo $stats['picking']['count'] ?></dd>
  </dl>
<?php else: ?>
  <ul id="rtPrimaryTools">
    <li><button class="reports"><?php echo __('View order report') ?></button></li>
    <li><button class="graphs-quarterly"><?php echo __('View sales charts') ?></button></li>
  </ul>
  <script type="text/javascript">
      $(function() {
      $("#rtPrimaryTools .reports").button({
        icons: { primary: 'ui-icon-transfer-e-w' }
      }).click(function(){ document.location.href='<?php echo url_for('rtShopOrderAdmin/orderReport') ?>'; });
      $("#rtPrimaryTools .graphs-quarterly").button({
        icons: { primary: 'ui-icon-image' }
      }).click(function(){ document.location.href='<?php echo url_for('rtShopOrderAdmin/graph') ?>'; });
      });
  </script>
  <h2><?php echo __('Sales Summary') ?></h2>
  <dl class="rt-admin-summary-panel clearfix">
    <dt class="rt-admin-primary"><?php echo __('Today') ?> (<?php echo $stats['today']['count'] ?>)</dt>
    <dd class="rt-admin-primary"><?php echo format_currency($stats['today']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
    <dt><?php echo __('Yesterday') ?> (<?php echo $stats['yesterday']['count'] ?>)</dt>
    <dd><?php echo format_currency($stats['yesterday']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
    <dt><?php echo __('This Month') ?> (<?php echo $stats['month_current']['count'] ?>)</dt>
    <dd><?php echo format_currency($stats['month_current']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
    <dt><?php echo __('Last Month') ?> (<?php echo $stats['month_last']['count'] ?>)</dt>
    <dd><?php echo format_currency($stats['month_last']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
    <dt><?php echo __('Total') ?> (<?php echo $stats['total']['count'] ?>)</dt>
    <dd><?php echo format_currency($stats['total']['revenue'], sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
    <dt><?php echo __('Average Order Value') ?></dt>
    <dd><?php echo format_currency(($stats['total']['count'] > 0) ? $stats['total']['revenue']/$stats['total']['count'] : 0.0, sfConfig::get('app_rt_currency', 'AUD')) ?></dd>
  </dl>
  <h2><?php echo __('30 Day Summary') ?></h2>
  <?php $orders_by_day = $sf_data->getRaw('orders_by_day') ?>
  <script type="text/javascript" charset="utf-8">
    window.onload = function () {
      var r = Raphael("graph-summary-one-month");
      r.g.txtattr.font = "10px 'Fontin Sans', Fontin-Sans, sans-serif";

      fin = function () {
        this.flag = r.g.popup(this.bar.x, this.bar.y, '$' + this.bar.value || "0").insertBefore(this);
      },
      fout = function () {
        this.flag.animate({opacity: 0}, 300, function () {this.remove();});
      },

      // X-Axis
      xaxis                  = r.path("M22 131 L202 131");
      xaxis_middle           = r.path("M22 81 L202 81").attr("stroke", "#ccc").attr("stroke-width", "0.5");

      // Y-Axis
      yaxis                  = r.path("M21 138 L21 25");
      yaxis_middle           = r.path("M116 138 L116 25").attr("stroke", "#ccc").attr("stroke-width", "0.5");
      yaxis_middle_inidcator = r.path("M116 138 L116 131");
      yaxis_end              = r.path("M202 138 L202 25");

      // Labels
      day01 = r.text(20, 155, "<?php echo date('M d',strtotime(sprintf("-%s days",30))) ?>").rotate(45);
      day15 = r.text(115, 155, "<?php echo date('M d',strtotime(sprintf("-%s days",15))) ?>").rotate(45);
      day30 = r.text(200, 155, "<?php echo date('M d') ?>").rotate(45);

      // Draw bar chart graph
      values = [[<?php echo implode(',', array_reverse($orders_by_day)) ?>]];
      chart = r.g.barchart(20, 10, 210, 140, values, {stacked: false, type: "soft"});
      chart.attr("fill", "#ccc");
      chart.hover(fin, fout);
    };
  </script>
  <div id="graph-summary-one-month" class="rt-graph-holder" style="height:240px; width: 240px"></div>
<?php endif; ?>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<table>
  <thead>
    <tr>
      <th><?php echo __('Details') ?></th>
      <?php if(!$is_dispatch_user): ?>
        <th><?php echo __('Total') ?></th>
      <?php endif;?>
      <th><?php echo __('Status') ?></th>
      <th><?php echo __('Email address') ?></th>
      <th><?php echo __('Created at') ?></th>
      <th><?php echo __('Actions') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($pager->getResults() as $rt_shop_order): ?>
      <tr>
        <td><a href="<?php echo url_for('rtShopOrderAdmin/show?id='.$rt_shop_order->getId()) ?>"><code><?php echo $rt_shop_order->getReference() ?></code></a></td>
        <?php if(!$is_dispatch_user): ?>
          <td><?php echo format_currency($rt_shop_order->getTotalCharge(), sfConfig::get('app_rt_currency', 'AUD')); ?></td>
        <?php endif;?>
        <td><?php echo strtoupper($rt_shop_order->getStatus()) ?></td>
        <td><?php echo $rt_shop_order->getEmailAddress(); ?></td>
        <td><?php echo $rt_shop_order->getCreatedAt() ?></td>
        <td>
        <ul class="rt-admin-tools">
          <li><?php echo rt_button_show(url_for('rtShopOrderAdmin/show?id='.$rt_shop_order->getId())) ?></li>
          <?php if(!$is_dispatch_user): ?>
            <li><?php echo rt_button_delete(url_for('rtShopOrderAdmin/delete?id='.$rt_shop_order->getId())) ?></li>
          <?php endif;?>
        </ul>
      </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include_partial('rtAdmin/pagination', array('pager' => $pager)); ?>