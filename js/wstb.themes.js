var
  stbStrings = stbOptions.text,
  stbUploader = stbOptions.uploader,
  stb = stb || {};


(function($) {
  $(document).ready(function() {
    var
      dlg = $('#save-dialog'),
      btn = $('#save-theme'),
      uri = btn.attr('href'),
      tName = $('#stb-name'),
      tSlug = $('#stb-slug'),
      tDesc = $('#stb-desc'),
      tCover = $('#stb-cover-image'),
      tAuth = $('#stb-author'),
      tAuthUrl = $('#stb-author-url'),
      params = '',
      media, mediaTexts = stbOptions.media;

    // WP Media
    stb.media = media = {
      buttonId: '#load-cover',
      imgId: '#stb-cover-image',

      init: function() {
        $(this.buttonId).on( 'click', this.openMediaDialog );
      },

      openMediaDialog: function( e ) {
        e.preventDefault();

        if ( this._frame ) {
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

        this._frame.on('ready', function() {
          //
        });

        this._frame.state( 'library' ).on('select', function() {
          var attachment = this.get( 'selection' ).single();
          media.handleMediaAttachment( attachment );
        });

        this._frame.open();
      },

      handleMediaAttachment: function(a) {
        var attachment = a.toJSON();
        $(this.imgId).val(attachment.url);
      }
    };

    media.init();

    // Saving Theme
    dlg.dialog({
      resizable: false,
      modal: true,
      autoOpen: false,
      width: 500,
      buttons: [
        {
          text: stbStrings.save,
          click: function(e) {
            if(('' != tSlug.val()) && ('' != tName.val()) && ('' != tDesc.val())) {
              params += '&save=' + tSlug.val();
              params += '&name=' + tName.val();
              params += '&desc=' + tDesc.val();
              params += ('' != tCover.val()) ? '&cover=' + tCover.val() : '';
              params += ('' != tAuth.val()) ? '&author=' + tAuth.val() : '';
              params += ('' != tAuthUrl.val()) ? '&au=' + tAuthUrl.val() : '';

              document.location = uri + params;
            }
            else $(this).dialog('close');
          }
        },
        {
          text: stbStrings.cancel,
          click: function(e) {
            $(this).dialog('close');
          }
        }
      ]
    });

    btn.click(function(e) {
      e.preventDefault();

      dlg.dialog('open');
    });

    // Uploading Theme
    var
      uConsole = $('#upload-console'),
      progress = $('#upload-progress'),
      message = $('#stb-message');

    var uploader = new plupload.Uploader({
      browse_button: 'upload-theme',
      url: stbUploader.url + '?path=' + stbUploader.path,
      multi_selection: false,
      filters: {
        max_file_size : '500kb',
        mime_types: [
          { title : "Zip files", extensions : "zip" }
        ]
      },
      init: {
        PostInit: function() {
          uConsole.text('');
          progress.text('');
        },
        FilesAdded: function(up, files) {
          plupload.each(files, function(file) {
            uConsole.text(file.name);
          });
          this.start();
        },
        UploadProgress: function(up, file) {
          progress.text(file.percent + '%');
        },
        UploadComplete: function() {
          uConsole.text('');
          progress.text('');
          document.location = stbUploader.redirect + '&uploaded=1';
        },
        Error: function(up, err) {
          message.addClass('error below-h2').html('<p>' + err.code + ": " + err.message + '</p>').show();
        }
      }
    });

    uploader.init();
  });
})(jQuery);