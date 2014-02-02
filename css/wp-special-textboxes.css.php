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

.stb-container-css {
	margin: <?php echo $stbOptions['top_margin']; ?>px <?php echo $stbOptions['right_margin']; ?>px <?php echo $stbOptions['bottom_margin']; ?>px <?php echo $stbOptions['left_margin']; ?>px;
}

.stb-box {
<?php if($stbOptions['fontSize'] !== '0') { ?>  font-size: <?php echo $stbOptions['fontSize'] ?>px;<?php } ?>
<?php if ($stbOptions['text_shadow'] == "true") {?>  text-shadow: 1px 1px 2px #888;<?php } ?>
}

.stb-caption-box {
<?php if($stbOptions['captionFontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['captionFontSize'] ?>px;<?php } ?>
}

.stb-body-box {
  box-sizing: content-box;
  padding: 5px 10px 10px;
<?php if($stbOptions['fontSize'] !== '0') { ?>font-size: <?php echo $stbOptions['fontSize'] ?>px;<?php } ?>
}

  /* Class Dependent Parameters */
<?php foreach($stbStyles as &$val) {
  if(!isset($val['cssStyle']['bgColorEnd'])) {
    $val['cssStyle']['bgColor'] = str_replace('#', '', $val['jsStyle']['color']);
    $val['cssStyle']['bgColorEnd'] = str_replace('#', '', $val['jsStyle']['colorTo']);
  }
  if(!isset($val['cssStyle']['captionBgColorEnd'])) {
    $val['cssStyle']['captionBgColor'] = str_replace('#', '', $val['jsStyle']['caption']['color']);
    $val['cssStyle']['captionBgColorEnd'] = str_replace('#', '', $val['jsStyle']['caption']['colorTo']);
  }
?>
.stb-border.stb-<?php echo $val['slug'] ?>-container {
  border: 1px <?php echo $stbOptions['border_style'].' #'.$val['cssStyle']['borderColor']; ?>;
}
.stb-<?php echo $val['slug'] ?>-container {
  background-image: linear-gradient(#<?php echo $val['cssStyle']['captionBgColor']; ?> 30%, #<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%);
  <?php if ($stbOptions['showImg'] === 'true') {?>
    /*background-image: url(<?php echo ($stbOptions['bigImg'] === 'true') ? $val['cssStyle']['bigImg'] : $val['cssStyle']['image']; ?>);*/
  <?php } ?>

}
.stb-<?php echo $val['slug'] ?>_box {
  background-color: #<?php echo $val['cssStyle']['bgColor']; ?>;
  background-image: linear-gradient(#<?php echo $val['cssStyle']['bgColor']; ?> 30%, #<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%);
  color: #<?php echo $val['cssStyle']['color']; ?>;
}

.stb-<?php echo $val['slug']; ?>-caption_box {
  background-image: linear-gradient(#<?php echo $val['cssStyle']['captionBgColor']; ?> 30%, #<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%);
  background-color: #<?php echo $val['cssStyle']['captionBgColor']; ?>;
  color: #<?php echo $val['cssStyle']['captionColor']; ?>;
  <?php if ($stbOptions['text_shadow'] == "true") {?>
  text-shadow: 1px 1px 2px #888;
  <?php } ?>
}

.stb-<?php echo $val['slug']; ?>-body_box {
  background-image: linear-gradient(#<?php echo $val['cssStyle']['bgColor']; ?> 30%, #<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%);
  background-color: #<?php echo $val['cssStyle']['bgColor']; ?>;
  color: #<?php echo $val['cssStyle']['color']; ?>;
  <?php if ($stbOptions['text_shadow'] == "true") {?>
  text-shadow: 1px 1px 2px #888;
  <?php } ?>
}

<?php } ?>