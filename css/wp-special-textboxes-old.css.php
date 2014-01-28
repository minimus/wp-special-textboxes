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
}
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
  border: 1px <?php echo $stbOptions['border_style']; ?> #FF4F4A;
  color: #<?php echo $val['cssStyle']['color']; ?>;
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
  background-color: #<?php echo $val['cssStyle']['bgColor']; ?>;
  color: #<?php echo $val['cssStyle']['color']; ?>;
  border-top-color: #<?php echo $val['cssStyle']['bgColor']; ?>;
  border-right-color: #<?php echo $val['cssStyle']['bgColor']; ?>;
  border-bottom-color: #<?php echo $val['cssStyle']['bgColor']; ?>;
  border-left-color: #<?php echo $val['cssStyle']['bgColor']; ?>;
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
}
<?php } ?>

.stb-alert_box, 
.stb-download_box,
.stb-info_box, 
.stb-warning_box, 
.stb-black_box {
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
}

.stb-alert_box {
	background-color: #FFE7E6;
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/alert<?php if($stbOptions['bigImg'] === 'true') echo '-b'; ?>.png);	
	<?php } ?>
	border: 1px <?php echo $stbOptions['border_style']; ?> #FF4F4A;
	color: #000000;
}

.stb-download_box {
	background-color: #DFF0FF;
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/download<?php if($stbOptions['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	border: 1px <?php echo $stbOptions['border_style']; ?> #65ADFE;
	color: #000000;
}

.stb-info_box {
	background-color: #E2F8DE;
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/info<?php if($stbOptions['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	border: 1px <?php echo $stbOptions['border_style']; ?> #7AD975;
	color: #000000;
}

.stb-warning_box {
	background-color: #FEFFD5;
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/warning<?php if($stbOptions['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	border: 1px <?php echo $stbOptions['border_style']; ?> #FE9A05;
	color: #000000;
}

.stb-black_box {
	background-color: #000000;
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/earth<?php if($stbOptions['bigImg'] === 'true') echo '-b'; ?>.png);
	<?php } ?>
	border: 1px <?php echo $stbOptions['border_style']; ?> #6E6E6E;
	color: #FFFFFF;
}

.stb-grey_box {
	background: #EEEEEE;
	padding: 5px;
	margin-top: <?php echo $stbOptions['top_margin']; ?>px;  
	margin-right: <?php echo $stbOptions['right_margin']; ?>px;  
	margin-bottom: <?php echo $stbOptions['bottom_margin']; ?>px;  
	margin-left: <?php echo $stbOptions['left_margin']; ?>px;
	border: 1px <?php echo $stbOptions['border_style']; ?> #BBBBBB;
	color: #000000;
	<?php if($stbOptions['fontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['fontSize'] ?>px;<?php } ?>
	<?php if($stbOptions['langDirect'] === 'ltr') {?>
	text-align: left;<?php } else {?>
	text-align: right;<?php } ?>
}

.stb-grey-caption_box {
	font-weight: bold;
	background-repeat: no-repeat;
	-webkit-background-origin: border;
	-webkit-background-clip: border;
	-moz-background-origin: border;
	-moz-background-clip: border;
  background-origin: border;
	background-clip: border;
	background-color: #BBBBBB;
	color: #FFFFFF;
	padding-top: 3px;
	padding-right: 5px;
	padding-bottom: 3px;
	padding-left: 5px;
	border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 0px;
	border-left-width: 1px;
	border-top-color: #BBBBBB;
	border-right-color: #BBBBBB;
	border-bottom-color: #BBBBBB;
	border-left-color: #BBBBBB;
	min-height:20px;
	<?php if($stbOptions['captionFontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['captionFontSize'] ?>px;<?php } ?>
	<?php if($stbOptions['langDirect'] === 'ltr') {?>
	text-align: left;<?php } else {?>
	text-align: right;<?php } ?>
	margin-top: <?php echo $stbOptions['top_margin']; ?>px;  
	margin-right: <?php echo $stbOptions['right_margin']; ?>px;  
	margin-bottom: 0px;  
	margin-left: <?php echo $stbOptions['left_margin']; ?>px;
}
.stb-grey-body_box {
	background-color: #EEEEEE;
	padding: 5px;
	color: #000000;
	border-top-width: 0px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-top-color: #BBBBBB;
	border-right-color: #BBBBBB;
	border-bottom-color: #BBBBBB;
	border-left-color: #BBBBBB;
	<?php if($stbOptions['fontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['fontSize'] ?>px;<?php } ?>
	<?php if($stbOptions['langDirect'] === 'ltr') {?>
	text-align: left;<?php } else {?>
	text-align: right;<?php } ?>
	margin-top: 0px;  
	margin-right: <?php echo $stbOptions['right_margin']; ?>px;  
	margin-bottom: <?php echo $stbOptions['bottom_margin']; ?>px;  
	margin-left: <?php echo $stbOptions['left_margin']; ?>px;
}

.stb-alert-caption_box,
.stb-download-caption_box,
.stb-info-caption_box,
.stb-warning-caption_box,
.stb-black-caption_box {
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
}

.stb-alert-caption_box {
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/alert.png);
	<?php } ?>
	background-color: #FF4F4A;
	color: #FFFFFF;
	border-top-color: #FF4F4A;
	border-right-color: #FF4F4A;
	border-bottom-color: #FF4F4A;
	border-left-color: #FF4F4A;
}

.stb-download-caption_box {
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/download.png);
	<?php } ?>
	background-color: #65ADFE;
	color: #FFFFFF;
	border-top-color: #65ADFE;
	border-right-color: #65ADFE;
	border-bottom-color: #65ADFE;
	border-left-color: #65ADFE;
}

.stb-info-caption_box {
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/info.png);
	<?php } ?>
	background-color: #7AD975;
	color: #FFFFFF;
	border-top-color: #7AD975;
	border-right-color: #7AD975;
	border-bottom-color: #7AD975;
	border-left-color: #7AD975;
}

.stb-warning-caption_box {
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/warning.png);
	<?php } ?>
	background-color: #FE9A05;
	color: #FFFFFF;
	border-top-color: #FE9A05;
	border-right-color: #FE9A05;
	border-bottom-color: #FE9A05;
	border-left-color: #FE9A05;
}

.stb-black-caption_box {
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(../images/earth.png);
	<?php } ?>
	background-color: #333333;
	color: #FFFFFF;
	border-top-color: #333333;
	border-right-color: #333333;
	border-bottom-color: #333333;
	border-left-color: #333333;
	border: 1px <?php echo $stbOptions['border_style']; ?> #6E6E6E;
}

.stb-alert-body_box,
.stb-download-body_box,
.stb-info-body_box,
.stb-warning-body_box,
.stb-black-body_box {
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
}

.stb-alert-body_box {
	background-color: #FFE7E6;
	color: #000000;
	border-top-color: #FF4F4A;
	border-right-color: #FF4F4A;
	border-bottom-color: #FF4F4A;
	border-left-color: #FF4F4A;
}

.stb-download-body_box {
	background-color: #DFF0FF;
	color: #000000;
	border-top-color: #65ADFE;
	border-right-color: #65ADFE;
	border-bottom-color: #65ADFE;
	border-left-color: #65ADFE;
}

.stb-info-body_box {
	background-color: #E2F8DE;
	color: #000000;
	border-top-color: #7AD975;
	border-right-color: #7AD975;
	border-bottom-color: #7AD975;
	border-left-color: #7AD975;
}

.stb-warning-body_box {
	background-color: #FEFFD5;
	color: #000000;
	border-top-color: #FE9A05;
	border-right-color: #FE9A05;
	border-bottom-color: #FE9A05;
	border-left-color: #FE9A05;
}

.stb-black-body_box {
	background-color: #000000;
	color: #FFFFFF;
	border-top-color: #333333;
	border-right-color: #333333;
	border-bottom-color: #333333;
	border-left-color: #333333;
	border: 1px <?php echo $stbOptions['border_style']; ?> #000000;
}

.stb-custom_box {
	padding-top: 5px;
	padding-bottom: 5px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-top-width: 1px;
	color: #<?php echo $stbOptions['cb_color']; ?>;	
	<?php if($stbOptions['cb_fontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['cb_fontSize'] ?>px;<?php } ?>
	background-color: #<?php echo $stbOptions['cb_background']; ?>;
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(<?php echo (($stbOptions['bigImg'] === 'true') ? $stbOptions['cb_bigImg'] :  $stbOptions['cb_image']); ?>);
	<?php } ?>
	background-repeat: no-repeat;
	margin-top: <?php echo $stbOptions['top_margin']; ?>px;
  margin-right: <?php echo $stbOptions['right_margin']; ?>px;
  margin-bottom: <?php echo $stbOptions['bottom_margin']; ?>px;
  margin-left: <?php echo $stbOptions['left_margin']; ?>px;
	border-top-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-right-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-bottom-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-left-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-top-style: <?php echo $stbOptions['border_style']; ?>;
	border-bottom-style: <?php echo $stbOptions['border_style']; ?>;
  border-right-style: <?php echo $stbOptions['border_style']; ?>;
  border-left-style: <?php echo $stbOptions['border_style']; ?>;
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
}

.stb-custom-caption_box {
	border-top-width: 1px;
	border-right-width: 1px;
	border-bottom-width: 0px;
	border-left-width: 1px;
	padding-top: 3px;
	padding-bottom: 3px;
	min-height:20px;
	color: #<?php echo $stbOptions['cb_caption_color']; ?>;
	font-weight: bold;
	border-top-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-right-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-bottom-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-left-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-top-style: <?php echo $stbOptions['border_style']; ?>;
  border-right-style: <?php echo $stbOptions['border_style']; ?>;
  border-left-style: <?php echo $stbOptions['border_style']; ?>;
	background-color: #<?php echo $stbOptions['cb_caption_background']; ?>;
	<?php if ($stbOptions['showImg'] === 'true') {?>
	background-image: url(<?php echo $stbOptions['cb_image']; ?>);
	<?php } ?>
	background-repeat: no-repeat;
	margin-top: <?php echo $stbOptions['top_margin']; ?>px;
  margin-right: <?php echo $stbOptions['right_margin']; ?>px;
  margin-bottom: 0px;
  margin-left: <?php echo $stbOptions['left_margin']; ?>px;
	<?php if($stbOptions['langDirect'] === 'ltr') {?>
	padding-left: <?php echo (($stbOptions['showImg'] === 'true') ? '25' : '5' ); ?>px;
	padding-right: 5px;
	background-position: left;
	text-align: left;<?php } else {?>
	padding-right: <?php echo (($stbOptions['showImg'] === 'true') ? '25' : '5' ); ?>px;
	padding-left: 5px;
	background-position: right;
	text-align: right;<?php } ?>
	<?php if($stbOptions['cb_captionFontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['cb_captionFontSize'] ?>px;<?php } ?>
}

.stb-custom-body_box {
	border-top-width: 0px;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	padding-top: 3px;
	padding-right: 5px;
	padding-bottom: 3px;
	padding-left: 5px;
	color: #<?php echo $stbOptions['cb_color']; ?>;
	<?php if($stbOptions['cb_fontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['cb_fontSize'] ?>px;<?php } ?>
	border-top-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-right-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-bottom-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-left-color: #<?php echo $stbOptions['cb_border_color']; ?>;
	border-bottom-style: <?php echo $stbOptions['border_style']; ?>;
  border-right-style: <?php echo $stbOptions['border_style']; ?>;
  border-left-style: <?php echo $stbOptions['border_style']; ?>;
	background-color: #<?php echo $stbOptions['cb_background']; ?>;
	margin-top: 0px;
  margin-right: <?php echo $stbOptions['right_margin']; ?>px;
  margin-bottom: <?php echo $stbOptions['bottom_margin']; ?>px;
  margin-left: <?php echo $stbOptions['left_margin']; ?>px;
	<?php if($stbOptions['langDirect'] === 'ltr') {?>
	text-align: left;<?php } else {?>
	text-align: right;<?php } ?>
}

<?php if ($stbOptions['rounded_corners'] == "true") { ?>
.stb-alert_box,
.stb-download_box,
.stb-grey_box,
.stb-info_box ,
.stb-warning_box,
.stb-black_box,
.stb-custom_box  {
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}
.stb-black-caption_box,
.stb-alert-caption_box,
.stb-download-caption_box,
.stb-info-caption_box,
.stb-warning-caption_box,
.stb-grey-caption_box,
.stb-custom-caption_box  {
	-webkit-border-top-left-radius: 5px;
	-webkit-border-top-right-radius: 5px;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
  border-top-left-radius: 5px;
	border-top-right-radius: 5px;
}
.stb-black-body_box,
.stb-alert-body_box,
.stb-download-body_box,
.stb-info-body_box,
.stb-warning-body_box,
.stb-grey-body_box,
.stb-custom-body_box {
	-webkit-border-bottom-left-radius: 5px;
	-webkit-border-bottom-right-radius: 5px;
	-moz-border-radius-bottomleft: 5px;
	-moz-border-radius-bottomright: 5px;
  border-bottom-left-radius: 5px;
	border-bottom-right-radius: 5px;
}
<?php 
}
if ( $stbOptions['box_shadow'] == "true" ) { ?>
.stb-alert_box,
.stb-download_box,
.stb-grey_box,
.stb-info_box ,
.stb-warning_box,
.stb-black_box,
.stb-black-body_box,
.stb-alert-body_box,
.stb-download-body_box,
.stb-info-body_box,
.stb-warning-body_box,
.stb-grey-body_box,
.stb-black-caption_box,
.stb-alert-caption_box,
.stb-download-caption_box,
.stb-info-caption_box,
.stb-warning-caption_box,
.stb-grey-caption_box,
.stb-custom_box,
.stb-custom-caption_box,
.stb-custom-body_box {
	-webkit-box-shadow: 3px 3px 3px #888;
	-moz-box-shadow: 3px 3px 3px #888;
  box-shadow: 3px 3px 3px #888;
}	
<?php 
} 

if ($stbOptions['text_shadow'] == "true") {?>
.stb-alert_box,
.stb-download_box,
.stb-grey_box,
.stb-info_box,
.stb-warning_box,
.stb-black_box,
.stb-custom_box,
.stb-black-body_box,
.stb-alert-body_box,
.stb-download-body_box,
.stb-info-body_box,
.stb-warning-body_box,
.stb-grey-body_box,
.stb-custom-body_box {
	text-shadow: 1px 1px 2px #888;
}
<?php 
}
?>