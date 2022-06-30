<?php
if (!defined('ABSPATH')) {
    exit;
}

include_once 'stb-tools.php';
include_once 'stb-db-tools.php';

if (!class_exists('StbBlock')) {
    class StbBlock
    {
        private array $data = [
            'content' => null,
            'id' => 'warning',
            'caption' => '',
            'atts' => [],
            'idNum' => 0,
        ];
        private array $styles;
        private array $style;
        private array $settings;
        private object $stbTools;
        private object $stbDbTools;

        public string $block = '';

        public function __construct($content = null, $id = 'warning', $caption = '', $atts = null)
        {
            $this->stbTools = new StbTools();
            $this->stbDbTools = new StbDbTools();

            $this->data['content'] = $content;
            $this->data['id'] = $id;
            $this->data['caption'] = $caption;
            $this->data['atts'] = $atts ?? [];
            $this->data['idNum'] = rand(1111, 9999);

            $this->styles = self::getStyles();
            $this->style = self::getStyle($this->data['id']);
            $this->settings = self::getSettings();

            $this->block = self::buildBlock($this->data);
        }

        private function getSettings(): array
        {
            return $this->stbTools->getSettings();
        }

        private function getStyles(): array
        {
            return $this->stbDbTools->getCurrentColors(' WHERE trash IS FALSE');
        }

        private function getStyle(string $slug): array
        {
            $styles = array_values(array_filter($this->styles, fn(array $st) => $st['slug'] === $slug));
            return $styles[0];
        }

        private function prepareAttributeValue(string $value)
        {

            if ($value === 'true') return true;
            elseif ($value === 'false') return false;
            elseif ($value === 'default' || $value === '') return null;
            elseif (preg_match('/^\d+$/', $value)) return (int)$value;
            else return $value;
        }

        private function normalizeAttributes(array $atts): array
        {
            $id = $this->data['id'];
            $caption = $this->data['caption'];
            return shortcode_atts(
                [
                    'id' => $id,
                    'caption' => $caption,
                    'defcaption' => null,
                    'color' => null,
                    'ccolor' => null,
                    'bcolor' => null,
                    'bgcolor' => null,
                    'bgcolorto' => null,
                    'cbgcolor' => null,
                    'cbgcolorto' => null,
                    'bwidth' => null,
                    'image' => null,
                    'big' => null,
                    'float' => false,
                    'align' => 'left',
                    'width' => 200,
                    'collapsed' => null,
                    'mtop' => null,
                    'mleft' => null,
                    'mbottom' => null,
                    'mright' => null,
                    'direction' => null,
                    'collapsing' => null,
                    'shadow' => null,
                    'mode' => null,
                    'level' => 0
                ],
                array_map(fn(string $value) => self::prepareAttributeValue($value), $atts)
            );
        }

        private function getContainerClasses(): string
        {
            $classes = [];
            $atts = self::normalizeAttributes($this->data['atts']);
            $id = $this->data['id'];
            $bigImg = $atts['big'] ?? $this->settings['bigImg'];

            $classes[] = "stb-style-$id";
            $classes[] = $this->data['caption'] !== '' || $atts['defcaption'] ? 'stb-caption-box' : '';
            $classes[] = $this->settings['side'] === 0 ? 'stb-no-caption' : '';
            $classes[] = $bigImg ? '' : 'stb-image-small';
            $classes[] = ($atts['collapsed'] ?? false) ? 'stb-collapsed' : '';
            $classes[] = (isset($atts['collapsing']) && $atts['collapsing'] === false) ? 'stb-fixed' : '';

            return array_reduce($classes, fn($acc, $curr) => $curr === '' ? $acc : $acc . " $curr", 'stb-container');
        }

        private function getContainerStyle(): string
        {
            $style = [];
            $atts = self::normalizeAttributes($this->data['atts']);

            $mtop = esc_attr($atts['mtop']);
            $mright = esc_attr($atts['mright']);
            $mbottom = esc_attr($atts['mbottom']);
            $mleft = esc_attr($atts['mleft']);

            $style[] = isset($atts['mtop']) ? "margin-top:{$mtop}px" : '';
            $style[] = isset($atts['mright']) ? "margin-right:{$mright}px" : '';
            $style[] = isset($atts['mbottom']) ? "margin-bottom:{$mbottom}px" : '';
            $style[] = isset($atts['mleft']) ? "margin-left:{$mleft}px" : '';

            $result = count($style) ? array_reduce($style, fn($acc, $curr) => $curr === '' ? $acc : $acc . "$curr;", '') : '';
            return strlen($result) > 0 ? "style='$result'" : '';
        }

        private function getBodyStyle(): string
        {
            $style = [];
            $atts = self::normalizeAttributes($this->data['atts']);

            $color = esc_attr($atts['color']);
            $bgcolor = esc_attr($atts['bgcolor'] ?? $this->style['colors']['body']['background'][0]);
            $bgcolorto = esc_attr($atts['bgcolorto'] ?? $this->style['colors']['body']['background'][1]);

            $style[] = isset($atts['color']) ? "color:{$color}" : '';
            $style[] = (isset($atts['bgcolor']) || isset($atts['bgcolorto']))
                ? "background-image:linear-gradient(to bottom, $bgcolor 30%, $bgcolorto 90%)"
                : '';

            $result = count($style) ? array_reduce($style, fn($acc, $curr) => $curr === '' ? $acc : $acc . "$curr;", '') : '';
            return strlen($result) > 0 ? "style='$result'" : '';
        }

        private function getCaptionStyle(): string
        {
            $style = [];
            $atts = self::normalizeAttributes($this->data['atts']);

            $ccolor = esc_attr($atts['ccolor']);
            $cbgcolor = esc_attr($atts['cbgcolor'] ?? $this->style['colors']['caption']['background'][0]);
            $cbgcolorto = esc_attr($atts['cbgcolorto'] ?? $this->style['colors']['caption']['background'][1]);

            $style[] = isset($atts['ccolor']) ? "color:{$ccolor}" : '';
            $style[] = (isset($atts['cbgcolor']) || isset($atts['cbgcolorto']))
                ? "background-image:linear-gradient(to bottom, $cbgcolor 30%, $cbgcolorto 90%)"
                : '';

            $result = count($style) ? array_reduce($style, fn($acc, $curr) => $curr === '' ? $acc : $acc . "$curr;", '') : '';
            return strlen($result) > 0 ? "style='$result'" : '';
        }

        private function floatBoxWrapper(string $content): string
        {
            $atts = self::normalizeAttributes($this->data['atts']);

            $float = $atts['float'] ?? false;
            $align = esc_attr($atts['align'] ?? 'left');
            $width = esc_attr($atts['width'] ?? 200);

            $start = $float ? "<div class='stb-float' style='width: {$width}px; float: $align;'>" : '';
            $end = $float ? '</div>' : '';

            return $start . $content . $end;
        }

        public function buildBlock($data): string
        {
            $content = $data['content'];
            $atts = self::normalizeAttributes($data['atts']);
            $caption = esc_attr($atts['defcaption'] ? $this->style['caption'] : $data['caption'] ?? '');
            $iconSrc = $this->style['colors']['image']['enabled']
                ? $this->style['colors']['image']['image']
                : $this->style['colors']['image']['defaultImage'];
            $imageSrc = esc_attr($atts['image'] ?? $iconSrc);

            $classes = self::getContainerClasses();

            $containerStyle = self::getContainerStyle();
            $bodyStyle = self::getBodyStyle();
            $captionStyle = self::getCaptionStyle();

            return self::floatBoxWrapper(
                "<div class='$classes'$containerStyle>" .
                "<div class='stb-caption'$captionStyle>" .
                "<div class='stb-logo'><img class='stb-logo__image' src='$imageSrc' alt='img'/></div>" .
                "<div class='stb-caption-content'>$caption</div>" .
                "<div class='stb-tool'></div>" .
                '</div>' .
                "<div class='stb-content'$bodyStyle>" .
                do_shortcode($content) .
                '</div>' .
                '</div>');
        }

        public function getWidgetBox(array $data = null): array
        {
            $boxData = $data ?? $this->data;
            $iconSrc = $this->style['colors']['image']['enabled']
                ? $this->style['colors']['image']['image']
                : $this->style['colors']['image']['defaultImage'];
            $imageSrc = esc_attr($atts['image'] ?? esc_attr($iconSrc));

            $classes = self::getContainerClasses();

            return [
                'before' => "<div class='$classes stb-widget'>",
                'after' => '</div></div>',
                'beforeTitle' => "<div class='stb-caption'>" .
                    "<div class='stb-logo'><img class='stb-logo__image' src='$imageSrc' alt='img'/></div>" .
                    "<div class='stb-caption-content'>",
                'afterTitle' => "</div>" .
                    "<div class='stb-tool'></div>" .
                    '</div>' .
                    "<div class='stb-content'>"
            ];
        }
    }
}