const stb = stb ?? {};

(function ($) {
  let media;
  const mediaTexts = window.stbUserOptions.media;

  // WP Media
  stb.media = media = {
    buttonId: '#js_imgMinus-select, #js_imgPlus-select',

    init: function () {
      $(this.buttonId).on('click', this.openMediaDialog);
    },

    openMediaDialog: function (e) {
      e.preventDefault();

      const source = '#' + e.target.id.replace('-select', '');

      if (this._frame) {
        this._frame.open();
        return;
      }

      this._frame = media.frame = wp.media({
        title: mediaTexts.title,
        button: {
          text: mediaTexts.button
        },
        multiple: false,
        library: {
          type: 'image'
        }
      });

      this._frame.on('ready', function () {
        //
      });

      this._frame.state('library').on('select', function () {
        const attachment = this.get('selection').single();
        media.handleMediaAttachment(attachment, source);
      });

      this._frame.open();
    },

    handleMediaAttachment: function (a, s) {
      const attachment = a.toJSON();
      $(s).val(attachment.url);
    }
  };

  $(document).ready(function () {
    const opts = window.stbUserOptions;

    media.init();

    $("#tabs").tabs();

    try {
      $('.color-btn').smallColorPicker({
        placement: {popup: true},
        texts: opts.texts
      }).on({
        scp_ok: function (p, c) {
          $('#' + p.target.id.replace("-button", '')).val(c.replace('#', ''));
          $('#' + p.target.id).html('<b style="background-color: ' + c + ';"></b>' + c.replace('#', '').toUpperCase());
        }
      });
    } catch (err) {
      // the browser is not supported
    }

    return false;
  });
})(jQuery);