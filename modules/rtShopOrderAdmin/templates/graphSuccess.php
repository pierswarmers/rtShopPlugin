<?php use_javascript('/rtCorePlugin/vendor/raphael/raphael.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/raphael/g.raphael.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/raphael/g.line.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/raphael/g.bar.min.js') ?>
<?php use_helper('I18N', 'rtAdmin') ?>

<?php $cur_day   = date('d'); ?>
<?php $cur_month = date('m'); ?>
<?php $cur_year  = date('Y'); ?>

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

<h1><?php echo __('Sales Charts - Sales Order Analysis') ?></h1>

<script type="text/javascript" charset="utf-8">
    window.onload = function () {
        // Graph Total Actual Income ---------------------------------------------------------- //
        var r1 = Raphael("graph-income-total-day");
        r1.g.txtattr.font = "10px 'Fontin Sans', Fontin-Sans, sans-serif";

        values_x = [<?php echo $days_in_months ?>];
        values_y = [<?php echo $revenue_per_day_in_months ?>];
        chart    = r1.g.linechart(60, 5, 680, 300, values_x, values_y, {shade: true, "colors":["#1751a7"], nostroke: false, axis: "0 0 0 1", smooth: false, symbol: "o"}); // symbol: "o"
        chart.symbols.attr({stroke: "#1751a7", r: 2});

        // X and Y axis
        r1.path("M70 305 L70 15").attr({"stroke-width": 1, stroke: "#000"});    // Y-axis left
        r1.path("M730 305 L730 15").attr({"stroke-width": 1, stroke: "#000"});  // Y-axis right
        r1.path("M70 295 L730 295").attr({"stroke-width": 1, stroke: "#000"});  // X-axis bottom
        r1.path("M70 15 L730 15").attr({"stroke-width": 1, stroke: "#000"});    // X-axis top

        // Months indicators
        r1.path("M180 305 L180 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 5 month
        r1.path("M290 305 L290 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 4 month
        r1.path("M400 305 L400 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 3 month
        r1.path("M510 305 L510 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 2 month
        r1.path("M620 305 L620 15").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // Current month - 1 month

        // X-axis separators
        r1.path("M70 202 L730 202").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // 1/3 of available height
        r1.path("M70 109 L730 109").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // 1/3 of available height

        // X-axis labels
        r1.text(70, 315, "<?php echo date('M d Y', mktime(0, 0, 0, $cur_month - 6, $cur_day, $cur_year)) ?>");
        r1.text(180, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 5, $cur_day, $cur_year)) ?>");
        r1.text(290, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 4, $cur_day, $cur_year)) ?>");
        r1.text(400, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 3, $cur_day, $cur_year)) ?>");
        r1.text(510, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 2, $cur_day, $cur_year)) ?>");
        r1.text(620, 315, "<?php echo date('M d Y',mktime(0, 0, 0, $cur_month - 1, $cur_day, $cur_year)) ?>");
        r1.text(730, 315, "<?php echo date('M d Y') ?>");

        // X and Y axis labels
        r1.g.text(380, 340, "<?php echo __('Month') ?>").attr({"font-weight": "bold", "font-size": "12px"});
        r1.g.text(20, 165, "<?php echo __('Total Income') . ' (' . sfConfig::get('app_rt_currency', 'AUD') . ')' ?>").attr({"font-weight": "bold", "font-size": "12px", rotation: 270});

        // Bar Chart: Graph Total Income Per Month ---------------------------------------------------------- //
        var r2 = Raphael("graph-income-total-month");
        r2.g.txtattr.font = "10px 'Fontin Sans', Fontin-Sans, sans-serif";

        fin = function () {
          this.flag = r2.g.popup(this.bar.x, this.bar.y, '<?php echo sfConfig::get('app_rt_currency', 'AUD') ?>' + this.bar.value || "0").insertBefore(this);
        },
        fout = function () {
          this.flag.animate({opacity: 0}, 300, function () {this.remove();});
        },

        // X-Axis / Y-Axis
        r2.path("M24 5 L369 5");     // X-Axis - Top
        r2.path("M24 240 L369 240"); // X-Axis - Bottom
        r2.path("M24 240 L24 5");    // Y-Axis - Left
        r2.path("M369 240 L369 5");  // Y-Axis - Right

        // Separators
        r2.path("M24 51 L369 51").attr("stroke", "#ccc").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"});   // X-Axis
        r2.path("M24 98 L369 98").attr("stroke", "#ccc").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"});   // X-Axis
        r2.path("M24 145 L369 145").attr("stroke", "#ccc").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // X-Axis
        r2.path("M24 192 L369 192").attr("stroke", "#ccc").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // X-Axis

        // Labels
        r2.text( 37, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 12, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text( 67, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 11, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text( 97, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 10, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text(127, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 9, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text(157, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 8, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text(185, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 7, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text(217, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 6, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text(247, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 5, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text(277, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 4, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text(307, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 3, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text(337, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 2, $cur_day, $cur_year)) ?>").rotate(45);
        r2.text(367, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 1, $cur_day, $cur_year)) ?>").rotate(45);
        
        // Draw bar chart graph
        values = [[<?php echo $total_income_in_month ?>]];
        chart = r2.g.barchart(20, 10, 355, 249, values, {stacked: false, type: "soft"});
        chart.attr("fill", "#b9cbe5");
        chart.hover(fin, fout);

        // Bar Chart: Average Order Value Per Month ---------------------------------------------------------- //
        var r3 = Raphael("graph-average-order-month");
        r3.g.txtattr.font = "10px 'Fontin Sans', Fontin-Sans, sans-serif";

        fin = function () {
          this.flag = r3.g.popup(this.bar.x, this.bar.y, '<?php echo sfConfig::get('app_rt_currency', 'AUD') ?>' + this.bar.value || "0").insertBefore(this);
        },
        fout = function () {
          this.flag.animate({opacity: 0}, 300, function () {this.remove();});
        },

        // X-Axis / Y-Axis
        r3.path("M24 5 L369 5");     // X-Axis - Top
        r3.path("M24 240 L369 240"); // X-Axis - Bottom
        r3.path("M24 240 L24 5");    // Y-Axis - Left
        r3.path("M369 240 L369 5");  // Y-Axis - Right

        // Separators
        r3.path("M24 51 L369 51").attr("stroke", "#ccc").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"});   // X-Axis
        r3.path("M24 98 L369 98").attr("stroke", "#ccc").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"});   // X-Axis
        r3.path("M24 145 L369 145").attr("stroke", "#ccc").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // X-Axis
        r3.path("M24 192 L369 192").attr("stroke", "#ccc").attr({"stroke-width": 0.4, stroke: "#000", "stroke-dasharray": "--"}); // X-Axis

        // Labels
        r3.text( 37, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 12, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text( 67, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 11, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text( 97, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 10, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text(127, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 9, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text(157, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 8, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text(185, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 7, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text(217, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 6, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text(247, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 5, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text(277, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 4, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text(307, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 3, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text(337, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 2, $cur_day, $cur_year)) ?>").rotate(45);
        r3.text(367, 270, "<?php echo date('M Y',mktime(0, 0, 0, $cur_month - 1, $cur_day, $cur_year)) ?>").rotate(45);

        // Draw bar chart graph
        values = [[<?php echo $average_order_in_month ?>]];
        chart = r3.g.barchart(20, 10, 355, 249, values, {stacked: false, type: "soft"});
        chart.attr("fill", "#b9cbe5");
        chart.hover(fin, fout);
    };
</script>

<!-- Total Income Per Day Start -->
<h2><?php echo __('Total Actual Income Received Per Day') ?></h2>
<div id="graph-income-total-day" class="rt-graph-holder" style="height:350px; width: 780px"></div>
<!-- Total Income Per Day End -->

<!-- Total Income Per Month Start -->
<div class="graph-totals">
  <h2><?php echo __('Total Income Per Month') ?></h2>
  <div id="graph-income-total-month" class="rt-graph-holder" style="height:300px;width:400px"></div>
</div>
<!-- Total Income Per Month End -->

<!-- Average Order Value Per Month Start -->
<div class="graph-averages">
  <h2><?php echo __('Average Order Value Per Month') ?></h2>
  <div id="graph-average-order-month" class="rt-graph-holder" style="height:300px;width:400px"></div>
</div>
<!-- Average Order Value Per Month End -->
