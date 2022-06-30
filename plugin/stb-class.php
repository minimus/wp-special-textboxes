<?php
if (!defined('ABSPATH')) {
    exit;
}

include_once 'stb-tools.php';
include_once 'stb-db-tools.php';
include_once 'stb-styles.php';
require_once 'stb-rest-api.php';

if (!class_exists("SpecialTextBoxes")) {
    class SpecialTextBoxes
    {
        protected string $nonce;
        public array $settings = [];
        public array $styles = [];
        public array $classes = [];
        private array $stbVersions = ['stb' => null, 'db' => null];
        protected object $stbTools;
        protected object $stbDbTools;
        protected object $stbStyles;

        public function __construct()
        {
            define('STB_VERSION', '6.0.1');
            define('STB_DB_VERSION', '2.0');
            define('STB_DIR', dirname(__FILE__) . '/');
            define('STB_DOMAIN', 'wp-special-textboxes');
            define('STB_OPTIONS', 'SpecialTextBoxesAdminOptions');
            define('STB_SETTINGS', 'SpecialTextBoxesAdminSettings');
            define('STB_URL', plugins_url('/', __FILE__));


            add_action('wp_enqueue_scripts', array(&$this, 'headerScripts'), 9999999999);

            add_filter('comment_text', 'do_shortcode');

            add_shortcode('stextbox', array(&$this, 'doShortcode'));
            add_shortcode('stb', array(&$this, 'doShortcode2'));
            add_shortcode('sgreybox', array(&$this, 'doShortcodeGrey'));

            $this->stbTools = new StbTools();
            $this->stbDbTools = new StbDbTools();
            $dbUpgraded = $this->stbDbTools->upgradeDb();

            $this->settings = self::getAdminOptions();
            $this->styles = $this->stbDbTools->getCurrentColors();
            $this->stbStyles = new StbStyles($this->settings, $this->styles);

            if ($dbUpgraded) {
                $this->stbStyles->updateCSS();
            }

            $this->classes = self::getClasses($this->styles);
            $this->getVersions(true);

            add_action('init', [$this, 'loadTextDomain']);
            add_action('init', [$this, 'createNonce']);
            add_action('init', [$this, 'restInit']);
            add_action('init', [$this, 'createStbBlock']);
            add_action('enqueue_block_editor_assets', [$this, 'blockEditorScripts']);
        }

        public function restInit()
        {
            add_action('rest_api_init', array($this, 'registerRoutes'));
        }

        public function registerRoutes()
        {
            global $current_user;
            $routes = new StbRestApi($current_user);
            $routes->setAdminRoutes();
        }

        public function createNonce()
        {
            global $STB_Nonce;
            $this->nonce = wp_create_nonce('wp_rest');
            $STB_Nonce = $this->nonce;
        }

        public function loadTextDomain()
        {
            if (function_exists('load_plugin_textdomain'))
                load_plugin_textdomain('wp-special-textboxes', false, dirname(plugin_basename(__FILE__)));
        }

        public function blockEditorScripts()
        {
            $defaults = [];
            $list = [];
            foreach ($this->styles as $val) {
                $list[] = ['label' => $val['caption'], 'text' => $val['caption'], 'value' => $val['slug']];
                $defaults[] = [
                    'slug' => $val['slug'],
                    'caption' => $val['caption'],
                    'image' => $val['colors']['image']
                ];
            }
            $stbBlockSettings = [
                'list' => $list,
                'strings' => [
                    'blockHeader' => __('Special Text', 'wp-special-textboxes'),
                    'blockDescription' => __('Highlights block of text as colored text block.', 'wp-special-textboxes'),
                    'colorSchemeLabel' => __('Color Scheme', 'wp-special-textboxes'),
                    'captionLabel' => __('Caption', 'wp-special-textboxes'),
                    'defaultCaptionLabel' => __('Default Caption', 'wp-special-textboxes'),
                    'contentLabel' => __('Content', 'wp-special-textboxes'),
                    'imageLabel' => __('Image', 'wp-special-textboxes'),
                    'selectImageCaption' => __('Select', 'wp-special-textboxes'),
                    'bigImageLabel' => __('Big image (only for blocks without caption)', 'wp-special-textboxes'),
                    'appearanceLabel' => __('Appearance', 'wp-special-textboxes'),
                    'collapsingLabel' => __('Can fold/unfold', 'wp-special-textboxes'),
                    'collapsedLabel' => __('Block is folded on loading', 'wp-special-textboxes'),
                    'marginsLabel' => __('Margins', 'wp-special-textboxes'),
                    'marginTopLabel' => __('Top, px', 'wp-special-textboxes'),
                    'marginRightLabel' => __('Right, px', 'wp-special-textboxes'),
                    'marginBottomLabel' => __('Bottom, px', 'wp-special-textboxes'),
                    'marginLeftLabel' => __('Left, px', 'wp-special-textboxes'),
                ],
                'defaults' => $defaults,
                'settings' => [
                    'bigImg' => $this->settings['bigImg'],
                    'showImg' => $this->settings['showImg'],
                    'collapsing' => $this->settings['collapsing'],
                    'collapsed' => $this->settings['collapsed'],
                    'margins' => $this->settings['margins'],
                    'side' => $this->settings['side'],
                ],
            ];

            wp_localize_script('wp-blocks', 'stbBlockSettings', $stbBlockSettings);
        }

        public function createStbBlock(): void
        {
            register_block_type(/* 'wp-special-textboxes/stb-block' */ __DIR__ . '/js/block');
        }

        public function getAdminOptions(): array
        {
            return $this->stbTools->getSettings();
        }

        function getVersions($force = false): array
        {
            $versions = array('stb' => null, 'db' => null);
            if ($force) {
                $versions['stb'] = get_option('stb_version', '');
                $versions['db'] = get_option('stb_db_version', '');
                $this->stbVersions = $versions;
            } else $versions = $this->stbVersions;

            return $versions;
        }

        public function getClasses($value): array
        {
            $classes = array();
            foreach ($value as $val) {
                $classes[] = $val['slug'];
            }
            return $classes;
        }

        protected function enqueueStbStyles()
        {
            // Styles
            wp_enqueue_style('stbCoreCSS', STB_URL . 'css/stb-core.css', false, STB_VERSION);
            if ($this->settings['cssLoading'] === 'static') {
                wp_enqueue_style('stbCommonCSS', STB_URL . 'css/stb-common.css', false, STB_VERSION);
                wp_enqueue_style('stbColorsCSS', STB_URL . 'css/stb-colors.css', false, STB_VERSION);
            } elseif ($this->settings['cssLoading'] === 'dynamic') {
                $inlineStyles = $this->stbStyles->getStyles();
                wp_add_inline_style('stbCoreCSS', $inlineStyles['common']);
                wp_add_inline_style('stbCoreCSS', $inlineStyles['colors']);
            }
        }

        public function headerScripts()
        {
            $options = [
                'restData' => [
                    'root' => esc_url_raw(rest_url()),
                    'nonce' => $this->nonce,
                ],
            ];

            // Styles
            self::enqueueStbStyles();

            // Scripts
            wp_enqueue_script('stbClient', STB_URL . 'js/client.js', [], STB_VERSION, true);
            wp_localize_script('stbClient', 'stbUserOptions', $options);
        }

        public function doShortcode($atts, string $content = null): string
        {
            $attributes = shortcode_atts([
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
                'shadow' => ''],
                !!$atts ? $atts : []);

            $block = new StbBlock($content, $attributes['id'], $attributes['caption'], $attributes);
            return $block->block;
        }

        public function doShortcode2($atts, string $content = null): string
        {
            $attributes = !!$atts ? $atts : [];
            $attributes['level'] = 1;
            return $this->doShortcode($attributes, $content);
        }

        public function doShortcodeGrey($atts, string $content = null): string
        {
            $attributes = !!$atts ? $atts : [];
            $attributes['id'] = 'grey';
            return $this->doShortcode($attributes, $content);
        }

        public function highlightText($content = null, $id = 'warning', $caption = '', $atts = null): string
        {
            $block = new StbBlock($content, $id, $caption, $atts);
            return $block->block;
        }
    }
}

if (!class_exists('special_text') && class_exists('WP_Widget')) {
    class special_text extends WP_Widget
    {
        function __construct()
        {
            $widget_ops = ['classname' => 'special_text', 'description' => __('Arbitrary text or PHP in colored block.', 'wp-special-textboxes')];
            $control_ops = array('width' => 350, 'height' => 450, 'id_base' => 'special_text');
            parent::__construct('special_text', __('Special Text', 'wp-special-textboxes'), $widget_ops, $control_ops);
        }

        function getClasses($value): array
        {
            $classes = array();
            foreach ($value as $val) {
                $classes[$val['slug']] = $val['name'];
            }
            return $classes;
        }

        function getStyles(): array
        {
            global $wpdb;
            $sTable = $wpdb->prefix . "stb_styles";
            $styles = array();

            if ($wpdb->get_var("SHOW TABLES LIKE '$sTable'") == $sTable) {
                $sSql = "SELECT slug, caption FROM $sTable WHERE trash IS FALSE;";
                $rows = $wpdb->get_results($sSql, ARRAY_A);
                $style = array();
                foreach ($rows as $value) {
                    $style['slug'] = $value['slug'];
                    $style['name'] = $value['caption'];
                    $styles[] = $style;
                }
            }
            return self::getClasses($styles);
        }

        function widget($args, $instance)
        {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
            $box_id = $instance['box_id'] ?? 'warning';
            // $parse = $instance['parse'] ?? false;
            $text = $instance['text'] ?? '';
            $showAll = $instance['show_all'] ?? true;
            $canShow = (((is_home() || is_front_page()) && ($instance['show_home'] ?? false)) ||
                (is_category() && ($instance['show_cat'] ?? false)) ||
                (is_archive() && ($instance['show_arc'] ?? false)) ||
                (is_single() && ($instance['show_single'] ?? false)) ||
                (is_tag() && ($instance['show_tag'] ?? false)) ||
                (is_author() && ($instance['show_author'] ?? false)));

            $block = new StbBlock($text, $box_id, $title);
            $codes = $block->getWidgetBox();

            $before_title = $codes['beforeTitle'];
            $after_title = $codes['afterTitle'];
            $before_widget = $codes['before'];
            $after_widget = $codes['after'];


            if (is_admin() || $showAll || $canShow) {
                echo $before_widget . $before_title . esc_attr__($title) . $after_title . esc_attr__($text) . $after_widget;
            }
        }

        function update($new_instance, $old_instance): array
        {
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

        function form($instance): void
        {
            $ids = self::getStyles();
            $instance = wp_parse_args((array)$instance,
                array(
                    'title' => '',
                    'box_id' => 'warning',
                    'parse' => false,
                    'text' => '',
                    'show_all' => true,
                    'show_home' => false,
                    'show_cat' => false,
                    'show_arc' => false,
                    'show_single' => false,
                    'show_tag' => false,
                    'show_author' => false
                )
            );
            $title = strip_tags($instance['title']);
            $boxId = $instance['box_id'];
            // $parse = $instance['parse'];
            $text = format_to_edit($instance['text']);
            $showAll = $instance['show_all'];
            $showHome = $instance['show_home'];
            $showSingle = $instance['show_single'];
            $showArc = $instance['show_arc'];
            $showCat = $instance['show_cat'];
            $showTag = $instance['show_tag'];
            $showAuthor = $instance['show_author'];
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                    <?php esc_attr_e('Title:', 'wp-special-textboxes'); ?>
                </label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text"
                       value="<?php echo esc_attr__($title); ?>"/>
            </p>

            <textarea class="widefat" rows="10" cols="20"
                      id="<?php echo esc_attr($this->get_field_id('text')); ?>"
                      name="<?php echo esc_attr($this->get_field_name('text')); ?>"><?php echo esc_textarea($text); ?>
            </textarea>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id('box_id')); ?>">
                    <?php _e('ID of Box:', 'wp-special-textboxes') ?>
                </label>
                <select class="widefat" id="<?php echo esc_attr($this->get_field_id('box_id')); ?>"
                        name="<?php echo esc_attr($this->get_field_name('box_id')); ?>">
                    <?php
                    foreach ($ids as $key => $option)
                        echo
                            '<option value="' . esc_attr($key) . '"' . selected($boxId, $key, false) . '>' .
                            esc_attr($option) .
                            '</option>';
                    ?>
                </select>
            </p>

            <p>
                <input id="<?php echo esc_attr($this->get_field_id('show_all')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('show_all')); ?>"
                       type="checkbox" <?php checked($showAll); ?> />&nbsp;
                <label for="<?php echo esc_attr($this->get_field_id('show_all')); ?>">
                    <?php esc_attr_e('Show on all pages of blog', 'wp-special-textboxes'); ?>
                </label>
            </p>

            <p><?php esc_attr_e('Show only on', 'wp-special-textboxes') ?>:
                <br/>
                <input id="<?php echo esc_attr($this->get_field_id('show_home')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('show_home')); ?>"
                       type="checkbox" <?php checked($showHome); ?> />&nbsp;
                <label for="<?php echo esc_attr($this->get_field_id('show_home')); ?>">
                    <?php esc_attr_e('Home Page', 'wp-special-textboxes'); ?>
                </label>
                <br/>
                <input id="<?php echo esc_attr($this->get_field_id('show_single')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('show_single')); ?>"
                       type="checkbox" <?php checked($showSingle); ?> />&nbsp;
                <label for="<?php echo esc_attr($this->get_field_id('show_single')); ?>">
                    <?php esc_attr_e('Single Post Pages', 'wp-special-textboxes'); ?>
                </label>
                <br/>
                <input id="<?php echo esc_attr($this->get_field_id('show_arc')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('show_arc')); ?>"
                       type="checkbox" <?php checked($showArc); ?> />&nbsp;
                <label for="<?php echo esc_attr($this->get_field_id('show_arc')); ?>">
                    <?php esc_attr_e('Archive Pages', 'wp-special-textboxes'); ?>
                </label>
                <br/>
                <input id="<?php echo esc_attr($this->get_field_id('show_cat')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('show_cat')); ?>"
                       type="checkbox" <?php checked($showCat); ?> />&nbsp;
                <label for="<?php echo esc_attr($this->get_field_id('show_cat')); ?>">
                    <?php esc_attr_e('Category Archive Pages', 'wp-special-textboxes'); ?>
                </label>
                <br/>
                <input id="<?php echo esc_attr($this->get_field_id('show_tag')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('show_tag')); ?>"
                       type="checkbox" <?php checked($showTag); ?> />&nbsp;
                <label for="<?php echo esc_attr($this->get_field_id('show_tag')); ?>">
                    <?php esc_attr_e('Tag Archive Pages', 'wp-special-textboxes'); ?>
                </label>
                <br/>
                <input id="<?php echo esc_attr($this->get_field_id('show_author')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('show_author')); ?>"
                       type="checkbox" <?php checked($showAuthor); ?> />&nbsp;
                <label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>">
                    <?php esc_attr_e('Author Archive Pages', 'wp-special-textboxes'); ?>
                </label>
                <br/>
            </p>
            <?php
        }
    } // End of class special_text
} // End of if
?>
