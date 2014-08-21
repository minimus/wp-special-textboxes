(function() {
	tinymce.PluginManager.requireLangPack('wstb');
	 
	tinymce.create('tinymce.plugins.wstb', {
		
		init : function(ed, url) {
      this.editor = ed;
      
			ed.addCommand('wstb', function() {
				var se = ed.selection;

				// No selection
				if (se.isCollapsed())	return;
					
				ed.windowManager.open({
					file : url + '/dialog.php',
					width : 520 + parseInt(ed.getLang('wstb.delta_width', 0)),
					height : 520 + parseInt(ed.getLang('wstb.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url 
				});
			});

			ed.addButton('wstb', {
				title : 'wstb.insert_wstb',
				cmd : 'wstb',
				image : url + '/img/wstb.png'
			});

			/*ed.onNodeChange.add(function(ed, cm, n, co) {
				//cm.setActive('wstb', !co);
				cm.setDisabled('wstb', co);
			});*/
      ed.on("NodeChange", function(e) {
        //tinymce.ui.Control.setDisabled("wstb", ed.selection.isCollapsed());
        ed.controlManager.setDisabled("wstb", ed.selection.isCollapsed());
      });
		},
		//createControl : function(n, cm) {
		//	return null;
		//},
		getInfo : function() {
			return {
					longname  : 'Special Text Boxes',
					author 	  : 'minimus',
					authorurl : 'http://blogcoding.ru',
					infourl   : 'http://www.simplelib.com',
					version   : "4.2.70"
			};
		}
	});

	tinymce.PluginManager.add('wstb', tinymce.plugins.wstb);
})();