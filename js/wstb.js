(function($){
  $(document).ready(function() {
    var options = {direction: 'vertical'};
  
    if(stbUserOptions.mode != 'js') {
      var cssOpts = stbUserOptions.cssOptions;
      function callback(cnt) {
        cnt.toggleClass('stb-hidden').toggleClass('stb-visible');
        return false;
      }

      $('.stb-tool').bind('click', function() {
        var
          id = $(this).attr('id').split('-'),
          idn = id[2],
          cnt = $('#stb-container-' + idn),
          sb = $('#stb-body-box-'+idn),
          si = $('#stb-toolimg-'+idn);
        if(cnt.hasClass('stb-visible')) {
          sb.hide('blind', options, 500, function() {callback(cnt)});
          si.attr({'src': cssOpts.imgShow, 'title': cssOpts.strShow});
        }
        else {
          sb.show('blind', options, 500, function() {callback(cnt)});
          si.attr({'src': cssOpts.imgHide, 'title': cssOpts.strHide});
        }
        return false;
      });
    }
    
    if(stbUserOptions.mode != 'css') {
      var
        jsOpts = stbUserOptions.jsOptions,
        jsStyles = stbUserOptions.styles;
      
      $.each(jsStyles, function(i, el) {
        if(el.stype != 'system') stbThemes.register(el.slug, el.jsStyle);
        else stbThemes.update(el.slug, el.jsStyle);        
      });
      $.each(jsStyles, function(i, el) {
        $('.stb-level-0.stb-'+el.slug+'-box ').stb(el.slug, jsOpts);
      });
      $.each(jsStyles, function(i, el) {
        $('.stb-level-0 .stb-level-1.stb-'+el.slug+'-box ').stb(el.slug, jsOpts);
      });
      
      //Codes for supporting "Wordpress Post Tabs" plugin
      $("li a[href^=#tab]").click(function() {
        $($(this).attr('href') + ' .stb-body').stbRedraw();
      });
    }
  });
})(jQuery);