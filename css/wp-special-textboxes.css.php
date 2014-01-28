<?php
/**
 * @author minimus
 * @copyright 2009 - 2010
 */

header("Content-type: text/css"); 
include("../../../../wp-load.php");
$stbOptions = $stbObject->getAdminOptions();
$stbStyles = $stbObject->styles;
$stbClasses = $stbObject->classes;
?>

.stb-container {
	margin: 0px auto; 
	padding: 0px;
	position: static;
  /*box-sizing: content-box !important;
  -moz-box-sizing: content-box !important;
  -webkit-box-sizing: content-box !important;*/
}

/*.stb-canvas, .stb-ccanvas, .stb-body {
  box-sizing: content-box !important;
  -moz-box-sizing: content-box !important;
  -webkit-box-sizing: content-box !important;
}*/

.stb-tool {
	<?php if($stbOptions['langDirect'] === 'ltr') {?> float: right; <?php } else { ?> float: left; <?php } ?>
	padding: 0px; 
	margin: 0px auto;
}

<?php foreach($stbStyles as $val) { ?>
.stb-<?php echo $val['slug'] ?>_box {
  <?php if($stbOptions['fontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['fontSize'] ?>px;<?php } ?>
  margin-top: <?php echo $stbOptions['top_margin']; ?>px;  
  margin-right: <?php echo $stbOptions['right_margin']; ?>px;  
  margin-bottom: <?php echo $stbOptions['bottom_margin']; ?>px;  
  margin-left: <?php echo $stbOptions['left_margin']; ?>px;
  <?php if ($stbOptions['showImg'] === 'true') { 
          if($stbOptions['langDirect'] === 'ltr') {?>
  padding-left: <?php echo (($stbOptions['bigImg'] === 'true') ? '50' : '25' ); ?>px;
  padding-right: 5px;
  background-position:top left;
  text-align: left;<?php } else {?>
  padding-right: <?php echo (($stbOptions['bigImg'] === 'true') ? '50' : '25' ); ?>px;
  padding-left: 5px;
  background-position:top right;
  text-align: right;<?php } ?>
  min-height: <?php echo (($stbOptions['bigImg'] === 'true') ? '40' : '20');?>px;
  <?php } else { ?>
  padding-left: 5px; 
  padding-right: 5px;
  <?php } ?>
  background-repeat: no-repeat;
  padding-top: 5px;
  padding-bottom: 5px;
  /* Class Dependent Parameters */
  background-color: #<?php echo $val['cssStyle']['bgColor']; ?>;
  <?php if ($stbOptions['showImg'] === 'true') {?>
  background-image: url(<?php echo ($stbOptions['bigImg'] === 'true') ? $val['cssStyle']['bigImg'] : $val['cssStyle']['image']; ?>);
  <?php } ?>
  border: 1px <?php echo $stbOptions['border_style'].' #'.$val['cssStyle']['borderColor']; ?>;
  color: #<?php echo $val['cssStyle']['color']; ?>;
  <?php if ($stbOptions['rounded_corners'] == "true") { ?>
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
  <?php }
  if ( $stbOptions['box_shadow'] == "true" ) { ?>
  -webkit-box-shadow: 3px 3px 3px #888;
  -moz-box-shadow: 3px 3px 3px #888;
  box-shadow: 3px 3px 3px #888;
  <?php }
  if ($stbOptions['text_shadow'] == "true") {?>
  text-shadow: 1px 1px 2px #888;
  <?php } ?>
}

.stb-<?php echo $val['slug']; ?>-caption_box {
  border-top-style: <?php echo $stbOptions['border_style']; ?>;  
  border-right-style: <?php echo $stbOptions['border_style']; ?>;  
  border-left-style: <?php echo $stbOptions['border_style']; ?>;  
  margin-top: <?php echo $stbOptions['top_margin']; ?>px;  
  margin-right: <?php echo $stbOptions['right_margin']; ?>px;  
  margin-bottom: 0px;  
  margin-left: <?php echo $stbOptions['left_margin']; ?>px;
  font-weight: bold;
  background-repeat: no-repeat;
  -webkit-background-origin: border;
  -webkit-background-clip: border;
  -moz-background-origin: border;
  -moz-background-clip: border;
  background-origin: border;
  background-clip: border;
  <?php if($stbOptions['langDirect'] === 'ltr') {?>
  padding-left: <?php echo (($stbOptions['showImg'] === 'true') ? '25' : '5' ); ?>px;
  padding-right: 5px;
  background-position: left;
  text-align: left;<?php } else {?>
  padding-right: <?php echo (($stbOptions['showImg'] === 'true') ? '25' : '5' ); ?>px;
  padding-left: 5px;
  background-position: right;
  text-align: right;<?php } ?>
  padding-top: 3px;
  padding-bottom: 3px;
  border-top-width: 1px;
  border-right-width: 1px;
  border-bottom-width: 0px;
  border-left-width: 1px;
  border-left-style: solid;
  min-height:20px;
  <?php if($stbOptions['captionFontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['captionFontSize'] ?>px;<?php } ?>
  /* Class Dependent Parameters */
  <?php if ($stbOptions['showImg'] === 'true') {?>
  background-image: url(<?php echo $val['cssStyle']['image']; ?>);
  <?php } ?>
  background-color: #<?php echo $val['cssStyle']['captionBgColor']; ?>;
  color: #<?php echo $val['cssStyle']['captionColor']; ?>;
  border-top-color: #<?php echo $val['cssStyle']['borderColor']; ?>;
  border-right-color: #<?php echo $val['cssStyle']['borderColor']; ?>;
  border-bottom-color: #<?php echo $val['cssStyle']['borderColor']; ?>;
  border-left-color: #<?php echo $val['cssStyle']['borderColor']; ?>;
  <?php if ($stbOptions['rounded_corners'] == "true") { ?>
  -webkit-border-top-left-radius: 5px;
  -webkit-border-top-right-radius: 5px;
  -moz-border-radius-topleft: 5px;
  -moz-border-radius-topright: 5px;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  <?php }
  if ( $stbOptions['box_shadow'] == "true" ) { ?>
  -webkit-box-shadow: 3px 3px 3px #888;
  -moz-box-shadow: 3px 3px 3px #888;
  box-shadow: 3px 3px 3px #888;
  <?php }
  if ($stbOptions['text_shadow'] == "true") {?>
  text-shadow: 1px 1px 2px #888;
  <?php } ?>
}

.stb-<?php echo $val['slug']; ?>-body_box {
  padding: 5px;
  border-top-width: 0px;
  border-right-width: 1px;
  border-bottom-width: 1px;
  border-left-width: 1px;
  <?php if($stbOptions['fontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['fontSize'] ?>px;<?php } ?>
  <?php if($stbOptions['langDirect'] === 'ltr') {?>
  text-align: left;<?php } else {?>
  text-align: right;<?php } ?>
  border-left-style: <?php echo $stbOptions['border_style']; ?>;  
  border-right-style: <?php echo $stbOptions['border_style']; ?>;  
  border-bottom-style: <?php echo $stbOptions['border_style']; ?>;  
  margin-top: 0px;  margin-right: <?php echo $stbOptions['right_margin']; ?>px;  
  margin-bottom: <?php echo $stbOptions['bottom_margin']; ?>px;  
  margin-left: <?php echo $stbOptions['left_margin']; ?>px;
  /* Class Dependent Parameters */
  background-color: #<?php echo $val['cssStyle']['bgColor']; ?>;
  color: #<?php echo $val['cssStyle']['color']; ?>;
  border-top-color: #<?php echo $val['cssStyle']['borderColor']; ?>;
  border-right-color: #<?php echo $val['cssStyle']['borderColor']; ?>;
  border-bottom-color: #<?php echo $val['cssStyle']['borderColor']; ?>;
  border-left-color: #<?php echo $val['cssStyle']['borderColor']; ?>;
  <?php if ($stbOptions['rounded_corners'] == "true") { ?>
  -webkit-border-bottom-left-radius: 5px;
  -webkit-border-bottom-right-radius: 5px;
  -moz-border-radius-bottomleft: 5px;
  -moz-border-radius-bottomright: 5px;
  border-bottom-left-radius: 5px;
  border-bottom-right-radius: 5px;
  <?php }
  if ( $stbOptions['box_shadow'] == "true" ) { ?>
  -webkit-box-shadow: 3px 3px 3px #888;
  -moz-box-shadow: 3px 3px 3px #888;
  box-shadow: 3px 3px 3px #888;
  <?php }
  if ($stbOptions['text_shadow'] == "true") {?>
  text-shadow: 1px 1px 2px #888;
  <?php } ?>
}

<?php } ?>