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
      duration: 500,
      side: true
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
  
  function StbThemesStream() {
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
  
  stbThemes = new StbThemesStream();
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
    return 'rgb(' + r.toString() + ',' + g.toString() + ',' + b.toString() + ')';
  }

  function getRGBA(color, alpha) {
    var clr = color.replace('#', '');
    var r = parseInt('0x'+clr.slice(0,2));
    var g = parseInt('0x'+clr.slice(2,4));
    var b = parseInt('0x'+clr.slice(4,6));
    return 'rgba(' + r.toString() + ',' + g.toString() + ',' + b.toString() + ',' + alpha + ')';
  }

  function getPosition(e) {
    var target;
    if(!e) e = window.event;
    if(e.target) target = e.target;
    else if(e.srcElement) target = e.srcElement;
    if(target.nodeType == 3) target = target.parentNode; // defeat Safari bug

    var x = e.pageX - $(target).offset().left;
    var y = e.pageY - $(target).offset().top;

    return {"x": x, "y": y};
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
  
  function simpleBox(ctx, width, height, opts, cl) {
    var
      top = opts.mtop,
      bottom = height - opts.mbottom,
      left = opts.mleft,
      right = width - opts.mright;

    if('undefined' == typeof(cl)) cl = 0;
    ctx.fillStyle  = 'rgba(255,255,255,1)';
    var gradient = ctx.createLinearGradient(left, top, left, bottom);
    gradient.addColorStop(0, opts.color);
    gradient.addColorStop(0.4, opts.color);
    gradient.addColorStop(1, opts.colorTo);

    if(opts.shadow.enabled) {
      ctx.shadowColor = opts.shadow.color;
      ctx.shadowBlur = opts.shadow.blur;
      ctx.shadowOffsetX = opts.shadow.offsetX;
      ctx.shadowOffsetY = opts.shadow.offsetY;
    }
    
    ctx.beginPath();
    ctx.moveTo(left, top + opts.radius);
    ctx.lineTo(left, bottom - opts.radius);
    ctx.quadraticCurveTo(left, bottom, left + opts.radius, bottom);
    ctx.lineTo(right - opts.radius, bottom);
    ctx.quadraticCurveTo(right, bottom, right, bottom - opts.radius);
    ctx.lineTo(right, top + opts.radius);
    ctx.quadraticCurveTo(right, top, right - opts.radius, top);
    ctx.lineTo(left + opts.radius, top);
    ctx.quadraticCurveTo(left, top, left, top + opts.radius);
    ctx.closePath();
    ctx.fillStyle = gradient;
    ctx.fill();

    if(opts.shadow.enabled) {
      ctx.shadowColor = "transparent";
    }

    if(opts.caption.text != '') {
      var cGradient = ctx.createLinearGradient(left, top, left, top + 30);
      cGradient.addColorStop(0, opts.caption.color);
      cGradient.addColorStop(0.5, opts.caption.color);
      cGradient.addColorStop(1, opts.caption.colorTo);

      ctx.beginPath();
      ctx.moveTo(left, top + opts.radius);
      if(cl) {
        ctx.lineTo(left, bottom - opts.radius);
        ctx.quadraticCurveTo(left, bottom, left + opts.radius, bottom);
        ctx.lineTo(right - opts.radius, bottom);
        ctx.quadraticCurveTo(right, bottom, right, bottom - opts.radius);
      } else {
        ctx.lineTo(left, top + 30);
        ctx.lineTo(right, top + 30);
      }
      ctx.lineTo(right, top + opts.radius);
      ctx.quadraticCurveTo(right, top, right - opts.radius, top);
      ctx.lineTo(left + opts.radius, top);
      ctx.quadraticCurveTo(left, top, left, top + opts.radius);
      ctx.closePath();
      ctx.fillStyle = cGradient;
      ctx.fill();
    }
    else if(opts.caption.side) {
      var sGradient = ctx.createLinearGradient(left, top, left, bottom);
      sGradient.addColorStop(0, opts.caption.color);
      sGradient.addColorStop(0.4, opts.caption.color);
      sGradient.addColorStop(1, opts.caption.colorTo);

      ctx.beginPath();
      if(opts.direction == 'ltr') {
        ctx.moveTo(left, top + opts.radius);
        ctx.lineTo(left, bottom - opts.radius);
        ctx.quadraticCurveTo(left, bottom, left + opts.radius, bottom);
        ctx.lineTo(left + 2 * opts.imgX + 50, bottom);
        ctx.lineTo(left + 2 * opts.imgX + 50, top);
        ctx.lineTo(left + opts.radius, top);
        ctx.quadraticCurveTo(left, top, left, top + opts.radius);
      }
      else {
        ctx.moveTo(right - (2 * opts.imgX + 50), top);
        ctx.lineTo(right - (2 * opts.imgX + 50), bottom);
        ctx.lineTo(right - opts.radius, bottom);
        ctx.quadraticCurveTo(right, bottom, right, bottom - opts.radius);
        ctx.lineTo(right, top + opts.radius);
        ctx.quadraticCurveTo(right, top, right - opts.radius, top);
        ctx.lineTo(right - (2 * opts.imgX + 50), top);
      }
      ctx.closePath();
      ctx.fillStyle = sGradient;
      ctx.fill();
    }

    var
      tp = (null != opts.image) ? 35 : 10,
      textPos = (opts.direction == 'ltr') ? tp + opts.mleft : width - tp - opts.mright;

    ctx.textAlign = (opts.direction == 'ltr') ? 'left' : 'right';
    ctx.textBaseline = 'middle';
    ctx.fillStyle = opts.caption.fontColor;
    ctx.font = 'bold ' + opts.caption.fontSize + 'px ' + opts.caption.fontFamily;
    if($.browser.safari) {
      var fontSize = opts.caption.fontSize,
        textWidth = ctx.measureText(opts.caption.text).width;
      if(textWidth > width - 80 - opts.mleft - opts.mright) {
        fontSize = Math.floor(fontSize * ((width - 80 - opts.mleft - opts.mright)/textWidth));
        ctx.font = fontSize + 'px ' + opts.caption.fontFamily;
      }
      ctx.fillText(opts.caption.text, textPos, top + 15);
    }
    else if(width > 150)
      ctx.fillText(opts.caption.text, textPos, top + 15, width - 75 - opts.mleft - opts.mright);
  }
  
  function roundedBorder(ctx, width, height, opts) {
    var
      top = opts.mtop,
      bottom = height - opts.mbottom,
      left = opts.mleft,
      right = width - opts.mright;

    var bw = opts.border.width;
    var op = 0.7;
    for(var i = 0; i < bw; i++) {
      ctx.beginPath();
      ctx.moveTo(left + i, top + opts.radius + i);
      ctx.lineTo(left + i, bottom - opts.radius - i);
      ctx.quadraticCurveTo(left + i, bottom - i, left + opts.radius + i, bottom - i);
      ctx.lineTo(right - opts.radius - i, bottom - i);
      ctx.quadraticCurveTo(right - i, bottom - i, right - i, bottom - opts.radius - i);
      ctx.lineTo(right - i, top + opts.radius+i);
      ctx.quadraticCurveTo(right - i, top + i, right - opts.radius - i, top + i);
      ctx.lineTo(left + opts.radius + i, top + i);
      ctx.quadraticCurveTo(left + i, top + i, left + i, top + opts.radius+i);
      ctx.closePath();
      ctx.strokeStyle = getRGBA(opts.border.color, op); //'rgba('+ getRGB(opts.border.color) + ',' + op +')';
      ctx.stroke();
      op -= 1/bw;
      if(op < 0) op = 0;
    }
    
  } 
  
  function drawBoxImage(canvas, ctx, opts, img) {
    if(img.complete && null != opts.image)
      ctx.drawImage(img, ((opts.direction == "ltr") ? opts.imgX + opts.mleft : canvas.width/* - opts.shadow.offsetX*/ - opts.imgX - 50 - opts.mright), opts.imgY + opts.mtop, 50, 50);
  }
  
  function drawCBoxImage(canvas, ctx, opts, img, imgM, imgP, mode, cl) {
    if(img.complete && null != opts.image && (mode == 'all' || mode == 'main')) {
      ctx.drawImage(
        img,
        ((opts.direction == "ltr") ? 7 + opts.mleft : canvas.width - opts.mright - 7 - 25),
        opts.mtop + 3,
        25, 25
      );
    }
    if(opts.caption.collapsing && imgM.complete && imgP.complete && (mode == 'all' || mode == 'tool')) {
      ctx.drawImage(
        (cl) ? imgP : imgM,
        ((opts.direction == "ltr") ? canvas.width - opts.mright - 7 - 16 : opts.mleft + 7),
        opts.mtop + 7,
        16, 16
      );
    }
  }
  
  function boxImage(index, mode, cl) {
    var 
      opts = stbItems[index].options,
      canvas = stbItems[index].container.cvs,
      ctx = stbItems[index].container.ctx,
      img = stbItems[index].image,
      imgM = stbItems[index].imgMinus,
      imgP = stbItems[index].imgPlus;
      
    if(typeof(mode) == 'undefined') mode = 'all';
    if(typeof(cl) == 'undefined') cl = opts.caption.collapsed;    
    (opts.caption.text != '') ? drawCBoxImage(canvas, ctx, opts, img, imgM, imgP, mode, cl) : drawBoxImage(canvas, ctx, opts, img);
  }
  
  function redrawBox(index, cl, height) {
    var
      opts = stbItems[index].options,
      canvas = stbItems[index].container.cvs,
      ctx = stbItems[index].container.ctx,
      img = stbItems[index].image;

    if('undefined' == typeof(height)) height = canvas.height;
      
    canvas.height = (cl) ? opts.mtop + 30 + opts.mbottom : height;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    simpleBox(ctx, canvas.width, canvas.height, opts, cl);
    boxImage(index, 'all', cl);
    if(opts.border.width > 0)
      roundedBorder(ctx, canvas.width, canvas.height, opts);
  }
  
  function drawBox(canvas, opts, img, il) {
    var ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    simpleBox(ctx, canvas.width, canvas.height, opts);
    drawBoxImage(canvas, ctx, opts, img);
    if(opts.border.width > 0)
      roundedBorder(ctx, canvas.width, canvas.height, opts);
  }
  
  function drawCBox(canvas, opts, img, imgM, imgP, cl) {
    var ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    simpleBox(ctx, canvas.width, canvas.height, opts, cl);
    drawCBoxImage(canvas, ctx, opts, img, imgM, imgP, 'all', cl);
    if(opts.border.width > 0)
      roundedBorder(ctx, canvas.width, canvas.height, opts);
  }
  
  function doDraw(data, force) {
    if (!data) return;
    
    var
      cvs = data.container.cvs,
      cnt = data.container.cnt,
      opts = data.options,
      body = data.container.body,
      width = $(cnt).parent(0).width(),
      height = $(body).innerHeight(),
      cp = data.options.caption;
        
    if(force || cvs.width != width || cvs.height != (height + opts.mtop + opts.mbottom)) {
      cvs.width = width;
      if(cp.text == '') {
        cvs.height = opts.mtop + height + opts.mbottom;
        drawBox(cvs, opts, data.image, data.image.complete);
      }
      else {
        cvs.height = opts.mtop + 30 + ((cp.collapsed) ? 0 : height) + opts.mbottom;
        drawCBox(cvs, opts, data.image, data.imgMinus, data.imgPlus, cp.collapsed);
      }
    }
  }
  
  function doDrawAll(force) {
    if(stbItems.length > 0)
      $.each(stbItems, function(index, item) {
         doDraw(item, force);
      });
  }
  
  function StbData(cnt, opts, cap, index) {
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
        var
          opts = {}, eop,
          eid = $(this).attr('id'),
          fop = $(this).data('stb'),
          bodyLineHeight = $(this).css('line-height'),
          caption = null;

        var canvas, ctx, cnt, body, data;
        
        try { 
          eop = typeof fop === 'string' ? (new Function("return " + fop))() : fop;
          if(typeof(eop) === 'object') $.extend(true, opts, options, eop);
          else opts = options; 
        }
        catch(e) { 
          log('Unable to parse HTML5 attribute data: ' + fop); 
        }

        var
          sidePadding = (opts.caption.side) ? 50 + 2 * opts.imgX + 10 + "px" : "50px",
          bodyStyle = {
            boxSizing: "content-box",
            lineHeight: bodyLineHeight,
            direction: opts.direction,
            unicodeBidi: 'embed',
            color: opts.fontColor,
            position: 'absolute',
            top: '0px',
            zIndex: 4,
            margin: opts.mtop + "px " + opts.mright + "px " + opts.mbottom + "px " + opts.mleft + "px",
            paddingRight: (opts.image == null) ? '10px' : ((opts.direction == "ltr") ? "10px" : sidePadding),
            paddingBottom: "10px",
            paddingTop: "10px",
            paddingLeft: (opts.image == null) ? '10px' : ((opts.direction == "ltr") ? sidePadding : "10px"),
            minHeight: "40px",
            textShadow: (opts.textShadow.enabled) ? opts.textShadow.color + ' ' + opts.textShadow.offsetX + 'px ' + opts.textShadow.offsetY + 'px ' + opts.textShadow.blur + 'px' : 'none'
          },
          bodyCStyle = {
            boxSizing: "content-box",
            lineHeight: bodyLineHeight,
            direction: opts.direction,
            unicodeBidi: 'embed',
            position: 'absolute',
            top: 30 + opts.mtop/* + '0px'*/,
            color: opts.fontColor,
            padding: "5px 10px 10px",
            minHeight: "5px",
            margin: /*(30 + opts.mtop) +*/ "0px " + opts.mright + "px " + opts.mbottom + "px " + opts.mleft + "px",
            zIndex: 4,
            textShadow: (opts.textShadow.enabled) ? opts.textShadow.color + ' ' + opts.textShadow.offsetX + 'px ' + opts.textShadow.offsetY + 'px ' + opts.textShadow.blur + 'px' : 'none'
          };
        
        
        if(typeof(eid) == 'undefined') {
          eid = 'stb_js_' + getRandInt(1000, 9999);
          $(this).attr('id', eid);
        }

        var canvasId = '#'+eid+'_canvas', cCanvasId = '#'+eid+'_ccanvas';

        if(opts.caption.text == '') {
          $(this).css(bodyStyle);
          $(this).wrap('<div id="'+eid+'_container" class="stb-container"></div>');
          $('#'+eid+'_container').css({
            direction: opts.direction,
            position: 'relative',
            boxSizing: "content-box"
          });
          
          $(this).before('<canvas id="'+eid+'_canvas" class="stb-canvas" width="0" height="0" ></canvas>');
          $(canvasId).css({
            position: "relative",
            top: 0,
            left: 0,
            boxSizing: "content-box"
          });

          canvas = $(canvasId).get(0);
          ctx = canvas.getContext("2d");
          cnt = $(this).get(0);
          body = $(this);
          data = new StbData(
            {cnt: cnt, cvs: canvas, ctx: ctx, body: body},
            opts,
            opts.caption,
            stbItems.length
          );
        } else {
          $(this).css(bodyCStyle).wrap('<div id="'+eid+'_container" class="stb-container" style="line-height: 0.1em;"></div>');
          $('#'+eid+'_container').css({
            direction: opts.direction,
            position: 'relative',
            boxSizing: "content-box"
          });
          
          $(this).before('<canvas id="'+eid+'_canvas" class="stb-canvas" width="0" height="0" ></canvas>');
          $(canvasId).css({
            position: "relative",
            left: 0,
            boxSizing: "content-box"
          });


          var dir = $('body').css('direction');
          if(opts.safe) {
            $(canvasId).css({
              position: "relative",
              top: 0,
              left: 0
            });
          }
          
          canvas = $(canvasId).get(0);
          ctx = canvas.getContext("2d");
          cnt = $(this).get(0);
          body = $(this);
          data = new StbData(
            {cnt: cnt, cvs: canvas, ctx: ctx, body: body},
            opts,
            opts.caption,
            stbItems.length
          );
          if(opts.caption.collapsing) {
            $(canvasId).click(function(e) {
              pos = getPosition(e);
              if((pos.x > opts.mleft && pos.x < ($(this).width() - opts.mright))
                && (pos.y > opts.mtop && pos.y < opts.mtop + 30)) {
                var cid = $(this).attr('id'),
                tid = '#' + cid.replace('_canvas', ''),
                bid = tid + '_canvas',
                data = $(tid).data('stb_props');
            
                if($(tid).is(":hidden")) {
                  redrawBox(data.index, false);
                  $(tid).slideDown({
                    duration: data.caption.duration,
                    progress: function(an, pr, rem) {
                      var height = $(this).innerHeight() + 30 + opts.mtop + opts.mbottom;
                      redrawBox(data.index, false, height);
                    },
                    done: function() {
                      var data = $(tid).data('stb_props');
                      data.caption.collapsed = false;
                      stbItems[data.index].caption.collapsed = false;
                      $(tid).data('stb_props', data);
                      redrawBox(data.index, false);
                    }
                  });
                } else {
                  $(tid).slideUp({
                    duration: data.caption.duration,
                    progress: function(an, pr, rem) {
                      var height = $(this).innerHeight() + 30 + opts.mtop + opts.mbottom;
                      redrawBox(data.index, false, height);
                    },
                    done: function() {
                      var data = $(tid).data('stb_props');
                      data.caption.collapsed = true;
                      stbItems[data.index].caption.collapsed = true;
                      $(tid).data('stb_props', data);
                      redrawBox(data.index, true);
                    }
                  });
                }
              }
            });
          }          
        }
               
        stbItems.push(data);
        $.data(stbItems[stbItems.length-1].image, 'itemIndex', stbItems.length-1);
        $.data(stbItems[stbItems.length-1].imgMinus, 'itemIndex', stbItems.length-1);
        $.data(stbItems[stbItems.length-1].imgPlus, 'itemIndex', stbItems.length-1);

        // Image
        if(stbItems[stbItems.length-1].options.image != null) {
          stbItems[stbItems.length-1].image.onload = function() {
            var index = $.data(this, 'itemIndex');
            boxImage(index, 'main');
          };
          stbItems[stbItems.length-1].image.src = stbItems[stbItems.length-1].options.image;
        }

        // imgMinus
        stbItems[stbItems.length-1].imgMinus.onload = function() {
          var index = $.data(this, 'itemIndex');
          boxImage(index, 'tool');
        };
        stbItems[stbItems.length-1].imgMinus.src = stbItems[stbItems.length-1].options.caption.imgMinus;

        // imgPlus
        stbItems[stbItems.length-1].imgPlus.onload = function() {
          var index = $.data(this, 'itemIndex');
          boxImage(index, 'tool');
        };
        stbItems[stbItems.length-1].imgPlus.src = stbItems[stbItems.length-1].options.caption.imgPlus;
        $(this).data('stb_props', data).addClass('stb-body');
        
        if(opts.caption.collapsed && opts.caption.text != '') {
          $(this).hide();
          //$(canvasId).hide();
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