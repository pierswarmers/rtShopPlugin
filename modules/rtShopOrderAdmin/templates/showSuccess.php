<?php use_helper('I18N', 'Number', 'rtAdmin') ?>

<h1><?php echo __('Order Invoice') ?></h1>

<?php slot('rt-tools') ?>
<ul id="rtPrimaryTools">
  <li><button class="cancel"><?php echo __('Cancel/List') ?></button></li>
</ul>

<script type="text/javascript">
	$(function() {
    $("#rtPrimaryTools .cancel").button({
      icons: { primary: 'ui-icon-cancel' }
    }).click(function(){ document.location.href='<?php echo url_for('rtShopOrderAdmin/index') ?>'; });
	});
</script>
<?php end_slot(); ?>

<?php slot('rt-side') ?>
  <?php include_partial('status_update', array('rt_shop_order' => $rt_shop_order)) ?>
<?php end_slot(); ?>

<?php include_partial('rtAdmin/flashes') ?>

<?php include_partial('rtShopOrderAdmin/invoice_html', array('rt_shop_order' => $rt_shop_order)) ?>