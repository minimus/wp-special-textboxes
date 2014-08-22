<?php
/**
 * @author minimus
 * @copyright 2009 - 2010
 */

$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

ini_set('html_errors', 0);

define('SHORTINIT', true);

function sanitize_option($option, $value) {
  return $value;
}

require_once( $root . '/wp-load.php' );

global $wpdb;

$stbOptions = get_option('SpecialTextBoxesAdminOptions');

function getStbStyles() {
  global $wpdb;
  $sTable = $wpdb->prefix . "stb_styles";
  $styles = array();

  if($wpdb->get_var("SHOW TABLES LIKE '$sTable'") == $sTable) {
    $sSql = "SELECT slug, caption, js_style, css_style, stype, trash FROM $sTable WHERE trash IS FALSE;";
    $rows = $wpdb->get_results($sSql, ARRAY_A);
    $style = array();
    foreach($rows as $value) {
      $style['slug'] = $value['slug'];
      $style['name'] = $value['caption'];
      $style['stype'] = $value['stype'];
      $style['jsStyle'] = unserialize($value['js_style']);
      $style['cssStyle'] = unserialize($value['css_style']);
      array_push($styles, $style);
    }
  }
  return $styles;
}

$stbStyles = getStbStyles();

header("Content-type: text/css");
/*include("../../../../wp-load.php");
$stbOptions = $stbObject->getAdminOptions();
$stbStyles = $stbObject->styles;
$stbClasses = $stbObject->classes;*/
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
    $val['cssStyle']['bgColorEnd'] = str_replace('#', '', $val['cssStyle']['bgColor']);
  }
  if(!isset($val['cssStyle']['captionBgColorEnd'])) {
    $val['cssStyle']['captionBgColorEnd'] = str_replace('#', '', $val['cssStyle']['captionBgColor']);
  }
?>
.stb-border.stb-<?php echo $val['slug'] ?>-container {
  border: 1px <?php echo $stbOptions['border_style'].' #'.$val['cssStyle']['borderColor']; ?>;
}
.stb-side.stb-<?php echo $val['slug'] ?>-container {
  background: #<?php echo $val['cssStyle']['captionBgColor']; ?>; /* Old browsers */
  background: -moz-linear-gradient(top,  #<?php echo $val['cssStyle']['captionBgColor']; ?> 30%, #<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* FF3.6+ */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#<?php echo $val['cssStyle']['captionBgColor']; ?>), color-stop(90%,#<?php echo $val['cssStyle']['captionBgColorEnd']; ?>)); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(top,  #<?php echo $val['cssStyle']['captionBgColor']; ?> 30%,#<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(top,  #<?php echo $val['cssStyle']['captionBgColor']; ?> 30%,#<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* Opera 11.10+ */
  background: -ms-linear-gradient(top,  #<?php echo $val['cssStyle']['captionBgColor']; ?> 30%,#<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* IE10+ */
  background: linear-gradient(#<?php echo $val['cssStyle']['captionBgColor']; ?> 30%, #<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* W3C */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $val['cssStyle']['captionBgColor']; ?>', endColorstr='#<?php echo $val['cssStyle']['captionBgColorEnd']; ?>',GradientType=0 ); /* IE6-9 */
}
.stb-side-none.stb-<?php echo $val['slug'] ?>-container {
  background: #<?php echo $val['cssStyle']['bgColor']; ?>; /* Old browsers */
  background: -moz-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%, #<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* FF3.6+ */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#<?php echo $val['cssStyle']['bgColor']; ?>), color-stop(90%,#<?php echo $val['cssStyle']['bgColorEnd']; ?>)); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%,#<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%,#<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* Opera 11.10+ */
  background: -ms-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%,#<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* IE10+ */
  background: linear-gradient(#<?php echo $val['cssStyle']['bgColor']; ?> 30%, #<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* W3C */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $val['cssStyle']['bgColor']; ?>', endColorstr='#<?php echo $val['cssStyle']['bgColorEnd']; ?>',GradientType=0 ); /* IE6-9 */
}
.stb-<?php echo $val['slug'] ?>_box {
  background: #<?php echo $val['cssStyle']['bgColor']; ?>; /* Old browsers */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $val['cssStyle']['bgColor']; ?>', endColorstr='#<?php echo $val['cssStyle']['bgColorEnd']; ?>',GradientType=0 ); /* IE6-9 */
  background: -moz-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%, #<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* FF3.6+ */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#<?php echo $val['cssStyle']['bgColor']; ?>), color-stop(90%,#<?php echo $val['cssStyle']['bgColorEnd']; ?>)); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%,#<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%,#<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* Opera 11.10+ */
  background: -ms-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%,#<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* IE10+ */
  background: linear-gradient(#<?php echo $val['cssStyle']['bgColor']; ?> 30%, #<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* W3C */
  color: #<?php echo $val['cssStyle']['color']; ?>;
}

.stb-<?php echo $val['slug']; ?>-caption_box {
  background: #<?php echo $val['cssStyle']['captionBgColor']; ?>; /* Old browsers */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $val['cssStyle']['captionBgColor']; ?>', endColorstr='#<?php echo $val['cssStyle']['captionBgColorEnd']; ?>',GradientType=0 ); /* IE6-9 */
  background: -moz-linear-gradient(top,  #<?php echo $val['cssStyle']['captionBgColor']; ?> 30%, #<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* FF3.6+ */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#<?php echo $val['cssStyle']['captionBgColor']; ?>), color-stop(90%,#<?php echo $val['cssStyle']['captionBgColorEnd']; ?>)); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(top,  #<?php echo $val['cssStyle']['captionBgColor']; ?> 30%,#<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(top,  #<?php echo $val['cssStyle']['captionBgColor']; ?> 30%,#<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* Opera 11.10+ */
  background: -ms-linear-gradient(top,  #<?php echo $val['cssStyle']['captionBgColor']; ?> 30%,#<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* IE10+ */
  background: linear-gradient(#<?php echo $val['cssStyle']['captionBgColor']; ?> 30%, #<?php echo $val['cssStyle']['captionBgColorEnd']; ?> 90%); /* W3C */
  color: #<?php echo $val['cssStyle']['captionColor']; ?>;
  <?php if ($stbOptions['text_shadow'] == "true") {?>
  text-shadow: 1px 1px 2px #888;
  <?php } ?>
}

.stb-<?php echo $val['slug']; ?>-body_box {
  background: #<?php echo $val['cssStyle']['bgColor']; ?>; /* Old browsers */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $val['cssStyle']['bgColor']; ?>', endColorstr='#<?php echo $val['cssStyle']['bgColorEnd']; ?>',GradientType=0 ); /* IE6-9 */
  background: -moz-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%, #<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* FF3.6+ */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#<?php echo $val['cssStyle']['bgColor']; ?>), color-stop(90%,#<?php echo $val['cssStyle']['bgColorEnd']; ?>)); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%,#<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%,#<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* Opera 11.10+ */
  background: -ms-linear-gradient(top,  #<?php echo $val['cssStyle']['bgColor']; ?> 30%,#<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* IE10+ */
  background: linear-gradient(#<?php echo $val['cssStyle']['bgColor']; ?> 30%, #<?php echo $val['cssStyle']['bgColorEnd']; ?> 90%); /* W3C */
  color: #<?php echo $val['cssStyle']['color']; ?>;
  <?php if ($stbOptions['text_shadow'] == "true") {?>
  text-shadow: 1px 1px 2px #888;
  <?php } ?>
}

<?php } ?>