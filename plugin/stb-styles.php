<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('StbStyles')) {
    class StbStyles
    {
        private array $settings;
        private array $colors;


        private string $coreFile = STB_DIR . 'css/stb-core.css';
        private string $commonFile = STB_DIR . 'css/stb-common.css';
        private string $colorsFile = STB_DIR . 'css/stb-colors.css';

        public function __construct($settings, $colors)
        {
            $this->settings = $settings;
            $this->colors = $colors;
        }

        private function getMargins(array $margins): string
        {
            $top = $margins['top'] ?? 10;
            $right = $margins['right'] ?? 10;
            $bottom = $margins['bottom'] ?? 10;
            $left = $margins['left'] ?? 10;

            return "{$top}px {$right}px {$bottom}px {$left}px";
        }

        private function getShadow(string $target, array $shadow): string
        {
            $inset = '';
            if ($target === 'box') {
                $inset = $shadow['inset'] ? 'inset ' : '';
            }
            $offsetX = $shadow['offsetX'] ?? 0;
            $offsetY = $shadow['offsetY'] ?? 0;
            $blur = $shadow['blur'] ?? 0;
            $color = $shadow['color'] ?? '#000';

            if (!$shadow['enabled']) return 'unset';
            else return "$inset{$offsetX}px {$offsetY}px {$blur}px $color";
        }

        public function getCoreStyles(): string
        {
            return '.stb-container {' .
                '-webkit-box-sizing: border-box;' .
                'box-sizing: border-box;' .
                'display: -ms-flexbox;' .
                'display: flex;' .
                'padding: 0;' .
                'overflow: hidden' .
                '}' .

                '.stb-container .stb-caption {' .
                '-ms-flex-order: 1;' .
                'display: -ms-flexbox;' .
                'display: flex;' .
                '-ms-flex-direction: column;' .
                'flex-direction: column;' .
                'order: 1' .
                '}' .

                '.stb-container .stb-caption .stb-logo {' .
                '-ms-flex-pack: center;' .
                '-ms-flex-align: center;' .
                '-ms-flex-order: 1;' .
                'align-items: center;' .
                'display: -ms-flexbox;' .
                'display: flex;' .
                'height: 60px;' .
                'justify-content: center;' .
                'order: 1;' .
                'width: 60px' .
                '}' .

                '.stb-container .stb-caption .stb-logo .stb-logo__image {' .
                'height: 50px;' .
                'width: 50px' .
                '}' .

                '.stb-container .stb-caption .stb-caption-content {' .
                '-ms-flex-order: 2;' .
                'display: none;' .
                'order: 2' .
                '}' .

                '.stb-container .stb-caption .stb-tool {' .
                '-ms-flex-order: 3;' .
                'display: none;' .
                'order: 3' .
                '}' .

                '.stb-container .stb-content {' .
                '-ms-flex-order: 2;' .
                'order: 2;' .
                'width: 100%' .
                '}' .

                '.stb-container .stb-content p:first-child {' .
                '-webkit-margin-before: 0;' .
                'margin-block-start: 0' .
                '}' .

                '.stb-container .stb-content p:last-child {' .
                '-webkit-margin-after: 0;' .
                'margin-block-end: 0' .
                '}' .

                '.stb-container.stb-image-small .stb-caption .stb-logo {' .
                'height: 30px;' .
                'width: 30px' .
                '}' .

                '.stb-container.stb-image-small .stb-caption .stb-logo .stb-logo__image {' .
                'height: 25px;' .
                'width: 25px' .
                '}' .

                '.stb-container.stb-image-none .stb-caption {' .
                'display: none' .
                '}' .
                '.stb-container.stb-caption-box {' .
                '-ms-flex-direction: column;' .
                'flex-direction: column' .
                '}' .

                '.stb-container.stb-caption-box .stb-caption {' .
                '-ms-flex-align: center;' .
                'align-items: center;' .
                'display: -ms-flexbox;' .
                'display: flex;' .
                '-ms-flex-direction: row;' .
                'flex-direction: row;' .
                'padding: 0 3px' .
                '}' .

                '.stb-container.stb-caption-box .stb-caption .stb-logo {' .
                '-ms-flex-pack: center;' .
                '-ms-flex-align: center;' .
                '-ms-flex-order: 1;' .
                'align-items: center;' .
                'display: -ms-flexbox;' .
                'display: flex;' .
                'height: 27px;' .
                'justify-content: center;' .
                'order: 1;' .
                'width: 27px' .
                '}' .

                '.stb-container.stb-caption-box .stb-caption .stb-logo .stb-logo__image {' .
                'height: 25px;' .
                'width: 25px' .
                '}' .

                '.stb-container.stb-caption-box .stb-caption .stb-caption-content {' .
                '-ms-flex-order: 2;' .
                'display: inherit;' .
                'order: 2;' .
                'padding: 0 3px;' .
                'width: 100%' .
                '}' .

                '.stb-container.stb-caption-box .stb-caption .stb-tool {' .
                '-ms-flex-order: 3;' .
                'cursor: pointer;' .
                'display: inherit;' .
                'height: 27px;' .
                'justify-self: flex-end;' .
                'order: 3;' .
                'width: 27px' .
                '}' .

                '.stb-container.stb-caption-box .stb-content {' .
                'overflow: hidden;' .
                '-webkit-transition: all .3s linear;' .
                '-o-transition: all .3s linear;' .
                'transition: all .3s linear;' .
                'width: 100%;' .
                'will-change: transform' .
                '}' .

                '.stb-container.stb-caption-box .stb-content p:first-child {' .
                '-webkit-margin-before: 0;' .
                'margin-block-start: 0' .
                '}' .

                '.stb-container.stb-caption-box .stb-content p:last-child {' .
                '-webkit-margin-after: 0;' .
                'margin-block-end: 0' .
                '}' .

                '.stb-container.stb-caption-box.stb-fixed .stb-caption .stb-tool {' .
                'display: none' .
                '}' .

                '.stb-container.stb-caption-box.stb-collapsed .stb-content {' .
                'line-height: unset;' .
                'font-size: 0' .
                '}' .

                '.stb-container.stb-caption-box.stb-collapsed .stb-content p {' .
                'line-height: unset;' .
                '}' .

                '.stb-container.stb-caption-box.stb-collapsed .stb-content img {' .
                'height: 0;' .
                'width: 0' .
                '}';
        }

        public function getCommonStyles(): string
        {
            $radius = $this->settings['roundedCorners'] ? $this->settings['radius'] : 0;
            $boxShadow = self::getShadow('box', $this->settings['shadow']);
            $textShadow = self::getShadow('text', $this->settings['text']['shadow']);
            $margins = self::getMargins($this->settings['margins']);
            $imgMinus = $this->settings['imgMinus']['enabled'] ? $this->settings['imgMinus']['image'] : $this->settings['imgMinus']['defaultImage'];
            $imgPlus = $this->settings['imgPlus']['enabled'] ? $this->settings['imgPlus']['image'] : $this->settings['imgPlus']['defaultImage'];
            $fontSize = ($this->settings['text']['font']['fontSize'] ?? false) ? "{$this->settings['text']['font']['fontSize']}px" : 'unset';
            $fontFamily = ($this->settings['text']['font']['fontFamily'] ?? false) ? $this->settings['text']['font']['fontFamily'] : 'unset';
            $captionFontSize = ($this->settings['caption']['font']['fontSize'] ?? false) ? "{$this->settings['caption']['font']['fontSize']}px" : 'unset';
            $captionFontFamily = ($this->settings['caption']['font']['fontFamily'] ?? false) ? $this->settings['caption']['font']['fontFamily'] : 'unset';

            return '.stb-container {' .
                "border-radius: {$radius}px;" .
                "-webkit-box-shadow: $boxShadow;" .
                "box-shadow: $boxShadow;" .
                "margin: $margins" .
                '}' .

                '.stb-container.stb-widget {' .
                'margin-left: 0;' .
                'margin-right: 0;' .
                'box-shadow: none' .
                '}' .

                '.stb-container .stb-caption .stb-caption-content {' .
                "font-size: $captionFontSize;" .
                "font-family: $captionFontFamily" .
                '}' .

                '.stb-container .stb-caption .stb-tool {' .
                'background-color: transparent;' .
                "background-image: url($imgMinus);" .
                'background-position: 50%;' .
                'background-repeat: no-repeat' .
                '}' .

                '.stb-container .stb-content {' .
                'padding: 10px;' .
                "font-size: $fontSize;" .
                "font-family: $fontFamily;" .
                "text-shadow: $textShadow" .
                '}' .

                '.stb-container.stb-collapsed .stb-caption .stb-tool {' .
                "background-image: url($imgPlus)" .
                '}' .

                '.stb-container.stb-collapsed .stb-content {' .
                'padding-bottom: 0;' .
                'padding-top: 0' .
                '}' .

                '.stb-container.stb-no-caption, .stb-container.stb-no-caption.stb-ltr {' .
                'direction: ltr' .
                '}' .

                '.stb-container.stb-no-caption:not(.stb-caption-box) .stb-content,' .
                '.stb-container.stb-no-caption.stb-ltr:not(.stb-caption-box) .stb-content {' .
                'padding: 10px 10px 10px 0' .
                '}' .

                '.stb-container.stb-no-caption.stb-rtl {' .
                'direction: rtl' .
                '}' .

                '.stb-container.stb-no-caption.stb-rtl:not(.stb-caption-box) .stb-content {' .
                'padding: 10px 0 10px 10px' .
                '}';
        }

        public function getColorStyle(array $colors): string
        {
            $slug = $colors['slug'];
            $color = $colors['colors']['body']['color'] ?? '#000';
            $borderStyle = $this->settings['borderStyle'] ?? 'solid';
            $borderWidth = $this->settings['borderWidth'] ?? 1;
            $borderColor = $colors['colors']['border']['color'] ?? '#000';
            $backgroundBody0 = $colors['colors']['body']['background'][0] ?? '#fff';
            $backgroundBody1 = $colors['colors']['body']['background'][1] ?? '#fff';

            $captionColor = $colors['colors']['caption']['color'] ?? '#000';
            $backgroundCaption0 = $colors['colors']['caption']['background'][0] ?? '#fff';
            $backgroundCaption1 = $colors['colors']['caption']['background'][1] ?? '#fff';


            return ".stb-container.stb-style-$slug {" .
                "color: $color;" .
                "border: {$borderWidth}px $borderStyle $borderColor;" .
                "background-image: -webkit-gradient(linear, left top, left bottom, color-stop(30%, $backgroundBody0), color-stop(90%, $backgroundBody1));" .
                "background-image: -o-linear-gradient(top, $backgroundBody0 30%, $backgroundBody1 90%);" .
                "background-image: linear-gradient(180deg, $backgroundBody0 30%, $backgroundBody1 90%);" .
                '}' .

                ".stb-container.stb-style-$slug .stb-caption {" .
                "color: $captionColor;" .
                "background-image: -webkit-gradient(linear, left top, left bottom, color-stop(30%, $backgroundCaption0), color-stop(90%, $backgroundCaption1));" .
                "background-image: -o-linear-gradient(top, $backgroundCaption0 30%, $backgroundCaption1 90%);" .
                "background-image: linear-gradient(180deg, $backgroundCaption0 30%, $backgroundCaption1 90%);" .
                '}' .

                ".stb-container.stb-style-$slug.stb-no-caption:not(.stb-caption-box) .stb-caption {" .
                "background-image: -webkit-gradient(linear, left top, left bottom, color-stop(30%, $backgroundBody0), color-stop(90%, $backgroundBody1));" .
                "background-image: -o-linear-gradient(top, $backgroundBody0 30%, $backgroundBody1 90%);" .
                "background-image: linear-gradient(180deg, $backgroundBody0 30%, $backgroundBody1 90%);" .
                '}';
        }

        public function getColorStyles(): string
        {
            $styles = '';
            foreach ($this->colors as $color) {
                $styles .= self::getColorStyle($color);
            }
            return $styles;
        }

        public function getStyles(): array
        {
            return [
                'core' => self::getCoreStyles(),
                'common' => self::getCommonStyles(),
                'colors' => self::getColorStyles(),
            ];
        }

        public function writeStyles(bool $core, bool $common, bool $colors): array
        {
            $styles = self::getStyles();

            $coreResult = [];
            $commonResult = [];
            $colorsResult = [];

            if ($core) {
                if (is_writable($this->coreFile) || !file_exists($this->coreFile)) {
                    if ($handle = fopen($this->coreFile, 'w')) {
                        fwrite($handle, $styles['core']);
                        fclose($handle);
                        $coreResult = ['core' => ['result' => true]];
                    } else {
                        $coreResult = [
                            'core' => [
                                'result' => false,
                                'error' => __("Can't open CSS file", 'wp-special-textboxes'),
                                'file' => $this->coreFile
                            ]
                        ];
                    }
                } else {
                    $coreResult = [
                        'core' => [
                            'result' => false,
                            'error' => __("CSS file is not writable", 'wp-special-textboxes'),
                            'file' => $this->coreFile
                        ]
                    ];
                }
            }

            if ($common) {
                if (is_writable($this->commonFile) || !file_exists($this->commonFile)) {
                    if ($handle = fopen($this->commonFile, 'w')) {
                        fwrite($handle, $styles['common']);
                        fclose($handle);
                        $commonResult = ['common' => ['result' => true]];
                    } else {
                        $commonResult = [
                            'common' => [
                                'result' => false,
                                'error' => __("Can't open CSS file", 'wp-special-textboxes'),
                                'file' => $this->commonFile
                            ]
                        ];
                    }
                } else {
                    $commonResult = [
                        'common' => [
                            'result' => false,
                            'error' => __("CSS file is not writable", 'wp-special-textboxes'),
                            'file' => $this->commonFile
                        ]
                    ];
                }
            }

            if ($colors) {
                if (is_writable($this->colorsFile) || !file_exists($this->colorsFile)) {
                    if ($handle = fopen($this->colorsFile, 'w')) {
                        fwrite($handle, $styles['colors']);
                        fclose($handle);
                        $colorsResult = ['colors' => ['result' => true]];
                    } else {
                        $colorsResult = [
                            'colors' => [
                                'result' => false,
                                'error' => __("Can't open CSS file", 'wp-special-textboxes'),
                                'file' => $this->colorsFile
                            ]
                        ];
                    }
                } else {
                    $colorsResult = [
                        'colors' => [
                            'result' => false,
                            'error' => __("CSS file is not writable", 'wp-special-textboxes'),
                            'file' => $this->colorsFile
                        ]
                    ];
                }
            }

            return array_merge($coreResult, $commonResult, $colorsResult);
        }

        public function updateCSS(): array
        {
            $core = !file_exists($this->coreFile);
            $common = !file_exists($this->commonFile);
            $colors = !file_exists($this->colorsFile);

            return self::writeStyles($core, $common, $colors);
        }
    }
}
