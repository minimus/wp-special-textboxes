(function () {
  var
    mceUrl = stbEditorOptions.mceUrl,
    mceUtilsUrl = stbEditorOptions.mceUtilsUrl,
    jsUrl = stbEditorOptions.jsUrl,
    slugs = stbEditorOptions.slugs;

  tinymce.PluginManager.requireLangPack('wstb');

  tinymce.create('tinymce.plugins.wstb', {

    init: function (ed, url) {
      this.editor = ed;
      var title = ed.getLang('wstb.title');

      ed.addCommand('wstb', function () {
        var se = ed.selection;

        // No selection
        if (se.isCollapsed())  return;

        ed.windowManager.open({
          file: url + '/dialog.html', //'/dialog.php',
          width: 520 + parseInt(ed.getLang('wstb.delta_width', 0)),
          height: 520 + parseInt(ed.getLang('wstb.delta_height', 0)),
          inline: 1
        }, {
          plugin_url: url,
          mceUrl: mceUrl,
          mceUtilsUrl: mceUtilsUrl,
          jsUrl: jsUrl,
          slugs: slugs
        });
      });

      ed.addButton('wstb', {
        title: title, //'Insert Special Text Box',
        cmd: 'wstb',
        image: url + '/img/wstb.png'
      });

      ed.on("NodeChange", function (e) {
        //tinymce.ui.Control.setDisabled("wstb", ed.selection.isCollapsed());
        ed.controlManager.setDisabled("wstb", ed.selection.isCollapsed());
      });
    },
    //createControl : function(n, cm) {
    //	return null;
    //},
    getInfo: function () {
      return {
        longname: 'Special Text Boxes',
        author: 'minimus',
        authorurl: 'http://blogcoding.ru',
        infourl: 'http://www.simplelib.com',
        version: "5.1.88"
      };
    }
  });

  tinymce.PluginManager.add('wstb', tinymce.plugins.wstb);
})();