(function($){
  $(document).ready(() => {
    const options = {direction: 'vertical'};
  
    if(stbUserOptions.mode !== 'js') {
      const cssOpts = stbUserOptions.cssOptions;
      function callback(cnt) {
        cnt.toggleClass('stb-hidden').toggleClass('stb-visible');
        return false;
      }

      $('.stb-tool').on('click', function() {
        const id = $(this).attr('id').split('-');
        const idn = id[2];
        const cnt = $(`#stb-container-${idn}`);
        const sb = $(`#stb-body-box-${idn}`);
        const si = $(`#stb-toolimg-${idn}`);
        if(cnt.hasClass('stb-visible')) {
          sb.hide('blind', options, 500, () => (callback(cnt)));
          si.attr({'src': cssOpts.imgShow, 'title': cssOpts.strShow});
        }
        else {
          sb.show('blind', options, 500, () => (callback(cnt)));
          si.attr({'src': cssOpts.imgHide, 'title': cssOpts.strHide});
        }
        return false;
      });
    }
    
    if(stbUserOptions.mode !== 'css') {
      const jsOpts = stbUserOptions.jsOptions;
      const jsStyles = stbUserOptions.styles;
      
      $.each(jsStyles, (i, el) => {
        if(el.stype !== 'system') window.stbThemes.register(el.slug, el.jsStyle);
        else window.stbThemes.update(el.slug, el.jsStyle);
      });
      $.each(jsStyles, (i, el) => {
        $(`.stb-level-0.stb-${el.slug}-box`).stb(el.slug, jsOpts);
      });
      $.each(jsStyles, (i, el) => {
        $(`.stb-level-0 .stb-level-1.stb-${el.slug}-box `).stb(el.slug, jsOpts);
      });
      
      //Codes for supporting "Wordpress Post Tabs" plugin
      $("li a[href^=#tab]").click(() => {
        $($(this).attr('href') + ' .stb-body').stbRedraw();
      });
    }
  });
})(jQuery);