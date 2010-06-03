<?php use_helper('I18N', 'Date', 'rtText', 'rtForm', 'rtDate', 'rtSite') ?>
<div class="rt-site-page-show">
  <?php include_partial('shop_category', array('rt_shop_category' => $rt_shop_category, 'sf_cache_key' => $rt_shop_category->getId())) ?>
  <dl class="rt-meta-data">
    <dt><?php echo __('Created') ?>:</dt>
    <dd><?php echo time_ago_in_words_abbr($rt_shop_category->getCreatedAt(), $sf_user->getCulture()) ?></dd>
    <dt><?php echo __('Updated') ?>:</dt>
    <dd><?php echo time_ago_in_words_abbr($rt_shop_category->getUpdatedAt(), $sf_user->getCulture()) ?></dd>
    <dt><?php echo __('Version') ?>:</dt>
    <dd><?php echo $rt_shop_category->version ?></dd>
  </dl>
</div>