<?php

/**
 * @author minimus
 * @copyright 2009 - 2010
 */

header("Content-type: text/css"); 
include("../../../../wp-load.php");
$settings = $stbObject->getAdminOptions();

?>

(function($){
	$(document).ready(function() {
		var options = {direction: 'vertical'};
	
		function callback(sb,sc,si) {
			if (sb.css('display') == 'none') {
				sc.css({'margin-bottom' : '<?php echo $settings['bottom_margin']; ?>px'});
				si.attr({'src' : '<?php echo WP_PLUGIN_URL.'/wp-special-textboxes/images/show.png';?>', 'title' : '<?php _e('Show', 'wp-special-textboxes');?>'});
				<?php if ($settings['rounded_corners'] === 'true') { ?>
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
		
			$(this).parent().parent().children('#caption').css({'margin-bottom' : '<?php echo $settings['bottom_margin']; ?>px'});
			return false;
		}
	
		$(".stb-tool").bind("click", function() {
			id = $(this).attr('id').split('-');
      idn = id[2];
      sb = $('#stb-body-box-'+idn);
			sc = $('#stb-caption-box-'+idn);
      si = $('#stb-toolimg-'+idn);
			if (sb.css('display') != 'none')	{				
				sb.hide('blind',options,500, function() {callback(sb,sc,si);});
			}
			else {
				sb.show('blind',options,500,function() {callback(sb,sc,si);});
				sc.css({'margin-bottom' : '0px'});			
				<?php if ($settings['rounded_corners'] === 'true') { ?>
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
		return false;
	});
})(jQuery)