<h1><?php echo $rt_shop_category->getTitle() ?></h1>

<div class="rt-page-content clearfix">
<?php echo markdown_to_html($rt_shop_category->getContent(), $rt_shop_category); ?>
</div>