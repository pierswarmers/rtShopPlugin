<?php use_javascript('/rtCorePlugin/vendor/raphael/raphael.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/raphael/g.raphael.min.js') ?>
<?php use_javascript('/rtCorePlugin/vendor/raphael/g.line.min.js') ?>
<?php use_helper('I18N', 'rtAdmin') ?>

<h1><?php echo __('Order Analysis') ?></h1>

<script type="text/javascript" charset="utf-8">
    window.onload = function () {
        var r = Raphael("graph-holder");
        r.g.txtattr.font = "10px 'Fontin Sans', Fontin-Sans, sans-serif";
        //r.g.text(160, 20, "Total Payments Recieved Per Calendar Day");
        var lines = r.g.linechart(40, 40, 600, 220,
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
        lines.symbols.attr({r: 3});
        lines.symbols.attr({stroke: "#FFF"});
        lines[0][0].attr({stroke: "#CCC"});

    };
</script>

<div id="graph-holder" style="background:#FFF; height:300px;"></div>

