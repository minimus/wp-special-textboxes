/**
 * @name Special Text Boxes
 * @author minimus
 * @copyright 2009 - 2013
 * @version 4.4.75
 */
(function($) {
  var defaultThemes = {
    alert: {
      image: 'alert-b.png',      
      color: '#fdcbc9',
      colorTo: '#fb7d78',
      fontColor: '#000000',
      border: {
        width: 5,
        color: '#f9574f'
      },
      caption: {
        fontColor: '#ffffff',
        color: '#1d1a1a',
        colorTo: '#504848'//'#eee0e0'
      }
    },
    black: {
      image: 'earth-b.png',
      color: '#3b3b3b',
      colorTo: '#000000',
      fontColor: '#ffffff',
      border: {
        width: 0,
        color: '#535353'
      },
      caption: {
        fontColor: '#ffffff',
        color: '#1d1a1a',
        colorTo: '#504848'//'#eee0e0'
      }
    },    
    download: {
      image: 'download-b.png',
      color: '#78c0f7',
      colorTo: '#2e7cb9',
      fontColor: '#000000',
      border: {
        width: 5,
        color: '#15609a'
      },
      caption: {
        fontColor: '#ffffff',
        color: '#1d1a1a',
        colorTo: '#504848'//'#eee0e0'
      }
    },
    info: {
      image: 'info-b.png',
      color: '#a1ea94',
      colorTo: '#79b06e',
      fontColor: '#000000',
      border: {
        width: 0,
        color: '#6c9c62'
      },
      caption: {
        fontColor: '#ffffff',
        color: '#1d1a1a',
        colorTo: '#504848'//'#eee0e0'
      }
    },
    warning: {
      image: 'warning-2-b.png',
      color: '#f8fc91', //'#fade7d',
      colorTo: '#f0d208', //'#fe9001'
      fontColor: '#000000',
      border: {
        width: 0,
        color: '#d9be08'
      },
      caption: {
        fontColor: '#ffffff',
        color: '#1d1a1a',
        colorTo: '#504848'//'#eee0e0'
      } 
    }/*,
    custom: {
      image: 'heart-b.png',
      color: '#f7cdf5',
      colorTo: '#f77df1',
      fontColor: '#000000',
      border: {
        width: 3,
        color: '#f844ee'
      },
      caption: {
        fontColor: '#ffffff',
        color: '#1d1a1a',
        colorTo: '#504848'//'#eee0e0'
      }
    }*/
  },
  
  defaultOptions = {
    caption: {
      text: '',
      fontFamily: 'Impact, Verdana',
      fontSize: 12,
      collapsed: false,
      collapsing: true,
      imgMinus: 'minus.png',
      imgPlus: 'plus.png',
      duration: 500
    },
    imgX: 5,
    imgY: 10,
    radius: 10,
    direction: "ltr",
    mtop: 10,
    mright: 10,
    mbottom: 10,
    mleft: 10,
    safe: false,
    shadow: {
      enabled: true,
      offsetX: 7,
      offsetY: 7,
      blur: 5,
      alpha: 0.15,
      color: '#000000' //'#676767'
    },
    textShadow: {
      enabled: true,
      offsetX: 1,
      offsetY: 1,
      blur: 3,
      alpha: 0.15,
      color: '#000000' //'#676767'
    },
    handlers: [
      {hClass: 'a[id^=tabs]', hEvent: 'click'},
      {hClass: 'li[id^=tabs]', hEvent: 'click'}
    ]
  },
  
  stbItems = [];
  
  function getRandInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }
  
  function log() {
    log.history = log.history || [];
    log.history.push(arguments);
    
    // Make sure console is present
    if('object' === typeof console) {

      // Setup console and arguments
      var c = console[ console.warn ? 'warn' : 'log' ],
      args = Array.prototype.slice.call(arguments), a;

      // Add qTip2 marker to first argument if it's a string
      if(typeof arguments[0] === 'string') { args[0] = 'qTip2: ' + args[0]; }

      // Apply console.warn or .log if not supported
      a = c.apply ? c.apply(console, args) : c(args);
    }
  }
  
  function stbThemesStream() {
    this.themes = [];
    this.getIndex = function(name) {
      var index = -1;
      $.each(this.themes, function(i, el) {
        if(el.name == name) {
          index = i;
          return false;
        } else return true;
      });
      return index;
    };
    this.getTheme = function(name) {
      return this.themes[this.getIndex(name)];
    };
    this.register = function(name, theme) {
      this.themes.push(theme);
      this.themes[this.themes.length-1].name = name;
    };
    this.update = function(name, udata) {
      var data;
      $.extend(true, this.themes[this.getIndex(name)], udata);
    };
  }
  
  stbThemes = new stbThemesStream();
  stbThemes.register('alert', defaultThemes.alert);
  stbThemes.register('black', defaultThemes.black);
  stbThemes.register('download', defaultThemes.download);
  stbThemes.register('info', defaultThemes.info);
  stbThemes.register('warning', defaultThemes.warning);
  //stbThemes.register('custom', defaultThemes.custom);
  
  function getRGB(color) {
    var clr = color.replace('#', '');
    var r = parseInt('0x'+clr.slice(0,2));
    var g = parseInt('0x'+clr.slice(2,4));
    var b = parseInt('0x'+clr.slice(4,6));
    return r.toString()+','+g.toString()+','+b.toString();
  }
  
  function buildOptions(theme, options) {
    var 
      settings = {},
      sets = {},
      opts = {};
    
    if(typeof(options) == 'undefined') {
      opts = defaultOptions;
    }
    else 
      if((typeof(options) == 'object') && null != options) {
        opts = defaultOptions;
        $.extend(true, sets, opts, options);
        opts = sets;
      }
      else opts = defaultOptions;
    
    if(typeof(theme) == 'string') {      
      if(-1 == stbThemes.getIndex(theme)) $.extend(true, settings, stbThemes.getTheme('warning'), opts);
      else $.extend(true, settings, stbThemes.getTheme(theme), opts);
    }
    else 
      if((typeof(theme) == 'object') && (null != theme)) {
        $.extend(true, settings, stbThemes.getTheme('warning'), opts, theme); 
      }
      else $.extend(true, settings, stbThemes.getTheme('warning'), opts);
    return settings;  
  }
  
  function roundedShadow(ctx, width, height, opts) {
    var 
      sh = opts.shadow.blur,
      ox = opts.shadow.offsetX,
      oy = opts.shadow.offsetY;
    //var i = 0;
    for (var i = 1; i < sh; i++) {
      ctx.fillStyle = 'rgba('+ getRGB(opts.shadow.color) + ', '+ opts.shadow.alpha +')';
      ctx.beginPath();
      ctx.moveTo(i + ox, opts.radius + oy);
      ctx.lineTo(i + ox, height - opts.radius);
      ctx.quadraticCurveTo(i + ox, height - i, opts.radius + ox, height - i);
      ctx.lineTo(width - opts.radius, height - i);
      ctx.quadraticCurveTo(width - i, height - i, width - i, height - opts.radius);
      ctx.lineTo(width - i, opts.radius + oy);
      ctx.quadraticCurveTo(width - i, i + oy, width - opts.radius, i + oy);
      ctx.lineTo(opts.radius + ox+i, i + oy);
      ctx.quadraticCurveTo(i + ox, i + oy, i + ox, opts.radius + oy);
      ctx.closePath();
      ctx.fill();
    }    
  }
  
  function roundedRect(ctx, width, height, opts) {
    height -= opts.shadow.offsetY;
    width -= opts.shadow.offsetX;
    ctx.fillStyle  = 'rgba(255,255,255,1)';
    var gradient = ctx.createLinearGradient(0, 0, 0, height);
    gradient.addColorStop(0, opts.color);
    gradient.addColorStop(0.4, opts.color);
    gradient.addColorStop(1, opts.colorTo);
    
    ctx.beginPath();
    ctx.moveTo(0, opts.radius);
    ctx.lineTo(0, height - opts.radius);
    ctx.quadraticCurveTo(0, height, opts.radius, height);
    ctx.lineTo(width - opts.radius, height);
    ctx.quadraticCurveTo(width, height, width, height - opts.radius);
    ctx.lineTo(width, opts.radius);
    ctx.quadraticCurveTo(width, 0, width - opts.radius, 0);
    ctx.lineTo(opts.radius, 0);
    ctx.quadraticCurveTo(0, 0, 0, opts.radius);
    ctx.closePath();
    ctx.fillStyle = gradient;
    ctx.fill();
  }
  
  function roundedCCShadow(cctx, cWidth, cHeight, opts, cl) {
    var 
      sh = opts.shadow.blur, 
      ox = opts.shadow.offsetX, 
      oy = opts.shadow.offsetY;
    for (var i = 1; i < sh; i++) {
      cctx.fillStyle = 'rgba('+ getRGB(opts.shadow.color) + ', '+ opts.shadow.alpha +')';
      cctx.beginPath();
      cctx.moveTo(i+ox, opts.radius + oy);
      if(cl) {
        cctx.lineTo(i + ox, cHeight - opts.radius);
        cctx.quadraticCurveTo(i + ox, cHeight - i, opts.radius + ox, cHeight - i);
        cctx.lineTo(cWidth - opts.radius-1, cHeight - i);
        cctx.quadraticCurveTo(cWidth - i, cHeight - i, cWidth - i, cHeight - opts.radius);
      } else {
        cctx.lineTo(i + ox, cHeight);
        cctx.lineTo(cWidth - i, cHeight);
      }
      cctx.lineTo(cWidth - i, opts.radius + oy);
      cctx.quadraticCurveTo(cWidth - i, i + oy, cWidth - opts.radius, i + oy);
      cctx.lineTo(opts.radius + ox+i, i + oy);
      cctx.quadraticCurveTo(i + ox, i + oy, i + ox, opts.radius + oy);
      cctx.closePath();
      cctx.fill();
    }
  }
  
  function roundedCShadow(ctx, cctx, width, height, cWidth, cHeight, opts, cl) {
    var 
      sh = opts.shadow.blur,
      ox = opts.shadow.offsetX,
      oy = 0;
    
    roundedCCShadow(cctx, cWidth, cHeight, opts, cl);
    
    for (var i = 1; i < sh; i++) {
      ctx.fillStyle = 'rgba('+ getRGB(opts.shadow.color) + ', '+ opts.shadow.alpha +')';
      ctx.beginPath();
      ctx.moveTo(i+ox, oy);
      ctx.lineTo(i + ox, height - opts.radius);
      ctx.quadraticCurveTo(i + ox, height - i, opts.radius + ox, height - i);
      ctx.lineTo(width - opts.radius, height - i);
      ctx.quadraticCurveTo(width - i, height - i, width - i, height - opts.radius);
      ctx.lineTo(width - i, oy);
      ctx.lineTo(ox+i, oy);
      ctx.closePath();
      ctx.fill();
    }
  }
  
  function roundedCCRect(cctx, cWidth, cHeight, opts, cl) {
    if(cl) cHeight -= opts.shadow.offsetY;
    cWidth -= opts.shadow.offsetX;
    var 
      tp = (null != opts.image) ? 35 : 10,
      textPos = (opts.direction == 'ltr') ? tp : cWidth - tp;
    
    cctx.fillStyle  = 'rgba(255,255,255,1)';
    var gradient = cctx.createLinearGradient(0, 0, 0, cHeight);
    gradient.addColorStop(0, opts.caption.color);
    gradient.addColorStop(0.4, opts.caption.color);
    gradient.addColorStop(1, opts.caption.colorTo);
    cctx.beginPath();
    cctx.moveTo(0, opts.radius);
    if(cl) {
      cctx.lineTo(0, cHeight - opts.radius);
      cctx.quadraticCurveTo(0, cHeight, opts.radius, cHeight);
      cctx.lineTo(cWidth - opts.radius, cHeight);
      cctx.quadraticCurveTo(cWidth, cHeight, cWidth, cHeight - opts.radius);
    } else {
       cctx.lineTo(0, cHeight);
       cctx.lineTo(cWidth, cHeight);
    }    
    cctx.lineTo(cWidth, opts.radius);
    cctx.quadraticCurveTo(cWidth, 0, cWidth - opts.radius, 0);
    cctx.lineTo(opts.radius, 0);
    cctx.quadraticCurveTo(0, 0, 0, opts.radius);
    cctx.closePath();
    cctx.fillStyle = gradient;
    cctx.fill();    
    
    cctx.textAlign = (opts.direction == 'ltr') ? 'left' : 'right'; //  'start';
    cctx.textBaseline = 'middle';
    cctx.fillStyle = opts.caption.fontColor;
    cctx.font = opts.caption.fontSize + 'px ' + opts.caption.fontFamily;
    if($.browser.safari) {
      var fontSize = opts.caption.fontSize,
      textWidth = cctx.measureText(opts.caption.text).width;
      if(textWidth > cWidth - 80) {
        fontSize = Math.floor(fontSize * ((cWidth - 80)/textWidth));
        cctx.font = fontSize + 'px ' + opts.caption.fontFamily;
      }
      cctx.fillText(opts.caption.text, textPos, cHeight/2);
    }
    else if(cWidth > 150) cctx.fillText(opts.caption.text, textPos, cHeight/2, cWidth - 75);
  }
  
  function roundedCRect(ctx, cctx, width, height, cWidth, cHeight, opts, cl) {
    height -= opts.shadow.offsetY;
    width -= opts.shadow.offsetX;
    
    roundedCCRect(cctx, cWidth, cHeight, opts, cl);
    
    ctx.fillStyle  = 'rgba(255,255,255,1)';
    var gradient = ctx.createLinearGradient(0, 0, 0, height);
    gradient.addColorStop(0, opts.color);
    gradient.addColorStop(0.4, opts.color);
    gradient.addColorStop(1, opts.colorTo);
    ctx.beginPath();
    ctx.moveTo(0, 0);
    ctx.lineTo(0, height - opts.radius);
    ctx.quadraticCurveTo(0, height, opts.radius, height);
    ctx.lineTo(width - opts.radius, height);
    ctx.quadraticCurveTo(width, height, width, height - opts.radius);
    ctx.lineTo(width, 0);
    ctx.lineTo(0, 0);
    ctx.closePath();
    ctx.fillStyle = gradient;
    ctx.fill();
  }
  
  function roundedBorder(ctx, width, height, opts) {
    height -= opts.shadow.offsetY;
    width -= opts.shadow.offsetX;
    var bw = opts.border.width;
    var op = 0.7;
    for(var i = 2; i < bw; i++) {
      ctx.beginPath();
      ctx.moveTo(i, opts.radius+i);
      ctx.lineTo(i, height - opts.radius-i);
      ctx.quadraticCurveTo(i, height-i, opts.radius+i, height-i);
      ctx.lineTo(width - opts.radius - i, height - i);
      ctx.quadraticCurveTo(width-i, height-i, width-i, height - opts.radius-i);
      ctx.lineTo(width-i, opts.radius+i);
      ctx.quadraticCurveTo(width-i, i, width - opts.radius-i, i);
      ctx.lineTo(opts.radius+i, i);
      ctx.quadraticCurveTo(i, i, i, opts.radius+i);
      ctx.closePath();
      ctx.strokeStyle = 'rgba('+ getRGB(opts.border.color) + ',' + op +')';
      ctx.stroke();
      op -= 1/bw;
      if(op < 0) op = 0;
    }
    
  } 
  
  function drawBoxImage(canvas, ctx, opts, img) {
    if(img.complete && null != opts.image)
      ctx.drawImage(img, ((opts.direction == "ltr") ? opts.imgX : canvas.width - opts.shadow.offsetX - opts.imgX - 50), opts.imgY, 50, 50);
  }
  
  function drawCBoxImage(ccanvas, cctx, opts, img, imgM, imgP, mode, cl) {
    if(img.complete && null != opts.image && (mode == 'all' || mode == 'main')) {
      cctx.drawImage(
        img,
        ((opts.direction == "ltr") ? opts.imgX : ccanvas.width - opts.shadow.offsetX - opts.imgX - 25),
        ((cl) ? ccanvas.height - opts.shadow.offsetX : ccanvas.height)/2 - 12,
        25, 25
      );
    }
    if(opts.caption.collapsing && imgM.complete && imgP.complete && (mode == 'all' || mode == 'tool')) {
      cctx.drawImage(
        (cl) ? imgP : imgM,
        ((opts.direction == "ltr") ? ccanvas.width - opts.shadow.offsetX - (opts.imgX * 2) - 16 : opts.imgX * 2),
        ((cl) ?  ccanvas.height - opts.shadow.offsetX : ccanvas.height)/2 - 8,
        16, 16
      );
    }
  }
  
  function rbi(index, mode, cl) {
    var 
      opts = stbItems[index].options,
      canvas = (opts.caption.text != '') ? stbItems[index].container.ccvs : stbItems[index].container.cvs,
      ctx = (opts.caption.text != '') ? stbItems[index].container.cctx : stbItems[index].container.ctx,        
      img = stbItems[index].image,
      imgM = stbItems[index].imgMinus,
      imgP = stbItems[index].imgPlus;
      
    if(typeof(mode) == 'undefined') mode = 'all';
    if(typeof(cl) == 'undefined') cl = opts.caption.collapsed;    
    (opts.caption.text != '') ? drawCBoxImage(canvas, ctx, opts, img, imgM, imgP, mode, cl) : drawBoxImage(canvas, ctx, opts, img);
  }
  
  function rcbr(index, cl) {
    var
      opts = stbItems[index].options,
      ccanvas = stbItems[index].container.ccvs,
      cctx = stbItems[index].container.cctx,
      img = stbItems[index].image;
      
    ccanvas.height = (cl) ? 30 + opts.shadow.offsetY : 30;
    cctx.clearRect(0, 0, ccanvas.width, ccanvas.height);
    if(opts.shadow.enabled) roundedCCShadow(cctx, ccanvas.width, ccanvas.height, opts, cl);
    roundedCCRect(cctx, ccanvas.width, ccanvas.height, opts, cl);
    rbi(index, 'all', cl);
  }
  
  function drawBox(canvas, opts, img, il) {
    var ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    if(opts.shadow.enabled) roundedShadow(ctx, canvas.width, canvas.height, opts);
    roundedRect(ctx, canvas.width, canvas.height, opts);
    drawBoxImage(canvas, ctx, opts, img);
    if(opts.border.width > 0)
      roundedBorder(ctx, canvas.width, canvas.height, opts);
  }
  
  function drawCBox(canvas, ccanvas, opts, img, imgM, imgP, cl) {
    var ctx = canvas.getContext("2d");
    var cctx = ccanvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    cctx.clearRect(0, 0, ccanvas.width, ccanvas.height);
    if(opts.shadow.enabled) roundedCShadow(ctx, cctx, canvas.width, canvas.height, ccanvas.width, ccanvas.height, opts, cl);
    roundedCRect(ctx, cctx, canvas.width, canvas.height, ccanvas.width, ccanvas.height, opts, cl);
    
    drawCBoxImage(ccanvas, cctx, opts, img, imgM, imgP, 'all', cl);
  }
  
  function doDraw(data, force) {
    if (!data) return;
    
    var cvs = data.container.cvs,
    cnt = data.container.cnt,
    opts = data.options,
    width = $(cnt).parent(0).width(),
    height = $(cnt).outerHeight(),
    cp = data.options.caption;     
        
    if(force || cvs.width != width || cvs.height != height + opts.shadow.offsetY + 20) {
      cvs.width = width;
      if(cp.text == '') {
        cvs.height = height + opts.shadow.offsetY + 20;
        drawBox(cvs, opts, data.image, data.image.complete);
      }
      else {
        var ccvs = data.container.ccvs;
        
        cvs.height = height + opts.shadow.offsetY + 10;
        ccvs.width = width;
        ccvs.height = 30;
        if(cp.collapsed) ccvs.height += opts.shadow.offsetY;
        drawCBox(cvs, ccvs, opts, data.image, data.imgMinus, data.imgPlus, cp.collapsed);  
      }
    }
  }
  
  function doDrawAll(force) {
    if(stbItems.length > 0)
      $.each(stbItems, function(index, item) {
         doDraw(item, force);
      });
  }
  
  function stbData(cnt, opts, cap, index) {
    this.container = cnt;
    this.options = opts;
    this.caption = cap;
    this.isDrawing = false;
    this.index = index;    
    this.image = new Image();
    this.imgMinus = new Image();
    this.imgPlus = new Image();
  }
  
  jQuery.fn.extend({
    stb: function(theme, opts) {
      var options = buildOptions(theme, opts); 
      
      this.each(function() {
        var opts = {}, eop,        
        eid = $(this).attr('id'),
        fop = $(this).data('stb'),
        caption = null;
        //lineHeight = $(this).css('line-height');
        
        try { 
          eop = typeof fop === 'string' ? (new Function("return " + fop))() : fop;
          if(typeof(eop) === 'object') $.extend(true, opts, options, eop);
          else opts = options; 
        }
        catch(e) { 
          log('Unable to parse HTML5 attribute data: ' + fop); 
        }

        var lineHeight = opts.lineHeight;
        
        var bodyStyle = {
          direction: opts.direction,
          'unicode-bidi': 'embed',
          color: opts.fontColor,
          position: 'absolute',
          top: '0px',
          'z-index': 4,
          "padding-right": (opts.image == null) ? '5px' : ((opts.direction == "ltr") ? "5px" : "50px"),
          "padding-bottom": "5px",
          "padding-top": "5px",
          "padding-left": (opts.image == null) ? '5px' : ((opts.direction == "ltr") ? "50px" : "5px"),
          "min-height": "40px",
          "margin-top": opts.mtop + "px",
          "margin-right": opts.mright + opts.shadow.offsetX + "px",
          "margin-bottom": opts.mbottom + "px",
          "margin-left": opts.mleft + "px",
          "box-sizing": "content-box",
          'line-height': lineHeight,
          "text-shadow": (opts.textShadow.enabled) ? opts.textShadow.color + ' ' + opts.textShadow.offsetX + 'px ' + opts.textShadow.offsetY + 'px ' + opts.textShadow.blur + 'px' : 'none'
        },
        bodyCStyle = {
          direction: opts.direction,
          'unicode-bidi': 'embed',
          position: 'absolute',
          top: '30px',
          color: opts.fontColor,
          "padding": "5px 5px 5px 5px",
          "min-height": "5px",
          "margin-top": "0px",
          "margin-right": opts.mright + opts.shadow.offsetX + "px",
          "margin-bottom": opts.mbottom + "px",
          "margin-left": opts.mleft + "px",
          "box-sizing": "content-box",
          "z-index": 4,
          'line-height': lineHeight,
          "text-shadow": (opts.textShadow.enabled) ? opts.textShadow.color + ' ' + opts.textShadow.offsetX + 'px ' + opts.textShadow.offsetY + 'px ' + opts.textShadow.blur + 'px' : 'none'
        };
        
        
        if(typeof(eid) == 'undefined') {
          eid = 'stb_js_' + getRandInt(1000, 9999);
          $(this).attr('id', eid);
        }
        if(opts.caption.text == '') {
          $(this).css(bodyStyle).wrap('<div id="'+eid+'_container" class="stb-container"></div>');
          $('#'+eid+'_container').css({
            direction: opts.direction,
            "margin-top": opts.mtop + "px",
            "margin-right": opts.mright + "px",
            "margin-bottom": opts.mbottom + "px",
            "margin-left": opts.mleft + "px",
            position: 'relative',
            "box-sizing": "content-box"
          });
          
          $(this).before('<canvas id="'+eid+'_canvas" class="stb-canvas" width="0" height="0" ></canvas>');
          $("#"+eid+"_canvas").css({
            "position": "relative",
            "top": "0px",
            "left": "0px",
            "box-sizing": "content-box"
          });
          
          var canvas = $('#'+eid+'_canvas').get(0),
          ctx = canvas.getContext("2d"),
          cnt = $(this).get(0),
          data = new stbData(
            {cnt: cnt, cvs: canvas, ctx: ctx},
            opts,
            opts.caption,
            stbItems.length
          );          
        } else {
          $(this).css(bodyCStyle).wrap('<div id="'+eid+'_container" class="stb-container"></div>');
          $('#'+eid+'_container').css({
            direction: opts.direction,
            "margin-top": opts.mtop + "px",
            "margin-right": opts.mright + "px",
            "margin-bottom": opts.mbottom + "px",
            "margin-left": opts.mleft + "px",
            position: 'relative',
            "box-sizing": "content-box"
          });
          
          $(this).before('<canvas id="'+eid+'_ccanvas" class="stb-ccanvas" width="0" height="0" ></canvas>');
          $("#"+eid+"_ccanvas").css({
            "box-sizing": "content-box"
          });
          $(this).before('<canvas id="'+eid+'_canvas" class="stb-canvas" width="0" height="0" ></canvas>');
          $("#"+eid+"_canvas").css({
            "position": "relative",
            //"top": -(opts.shadow.offsetY + 1), //"0px",
            "left": "0px",
            "box-sizing": "content-box"
          });


          var dir = $('body').css('direction');
          if(dir != opts.direction)
            $('#'+eid+'_ccanvas').attr('dir', opts.direction).css({'unicode-bidi': 'bidi-override'});
          if(opts.safe) {
            $('#'+eid+'_canvas').css({
              "position": "relative",
              "top": "0px",
              "left": "0px"
            });
          }
          
          var canvas = $('#'+eid+'_canvas').get(0),
          ctx = canvas.getContext("2d"),
          ccanvas = $('#'+eid+'_ccanvas').get(0),
          cctx = ccanvas.getContext("2d"),
          cnt = $(this).get(0),
          data = new stbData(
            {cnt: cnt, cvs: canvas, ctx: ctx, ccvs: ccanvas, cctx: cctx},
            opts,
            opts.caption,
            stbItems.length
          );
          if(opts.caption.collapsing) {
            $('#'+eid+'_ccanvas').click(function() {
              var cid = $(this).attr('id'),
              tid = '#' + cid.replace('_ccanvas', ''),
              bid = tid + '_canvas',
            
              data = $(tid).data('stb_props');
            
              if($(tid).is(":hidden")) {
                rcbr(data.index, false);
                $(tid).effect('blind', {mode: 'show', direction: 'vertical'}, data.caption.duration, function() {                
                  var data = $(tid).data('stb_props');
                  data.caption.collapsed = false;
                  stbItems[data.index].caption.collapsed = false;
                  $(tid).data('stb_props', data);
                });
              } else {
                $(tid).effect('blind', {mode: 'hide', direction: 'vertical'}, data.caption.duration, function() {
                  var data = $(tid).data('stb_props');
                  data.caption.collapsed = true;
                  stbItems[data.index].caption.collapsed = true;
                  $(tid).data('stb_props', data);
                  rcbr(data.index, true);
                });
              } 
            
              if($(bid).is(":hidden")) $(bid).effect('blind', {mode: 'show', direction: 'vertical'}, data.caption.duration);
              else $(bid).effect('blind', {mode: 'hide', direction: 'vertical'}, data.caption.duration);
            });
          }          
        }
               
        stbItems.push(data);
        $.data(stbItems[stbItems.length-1].image, 'itemIndex', stbItems.length-1);
        $.data(stbItems[stbItems.length-1].imgMinus, 'itemIndex', stbItems.length-1);
        $.data(stbItems[stbItems.length-1].imgPlus, 'itemIndex', stbItems.length-1);
        stbItems[stbItems.length-1].image.onload = function() {
          var index = $.data(this, 'itemIndex');
          rbi(index, 'main');
        };
        stbItems[stbItems.length-1].image.src = stbItems[stbItems.length-1].options.image;
        stbItems[stbItems.length-1].imgMinus.onload = function() {
          var index = $.data(this, 'itemIndex');
          rbi(index, 'tool');
        };
        stbItems[stbItems.length-1].imgMinus.src = stbItems[stbItems.length-1].options.caption.imgMinus;
        stbItems[stbItems.length-1].imgPlus.onload = function() {
          var index = $.data(this, 'itemIndex');
          rbi(index, 'tool');
        };
        stbItems[stbItems.length-1].imgPlus.src = stbItems[stbItems.length-1].options.caption.imgPlus;
        $(this).data('stb_props', data).addClass('stb-body');
        
        //doDraw(data, true);
        if(opts.caption.collapsed) {
          $(this).hide();
          $('#'+eid+'_canvas').hide();
        }
      });
      
      doDrawAll(true);
      
      return false;
    },
    
    stbRedraw: function() {
      this.each(function() {
        var data = $(this).data('stb_props');
        doDraw(data, true);
      });
    }
  });
      
  $(window).resize(function() {
    doDrawAll(true);
    return false;
  });
})(jQuery);