<?php

/**
 * @author minimus
 * @copyright 2009
 */

header("Content-type: text/css"); 
include("../../../../wp-load.php");
$stextboxesOptions = $stbObject->getAdminOptions();

?>

(function($){
	$(document).ready(function() {
		var options = {direction: 'vertical'};
	
		function callback(sb,sc,si) {
			if (sb.css('display') == 'none') {
				sc.css({'margin-bottom' : '<?php echo $stextboxesOptions['bottom_margin']; ?>px'});
				si.attr({'src' : '<?php echo WP_PLUGIN_URL.'/wp-special-textboxes/images/show.png';?>', 'title' : '<?php _e('Show', 'wp-special-textboxes');?>'});
				<?php if ($stextboxesOptions['rounded_corners'] === 'true') { ?>
				sc.css({'-webkit-border-bottom-left-radius' : '5px', 
								'-webkit-border-bottom-right-radius' : '5px', 
								'-moz-border-radius-bottomleft' : '5px', 
								'-moz-border-radius-bottomright' : '5px',
                'border-bottom-left-radius' : '5px', 
								'border-bottom-right-radius' : '5px'});
				<?php }?>
			}
			else {
				si.attr({'src' : '<?php echo WP_PLUGIN_URL.'/wp-special-textboxes/images/hide.png';?>', 'title' : '<?php _e('Hide', 'wp-special-textboxes');?>'});
			}
		
			$(this).parent().parent().children('#caption').css({'margin-bottom' : '<?php echo $stextboxesOptions['bottom_margin']; ?>px'});
			return false;
		}
	
		$(".stb-tool").bind("click", function() {
			sb = $(this).parent().parent().children('#body');
			sc = $(this).parent().parent().children('#caption');
			si = $(this).children('#stb-toolimg');
			if (sb.css('display') != 'none')	{				
				sb.hide('blind',options,500, function() {callback(sb,sc,si);});
				
			}
			else {
				sb.show('blind',options,500,function() {callback(sb,sc,si);});
				sc.css({'margin-bottom' : '0px'});			
				<?php if ($stextboxesOptions['rounded_corners'] === 'true') { ?>
				sc.css({'-webkit-border-bottom-left-radius' : '0px', 
								'-webkit-border-bottom-right-radius' : '0px', 
								'-moz-border-radius-bottomleft' : '0px', 
								'-moz-border-radius-bottomright' : '0px',
                'border-bottom-left-radius' : '0px', 
								'border-bottom-right-radius' : '0px'});
				<?php }?>
			}
			return false;
		});
    
    $("#tabs").tabs();
	
		$('#cb_color, #cb_caption_color, #cb_background, #cb_caption_background, #cb_border_color').ColorPicker({
  			onSubmit: function(hsb, hex, rgb, el){
  				$(el).val(hex);
  				$(el).ColorPickerHide();
  			},
  			onBeforeShow: function(){
  				$(this).ColorPickerSetColor(this.value);
  			}
  		}).bind('keyup', function(){
  		$(this).ColorPickerSetColor(this.value);
  	});
		return false;
	});
})(jQuery)