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

<h1><?php echo __('Quarterly - Sales Analysis') ?></h1>

<script type="text/javascript" charset="utf-8">
    window.onload = function () {
        var r = Raphael("graph-income-total");
        r.g.txtattr.font = "10px 'Fontin Sans', Fontin-Sans, sans-serif";
        var lines = r.g.linechart(50, 0, 620, 250,
        [<?php $comma_0=''; foreach($orders_by_month as $month): ?><?php echo $comma_0 ?>[<?php $comma_1=''; foreach($month as $data): ?><?php echo $comma_1 ?><?php echo $data['o_day'] ?><?php $comma_1=', '; endforeach; ?>]<?php $comma_0=', '; endforeach; ?>],
        [<?php $comma_0=''; foreach($orders_by_month as $month): ?><?php echo $comma_0 ?>[<?php $comma_1=''; foreach($month as $data): ?><?php echo $comma_1 ?><?php echo $data['o_sum'] ?><?php $comma_1=', '; endforeach; ?>]<?php $comma_0=', '; endforeach; ?>],
        {nostroke: false, axis: "0 0 1 1", symbol: "x", smooth: false});
//        .hoverColumn(function () {
//            this.tags = r.set();
//            for (var i = 0, ii = this.y.length; i < ii; i++) {
//                this.tags.push(r.g.tag(this.x, this.y[i], this.values[i], 160, 10).insertBefore(this).attr([{fill: "#fff"}, {fill: this.symbols[i].attr("fill")}]));
//            }
//        }, function () {
//            this.tags && this.tags.remove();
//        });
        lines.symbols.attr({r: 3}).click(function(){alert(this)});
        lines.symbols.attr({stroke: "#FFF"});
//        lines[0][0].attr({stroke: "#CCC"});

        r.g.text(330, 270, "<?php echo __('Day') ?>").attr({"font-weight": "bold", "font-size": "12px"});
        r.g.text(20, 130, "<?php echo __('Total Income') ?>").attr({"font-weight": "bold", "font-size": "12px", rotation: 270});


        var l1 = r.path("M675 190L685 190").attr({"stroke-width": 3, stroke: "#1751A7"});
        r.g.text(690, 190, "<?php echo date('F') ?>").attr({"text-anchor": "start"});

        var l1 = r.path("M675 160L685 160").attr({"stroke-width": 3, stroke: "#8AA717"});
        r.g.text(690, 160, "<?php echo date('F', mktime(null, null, null, date('n') - 1)) ?>").attr({"text-anchor": "start"});

        var l1 = r.path("M675 130L685 130").attr({"stroke-width": 3, stroke: "#A74217"});
        r.g.text(690, 130, "<?php echo date('F', mktime(null, null, null, date('n') - 2)) ?>").attr({"text-anchor": "start"});

        var l1 = r.path("M675 100L685 100").attr({"stroke-width": 3, stroke: "#A78A17"});
        r.g.text(690, 100, "<?php echo date('F', mktime(null, null, null, date('n') - 3)) ?>").attr({"text-anchor": "start"});


        // Graph 2.

        var r2 = Raphael("graph-order-count");
        var lines2 = r2.g.linechart(50, 0, 620, 250,
        [<?php $comma_0=''; foreach($orders_by_month as $month): ?><?php echo $comma_0 ?>[<?php $comma_1=''; foreach($month as $data): ?><?php echo $comma_1 ?><?php echo $data['o_day'] ?><?php $comma_1=', '; endforeach; ?>]<?php $comma_0=', '; endforeach; ?>],
        [<?php $comma_0=''; foreach($orders_by_month as $month): ?><?php echo $comma_0 ?>[<?php $comma_1=''; foreach($month as $data): ?><?php echo $comma_1 ?><?php echo $data['o_count'] ?><?php $comma_1=', '; endforeach; ?>]<?php $comma_0=', '; endforeach; ?>],
        {nostroke: false, axis: "0 0 1 1", symbol: "x", smooth: false});
        lines2.symbols.attr({r: 3}).click(function(){alert(this)});
        lines2.symbols.attr({stroke: "#FFF"});

        r2.g.text(330, 270, "<?php echo __('Day') ?>").attr({"font-weight": "bold", "font-size": "12px"});
        r2.g.text(20, 130, "<?php echo __('Total Orders') ?>").attr({"font-weight": "bold", "font-size": "12px", rotation: 270});


        var l1 = r2.path("M675 190L685 190").attr({"stroke-width": 3, stroke: "#1751A7"});
        r2.g.text(690, 190, "<?php echo date('F') ?>").attr({"text-anchor": "start"});

        var l1 = r2.path("M675 160L685 160").attr({"stroke-width": 3, stroke: "#8AA717"});
        r2.g.text(690, 160, "<?php echo date('F', mktime(null, null, null, date('n') - 1)) ?>").attr({"text-anchor": "start"});

        var l1 = r2.path("M675 130L685 130").attr({"stroke-width": 3, stroke: "#A74217"});
        r2.g.text(690, 130, "<?php echo date('F', mktime(null, null, null, date('n') - 2)) ?>").attr({"text-anchor": "start"});

        var l1 = r2.path("M675 100L685 100").attr({"stroke-width": 3, stroke: "#A78A17"});
        r2.g.text(690, 100, "<?php echo date('F', mktime(null, null, null, date('n') - 3)) ?>").attr({"text-anchor": "start"});
    };
</script>

<h2><?php echo __('Total Actual Income Received - Quartery Summary') ?></h2>

<div id="graph-income-total" class="rt-graph-holder" style="height:300px; width: 780px"></div>

<h2><?php echo __('Total Orders - Quartery Summary') ?></h2>

<div id="graph-order-count" class="rt-graph-holder" style="height:300px; width: 780px"></div>