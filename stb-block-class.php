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
    
    public $block = '';
    
    public function __construct($content = null, $id = 'warning', $caption = '', $atts = null) {
      $this->data['content'] = $content;
      $this->data['id'] = $id;
      $this->data['caption'] = $caption;
      $this->data['atts'] = $atts;
      $this->data['idNum'] = rand(1, 9999);
      $this->styles = $this->getStyles();
      
      $this->block = $this->buildBlock($this->data);
    }
    
    private function getSettings() {
      $settings = get_option(STB_OPTIONS, '');
      return $settings;
    }
    
    private function getStyles() {
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
    
    private function getClasses($value, $aa = false) {
      $classes = array();
      foreach($value as $val) {
        if($aa) $classes[$val['slug']] = $val['name'];
        else array_push($classes, $val['slug']);
      }
      return $classes;
    }
    
    private function getMode($val, $sval) {
      if(!empty($val)) $mode = $val;
      else $mode = ($sval == 'mix') ? 'js' : $sval;
      if('css' == STB_DRAWING_MODE) $mode = 'css';
      return $mode;
    }
    
    private function extendedStyleLogic($atts = null, $idNum = 0) {
      if(is_null($atts)) return '';
      
      $settings = $this->getSettings();
      
      $styleStart = 'style="';
      $styleBody = '';
      $styleCaption = '';
      $styleEnd = '"';
      $floatStart = '';
      $floatEnd = '';
      
      if($atts['defcaption'] == 'true') {
        $classes = $this->getClasses($this->styles, true);
        $atts['caption'] = $classes[$atts['id']];
      }
      
      if($atts['collapsing'] === 'default') $collapsing = ($settings['collapsing'] === 'true');
      else $collapsing = ($atts['collapsing'] === 'true'); 
      $collapsed = ($settings['collapsing'] === 'true') && (($atts['collapsed'] === 'true') || (($settings['collapsed'] === 'true') && ($atts['collapsed'] !== 'false')));
      
      $mode = self::getMode($atts['mode'], $settings['mode']);
      
      if($mode == 'js') {
        if(is_array($atts)) {
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
          if(!empty($atts['mtop'])) $stbOpts .= ", mtop: ".$atts['mtop'];
          if(!empty($atts['mright'])) $stbOpts .= ", mright: ".$atts['mright'];
          if(!empty($atts['mbottom'])) $stbOpts .= ", mbottom: ".$atts['mbottom'];
          if(!empty($atts['mleft'])) $stbOpts .= ", mleft: ".$atts['mleft'];
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
            'mode' => $mode,
            'floatStart' => $floatStart,
            'floatEnd' => $floatEnd,
            'data' => (!empty($stbData)) ? "data-stb='$stbData'" : ''
          );
        }
      }
      
      if ( is_array($atts) ) {
        $needResizing = ( ( $atts['big'] !== '' ) & ( $atts['big'] !==  $settings['bigImg'] ) );
        $direction = ($atts['direction'] != '') ? $atts['direction'] : $settings['langDirect'];
        
        // Float Mode
        if (( $atts['float'] === 'true' ) && in_array($atts['align'], array('left', 'right')) ) {
          $floatStart = "<div style='float:{$atts['align']}; width:{$atts['width']}px;' >";
          $floatEnd = '</div>';
        }
        
        // Body style 
        $styleBody .= ( $atts['color'] === '' ) ? '' : "color:#{$atts['color']}; ";
        $styleBody .= ( $atts['bcolor'] === '' ) ? '' : "border-top-color: #{$atts['bcolor']}; border-left-color: #{$atts['bcolor']}; border-right-color: #{$atts['bcolor']}; border-bottom-color: #{$atts['bcolor']}; ";
        $styleBody .= ( $atts['bgcolor'] === '' ) ? '' : "background-color: #{$atts['bgcolor']}; ";
        if(($atts['direction'] != '') && ($atts['direction'] != $settings['langDirect'])) {
          $styleBody .= "direction: $direction; ";
          $styleBody .= "text-align: ".(($direction == 'rtl') ? "right" : "left")."; ";
        }
        if(!empty( $atts['shadow'] )) {
          if($atts['shadow'] === 'true') $styleBody .= "-webkit-box-shadow: 3px 3px 3px #888; -moz-box-shadow: 3px 3px 3px #888; box-shadow: 3px 3px 3px #888;";
          else $styleBody .= "-webkit-box-shadow: none; -moz-box-shadow: none; box-shadow: none;";
        }
      
        // Caption style
        $styleCaption .= ( $atts['ccolor'] === '' ) ? '' : "color:#{$atts['ccolor']}; ";
        $styleCaption .= ( $atts['bcolor'] === '' ) ? '' : "border-top-color: #{$atts['bcolor']}; border-left-color: #{$atts['bcolor']}; border-right-color: #{$atts['bcolor']}; border-bottom-color: #{$atts['bcolor']}; ";
        $styleCaption .= ( $atts['cbgcolor'] === '' ) ? '' : "background-color: #{$atts['cbgcolor']}; ";
        if(($atts['direction'] != '') && ($atts['direction'] != $settings['langDirect'])) {
          $styleCaption .= "direction: $direction; ";
          $styleCaption .= "text-align: ".(($direction == 'rtl') ? "right" : "left")."; ";
        }
        if(!empty( $atts['shadow'] )) {
          if($atts['shadow'] === 'true') $styleCaption .= "-webkit-box-shadow: 3px 3px 3px #888; -moz-box-shadow: 3px 3px 3px #888; box-shadow: 3px 3px 3px #888;";
          else $styleCaption .= "-webkit-box-shadow: none; -moz-box-shadow: none; box-shadow: none;";
        }
        
        // Tool Image
        
        $toolImg = ($collapsing) ? '<div id="stb-tool-'.$idNum.'" class="stb-tool" style="float:'.(($direction === 'ltr')?'right':'left').'; padding:0px; margin:0px auto"><img id="stb-toolimg-'.$idNum.'" style="border: none; background-color: transparent; padding: 0px; margin: 0px auto;" src="'.STB_URL.(($collapsed) ? 'images/show.png" title="'.__('Show', STB_DOMAIN) : 'images/hide.png" title="'.__('Hide', STB_DOMAIN)).'" /></div>'  : '';
        
        // Image logic
        
        if ($atts['caption'] === '') {
          if ($atts['image'] === '') {
            if ($needResizing & ($settings['showImg'] === 'true')) {
              if (!in_array($atts['id'], array('custom', 'grey'))) {
                $styleBody .= ( $atts['big'] === 'true' ) ? "background-image: url(".STB_URL.'images/'."{$atts['id']}-b.png); " : "background-image: url(".STB_URL.'images/'."{$atts['id']}.png); ";
                $styleBody .= ( $atts['big'] === 'true' ) ? 'min-height: 40px; padding-'.(($direction === 'ltr')?'left':'right').': 50px; ' : 'min-height: 20px; padding-'.(($direction === 'ltr')?'left':'right').': 25px; ';
              } elseif ($atts['id'] === 'custom') {
                $styleBody .= ( $atts['big'] === 'true' ) ? "background-image: url({$settings['cb_bigImg']}); " : "background-image: url({$settings['cb_image']}); ";
                $styleBody .= ( $atts['big'] === 'true' ) ? 'min-height: 40px; padding-'.(($direction === 'ltr')?'left':'right').': 50px; ' : 'min-height: 20px; padding-'.(($direction === 'ltr')?'left':'right').': 25px; ';
              } else {
                $styleBody .= 'min-height: 20px; padding-'.(($direction === 'ltr')?'left':'right').': 5px; ';
              }              
            }
            if (($atts['direction'] != '') && ($atts['direction'] != $settings['langDirect'])) {
              $styleBody .= "padding-".(($direction === 'rtl')?'left':'right').": 5px; ";
              $styleBody .= "padding-".(($direction === 'ltr')?'left':'right').": 50px; ";
              $styleBody .= "background-position:top ".(($direction === 'rtl')?'right':'left')."; ";
            }
          } elseif ($atts['image'] === 'null') {
            $styleBody .= 'background-image: url(none); min-height: 20px; padding-'.(($direction === 'ltr')?'left':'right').': 5px; ';
          } else {
            $styleBody .= "background-image: url({$atts['image']}); ";
            if ($needResizing || ($settings['showImg'] === 'false')) $styleBody .= ( $atts['big'] === 'true' ) ? 'min-height: 40px; padding-'.(($direction === 'ltr')?'left':'right').': 50px; ' : 'min-height: 20px; padding-'.(($direction === 'ltr')?'left':'right').': 25px; ';
            if (($atts['direction'] != '') && ($atts['direction'] != $settings['langDirect'])) {
              $styleBody .= "padding-".(($direction === 'rtl')?'left':'right').": 5px; ";
              $styleBody .= "padding-".(($direction === 'ltr')?'left':'right').": 50px; ";
              $styleBody .= "background-position:top ".(($direction === 'rtl')?'right':'left')."; ";
            }
          }
          if(($atts['mtop'] !== '') || ($atts['mleft'] !== '') || ($atts['mbottom'] !== '') || ($atts['mright'] !== '')) {
            $mTop = ($atts['mtop'] !== '') ? $atts['mtop'] : $settings['top_margin'];
            $mLeft = ($atts['mleft'] !== '') ? $atts['mleft'] : $settings['left_margin'];
            $mBottom = ($atts['mbottom'] !== '') ? $atts['mbottom'] : $settings['bottom_margin'];
            $mRight = ($atts['mright'] !== '') ? $atts['mright'] : $settings['right_margin'];
            $styleBody .= "margin: {$mTop}px {$mRight}px {$mBottom}px {$mLeft}px; ";
          }
        } else {          
          if ( $collapsed ) {
            $styleBody .= 'display: none; ';
            $styleCaption .= '-webkit-border-bottom-left-radius: 5px; -webkit-border-bottom-right-radius: 5px; -moz-border-radius-bottomleft: 5px; -moz-border-radius-bottomright: 5px; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px; ';
            if(($atts['mtop'] !== '') || ($atts['mleft'] !== '') || ($atts['mbottom'] !== '') || ($atts['mright'] !== '')) {
              $mTop = ($atts['mtop'] !== '') ? $atts['mtop'] : $settings['top_margin'];
              $mLeft = ($atts['mleft'] !== '') ? $atts['mleft'] : $settings['left_margin'];
              $mBottom = ($atts['mbottom'] !== '') ? $atts['mbottom'] : $settings['bottom_margin'];
              $mRight = ($atts['mright'] !== '') ? $atts['mright'] : $settings['right_margin'];
              $styleBody .= "margin: 0px {$mRight}px {$mBottom}px {$mLeft}px; ";
              $styleCaption .= "margin: {$mTop}px {$mRight}px {$mBottom}px {$mLeft}px; ";
            }
          } else {
            if(($atts['mtop'] !== '') || ($atts['mleft'] !== '') || ($atts['mbottom'] !== '') || ($atts['mright'] !== '')) {
              $mTop = ($atts['mtop'] !== '') ? $atts['mtop'] : $settings['top_margin'];
              $mLeft = ($atts['mleft'] !== '') ? $atts['mleft'] : $settings['left_margin'];
              $mBottom = ($atts['mbottom'] !== '') ? $atts['mbottom'] : $settings['bottom_margin'];
              $mRight = ($atts['mright'] !== '') ? $atts['mright'] : $settings['right_margin'];
              $styleBody .= "margin: 0px {$mRight}px {$mBottom}px {$mLeft}px; ";
              $styleCaption .= "margin: {$mTop}px {$mRight}px 0px {$mLeft}px; ";
            }
          }           
          if ( $atts['image'] !== '')
            $styleCaption .= ( $atts['image'] === 'null' ) ? "background-image: url(none); padding-".(($direction === 'ltr')?'left':'right').": 5px; " : "background-image: url({$atts['image']}); padding-".(($direction === 'ltr')?'left':'right').": 25px; ";
          if (($atts['direction'] != '') && ($atts['direction'] != $settings['langDirect'])) {
            $styleCaption .= "padding-".(($direction === 'rtl')?'left':'right').": 5px; ";
            $styleCaption .= "padding-".(($direction === 'ltr')?'left':'right').": 25px; ";
            $styleCaption .= "background-position:top ".(($direction === 'rtl')?'right':'left')."; ";
          }
        }
        
        return array(
          'mode' => $mode,
          'body' => ( $styleBody !== '' ) ? $styleStart.$styleBody.$styleEnd : '', 
          'caption' => ( $styleCaption !== '' ) ? $styleStart.$styleCaption.$styleEnd : '',
          'floatStart' => $floatStart,
          'floatEnd' => $floatEnd,
          'toolImg' => $toolImg
        );
      }
      else return '';
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
                $data['atts']);
      $idNum = $data['idNum'];
      
      if($atts['defcaption'] == 'true') {
        $classes = $this->getClasses($this->styles, true);
        $atts['caption'] = $classes[$atts['id']];
        $caption = $atts['caption'];
      }
      
      $stbClasses = $this->getClasses($this->styles);
      $block = array('body' => '', 'caption' => '', 'floatStart' => '', 'floatEnd' => '');
      $cntStart = "<div id='stb-container-$idNum' class='stb-container'>";
      $cntEnd = '</div>';
      
      if (!is_null($atts) && is_array($atts)) {
        $block = $this->extendedStyleLogic($atts, $idNum);
      } else return do_shortcode($content);
      if($block['mode'] == 'js') {
        if($id == 'grey')
          return $block['floatStart']."<div id='stb-box-$idNum' class='stb-$id-box stb-level-{$atts['level']}' {$block['data']}>$content</div>".$block['floatEnd'];
        else
          return $block['floatStart']."<div id='stb-box-$idNum' class='stb-$id-box stb-level-{$atts['level']}' {$block['data']}>" . do_shortcode($content) . "</div>".$block['floatEnd'];
      }
      else {
        if ( $caption === '') {
          if ( in_array( $id, $stbClasses) && $id !== 'grey' ) {
            return $block['floatStart']."<div id='stb-box-$idNum' class='stb-{$id}_box' {$block['body']}>" . do_shortcode($content) . "</div>".$block['floatEnd'];
          } elseif ( in_array( $id, $stbClasses) && $id === 'grey' ) {
            return $block['floatStart']."<div id='stb-box-$idNum' class='stb-{$id}_box' {$block['body']}>$content</div>".$block['floatEnd'];
          } else { 
            return do_shortcode($content);  
          }
        } else {
          if ( in_array( $id, $stbClasses ) && $id !== 'grey' ) {
            return $block['floatStart']. $cntStart ."<div id='stb-caption-box-$idNum' class='stb-$id-caption_box stb_caption' {$block['caption']}>" . $caption . $block['toolImg'] . "</div><div id='stb-body-box-$idNum' class='stb-$id-body_box stb_body' {$block['body']}>" . do_shortcode($content) . "</div>". $cntEnd .$block['floatEnd'];
          } elseif ( in_array( $id, $stbClasses) && $id === 'grey' ) {
            return $block['floatStart']."<div id='stb-caption-box-$idNum' class='stb-$id-caption_box' {$block['caption']}>$caption</div><div id='stb-body-box-$idNum' class='stb-$id-body_box' {$block['body']}>$content</div>".$block['floatEnd'];
          } else { 
            return do_shortcode($content);  
          }
        }
      }
    }
  }
}
?>