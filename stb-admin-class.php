<?php
include_once('stb-class.php');
if(!class_exists('SpecialTextBoxesAdmin') && class_exists('SpecialTextBoxes')) {
  class SpecialTextBoxesAdmin extends SpecialTextBoxes {    
    public function __construct() {
      parent::__construct();
      
      add_action('activate_wp-special-textboxes/wp-special-textboxes.php',  array(&$this, 'onActivate'));
      add_action('deactivate_wp-special-textboxes/wp-special-textboxes.php', array(&$this, 'onDeactivate'));
      add_action('admin_init', array(&$this, 'initSettings'));
      add_action('admin_menu', array(&$this, 'regAdminPage'));
      add_filter('tiny_mce_version', array(&$this, 'tinyMCEVersion'));
      add_action('init', array(&$this, 'addButtons'));
      
      $this->updateDB();
      if(!file_exists(STB_DIR.'css/wp-special-textboxes.css')) self::writeCSS('file');
    }
    
    public function updateDB() {
      global $wpdb, $charset_collate;
      $sTable = $wpdb->prefix . "stb_styles";
      
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      if($wpdb->get_var("SHOW TABLES LIKE '$sTable'") != $sTable) {
        $sSql = "CREATE TABLE $sTable (
                    slug VARCHAR(255) NOT NULL,
                    caption VARCHAR(255) NOT NULL,
                    js_style TEXT DEFAULT NULL,
                    css_style TEXT DEFAULT NULL,
                    stype VARCHAR(8) DEFAULT NULL,
                    trash TINYINT(1) DEFAULT 0,
                    PRIMARY KEY (slug)
                   ) $charset_collate;";
        dbDelta($sSql);
        
        $names = array(
          'alert' => __('Alert!', STB_DOMAIN),
          'black' => __('Black Quote', STB_DOMAIN),
          'download' => __('Download', STB_DOMAIN),
          'info' => __('Info', STB_DOMAIN),
          'warning' => __('Warning!', STB_DOMAIN),
          'grey' => __('Codes', STB_DOMAIN),
          'custom' => __('Custom Style', STB_DOMAIN)
        );
        $defJsStyles = array(
          'alert' => array(
            'image' => STB_URL.'images/alert-b.png',      
            'color' => '#fdcbc9',
            'colorTo' => '#fb7d78',
            'fontColor' => '#000000',
            'border' => array(
              'width' => 0,
              'color' => '#f9574f'
            ),
            'caption' => array(
              'fontColor' => '#ffffff',
              'color' => '#1d1a1a',
              'colorTo' => '#504848'
            )
          ),
          'black' => array(
            'image' => STB_URL.'images/earth-b.png',
            'color' => '#3b3b3b',
            'colorTo' => '#000000',
            'fontColor' => '#ffffff',
            'border' => array(
              'width' => 0,
              'color' => '#535353'
            ),
            'caption' => array(
              'fontColor' => '#ffffff',
              'color' => '#1d1a1a',
              'colorTo' => '#504848'
            )
          ),    
          'download' => array(
            'image' => STB_URL.'images/download-b.png',
            'color' => '#78c0f7',
            'colorTo' => '#2e7cb9',
            'fontColor' => '#000000',
            'border' => array(
              'width' => 0,
              'color' => '#15609a'
            ),
            'caption' => array(
              'fontColor' => '#ffffff',
              'color' => '#1d1a1a',
              'colorTo' => '#504848'
            )
          ),
          'info' => array(
            'image' => STB_URL.'images/info-b.png',
            'color' => '#a1ea94',
            'colorTo' => '#79b06e',
            'fontColor' => '#000000',
            'border' => array(
              'width' => 0,
              'color' => '#6c9c62'
            ),
            'caption' => array(
              'fontColor' => '#ffffff',
              'color' => '#1d1a1a',
              'colorTo' => '#504848'
            )
          ),
          'warning' => array(
            'image' => STB_URL.'images/warning-2-b.png',
            'color' => '#f8fc91', 
            'colorTo' => '#f0d208',
            'fontColor' => '#000000',
            'border' => array(
              'width' => 0,
              'color' => '#d9be08'
            ),
            'caption' => array(
              'fontColor' => '#ffffff',
              'color' => '#1d1a1a',
              'colorTo' => '#504848'
            ) 
          ),
          'grey' => array(
            'image' => STB_URL.'images/gears-b.png',
            'color' => '#e3e3e3', 
            'colorTo' => '#ababab',
            'fontColor' => '#000000',
            'border' => array(
              'width' => 0,
              'color' => '#6e6e6e'
            ),
            'caption' => array(
              'fontColor' => '#ffffff',
              'color' => '#b5b5b5',
              'colorTo' => '#6e6e6e'
            ) 
          ), 
          'custom' => array(
            'image' => STB_URL.'images/heart-b.png',
            'color' => '#f7cdf5',
            'colorTo' => '#f77df1',
            'fontColor' => '#000000',
            'border' => array(
              'width' => 0,
              'color' => '#f844ee'
            ),
            'caption' => array(
              'fontColor' => '#ffffff',
              'color' => '#1d1a1a',
              'colorTo' => '#504848'
            )
          )
        );
        
        $defCssStyles = array(
          'alert' => array(
            'color' => '000000',
            'captionColor' => 'FFFFFF',
            'borderColor' => 'FF4F4A',
            'bgColor' => 'FFE7E6',
            'captionBgColor' => 'FF4F4A',
            'image' => STB_URL.'images/alert.png',
            'bigImg' => STB_URL.'images/alert-b.png'
          ),
          'black' => array(
            'color' => 'FFFFFF',
            'captionColor' => 'FFFFFF',
            'borderColor' => '6E6E6E',
            'bgColor' => '000000',
            'captionBgColor' => '333333',
            'image' => STB_URL.'images/earth.png',
            'bigImg' => STB_URL.'images/earth-b.png'
          ),    
          'download' => array(
            'color' => '000000',
            'captionColor' => 'FFFFFF',
            'borderColor' => '65ADFE',
            'bgColor' => 'DFF0FF',
            'captionBgColor' => '65ADFE',
            'image' => STB_URL.'images/download.png',
            'bigImg' => STB_URL.'images/download-b.png'
          ),
          'info' => array(
            'color' => '000000',
            'captionColor' => 'FFFFFF',
            'borderColor' => '7AD975',
            'bgColor' => 'E2F8DE',
            'captionBgColor' => '7AD975',
            'image' => STB_URL.'images/info.png',
            'bigImg' => STB_URL.'images/info-b.png'
          ),
          'warning' => array(
            'color' => '000000',
            'captionColor' => 'FFFFFF',
            'borderColor' => 'FE9A05',
            'bgColor' => 'FEFFD5',
            'captionBgColor' => 'FE9A05',
            'image' => STB_URL.'images/warning.png',
            'bigImg' => STB_URL.'images/warning-b.png' 
          ),
          'grey' => array(
            'color' => '000000',
            'captionColor' => 'FFFFFF',
            'borderColor' => 'BBBBBB',
            'bgColor' => 'EEEEEE',
            'captionBgColor' => 'BBBBBB',
            'image' => STB_URL.'images/gears.png',
            'bigImg' => STB_URL.'images/gears-b.png' 
          ),
          'custom' => array(
            'color' => $this->settings['cb_color'],
            'captionColor' => $this->settings['cb_caption_color'],
            'borderColor' => $this->settings['cb_border_color'],
            'bgColor' => $this->settings['cb_background'],
            'captionBgColor' => $this->settings['cb_caption_background'],
            'image' => $this->settings['cb_image'],
            'bigImg' => $this->settings['cb_bigImg']
          )
        );
        
        $uSql = '';
        foreach($defJsStyles as $key => $value) {
          $jsVal = serialize($value);
          $cssVal = serialize($defCssStyles[$key]);
          $cap = $names[$key];
          $stype = ($key == 'custom') ? $key : (($key == 'grey') ? 'special' : 'system');
          $wpdb->query(
            $wpdb->prepare("
              INSERT INTO $sTable 
              (slug, caption, js_style, css_style, stype, trash) 
              VALUES(%s, %s, %s, %s, %s, %d);",
            $key, 
            $cap, 
            $jsVal, 
            $cssVal, 
            $stype, 
            0)
          );
        }
        update_option('stb_db_version', STB_DB_VERSION);
      }
      update_option('stb_version', STB_VERSION);
    }
    
    public function onActivate() {
      $stbAdminOptions = $this->getAdminOptions();
      update_option(STB_OPTIONS, $stbAdminOptions);
    }
    
    public function onDeactivate() {
      global $wpdb;
      $sTable = $wpdb->prefix . "stb_styles";
      
      if($this->settings['deleteOptions'] == 1) delete_option(STB_OPTIONS);
      if($this->settings['deleteDB'] == 1) $wpdb->query("DROP TABLE IF EXISTS $sTable;");
    }
    
    public function addAdminHeaderCSS() {
      wp_enqueue_style('stbAdminCSS', STB_URL.'css/stb-admin.css', false, STB_VERSION);
      wp_enqueue_style('stbCSS', STB_URL.'css/wp-special-textboxes.css.php', false, STB_VERSION);
      //wp_enqueue_style('ColorPickerCSS', STB_URL.'css/colorpicker.css');
      wp_enqueue_style('jquery-ui-tabs', STB_URL.'css/jquery-ui-wp38.css', false, '1.10.3');
      wp_enqueue_style('smallColorPickerButtonsCSS', STB_URL.'css/color-buttons.min.css');
      wp_enqueue_style('smallColorPickerCSS', STB_URL.'css/small-color-picker.min.css');
    }
    
    public function addAdminEditorCSS() {
      wp_enqueue_style('stbAdminCSS', STB_URL.'css/stb-edit.css', false, STB_VERSION);
      wp_enqueue_style('stbCoreCSS', STB_URL.'css/stb-core.css', false, STB_VERSION);
      wp_enqueue_style('stbCSS', STB_URL.'css/wp-special-textboxes.css.php', false, STB_VERSION);
      //wp_enqueue_style('ColorPickerCSS', STB_URL.'css/colorpicker.css');
      wp_enqueue_style('smallColorPickerButtonsCSS', STB_URL.'css/color-buttons.min.css');
      wp_enqueue_style('smallColorPickerCSS', STB_URL.'css/small-color-picker.min.css');
    }
    
    public function adminHeaderScripts() {
      $options = array(
        'texts' => array(
          'ok' => __('OK', STB_DOMAIN),
          'cancel' => __('Cancel', STB_DOMAIN),
          'switchModeToNum' => __('Show numbers', STB_DOMAIN),
          'switchModeToCol' => __('Show color wheel', STB_DOMAIN)
        )
      );

      if($this->cmsVer === 'low') {
        wp_register_script('jquery-effects-core', STB_URL.'js/jquery.effects.core.min.js', array('jquery'), '1.8.16');
        wp_register_script('jquery-effects-blind', STB_URL.'js/jquery.effects.blind.min.js', array('jquery', 'jquery-effects-core'), '1.8.16');
      }
      wp_enqueue_script('jquery');      
      wp_enqueue_script('jquery-ui-core');
      wp_enqueue_script('jquery-ui-tabs');
      wp_enqueue_script('jquery-effects-core');
      wp_enqueue_script('jquery-effects-blind');
      //wp_enqueue_script('ColorPicker', STB_URL.'js/colorpicker.js');
      wp_enqueue_script('smallColorPicker', STB_URL.'js/small-color-picker.min.js', array('jquery'));
      wp_enqueue_script('wstbAdminLayout', STB_URL.'js/wstb.admin.min.js', array('jquery'), STB_VERSION);

      if($this->cmsVer === 'high') wp_localize_script('wstbAdminLayout', 'stbUserOptions', $options);
      else wp_localize_script('wstbAdminLayout', 'stbUserOptions', array('l10n_print_after' => 'stbUserOptions = ' . json_encode($options) . ';'));
    }
    
    public function adminEditorScripts() {
      $jsOptions = array(
        'caption' => array(
          'text' => '',
          'fontFamily' => $this->settings['js_caption_fontFamily'],
          'fontSize' => intval($this->settings['js_caption_fontSize']),
          'collapsed' => ($this->settings['collapsed'] == 'true'),
          'collapsing' => ($this->settings['collapsing'] == 'true'),
          'imgMinus' => $this->settings['js_imgMinus'],
          'imgPlus' => $this->settings['js_imgPlus'],
          'duration' => intval($this->settings['js_duration']),
          'side' => ((isset($this->settings['side'])) ? $this->settings['side'] : false)
        ),
        'imgX' => intval($this->settings['js_imgX']),
        'imgY' => intval($this->settings['js_imgY']),
        'radius' => intval($this->settings['js_radius']),
        'direction' => $this->settings['langDirect'],
        'mtop' => intval($this->settings['top_margin']),
        'mright' => intval($this->settings['right_margin']),
        'mbottom' => intval($this->settings['bottom_margin']),
        'mleft' => intval($this->settings['left_margin']),
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
        ),
        'pickerOptions' => array(
          'texts' => array(
            'ok' => __('OK', STB_DOMAIN),
            'cancel' => __('Cancel', STB_DOMAIN),
            'switchModeToNum' => __('Show numbers', STB_DOMAIN),
            'switchModeToCol' => __('Show color wheel', STB_DOMAIN)
          )
        )
      );
      
      $cssOptions = array(
        'roundedCorners' => ($this->settings['rounded_corners'] == 'true'),
        'mbottom' => intval($this->settings['bottom_margin']),
        'imgHide' => STB_URL.'images/minus.png',
        'imgShow' => STB_URL.'images/plus.png',
        'strHide' => __('Hide', STB_DOMAIN),
        'strShow' => __('Show', STB_DOMAIN)
      );
      
      $options = array(
        'jsOptions' => $jsOptions,
        'cssOptions' => $cssOptions,
        'strings' => array('title' => __('Select Image', STB_DOMAIN), 'update' => __('Select', STB_DOMAIN))
      );

      wp_enqueue_media();
      if($this->cmsVer === 'low') {
        wp_register_script('jquery-effects-core', STB_URL.'js/jquery.effects.core.min.js', array('jquery'), '1.8.16');
        wp_register_script('jquery-effects-blind', STB_URL.'js/jquery.effects.blind.min.js', array('jquery', 'jquery-effects-core'), '1.8.16');
      }
      wp_enqueue_script('jquery');
      wp_enqueue_script('jquery-effects-core');
      wp_enqueue_script('jquery-effects-blind');
      //wp_enqueue_script('ColorPicker', STB_URL.'js/colorpicker.js');
      wp_enqueue_script('smallColorPicker', STB_URL.'js/small-color-picker.min.js', array('jquery'));
      wp_enqueue_script('STB', STB_URL.'js/jquery.stb.js', array('jquery', 'jquery-effects-core', 'jquery-effects-blind'), STB_VERSION);
      wp_enqueue_script('wstbAdminLayout', STB_URL.'js/wstb.edit.js', array('jquery', 'jquery-effects-core', 'jquery-effects-blind', 'STB'), STB_VERSION);
      if($this->cmsVer === 'high') wp_localize_script('wstbAdminLayout', 'stbUserOptions', $options);
      else wp_localize_script('wstbAdminLayout', 'stbUserOptions', array('l10n_print_after' => 'stbUserOptions = ' . json_encode($options) . ';'));
    }
    
    private function getSamples($slug = 'custom', $theme = 'Custom') {
      $stbOptions = $this->getAdminOptions();
      $sampleBox = "<div class='stb-".$slug."_box' >".__("This is example of Custom Special Text Box. You must save options to view changes.", STB_DOMAIN).'</div>';
      $sampleCaptionedBox = "<div id='stb-container' class='stb-container'><div id='caption' class='stb-".$slug."-caption_box' >".__("This is caption", STB_DOMAIN);
      $sampleCaptionedBox .= "<div id='stb-tool' class='stb-tool' style='float:".(($stbOptions['langDirect'] === 'ltr')?'right':'left')."; padding:0px; margin:0px auto'><img id='stb-toolimg' style='border: none; background-color: transparent;' src='".WP_PLUGIN_URL.(($stbOptions['collapsed'] === 'true') ? "/wp-special-textboxes/images/show.png' title='".__('Show', STB_DOMAIN) : "/wp-special-textboxes/images/hide.png' title='".__('Hide', STB_DOMAIN))."' /></div></div>";
      $sampleCaptionedBox .= "<div id='body' class='stb-".$slug."-body_box' >".__("This is example of Captioned Custom Special Text Box. You must save options to view changes.", STB_DOMAIN)."</div></div>";
      return $sampleBox.$sampleCaptionedBox;
    }
    
    private function getSamples2($slug = 'custom', $theme = 'Custom') {
      $ccontent = sprintf(__('This is example of Captioned %s Special Text Box. You must save style parameters to view changes.', STB_DOMAIN), $theme)."<br/><br/>
                  Lacus massa. Volutpat lacus irure sem malesuada. Nullam eu amet tincidunt, turpis est vestibulum. Elit ipsum justo, in mattis. Ultricies lacus tristique molestie eu, metus iure, et in, mattis sem.";
      $content = sprintf(__('This is example of %s Special Text Box. You must save style parameters to view changes.', STB_DOMAIN), $theme)."<br/><br/>
                  Lacus massa. Volutpat lacus irure sem malesuada. Nullam eu amet tincidunt, turpis est vestibulum. Elit ipsum justo, in mattis. Ultricies lacus tristique molestie eu, metus iure, et in, mattis sem.";
      $atts = array('mode' => 'css');
      
      $cblock = new StbBlock($ccontent, $slug, $theme, $atts);
      $block = new StbBlock($content, $slug, '', $atts);
      return $cblock->block.$block->block;
    }
    
    public function regAdminPage() {
      if (function_exists('add_options_page')) {
        $menu_page = add_menu_page(__('Special Text Boxes', STB_DOMAIN), __('Special Text Boxes', STB_DOMAIN), 'manage_options', 'stb-settings', array(&$this, 'stbAdminPage'), STB_URL.'images/stb-icon.png');
        $plugin_page = add_submenu_page('stb-settings', __('STB Settings', STB_DOMAIN), __('Settings', STB_DOMAIN), 'manage_options', 'stb-settings', array(&$this, 'stbAdminPage'));
        add_action('admin_print_styles-'.$plugin_page, array(&$this, 'addAdminHeaderCSS'));
        add_action('admin_print_scripts-'.$plugin_page, array(&$this, 'adminHeaderScripts'));
        $styles_page = add_submenu_page('stb-settings', __('STB Styles', STB_DOMAIN), __('Styles', STB_DOMAIN), 'manage_options', 'stb-styles', array(&$this, 'stbStylesPage'));
        $editor_page = add_submenu_page('stb-settings', __('STB Style Editor', STB_DOMAIN), __('New Style', STB_DOMAIN), 'manage_options', 'stb-editor', array(&$this, 'stbEditorPage'));
        add_action('admin_print_styles-'.$editor_page, array(&$this, 'addAdminEditorCSS'));
        add_action('admin_print_scripts-'.$editor_page, array(&$this, 'adminEditorScripts'));
      }
    }
    
    public function initSettings() {      
      $modeHelp = '<ul>';
      $modeHelp .= '<li><strong><em>'.__('CSS Mode', STB_DOMAIN).'</em></strong>: '.__('In any cases STB blocks will be drawn using predefined style sheets.', STB_DOMAIN).'</li>';
      $modeHelp .= '<li><strong><em>'.__('Javascript Mode', STB_DOMAIN).'</em></strong>: '.__('If user browser supports tags "canvas" (all modern browsers, including Internet Explorer, support this tag) STB block will be drawn using Javascript, in any other cases this one will be drawn using CSS mode.', STB_DOMAIN).'</li>';
      $modeHelp .= '<li><strong><em>'.__('Mixed Mode', STB_DOMAIN).'</em></strong>: '.__('You can use both CSS and Javascript methods of drawing text blocks on one page. Just define drawing mode of STB shortcode. Default is Javascript or CSS method.', STB_DOMAIN).'</li>';
      $modeHelp .= '</ul>';
      
      add_settings_section('modeSection', __('Drawing Mode Settings', STB_DOMAIN), array(&$this, 'drawModeSection'), 'stb-settings');
      add_settings_section('basicSection', __('Basic Settings', STB_DOMAIN), array(&$this, 'drawBasicSection'), 'stb-settings');
      add_settings_section('extendedSection', __('Extended Settings', STB_DOMAIN), array(&$this, 'drawExtendedSection'), 'stb-settings');
      add_settings_section('deactivationSection', __('Deactivation Settings', STB_DOMAIN), array(&$this, 'drawDeactivationSection'), 'stb-settings');
      add_settings_section('jsSection', __('Basic Settings', STB_DOMAIN), array(&$this, 'drawJsSection'), 'stb-settings');
      add_settings_section('jsShadowSection', __('Box Shadow Settings', STB_DOMAIN), array(&$this, 'drawJsShadowSection'), 'stb-settings');
      add_settings_section('jsTextShadowSection', __('Text Shadow Settings', STB_DOMAIN), array(&$this, 'drawJsTextShadowSection'), 'stb-settings');
      add_settings_section('cssSection', __('Basic Settings', STB_DOMAIN), array(&$this, 'drawCssSection'), 'stb-settings');
      add_settings_section('cssXSection', __('Extended Settings', STB_DOMAIN), array(&$this, 'drawCssXSection'), 'stb-settings');
      add_settings_section('cssSysSection', __('System Settings', STB_DOMAIN), array(&$this, 'drawSysSection'), 'stb-settings');
      
      add_settings_field('mode', __('Define Drawing Mode', STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'modeSection', array('description' => __('Select Drawing Mode', STB_DOMAIN).':'.$modeHelp, 'options' => array('css' => __('CSS Mode', STB_DOMAIN), 'js' => __('Javascript Mode', STB_DOMAIN), 'mix' => __('Mixed Mode', STB_DOMAIN).' (Javascript)', 'mix2' => __('Mixed Mode', STB_DOMAIN).' (CSS)')));
      
      add_settings_field('top_margin', __("Define top margin for Special Text Boxes", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'basicSection', array('description' => __("This is a gap between top edge of Special Text Box and text above.", STB_DOMAIN)));
      add_settings_field('left_margin', __("Define left margin for Special Text Boxes", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'basicSection', array('description' => __("This is a gap between left edge of Special Text Box and left edge of post area.", STB_DOMAIN)));
      add_settings_field('right_margin', __("Define right margin for Special Text Boxes", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'basicSection', array('description' => __("This is a gap between right edge of Special Text Box and right edge of post area.", STB_DOMAIN)));
      add_settings_field('bottom_margin', __("Define bottom margin for Special Text Boxes", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'basicSection', array('description' => __("This is a gap between bottom edge of Special Text Box and text below.", STB_DOMAIN)));
      
      add_settings_field('langDirect', __('Define language direction', STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'extendedSection', array('description' => __('Selecting "Left-to-Right" will set Left-to-Right language direction for Special Text Boxes and visa versa.', STB_DOMAIN), 'options' => array( 'ltr' => __('Left-to-Right', STB_DOMAIN), 'rtl' => __('Right-to-Left', STB_DOMAIN))));
      add_settings_field('collapsing', __('Allow collapsing/expanding captioned Special Text Boxes?', STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'extendedSection', array('description' => __('Selecting "Yes" will allow displaying show/hide button in captioned Special Text Boxes.', STB_DOMAIN), 'options' => array( 'true' => __('Yes', STB_DOMAIN), 'false' => __('No', STB_DOMAIN))));
      add_settings_field('collapsed', __('Allow "collapsed on load" captioned Special Text Boxes?', STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'extendedSection', array('description' => __('Selecting "Yes" will allow displaying collapsed captioned Special Text Boxes after page loading.', STB_DOMAIN), 'options' => array( 'true' => __('Yes', STB_DOMAIN), 'false' => __('No', STB_DOMAIN))));
      add_settings_field('side', __('Allow caption background colors for side image background (boxes without caption only)', STB_DOMAIN), array(&$this, 'drawCheckboxOption'), 'stb-settings', 'extendedSection', array('label_for' => 'side', 'checkbox' => true));
      
      add_settings_field('deleteOptions', __("Delete plugin options during deactivation of plugin", STB_DOMAIN), array(&$this, 'drawCheckboxOption'), 'stb-settings', 'deactivationSection', array('label_for' => 'deleteOptions', 'checkbox' => true));
      add_settings_field('deleteDB', __("Delete database table of plugin during deactivation of plugin", STB_DOMAIN), array(&$this, 'drawCheckboxOption'), 'stb-settings', 'deactivationSection', array('label_for' => 'deleteDB', 'checkbox' => true));
      
      add_settings_field('js_imgMinus', __('Define Hide Tool Image', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsSection', array('description' => __("This image is displayed in the text block header and shows the status of the non collapsed text block.", STB_DOMAIN), 'width' => '100'));
      add_settings_field('js_imgPlus', __('Define Show Tool Image', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsSection', array('description' => __("This image is displayed in the text block header and shows the status of the collapsed text block.", STB_DOMAIN), 'width' => '100'));
      add_settings_field('js_duration', __('Define Duration of Collapsing/Expanding Animation', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsSection', array('description' => __("This is time of collapsing/expanding of the text block in milliseconds.", STB_DOMAIN)));
      add_settings_field('js_imgX', __('Define Image Offset X', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsSection', array('description' => __("This is image offset by X coordinate for non-caption text block.", STB_DOMAIN)));
      add_settings_field('js_imgY', __('Define Image Offset Y', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsSection', array('description' => __("This is image offset by Y coordinate for non-caption text block.", STB_DOMAIN)));
      add_settings_field('js_radius', __('Define Corners Radius', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsSection', array('description' => __("This is corners radius in pixels.", STB_DOMAIN)));
      add_settings_field('js_caption_fontSize', __('Define Caption Font Size', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsSection', array('description' => __("This is font size of caption text in pixels.", STB_DOMAIN)));
      add_settings_field('js_caption_fontFamily', __('Define Caption Font Family', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsSection', array('description' => __("This is font family for caption text.", STB_DOMAIN), 'width' => 100));
      add_settings_field('js_text_height', __('Select Text Line Height', STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'jsSection', array('description' => __("Inherit - Defined by theme style sheet.", STB_DOMAIN)."<br />".__("Normal - Defined by visitor's browser.", STB_DOMAIN)."<br />".__("Custom - Defined by You. You can define custom value for text line height using parameter below.", STB_DOMAIN), 'options' => array('inherit' => __('Inherit', STB_DOMAIN), 'normal' => __('Normal', STB_DOMAIN), 'custom' => __('Custom', STB_DOMAIN))));
      add_settings_field('js_custom_text_height', __('Define Custom Text Line Height', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsSection', array('description' => __('This is custom text line height of STB block in "em" defined by You.', STB_DOMAIN), 'suffix' => 'em'));
      
      add_settings_field('js_shadow_enabled', __('Enable Box Shadow', STB_DOMAIN),  array(&$this, 'drawRadioOption'), 'stb-settings', 'jsShadowSection', array('description' => __('Selecting "Yes" will allow drawing shadow of Special Text Boxes.', STB_DOMAIN), 'options' => array( 'true' => __("Yes", STB_DOMAIN), 'false' => __("No", STB_DOMAIN))));
      add_settings_field('js_shadow_offsetX', __('Define Shadow Offset X', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsShadowSection', array('description' => __("This is box shadow offset by X coordinate for text block in pixels.", STB_DOMAIN)));
      add_settings_field('js_shadow_offsetY', __('Define Shadow Offset Y', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsShadowSection', array('description' => __("This is box shadow offset by Y coordinate for text block in pixels.", STB_DOMAIN)));
      add_settings_field('js_shadow_blur', __('Define Shadow Blur', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsShadowSection', array('description' => __("This is box shadow blur for text block in pixels.", STB_DOMAIN)));
      add_settings_field('js_shadow_alpha', __('Define Shadow Alpha', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsShadowSection', array('description' => __("This is box shadow alpha chanel value for text block.", STB_DOMAIN)));
      add_settings_field('js_shadow_color', __('Define Shadow Color', STB_DOMAIN), array(&$this, 'drawColorButton'), 'stb-settings', 'jsShadowSection', array('description' => __("This is box shadow color for text block.", STB_DOMAIN)));
      
      add_settings_field('js_textShadow_enabled', __('Enable Text Shadow', STB_DOMAIN),  array(&$this, 'drawRadioOption'), 'stb-settings', 'jsTextShadowSection', array('description' => __('Selecting "Yes" will allow drawing text shadow of Special Text Boxes.', STB_DOMAIN), 'options' => array( 'true' => __("Yes", STB_DOMAIN), 'false' => __("No", STB_DOMAIN))));
      add_settings_field('js_textShadow_offsetX', __('Define Shadow Offset X', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsTextShadowSection', array('description' => __("This is text shadow offset by X coordinate for text block in pixels.", STB_DOMAIN)));
      add_settings_field('js_textShadow_offsetY', __('Define Shadow Offset Y', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsTextShadowSection', array('description' => __("This is text shadow offset by Y coordinate for text block in pixels.", STB_DOMAIN)));
      add_settings_field('js_textShadow_blur', __('Define Shadow Blur', STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'jsTextShadowSection', array('description' => __("This is text shadow blur for text block in pixels.", STB_DOMAIN)));
      add_settings_field('js_textShadow_color', __('Define Shadow Color', STB_DOMAIN), array(&$this, 'drawColorButton'), 'stb-settings', 'jsTextShadowSection', array('description' => __("This is text shadow color for text block.", STB_DOMAIN)));
      
      add_settings_field('border_style', __("Select border style for Special Text Boxes", STB_DOMAIN), array(&$this, 'drawSelectOption'), 'stb-settings', 'cssSection', array('description' => __('Selecting "None" will disable Special Text Boxes border.', STB_DOMAIN), "options" => array( 'solid' => __('Solid', STB_DOMAIN), 'dashed' => __('Dashed', STB_DOMAIN), 'dotted' => __('Dotted', STB_DOMAIN), 'none' => __('None', STB_DOMAIN) )));
      add_settings_field('fontSize', __("Define font size for Special Text Boxes", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'cssSection', array('description' => __("This is font size in pixels.", STB_DOMAIN).' '.__("Set this parameter to value 0 for theme default font size.", STB_DOMAIN)));
      add_settings_field('captionFontSize', __("Define caption font size for Special Text Boxes", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'cssSection', array('description' => __("This is caption font size in pixels.", STB_DOMAIN).' '.__("Set this parameter to value 0 for theme default font size.", STB_DOMAIN)));
      add_settings_field('bigImg', __('Allow Big Image for Simple (non-captioned) Special Text Boxes?', STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'cssSection', array('description' => __('Selecting "Yes" will allow big icons for simple (non-captioned) Special Text Boxes.', STB_DOMAIN), 'options' => array( 'true' => __("Yes", STB_DOMAIN), 'false' => __("No", STB_DOMAIN))));
      add_settings_field('showImg', __('Allow icon images for Special Text Boxes?', STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'cssSection', array('optionName' => 'showImg', 'description' => __('Selecting "Yes" will allow displaying icon images in Special Text Boxes.', STB_DOMAIN), "options" => array( 'true' => __("Yes", STB_DOMAIN), 'false' => __("No", STB_DOMAIN))));
      
      add_settings_field('rounded_corners', __("Allow rounded corners for Special Text Boxes?", STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'cssXSection', array('description' => __('Selecting "No" will disable Special Text Boxes rounded corners.', STB_DOMAIN), 'options' => array( 'true' => __('Yes', STB_DOMAIN), 'false' => __('No', STB_DOMAIN))));
      add_settings_field('box_shadow', __("Allow box shadow for Special Text Boxes?", STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'cssXSection', array('description' => __('Selecting "No" will disable Special Text Boxes shadow.', STB_DOMAIN), 'options' => array( 'true' => __('Yes', STB_DOMAIN), 'false' => __('No', STB_DOMAIN))));
      add_settings_field('text_shadow', __('Allow text shadow for Special Text Boxes?', STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'cssXSection', array('description' => __('Selecting "No" will disable Special Text Boxes text shadow.', STB_DOMAIN), 'options' => array( 'true' => __('Yes', STB_DOMAIN), 'false' => __('No', STB_DOMAIN))));

      add_settings_field('css_loading', __('Define mode of CSS loading', STB_DOMAIN), array(&$this, 'drawRadioOption'), 'stb-settings', 'cssSysSection', array('description' => __('Static - will be loaded static styles sheet file. More faster but needs full read/write access to file. Dynamic - will be loaded dynamic (PHP) styles sheet.', STB_DOMAIN), 'options' => array('static' => __('Static', STB_DOMAIN), 'dynamic' => __('Dynamic', STB_DOMAIN))));
      
      add_settings_field('cb_color', __("Define font color for Custom Special Text Box", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'editorSection', array('optionName' => 'cb_color', 'description' => __("This is a font color of Custom Special Text Box (Six Hex Digits).", STB_DOMAIN)));
      add_settings_field('cb_caption_color', __("Define caption font color for Custom Special Text Box", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'editorSection', array('optionName' => 'cb_caption_color', 'description' => __("This is a font color of Custom Special Text Box caption (Six Hex Digits).", STB_DOMAIN)));
      add_settings_field('cb_fontSize', __("Define font size for Custom Special Text Box", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'editorSection', array('optionName' => 'cb_fontSize', 'description' => __("This is font size in pixels.", STB_DOMAIN).' '.__("Set this parameter to value 0 for theme default font size.", STB_DOMAIN)));
      add_settings_field('cb_captionFontSize', __("Define caption font size for Custom Special Text Box", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'editorSection', array('optionName' => 'cb_captionFontSize', 'description' => __("This is caption font size in pixels.", STB_DOMAIN).' '.__("Set this parameter to value 0 for theme default font size.", STB_DOMAIN)));
      add_settings_field('cb_background', __("Define background color for Custom Special Text Box", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'editorSection', array('optionName' => 'cb_background', 'description' => __("This is a background color of Custom Special Text Box (Six Hex Digits).", STB_DOMAIN)));
      add_settings_field('cb_caption_background', __("Define background color for Custom Special Text Box caption", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'editorSection', array('optionName' => 'cb_caption_background', 'description' => __("This is a background color of Custom Special Text Box caption (Six Hex Digits).", STB_DOMAIN)));
      add_settings_field('cb_border_color', __("Define border color for Custom Special Text Box", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'editorSection', array('optionName' => 'cb_border_color', 'description' => __("This is a border color of Custom Special Text Box (Six Hex Digits).", STB_DOMAIN)));
      add_settings_field('cb_image', __("Define image for Custom Special Text Box", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'editorSection', array('optionName' => 'cb_image', 'description' => __("This is an image of Custom Special Text Box (Full URL). 25x25 pixels, transparent background PNG image recommended.", STB_DOMAIN), 'width' => 100));
      add_settings_field('cb_bigImg', __("Define big image for simple (non-captioned) Custom Special Text Box", STB_DOMAIN), array(&$this, 'drawTextOption'), 'stb-settings', 'editorSection', array('optionName' => 'cb_bigImg', 'description' => __("This is big image for simple (non-captioned) Custom Special Text Box (Full URL). 50x50 pixels, transparent background PNG image recommended.", STB_DOMAIN), 'width' => 100));
      
      register_setting('stbOptions', STB_OPTIONS);
    }
    
    public function doSettingsSections($page) {
      global $wp_settings_sections, $wp_settings_fields;

      if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) ) return;

      foreach ( (array) $wp_settings_sections[$page] as $section ) {
        switch($section['id']) {
          case 'modeSection':
            echo "<div id='tab-general'>";
            break;
          case 'jsSection':
            echo "<div id='tab-js'>";
            break;
          case 'cssSection':
            echo "<div id='tab-css'>";
            break;
          default: break;
        }
        
        echo "<div id='poststuff' class='ui-sortable'>\n";
        echo "<div class='postbox opened'>\n";
        echo "<h3>{$section['title']}</h3>\n";
        echo '<div class="inside">';
        call_user_func($section['callback'], $section);
        if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
          continue;
        $this->doSettingsFields($page, $section['id']);
        
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        switch($section['id']) {
          case 'deactivationSection':
            echo "</div>";
            break;
          case 'jsTextShadowSection':
            echo "</div>";
            break;
          case 'cssSysSection':
            echo "</div>";
            break;
          default: break;
        }
      }
    }
    
    public function doSettingsFields($page, $section) {
      global $wp_settings_fields;

      if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
        return;

      foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
        echo '<p>';
        if ( !empty($field['args']['checkbox']) ) {
          call_user_func($field['callback'], $field['id'], $field['args']);
          echo '<label for="' . $field['args']['label_for'] . '">' . $field['title'] . '</label>';
          echo '</p>';
        }
        else {
          if ( !empty($field['args']['label_for']) )
            echo '<label for="' . $field['args']['label_for'] . '">' . $field['title'] . '</label>';
          else
            echo '<strong>' . $field['title'] . '</strong><br/>';
          echo '</p>';
          echo '<p>';
          call_user_func($field['callback'], $field['id'], $field['args']);
          echo '</p>';
        }
        if(!empty($field['args']['description'])) echo '<p>' . $field['args']['description'] . '</p>';
      }
    }
    
    public function drawModeSection() {
      echo '';
    }
    
    public function drawBasicSection() {
      echo '';
    }
    
    public function drawExtendedSection() {
      echo '';
    }
    
    public function drawDeactivationSection() {
      echo '<p>'.__('Are you allow to perform these actions during deactivating plugin?', STB_DOMAIN).'</p>';
    }
    
    public function drawJsSection() {
      echo '<p>'.__('Use parameters below for customising Special Text Box for drawing in javascript mode.', STB_DOMAIN).'</p>';
    }
    
    public function drawJsShadowSection() {
      echo '<p>'.__('Use parameters below for customising shadow of Special Text Box for drawing in javascript mode.', STB_DOMAIN).'</p>';
    }
    
    public function drawJsTextShadowSection() {
      echo '<p>'.__('Use parameters below for customising text shadow of Special Text Box for drawing in javascript mode.', STB_DOMAIN).'</p>';
    }
    
    public function drawCssSection() {
      echo '<p>'.__('Use parameters below for customising Special Text Box for drawing in CSS mode.', STB_DOMAIN).'</p>';
    }
    
    public function drawCssXSection() {
      echo '<p>'.__('Parameters below add elements of CSS3 standard to Style Sheet. Not all browsers can interpret this elements properly, but including this elements to HTML page not crash browser.', STB_DOMAIN).'</p>';
    }

    public function drawSysSection() {
      echo '';
    }
    
    public function drawSelectOption( $optionName, $args ) {
      $options = $args['options'];
      ?>
        <select id="<?php echo $optionName; ?>"
          name="<?php echo STB_OPTIONS.'['.$optionName.']'; ?>">
          <?php foreach($options as $key => $option) { ?>
            <option value="<?php echo $key; ?>" 
              <?php selected($key, $this->settings[$optionName]); ?> ><?php echo $option; ?>
            </option>
          <?php } ?>
        </select>
      <?php
    }
    
    public function drawRadioOption( $optionName, $args ) {
      $options = $args['options'];
      $multiLines = $args['multiLines'];
      foreach ($options as $key => $option) {
      ?>
        <label for="<?php echo $optionName.'_'.$key; ?>">
          <input type="radio" 
            id="<?php echo $optionName.'_'.$key; ?>" 
            name="<?php echo STB_OPTIONS.'['.$optionName.']'; ?>" 
            value="<?php echo $key; ?>" <?php checked($key, $this->settings[$optionName]); ?> /> 
          <?php echo $option;?>
        </label>&nbsp;&nbsp;&nbsp;&nbsp;
      <?php
      if($multiLines) echo '<br />';
      }
    }
    
    public function drawTextOption( $optionName, $args ) {
      $width = $args['width'];
      $suffix = (empty($args['suffix'])) ? '' : $args['suffix'];
      ?>
        <input id="<?php echo $optionName; ?>"
          name="<?php echo STB_OPTIONS.'['.$optionName.']'; ?>"
          type="text"
          value="<?php echo $this->settings[$optionName]; ?>" 
          style="height: 22px; font-size: 11px; <?php if(!empty($width)) echo 'width: '.$width.'%;' ?>" /> <?php echo $suffix ?>
      <?php
    }

    public function drawCheckboxOption( $optionName, $args ) {
      ?>
        <input id="<?php echo $optionName; ?>"
          <?php checked('1', $this->settings[$optionName]); ?>
          name="<?php echo STB_OPTIONS.'['.$optionName.']'; ?>"
          type="checkbox"
          value="1" />
      <?php
    }

    public function drawColorButton( $optionName, $args ) {
      ?>
      <div id="<?php echo $optionName; ?>-button" class="color-btn color-btn-left">
        <b style="background-color: <?php echo '#'.$this->settings[$optionName]; ?>;"></b>
        <?php echo strtoupper(/*str_replace('#', '',*/ $this->settings[$optionName]/*)*/); ?>
      </div>
      <input id="<?php echo $optionName; ?>"
        name="<?php echo  STB_OPTIONS.'['.$optionName.']'; ?>"
        value="<?php echo $this->settings[$optionName]; ?>"
        type="hidden" />
      <?php
    }

    public function writeCSS($out) {
      $options = $this->settings;
      $styles = $this->styles;
      $cssFile = STB_DIR.'css/wp-special-textboxes.css';

      $content =  ".stb-container-css {margin: {$options['top_margin']}px {$options['right_margin']}px {$options['bottom_margin']}px {$options['left_margin']}px;}";

      $content .= ".stb-box {";
      if($options['fontSize'] !== '0') $content .=  "font-size: {$options['fontSize']}px;";
      if($options['text_shadow'] == "true") $content .= "text-shadow: 1px 1px 2px #888;";
      $content .= "}";

      $content .= ".stb-caption-box {";
      if($options['captionFontSize'] !== '0') $content .= "font-size: {$options['captionFontSize']}px;";
      $content .= "}";

      $content .= ".stb-body-box {";
      if($options['fontSize'] !== '0') $content .= "font-size: {$options['fontSize']}px;";
      $content .= "}";

      $content .= "\n"."/* Class Dependent Parameters */"."\n";
      foreach($styles as &$val) {
        if(!isset($val['cssStyle']['bgColorEnd'])) {
          $val['cssStyle']['bgColorEnd'] = str_replace('#', '', $val['cssStyle']['bgColor']);
        }
        if(!isset($val['cssStyle']['captionBgColorEnd'])) {
          $val['cssStyle']['captionBgColorEnd'] = str_replace('#', '', $val['cssStyle']['captionBgColor']);
        }

        $content .= ".stb-border.stb-{$val['slug']}-container {";
        $content .= "border: 1px {$options['border_style']} #{$val['cssStyle']['borderColor']};";
        $content .= "}";

        $content .= ".stb-side.stb-{$val['slug']}-container {";
        $content .= "background: #{$val['cssStyle']['captionBgColor']};";
        $content .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#{$val['cssStyle']['captionBgColor']}', endColorstr='#{$val['cssStyle']['captionBgColorEnd']}',GradientType=0 );";
        $content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%, #{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['captionBgColor']}), color-stop(90%,#{$val['cssStyle']['captionBgColorEnd']}));";
        $content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "background: linear-gradient(#{$val['cssStyle']['captionBgColor']} 30%, #{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "}";

        $content .= ".stb-side-none.stb-{$val['slug']}-container {";
        $content .= "background: #{$val['cssStyle']['bgColor']};";
        $content .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#{$val['cssStyle']['bgColor']}', endColorstr='#{$val['cssStyle']['bgColorEnd']}',GradientType=0 );";
        $content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['bgColor']}), color-stop(90%,#{$val['cssStyle']['bgColorEnd']}));";
        $content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: linear-gradient(#{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "}";

        $content .= ".stb-{$val['slug']}_box {";
        $content .= "background: #{$val['cssStyle']['bgColor']};";
        $content .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#{$val['cssStyle']['bgColor']}', endColorstr='#{$val['cssStyle']['bgColorEnd']}',GradientType=0 );";
        $content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['bgColor']}), color-stop(90%,#{$val['cssStyle']['bgColorEnd']}));";
        $content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: linear-gradient(#{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "color: #{$val['cssStyle']['color']};";
        $content .= "}";

        $content .= ".stb-{$val['slug']}-caption_box {";
        $content .= "background: #{$val['cssStyle']['captionBgColor']};";
        $content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%, #{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['captionBgColor']}), color-stop(90%,#{$val['cssStyle']['captionBgColorEnd']}));";
        $content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['captionBgColor']} 30%,#{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "background: linear-gradient(#{$val['cssStyle']['captionBgColor']} 30%, #{$val['cssStyle']['captionBgColorEnd']} 90%);";
        $content .= "color: #{$val['cssStyle']['captionColor']};";
        if ($options['text_shadow'] == "true") {
          $content .= "text-shadow: 1px 1px 2px #888;";
        }
        $content .= "}";

        $content .= ".stb-{$val['slug']}-body_box {";
        $content .= "background: #{$val['cssStyle']['bgColor']};";
        $content .= "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#{$val['cssStyle']['bgColor']}', endColorstr='#{$val['cssStyle']['bgColorEnd']}',GradientType=0 );";
        $content .= "background: -moz-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: -webkit-gradient(linear, left top, left bottom, color-stop(30%,#{$val['cssStyle']['bgColor']}), color-stop(90%,#{$val['cssStyle']['bgColorEnd']}));";
        $content .= "background: -webkit-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: -o-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: -ms-linear-gradient(top,  #{$val['cssStyle']['bgColor']} 30%,#{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "background: linear-gradient(#{$val['cssStyle']['bgColor']} 30%, #{$val['cssStyle']['bgColorEnd']} 90%);";
        $content .= "color: #{$val['cssStyle']['color']};";
        if ($options['text_shadow'] == "true") {
          $content .= "text-shadow: 1px 1px 2px #888;";
        }
        $content .= "}";
      }

      if($out === 'file') {
        if(is_writable($cssFile) || !file_exists($cssFile)) {
          if($handle = fopen($cssFile, 'w')) {
            fwrite($handle, $content);
            fclose($handle);
            $result['action'] = true;
          }
          else {
            $result['action'] = false;
            $result['error'] = __("Can't open CSS file.", STB_DOMAIN);
          }
        }
        else {
          $result['action'] = false;
          $result['error'] = __("CSS file is not writable");
        }
      }
      else {
        echo $content;
        $result['action'] = true;
      }

      return $result;
    }
    
    public function stbAdminPage() {
      global $wpdb;
      
      $row = $wpdb->get_row('SELECT VERSION()AS ver', ARRAY_A);
      $sqlVersion = $row['ver'];
      $this->settings = parent::getAdminOptions();
      $mem = ini_get('memory_limit');
      $version = $this->getWpVersion();
      $wpVersion = $version['str'];
      $updated = 'false';
      ?>
      <div class="wrap">
        <div class="icon32" style="background: url('<?php echo STB_URL.'images/settings.png' ?>') no-repeat transparent; "><br/></div>
        <h2><?php  _e("Special Text Boxes Settings", STB_DOMAIN); ?></h2>
        <?php
        if(isset($_GET['settings-updated'])) $updated = $_GET['settings-updated'];
        elseif(isset($_GET['updated'])) $updated = $_GET['updated'];
        if($updated === 'true') {
          //$this->getCounters();
          //$this->settings = parent::getOptions();
          ?>
          <div class="updated below-h2">
            <p><strong><?php _e('Special Text Box settings are updated.', STB_DOMAIN); ?></strong></p>
          </div>
          <?php
          $outFile = self::writeCSS('file');
          if(!$outFile['action']) {
            ?>
<div class="error"><p><strong><?php echo $outFile['error'] ?></strong></p></div>
            <?php
          }
        }
        ?>
        <div class="clear"></div>
        <form action="options.php" method="post">
          <div id='poststuff' class='metabox-holder has-right-sidebar'>
            <div id="side-info-column" class="inner-sidebar" style='width: 281px !important;'>
              <div class='postbox opened'>
                <h3><?php _e('System Info', STB_DOMAIN) ?></h3>
                <div class="inside">
                  <p>
                    <?php 
                      echo __('Wordpress Version', STB_DOMAIN).': <strong>'.$wpVersion.'</strong><br/>';
                      echo __('Plugin Version', STB_DOMAIN).': <strong>'.STB_VERSION.'</strong><br/>';
                      echo __('Plugin DB Version', STB_DOMAIN).': <strong>'.STB_DB_VERSION.'</strong><br/>';
                      echo __('PHP Version', STB_DOMAIN).': <strong>'.PHP_VERSION.'</strong><br/>';
                      echo __('MySQL Version', STB_DOMAIN).': <strong>'.$sqlVersion.'</strong><br/>';
                      echo __('Memory Limit', STB_DOMAIN).': <strong>'.$mem.'</strong>'; 
                    ?>
                  </p>
                  <p>
                    <?php _e('Note! If you have detected a bug, include this data to bug report.', STB_DOMAIN); ?>
                  </p>
                </div>
              </div>
              <div class='postbox opened'>
                <h3><?php _e('Resources', STB_DOMAIN) ?></h3>
                <div class="inside">
                  <ul>
                    <li><a target='_blank' href='http://wordpress.org/extend/plugins/wp-special-textboxes/'><?php _e("Wordpress Plugin Page", STB_DOMAIN); ?></a></li>
                    <li><a target='_blank' href='http://www.simplelib.com/?p=11'><?php _e("Author Plugin Page", STB_DOMAIN); ?></a></li>
                    <li><a target='_blank' href='http://forum.simplelib.com/forumdisplay.php?6-Special-Text-Boxes'><?php _e("Support Forum", STB_DOMAIN); ?></a></li>
                    <li><a target='_blank' href='http://www.simplelib.com/'><?php _e("Author's Blog", STB_DOMAIN); ?></a></li>
                  </ul>                    
                </div>
              </div>  
              <div class='postbox opened'>
                <h3><?php _e('Donations', STB_DOMAIN) ?></h3>
                <div class="inside">
                  <div style="text-align: center; margin-top: 10px;">
                    <script type="text/javascript">
                      /* <![CDATA[ */
                      function affiliateLink(str){ str = unescape(str); var r = ''; for(var i = 0; i < str.length; i++) r += String.fromCharCode(8^str.charCodeAt(i)); document.write(r); }
                      affiliateLink('4i%28%60zmn5*%60%7C%7Cx2%27%27%7F%7F%7F%26%7Cmp%7C%25dafc%25il%7B%26kge%277zmn5%3B%3A9%3E%3F0*64aeo%28%7Bzk5*%60%7C%7Cx2%27%27%7F%7F%7F%26%7Cmp%7C%25dafc%25il%7B%26kge%27aeiom%7B%27jiffmz%7B%27%7Bfgzm%25908p%3E8%26oan*%28jgzlmz5*8*%28id%7C5*%5Cmp%7C%28Dafc%28Il%7B*%2764%27i6');
                      /* ]]> */
                    </script>
                  </div>
                  <p>
                    <?php
                    $format = __('If you have found this plugin useful, please consider making a %s to help support future development. Your support will be much appreciated. Thank you!', STB_DOMAIN);
                    $str = '<a title="'.__('Donate Now!', STB_DOMAIN).'" href="https://load.payoneer.com/LoadToPage.aspx?email=minimus@simplelib.com" target="_blank">'.__('donation', STB_DOMAIN).'</a>';
                    printf($format, $str);
                    ?>
                  </p>
                  <div style="text-align: center;">
                    <a href='https://pledgie.com/campaigns/23196'><img alt='Click here to lend your support to: Funds to complete the development of plugin Simple Ads Manager 2 and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/23196.png?skin_name=chrome' border='0' ></a>
                  </div>
                  <div style="text-align: center; margin: 10px;">
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                      <input type="hidden" name="cmd" value="_s-xclick">
                      <input type="hidden" name="hosted_button_id" value="FNPBPFSWX4TVC">
                      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                      <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                    </form>
                  </div>
                </div>
              </div>
              <div class='postbox opened'>
                <h3><?php _e('Another Plugins', STB_DOMAIN) ?></h3>
                <div class="inside">
                  <p>
                    <?php
                    $format = __('Another plugins from %s', STB_DOMAIN).':';
                    $str = '<strong><a target="_blank" href="http://wordpress.org/extend/plugins/profile/minimus">minimus</a></strong>'; 
                    printf($format, $str); 
                    ?>
                  </p>
                  <ul>
                    <li><a target='_blank' href='http://wordpress.org/extend/plugins/simple-ads-manager/'><strong>Simple Ads Manager</strong></a> - <?php _e("Advertisment rotation system with a flexible logic of displaying advertisements. ", STB_DOMAIN); ?></li>
                    <li><a target='_blank' href='http://wordpress.org/extend/plugins/simple-counters/'><strong>Simple Counters</strong></a> - <?php _e("Adds simple counters badge (FeedBurner subscribers and Twitter followers) to your blog.", STB_DOMAIN); ?></li>
                    <li><a target='_blank' href='http://wordpress.org/extend/plugins/simple-view/'><strong>Simple View</strong></a> - <?php _e("This plugin is WordPress shell for FloatBox library by Byron McGregor.", STB_DOMAIN); ?></li>
                    <li><a target='_blank' href='http://wordpress.org/extend/plugins/wp-copyrighted-post/'><strong>Copyrighted Post</strong></a> - <?php _e("Adds copyright notice in the end of each post of your blog. ", STB_DOMAIN); ?></li>
                  </ul>                    
                </div>
              </div>
            </div>
            <div id="post-body">
              <div id="post-body-content">
                <div id='tabs'>
                  <ul>
                    <li><a href='#tab-general'><?php _e('General', STB_DOMAIN); ?></a></li>
                    <li><a href='#tab-js'><?php _e('Javascript', STB_DOMAIN); ?></a></li>
                    <li><a href='#tab-css'><?php _e('CSS', STB_DOMAIN); ?></a></li>
                  </ul>
                  <?php settings_fields('stbOptions'); ?>
                  <?php $this->doSettingsSections('stb-settings'); ?>
                </div>
                <p class="submit">
                  <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
                </p>
                <p style='color: #777777; font-size: 12px; font-style: italic;'>Special Text Boxes plugin for Wordpress. Copyright &copy; 2010 - 2011, <a href='http://www.simplelib.com/'>minimus</a>. All rights reserved.</p>
              </div>
            </div>
          </div>
        </form>
      </div>
      <?php
    }
    
    public function stbStylesPage() {
      global $wpdb;
      $sTable = $wpdb->prefix . "stb_styles";
      
      if(isset($_GET['mode'])) $mode = $_GET['mode'];
      else $mode = 'active';
      if(isset($_GET["action"])) $action = $_GET['action'];
      else $action = 'styles';
      if(isset($_GET['item'])) $item = $_GET['item'];
      else $item = null;
      if(isset($_GET['iaction'])) $iaction = $_GET['iaction'];
      else $iaction = null;
      if(isset($_GET['iitem'])) $iitem = $_GET['iitem'];
      else $iitem = null;
      if(isset($_GET['apage'])) $apage = abs( (int) $_GET['apage'] );
      else $apage = 1;

      $options = $this->settings;
      $styles_per_page = 10;//$options['stylesPerPage'];
      //$items_per_page = $options['itemsPerPage'];
      $types = array('system' => __('System Style', STB_DOMAIN), 'custom' => __('Custom Style', STB_DOMAIN), 'special' => __('Special Style', STB_DOMAIN));
      
      if(!is_null($item)) {
        if($iaction === 'delete') $wpdb->update( $sTable, array( 'trash' => true ), array( 'slug' => $item ), array( '%d' ), array( '%s' ) );
        elseif($iaction === 'untrash') $wpdb->update( $sTable, array( 'trash' => false ), array( 'slug' => $item ), array( '%d' ), array( '%s' ) );
        elseif($iaction === 'kill') $wpdb->query("DELETE FROM $sTable WHERE slug='$item'");
      }
      if($iaction === 'kill-em-all') $wpdb->query("DELETE FROM $sTable WHERE trash=true AND stype='custom'");
      $trash_num = $wpdb->get_var("SELECT COUNT(*) FROM $sTable WHERE trash = TRUE");
      $active_num = $wpdb->get_var("SELECT COUNT(*) FROM $sTable WHERE trash = FALSE");
      if(is_null($active_num)) $active_num = 0;
      if(is_null($trash_num)) $trash_num = 0;
      $all_num = $trash_num + $active_num;
      $total = (($mode !== 'all') ? (($mode === 'trash') ? $trash_num : $active_num) : $all_num);
      $start = $offset = ( $apage - 1 ) * $styles_per_page;

      $page_links = paginate_links( array(
        'base' => add_query_arg( 'apage', '%#%' ),
        'format' => '',
        'prev_text' => __('&laquo;'),
        'next_text' => __('&raquo;'),
        'total' => ceil($total / $styles_per_page),
        'current' => $apage
      ));
      ?>
<div class='wrap'>
  <div class="icon32" style="background: url('<?php echo STB_URL.'images/stb-list.png' ?>') no-repeat transparent; "><br/></div>
  <h2><?php _e('Managing Styles', STB_DOMAIN); ?></h2>
  <?php
    //include_once('errors.class.php');
    //$errors = new samErrors();
    //if(!empty($errors->errorString)) echo $errors->errorString;
  ?>
  <ul class="subsubsub">
    <li><a <?php if($mode === 'all') echo 'class="current"';?> href="<?php echo admin_url('admin.php'); ?>?page=stb-styles&action=styles&mode=all"><?php _e('All', STB_DOMAIN); ?></a> (<?php echo $all_num; ?>) | </li>
    <li><a <?php if($mode === 'active') echo 'class="current"';?> href="<?php echo admin_url('admin.php'); ?>?page=stb-styles&action=styles&mode=active"><?php _e('Active', STB_DOMAIN); ?></a> (<?php echo $active_num; ?>) | </li>
    <li><a <?php if($mode === 'trash') echo 'class="current"';?> href="<?php echo admin_url('admin.php'); ?>?page=stb-styles&action=styles&mode=trash"><?php _e('Trash', STB_DOMAIN); ?></a> (<?php echo $trash_num; ?>)</li>
  </ul>
  <div class="tablenav">
    <div class="alignleft">
      <?php if($mode === 'trash') {?>
      <a class="button-secondary" href="<?php echo admin_url('admin.php'); ?>?page=stb-styles&action=styles&mode=trash&iaction=kill-em-all"><?php _e('Clear Trash', STB_DOMAIN); ?></a>
      <?php } else { ?>
      <a class="button-secondary" href="<?php echo admin_url('admin.php'); ?>?page=stb-editor&action=new&mode=style"><?php _e('Add New Style', STB_DOMAIN); ?></a>
      <?php } ?>
    </div>
    <div class="tablenav-pages">
      <?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', STB_DOMAIN ) . '</span>%s',
        number_format_i18n( $start + 1 ),
        number_format_i18n( min( $apage * $styles_per_page, $total ) ),
        '<span class="total-type-count">' . number_format_i18n( $total ) . '</span>',
        $page_links
      ); echo $page_links_text; ?>
    </div>
  </div>
  <div class="clear"></div>
  <table class="widefat fixed" cellpadding="0">
    <thead>
      <tr>
        <th id="t-thumb" class='manage-column column-title' style="width:10%;" scope="col"><?php _e('Style', STB_DOMAIN); ?></th>
        <th id="t-cap" class="manage-column column-title" style="width:50%;" scope="col"><?php _e('Style Name', STB_DOMAIN);?></th>
        <th id="t-slug" class="manage-column column-title" style="width:20%;" scope="col"><?php _e('Style Slug', STB_DOMAIN); ?></th>
        <th id="t-type" class="manage-column column-title" style="width:20%;" scope="col"><?php _e('Style Type', STB_DOMAIN);?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th id="b-thumb" class='manage-column column-title' style="width:10%;" scope="col"><?php _e('Style', STB_DOMAIN); ?></th>
        <th id="b-cap" class="manage-column column-title" style="width:50%;" scope="col"><?php _e('Style Name', STB_DOMAIN);?></th>
        <th id="b-slug" class="manage-column column-title" style="width:20%;" scope="col"><?php _e('Style Slug', STB_DOMAIN); ?></th>
        <th id="b-type" class="manage-column column-title" style="width:20%;" scope="col"><?php _e('Style Type', STB_DOMAIN);?></th>
      </tr>
    </tfoot>
    <tbody>
      <?php
      $sSql = "SELECT 
                  $sTable.slug, 
                  $sTable.caption, 
                  $sTable.js_style, 
                  $sTable.stype,
                  $sTable.trash 
                FROM $sTable".
                (($mode !== 'all') ? " WHERE $sTable.trash = ".(($mode === 'trash') ? 'TRUE' : 'FALSE') : '').
                " LIMIT $offset, $styles_per_page";
      $styles = $wpdb->get_results($sSql, ARRAY_A);          
      $i = 0;
      if(!is_array($styles) || empty ($styles)) {
      ?>
      <tr class="no-items" valign="top">
        <th class="colspanchange" colspan='4'><?php _e('There are no data ...', STB_DOMAIN).$sTable; ?></th>
      </tr>
        <?php } else {
          foreach($styles as $row) {            
            $jsStyle = unserialize($row['js_style']);            
        ?>
      <tr id="<?php echo $row['slug'];?>" class="<?php echo (($i & 1) ? 'alternate' : ''); ?> author-self status-publish iedit" valign="top">
        <td class="column-icon media-icon">
          <img src='<?php echo $jsStyle['image']; ?>' alt='<?php echo $row['caption']; ?>' width='30' height='30'>
        </td>
        <td class="post-title column-title">
          <strong style='display: inline;'><a href="<?php echo admin_url('admin.php'); ?>?page=stb-editor&action=edit&mode=style&item=<?php echo $row['slug']; ?>"><?php echo $row['caption'];?></a><?php echo ((($row['trash'] == true) && ($mode === 'all')) ? '<span class="post-state"> - '.__('in Trash', STB_DOMAIN).'</span>' : ''); ?></strong>
          <div class="row-actions">
            <span class="edit"><a href="<?php echo admin_url('admin.php'); ?>?page=stb-editor&action=edit&mode=style&item=<?php echo $row['slug']; ?>" title="<?php _e('Edit Style', STB_DOMAIN) ?>"><?php _e('Edit', STB_DOMAIN); ?></a></span>
            <?php 
            if($row['trash'] == true) { 
              ?>
              <span class="untrash"> | <a href="<?php echo admin_url('admin.php'); ?>?page=stb-styles&action=zones&mode=<?php echo $mode ?>&iaction=untrash&item=<?php echo $row['slug'] ?>" title="<?php _e('Restore this Style from the Trash', STB_DOMAIN) ?>"><?php _e('Restore', STB_DOMAIN); ?></a></span>
              <span class="delete"> | <a href="<?php echo admin_url('admin.php'); ?>?page=stb-styles&action=styles&mode=<?php echo $mode ?>&iaction=kill&item=<?php echo $row['slug'] ?>" title="<?php _e('Remove this Style permanently', STB_DOMAIN) ?>"><?php _e('Remove permanently', STB_DOMAIN); ?></a></span>
            <?php 
            } 
            elseif($row['stype'] == 'custom') { 
              ?>
              <span class="delete"> | <a href="<?php echo admin_url('admin.php'); ?>?page=stb-styles&action=styles&mode=<?php echo $mode ?>&iaction=delete&item=<?php echo $row['slug'] ?>" title="<?php _e('Move this Style to the Trash', STB_DOMAIN) ?>"><?php _e('Delete', STB_DOMAIN); ?></a></span>
            <?php } ?>
          </div>
        </td>
        <th class="post-title column-title"><?php echo $row['slug']; ?></th>
        <td class='post-title column-title'><?php echo $types[$row['stype']] ?></td>
      </tr>
        <?php $i++; }}?>
    </tbody>
  </table>
  <div class="tablenav">
    <div class="alignleft">
      <?php if($mode === 'trash') {?>
      <a class="button-secondary" href="<?php echo admin_url('admin.php'); ?>?page=stb-styles&action=styles&mode=trash&iaction=kill-em-all"><?php _e('Clear Trash', STB_DOMAIN); ?></a>
      <?php } else { ?>
      <a class="button-secondary" href="<?php echo admin_url('admin.php'); ?>?page=stb-editor&action=new&mode=style"><?php _e('Add New Style', STB_DOMAIN); ?></a>      
      <?php } ?>
    </div>
    <div class="tablenav-pages">
      <?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', STB_DOMAIN ) . '</span>%s',
        number_format_i18n( $start + 1 ),
        number_format_i18n( min( $apage * $styles_per_page, $total ) ),
        '<span class="total-type-count">' . number_format_i18n( $total ) . '</span>',
        $page_links
      ); echo $page_links_text; ?>
    </div>
  </div>
</div>      
      <?php
    }
    
    public function stbEditorPage() {
      global $wpdb;
      $sTable = $wpdb->prefix . "stb_styles";
      
      $options = $this->settings;
      
      if(isset($_GET['action'])) $action = $_GET['action'];
      else $action = 'new';
      if(isset($_GET['mode'])) $mode = $_GET['mode'];
      else $mode = 'style';
      if(isset($_GET['item'])) $item = $_GET['item'];
      else $item = null;
      if(isset($_GET['style'])) $style = $_GET['style'];
      else $style = null;
      
      $updated = false;
      $jsStyle = array();
      $cssStyle = array();
      $types = array('system' => __('System Style', STB_DOMAIN), 'custom' => __('Custom Style', STB_DOMAIN), 'special' => __('Special Style', STB_DOMAIN));
      $xUpdateString = '';
      $errorFile = false;

      if(isset($_POST['update_style'])) {
        $styleSlug = $_POST['style_slug'];
        
        $jsStyle = array(
          'image' => $_POST['js_image'],
          'color' => '#'.$_POST['js_color'], 
          'colorTo' => '#'.$_POST['js_color_to'],
          'fontColor' => '#'.$_POST['js_font_color'],
          'border' => array(
            'width' => $_POST['js_border_width'],
            'color' => '#'.$_POST['js_border_color']
          ),
          'caption' => array(
            'fontColor' => '#'.$_POST['js_caption_font_color'],
            'color' => '#'.$_POST['js_caption_color'],
            'colorTo' => '#'.$_POST['js_caption_color_to']
          )          
        );
        $cssStyle = array(
          'color' => $_POST['css_color'],
          'captionColor' => $_POST['css_caption_color'],
          'borderColor' => $_POST['css_border_color'],
          'bgColor' => $_POST['css_bg_color'],
          'bgColorEnd' => $_POST['css_bg_color_end'],
          'captionBgColor' => $_POST['css_caption_bg_color'],
          'captionBgColorEnd' => $_POST['css_caption_bg_color_end'],
          'image' => $_POST['css_image'],
          'bigImg' => $_POST['css_big_image'] 
        );
        
        $xUpdateString = '';
        $uSlug = '';
        
        if(!empty($_POST['slug'])) $uSlug = $_POST['slug'];
        else {
          $uSlug = 'stb_style_'.rand(100000, 999999);
          $xUpdateString = sprintf(__('You forgot to define the slug of style, therefore it was assigned randomly to %s. You can always change this one to the desired value.', STB_DOMAIN), $uSlug);
        }
        
        $updateRow = array(
          'slug' => $uSlug,
          'caption' => $_POST['caption'],
          'js_style' => serialize($jsStyle),
          'css_style' => serialize($cssStyle),
          'stype' => $_POST['stype'],
          'trash' => ($_POST['trash'] === 'true')
        );
        $formatRow = array( '%s', '%s', '%s', '%s', '%s', '%d');
        if($styleSlug === 'Undefined') {
          $wpdb->insert($sTable, $updateRow);
          $updated = true;
          //$item = $wpdb->insert_id;
          $item = $uSlug;
        }
        else {
          if(is_null($item)) $item = $styleSlug;
          $wpdb->update($sTable, $updateRow, array( 'slug' => $item ), $formatRow, array( '%s' ));
          $updated = true;
        }
        ?>
<!--<div class="updated below-h2"><p><strong><?php echo __("Style Data Updated.", STB_DOMAIN).' '.$xUpdateString;?></strong></p></div>-->
        <?php
        $this->styles = parent::getStyles();
        $outFile = self::writeCSS('file');
        if(!$outFile['action']) {
          $errorFile = true;
          ?>
<!--<div class="error"><p><strong><?php echo $outFile['error'] ?></strong></p></div>-->
          <?php
        }
      }
      
      $sSql = "SELECT 
                  $sTable.slug, 
                  $sTable.caption, 
                  $sTable.js_style, 
                  $sTable.css_style, 
                  $sTable.stype, 
                  $sTable.trash 
                FROM $sTable 
                WHERE slug = '$item';";
      
      if($action !== 'new') {
        $row = $wpdb->get_row($sSql, ARRAY_A);
        $jsStyle = unserialize($row['js_style']);
        $cssStyle = unserialize($row['css_style']);
        $styleSlug = $row['slug'];
        
      }
      else {
        if($updated) {
          $row = $wpdb->get_row($sSql, ARRAY_A);
          $jsStyle = unserialize($row['js_style']);
          $cssStyle = unserialize($row['css_style']);
          $styleSlug = $row['slug'];          
        }
        else {
          $row = array(
            'slug' => '',
            'caption' => '',
            'js_style' => '',
            'css_style' => '',
            'stype' => 'custom',
            'trash' => false
          );
          $jsStyle = array(
            'image' => STB_URL.'images/warning-2-b.png',
            'color' => '#f8fc91', 
            'colorTo' => '#f0d208',
            'fontColor' => '#000000',
            'border' => array(
              'width' => 0,
              'color' => '#d9be08'
            ),
            'caption' => array(
              'fontColor' => '#ffffff',
              'color' => '#1d1a1a',
              'colorTo' => '#504848'
            ) 
          );
          $cssStyle = array(
            'color' => '000000',
            'captionColor' => 'FFFFFF',
            'borderColor' => 'FE9A05',
            'bgColor' => 'FEFFD5',
            'bgColorEnd' => 'FEFFD5',
            'captionBgColor' => 'FE9A05',
            'captionBgColorEnd' => 'FE9A05',
            'image' => STB_URL.'images/warning.png',
            'bigImg' => STB_URL.'images/warning-b.png' 
          );
          $styleSlug = 'Undefined';
        }
      }
      if(!isset($cssStyle['bgColorEnd'])) $cssStyle['bgColorEnd'] = str_replace('#', '', $jsStyle['colorTo']);
      if(!isset($cssStyle['captionBgColorEnd'])) $cssStyle['captionBgColorEnd'] = str_replace('#', '', $jsStyle['caption']['colorTo']);
      ?>
<div class="wrap">
  <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <div class="icon32" style="background: url('<?php echo STB_URL.'images/stb-editor.png'; ?>') no-repeat transparent; "><br/></div>
    <h2><?php echo ( ( ($action === 'new') && ( $styleSlug === 'Undefined' ) ) ? __('New Style', STB_DOMAIN) : __('Edit Style', STB_DOMAIN).' ('.$item.')' ); ?></h2>
    <?php if($updated) { ?>
      <div class="updated below-h2"><p><strong><?php echo __("Style Data Updated.", STB_DOMAIN).' '.$xUpdateString;?></strong></p></div>
    <?php }
      if($errorFile) {
        echo "<div class='error'><p><strong>".$outFile['error']."</strong></p></div>";
      }
      //include_once('errors.class.php');
      //$errors = new samErrors();
      //if(!empty($errors->errorString)) echo $errors->errorString;
    ?>
    <div class="metabox-holder has-right-sidebar" id="poststuff">
      <div id="side-info-column" class="inner-sidebar">
        <div class="meta-box-sortables ui-sortable">
          <div id="submitdiv" class="postbox ">
            <div class="handlediv" title="<?php _e('Click to toggle', STB_DOMAIN); ?>"><br/></div>
            <h3 class="hndle"><span><?php _e('Status', STB_DOMAIN);?></span></h3>
            <div class="inside">
              <div id="submitpost" class="submitbox">
                <div id="minor-publishing">
                  <div id="minor-publishing-actions">
                    <div id="save-action"> </div>
                    <div id="preview-action">
                      <a id="post-preview" class="preview button" href='<?php echo admin_url('admin.php'); ?>?page=stb-styles'><?php _e('Back to Styles List', STB_DOMAIN) ?></a>
                    </div>
                    <div class="clear"></div>
                  </div>
                  <div id="misc-publishing-actions">
                    <div class="misc-pub-section">
                      <label for="place_id_stat"><?php echo __('Style Type', STB_DOMAIN).':'; ?></label>
                      <span id="style_type" class="post-status-display"><?php echo $types[$row['stype']]; ?></span>
                      <input type="hidden" id="style_slug" name="style_slug" value="<?php echo $styleSlug; ?>" />
                      <input type='hidden' name='editor_mode' id='editor_mode' value='style'>
                      <input type='hidden' name='stype' id='stype' value='<?php echo $row['stype']; ?>'>
                    </div>
                    <div class="misc-pub-section">
                      <label for="trash_no"><input type="radio" id="trash_no" value="false" name="trash" <?php if (!$row['trash']) { echo 'checked="checked"'; }?> >  <?php _e('Is Active', STB_DOMAIN); ?></label><br/>
                      <label for="trash_yes"><input type="radio" id="trash_yes" value="true" name="trash" <?php if ($row['trash']) { echo 'checked="checked"'; }?> >  <?php _e('Is In Trash', STB_DOMAIN); ?></label>
                    </div>
                  </div>
                  <div class="clear"></div>
                </div>
                <div id="major-publishing-actions">
                  <div id="delete-action">
                    <a class="submitdelete deletion" href='<?php echo admin_url('admin.php'); ?>?page=stb-styles'><?php _e('Cancel', STB_DOMAIN) ?></a>
                  </div>
                  <div id="publishing-action">
                    <input type="submit" class='button-primary' name="update_style" value="<?php _e('Save', STB_DOMAIN) ?>" />
                  </div>
                  <div class="clear"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="post-body">
        <div id="post-body-content">
          <div class="meta-box-sortables ui-sortable">
            <div id="descdiv" class="postbox ">
              <div class="handlediv" title="<?php _e('Click to toggle', STB_DOMAIN); ?>"><br/></div>
              <h3 class="hndle"><span><?php _e('Style Names', STB_DOMAIN);?></span></h3>
              <div class="inside">
                <p><?php ($row['stype'] == 'custom') ? _e('Enter Default Caption/Name and Slug for this Style.', STB_DOMAIN) : _e('Enter Default Caption/Name for this Style.', STB_DOMAIN);?></p>
                <p>
                  <label for='caption'><strong><?php echo __('Default Caption/Name', STB_DOMAIN).':'; ?></strong></label>
                  <input type='text' name='caption' id='caption' value='<?php echo $row['caption']; ?>' style='width: 100%;'>
                </p>
                <p><?php _e('This is name of style. Also it can be used as default caption for captioned STB block.', STB_DOMAIN); ?></p>
                <?php if($row['stype'] === 'custom') { ?>
                <p>
                  <label for="slug"><strong><?php echo __('Slug', STB_DOMAIN).':'; ?></strong></label>
                  <input type='text' name='slug' id='slug' value='<?php echo $row['slug']; ?>' style="width:100%">
                </p>
                <p><?php _e('This is a unique style name. Must consist of latin characters, numbers and underscore characters only!', STB_DOMAIN); ?></p>
                <p>
                  <?php } else { echo __('Slug', STB_DOMAIN).': <strong><em>'.$row['slug'].'</em></strong>'; ?>
                  <input type='hidden' name='slug' id='slug' value='<?php echo $row['slug']; ?>'>
                </p>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="meta-box-sortables ui-sortable">
            <div id="sizediv" class="postbox ">
              <div class="handlediv" title="<?php _e('Click to toggle', STB_DOMAIN); ?>"><br/></div>
              <h3 class="hndle"><span><?php _e('Javascript Style Parameters', STB_DOMAIN);?></span></h3>
              <div class="inside">
                <p><strong><?php echo __('Box background gradient', STB_DOMAIN).':'; ?></strong></p>
                <div id="js_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo $jsStyle['color']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $jsStyle['color'])); ?></div>
                <input type='hidden' name='js_color' id='js_color' value='<?php echo str_replace('#', '', $jsStyle['color']); ?>'/>
                <div id="js_color_to-button" class="color-btn color-btn-left"><b style="background-color: <?php echo $jsStyle['colorTo']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $jsStyle['colorTo'])); ?></div>
                <input type='hidden' name='js_color_to' id='js_color_to' value='<?php echo str_replace('#', '', $jsStyle['colorTo']); ?>'/>
                <p><?php _e('There are colors of box background gradient. Direction of gradient drawing is from top to bottom.', STB_DOMAIN); ?></p>
                <p><strong><?php echo __('Font color', STB_DOMAIN).': '; ?></strong></p>
                <div id="js_font_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo $jsStyle['fontColor']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $jsStyle['fontColor'])); ?></div>
                <input type='hidden' name='js_font_color' id='js_font_color' value='<?php echo str_replace('#', '', $jsStyle['fontColor']); ?>'/>
                <p><?php printf(__("This is a font color of %s Special Text Box (Six Hex Digits).", STB_DOMAIN), $row['caption']); ?></p>
                <p>
                  <strong><?php echo __('Image', STB_DOMAIN).': '; ?></strong><br/>
                  <input type='text' name='js_image' id='js_image' value='<?php echo $jsStyle['image']; ?>' style='width: 80%;'>&nbsp;&nbsp;
                  <input type="button" class="button-secondary" id="selJsImg" name="selJsImg" value="<?php _e('Select', STB_DOMAIN); ?>">
                </p>
                <p><?php printf(__("This is image for %s Special Text Box (Full URL). 50x50 pixels, transparent background PNG image recommended.", STB_DOMAIN), $row['caption']); ?></p>
                <div class='clear-line'></div>
                <p>
                  <strong><?php echo __('Border Width', STB_DOMAIN).': '; ?></strong><br/>
                  <input type='text' name='js_border_width' id='js_border_width' value='<?php echo $jsStyle['border']['width']; ?>' style='width: 100px;'>
                </p>
                <p><strong><?php echo __('Border Color', STB_DOMAIN).': '; ?></strong><br/></p>
                <div id="js_border_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo $jsStyle['border']['color']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $jsStyle['border']['color'])); ?></div>
                <input type='hidden' name='js_border_color' id='js_border_color' value='<?php echo str_replace('#', '', $jsStyle['border']['color']); ?>'/>
                <p><?php printf(__("This is a border color of %s Special Text Box (Six Hex Digits).", STB_DOMAIN), $row['caption']); ?></p>
                <div class='clear-line'></div>
                <p><strong><?php echo __('Caption background gradient', STB_DOMAIN).':'; ?></strong></p>
                <div id="js_caption_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo $jsStyle['caption']['color']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $jsStyle['caption']['color'])); ?></div>
                <input type='hidden' name='js_caption_color' id='js_caption_color' value='<?php echo str_replace('#', '', $jsStyle['caption']['color']); ?>' style='width: 150px'>
                <div id="js_caption_color_to-button" class="color-btn color-btn-left"><b style="background-color: <?php echo $jsStyle['caption']['colorTo']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $jsStyle['caption']['colorTo'])); ?></div>
                <input type='hidden' name='js_caption_color_to' id='js_caption_color_to' value='<?php echo str_replace('#', '', $jsStyle['caption']['colorTo']); ?>' style='width: 150px;'>
                <p><?php _e('There are colors of caption background gradient. Direction of gradient drawing is from top to bottom.', STB_DOMAIN); ?></p>
                <p><strong><?php echo __('Caption Font Color', STB_DOMAIN).': '; ?></strong></p>
                <div id="js_caption_font_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo $jsStyle['caption']['fontColor']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $jsStyle['caption']['fontColor'])); ?></div>
                <input type='hidden' name='js_caption_font_color' id='js_caption_font_color' value='<?php echo str_replace('#', '', $jsStyle['caption']['fontColor']); ?>'/>
                <p><?php printf(__("This is a font color of %s Special Text Box caption (Six Hex Digits).", STB_DOMAIN), $row['caption']); ?></p>
                <?php if(($action !== 'new') || $updated) { ?>
                <div class='clear-line'></div>
                <div id='js_test_cap' class='test-box' data-stb="{safe: false, caption: {text: '<?php echo $row['caption'] ?>'}}">
                  <?php printf(__('This is example of Captioned %s Special Text Box. You must save style parameters to view changes.', STB_DOMAIN), $row['caption']); ?><br/><br/>
                  Lacus massa. Volutpat lacus irure sem malesuada. Nullam eu amet tincidunt, turpis est vestibulum. Elit ipsum justo, in mattis. Ultricies lacus tristique molestie eu, metus iure, et in, mattis sem.
                </div>
                <div id='js_test' class='test-box'>
                  <?php printf(__('This is example of %s Special Text Box. You must save style parameters to view changes.', STB_DOMAIN), $row['caption']); ?><br/><br/>
                  Lacus massa. Volutpat lacus irure sem malesuada. Nullam eu amet tincidunt, turpis est vestibulum. Elit ipsum justo, in mattis. Ultricies lacus tristique molestie eu, metus iure, et in, mattis sem.
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="meta-box-sortables ui-sortable">
            <div id="sizediv" class="postbox ">
              <div class="handlediv" title="<?php _e('Click to toggle', STB_DOMAIN); ?>"><br/></div>
              <h3 class="hndle"><span><?php _e('CSS Style Parameters', STB_DOMAIN);?></span></h3>
              <div class="inside">
                <p><strong><?php echo _e('Background Color', STB_DOMAIN).':'; ?></strong></p>
                <div id="css_bg_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo '#'.$cssStyle['bgColor']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $cssStyle['bgColor'])); ?></div>
                <input type='hidden' name='css_bg_color' id='css_bg_color' value='<?php echo $cssStyle['bgColor']; ?>'/>
                <div id="css_bg_color_end-button" class="color-btn color-btn-left"><b style="background-color: <?php echo '#'.$cssStyle['bgColorEnd']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $cssStyle['bgColorEnd'])); ?></div>
                <input type='hidden' name='css_bg_color_end' id='css_bg_color_end' value='<?php echo $cssStyle['bgColorEnd']; ?>'/>
                <p><?php _e('There are colors of box background gradient. Direction of gradient drawing is from top to bottom.', STB_DOMAIN); ?></p>
                <p><strong><?php echo _e('Font Color', STB_DOMAIN).':'; ?></strong></p>
                <div id="css_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo '#'.$cssStyle['color']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $cssStyle['color'])); ?></div>
                <input type='hidden' name='css_color' id='css_color' value='<?php echo $cssStyle['color']; ?>'/>
                <p><?php printf(__("This is a font color of %s Special Text Box (Six Hex Digits).", STB_DOMAIN), $row['caption']); ?></p>
                <!--<p>
                  <strong><?php echo __('Image', STB_DOMAIN).': '; ?></strong><br/>
                  <input type='text' name='css_image' id='css_image' value='<?php echo $cssStyle['image']; ?>' style='width: 100%;'>
                </p>
                <p><?php printf(__("This is an image of %s Special Text Box (Full URL). 25x25 pixels, transparent background PNG image recommended.", STB_DOMAIN), $row['caption']); ?></p>-->
                <p>
                  <strong><?php echo __('Image', STB_DOMAIN).': '; ?></strong><br/>
                  <input type='text' name='css_big_image' id='css_big_image' value='<?php echo $cssStyle['bigImg']; ?>' style='width: 80%;'>&nbsp;&nbsp;
                  <input type="button" class="button-secondary" id="selCssImg" name="selCssImg" value="<?php _e('Select', STB_DOMAIN); ?>">
                </p>
                <p><?php printf(__("This is image for %s Special Text Box (Full URL). 50x50 pixels, transparent background PNG image recommended.", STB_DOMAIN), $row['caption']); ?></p>
                <div class='clear-line'></div>
                <p><strong><?php echo _e('Border Color', STB_DOMAIN).':'; ?></strong></p>
                <div id="css_border_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo '#'.$cssStyle['borderColor']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $cssStyle['borderColor'])); ?></div>
                <input type='hidden' name='css_border_color' id='css_border_color' value='<?php echo $cssStyle['borderColor']; ?>'/>
                <p><?php printf(__("This is a border color of %s Special Text Box (Six Hex Digits).", STB_DOMAIN), $row['caption']); ?></p>
                <div class='clear-line'></div>
                <p><strong><?php echo _e('Caption Background Color', STB_DOMAIN).':'; ?></strong></p>
                <div id="css_caption_bg_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo '#'.$cssStyle['captionBgColor']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $cssStyle['captionBgColor'])); ?></div>
                <input type='hidden' name='css_caption_bg_color' id='css_caption_bg_color' value='<?php echo $cssStyle['captionBgColor']; ?>'/>
                <div id="css_caption_bg_color_end-button" class="color-btn color-btn-left"><b style="background-color: <?php echo '#'.$cssStyle['captionBgColorEnd']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $cssStyle['captionBgColorEnd'])); ?></div>
                <input type='hidden' name='css_caption_bg_color_end' id='css_caption_bg_color_end' value='<?php echo $cssStyle['captionBgColorEnd']; ?>'/>
                <p><?php _e('There are colors of caption background gradient. Direction of gradient drawing is from top to bottom.', STB_DOMAIN); ?></p>
                <p><strong><?php echo _e('Caption Font Color', STB_DOMAIN).':'; ?></strong></p>
                <div id="css_caption_color-button" class="color-btn color-btn-left"><b style="background-color: <?php echo '#'.$cssStyle['captionColor']; ?>;"></b><?php echo strtoupper(str_replace('#', '', $cssStyle['captionColor'])); ?></div>
                <input type='hidden' name='css_caption_color' id='css_caption_color' value='<?php echo $cssStyle['captionColor']; ?>'/>
                <p><?php printf(__("This is a font color of %s Special Text Box caption (Six Hex Digits).", STB_DOMAIN), $row['caption']); ?></p>
                <?php if(($action !== 'new') || $updated) { ?>
                <div class='clear-line'></div>
                <p><?php echo $row['caption']; ?></p>
                <?php echo $this->getSamples2(/*$row['slug']*/$item, $row['caption']);} ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>      
      <?php
    }
    
    public function addButtons() {
      // Don't bother doing this stuff if the current user lacks permissions
      if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
        return;
      
      // Add only in Rich Editor mode
      if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", array(&$this, "addTinyMCEPlugin"));
        add_filter('mce_buttons', array(&$this, 'registerButton'));
      }
    }
    
    public function registerButton( $buttons ) {
      array_push($buttons, "separator", "wstb");
      return $buttons;
    }
    
    public function addTinyMCEPlugin( $plugin_array ) {
      $plugin_array['wstb'] = plugins_url('wp-special-textboxes/js/editor_plugin.js');
      return $plugin_array;
    }
    
    public function tinyMCEVersion( $version ) {
      return ++$version;
    }
  }
}
?>