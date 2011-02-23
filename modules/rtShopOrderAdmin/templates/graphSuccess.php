<?php use_javascript('/rtCorePlugin/vendor/raphael/raphael.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/raphael/g.raphael.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/raphael/g.line.min.js') ?>
<?php use_helper('I18N', 'rtAdmin') ?>

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

<h1><?php echo __('Quarterly Summary - Sales Order Analysis') ?></h1>

<script type="text/javascript" charset="utf-8">
    window.onload = function () {
        // Graph 1 ---------------------------------------------------------- //
        var r1 = Raphael("graph-income-total");
        r1.g.txtattr.font = "10px 'Fontin Sans', Fontin-Sans, sans-serif";

        values_x = [<?php echo $days_in_months ?>];
        values_y = [<?php echo $revenue_per_day_in_months ?>];
        chart    = r1.g.linechart(60, 5, 680, 300, values_x, values_y, {shade: true, "colors":["#1751a7"], nostroke: false, axis: "0 0 0 1", smooth: false, symbol: "o"}); // symbol: "o"
        chart.symbols.attr({stroke: "#1751a7", r: 2});

        // X and Y axis
        var l1 = r1.path("M70 305 L70 15").attr({"stroke-width": 1, stroke: "#000"});    // Y-axis left
        var l1 = r1.path("M730 305 L730 15").attr({"stroke-width": 1, stroke: "#000"});  // Y-axis right
        var l1 = r1.path("M70 295 L730 295").attr({"stroke-width": 1, stroke: "#000"});  // X-axis bottom
        var l1 = r1.path("M70 15 L730 15").attr({"stroke-width": 1, stroke: "#000"});    // X-axis top

        // Months indicators
        var l1 = r1.path("M180 305 L180 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 5 month
        var l1 = r1.path("M290 305 L290 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 4 month
        var l1 = r1.path("M400 305 L400 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 3 month
        var l1 = r1.path("M510 305 L510 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 2 month
        var l1 = r1.path("M620 305 L620 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 1 month

        // X-axis separators
        var l1 = r1.path("M70 202 L730 202").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // 1/3 of available height
        var l1 = r1.path("M70 109 L730 109").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // 1/3 of available height

        // X-axis labels
        <?php $cur_day = date('d'); ?>
        <?php $cur_month = date('m'); ?>
        <?php $cur_year = date('Y'); ?>
        month06 = r1.text(70, 315, "<?php echo date('M d Y', mktime(0, 0, 0, $cur_month - 6, $cur_day, $cur_year)) ?>");
        month05 = r1.text(180, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 5, $cur_day, $cur_year)) ?>");
        month04 = r1.text(290, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 4, $cur_day, $cur_year)) ?>");
        month03 = r1.text(400, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 3, $cur_day, $cur_year)) ?>");
        month02 = r1.text(510, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 2, $cur_day, $cur_year)) ?>");
        month01 = r1.text(620, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 1, $cur_day, $cur_year)) ?>");
        month00 = r1.text(730, 315, "<?php echo date('M d Y') ?>");

        // X and Y axis labels
        r1.g.text(380, 340, "<?php echo __('Month') ?>").attr({"font-weight": "bold", "font-size": "12px"});
        r1.g.text(20, 165, "<?php echo __('Total Income') . ' (' . sfConfig::get('app_rt_currency', 'AUD') . ')' ?>").attr({"font-weight": "bold", "font-size": "12px", rotation: 270});

        // Graph 2 ---------------------------------------------------------- //
        var r2 = Raphael("graph-order-count");
        r2.g.txtattr.font = "10px 'Fontin Sans', Fontin-Sans, sans-serif";

        values_x = [<?php echo $days_in_months ?>];
        values_y = [<?php echo $orders_per_day_in_months ?>];
        chart    = r2.g.linechart(60, 5, 680, 300, values_x, values_y, {shade: true, "colors":["#8aa717"], nostroke: false, axis: "0 0 0 1", smooth: false, symbol: "o"}); // symbol: "o"
        chart.symbols.attr({stroke: "#8aa717", r: 2});

        // X and Y axis
        var l1 = r2.path("M70 305 L70 15").attr({"stroke-width": 1, stroke: "#000"});    // Y-axis left
        var l1 = r2.path("M730 305 L730 15").attr({"stroke-width": 1, stroke: "#000"});  // Y-axis right
        var l1 = r2.path("M70 295 L730 295").attr({"stroke-width": 1, stroke: "#000"});  // X-axis bottom
        var l1 = r2.path("M70 15 L730 15").attr({"stroke-width": 1, stroke: "#000"});    // X-axis top

        // Months indicators
        var l1 = r2.path("M180 305 L180 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 5 month
        var l1 = r2.path("M290 305 L290 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 4 month
        var l1 = r2.path("M400 305 L400 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 3 month
        var l1 = r2.path("M510 305 L510 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 2 month
        var l1 = r2.path("M620 305 L620 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 1 month

        // X-axis separators
        var l1 = r2.path("M70 202 L730 202").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // 1/3 of available height
        var l1 = r2.path("M70 109 L730 109").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // 1/3 of available height

        // X-axis labels
        <?php $cur_day = date('d'); ?>
        <?php $cur_month = date('m'); ?>
        <?php $cur_year = date('Y'); ?>
        month06 = r2.text(70, 315, "<?php echo date('M d Y', mktime(0, 0, 0, $cur_month - 6, $cur_day, $cur_year)) ?>");
        month05 = r2.text(180, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 5, $cur_day, $cur_year)) ?>");
        month04 = r2.text(290, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 4, $cur_day, $cur_year)) ?>");
        month03 = r2.text(400, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 3, $cur_day, $cur_year)) ?>");
        month02 = r2.text(510, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 2, $cur_day, $cur_year)) ?>");
        month01 = r2.text(620, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 1, $cur_day, $cur_year)) ?>");
        month00 = r2.text(730, 315, "<?php echo date('M d Y') ?>");

        // X and Y axis labels
        r2.g.text(380, 340, "<?php echo __('Month') ?>").attr({"font-weight": "bold", "font-size": "12px"});
        r2.g.text(20, 165, "<?php echo __('Total Orders') ?>").attr({"font-weight": "bold", "font-size": "12px", rotation: 270});
    };
</script>

<h2><?php echo __('Total Actual Income Received Per Day In The Last Six Month') ?></h2>
<div id="graph-income-total" class="rt-graph-holder" style="height:350px; width: 780px"></div>

<h2><?php echo __('Total Orders Recieved Per Day In The Last Six Month') ?></h2>
<div id="graph-order-count" class="rt-graph-holder" style="height:350px; width: 780px"></div>