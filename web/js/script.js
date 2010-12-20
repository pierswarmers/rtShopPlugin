
// Hack to avoid unstyled order panel being shown.
$("form.rt-shop-product-order-panel").hide();

$(function() {
  /*
   * Handle the Colorbox (http://colorpowered.com/colorbox/) intereaction for
   * the image gallery on products.
   */
  $(".rt-shop-product-primary-image a").colorbox({ preloading:false });
  $(".rt-shop-product-image-thumbs div").click(function(){
    $(".rt-shop-product-primary-image a#" + $(this)
    .children()
    .children('img').attr('class')).css("display","inline")
    .siblings('a').css("display","none");
  });

  /*
   * Handle clicks on the wishlist and the Ajax call to save the item.
   */
  $(".rt-shop-add-to-wishlist a").click(function() {
    $('.rt-shop-add-to-wishlist').addClass('loading').html('Adding to wishlist...');
    $.ajax({ type: "POST", url: '/add-to-wishlist', data: ({
      id : $('#rt-shop-product-id').attr('value')
    }), dataType: "xhr",
    success: function(data) {
      $('.rt-shop-add-to-wishlist').removeClass('loading').addClass('success');
      $('.rt-shop-add-to-wishlist').html(data);
    }
    });
    return false;
  });

  /*
   * Handle the variation selections and how they interact with the gallery images.
   */
  $(".rt-shop-option-set").buttonset().find(':radio');
  $(".rt-shop-option-set").find(':radio').click(function() {
    var match = $(this).attr("title").toLowerCase().replace(/[^a-zA-Z0-9]/g, "");
    $(".rt-shop-product-primary-image a[class*=rt-image-ref-"+match+"]").css("display","inline").siblings('a').css("display","none");
    // de-focus all options
    $(".rt-shop-option-set input[type=radio]").each(function(){
      $(this).button( "widget" ).fadeTo(1, 0.3).removeClass('available');
    });
    // focus available options based on stock id matrix
    $($(this).next('.ref').html()).each(function(){
      $(this).button( "widget" ).fadeTo(1, 1).addClass('available')
    });
    checkUserSelection();
  }).each(function(){
    if($(this).button( "widget" ).hasClass('unavailable')) {
      $(this).button('disable', true);
    }
  });
  $("form.rt-shop-product-order-panel").show();
  //$("form.rt-shop-product-order-panel button").attr("disabled",true);
});

/*
 * Provide extended feedback on variation selections. These interactions are based
 * on attribute group / variation availabily.
 */
checkUserSelection = function() {
  var count_available_items   = 0;
  var count_selection_groups  = $("form.rt-shop-product-order-panel .rt-shop-option-set").size();
  var button                  = $('form.rt-shop-product-order-panel button');

  $(".rt-shop-option-set").each(function(){
    $(this).children('input:checked').each(function(){
      count_available_items++;
    });
  });
  if(count_available_items == count_selection_groups)
  {
    var availablity_check = true;
    $("form .rt-shop-option-set input:checked").each(function()
    {
      if(!$(this).button('widget').hasClass('available'))
      {
        availablity_check = false;
      }
    });
    if(availablity_check)
    {
      button.text("Add to Cart").attr("disabled",false);
      button.removeClass("disabled");
    }
    else
    {
      button.text("Selection not available").attr("disabled",true);
      button.addClass("disabled");
    }
  }
}

checkUserSelection();