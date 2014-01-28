(function($){
  $(document).ready(function() {
    var opts = stbUserOptions;

    $("#tabs").tabs();
  
    /*$('#js_shadow_color, #js_textShadow_color').ColorPicker({
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
        texts: opts.texts
      }).on({
          scp_ok: function(p,c) {
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