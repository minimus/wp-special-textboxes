<?php
if (!defined('ABSPATH')) {
    exit;
}

include_once 'stb-tools.php';
include_once 'stb-db-tools.php';

if (!class_exists('StbRestApi')) {
    class StbRestApi
    {
        public object $currentUser;
        public object $stbTools;
        public object $stbDbTools;

        public function __construct($currentUser)
        {
            $this->currentUser = $currentUser;
            $this->stbTools = new StbTools();
            $this->stbDbTools = new StbDbTools();
        }

        public function setAdminRoutes(): void
        {
            // ** SETTINGS ROUTE START **
            register_rest_route('stb/v6', '/admin/settings', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'getSettings'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);

            register_rest_route('stb/v6', '/admin/settings', [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'setSettings'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);
            // ** SETTINGS ROUTE END **

            // ** STYLES ROUTE START **
            register_rest_route('stb/v6', '/admin/styles/(?P<filter>\d+)', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'getStyles'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);
            // ** STYLES ROUTE END **

            // ** COLORS ROUTE START **
            register_rest_route('stb/v6', '/admin/colors/(?P<slug>\S+)', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'getColors'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);

            register_rest_route('stb/v6', '/admin/colors/(?P<slug>\S+)', [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'setColors'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);

            register_rest_route('stb/v6', '/admin/colors/(?P<slug>\S+)', [
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'deleteColors'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);
            // ** COLORS ROUTE END **

            // ** THEMES ROUTE START **
            register_rest_route('stb/v6', '/admin/themes', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'getThemesInfo'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);

            register_rest_route('stb/v6', '/admin/themes/(?P<slug>\S+)', [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'activateTheme'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);
            // ** THEMES ROUTE END **

            // ** SYSINFO ROUTE START **
            register_rest_route('stb/v6', '/admin/sysinfo', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'getSysInfo'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);
            // ** SYSINFO ROUTE END **

            // ** LOCALIZATION ROUTE START **
            register_rest_route('stb/v6', '/admin/locale', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'getLocaleStrings'),
                'permission_callback' => array($this, 'adminAccess'),
            ]);
            // ** LOCALIZATION ROUTE END **
        }

        private function getCurrentSettings(): array {
            return get_option('SpecialTextBoxesAdminSettings', false);
        }

        public function adminAccess($request): bool
        {
            return current_user_can('manage_options');
        }

        public function getSettings($data): array
        {
            $settings = $this->stbTools->getSettings();
            if (!$settings) {
                $options = get_option('SpecialTextBoxesAdminOptions', false);
                if (!$options) {
                    require_once 'stb-default-themes.php';
                    $stbDefaultThemes = new StbDefaultThemes();
                    $settings = $stbDefaultThemes->getTheme('stb-dark')['settings'];
                } else {
                    $settings = $this->stbTools->convertOptions($options);
                }
            }
            return ['settings' => $settings];
        }

        public function setSettings($request): array
        {
            include_once 'stb-styles.php';
            $settings = $request->get_json_params();
            if (!$settings) {
                return ['success' => false];
            } else {
                update_option('SpecialTextBoxesAdminSettings', $settings, true);
                $colors = $this->stbDbTools->getCurrentColors();
                $stbStyles = new StbStyles($settings, $colors);
                $written = $stbStyles->writeStyles(false, true, true);
                return ['success' => true, 'data' => $settings, 'written' => $written];
            }
        }

        public function getStyles($request): array
        {
            $filter = (string)$request['filter'];
            $sqlFilter = '';
            if ($filter != '0') {
                $sqlFilter = ($filter == '2') ? ' WHERE st.trash = 1' : ' WHERE st.trash = 0';
            }
            $data = $this->stbDbTools->getCurrentColors($sqlFilter);
            return ['data' => $data];
        }

        public function getColors($request): array
        {
            $slug = (string)$request['slug'];

            global $wpdb;
            $sTable = $wpdb->prefix . "stb_stylez";
            $sSql = $wpdb->prepare("SELECT * FROM $sTable st WHERE st.slug = %s;", [$slug]);
            $colorSet = $wpdb->get_row($sSql, ARRAY_A);

            return $colorSet
                ? [
                    'result' => 'ok',
                    'data' => [
                        'slug' => $colorSet['slug'],
                        'type' => $colorSet['stbType'],
                        'caption' => $colorSet['caption'],
                        'colors' => unserialize($colorSet['colors']),
                        'trash' => (int)$colorSet['trash']
                    ]]
                : [
                    'result' => 'error'
                ];
        }

        public function setColors($request): array
        {
            include_once 'stb-styles.php';

            global $wpdb;

            $sTable = $wpdb->prefix . "stb_stylez";

            $slug = (string)$request['slug'];
            $colorSet = $request->get_json_params();

            $result = $wpdb->replace($sTable, [
                'slug' => $slug,
                'stbType' => $colorSet['type'],
                'caption' => $colorSet['caption'],
                'colors' => serialize($colorSet['colors']),
                'trash' => (int)$colorSet['trash']
            ], ['%s', '%s', '%s', '%s', '%d']);

            if (!$result && $result !== 0) {
                return ['result' => 'error'];
            } else {
                $stbDbTools = new StbDbTools();
                $settings = self::getCurrentSettings();
                $colors = $stbDbTools->getCurrentColors();
                $stbStyles = new StbStyles($settings, $colors);
                $written = $stbStyles->writeStyles(false, true, true);
                return ['result' => 'ok', 'completed' => $result, 'written' => $written];
            }
        }

        public function deleteColors($request): array
        {
            global $wpdb;
            $sTable = $wpdb->prefix . "stb_stylez";

            $slug = (string)$request['slug'];

            $result = $wpdb->delete($sTable, ['slug' => $slug], '%s');

            if (!$result && $result !== 0) {
                return ['result' => 'error'];
            } else {
                return ['result' => 'ok', 'deleted' => $result];
            }
        }

        public function getThemesInfo(): array
        {
            include_once 'stb-default-themes.php';

            $themesModel = new StbDefaultThemes();
            return ['data' => $themesModel->getThemesInfo()];
        }

        public function activateTheme($request): array
        {
            include_once 'stb-default-themes.php';
            include_once 'stb-db-tools.php';
            include_once 'stb-styles.php';

            $slug = (string)$request['slug'];
            $themesModel = new StbDefaultThemes();
            $dbTools = new StbDbTools();

            $theme = $themesModel->getTheme($slug);
            $themeStyles = $theme['styles'];
            $themeSettings = $theme['settings'];

            $colors = [];

            foreach ($themeStyles as $style) {
                $colors[] = [
                    'slug' => $style['slug'],
                    'type' => $style['stbType'],
                    'caption' => $style['caption'],
                    'colors' => unserialize($style['colors']),
                    'trash' => (int)$style['trash']
                ];
            }

            $stbStyles = new StbStyles($themeSettings, $colors);
            $written = $stbStyles->writeStyles(false, true, true);

            $dbTools->updateStyles($themeStyles);
            update_option('SpecialTextBoxesAdminSettings', $themeSettings, true);

            return ['result' => 'ok', 'data' => $slug, 'written' => $written];
        }

        public function getSysInfo(): array
        {
            global $wpdb;

            $row = $wpdb->get_row('SELECT VERSION() AS ver', ARRAY_A);
            $sqlVersion = $row['ver'];
            $mem = ini_get('memory_limit');
            return array('data' => [
                'version' => STB_VERSION,
                'dbVersion' => STB_DB_VERSION,
                'phpVersion' => PHP_VERSION,
                'sqlVersion' => $sqlVersion,
                'memoryLimit' => $mem,
                'wpMemoryLimit' => WP_MEMORY_LIMIT,
                'wpMemoryLimitMax' => WP_MAX_MEMORY_LIMIT,
            ]);
        }

        public function getLocaleStrings(): array
        {
            require_once 'stb-locales.php';
            $localizer = new StbLocales();

            return ['result' => 'ok', 'data' => $localizer->getLocales()];
        }
    }
}