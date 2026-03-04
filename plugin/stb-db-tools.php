<?php
/**
 * Created by PhpStorm.
 * Author: minimus
 * Date: 14.09.2019
 * Time: 9:12
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once 'stb-default-themes.php';

if (!class_exists('StbDbTools')) {
    class StbDbTools
    {
        private string $sTable = '';
        private string $osTable = '';
        private array $defaultColors;
        private array $defaultImages;

        public function __construct($sTable = 'stb_stylez', $osTable = 'stb_styles')
        {
            global $wpdb;

            $stbDefaultThemes = new StbDefaultThemes();

            $this->sTable = $wpdb->prefix . $sTable;
            $this->osTable = $wpdb->prefix . $osTable;
            $this->defaultColors = $stbDefaultThemes->getTheme('stb-dark')['styles'];
            $this->defaultImages = $stbDefaultThemes->getThemeDefaultImages('stb-dark');
        }

        protected function isSetValue($valid, $invalid)
        {
            if (isset($valid)) return $valid;
            return $invalid;
        }

        public function tableExists($table): bool
        {
            global $wpdb;

            return $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
        }

        public function createStylesTable()
        {
            global $wpdb;
            $charset = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $this->sTable (
                 slug varchar(55) NOT NULL,
                 caption varchar(255) NOT NULL,
                 colors text DEFAULT NULL,
                 stbType varchar(8) DEFAULT NULL,
                 trash tinyint(1) DEFAULT 0,
                 PRIMARY KEY (slug)
                ) $charset;";
            dbDelta($sql);
        }

        public function convertStyles(): array
        {
            global $wpdb;

            $getSql = "SELECT slug, caption, css_style, stype, trash FROM $this->osTable;";
            $rows = $wpdb->get_results($getSql, ARRAY_A);
            $styles = [];
            foreach ($rows as $row) {
                $cssStyles = unserialize($row['css_style']);
                $style = [
                    'slug' => $row['slug'],
                    'caption' => $row['caption'],
                    'colors' => serialize([
                        'body' => [
                            'color' => '#' . $cssStyles['color'],
                            'background' => array('#' . $cssStyles['bgColor'], '#' . $cssStyles['bgColorEnd']),
                        ],
                        'border' => [
                            'color' => '#' . $cssStyles['borderColor'],
                        ],
                        'caption' => [
                            'color' => '#' . $cssStyles['captionColor'],
                            'background' => array('#' . $cssStyles['captionBgColor'], '#' . $cssStyles['captionBgColorEnd']),
                        ],
                        'image' => [
                            'image' => strpos($cssStyles['image'], STB_URL) === 0 ? '' : $cssStyles['image'],
                            'defaultImage' => $this->defaultImages[$row['slug']],
                            'enabled' => !(strpos($cssStyles['image'], STB_URL) === 0),
                        ],
                    ]),
                    'stbType' => $row['stype'],
                    'trash' => (int)$row['trash'],
                ];
                $styles[] = $style;
                $wpdb->insert($this->sTable, $style, ['%s', '%s', '%s', '%s', '%d']);
            }
            return $styles;
        }

        private function insertDefaultStyles(): void
        {
            global $wpdb;

            foreach ($this->defaultColors as $row) {
                $wpdb->insert($this->sTable, $row, ['%s', '%s', '%s', '%s', '%d']);
            }
        }

        public function updateStyles($styles): void
        {
            global $wpdb;

            foreach ($styles as $style) {
                $wpdb->replace($this->sTable, $style, ['%s', '%s', '%s', '%s', '%d']);
            }
        }

        public function upgradeDb(): bool
        {
            if (!self::tableExists($this->sTable)) {
                self::createStylesTable();
                if (self::tableExists($this->osTable)) {
                    self::convertStyles();
                } else {
                    self::insertDefaultStyles();
                }
            }

            return self::tableExists($this->sTable);
        }

        public function getCurrentColors($filter = NULL): array
        {
            global $wpdb;

            $sTable = $wpdb->prefix . "stb_stylez";
            $sSql = is_null($filter) ? "SELECT * FROM $sTable st" : $wpdb->prepare("SELECT * FROM $sTable st WHERE st.trash = %d", [$filter]);
            $colors = $wpdb->get_results($sSql, ARRAY_A);
            $data = [];
            foreach ($colors as $color) {
                $data[] = [
                    'slug' => $color['slug'],
                    'type' => $color['stbType'],
                    'caption' => $color['caption'],
                    'colors' => unserialize($color['colors']),
                    'trash' => (int)$color['trash']
                ];
            }

            return $data;
        }

        public function getColor(string $slug): array
        {
            global $wpdb;
            $sTable = $wpdb->prefix . "stb_stylez";
            $sSql = $wpdb->prepare("SELECT * FROM $sTable st WHERE st.slug = %s;", [$slug]);
            $colorSet = $wpdb->get_row($sSql, ARRAY_A);

            return $colorSet
                ? [
                    'slug' => $colorSet['slug'],
                    'type' => $colorSet['stbType'],
                    'caption' => $colorSet['caption'],
                    'colors' => unserialize($colorSet['colors']),
                    'trash' => (int)$colorSet['trash']
                ]
                : [];
        }

        public function getColorsNames(): array {
            global $wpdb;
            $sTable = $wpdb->prefix . "stb_stylez";
            $sSql = "SELECT st.slug FROM $sTable st;";
            return $wpdb->get_col($sSql);
        }

        public function deleteTables(bool $all = true): void
        {
            global $wpdb;

            $wpdb->query("DROP TABLE IF EXISTS {$this->sTable};");
            if ($all) {
                $wpdb->query("DROP TABLE IF EXISTS {$this->osTable};");
            }
            delete_option('stb_db_version');
        }
    }
}
