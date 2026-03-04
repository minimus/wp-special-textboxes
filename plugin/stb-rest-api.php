<?php
if (!defined('ABSPATH')) {
    exit;
}

include_once 'stb-tools.php';
include_once 'stb-db-tools.php';

const routeNamespace = 'stb/v6';

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
            register_rest_route(routeNamespace, '/admin/settings', [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'getSettings'],
                    'permission_callback' => [$this, 'adminAccess'],
                ], [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'setSettings'],
                    'permission_callback' => [$this, 'adminAccess'],
                ]
            ]);
            // ** SETTINGS ROUTE END **

            // ** STYLES ROUTE START **
            register_rest_route(routeNamespace, '/admin/styles/(?P<filter>\d+)', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getStyles'],
                'permission_callback' => [$this, 'adminAccess'],
                'args' => [
                    'filter' => [
                        'type' => 'integer',
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_integer((int)$param) && (int)$param >= 0 && (int)$param <= 2;
                        }
                    ],
                ],
            ]);
            // ** STYLES ROUTE END **

            // ** COLORS ROUTE START **
            register_rest_route(routeNamespace, '/admin/colors/(?P<slug>\S+)', [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'getColors'],
                    'permission_callback' => [$this, 'adminAccess'],
                    'args' => [
                        'slug' => [
                            'type' => 'string',
                            'required' => true,
                            'validate_callback' => function ($param, $request, $key) {
                                return self::validateColorNames($param, $request, $key);
                            }
                        ],
                    ],
                ], [
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => [$this, 'setColors'],
                    'permission_callback' => [$this, 'adminAccess'],
                    'args' => [
                        'slug' => [
                            'type' => 'string',
                            'required' => true,
                            'validate_callback' => function ($param, $request, $key) {
                                return preg_match('/^[\da-zA-Z\-_]+$/', $param) === 1;
                            }
                        ],
                    ],
                ], [
                    'methods' => WP_REST_Server::DELETABLE,
                    'callback' => [$this, 'deleteColors'],
                    'permission_callback' => [$this, 'adminAccess'],
                    'args' => [
                        'slug' => [
                            'type' => 'string',
                            'required' => true,
                            'validate_callback' => function ($param, $request, $key) {
                                return self::validateColorNames($param, $request, $key);
                            }
                        ],
                    ],
                ]
            ]);
            // ** COLORS ROUTE END **

            // ** THEMES ROUTE START **
            register_rest_route(routeNamespace, '/admin/themes', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getThemesInfo'],
                'permission_callback' => [$this, 'adminAccess'],
            ]);

            register_rest_route('stb/v6', '/admin/themes/(?P<slug>\S+)', [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'activateTheme'],
                'permission_callback' => [$this, 'adminAccess'],
                'args' => [
                    'slug' => [
                        'type' => 'string',
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return self::validateThemeName($param, $request, $key);
                        }
                    ],
                ],
            ]);
            // ** THEMES ROUTE END **

            // ** SYSINFO ROUTE START **
            register_rest_route(routeNamespace, '/admin/sysinfo', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getSysInfo'],
                'permission_callback' => [$this, 'adminAccess'],
            ]);
            // ** SYSINFO ROUTE END **

            // ** LOCALIZATION ROUTE START **
            register_rest_route('stb/v6', '/admin/locale', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getLocaleStrings'],
                'permission_callback' => [$this, 'adminAccess'],
            ]);
            // ** LOCALIZATION ROUTE END **

            // ** CLIENT SIDE THEME REQUEST START **
            register_rest_route(routeNamespace, '/theme/(?P<slug>\S+)', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'getTheme'],
                'permission_callback' => '__return_true',
                'args' => [
                    'slug' => [
                        'type' => 'string',
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return self::validateThemeName($param, $request, $key);
                        }
                    ],
                ],
            ]);
            // ** CLIENT SIDE THEME REQUEST END **
        }

        private function getCurrentSettings(): array
        {
            return get_option('SpecialTextBoxesAdminSettings', false);
        }

        public function adminAccess($request): bool
        {
            return current_user_can('manage_options');
        }

        public function validateThemeName($param, $request, $key): bool
        {
            include_once 'stb-default-themes.php';
            $defaultThemes = new StbDefaultThemes();
            $themes = $defaultThemes->getThemesNames();
            return in_array($param, $themes);
        }

        public function validateColorNames($param, $request, $key): bool
        {
            $names = $this->stbDbTools->getColorsNames();
            return in_array($param, $names);
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
            $filter = (int)$request['filter'];
            $sqlFilter = $filter === 0 ? NULL : $filter - 1;
            $data = $this->stbDbTools->getCurrentColors($sqlFilter);
            return ['data' => $data];
        }

        public function getColors($request): array
        {
            $slug = (string)$request['slug'];
            $data = $this->stbDbTools->getColor($slug);

            return count($data)
                ? ['result' => 'ok', 'data' => $data]
                : ['result' => 'error'];
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

        private function getThemeProps(string $slug): array
        {
            include_once 'stb-default-themes.php';
            $themeModel = new StbDefaultThemes();

            ['styles' => $themeStyles, 'settings' => $themeSettings] = $themeModel->getTheme($slug);
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

            return [
                'themeStyles' => $themeStyles,
                'colors' => $colors,
                'settings' => $themeSettings
            ];
        }

        public function activateTheme($request): array
        {
            include_once 'stb-styles.php';

            $slug = (string)$request['slug'];

            [
                'themeStyles' => $themeStyles,
                'settings' => $themeSettings,
                'colors' => $colors
            ] = self::getThemeProps($slug);

            $stbStyles = new StbStyles($themeSettings, $colors);
            $written = $stbStyles->writeStyles(false, true, true);

            $this->stbDbTools->updateStyles($themeStyles);
            update_option('SpecialTextBoxesAdminSettings', $themeSettings, true);

            return ['result' => 'ok', 'data' => $slug, 'written' => $written];
        }

        public function getTheme($request): array
        {
            $slug = (string)$request['slug'];
            ['colors' => $colors, 'settings' => $themeSettings] = self::getThemeProps($slug);

            return ['result' => 'ok', 'data' => ['colors' => $colors, 'settings' => $themeSettings]];
        }

        public function getSysInfo(): array
        {
            global $wpdb;

            $row = $wpdb->get_row('SELECT VERSION() AS ver', ARRAY_A);
            $sqlVersion = $row['ver'];
            $mem = ini_get('memory_limit');
            return ['data' => [
                'version' => STB_VERSION,
                'dbVersion' => STB_DB_VERSION,
                'phpVersion' => PHP_VERSION,
                'sqlVersion' => $sqlVersion,
                'memoryLimit' => $mem,
                'wpMemoryLimit' => WP_MEMORY_LIMIT,
                'wpMemoryLimitMax' => WP_MAX_MEMORY_LIMIT,
            ]];
        }

        public function getLocaleStrings(): array
        {
            require_once 'stb-locales.php';
            $localizer = new StbLocales();

            return ['result' => 'ok', 'data' => $localizer->getLocales()];
        }
    }
}
