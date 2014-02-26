<?php
if(!class_exists('StbBlock')) {
  class StbBlock {
    private $data = array(
      'content' => null,
      'id' => 'warning',
      'caption' => '',
      'atts' => array(),
      'idNum' => 0
    );
    private $styles = array();
    private $aStyles = array();
    
    public $block = '';
    public $widgetBlock;
    
    public function __construct($content = null, $id = 'warning', $caption = '', $atts = null) {
      $this->data['content'] = $content;
      $this->data['id'] = $id;
      $this->data['caption'] = $caption;
      $this->data['atts'] = (!is_null($atts)) ? $atts : array();
      $this->data['idNum'] = rand(1111, 9999);
      $styles = self::getStyles();
      $this->styles = $styles[0];
      $this->aStyles = $styles[1];
      
      $this->block = self::buildBlock($this->data);
      //$this->widgetBlock = self::getWidgetBox($this->data);
    }
    
    private function getSettings() {
      $settings = get_option(STB_OPTIONS, '');
      return $settings;
    }
    
    private function getStyles() {
      global $wpdb;
      $sTable = $wpdb->prefix . "stb_styles";
      $styles = array();
      $aStyles = array();
      
      if($wpdb->get_var("SHOW TABLES LIKE '$sTable'") == $sTable) {
        $sSql = "SELECT slug, caption, js_style, css_style, stype, trash FROM $sTable WHERE trash IS FALSE;";
        $rows = $wpdb->get_results($sSql, ARRAY_A);
        $style = array();
        //$aStyle = array();
        foreach($rows as $value) {
          $style['slug'] = $value['slug'];
          $style['name'] = $value['caption'];
          $style['stype'] = $value['stype'];
          $style['jsStyle'] = unserialize($value['js_style']);
          $style['cssStyle'] = unserialize($value['css_style']);

          if(!isset($style['cssStyle']['bgColorEnd'])) {
            $style['cssStyle']['bgColor'] = str_replace('#', '', $style['jsStyle']['color']);
            $style['cssStyle']['bgColorEnd'] = str_replace('#', '', $style['jsStyle']['colorTo']);
          }
          if(!isset($style['cssStyle']['captionBgColorEnd'])) {
            $style['cssStyle']['captionBgColor'] = str_replace('#', '', $style['jsStyle']['caption']['color']);
            $style['cssStyle']['captionBgColorEnd'] = str_replace('#', '', $style['jsStyle']['caption']['colorTo']);
          }

          array_push($styles, $style);

          $aStyles[$value['slug']] = array(
            'name' => $value['caption'],
            'stype' => $value['stype'],
            'jsStyle' => unserialize($value['js_style']),
            'cssStyle' => unserialize($value['css_style'])
          );

          if(!isset($aStyles[$value['slug']]['cssStyle']['bgColorEnd'])) {
            $aStyles[$value['slug']]['cssStyle']['bgColorEnd'] = str_replace('#', '', $aStyles[$value['slug']]['cssStyle']['bgColor']);
          }
          if(!isset($aStyles[$value['slug']]['cssStyle']['captionBgColorEnd'])) {
            $aStyles[$value['slug']]['cssStyle']['captionBgColorEnd'] = str_replace('#', '', $aStyles[$value['slug']]['cssStyle']['captionBgColor']);
          }
        }
      }
      return array($styles, $aStyles);
    }
    
    private function getClasses($value, $aa = false) {
      $classes = array();
      foreach($value as $val) {
        if($aa) $classes[$val['slug']] = $val['name'];
        else array_push($classes, $val['slug']);
      }
      return $classes;
    }
    
    private function getMode($val, $setsVal) {
      if(!empty($val)) $mode = $val;
      elseif($setsVal == 'mix') $mode =  'js';
      else $mode = 'css';
      if('css' == STB_DRAWING_MODE) $mode = 'css';
      return $mode;
    }

    private function getCssImage($id) {
      if(!empty($this->aStyles[$id]['cssStyle']['bigImg']))
        return $this->aStyles[$id]['cssStyle']['bigImg'];
      elseif(!empty($this->aStyles[$id]['cssStyle']['image']))
        return $this->aStyles[$id]['cssStyle']['image'];
      else return '';
    }

    private function getCssClasses( $atts = null ) {
      if(is_null($atts)) return '';

      $settings = self::getSettings();
      $cntClasses = array();

      if(is_array($atts)) {
        if($atts['defcaption'] == 'true') {
          $classes = $this->getClasses($this->styles, true);
          $atts['caption'] = $classes[$atts['id']];
        }

        if($atts['collapsing'] === 'default') $collapsing = ($settings['collapsing'] === 'true');
        else $collapsing = ($atts['collapsing'] === 'true');
        $collapsed = ($settings['collapsing'] === 'true') && (($atts['collapsed'] === 'true') || (($settings['collapsed'] === 'true') && ($atts['collapsed'] !== 'false')));

        $side = (isset($settings['side'])) ? $settings['side'] : false;
        if(!empty($atts['side'])) $side = ($atts['side'] == 'true');

        // Collapsing and Collapsed
        if(!empty($atts['caption'])) {
          if($collapsing) {
            array_push($cntClasses, 'stb-collapsible');
            if($collapsed) $value = 'stb-hidden';
            else $value = 'stb-visible';
            array_push($cntClasses, $value);
          }
          else {
            array_push($cntClasses, 'stb-fixed');
            array_push($cntClasses, 'stb-visible');
          }
        }

        // Image Size
        $value = (($settings['bigImg'] === 'true') ? 'stb-image-big' : 'stb-image-small');
        if(!empty($atts['big'])) {
          if($atts['big'] != $settings['bigImg']) $value = (($atts['big'] == 'true') ? 'stb-image-big' : 'stb-image-small');
        }
        if($atts['image'] === 'null') $value = 'stb-image-none';
        array_push($cntClasses, $value);

        // Language Direct
        $value = (!empty($atts['direction'])) ? 'stb-'.$atts['direction'] : 'stb-'.$settings['langDirect'];
        array_push($cntClasses, $value);

        // Rounded Corners
        if($settings['rounded_corners'] === 'true') array_push($cntClasses, 'stb-corners');

        // Shadow
        $value = '';
        if($settings['box_shadow'] === 'true') $value = 'stb-shadow';
        if(strlen($atts['shadow']) > 0 && $atts['shadow'] != $settings['box_shadow']) {
          if($atts['shadow'] == 'true') $value = 'stb-shadow';
          else $value = '';
        }
        if(!empty($value)) array_push($cntClasses, $value);

        // Border
        if(($settings['border_style'] != 'none') || !empty($atts['bwidth']))
          array_push($cntClasses, 'stb-border');

        // Side Caption
        if($atts['caption'] == '') {
          if($side) array_push($cntClasses, 'stb-side');
          else array_push($cntClasses, 'stb-side-none');
        }
      }

      return $cntClasses;
    }

    private function getCssStyles($atts = null, $idNum = 0) {
      if(is_null($atts)) return '';

      $settings = self::getSettings();
      $sImg = self::getCssImage($atts['id']); //$this->aStyles[$atts['id']]['cssStyle']['bigImg'];
      $mode = '';

      $toolImg = '';
      $iconImg = '';

      $body = '';
      $cap = '';
      $cnt = '';
      $float = '';

      if(is_array($atts)) {
        $mode = self::getMode($atts['mode'], $settings['mode']);

        if($atts['collapsing'] === 'default') $collapsing = ($settings['collapsing'] === 'true');
        else $collapsing = ($atts['collapsing'] === 'true');
        $collapsed = ($settings['collapsing'] === 'true') && (($atts['collapsed'] === 'true') || (($settings['collapsed'] === 'true') && ($atts['collapsed'] !== 'false')));

        // Float Mode
        if (( $atts['float'] === 'true' ) && in_array($atts['align'], array('left', 'right')) ) {
          $float = "<div style='float: {$atts['align']}; width: {$atts['width']}px;' >";
        }

        // Tool Image
        $toolImg = ($collapsing) ? '<div id="stb-tool-'.$idNum.'" class="stb-tool"><img id="stb-toolimg-'.$idNum.'" src="'.(($collapsed) ? $settings['js_imgPlus'].'" title="'.__('Show', STB_DOMAIN) : $settings['js_imgMinus'].'" title="'.__('Hide', STB_DOMAIN)).'" /></div>'  : '';

        // Icon Image
        if(!empty($atts['image']) && ($atts['image'] != 'null')) {
          if(empty($atts['caption'])) $iconImg = "<aside class='stb-icon'><img src='{$atts['image']}'></aside>";
          else $iconImg = "<aside class='stb-caption-icon'><img src='{$atts['image']}'></aside>";
        }
        else {
          if(empty($atts['caption'])) $iconImg = "<aside class='stb-icon'><img src='{$sImg}'></aside>";
          else $iconImg = "<aside class='stb-caption-icon'><img src='{$sImg}'></aside>";
        }

        // Custom Styles
        // Caption
        if(!empty($atts['cbgcolor']) || !empty($atts['cbgcolorto'])) {
          $c1 = (empty($atts['cbgcolor'])) ? $this->aStyles[$atts['id']]['cssStyle']['captionBgColor'] : $atts['cbgcolor'];
          $c2 = (empty($atts['cbgcolorto'])) ? $this->aStyles[$atts['id']]['cssStyle']['captionBgColorEnd'] : $atts['cbgcolorto'];
          if(empty($atts['caption'])) $cnt = "background-image: linear-gradient(#{$c1} 30%, #{$c2} 90%);";
          else $cap = "background-image: linear-gradient(#{$c1} 30%, #{$c2} 90%);";
        }
        if(!empty($atts['ccolor'])) $cap .= "color: #{$atts['ccolor']};";

        // Body
        if(!empty($atts['bgcolor']) || !empty($atts['bgcolorto'])) {
          $c1 = (empty($atts['bgcolor'])) ? $this->aStyles[$atts['id']]['cssStyle']['bgColor'] : $atts['bgcolor'];
          $c2 = (empty($atts['bgcolorto'])) ? $this->aStyles[$atts['id']]['cssStyle']['bgColorEnd'] : $atts['bgcolorto'];
          $body = "background-image: linear-gradient(#{$c1} 30%, #{$c2} 90%);";
        }
        if(!empty($atts['color'])) $body .= "color: #{$atts['color']};";

        // Container
        if(!empty($atts['bwidth']) || !empty($atts['bcolor'])) {
          $width = (!empty($atts['bwidth'])) ? $atts['bwidth'] : 1;
          $style = ($settings['border_style'] != 'none') ? $settings['border_style'] : 'solid';
          $color = (!empty($atts['bcolor'])) ? $atts['bcolor'] : $this->aStyles[$atts['id']]['cssStyle']['borderColor'];
          $cnt .= "border: {$width}px {$style} #{$color};";
        }
        if(strlen($atts['mtop']) > 0 || strlen($atts['mleft']) > 0 || strlen($atts['mbottom']) > 0 || strlen($atts['mright']) > 0) {
          $top = (strlen($atts['mtop']) > 0) ? $atts['mtop'] : $settings['top_margin'];
          $right = (strlen($atts['mright']) > 0) ? $atts['mright'] : $settings['right_margin'];
          $bottom = (strlen($atts['mbottom']) > 0) ? $atts['mbottom'] : $settings['bottom_margin'];
          $left = (strlen($atts['mleft']) > 0) ? $atts['mleft'] : $settings['left_margin'];

          $cnt .= "margin: {$top}px {$right}px {$bottom}px {$left}px;";
        }
      }

      return array(
        'mode' => $mode,
        'floatStart' => $float,
        'floatEnd' => ((empty($float)) ? '' : "</div>"),
        'cnt' => ((!empty($cnt)) ? " style='{$cnt}'" : ''),
        'cap' => ((!empty($cap)) ? " style='{$cap}'" : ''),
        'body' => ((!empty($body)) ? " style='{$body}'" : ''),
        'tool' => $toolImg,
        'icon' => $iconImg
      );
    }
    
    private function getJsStyles($atts = null, $idNum = 0) {
      if(is_null($atts)) return '';
      
      $settings = $this->getSettings();

      $floatStart = '';
      $floatEnd = '';
      //var_dump($atts);
      /*if($atts['defcaption'] == 'true') {
        $classes = $this->getClasses($this->styles, true);
        $atts['caption'] = $classes[$atts['id']];
      }
      
      if($atts['collapsing'] === 'default') $collapsing = ($settings['collapsing'] === 'true');
      else $collapsing = ($atts['collapsing'] === 'true'); 
      $collapsed = ($settings['collapsing'] === 'true') && (($atts['collapsed'] === 'true') || (($settings['collapsed'] === 'true') && ($atts['collapsed'] !== 'false')));
      */
      //$mode = self::getMode($atts['mode'], $settings['mode']);
      //$mode = $atts['mode'];
      
      /*if($mode == 'js') {
        if(is_array($atts)) {*/
          // Float Mode
          if (( $atts['float'] === 'true' ) && in_array($atts['align'], array('left', 'right')) ) {
            $floatStart = "<div style='float:{$atts['align']}; width:{$atts['width']}px;' >";
            $floatEnd = '</div>';
          }
          
          // Caption
          $stbData = '';
          $stbCaption = '';
          $stbBorder = '';
          $stbOpts = '';
          $stbShadow = '';
          if(!empty($atts['caption'])) {
            $stbCaption = 'text: "'.str_replace("'", '’', $atts['caption']).'"';
            if(!empty($atts['collapsed'])) $stbCaption .= ", collapsed: ".$atts['collapsed'];
            if($atts['collapsing'] != 'default') $stbCaption .= ", collapsing: ".$atts['collapsing'];
            if(!empty($atts['ccolor'])) $stbCaption .= ', fontColor: "#'.$atts['ccolor'].'"';
            if(!empty($atts['cbgcolor'])) $stbCaption .= ', color: "#'.$atts['cbgcolor'].'"';
            if(!empty($atts['cbgcolorto'])) $stbCaption .= ', colorTo: "#'.$atts['cbgcolorto'].'"';
          }
          if(!empty($atts['bwidth'])) {
            $stbBorder = 'width: '.$atts['bwidth'];
            if(!empty($atts['bcolor'])) $stbBorder .= ', color: "#'.$atts['bcolor'].'"';
          }
          if(!empty($atts['shadow'])) {
            $stbShadow = 'enabled: '.$atts['shadow'];
          }
          if(strlen($atts['mtop']) > 0) $stbOpts .= ", mtop: ".$atts['mtop'];
          if(strlen($atts['mright']) > 0) $stbOpts .= ", mright: ".$atts['mright'];
          if(strlen($atts['mbottom']) > 0) $stbOpts .= ", mbottom: ".$atts['mbottom'];
          if(strlen($atts['mleft']) > 0) $stbOpts .= ", mleft: ".$atts['mleft'];
          if(!empty($atts['color'])) $stbOpts .= ', fontColor: "#'.$atts['color'].'"';
          if(!empty($atts['bgcolor'])) $stbOpts .= ', color: "#'.$atts['bgcolor'].'"';
          if(!empty($atts['bgcolorto'])) $stbOpts .= ', colorTo: "#'.$atts['bgcolorto'].'"';
          if(!empty($atts['direction'])) $stbOpts .= ', direction: "'.$atts['direction'].'"';
          if(!empty($atts['image'])) $stbOpts .= ', image: '.(($atts['image'] == 'null') ? 'null' : '"'.$atts['image'].'"');
          
          if(!empty($stbCaption)) $stbData = "caption: {{$stbCaption}}";
          if(!empty($stbBorder)) {
            if(!empty($stbData)) $stbData .= ", border: {{$stbBorder}}";
            else $stbData = "border: {{$stbBorder}}";
          }
          if(!empty($stbOpts)) {
            if(!empty($stbData)) $stbData .= $stbOpts;
            else $stbData = substr_replace($stbOpts, '', 0, 2);
          }
          if(!empty($stbShadow)) {
            if(!empty($stbData)) $stbData .= ", shadow: {{$stbShadow}}";
            else $stbData = "shadow: {{$stbShadow}}";
          }
          if(!empty($stbData)) $stbData = "{{$stbData}}";
          
          return array(
            //'mode' => $mode,
            'floatStart' => $floatStart,
            'floatEnd' => $floatEnd,
            'data' => (!empty($stbData)) ? "data-stb='$stbData'" : ''
          );
        /*}
      }*/
      /*return array(
        //'mode' => $mode,
        'floatStart' => $floatStart,
        'floatEnd' => $floatEnd,
        'data' => (!empty($stbData)) ? "data-stb='$stbData'" : ''
      );*/
    }

    public function getWidgetBox($data = null) {
      $boxData = (is_null($data)) ? $this->data : $data;
      $id = $boxData['id'];
      $caption = $boxData['caption'];
      $defaults = array(
        'id' => $id,
        'caption' => $caption,
        'defcaption' => 'false',
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
        'mleft' => '0',
        'mbottom' => '',
        'mright' => '0',
        'direction' => '',
        'collapsing' => 'default',
        'shadow' => 'false',
        'mode' => '',
        'level' => 0
      );
      $out = array();
      if(is_null($boxData)) $atts = $defaults;
      else $atts = shortcode_atts($defaults, $boxData['atts']);

      $idNum = $boxData['idNum'];

      $settings = self::getSettings();
      if(isset($atts['mode'])) $mode = self::getMode($atts['mode'], $settings['mode']);
      else $mode = self::getMode('', $settings['mode']);
      $atts['mode'] = $mode;

      if($atts['defcaption'] == 'true') {
        $classes = $this->getClasses($this->styles, true);
        $atts['caption'] = $classes[$atts['id']];
        $caption = $atts['caption'];
      }

      //$stbClasses = $this->getClasses($this->styles);

      if($mode == 'js') {
        $block = self::getJsStyles($atts, $idNum);
        $out = array(
          'before' => "<div id='stb-box-{$idNum}' class='stb-box-jq stb-{$id}-box stb-level-{$atts['level']}' {$block['data']}>",
          'after' => "</div>",
          'beforeTitle' => '',
          'afterTitle' => ''
        );
      }
      elseif($mode == 'css') {
        $cssClasses = self::getCssClasses($atts);
        $cntClasses = implode(' ', $cssClasses);
        $cssBlock = self::getCssStyles($atts, $idNum);
        $cntStart = "<div id='stb-container-{$idNum}' class='stb-container-css stb-{$id}-container {$cntClasses}'{$cssBlock['cnt']}>";
        $cntEnd = '</div>';
        if($caption == '') {
          $out = array(
            'before' => $cntStart.$cssBlock['icon']."<div id='stb-box-{$idNum}' class='stb-{$id}_box stb-box' {$cssBlock['body']}>",
            'after' => "</div>".$cntEnd,
            'beforeTitle' => '',
            'afterTitle' => ''
          );
        }
        else {
          $out = array(
            'before' => $cntStart,
            'after' => "</div>{$cntEnd}",
            'beforeTitle' => "<div id='stb-caption-box-$idNum' class='stb-$id-caption_box stb_caption stb-caption-box' {$cssBlock['cap']}>{$cssBlock['icon']}{$cssBlock['tool']}",
            'afterTitle' => "</div><div id='stb-body-box-$idNum' class='stb-$id-body_box stb_body stb-body-box' {$cssBlock['body']}>"
          );
        }
      }

      //var_dump($out);
      return $out;
    }
    
    private function buildBlock($data) {
      $content = $data['content'];
      $id = $data['id'];
      $caption = $data['caption'];
      $atts = shortcode_atts( array( 
          'id' => $id,
          'caption' => $caption,
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
          'shadow' => '',
          'mode' => '',
          'level' => 0 ),
        $data['atts']
      );
      $idNum = $data['idNum'];

      $settings = $this->getSettings();
      $mode = self::getMode($atts['mode'], $settings['mode']);

      if($atts['defcaption'] == 'true') {
        $classes = $this->getClasses($this->styles, true);
        $atts['caption'] = $classes[$atts['id']];
        $caption = $atts['caption'];
      }
      
      $stbClasses = $this->getClasses($this->styles);
      //$block = array('body' => '', 'caption' => '', 'floatStart' => '', 'floatEnd' => '');
      //$cntStart = "<div id='stb-container-{$idNum}' class='stb-container-css stb-{$id}-container'>";
      //$cntEnd = '</div>';
      
      /*if (!is_null($atts) && is_array($atts)) {
        $block = self::getJsStyles($atts, $idNum);
      } else return do_shortcode($content);*/
      if($mode == 'js') {
        $block = self::getJsStyles($atts, $idNum);
        if($id == 'grey')
          return
            $block['floatStart'].
            //"<div id='stb-box-{$idNum}-container' class='stb-container'><canvas id='stb-box-{$idNum}-canvas'></canvas>".
            "<div id='stb-box-{$idNum}' class='stb-box-jq stb-{$id}-box stb-level-{$atts['level']}' {$block['data']}>{$content}</div>".
            //"</div>".
            $block['floatEnd'];
        else
          return
            $block['floatStart'].
            //"<div id='stb-box-{$idNum}-container' class='stb-container'><canvas id='stb-box-{$idNum}-canvas'></canvas>".
            "<div id='stb-box-{$idNum}' class='stb-box-jq stb-{$id}-box stb-level-{$atts['level']}' {$block['data']}>" . do_shortcode($content) . "</div>".
            //"</div>".
            $block['floatEnd'];
      }
      elseif($mode == 'css') {
        $cssClasses = self::getCssClasses($atts);
        $cntClasses = implode(' ', $cssClasses);
        $cssBlock = self::getCssStyles($atts, $idNum);
        $cntStart = "<div id='stb-container-{$idNum}' class='stb-container-css stb-{$id}-container {$cntClasses}'{$cssBlock['cnt']}>";
        $cntEnd = '</div>';
        if ( $caption === '') {
          if ( in_array( $id, $stbClasses) && $id !== 'grey' ) {
            return $cssBlock['floatStart'].$cntStart.$cssBlock['icon']."<div id='stb-box-{$idNum}' class='stb-{$id}_box stb-box' {$cssBlock['body']}>" . do_shortcode($content) . "</div>".$cntEnd.$cssBlock['floatEnd'];
          } elseif ( in_array( $id, $stbClasses) && $id === 'grey' ) {
            return $cssBlock['floatStart'].$cntStart.$cssBlock['icon']."<div id='stb-box-$idNum' class='stb-{$id}_box stb-box' {$cssBlock['body']}>$content</div>".$cntEnd.$cssBlock['floatEnd'];
          } else { 
            return do_shortcode($content);  
          }
        } else {
          if ( in_array( $id, $stbClasses ) && $id !== 'grey' ) {
            return $cssBlock['floatStart']. $cntStart ."<div id='stb-caption-box-$idNum' class='stb-$id-caption_box stb_caption stb-caption-box' {$cssBlock['cap']}>{$cssBlock['icon']}{$cssBlock['tool']}{$caption}</div><div id='stb-body-box-$idNum' class='stb-$id-body_box stb_body stb-body-box' {$cssBlock['body']}>" . do_shortcode($content) . "</div>". $cntEnd .$cssBlock['floatEnd'];
          } elseif ( in_array( $id, $stbClasses) && $id === 'grey' ) {
            return $cssBlock['floatStart']. $cntStart ."<div id='stb-caption-box-$idNum' class='stb-$id-caption_box stb_caption stb-caption-box' {$cssBlock['cap']}>{$cssBlock['icon']}{$cssBlock['tool']}{$caption}</div><div id='stb-body-box-$idNum' class='stb-$id-body_box stb_body stb-body-box' {$cssBlock['body']}>$content</div>".$cntEnd.$cssBlock['floatEnd'];
          } else { 
            return do_shortcode($content);  
          }
        }
      }
      else return do_shortcode($content);
    }
  }
}
?>