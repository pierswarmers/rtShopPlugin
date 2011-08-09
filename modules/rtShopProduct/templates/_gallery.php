<?php

$config = sfConfig::get('app_rt_gallery');

$img_s_width  = isset($config['product_preview']['max_width'])  ? $config['product_preview']['max_width'] : 49;
$img_s_height = isset($config['product_preview']['max_height']) ? $config['product_preview']['max_height'] : 69;

$img_m_width  = isset($config['product_medium']['max_width'])  ? $config['product_medium']['max_width'] : 650;
$img_m_height = isset($config['product_medium']['max_height']) ? $config['product_medium']['max_height'] : 1000;

$img_l_width  = isset($config['product_full']['max_width'])  ? $config['product_full']['max_width'] : 1000;
$img_l_height = isset($config['product_full']['max_height']) ? $config['product_full']['max_height'] : 800;

if(!isset($config['javascripts']))
{
  $config['javascripts'] = array('/rtCorePlugin/vendor/jquery/js/jquery.min.js',
                                 '/rtCorePlugin/js/frontend-gallery.js');
}

foreach ($config['javascripts'] as $file)
{
  use_javascript($file);
}

if(!isset($config['stylesheets']))
{
  $config['stylesheets'] = array(
      '/rtCorePlugin/css/frontend-gallery.css'
  );
}

foreach ($config['stylesheets'] as $file)
{
  use_stylesheet($file);
}

?>

<div class="rt-shop-product-primary-image">
  <?php $promo_span = $rt_shop_product->isOnPromotion() ? '<span class="rt-shop-product-promotion">'.__('On Sale Now').'</span>' : ''; ?>
  <?php echo $promo_span ?>
  <?php $style = ''; foreach($rt_shop_product->getImages() as $image): ?>
    <?php
      $image_large   = rtAssetToolkit::getThumbnailPath($image->getSystemPath(), array('maxHeight' => $img_l_height, 'maxWidth' => $img_l_width));
      $image_medium  = rtAssetToolkit::getThumbnailPath($image->getSystemPath(), array('maxHeight' => $img_m_height, 'maxWidth' => $img_m_width));

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
    <a style="<?php echo $style ?>" href="<?php echo $image_large ?>" id="primary-image-holder-<?php echo $image->getId() ?>" class="rt-image-ref-<?php echo $image_variation_key; ?>" title="<?php echo $image->getTitle() ?>" rel="gallery-images">
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
