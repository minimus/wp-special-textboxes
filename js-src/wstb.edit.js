const stb = stb || {};
const mediaStrings = stbUserOptions.strings;

(function($) {
  let media;

  stb.media = media = {
    jsId: '#selJsImg',
    cssId: '#selCssImg',
    jsImgUrl: '#js_image',
    cssImgUrl: '#css_big_image',

    init: function() {
      $(this.jsId).on( 'click', {mode: 'js'}, this.openMediaDialog );
      $(this.cssId).on( 'click', {mode: 'css'}, this.openMediaDialog );
    },

    openMediaDialog: function( e ) {
      e.preventDefault();

      const mode = e.data.mode;

      if ( this._frame ) {
        this._frame.open();
        return;
      }

      this._frame = media.frame = wp.media({
        title: mediaStrings.title,
        button: {
          text: mediaStrings.update
        },
        multiple: false,
        library: {
          type: 'image'
        }
      });

      this._frame.on('ready', function() {
        //
      });

      this._frame.state( 'library' ).on('select', function() {
        const attachment = this.get( 'selection' ).single();
        media.handleMediaAttachment( attachment, mode );
      });

      this._frame.open();
    },

    handleMediaAttachment: function( a, mode ) {
      const attachment = a.toJSON();
      if(mode === 'js') $(this.jsImgUrl).val(attachment.url);
      else $(this.cssImgUrl).val(attachment.url);
    }
  };

  function sanitizeOptions(opts) {
    if(typeof(opts.radius) === 'string') opts.radius = parseInt(opts.radius);
    if(typeof(opts.imgX) === 'string') opts.imgX = parseInt(opts.imgX);
    if(typeof(opts.imgY) === 'string') opts.imgY = parseInt(opts.imgY);
    
    return opts;
  }
  
  $(document).ready(function() {
    const theme = $('#style_slug').val();
    const thm = {
      image: $('#js_image').val(),
      color: '#' + $('#js_color').val(),
      colorTo: '#' + $('#js_color_to').val(),
      fontColor: '#' + $('#js_font_color').val(),
      border: {
        width: $('#js_border_width').val(),
        color: '#' + $('#js_border_color').val()
      },
      caption: {
        fontColor: '#' + $('#js_caption_font_color').val(),
        color: '#' + $('#js_caption_color').val(),
        colorTo: '#' + $('#js_caption_color_to').val()
      }
    };
    const opts = sanitizeOptions(stbUserOptions.jsOptions);
    const cpOpts = opts.pickerOptions;
    const options = {direction: 'vertical'};
    if('Undefined' !== theme) $('#js_test, #js_test_cap').stb(thm, opts);

    media.init();

    const cssOpts = stbUserOptions.cssOptions;
    function callback(cnt) {
      cnt.toggleClass('stb-hidden').toggleClass('stb-visible');
      return false;
    }

    $('.stb-tool').on('click', function() {
      const id = $(this).attr('id').split('-');
      const idn = id[2];
      const cnt = $('#stb-container-' + idn);
      const sb = $('#stb-body-box-' + idn);
      const si = $('#stb-toolimg-' + idn);
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