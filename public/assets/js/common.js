jQuery(document).ready(function ($) {

    $("body").on("click",function(ev){
        if($(ev.target).closest('.select2-container--open').length > 0){
            $('.detail-field').not($(ev.target).closest('.detail-field')).removeClass('focused');
            $(ev.target).closest('.detail-field').addClass('focused');
        }else{
            $('.detail-field').removeClass('focused');
        }
    });

     
    window.addEventListener('orientationchange', doOnOrientationChange);
    
    ffooter();

});

jQuery(window).resize(function(){
    ffooter();
});



 function doOnOrientationChange()
  {
    switch(window.orientation) 
    {  
      case -90:
      case 90:
        //alert('landscape');
        if($('body').hasClass('modal-open')){ 
            $('body').removeClass('modal-open');
            setTimeout(function(){
                $('body').addClass('modal-open');
            },500);
        }

        break; 
      default:
        //alert('portrait');
        break; 
    }
  }


$.fn.serializeObject = function(){
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function pad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function ffooter() {
    var wHeight = $( window ).height();
    var wWidth = $( window ).width();
    var dHeight = $( document ).height();
    var hHeight = $('.main-header').outerHeight();
    var cHeight = $('.main-content').outerHeight();
    var fHeight = $('.main-footer').outerHeight();

    var containerHeight = wHeight - (hHeight + fHeight);
    var scrollBoxHeight = containerHeight - 116;

    if(wHeight > 753 && wWidth > 767) {
        jQuery('body').addClass('fixed-footer');
        jQuery('body').css('margin-bottom', fHeight);
        jQuery('.main-footer').css('visibility', 'visible');
        
        if (cHeight < scrollBoxHeight) {
            if($('.main-content.add_home').length || $('.main-content.admin-home-page').length){
                $('.scrollbox').height(scrollBoxHeight);
            }
        }
    }
    else {
        jQuery('body').removeClass('fixed-footer');
        jQuery('body').css('margin-bottom', '0');
        jQuery('.main-footer').css('visibility', 'visible');
    }
}
