<?php use_helper('I18N') ?>
<div class="rt-shop-send-to-friend rt-show rt-primary-container">

  <?php include_partial('rtAdmin/flashes_public'); ?>

  <h1><?php echo __('Send To A Friend') ?></h1>
  
  <form action="<?php echo url_for('rt_shop_send_to_friend') ?>" method="post">

    <div class="rt-container">
      <table>
        <tbody>
          <?php echo $form ?>
        </tbody>
      </table>
    </div>

    <div class="rt-container rt-tools rt-shop-send-to-friend-tools">
      <button type="submit" class="button rt-shop-send-to-friend-send"><?php echo __('Send email') ?></button>
    </div>

  </form>

</div>