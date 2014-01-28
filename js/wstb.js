(function($){
  $(document).ready(function() {
    var options = {direction: 'vertical'};
  
    if(stbUserOptions.mode != 'js') {
      var cssOpts = stbUserOptions.cssOptions;
      
      function callback(sb,sc,si) {
        if (sb.css('display') == 'none') {
          sc.css({'margin-bottom': cssOpts.mbottom + 'px'});
          si.attr({'src': cssOpts.imgShow, 'title': cssOpts.strShow});
          if (cssOpts.roundedCorners) {
            sc.css({'-webkit-border-bottom-left-radius' : '5px', 
              '-webkit-border-bottom-right-radius' : '5px', 
              '-moz-border-radius-bottomleft' : '5px', 
              '-moz-border-radius-bottomright' : '5px',
              'border-bottom-left-radius' : '5px', 
              'border-bottom-right-radius' : '5px'
            });
          }
        }
        else {
          si.attr({'src': cssOpts.imgHide, 'title': cssOpts.strHide});
        }
    
        $(this).parent().parent().children('#caption').css({'margin-bottom': cssOpts.mbottom + 'px'});
        return false;
      }
  
      $(".stb-tool").bind("click", function() {
        id = $(this).attr('id').split('-');
        idn = id[2];
        sb = $('#stb-body-box-'+idn);
        sc = $('#stb-caption-box-'+idn);
        si = $('#stb-toolimg-'+idn);
        if (sb.css('display') != 'none')  {        
          sb.hide('blind',options,500, function() {callback(sb,sc,si);});
        }
        else {
          sb.show('blind',options,500,function() {callback(sb,sc,si);});
          sc.css({'margin-bottom' : '0px'});      
          if (cssOpts.roundedCorners) { 
            sc.css({'-webkit-border-bottom-left-radius' : '0px', 
              '-webkit-border-bottom-right-radius' : '0px', 
              '-moz-border-radius-bottomleft' : '0px', 
              '-moz-border-radius-bottomright' : '0px',
              'border-bottom-left-radius' : '0px', 
              'border-bottom-right-radius' : '0px'
            });
          }
        }
        return false;
      });
    }
    
    if(stbUserOptions.mode != 'css') {
      var jsOpts = stbUserOptions.jsOptions;
      var jsStyles = stbUserOptions.styles;
      
      $.each(jsStyles, function(i, el) {
        if(el.stype != 'system') stbThemes.register(el.slug, el.jsStyle);
        else stbThemes.update(el.slug, el.jsStyle);        
      });
      $.each(jsStyles, function(i, el) {
        $('.stb-level-0.stb-'+el.slug+'-box ').stb(el.slug, jsOpts);
      });
      $.each(jsStyles, function(i, el) {
        $('.stb-level-1.stb-'+el.slug+'-box ').stb(el.slug, jsOpts);
      });
      
      //Codes for supporting "Wordpress Post Tabs" plugin
      $("li a[href^=#tab]").click(function() {
        $($(this).attr('href') + ' .stb-body').stbRedraw();
      });
    }
  });
})(jQuery);