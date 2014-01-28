<?php
if (!class_exists("SpecialTextBoxes")) {
  class SpecialTextBoxes {
    public $stbInitOptions = array(
      'rounded_corners' => 'true', 
      'text_shadow' => 'false', 
      'box_shadow' => 'false', 
      'border_style' => 'solid',
      'top_margin' => '10',
      'left_margin' => '10',
      'right_margin' => '10',
      'bottom_margin' => '10',
      'bigImg' => 'true',
      'showImg' => 'true',
      'collapsing' => 'true',
      'collapsed' => 'false',
      'fontSize' => '0',
      'captionFontSize' => '0',
      'langDirect' => 'ltr',
      'mode' => 'css',
      'js_imgMinus' => '',
      'js_imgPlus' => '',
      'js_duration' => 500,
      'js_imgX' => 5,
      'js_imgY' => 10,
      'js_radius' => 10,
      'js_caption_fontSize' => 12,
      'js_caption_fontFamily' => 'Impact, Verdana, Helvetica, Arial, sans-serif',
      'js_shadow_enabled' => 'true',
      'js_shadow_offsetX' => 7,
      'js_shadow_offsetY' => 7,
      'js_shadow_blur' => 5,
      'js_shadow_alpha' => 0.15,
      'js_shadow_color' => '000000',
      'js_textShadow_enabled' => 'false',
      'js_textShadow_offsetX' => 1,
      'js_textShadow_offsetY' => 1,
      'js_textShadow_blur' => 3,
      'js_textShadow_alpha' => 0.15,
      'js_textShadow_color' => '000000',
      'js_text_height' => 'inherit',
      'js_custom_text_height' => 0,
      'deleteOptions' => 0,
      'deleteDB' => 0,
      'css_loading' => 'dynamic',
      
      'cb_color' => '000000',
      'cb_caption_color' => 'ffffff',
      'cb_background' => 'f7cdf5',
      'cb_caption_background' => 'f844ee',
      'cb_border_color' => 'f844ee',
      'cb_image' => '',
      'cb_bigImg' => '',
      'cb_fontSize' => '0',
      'cb_captionFontSize' => '0' 
    );
    public $settings = array();
    public $styles = array();
    public $classes = array();
    public $cmsVer = '';
    private $stbVersions = array('stb' => null, 'db' => null);
    public $globalMode = '';
    
    public function __construct() {
      define('STB_VERSION', '4.4.75');
      define('STB_DB_VERSION', '1.0');
      define('STB_DIR', plugin_dir_path(__FILE__));
      define('STB_DOMAIN', 'wp-special-textboxes');
      define('STB_OPTIONS', 'SpecialTextBoxesAdminOptions');
      define('STB_URL', WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__), "", plugin_basename( __FILE__ ) ));
      
      if (function_exists( 'load_plugin_textdomain' ))
        load_plugin_textdomain( STB_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) );
      
     
      add_action('wp_head', array(&$this, 'addHeaderCSS'), 1);
      add_action('template_redirect', array(&$this, 'headerScripts'), 9999999999);
      
      add_filter( 'comment_text', 'do_shortcode' );
      
      add_shortcode('stextbox', array(&$this, 'doShortcode'));
      add_shortcode('stb', array(&$this, 'doShortcode2'));
      add_shortcode('sgreybox', array(&$this, 'doShortcodeGrey'));
      
      $this->settings = self::getAdminOptions();
      $this->styles = self::getStyles();
      $this->classes = self::getClasses($this->styles);
      $ver = $this->getWpVersion();
      if((int)$ver['major'] >= 3) {
        if((int)$ver['minor'] >= 3) $this->cmsVer = 'high';
        else $this->cmsVer = 'low';
      }
      else $this->cmsVer = 'not supported';
      $this->getVersions(true);
      $this->globalMode = self::getMode($this->settings['mode']);
      define('STB_DRAWING_MODE', $this->globalMode);
    }
    
    public function getAdminOptions($force = false) {
      $stbAdminOptions = $this->stbInitOptions;
      $stbOptions = get_option(STB_OPTIONS);
      if (!empty($stbOptions)) {
        foreach ($stbOptions as $key => $option) 
          $stbAdminOptions[$key] = $option;        
      }
      if ( $stbAdminOptions['js_imgMinus'] === '' )
        $stbAdminOptions['js_imgMinus'] = STB_URL.'images/minus.png';
      if ( $stbAdminOptions['js_imgPlus'] === '' )
        $stbAdminOptions['js_imgPlus'] = STB_URL.'images/plus.png';
      if ( $stbAdminOptions['cb_image'] === '' )
        $stbAdminOptions['cb_image'] = STB_URL.'images/heart.png';
      if ( $stbAdminOptions['cb_bigImg'] === '' )
        $stbAdminOptions['cb_bigImg'] = STB_URL.'images/heart-b.png';
      if($force) update_option(STB_OPTIONS, $stbAdminOptions);
      return $stbAdminOptions;
    }
    
    public function getStyles() {
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
    
    function getVersions($force = false) {
      $versions = array('stb' => null, 'db' => null);
      if($force) {
        $versions['stb'] = get_option( 'stb_version', '' );
        $versions['db'] = get_option( 'stb_db_version', '' );
        $this->stbVersions = $versions;
      }
      else $versions = $this->stbVersions;
      
      return $versions;
    }
    
    public function getClasses($value) {
      $classes = array();
      foreach($value as $val) {
        array_push($classes, $val['slug']);
      }
      return $classes;
    }
    
    public function getWpVersion() {
      global $wp_version;
      $version = array();
      
      $ver = explode('.', $wp_version);
      $version['major'] = $ver[0];
      $vc = count($ver);
      if($vc == 2) {
        $subver = explode('-', $ver[1]);
        $version['minor'] = $subver[0];
        $version['spec'] = (count($subver) > 1) ? $subver[1] : '';
        $version['str'] = $version['major'].'.'.$version['minor'].((!empty($version['spec'])) ? ' ('.$version['spec'].')' : '');
      }
      else {
        $version['minor'] = $ver[1];
        $version['build'] = $ver[2];
        $version['str'] = $wp_version;
      }      
      
      return $version;
    }
    
    public function getMode($oMode = 'mix') {
      if($oMode == 'css') return $oMode;
      
      $validBrowsers = array(
        'Opera' => array(9,80),
        'Internet Explorer' => array(9,0),
        'Firefox' => array(3,0),
        'Safari' => array(5,1),
        'Chrome' => array(12,0)
      );
      if(!class_exists('Browser')) include_once('browser.php');
      $browser = new Browser();
      $bName = $browser->getBrowser();
      $bVersion = explode('.', $browser->getVersion());
      if(!is_null($validBrowsers[$bName]) && 
         (intval($bVersion[0]) > $validBrowsers[$bName][0] ||
         (intval($bVersion[0]) == $validBrowsers[$bName][0] && 
          intval($bVersion[1]) >= $validBrowsers[$bName][1]))) return $oMode;
      else return 'css';
    }
    
    public function addHeaderCSS() {
      if($this->globalMode != 'js') {
        if($this->settings['css_loading'] === 'dynamic')
          wp_enqueue_style('stbCSS', STB_URL.'css/wp-special-textboxes.css.php', false, STB_VERSION);
        else wp_enqueue_style('stbCSS', STB_URL.'css/wp-special-textboxes.css', false, STB_VERSION);
      }
    }
    
    public function headerScripts() {
      $jsOptions = array(
        'caption' => array(
          'text' => '',
          'fontFamily' => $this->settings['js_caption_fontFamily'],
          'fontSize' => intval($this->settings['js_caption_fontSize']),
          'collapsed' => ($this->settings['collapsed'] == 'true'),
          'collapsing' => ($this->settings['collapsing'] == 'true'),
          'imgMinus' => $this->settings['js_imgMinus'],
          'imgPlus' => $this->settings['js_imgPlus'],
          'duration' => intval($this->settings['js_duration'])
        ),
        'imgX' => intval($this->settings['js_imgX']),
        'imgY' => intval($this->settings['js_imgY']),
        'radius' => intval($this->settings['js_radius']),
        'direction' => $this->settings['langDirect'],
        'mtop' => intval($this->settings['top_margin']),
        'mright' => intval($this->settings['right_margin']),
        'mbottom' => intval($this->settings['bottom_margin']),
        'mleft' => intval($this->settings['left_margin']),
        'lineHeight' => ($this->settings['js_text_height'] === 'custom') ?
          $this->settings['js_custom_text_height'] . 'em' :
          $this->settings['js_text_height'],
        'shadow' => array(
          'enabled' => ($this->settings['js_shadow_enabled'] == 'true'),
          'offsetX' => intval($this->settings['js_shadow_offsetX']),
          'offsetY' => intval($this->settings['js_shadow_offsetY']),
          'blur' => intval($this->settings['js_shadow_blur']),
          'alpha' => floatval($this->settings['js_shadow_alpha']),
          'color' => '#'.$this->settings['js_shadow_color']
        ),
        'textShadow' => array(
          'enabled' => ($this->settings['js_textShadow_enabled'] == 'true'),
          'offsetX' => intval($this->settings['js_textShadow_offsetX']),
          'offsetY' => intval($this->settings['js_textShadow_offsetY']),
          'blur' => intval($this->settings['js_textShadow_blur']),
          'alpha' => 0.15,
          'color' => '#'.$this->settings['js_textShadow_color']
        )
      );
      
      $cssOptions = array(
        'roundedCorners' => ($this->settings['rounded_corners'] == 'true'),
        'mbottom' => intval($this->settings['bottom_margin']),
        'imgHide' => STB_URL.'images/hide.png',
        'imgShow' => STB_URL.'images/show.png',
        'strHide' => __('Hide', STB_DOMAIN),
        'strShow' => __('Show', STB_DOMAIN)
      );
      
      switch( $this->globalMode ) { 
        case 'css': 
          $options = array('mode' => $this->globalMode, 'cssOptions' => $cssOptions); 
          break;
        case 'js': 
          $options = array('mode' => $this->globalMode, 'jsOptions' => $jsOptions, 'styles' => $this->styles); 
          break;
        case 'mix': 
          $options = array('mode' => $this->globalMode, 'jsOptions' => $jsOptions, 'cssOptions' => $cssOptions, 'styles' => $this->styles); 
          break;
        default: 
          $options = array('mode' => $this->globalMode, 'jsOptions' => $jsOptions, 'cssOptions' => $cssOptions, 'styles' => $this->styles); 
          break;
      }
      
      if($this->cmsVer === 'low') {
        wp_register_script('jquery-effects-core', STB_URL.'js/jquery.effects.core.min.js', array('jquery'), '1.8.16');
        wp_register_script('jquery-effects-blind', STB_URL.'js/jquery.effects.blind.min.js', array('jquery', 'jquery-effects-core'), '1.8.16');
      }
      wp_enqueue_script('jquery');
      wp_enqueue_script('jquery-effects-core');
      wp_enqueue_script('jquery-effects-blind');
      if($this->globalMode != 'css') wp_enqueue_script('stbJS', STB_URL.'js/jquery.stb.min.js', array('jquery'), STB_VERSION);
      wp_enqueue_script('wstbLayout', STB_URL.'js/wstb.min.js', array('jquery'), STB_VERSION, true);
      if($this->cmsVer === 'high') wp_localize_script('wstbLayout', 'stbUserOptions', $options);
      else wp_localize_script('wstbLayout', 'stbUserOptions', array('l10n_print_after' => 'stbUserOptions = ' . json_encode($options) . ';'));
    }
    
    public function doShortcode( $atts, $content = null ) {
      $attributes = shortcode_atts( array(
        'id' => 'warning',
        'mode' => '',
        'level' => 0,
        'caption' => '',
        'defcaption' => '',
        'color' => '',
        'ccolor' => '',
        'bcolor' => '',
        'bgcolor' => '',
        'bgcolorto' => '',
        'cbgcolor' => '',
        'cbgcolorto' => '',
        'bwidth' => '',
        'image' => '',
        'big' => '',
        'float' => 'false',
        'align' => 'left',
        'width' => '200',
        'collapsed' => '',
        'mtop' => '',
        'mleft' => '',
        'mbottom' => '',
        'mright' => '',
        'direction' => '',
        'collapsing' => 'default',
        'shadow' => ''), 
        $atts );

      $block = new StbBlock($content, $attributes['id'], $attributes['caption'], $attributes);
      return $block->block;
    }
    
    public function doShortcode2($atts, $content = null) {
      $atts['level'] = 1;
      return $this->doShortcode($atts, $content);
    }
    
    public function doShortcodeGrey( $atts, $content = null ) {
      $atts['id'] = 'grey';
      return $this->doShortcode($atts, $content);
    }
    
    public function highlightText( $content = null, $id = 'warning', $caption = '', $atts = null ) {
      $block = new StbBlock($content, $id, $caption, $atts);
      return $block->block;
    }
  }
}

if (!class_exists('special_text') && class_exists('WP_Widget')) {
  class special_text extends WP_Widget {
    function special_text() {
      $widget_ops = array( 'classname' => 'special_text', 'description' => __('Arbitrary text or PHP in colored block.', STB_DOMAIN));
      $control_ops = array( 'width' => 350, 'height' => 450, 'id_base' => 'special_text' );
      $this->WP_Widget( 'special_text', __('Special Text', STB_DOMAIN), $widget_ops, $control_ops );
    }
    
    function getMode($val) {
      if(!empty($val)) $mode = ($val == 'mix') ? 'js' : $val;
      if('css' == STB_DRAWING_MODE) $mode = 'css';
      return $mode;
    }
    
    function getClasses($value) {
      $classes = array();
      foreach($value as $val) {
        $classes[ $val['slug']] = $val['name'];
      }
      return $classes;
    }
    
    function getStyles() {
      global $wpdb;
      $sTable = $wpdb->prefix . "stb_styles";
      $styles = array();
      
      if($wpdb->get_var("SHOW TABLES LIKE '$sTable'") == $sTable) {
        $sSql = "SELECT slug, caption FROM $sTable WHERE trash IS FALSE;";
        $rows = $wpdb->get_results($sSql, ARRAY_A);
        $style = array();
        foreach($rows as $value) {
          $style['slug'] = $value['slug'];
          $style['name'] = $value['caption'];
          array_push($styles, $style);
        }
      }
      return self::getClasses( $styles );
    }
    
    function widget( $args, $instance ) {
      extract($args);
      $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
      $box_id = empty($instance['box_id']) ? 'warning' : $instance['box_id'];
      $parse = $instance['parse'];
      $text = $instance['text'];
      $showAll = $instance['show_all'];
      $canShow = (((is_home() || is_front_page()) && $instance['show_home']) || 
            (is_category() && $instance['show_cat']) ||
            (is_archive() && $instance['show_arc']) ||
            (is_single() && $instance['show_single']) ||
            (is_tag() && $instance['show_tag']) ||
            (is_author() && $instance['show_author']));
      
      if($box_id !== 'none') {
        $mode = self::getMode(STB_DRAWING_MODE);
        if($mode == 'css') {
          $before_title = '<div class="stb-'.$box_id.'-caption_box" style="margin-left: 0px; margin-right: 0px" >';
          $after_title = '</div>'.( !empty($title) ? '<div class="stb-'.$box_id.'-body_box" style="margin-left: 0px; margin-right: 0px" >' : '' );
          $before_widget = '<div class="stb-container">'.( empty($title) ? '<div class="stb-'.$box_id.'_box" style="margin-left: 0px; margin-right: 0px" >' : '' );
          $after_widget = '</div></div>';
        }
        else {
          $before_title = '';
          $after_title = '';
          $before_widget = "<div class='stb-$box_id-box stb-level-0' data-stb='{mleft: 0, mright: 0, caption: {text: \"$title\"}}'>";
          $after_widget = '</div>';
        }
      }
      
      if ( $showAll || $canShow ) {
        echo $before_widget;
        if ( !empty( $title ) && $mode == 'css' ) echo $before_title . $title . $after_title;
        echo ($parse ? eval("?>".$text."<?") : $text);
        echo $after_widget;
      }
    }
    
    function update( $new_instance, $old_instance ) {
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['box_id'] = $new_instance['box_id'];
      $instance['parse'] = isset($new_instance['parse']);
      $instance['text'] = $new_instance['text'];
      $instance['show_all'] = isset($new_instance['show_all']);
      $instance['show_home'] = isset($new_instance['show_home']);
      $instance['show_single'] = isset($new_instance['show_single']);
      $instance['show_arc'] = isset($new_instance['show_arc']);
      $instance['show_cat'] = isset($new_instance['show_cat']);
      $instance['show_tag'] = isset($new_instance['show_tag']);
      $instance['show_author'] = isset($new_instance['show_author']);
      return $instance;
    }
    
    function form( $instance ) {
      $ids = self::getStyles();      
      $instance = wp_parse_args((array) $instance, 
        array(
          'title'       => '', 
          'box_id'      => 'warning', 
          'parse'       => false, 
          'text'        => '', 
          'show_all'    => true, 
          'show_home'   => false, 
          'show_cat'    => false, 
          'show_arc'    => false, 
          'show_single' => false,
          'show_tag'    => false,
          'show_author' => false
        )
      );
      $title = strip_tags($instance['title']);
      $box_id = $instance['box_id'];
      $parse = $instance['parse'];
      $text = format_to_edit($instance['text']);
      $show_all = $instance['show_all'];
      $show_home = $instance['show_home'];
      $show_single = $instance['show_single'];
      $show_arc = $instance['show_arc'];
      $show_cat = $instance['show_cat'];
      $show_tag = $instance['show_tag'];
      $show_author = $instance['show_author'];
      ?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', STB_DOMAIN); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

    <textarea class="widefat" rows="10" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea><br />&nbsp;

    <p><label for="<?php echo $this->get_field_id('box_id'); ?>"><?php _e('ID of Box:', STB_DOMAIN) ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id('box_id'); ?>" name="<?php echo $this->get_field_name('box_id'); ?>" >
    <?php 
    foreach ($ids as $key => $option) echo '<option value='.$key.(($instance['box_id'] === $key) ? ' selected' : '' ).'>'.$option.'</option>';?> 
    </select></p>
    
    <p><input id="<?php echo $this->get_field_id('parse'); ?>" name="<?php echo $this->get_field_name('parse'); ?>" type="checkbox" <?php checked($instance['parse']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('parse'); ?>"><?php _e('Evaluate as PHP code.', STB_DOMAIN); ?></label></p>
    
    <p><input id="<?php echo $this->get_field_id('show_all'); ?>" name="<?php echo $this->get_field_name('show_all'); ?>" type="checkbox" <?php checked($instance['show_all']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_all'); ?>"><?php _e('Show on all pages of blog', STB_DOMAIN); ?></label></p>
    
    <p><?php _e('Show only on', STB_DOMAIN) ?>:<br />
    <input id="<?php echo $this->get_field_id('show_home'); ?>" name="<?php echo $this->get_field_name('show_home'); ?>" type="checkbox" <?php checked($instance['show_home']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_home'); ?>"><?php _e('Home Page', STB_DOMAIN); ?></label><br />
    <input id="<?php echo $this->get_field_id('show_single'); ?>" name="<?php echo $this->get_field_name('show_single'); ?>" type="checkbox" <?php checked($instance['show_single']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_single'); ?>"><?php _e('Single Post Pages', STB_DOMAIN); ?></label><br />
    <input id="<?php echo $this->get_field_id('show_arc'); ?>" name="<?php echo $this->get_field_name('show_arc'); ?>" type="checkbox" <?php checked($instance['show_arc']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_arc'); ?>"><?php _e('Archive Pages', STB_DOMAIN); ?></label><br />
    <input id="<?php echo $this->get_field_id('show_cat'); ?>" name="<?php echo $this->get_field_name('show_cat'); ?>" type="checkbox" <?php checked($instance['show_cat']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_cat'); ?>"><?php _e('Category Archive Pages', STB_DOMAIN); ?></label><br />
    <input id="<?php echo $this->get_field_id('show_tag'); ?>" name="<?php echo $this->get_field_name('show_tag'); ?>" type="checkbox" <?php checked($instance['show_tag']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_tag'); ?>"><?php _e('Tag Archive Pages', STB_DOMAIN); ?></label><br />
    <input id="<?php echo $this->get_field_id('show_author'); ?>" name="<?php echo $this->get_field_name('show_author'); ?>" type="checkbox" <?php checked($instance['show_author']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Author Archive Pages', STB_DOMAIN); ?></label><br /></p>
<?php
    }
  } // End of class special_text
} // End of if
?>
