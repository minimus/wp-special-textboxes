<?php
/**
 * Created by PhpStorm.
 * author: minimus
 * Date: 08.02.2014
 * Time: 21:11
 */

/**
 *
 * Array 2 XML class
 * Convert an array or multi-dimentional array to XML
 *
 * @author Kevin Waterson
 * @copyright 2009 PHPRO.ORG
 *
 */
class array2xml extends DomDocument {

  public $nodeName;

  private $xpath;
  private $root;
  private $node_name;


  /*
   * Constructor, duh
   *
   * Set up the DOM environment
   *
   * @param    string    $root        The name of the root node
   * @param    string    $nod_name    The name numeric keys are called
   *
   */
  public function __construct($root='root', $node_name='node')
  {
    parent::__construct();

    /*** set the encoding ***/
    $this->encoding = "UTF-8";

    /*** format the output ***/
    $this->formatOutput = true;

    /*** set the node names ***/
    $this->node_name = $node_name;

    /*** create the root element ***/
    $this->root = $this->appendChild($this->createElement( $root ));

    $this->xpath = new DomXPath($this);
  }

  /*
  * creates the XML representation of the array
  *
  * @access    public
  * @param    array    $arr    The array to convert
  * @aparam    string    $node    The name given to child nodes when recursing
  *
  */
  public function createNode( $arr, $node = null) {
    if (is_null($node)) $node = $this->root;
    foreach($arr as $element => $value) {
      $element = is_numeric( $element ) ? $this->node_name : $element;

      $child = $this->createElement($element, (is_array($value) ? null : $value));
      $node->appendChild($child);

      if (is_array($value)) self::createNode($value, $child);
    }
  }
  /*
  * Return the generated XML as a string
  *
  * @access    public
  * @return    string
  *
  */
  public function __toString() {
    return $this->saveXML();
  }

  /*
  * array2xml::query() - perform an XPath query on the XML representation of the array
  * @param str $query - query to perform
  * @return mixed
  */
  public function query($query) {
    return $this->xpath->evaluate($query);
  }
} // end of class array2xml



if(!class_exists('StbThemes')) {
  class StbThemes{
    public $count;
    private $names;
    private $themes;
    private $dir;

    public function __construct( $dir ) {
      $this->dir = $dir;
      $this->themes = self::loadThemesData($dir);
      foreach($this->themes as $key => $value) {
        $this->names[$key] = $value['name'];
      }
      $this->count = count($this->names);
    }

    private function getOptions() {
      $options = get_option(STB_OPTIONS);
      return $options;
    }

    private function getStyles($slug, $name, $description, $author, $authorUrl) {
      global $wpdb;
      $sTable = $wpdb->prefix . "stb_styles";
      $styles = array(
        'slug' => $slug,
        'name' => $name,
        'description' => $description,
        'icon' => '',
        'author' => $author,
        'author_url' => $authorUrl
      );

      if($wpdb->get_var("SHOW TABLES LIKE '$sTable'") == $sTable) {
        $sSql = "SELECT slug, caption, js_style, css_style, stype, trash FROM $sTable WHERE trash IS FALSE;";
        $rows = $wpdb->get_results($sSql, ARRAY_A);
        foreach($rows as $value) {
          $styles['jsStyles'][$value['slug']] = unserialize($value['js_style']);
          $styles['cssStyles'][$value['slug']] = unserialize($value['css_style']);
        }
        $styles['options'] = self::getOptions();
      }
      return $styles;
    }

    private function getStyleStatus($name) {
      return ($name == 'custom') ? $name : (($name == 'grey') ? 'special' : 'system');
    }

    private function getStyleName($slug) {
      $names = array(
        'alert' => __('Alert!', STB_DOMAIN),
        'black' => __('Black Quote', STB_DOMAIN),
        'download' => __('Download', STB_DOMAIN),
        'info' => __('Info', STB_DOMAIN),
        'warning' => __('Warning!', STB_DOMAIN),
        'grey' => __('Codes', STB_DOMAIN),
        'custom' => __('Custom Style', STB_DOMAIN)
      );

      return $names[$slug];
    }

    public function themesNames() {
      return $this->names;
    }

    private function refresh($dir = null) {
      if(is_null($dir)) $rDir = $this->dir;
      else $rDir = $dir;

      $this->themes = self::loadThemesData($rDir);
      foreach($this->themes as $key => $value) {
        $this->names[$key] = $value['name'];
      }
      $this->count = count($this->names);
    }

    public function saveThemeData( $dir = null, $atts = null ) {
      if(is_null($dir)) return false;

      $opts = shortcode_atts(array(
        'slug' => 'stb_test',
        'name' => 'Test',
        'description' => 'Test STB Theme.',
        'cover' => '',
        'author' => '',
        'author_url' => ''
      ), $atts);
      $data = self::getStyles(
        $opts['slug'],
        $opts['name'],
        $opts['description'],
        $opts['author'],
        $opts['author_url']
      );
      $images = array();

      $image = $opts['cover'];
      if(!empty($image)) {
        $imgName = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_BASENAME);
        array_push($images, array(
          'old' => $image,
          'new' => $this->dir . $dir . '/' . $imgName
        ));
        $data['icon'] = $imgName;
      }

      foreach($data['jsStyles'] as $key => $value) {
        $image = $data['jsStyles'][$key]['image'];
        if(!empty($image)) {
          $imgName = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_BASENAME);
          array_push($images, array(
            'old' => $image,
            'new' => $this->dir . $dir . '/' . $imgName
          ));
          $data['jsStyles'][$key]['image'] = $imgName;
        }

        $image = $data['cssStyles'][$key]['image'];
        if(!empty($image)) {
          $imgName = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_BASENAME);
          array_push($images, array(
            'old' => $image,
            'new' => $this->dir . $dir . '/' . $imgName
          ));
          $data['cssStyles'][$key]['image'] = $imgName;
        }

        $image = $data['cssStyles'][$key]['bigImg'];
        if(!empty($image)) {
          $imgName = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_BASENAME);
          array_push($images, array(
            'old' => $image,
            'new' => $this->dir . $dir . '/' . $imgName
          ));
          $data['cssStyles'][$key]['bigImg'] = $imgName;
        }
      }

      if(!empty($data['options']['js_imgPlus'])) {
        $image = $data['options']['js_imgPlus'];
        $imgName = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_BASENAME);
        array_push($images, array(
          'old' => $image,
          'new' => $this->dir . $dir . '/' . $imgName
        ));
        $data['options']['js_imgPlus'] = $imgName;
      }

      if(!empty($data['options']['js_imgMinus'])) {
        $image = $data['options']['js_imgMinus'];
        $imgName = pathinfo(parse_url($image, PHP_URL_PATH), PATHINFO_BASENAME);
        array_push($images, array(
          'old' => $image,
          'new' => $this->dir . $dir . '/' . $imgName
        ));
        $data['options']['js_imgMinus'] = $imgName;
      }

      if(!is_dir($this->dir.$dir)) {
        if(mkdir($this->dir.$dir)) {
          $filename = $this->dir . $dir . '/theme.xml';
          $xml = new Array2XML('root');
          $xml->createNode($data);
          if($handle = fopen($filename, 'w')) {
            if(fwrite($handle, $xml) === false) return false;
            else {
              fclose($handle);
              foreach($images as $img) copy($img['old'], $img['new']);
            }
          }
          else return false;
        }
        else return false;
      }
      else return false;

      $out = self::zipThemeData($opts['slug']);
      self::refresh();

      return array('zip' => $out, 'message' => sprintf(__('Theme %s is saved ...', STB_DOMAIN), $data['name']));
    }

    private function sanitizeThemeData( $data, $dir ) {
      if(empty($data) || empty($dir)) return null;

      $tData = $data;
      switch($tData['slug']) {
        case 'stb_dark':
          $tData['name'] = __('STB Dark', STB_DOMAIN);
          $tData['description'] = __('Colored content boxes with dark captions.', STB_DOMAIN);
          break;
        case 'stb_light':
          $tData['name'] = __('STB Light', STB_DOMAIN);
          $tData['description'] = __('Light content boxes with colored captions.', STB_DOMAIN);
          break;
        case 'stb_metro':
          $tData['name'] = __('Metro', STB_DOMAIN);
          $tData['description'] = __('Flat colored content boxes, clone of Windows Metro theme.', STB_DOMAIN);
          break;
        default: break;
      }
      $tData['icon'] = (!empty($tData['icon'])) ? STB_THEMES_URL . $dir . '/' . $tData['icon'] : '';
      foreach($tData as $key => &$value) {
        if(is_array($value)) {
          foreach($value as $k => &$val) {
            if($k == 'js_imgMinus' || $k == 'js_imgPlus')
              $val = STB_THEMES_URL . $dir . '/' . $val;
            if(($key == 'cssStyles' || $key == 'jsStyles') && !empty($val['image']))
              $val['image'] = STB_THEMES_URL . $dir . '/' . $val['image'];
            if($key == 'cssStyles' && !empty($val['bigImg']))
              $val['bigImg'] = STB_THEMES_URL . $dir . '/' . $val['bigImg'];
          }
        }
      }
      return $tData;
    }

    private function getThemeData( $dir ) {
      $xml = simplexml_load_file($dir.'/theme.xml');
      $json = json_encode($xml);
      $array = json_decode($json,TRUE);

      return $array;
    }

    private function loadThemesData( $dir ) {
      $themes = array();

      if(empty($dir)) return '';

      if($dh = opendir($dir)) {
        while(false !== ($file = readdir($dh))) {
          if( $file != '.' && $file != '..' && is_dir( $dir . $file ) ) {
            $themeData = self::getThemeData($dir.$file);
            if(!empty($themeData)) {
              $themeData = self::sanitizeThemeData($themeData, $file);
              $themes[$themeData['slug']] = $themeData;
            }
          }
        }
        closedir($dh);
      }

      return $themes;
    }

    public function getThemesInfo( $dir = '' ) {
      $info = array();
      $i = 0;

      if($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
          if( $file != '.' && $file != '..' && is_dir( $dir . $file ) ) {
            $themeData = self::getThemeData($dir . $file);
            if(!empty($themeData)) {
              $info[$themeData['slug']] = array(
                'name' => $themeData['name'],
                'icon' => STB_THEMES_URL . $file . '/' . $themeData['icon'],
                'description' => $themeData['description']
              );
            }
          }
        }
        closedir($handle);
      }

      return $info;
    }

    public function zipThemeData($slug = null) {
      if(is_null($slug)) return false;

      $out = false;
      $themeDir = str_replace('_', '-', $slug);
      $dir = $this->dir . $themeDir . '/';
      if(is_dir($dir)) {
        if($handle = opendir($dir)) {
          $zip = new ZipArchive();
          $res = $zip->open($this->dir.$themeDir.'.zip', ZipArchive::CREATE);
          if($res === true) {
            while(false !== ($file = readdir($handle))) {
              if($file != '.' && $file != '..' && !is_dir($file)) $zip->addFile($dir.$file, $file);
            }
            $zip->close();
            $out = $this->dir.$themeDir.'.zip';
          }
          else $out = false;
        }
        else $out = false;
      }
      else $out = false;

      return $out;
    }

    public function themesInfo() {
      $info = array();
      $themes = $this->themes;
      foreach($themes as $key => $value) {
        $info[$key] = array(
          'name' => $value['name'],
          'icon' => $value['icon'],
          'description' => $value['description'],
          'note' => ((!empty($value['options'])) ? '<strong>'.__('Note:', STB_DOMAIN).'</strong> '.__('This theme may change your STB settings.', STB_DOMAIN) : ''),
          'author' => ((isset($value['author'])) ? $value['author'] : ''),
          'author_url' => (isset($value['author_url']) ? $value['author_url'] : '')
        );
      }

      return $info;
    }

    public function theme($theme) {
      $themes = $this->themes;
      return $themes[$theme];
    }

    public function installTheme($theme, $mode = 'update') {
      global $wpdb;

      $sTable = $wpdb->prefix . 'stb_styles';
      $options = self::getOptions();
      $data = self::theme($theme);

      $error = 0;
      $errMess = '';
      $success = 0;

      if($mode == 'update') {
        foreach($data['jsStyles'] as $key => $value) {
          $dbData = array(
            'slug' => $key,
            'js_style' => serialize($value),
            'css_style' => serialize($data['cssStyles'][$key])
          );
          $where = array('slug' => $key);
          $result = $wpdb->update($sTable, $dbData, $where, '%s', '%s');
          if(is_bool($result) && !$result) {
            $error++;
            $name = self::getStyleName($key);
            $errMess .= "<br>  <strong>{$name}</strong>: {$wpdb->last_error}";
          }
          else $success++;
        }
      }
      elseif($mode = 'install') {
        foreach($data['jsStyles'] as $key => $value) {
          $dbData = array(
            'slug' => $key,
            'caption' => self::getStyleName($key),
            'js_style' => serialize($value),
            'css_style' => serialize($data['cssStyles'][$key]),
            'stype' => self::getStyleStatus($key),
            'trash' => 0
          );
          $format = array('%s', '%s', '%s', '%s', '%s', '%d');
          $result = $wpdb->insert($sTable, $dbData, $format);
          if(is_bool($result) && !$result) {
            $error++;
            $name = $dbData['caption'];
            $errMess .= "<br>  <strong>{$name}</strong>: {$wpdb->last_error}";
          }
          else $success++;
        }
      }
      else $success = 0;

      foreach($data['options'] as $key => $value) $options[$key] = $value;
      update_option(STB_OPTIONS, $options);

      if($success == count($data['jsStyles'])) {
        $mess = sprintf( __('%s theme installed...', STB_DOMAIN), $data['name'] );
        $status = true;
      }
      else {
        $mess = __('Something went wrong...', STB_DOMAIN) . $errMess;
        $status = false;
      }

      return array('message' => $mess, 'status' => $status);
    }
  }
}
