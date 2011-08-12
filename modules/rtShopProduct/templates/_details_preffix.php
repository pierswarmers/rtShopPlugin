<?php use_helper('I18N', 'Date', 'rtText') ?>

<h2>
  Price: <?php echo price_for($rt_shop_product) ?>
  <?php if($rt_shop_product->getSku()): ?>
  <span class="sku"><code>#<?php echo $rt_shop_product->getSku() ?></code></span>
  <?php endif; ?>
</h2>

<div class="rt-metas">
  <?php if($rt_shop_product->getNumberOfComments() > 0): ?>
  Average Rating: <?php if($rt_shop_product->getOverallRating() > 0) { include_partial('rtComment/rating', array('rating_value' => $rt_shop_product->getOverallRating(), 'show_items' => array('text','graph'))); } ?>
  <?php endif; ?>
  <span class="tabs"><a id="commentsTrigger"><?php echo __($rt_shop_product->getNumberOfComments() > 0 ? 'See reviews' : 'Be the first to review') ?></a></span>
</div>

<div class="description"><?php echo markdown_to_html($rt_shop_product->getContent(), $rt_shop_product); ?></div>

<script type="text/javascript">
  $(function() {
    $('#commentsTrigger').click(function(){ $('a[href="#comments"]').trigger('click'); });
  });
</script>