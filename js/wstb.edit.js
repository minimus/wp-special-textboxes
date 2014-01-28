(function($) {
  function sanitizeOptions(opts) {
    if(typeof(opts.radius) == 'string') opts.radius = parseInt(opts.radius);
    if(typeof(opts.imgX) == 'string') opts.imgX = parseInt(opts.imgX);
    if(typeof(opts.imgY) == 'string') opts.imgY = parseInt(opts.imgY);
    
    return opts;
  }
  
  $(document).ready(function() {
    var 
      theme = $('#style_slug').val(),
      thm = {
        image: $('#js_image').val(),      
        color: '#'+$('#js_color').val(),
        colorTo: '#'+$('#js_color_to').val(),
        fontColor: '#'+$('#js_font_color').val(),
        border: {
          width: $('#js_border_width').val(),
          color: '#'+$('#js_border_color').val()
        },
        caption: {
          fontColor: '#'+$('#js_caption_font_color').val(),
          color: '#'+$('#js_caption_color').val(),
          colorTo: '#'+$('#js_caption_color_to').val()
        }
      },
      opts = sanitizeOptions(stbUserOptions.jsOptions),
      cpOpts = opts.pickerOptions,
      options = {direction: 'vertical'};
    if('Undefined' != theme) $('#js_test, #js_test_cap').stb(thm, opts);
    
    /*var cp = '#css_color, #css_caption_color, #css_bg_color, #css_caption_bg_color, #css_border_color,';
    cp += '#js_color, #js_color_to, #js_caption_font_color, #js_font_color, #js_caption_color, #js_caption_color_to, #js_border_color';
    $(cp).ColorPicker({
      onSubmit: function(hsb, hex, rgb, el){
        $(el).val(hex);
        $(el).ColorPickerHide();
      },
      onBeforeShow: function(){
        $(this).ColorPickerSetColor(this.value);
      }
    }).bind('keyup', function(){
      $(this).ColorPickerSetColor(this.value);
    });*/
      
    function callback(sb,sc,si) {
      if (sb.css('display') == 'none') {
        sc.css({'margin-bottom' : stbUserOptions.cssOptions.mbottom + 'px'});
        si.attr({'src' : stbUserOptions.cssOptions.imgShow, 'title' : stbUserOptions.cssOptions.strShow});
        if (stbUserOptions.cssOptions.roundedCorners) {
          sc.css({
            '-webkit-border-bottom-left-radius' : '5px',
            '-webkit-border-bottom-right-radius' : '5px',
            '-moz-border-radius-bottomleft' : '5px',
            '-moz-border-radius-bottomright' : '5px',
            'border-bottom-left-radius' : '5px',
            'border-bottom-right-radius' : '5px'
          });
        }
      }
      else {
        si.attr({'src' : stbUserOptions.cssOptions.imgHide, 'title' : stbUserOptions.cssOptions.strHide});
      }
    
      $(this).parent().parent().children('#caption').css({'margin-bottom' : stbUserOptions.cssOptions.mbottom + 'px'});
      return false;
    }
      
    $(".stb-tool").bind("click", function() {
      var
        eid = $(this).attr('id').split('-'),
        num = eid[2],
        sb = $(this).parent().parent().children('#stb-body-box-'+num),
        sc = $(this).parent().parent().children('#stb-caption-box-'+num),
        si = $(this).children('#stb-toolimg-'+num);
      if (sb.css('display') != 'none')  {
        sb.hide('blind',options,500, function() {callback(sb,sc,si);});

      }
      else {
        sb.show('blind',options,500,function() {callback(sb,sc,si);});
        sc.css({'margin-bottom' : '0px'});
        if (stbUserOptions.cssOptions.roundedCorners) {
          sc.css({
            '-webkit-border-bottom-left-radius' : '0px',
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
  
    /*$('#cb_color, #cb_caption_color, #cb_background, #cb_caption_background, #cb_border_color').ColorPicker({
      onSubmit: function(hsb, hex, rgb, el){
        $(el).val(hex);
        $(el).ColorPickerHide();
      },
      onBeforeShow: function(){
        $(this).ColorPickerSetColor(this.value);
      }
    }).bind('keyup', function(){
      $(this).ColorPickerSetColor(this.value);
    });*/

    try {
      $('.color-btn').smallColorPicker({
        placement: { popup: true },
        texts: cpOpts.texts
      }).on({
        scp_ok: function(p,c) {
          $('#' + p.target.id.replace("-button", '')).val(c.replace('#', ''));
          $('#' + p.target.id).html('<b style="background-color: ' + c + ';"></b>' + c.replace('#', '').toUpperCase());
        }
      });
    } catch (err) {
      // the browser is not supported
    }
  });
})(jQuery);