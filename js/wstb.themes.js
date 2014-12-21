/**
 * Created by minimus on 14.12.2014.
 */
var sPointer = stbOptions.pointer;
(function($) {
  $(document).ready(function() {
    var target = $('h2 .theme-count');

    if(sPointer.enabled) {
      target.pointer({
        content: '<h3>' + sPointer.title + '</h3>' + sPointer.content,
        position: sPointer.position,
        close: function() {
          $.ajax({
            url: ajaxurl,
            data: {
              action: 'close_stb_pointer',
              pointer: 'themes'
            },
            async: true
          });
        }
      }).pointer('open');
    }
  });
})(jQuery);