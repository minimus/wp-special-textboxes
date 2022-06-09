<?php
if (!defined('ABSPATH')) {
    exit;
}

include_once 'stb-class.php';

if (!class_exists('SpecialTextBoxesAdmin') && class_exists('SpecialTextBoxes')) {
    class SpecialTextBoxesAdmin extends SpecialTextBoxes
    {
        public string $menu_page;
        public array $stbProPointer = ['all' => true, 'themes' => true];

        public function __construct()
        {
            parent::__construct();

            $themesDir = trailingslashit(WP_CONTENT_DIR) . 'stb-themes/';

            if (self::checkThemesFolder($themesDir)) {
                define('STB_THEMES_DIR', $themesDir);                  // for backward compatibility
                define('STB_THEMES_URL', content_url('/stb-themes/')); // for backward compatibility
                define('STB_EXT_THEMES', true);
            } else {
                define('STB_THEMES_DIR', STB_DIR . 'themes/');
                define('STB_THEMES_URL', STB_URL . 'themes/');
                define('STB_EXT_THEMES', false);
            }

            /* $dbUpgraded = $this->stbDbTools->upgradeDb();
            if ($dbUpgraded) {
                $this->stbStyles->updateCSS();
            } */

            register_activation_hook(STB_MAIN_FILE, [&$this, 'onActivate']);
            register_deactivation_hook(STB_MAIN_FILE, [&$this, 'onDeactivate']);
            add_action('admin_menu', [&$this, 'regAdminPage']);
            add_filter('tiny_mce_version', [&$this, 'tinyMCEVersion']);
            add_action('init', [&$this, 'addButtons']);
            add_action('wp_ajax_close_stb_pointer', [&$this, 'closePointerHandler']);
            add_filter('mce_external_languages', [&$this, 'addMceLocale']);
        }

        public function onActivate()
        {
            $stbAdminOptions = $this->getAdminOptions();
            update_option(STB_SETTINGS, $stbAdminOptions);
        }

        public function onDeactivate()
        {
            if ($this->settings['deleteOptions'] == 1) {
                delete_option(STB_SETTINGS);
                delete_option('stb_version');
                delete_option('stb_pointers');
            }
            if ($this->settings['deleteDB'] == 1) {
                $this->stbDbTools->deleteTables();
            }
        }

        private function checkThemesFolder($dir): bool
        {
            return is_dir($dir);
        }

        public function addMceLocale($locales)
        {
            $locales['wstb'] = plugin_dir_path(__FILE__) . 'stb-tinymce-langs.php';

            return $locales;
        }

        public function getPointerOptions($force = false): array
        {
            if ($force) {
                $pointers = get_option('stb_pointers', '');
                if ($pointers == '') {
                    $pointers = $this->stbProPointer;
                    update_option('stb_pointers', $pointers);
                }
            } else {
                $pointers = $this->stbProPointer;
            }

            return $pointers;
        }

        public function closePointerHandler()
        {
            $options = self::getPointerOptions(true);
            $charset = get_bloginfo('charset');
            @header("Content-Type: application/json; charset={$charset}");
            if (isset($_REQUEST['pointer'])) {
                $pointer = $_REQUEST['pointer'];
                $options[$pointer] = false;
                update_option('stb_pointers', $options);
                wp_send_json_success(['pointer' => $pointer, 'options' => $options]);
            } else {
                wp_send_json_error();
            }
        }

        private function getPointerContent($pointer = false): string
        {
            $alt = __('Upgrade Now', 'wp-special-textboxes');
            $image = STB_URL . 'images/upgrade-now' . (($pointer) ? '-pointer' : '') . '.png';
            $about = __('About STB Pro...', 'wp-special-textboxes');
            $docs = __('STB Pro Documentation', 'wp-special-textboxes');
            $themes = __('Free STB Pro Themes', 'wp-special-textboxes');
            $intro = __('Get the full feature set of the <strong>Special Text Boxes</strong> plugin.', 'wp-special-textboxes');
            $intro2 = __('Upgrade to the STB Pro now!', 'wp-special-textboxes');
            $margin = (($pointer) ? " margin: 20px 15px 0;" : '');

            return
                "<div style='text-align: center;{$margin}'>" .
                "<a href='https://codecanyon.net/item/stb-pro-special-text-boxes-pro-editin/9749695' target='_blank'>" .
                "<img src='{$image}' alt='{$alt}'>" .
                "</a>" .
                "</div>" .
                "<p>{$intro}<br><a href='http://codecanyon.net/item/stb-pro-special-text-boxes-pro-editin/9749695'><strong>{$intro2}</strong></a></p>" .
                "<p><a target='_blank' href='http://stb.simplelib.com/info/stb-pro/'>{$about}</a><br>" .
                "<a href='https://stb.simplelib.com/category/documentation/' target='_blank'>{$docs}</a><br>" .
                "<a href='https://stb.simplelib.com/stb-pro-themes/'>{$themes}</a></p>";
        }

        public function loadScripts($hook)
        {
            if ($hook == $this->menu_page) {
                wp_enqueue_media();
                wp_enqueue_style('stbAdminCSS', STB_URL . 'css/stb-admin.css', false, STB_VERSION);

                $options = [
                    'restData' => [
                        'root' => esc_url_raw(rest_url()),
                        'nonce' => $this->nonce,
                    ],
                    'texts' => [
                        'ok' => __('OK', 'wp-special-textboxes'),
                        'cancel' => __('Cancel', 'wp-special-textboxes'),
                        'switchModeToNum' => __('Show numbers', 'wp-special-textboxes'),
                        'switchModeToCol' => __('Show color wheel', 'wp-special-textboxes')
                    ],
                    'media' => [
                        'title' => __('Select Image', 'wp-special-textboxes'),
                        'button' => __('Choose', 'wp-special-textboxes')
                    ],
                    'pluginData' => [
                        'root' => STB_URL,
                    ]
                ];
                wp_enqueue_script('wstbAdminLayout', STB_URL . 'js/admin.js', [], STB_VERSION, true);
                wp_localize_script('wstbAdminLayout', 'stbUserOptions', $options);
            } elseif ($hook == 'post.php' || $hook == 'post-new.php' || $hook === 'widgets.php') {
                parent::enqueueStbStyles();

                $styles = $this->styles;
                $slugs = '';
                $list = [];
                $defaults = [];
                foreach ($styles as $val) {
                    /* $slug = esc_attr($val['slug']);
                    $caption = esc_attr($val['caption']);
                    $slugs .= "<option value='{$slug}'>{$caption}</option>"; */
                    $list[] = ['label' => $val['caption'], 'text' => $val['caption'], 'value' => $val['slug']];
                    $defaults[] = [
                        'slug' => $val['slug'],
                        'caption' => $val['caption'],
                        'image' => $val['colors']['image']
                    ];
                }
                $data = [
                    'mceUrl' => get_option('siteurl') . '/wp-includes/js/tinymce/',
                    'mceUtilsUrl' => get_option('siteurl') . '/wp-includes/js/tinymce/utils/',
                    'jsUrl' => STB_URL . 'js/',
                    // 'slugs' => $slugs,
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

                /* wp_enqueue_script('stbClient', STB_URL . 'js/client.js', [], STB_VERSION, false);
                wp_localize_script('stbClient', 'stbEditorOptions', $data); */

                $json = wp_json_encode((object)$data);
                echo "<script type='text/javascript'>window.stbEditorOptions = {$json}</script>";
            } else {
                $pointers = self::getPointerOptions(true);
                /* if ($pointers['all']) {
                    $stbImage = STB_URL . 'images/upgrade-now.png';
                    $stbAlt = __('Upgrade Now!', 'wp-special-textboxes');

                    wp_enqueue_style('wp-pointer');

                    wp_enqueue_script('jquery');
                    wp_enqueue_script('wp-pointer');
                    wp_enqueue_script('stbAll', STB_URL . 'js/wstb.all.min.js');
                    wp_localize_script('stbAll', 'stbOptions', [
                        'pointer' => [
                            'enabled' => $pointers['all'],
                            'title' => __('Upgrade to STB Pro', 'wp-special-textboxes'),
                            'content' => self::getPointerContent(true),
                            'position' => ((is_rtl()) ? 'right' : 'left')
                        ],
                    ]);
                } */
            }
        }

        public function regAdminPage()
        {
            if (function_exists('add_options_page')) {
                $this->menu_page = add_options_page(
                    __('Special Text Boxes', 'wp-special-textboxes'),
                    __('STB', 'wp-special-textboxes'),
                    'manage_options',
                    'stb-settings',
                    [&$this, 'stbAdminPage']
                );
                add_action('admin_enqueue_scripts', [&$this, 'loadScripts']);
            }
        }

        public function stbAdminPage()
        {
            //echo phpinfo();
            ?>
            <div id="stb-admin-container" class="stb-admin-container"></div>
            <?php
        }

        public function addButtons()
        {
            // Don't bother doing this stuff if the current user lacks permissions
            if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
                return;
            }

            // Add only in Rich Editor mode
            if (get_user_option('rich_editing') == 'true') {
                add_filter("mce_external_plugins", [&$this, "addTinyMCEPlugin"]);
                add_filter('mce_buttons', [&$this, 'registerButton']);
            }
        }

        public function registerButton($buttons): array
        {
            array_push($buttons, "separator", "wstb");

            return $buttons;
        }

        public function addTinyMCEPlugin($plugin_array): array
        {
            $plugin_array['wstb'] = plugins_url('wp-special-textboxes/js/editor.plugin.js');

            return $plugin_array;
        }

        public function tinyMCEVersion($version): int
        {
            return ++$version;
        }
    }
}
?>