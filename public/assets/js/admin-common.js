jQuery(document).ready(function ($) {
    ffooter();
     window.addEventListener('orientationchange', doOnOrientationChange);
     $(".login-container").hover(function(){ $(this).addClass('open'); });
     $(".login-container").mouseleave(function(){ $(this).removeClass('open'); });
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

jQuery(window).resize(function(){
    ffooter();
});

$("#searchString").on('keypress keyup',function(){
    $(this).val().trim();
    if($(this).val().length >=2 ){
        $(this).autocomplete({ source:site_url+"/search",minLength:3}).data("uiAutocomplete")._renderItem = function (ul, item) {
            ul.addClass('search_result')
            var reg = new RegExp(this.term, "i") ;
            if(item.parent_user_id != 0) {
                var id = item.parent_user_id;
            } else {
                var id = item.id;
            }
            var name = item.name.replace(reg,"<span style='background-color:#eac5c8;'>" + this.term + "</span>");
            name = name.toLowerCase();
            var email = item.email;
            if(item.search_type == 'user'){
                var html =  $("<li class='search-result'></li>").data("item.autocomplete", item).append('<div class="media"><a class="media-left media-middle search-bar" href="'+site_url+'/user/'+id+ '"><img class="media-object" src="'+base_url+'/assets/images/common/user_male.png" ><div class="media-body media-middle"><h4 class="media-heading">'+name+'</h4></a></div></div>').appendTo(ul);
            }
            if(item.search_type == 'company'){
                if(item.com_name != '' && item.com_name != null && item.com_name != 'undefined') {
                    var com_name = item.com_name.replace(reg,"<span style='background-color:#eac5c8;'>" + this.term + "</span>");
                    com_name = com_name.toLowerCase();
                    var  html = $("<li class='search-result'></li>").data("autocomplete", item).append('<div class="media"><a class="media-left media-middle search-bar" href="'+site_url+'/user/'+id+'"><img class="media-object" src="'+base_url+'/assets/images/common/buildings.png" ><div class="media-body media-middle"><h4 class="media-heading">'+com_name+'</h4></a></div></div>').appendTo(ul);
                }
            }
            if(item.search_type == 'email' && item.email != null && item.email != undefined) {
                var email = item.email.replace(reg,"<span style='background-color:#eac5c8;'>" + this.term + "</span>");
                email = email.toLowerCase();
                var  html = $("<li class='search-result'></li>").data("autocomplete", item).append('<div class="media"><a class="media-left media-middle search-bar" href="'+site_url+'/user/'+id+'"><img class="media-object img-email-env" src="'+base_url+'/assets/images/common/envalop.png" ><div class="media-body media-middle"><h4 class="media-heading">'+email+'</h4></a></div></div>').appendTo(ul);
            }
            return html;
        };
    }
});

 
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
