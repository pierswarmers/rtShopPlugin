<?php

use_helper('I18N');

use_javascript('/rtCorePlugin/vendor/jquery/js/jquery.min.js');
use_javascript('/rtCorePlugin/vendor/colorbox/js/jquery.colorbox-min.js');
use_stylesheet('/rtCorePlugin/vendor/colorbox/css/colorbox.css');
use_stylesheet('/rtCorePlugin/vendor/jquery/css/ui/jquery.ui.css');

?>

<div class="rt-shop-product-primary-image">
  <?php $promo_span = $rt_shop_product->isOnPromotion() ? '<span class="rt-shop-product-promotion">'.__('On Sale Now').'</span>' : ''; ?>
  <?php echo $promo_span ?>
  <?php $style = ''; foreach($rt_shop_product->getImages() as $image): ?>
    <?php
      $image_large   = rtAssetToolkit::getThumbnailPath($image->getSystemPath(), array('maxHeight' => 500, 'maxWidth' => 800));
      $image_medium  = rtAssetToolkit::getThumbnailPath($image->getSystemPath(), array('maxHeight' => 230, 'maxWidth' => 180));

      $image_variation_key  = $image->getOriginalFilename();
      $needles              = array("_","-");
      
      foreach($needles as $needle) {
        $needle_pos = strrpos($image_variation_key,$needle);
        if($needle_pos == true)
        {
          $image_variation_key = substr($image_variation_key,$needle_pos);
          $image_variation_key = strtolower(str_replace(array('_','-','.jpg','.jpeg','.gif','.png'),'',$image_variation_key));
        }
      }
    ?>
    <a style="<?php echo $style ?>" href="<?php echo $image_large ?>" id="primary-image-holder-<?php echo $image->getId() ?>" class="rt-image-ref-<?php echo $image_variation_key; ?>" title="<?php echo $image->getOriginalFilename() ?>" rel="gallery-images">
      <?php echo image_tag($image_medium) ?>
    </a>
  <?php $style = 'display:none'; endforeach; ?>
</div>
<div class="rt-shop-product-image-thumbs">
  <?php $i = 1; foreach($rt_shop_product->getImages() as $image): ?>
    <div class="rt-list-item-<?php echo $i ?>">
      <span>
        <?php echo image_tag(rtAssetToolkit::getThumbnailPath($image->getSystemPath(), array('maxHeight' => 69, 'maxWidth' => 49)), array('class' => 'primary-image-holder-'.$image->getId())) ?>
      </span>
    </div>
  <?php $i++; endforeach; ?>
</div>
<script type="text/javascript">
  $(function()
  {
    $(".rt-shop-product-primary-image a").colorbox({preloading:false});
    $(".rt-shop-product-image-thumbs div").click(function()
    {
      $(".rt-shop-product-primary-image a#" + $(this).children('img').attr('class')).css("display","inline").siblings('a').css("display","none");
    });
  });
</script>
