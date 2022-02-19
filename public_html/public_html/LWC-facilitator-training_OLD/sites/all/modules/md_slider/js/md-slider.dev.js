/*------------------------------------------------------------------------
 # MD Slider - March 18, 2013
 # ------------------------------------------------------------------------
 # Websites:  http://www.megadrupal.com -  Email: info@megadrupal.com
 --------------------------------------------------------------------------*/

(function ($) {
    effectsIn = [
        'bounceIn',
        'bounceInDown',
        'bounceInUp',
        'bounceInLeft',
        'bounceInRight',
        'fadeIn',
        'fadeInUp',
        'fadeInDown',
        'fadeInLeft',
        'fadeInRight',
        'fadeInUpBig',
        'fadeInDownBig',
        'fadeInLeftBig',
        'fadeInRightBig',
        'flipInX',
        'flipInY',
        'foolishIn', //-
        'lightSpeedIn',
        'rollIn',
        'rotateIn',
        'rotateInDownLeft',
        'rotateInDownRight',
        'rotateInUpLeft',
        'rotateInUpRight',
        'twisterInDown', //-
        'twisterInUp', //-
        'swap', //-
        'swashIn',  //-
        'tinRightIn',  //-
        'tinLeftIn',  //-
        'tinUpIn',  //-
        'tinDownIn', //-
    ];
    effectsOut = [
        'bombRightOut',  //-
        'bombLeftOut', //-
        'bounceOut',
        'bounceOutDown',
        'bounceOutUp',
        'bounceOutLeft',
        'bounceOutRight',
        'fadeOut',
        'fadeOutUp',
        'fadeOutDown',
        'fadeOutLeft',
        'fadeOutRight',
        'fadeOutUpBig',
        'fadeOutDownBig',
        'fadeOutLeftBig',
        'fadeOutRightBig',
        'flipOutX',
        'flipOutY',
        'foolishOut', //-
        'hinge',
        'holeOut', //-
        'lightSpeedOut',
        'puffOut',  //-
        'rollOut',
        'rotateOut',
        'rotateOutDownLeft',
        'rotateOutDownRight',
        'rotateOutUpLeft',
        'rotateOutUpRight',
        'rotateDown', //-
        'rotateUp', //-
        'rotateLeft', //-
        'rotateRight', //-
        'swashOut', //-
        'tinRightOut', //-
        'tinLeftOut', //-
        'tinUpOut', //-
        'tinDownOut', //-
        'vanishOut' //-
    ];
    var e_in_length = effectsIn.length;
    var e_out_length = effectsOut.length;
    $.fn.mdSlider = function(options) {
        var defaults = {
            className: 'md-slide-wrap',
            itemClassName: 'md-slide-item',
            transitions: 'strip-down-left', // name of transition effect (fade, scrollLeft, scrollRight, scrollHorz, scrollUp, scrollDown, scrollVert)
            transitionsSpeed: 800, // speed of the transition (millisecond)
            width: 990, // responsive = false: this appear as container width; responsive = true: use for scale ;fullwidth = true: this is effect zone width
            height: 420, // container height
            responsive: true,
            fullwidth: true,
            styleBorder: 0, // Border style, from 1 - 9, 0 to disable
            styleShadow: 0, // Dropshadow style, from 1 - 5, 0 to disable
            posBullet: 2, // Bullet position, from 1 to 6, default is 5
            posThumb: 1, // Thumbnail position, from 1 to 5, default is 1
            stripCols: 20,
            stripRows: 10,
            slideShowDelay: 6000, // stop time for each slide item (millisecond)
            slideShow: true,
            loop: false,
            pauseOnHover: false,
            showLoading: true, // Show/hide loading bar
            loadingPosition: 'bottom', // choose your loading bar position (top, bottom)
            showArrow: true, // show/hide next, previous arrows
            showBullet: true,
			videoBox: false,
            showThumb: true, // Show thumbnail, if showBullet = true and showThumb = true, thumbnail will be shown when you hover bullet navigation
            enableDrag: true, // Enable mouse drag
            touchSensitive: 50,
            onEndTransition: function() {  },	//this callback is invoked when the transition effect ends
            onStartTransition: function() {  }	//this callback is invoked when the transition effect starts
        };
        options = $.extend({}, defaults, options);
        var self= $(this), slideItems = [], oIndex, activeIndex = -1, numItem = 0, slideWidth, slideHeight, lock = true,
            wrap,
            hoverDiv,
            hasTouch,
            arrowButton,
            buttons,
            loadingBar,
            timerGlow,
            slideThumb,
            minThumbsLeft = 0,
            touchstart = false,
            isScrolling,
            mouseleft,
            thumbsDrag = false,
            slideShowDelay = 0,
            play = false,
            pause = false,
            timer,
            step = 0;
          var Utils = {
            /**
             * range Get an array of numbers within a range
             * @param min {number} Lowest number in array
             * @param max {number} Highest number in array
             * @param rand {bool} Shuffle array
             * @return {array}
             */
            range: function( min, max, rand ) {
              var arr = ( new Array( ++max - min ) ).join('.').split('.').map(function( v,i ){ return min + i });
              return rand ? arr.map(function( v ) { return [ Math.random(), v ] }).sort().map(function( v ) { return v[ 1 ] }) : arr;
            }
          };

        // init
        function init() {
            if ("ActiveXObject" in window)
                $(".md-item-opacity", self).addClass("md-ieopacity");

            self.addClass("loading-image");
            var slideClass = '';
            if (options.responsive)
                slideClass += ' md-slide-responsive';
            if (options.fullwidth)
                slideClass += ' md-slide-fullwidth';
            if (options.showBullet && options.posBullet)
                slideClass += ' md-slide-bullet-' + options.posBullet;
            if (!options.showBullet && options.showThumb && options.posThumb)
                slideClass += ' md-slide-thumb-' + options.posThumb;
            self.wrap('<div class="' + options.className + slideClass + '"><div class="md-item-wrap"></div></div>');
            hoverDiv = self.parent();
            wrap = hoverDiv.parent();
            slideWidth = options.responsive ? self.width() : options.width;
            slideHeight = options.height;
            slideItems = [];
            hasTouch = documentHasTouch();
            if(hasTouch)
                wrap.addClass("md-touchdevice");
            //
            self.find('.' + options.itemClassName).each(function (index) {
                numItem++;
                slideItems[index] = $(this);
                $(this).find(".md-object").each(function() {
                    var top =  $(this).data("y") ? $(this).data("y") : 0,
                        left = $(this).data("x") ? $(this).data("x") : 0,
                        width = $(this).data("width") ? $(this).data("width") : 0,
                        height = $(this).data("height") ? $(this).data("height") : 0;
                    if(width > 0) {
                        $(this).width((width / options.width * 100) + "%");
                    }
                    if(height > 0) {
                        $(this).height((height / options.height * 100) + "%");
                    }
                    var css = {
                        top:(top / options.height * 100) + "%",
                        left:(left / options.width * 100) + "%"
                    };
                    $(this).css(css);
                });
                if(index > 0)
                    $(this).hide();
            });
            initControl();
            initDrag();
            if(options.slideShow) {
                play = true;
            }
            $('.md-object', self).hide();
            if($(".md-video", wrap).size() > 0) {
				if(options.videoBox) {
					$(".md-video", wrap).mdvideobox();
				} else {
					var videoCtrl = $('<div class="md-video-control" style="display: none"></div>');
					wrap.append(videoCtrl);
					$(".md-video", wrap).click(function() {
						var video_ele = $("<iframe></iframe>");
						video_ele.attr('allowFullScreen' , '').attr('frameborder' , '0').css({width:"100%", height: "100%", background: "black"});
						video_ele.attr("src", $(this).attr("href"));
						var closeButton = $('<a href="#" class="md-close-video" title="Close video"></a>');
						closeButton.click(function() {
							videoCtrl.html("").hide();
							play = true;
							return false;
						});
						videoCtrl.html("").append(video_ele).append(closeButton).show();
						play = false;
						return false;
					});
				}
            }
            $(window).resize(function() {
                resizeWindow();
            }).trigger("resize");
            preloadImages();

            // process when un-active tab
            var inActiveTime = false;
            $(window).blur(function(){
                inActiveTime = (new Date()).getTime();
            });
            $(window).focus(function(){
                if(inActiveTime) {
                    var duration = (new Date()).getTime() - inActiveTime;

                    if(duration > slideShowDelay - step)
                        step = slideShowDelay - 200;
                    else
                        step += duration;
                    inActiveTime = false;
                }
            });
        }
        function initControl() {
            // Loading bar
            if(options.slideShow && options.showLoading) {
                var loadingDiv = $('<div class="loading-bar-hoz loading-bar-' + options.loadingPosition + '"><div class="br-timer-glow" style="left: -100px;"></div><div class="br-timer-bar" style="width:0px"></div></div>');
                wrap.append(loadingDiv);
                loadingBar = $(".br-timer-bar", loadingDiv);
                timerGlow  = $(".br-timer-glow", loadingDiv);
            }
            if(options.slideShow && options.pauseOnHover) {
                hoverDiv.hover(function() {
                    pause = true;
                }, function() {
                    pause = false;
                });
            }
            // Border
            if (options.styleBorder != 0) {
                var borderDivs = '<div class="border-top border-style-' + options.styleBorder + '"></div>';
                borderDivs += '<div class="border-bottom border-style-' + options.styleBorder + '"></div>';
                if (!options.fullwidth) {
                    borderDivs += '<div class="border-left border-style-' + options.styleBorder + '"><div class="edge-top"></div><div class="edge-bottom"></div></div>';
                    borderDivs += '<div class="border-right border-style-' + options.styleBorder + '"><div class="edge-top"></div><div class="edge-bottom"></div></div>';
                }
                wrap.append(borderDivs);
            }
            // Shadow
            if (options.styleShadow != 0) {
                var shadowDivs = '<div class="md-shadow md-shadow-style-' + options.styleShadow + '"></div>';
            }
            // Next, preview arrow
            if (options.showArrow) {
                arrowButton = $('<div class="md-arrow"><div class="md-arrow-left"><span></span></div><div class="md-arrow-right"><span></span></div></div>');
                hoverDiv.append(arrowButton);
                $('.md-arrow-right', arrowButton).bind('click', function () {
                    slideNext();
                });
                $('.md-arrow-left', arrowButton).bind('click', function () {
                    slidePrev();
                });
            };
            if (options.showBullet != false) {
                buttons = $('<div class="md-bullets"></div>');
                wrap.append(buttons);
                for (var i = 0; i < numItem; i++) {
                    buttons.append('<div class="md-bullet"  rel="' + i + '"><a></a></div>');
                };
                if (options.showThumb) {
                    var thumbW = parseInt(self.data("thumb-width")),
                        thumbH = parseInt(self.data("thumb-height"));
                    for (var i = 0; i < numItem; i++) {
                        var thumbSrc = slideItems[i].data("thumb"),
                            thumbType = slideItems[i].data("thumb-type"),
                            thumbAlt = slideItems[i].data("thumb-alt");
                        if(thumbSrc) {
                            var thumb;
                            if (thumbType == "image")
                                thumb = $('<img />').attr("src", thumbSrc).attr("alt", slideItems[i].data("thumb-alt")).css({top: -(9 + thumbH) + "px", left: -(thumbW/2 - 2) + "px", opacity: 0})
                            else
                                thumb = $("<span></span>").attr("style", thumbSrc).css({top: -(9 + thumbH) + "px", left: -(thumbW/2 - 2) + "px", opacity: 0});
                            $('div.md-bullet:eq(' + i + ')', buttons).append(thumb).append('<div class="md-thumb-arrow" style="opacity: 0"></div>');
                        }
                    }
                    $('div.md-bullet', buttons).hover(function () {
                        $(this).addClass('md_hover');
                        $("img, span", this).show().animate({'opacity':1},200);
                        $('.md-thumb-arrow', this).show().animate({'opacity':1}, 200);
                    }, function () {
                        $(this).removeClass('md_hover');
                        $('img, span', this).animate({'opacity':0}, 200,function(){
                            $(this).hide();
                        });
                        $('.md-thumb-arrow',this).animate({'opacity':0},200,function(){
                            $(this).hide();
                        });
                    });
                }
                $('div.md-bullet', wrap).click(function () {
                    if ($(this).hasClass('md-current')) {
                        return false;
                    };
                    var index = $(this).attr('rel');
                    slide(index);
                });
            } else if (options.showThumb) {
                var thumbDiv = $('<div class="md-thumb"><div class="md-thumb-container"><div class="md-thumb-items"></div></div></div>').appendTo(wrap);
                slideThumb =  $(".md-thumb-items", thumbDiv);
                for (var i = 0; i < numItem; i++) {
                    var thumbSrc = slideItems[i].data("thumb"),
                        thumbType = slideItems[i].data("thumb-type"),
                        thumbAlt = slideItems[i].data("thumb-alt");

                    if(thumbSrc) {
                        var $link = $('<a class="md-thumb-item" />').attr("rel", i);
                        if (thumbType == "image")
                            $link.append($('<img />').attr("src", thumbSrc).attr("alt", slideItems[i].data("thumb-alt")))
                        else
                            $link.append($('<span />').attr("style", thumbSrc).css("display", "inline-block"));
                        slideThumb.append($link);
                    }
                }
                $("a", slideThumb).click(function() {
                    if ($(this).hasClass('md-current') || thumbsDrag) {
                        return false;
                    };
                    var index = $(this).attr('rel');
                    slide(index);
                });
            }
        }
        function initDrag() {
            if(hasTouch) {
                self.bind('touchstart', function (event) {
                    if(touchstart) return false;
                    event = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
                    touchstart = true;
                    isScrolling = undefined;
                    self.mouseY = event.pageY;
                    self.mouseX = event.pageX;
                });
                self.bind('touchmove', function (event) {
                    event = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
                    if (touchstart) {
                        var pageX = (event.pageX || event.clientX);
                        var pageY = (event.pageY || event.clientY);

                        if ( typeof isScrolling == 'undefined') {
                            isScrolling = !!( isScrolling || Math.abs(pageY - self.mouseY) > Math.abs( pageX - self.mouseX ) )
                        }
                        if (isScrolling ) {
                            touchstart = false;
                            return
                        } else {
                            mouseleft = pageX - self.mouseX;
                            return false;
                        }
                    };
                    return ;
                });
                self.bind('touchend', function (event) {
                    if(touchstart) {
                        touchstart = false;
                        if(mouseleft > options.touchSensitive) {
                            slidePrev();
                            mouseleft = 0;
                            return false;
                        } else if(mouseleft < -options.touchSensitive) {
                            slideNext();
                            mouseleft = 0;
                            return false;
                        }
                    }
                });
            } else {
                hoverDiv.hover(function() {
                    if (arrowButton) {
                        arrowButton.stop(true, true).animate({opacity:1},200);
                    }
                }, function() {
                    if (arrowButton) {
                        arrowButton.stop(true, true).animate({opacity:0},200);
                    }
                });
                wrap.trigger("hover");
            }

            if (options.enableDrag) {
                self.mousedown(function (event) {
                    if (!touchstart) {
                        touchstart = true;
                        isScrolling = undefined;
                        self.mouseY = event.pageY;
                        self.mouseX = event.pageX;
                    };
                    return false;
                });
                self.mousemove(function (event) {
                    if (touchstart) {
                        var pageX = (event.pageX || event.clientX);
                        var pageY = (event.pageY || event.clientY);

                        if ( typeof isScrolling == 'undefined') {
                            isScrolling = !!( isScrolling || Math.abs(pageY - self.mouseY) > Math.abs( pageX - self.mouseX ) )
                        }
                        if (isScrolling ) {
                            touchstart = false;
                            return
                        } else {
                            mouseleft = pageX - self.mouseX;
                            return false;
                        }
                    };
                    return ;
                });
                self.mouseup(function (event) {
                    if(touchstart) {
                        touchstart = false;
                        if(mouseleft > options.touchSensitive) {
                            slidePrev();
                        } else if(mouseleft < -options.touchSensitive) {
                            slideNext();
                        }
                        mouseleft = 0;
                        return false;
                    }
                });
                self.mouseleave(function (event) {
                    self.mouseup();
                });
            };

        }
        function resizeThumbDiv() {
            if(slideThumb) {
                slideThumb.unbind("touchstart");
                slideThumb.unbind("touchmove");
                slideThumb.unbind("touchmove");
                slideThumb.css("left", 0);
                var thumbsWidth = 0,
                    thumbDiv = slideThumb.parent().parent();

                $("a.md-thumb-item", slideThumb).each(function() {

                    if ($("img", $(this)).length > 0) {
                        if ($("img", $(this)).css("borderLeftWidth"))
                            thumbsWidth += parseInt($("img", $(this)).css("borderLeftWidth"), 10);
                        if ($("img", $(this)).css("borderRightWidth"))
                            thumbsWidth += parseInt($("img", $(this)).css("borderRightWidth"), 10);
                        if ($("img", $(this)).css("marginLeft"))
                            thumbsWidth += parseInt($("img", $(this)).css("marginLeft"), 10);
                        if ($("img", $(this)).css("marginRight"))
                            thumbsWidth += parseInt($("img", $(this)).css("marginRight"), 10);

                    }
                    else {
                        if ($("span", $(this)).css("borderLeftWidth"))
                            thumbsWidth += parseInt($("span", $(this)).css("borderLeftWidth"), 10);
                        if ($("span", $(this)).css("borderRightWidth"))
                            thumbsWidth += parseInt($("span", $(this)).css("borderRightWidth"), 10);
                        if ($("span", $(this)).css("marginLeft"))
                            thumbsWidth += parseInt($("span", $(this)).css("marginLeft"), 10);
                        if ($("span", $(this)).css("marginRight"))
                            thumbsWidth += parseInt($("span", $(this)).css("marginRight"), 10);
                    }

                    if ($(this).css("borderLeftWidth"))
                        thumbsWidth += parseInt($(this).css("borderLeftWidth"), 10);
                    if ($(this).css("borderRightWidth"))
                        thumbsWidth += parseInt($(this).css("borderRightWidth"), 10);
                    if ($(this).css("marginLeft"))
                        thumbsWidth += parseInt($(this).css("marginLeft"), 10);
                    if ($(this).css("marginRight"))
                        thumbsWidth += parseInt($(this).css("marginRight"), 10);

                    thumbsWidth += parseInt(self.data("thumb-width"));
                });

                $(".md-thumb-next", thumbDiv).remove();
                $(".md-thumb-prev", thumbDiv).remove();
                if(thumbsWidth > $(".md-thumb-container", thumbDiv).width()) {
                    minThumbsLeft = $(".md-thumb-container", thumbDiv).width() - thumbsWidth;
                    slideThumb.width(thumbsWidth);
                    thumbDiv.append('<div class="md-thumb-prev"></div><div class="md-thumb-next"></div>');
                    $(".md-thumb-prev", thumbDiv).click(function() {
                        scollThumb("right");
                    });
                    $(".md-thumb-next", thumbDiv).click(function() {
                        scollThumb("left");
                    });

                    checkThumbArrow();
                    if(hasTouch) {
                        thumbsDrag = true;

                        var thumbTouch, thumbLeft;
                        slideThumb.bind('touchstart', function (event) {
                            event = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
                            thumbTouch = true;
                            this.mouseX = event.pageX;
                            thumbLeft = slideThumb.position().left;
                            return false;
                        });
                        slideThumb.bind('touchmove', function (event) {
                            event.preventDefault();
                            event = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
                            if (thumbTouch) {
                                slideThumb.css("left", thumbLeft + event.pageX - this.mouseX);
                            };
                            return false;
                        });
                        slideThumb.bind('touchend', function (event) {
                            event.preventDefault();
                            event = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
                            thumbTouch = false;
                            if(Math.abs(event.pageX - this.mouseX) < options.touchSensitive) {
                                var item = $(event.target).closest('a.md-thumb-item');
                                if(item.length) {
                                    slide(item.attr('rel'));
                                }
                                slideThumb.stop(true, true).animate({left: thumbLeft}, 400);
                                return false;
                            }
                            if(slideThumb.position().left < minThumbsLeft) {
                                slideThumb.stop(true, true).animate({left: minThumbsLeft}, 400, function() {checkThumbArrow()});
                            } else if(slideThumb.position().left > 0) {
                                slideThumb.stop(true, true).animate({left: 0}, 400, function() {checkThumbArrow()});
                            }
                            thumbLeft = 0;
                            return false;
                        });
                    }
                }
            }
        }
        function scollThumb(position) {
            if(slideThumb) {
                if(position == "left") {
                    var thumbLeft = slideThumb.position().left;
                    if(thumbLeft > minThumbsLeft) {
                        var containerWidth = $(".md-thumb-container", wrap).width();
                        if((thumbLeft - containerWidth) > minThumbsLeft) {
                            slideThumb.stop(true, true).animate({left: thumbLeft - containerWidth}, 400, function() {checkThumbArrow()});
                        } else {
                            slideThumb.stop(true, true).animate({left: minThumbsLeft}, 400, function() {checkThumbArrow()});
                        }
                    }
                } else if(position == "right") {
                    var thumbLeft = slideThumb.position().left;
                    if(thumbLeft < 0) {
                        var containerWidth = $(".md-thumb-container", wrap).width();
                        if((thumbLeft + containerWidth) < 0) {
                            slideThumb.stop(true, true).animate({left: thumbLeft + containerWidth}, 400, function() {checkThumbArrow()});
                        } else {
                            slideThumb.stop(true, true).animate({left: 0}, 400, function() {checkThumbArrow()});
                        }
                    }
                } else {
                    var thumbCurrent = $("a", slideThumb).index($("a.md-current", slideThumb));
                    if(thumbCurrent >= 0) {
                        var thumbLeft = slideThumb.position().left;
                        var currentLeft = thumbCurrent * $("a", slideThumb).width();
                        if(currentLeft + thumbLeft < 0) {
                            slideThumb.stop(true, true).animate({left: -currentLeft}, 400, function() {checkThumbArrow()});
                        } else {
                            var currentRight = currentLeft + $("a", slideThumb).width();
                            var containerWidth = $(".md-thumb-container", wrap).width();
                            if (currentRight + thumbLeft > containerWidth) {
                                slideThumb.stop(true, true).animate({left: containerWidth - currentRight}, 400, function() {checkThumbArrow()});
                            }
                        }
                    }
                }
            }
        }
        function checkThumbArrow() {
            var thumbLeft = slideThumb.position().left;
            if(thumbLeft > minThumbsLeft) {
                $(".md-thumb-next", wrap).show();
            } else {
                $(".md-thumb-next", wrap).hide();
            }
            if(thumbLeft < 0) {
                $(".md-thumb-prev", wrap).show();
            } else {
                $(".md-thumb-prev", wrap).hide();
            }
        }

        function slide(index) {
            step = 0;
            slideShowDelay = slideItems[index].data("timeout") ? slideItems[index].data("timeout") : options.slideShowDelay;
            if(loadingBar) {
                var width = step * slideWidth / slideShowDelay;
                loadingBar.width(width);
                timerGlow.css({left: width - 100 + 'px'});
            }
			oIndex = activeIndex;
			activeIndex = index;
			options.onStartTransition.call(self);
			if (slideItems[oIndex]) {
				$('div.md-bullet:eq(' + oIndex + ')', buttons).removeClass('md-current');
                $('a:eq(' + oIndex + ')', slideThumb).removeClass('md-current');
				removeTheCaptions(slideItems[oIndex]);
				var fx = options.transitions;
				//Generate random transition
				if (options.transitions.toLowerCase() == 'random') {
					var transitions = new Array(
						'slit-horizontal-left-top',
						'slit-horizontal-top-right',
						'slit-horizontal-bottom-up',
						'slit-vertical-down',
						'slit-vertical-up',
						'strip-up-right',
						'strip-up-left',
						'strip-down-right',
						'strip-down-left',
						'strip-left-up',
						'strip-left-down',
						'strip-right-up',
						'strip-right-down',
						'strip-right-left-up',
						'strip-right-left-down',
						'strip-up-down-right',
						'strip-up-down-left',
						'left-curtain',
						'right-curtain',
						'top-curtain',
						'bottom-curtain',
						'slide-in-right',
						'slide-in-left',
						'slide-in-up',
						'slide-in-down',
                        'fade');
					fx = transitions[Math.floor(Math.random() * (transitions.length + 1))];
					if (fx == undefined) fx = 'fade';
					fx = $.trim(fx.toLowerCase());
				}

				//Run random transition from specified set (eg: effect:'strip-left-fade,right-curtain')
				if (options.transitions.indexOf(',') != -1) {
					var transitions = options.transitions.split(',');
					fx = transitions[Math.floor(Math.random() * (transitions.length))];
					if (fx == undefined) fx = 'fade';
					fx = $.trim(fx.toLowerCase());
				}

				//Custom transition as defined by "data-transition" attribute
				if (slideItems[activeIndex].data('transition')) {
					var transitions = slideItems[activeIndex].data('transition').split(',');
					fx = transitions[Math.floor(Math.random() * (transitions.length))];
					fx = $.trim(fx.toLowerCase());
				}
				if(!(this.support = Modernizr.csstransitions && Modernizr.csstransforms3d) && (fx == 'slit-horizontal-left-top' || fx == 'slit-horizontal-top-right' || fx == 'slit-horizontal-bottom-up' || fx == 'slit-vertical-down' || fx == 'slit-vertical-up')) {
					fx = 'fade';
				}
				lock = true;
				runTransition(fx);
				if(buttons)
					$('div.md-bullet:eq(' + activeIndex + ')', buttons).addClass('md-current');
				if(slideThumb)
					$('a:eq(' + activeIndex + ')', slideThumb).addClass('md-current');
                scollThumb();
			} else {
				slideItems[activeIndex].css({top:0, left:0}).show();
				animateTheCaptions(slideItems[index]);
				if(buttons)
					$('div.md-bullet:eq(' + activeIndex + ')', buttons).addClass('md-current');
				if(slideThumb)
					$('a:eq(' + activeIndex + ')', slideThumb).addClass('md-current');
                scollThumb();
				lock = false;
			}
        }
        function setTimer() {
            slide(0);
            timer = setInterval(next, 40);
        }
        function next() {
            if(lock) return false;
            if(play && !pause) {
                step += 40;
                if(step > slideShowDelay) {
                    slideNext();
                } else if(loadingBar) {
                    var width = step * slideWidth / slideShowDelay;
                    loadingBar.width(width);
                    timerGlow.css({left: width - 100 + 'px'});
                }
            }
        }

        function slideNext() {
            if(lock) return false;
            var index = activeIndex;
            index++;
            if(index >= numItem && options.loop) {
                index = 0;
                slide(index);
            } else if(index < numItem) {
                slide(index);
            }
        }
        function slidePrev() {
            if(lock) return false;
            var index = activeIndex;
            index--;
            if(index < 0 && options.loop) {
                index = numItem - 1;
                slide(index);
            }
            else if(index >= 0) {
                slide(index);
            }
        }
        function endMoveCaption(caption) {
            var easeout = (caption.data("easeout")) ? caption.data("easeout") : "",
                ieVersion = (!! window.ActiveXObject && +(/msie\s(\d+)/i.exec(navigator.userAgent)[1])) || NaN;

            if (ieVersion != NaN)
                ieVersion = 11;
            else
                ieVersion = parseInt(ieVersion);

            clearTimeout(caption.data('timer-start'));
            if (easeout != "" && easeout != "keep" && ieVersion <= 9)
                caption.fadeOut();
            else {
                caption.removeClass(effectsIn.join(' '));
                if(easeout != "") {
                    if(easeout == "random")
                        easeout = effectsOut[Math.floor(Math.random() * e_out_length)];
                    caption.addClass(easeout);
                }
                else
                    caption.hide();
            }
        }
        function removeTheCaptions(oItem) {
            oItem.find(".md-object").each(function() {
                var caption = $(this);
                caption.stop(true, true).hide();
                clearTimeout(caption.data('timer-start'));
                clearTimeout(caption.data('timer-stop'));
            });
        }
        function animateTheCaptions(nextItem) {
            $(".md-object", nextItem).each(function (boxIndex) {
                var caption = $(this);
                if(caption.data("easeout"))
                    caption.removeClass(effectsOut.join(' '));
                var easein = caption.data("easein") ? caption.data("easein") : "",
                    ieVersion = (!! window.ActiveXObject && +(/msie\s(\d+)/i.exec(navigator.userAgent)[1])) || NaN;

                if (ieVersion != NaN)
                    ieVersion = 11;
                else
                    ieVersion = parseInt(ieVersion);

                if(easein == "random")
                    easein = effectsIn[Math.floor(Math.random() * e_in_length)];

                caption.removeClass(effectsIn.join(' '));
                caption.hide();
                if(caption.data("start") != undefined) {
                    caption.data('timer-start', setTimeout(function() {
                        if (easein != "" && ieVersion <= 9)
                            caption.fadeIn();
                        else
                            caption.show().addClass(easein);
                    }, caption.data("start")));
                }
                else
                    caption.show().addClass(easein);

                if(caption.data("stop") != undefined) {
                    caption.data('timer-stop', setTimeout(function() {
                        endMoveCaption(caption);
                    }, caption.data('stop')));
                }
            });
        }
        //When Animation finishes
        function transitionEnd() {
            options.onEndTransition.call(self);
            $('.md-strips-container', self).remove();
            slideItems[oIndex].hide();
            slideItems[activeIndex].show();
            lock = false;
            animateTheCaptions(slideItems[activeIndex]);
        }

        // Add strips
        function addStrips(vertical, opts) {
            var strip,
                opts = (opts) ? opts : options,
                stripsContainer = $('<div class="md-strips-container"></div>'),
                stripWidth = Math.round(slideWidth / opts.strips),
                stripHeight = Math.round(slideHeight / opts.strips),
                $image = $(".md-mainimg img", slideItems[activeIndex]);

            if ($image.length == 0)
                $image = $(".md-mainimg", slideItems[activeIndex]);

            for (var i = 0; i < opts.strips; i++) {
                 var top = ((vertical) ? (stripHeight * i) + 'px' : '0px'),
                     left = ((vertical) ? '0px' : (stripWidth * i) + 'px'),
                     width, height;

                if (i == opts.strips - 1) {
                    width = ((vertical) ? '0px' : (slideWidth - (stripWidth * i)) + 'px'),
                    height = ((vertical) ? (slideHeight - (stripHeight * i)) + 'px' : '0px');
                } else {
                    width = ((vertical) ? '0px' : stripWidth + 'px'),
                    height = ((vertical) ? stripHeight + 'px' : '0px');
                }

                strip = $('<div class="mdslider-strip"></div>').css({
                    width: width,
                    height: height,
                    top: top,
                    left: left,
                    opacity: 0
                }).append($image.clone().css({
                    marginLeft: vertical ? 0 : -(i * stripWidth) + "px",
                    marginTop: vertical ? -(i * stripHeight) + "px" : 0
                }));
                stripsContainer.append(strip);
            }
            self.append(stripsContainer);
        }
        // Add strips
        function addTiles(x, y, index) {
            var tile;
            var stripsContainer = $('<div class="md-strips-container"></div>');
            var tileWidth = slideWidth / x,
                tileHeight = slideHeight / y,
                $image = $(".md-mainimg img", slideItems[index]);

            if ($image.length == 0)
                $image = $(".md-mainimg", slideItems[index]);

            for(var i = 0; i < y; i++) {
                for(var j = 0; j < x; j++) {
                    var top = (tileHeight * i) + 'px',
                        left = (tileWidth * j) + 'px';
                    tile = $('<div class="mdslider-tile"/>').css({
                        width: tileWidth,
                        height: tileHeight,
                        top: top,
                        left: left
                    }).append($image.clone().css({
                            marginLeft: "-" + left,
                            marginTop: "-" + top
                    }));
                    stripsContainer.append(tile);
                }
            }
            self.append(stripsContainer);
        }
        // Add strips
        function addStrips2() {
            var strip,
                images = [],
                stripsContainer = $('<div class="md-strips-container"></div>');

            $(".md-mainimg img", slideItems[oIndex]), $(".md-mainimg img", slideItems[activeIndex])

            if ($(".md-mainimg img", slideItems[oIndex]).length > 0)
                images.push($(".md-mainimg img", slideItems[oIndex]));
            else
                images.push($(".md-mainimg", slideItems[oIndex]));

            if ($(".md-mainimg img", slideItems[activeIndex]).length > 0)
                images.push($(".md-mainimg img", slideItems[activeIndex]));
            else
                images.push($(".md-mainimg", slideItems[activeIndex]));

            for (var i = 0; i < 2; i++) {
                strip = $('<div class="mdslider-strip"></div>').css({
                    width: slideWidth,
                    height: slideHeight
                }).append(images[i].clone());
                stripsContainer.append(strip);
            }
            self.append(stripsContainer);
        }
        // Add strips
        function addSlits(fx) {
            var $stripsContainer = $('<div class="md-strips-container ' + fx + '"></div>'),
                $image = ($(".md-mainimg img", slideItems[oIndex]).length > 0) ? $(".md-mainimg img", slideItems[oIndex]) : $(".md-mainimg", slideItems[oIndex]),
                $div1 = $('<div class="mdslider-slit"/>').append($image.clone()),
                $div2 = $('<div class="mdslider-slit"/>'),
                position = $image.position();

            $div2.append($image.clone().css("top", position.top - (slideHeight/2) + "px"));
            if(fx == "slit-vertical-down" || fx == "slit-vertical-up")
                $div2 = $('<div class="mdslider-slit"/>').append($image.clone().css("left", position.left - (slideWidth/2) + "px"));

            $stripsContainer.append($div1).append($div2);
            self.append($stripsContainer);
        }
        function runTransition(fx) {
            switch (fx) {
                case 'slit-horizontal-left-top':
                case 'slit-horizontal-top-right':
                case 'slit-horizontal-bottom-up':
                case 'slit-vertical-down':
                case 'slit-vertical-up':
                    addSlits(fx);
                    $(".md-object", slideItems[activeIndex]).hide();
                    slideItems[oIndex].hide();
                    slideItems[activeIndex].show();
                    var slice1 = $('.mdslider-slit', self).first(),
                        slice2 = $('.mdslider-slit', self).last();
                    var transitionProp = {
                        'transition' : 'all ' + options.transitionsSpeed + 'ms ease-in-out',
						'-webkit-transition' : 'all ' + options.transitionsSpeed + 'ms ease-in-out',
						'-moz-transition' : 'all ' + options.transitionsSpeed + 'ms ease-in-out',
						'-ms-transition' : 'all ' + options.transitionsSpeed + 'ms ease-in-out'
                    };
                    $('.mdslider-slit', self).css(transitionProp);
                    setTimeout( function() {
                        slice1.addClass("md-trans-elems-1");
                        slice2.addClass("md-trans-elems-2");
                    }, 50 );
                    setTimeout(function() {
                        options.onEndTransition.call(self);
                        $('.md-strips-container', self).remove();
                        lock = false;
                        animateTheCaptions(slideItems[activeIndex]);
                    }, options.transitionsSpeed);
                    break;
                case 'strip-up-right':
                case 'strip-up-left':
                    addTiles(options.stripCols, 1, activeIndex);
                    var strips = $('.mdslider-tile', self),
                        timeStep = options.transitionsSpeed / options.stripCols / 2,
                        speed = options.transitionsSpeed / 2;
                    if (fx == 'strip-up-right') strips = $('.mdslider-tile', self).reverse();
                    strips.css({
                        height: '1px',
                        bottom: '0px',
                        top: "auto"
                    });
                    strips.each(function (i) {
                        var strip = $(this);
                        setTimeout(function () {
                            strip.animate({
                                height: '100%',
                                opacity: '1.0'
                            }, speed, 'easeInOutQuart', function () {
                                if (i == options.stripCols - 1) transitionEnd();
                            });
                        }, i * timeStep);
                    });
                    break;
                case 'strip-down-right':
                case 'strip-down-left':
                    addTiles(options.stripCols, 1, activeIndex);
                    var strips = $('.mdslider-tile', self),
                        timeStep = options.transitionsSpeed / options.stripCols / 2,
                        speed = options.transitionsSpeed / 2;
                    if (fx == 'strip-down-right') strips = $('.mdslider-tile', self).reverse();
                    strips.css({
                        height: '1px',
                        top: '0px',
                        bottom: "auto"
                    });
                    strips.each(function (i) {
                        var strip = $(this);
                        setTimeout(function () {
                            strip.animate({
                                height: '100%',
                                opacity: '1.0'
                            }, speed, 'easeInOutQuart', function () {
                                if (i == options.stripCols - 1) transitionEnd();
                            });
                        }, i * timeStep);
                    });
                    break;
                case 'strip-left-up':
                case 'strip-left-down':
                    addTiles(1, options.stripRows, activeIndex);
                    var strips = $('.mdslider-tile', self),
                        timeStep = options.transitionsSpeed / options.stripRows / 2,
                        speed = options.transitionsSpeed / 2;
                    if (fx == 'strip-left-up') strips = $('.mdslider-tile', self).reverse();
                    strips.css({
                        width: '1px',
                        left: '0px',
                        right: "auto"
                    });
                    strips.each(function (i) {
                        var strip = $(this);
                        setTimeout(function () {
                            strip.animate({
                                width: '100%',
                                opacity: '1.0'
                            }, speed, 'easeInOutQuart', function () {
                                if (i == options.stripRows - 1) transitionEnd();
                            });
                        }, i * timeStep);
                    });
                    break;
                case 'strip-right-up':
                case 'strip-right-down':
                    addTiles(1, options.stripRows, activeIndex);
                    var strips = $('.mdslider-tile', self),
                        timeStep = options.transitionsSpeed / options.stripRows / 2,
                        speed = options.transitionsSpeed / 2;
                    if (fx == 'strip-left-right-up') strips = $('.mdslider-tile', self).reverse();
                    strips.css({
                        width: '1px',
                        left: 'auto',
                        right: "1px"
                    });
                    strips.each(function (i) {
                        var strip = $(this);
                        setTimeout(function () {
                            strip.animate({
                                width: '100%',
                                opacity: '1.0'
                            }, speed, 'easeInOutQuart', function () {
                                if (i == options.stripRows - 1) transitionEnd();
                            });
                        }, i * timeStep);
                    });
                    break;
                case 'strip-right-left-up':
                case 'strip-right-left-down':
                    addTiles(1, options.stripRows, oIndex);
                    slideItems[oIndex].hide();
                    slideItems[activeIndex].show();
                    var strips = $('.mdslider-tile', self),
                        timeStep = options.transitionsSpeed / options.stripRows,
                        speed = options.transitionsSpeed / 2;
                    if (fx == 'strip-right-left-up') strips = $('.mdslider-tile', self).reverse();
                    strips.filter(':odd').css({
                        width: '100%',
                        right: '0px',
                        left: "auto",
                        opacity: 1
                    }).end().filter(':even').css({
                            width: '100%',
                            right: 'auto',
                            left: "0px",
                            opacity: 1
                     });;
                    strips.each(function (i) {
                        var strip = $(this);
                        var css = (i%2 == 0) ? {left: '-50%',opacity: '0'} : {right: '-50%', opacity: '0'};
                        setTimeout(function () {
                            strip.animate(css, speed, 'easeOutQuint', function () {
                                if (i == options.stripRows - 1) {
                                    options.onEndTransition.call(self);
                                    $('.md-strips-container', self).remove();
                                    lock = false;
                                    animateTheCaptions(slideItems[activeIndex]);
                                }
                            });
                        }, i * timeStep);
                    });
                    break;
                case 'strip-up-down-right':
                case 'strip-up-down-left':
                    addTiles(options.stripCols, 1, oIndex);
                    slideItems[oIndex].hide();
                    slideItems[activeIndex].show();
                    var strips = $('.mdslider-tile', self),
                        timeStep = options.transitionsSpeed / options.stripCols / 2 ,
                        speed = options.transitionsSpeed / 2;
                    if (fx == 'strip-up-down-right') strips = $('.mdslider-tile', self).reverse();
                    strips.filter(':odd').css({
                        height: '100%',
                        bottom: '0px',
                        top: "auto",
                        opacity: 1
                    }).end().filter(':even').css({
                            height: '100%',
                            bottom: 'auto',
                            top: "0px",
                            opacity: 1
                        });;
                    strips.each(function (i) {
                        var strip = $(this);
                        var css = (i%2 == 0) ? {top: '-50%',opacity: 0} : {bottom: '-50%', opacity: 0};
                        setTimeout(function () {
                            strip.animate(css, speed, 'easeOutQuint', function () {
                                if (i == options.stripCols - 1) {
                                    options.onEndTransition.call(self);
                                    $('.md-strips-container', self).remove();
                                    lock = false;
                                    animateTheCaptions(slideItems[activeIndex]);
                                }
                            });
                        }, i * timeStep);
                    });
                    break;
                case 'left-curtain':
                    addTiles(options.stripCols, 1, activeIndex);
                    var strips = $('.mdslider-tile', self),
                        width = slideWidth / options.stripCols,
                        timeStep = options.transitionsSpeed / options.stripCols / 2;
                    strips.each(function (i) {
                        var strip = $(this);
                        strip.css({left: width * i, width: 0, opacity: 0});
                        setTimeout(function () {
                            strip.animate({
                                width: width,
                                opacity: '1.0'
                            }, options.transitionsSpeed / 2, function () {
                                if (i == options.stripCols - 1) transitionEnd();
                            });
                        }, timeStep * i);
                    });
                    break;
                case 'right-curtain':
                    addTiles(options.stripCols, 1, activeIndex);
                    var strips = $('.mdslider-tile', self).reverse(),
                        width = slideWidth / options.stripCols,
                        timeStep = options.transitionsSpeed / options.stripCols / 2;
                    strips.each(function (i) {
                        var strip = $(this);
                        strip.css({right: width * i, left: "auto", width: 0, opacity: 0});
                        setTimeout(function () {
                            strip.animate({
                                width: width,
                                opacity: '1.0'
                            }, options.transitionsSpeed / 2, function () {
                                if (i == options.stripCols - 1) transitionEnd();
                            });
                        }, timeStep * i);
                    });
                    break;
                case 'top-curtain':
                    addTiles(1, options.stripRows, activeIndex);
                    var strips = $('.mdslider-tile', self),
                        height = slideHeight / options.stripRows,
                        timeStep = options.transitionsSpeed / options.stripRows / 2;
                    strips.each(function (i) {
                        var strip = $(this);
                        strip.css({top: height * i, height: 0, opacity: 0});
                        setTimeout(function () {
                            strip.animate({
                                height: height,
                                opacity: '1.0'
                            }, options.transitionsSpeed / 2, function () {
                                if (i == options.stripRows - 1) transitionEnd();
                            });
                        }, timeStep * i);
                    });
                    break;
                case 'bottom-curtain':
                    addTiles(1, options.stripRows, activeIndex);
                    var strips = $('.mdslider-tile', self).reverse(),
                        height = slideHeight / options.stripRows,
                        timeStep = options.transitionsSpeed / options.stripRows / 2;
                    strips.each(function (i) {
                        var strip = $(this);
                        strip.css({bottom: height * i, height: 0, opacity: 0});
                        setTimeout(function () {
                            strip.animate({
                                height: height,
                                opacity: '1.0'
                            }, options.transitionsSpeed / 2, function () {
                                if (i == options.stripRows - 1) transitionEnd();
                            });
                        }, timeStep * i);
                    });
                    break;
                case 'slide-in-right':
                    var i = 0;
                    addStrips2();
                    var strips = $('.mdslider-strip', self);
                    strips.each(function() {
                        strip = $(this);
                        var left = i * slideWidth;
                        strip.css({
                            left: left
                        });
                        strip.animate({
                            left: left - slideWidth
                        }, options.transitionsSpeed, function () {
                            transitionEnd();
                        });
                        i++;
                    });
                    break;
                case 'slide-in-left':
                    var i = 0;
                    addStrips2();
                    var strips = $('.mdslider-strip', self);
                    strips.each(function() {
                        strip = $(this);
                        var left = -i * slideWidth;
                        strip.css({
                            left: left
                        });
                        strip.animate({
                            left: slideWidth + left
                        }, (options.transitionsSpeed * 2), function () {
                            transitionEnd();
                        });
                        i++;
                    });
                    break;
                case 'slide-in-up':
                    var i = 0;
                    addStrips2();
                    var strips = $('.mdslider-strip', self);
                    strips.each(function() {
                        strip = $(this);
                        var top = i * slideHeight;
                        strip.css({
                            top: top
                        });
                        strip.animate({
                            top: top - slideHeight
                        }, options.transitionsSpeed, function () {
                            transitionEnd();
                        });
                        i++;
                    });
                    break;
                case 'slide-in-down':
                    var i = 0;
                    addStrips2();
                    var strips = $('.mdslider-strip', self);
                    strips.each(function() {
                        strip = $(this);
                        var top = -i * slideHeight;
                        strip.css({
                            top: top
                        });
                        strip.animate({
                            top: slideHeight + top
                        }, options.transitionsSpeed, function () {
                            transitionEnd();
                        });
                        i++;
                    });
                    break;
                case 'fade':
                default:
                    var opts = {
                        strips: 1
                    };
                    addStrips(false, opts);
                    var strip = $('.mdslider-strip:first', self);
                    strip.css({
                        'height': '100%',
                        'width': slideWidth
                    });
                    if (fx == 'slide-in-right') strip.css({
                        'height': '100%',
                        'width': slideWidth,
                        'left': slideWidth + 'px',
                        'right': ''
                    });
                    else if (fx == 'slide-in-left') strip.css({
                        'left': '-' + slideWidth + 'px'
                    });

                    strip.animate({
                        left: '0px',
                        opacity: 1
                    }, options.transitionsSpeed, function () {
                        transitionEnd();
                    });
                    break;
            }
        }

        // Shuffle an array
        function shuffle(oldArray) {
            var newArray = oldArray.slice();
            var len = newArray.length;
            var i = len;
            while (i--) {
                var p = parseInt(Math.random() * len);
                var t = newArray[i];
                newArray[i] = newArray[p];
                newArray[p] = t;
            }
            return newArray;
        }
        function documentHasTouch() {
            return ('ontouchstart' in window || 'createTouch' in document);
        }
        function resizeWindow() {
            wrap.width();
            slideWidth = options.responsive ? wrap.width() : options.width;
            if(options.responsive) {
                if (options.fullwidth && slideWidth > options.width)
                    slideHeight = options.height;
                else
                    slideHeight =  Math.round(slideWidth/options.width * options.height);
            }

            if(!options.responsive && !options.fullwidth)
                wrap.width(slideWidth);
            if(!options.responsive && options.fullwidth)
                wrap.css({"min-width": slideWidth + "px"});
            if (options.fullwidth) {
                $(".md-objects", self).width(options.width);
                var bulletSpace = 20;
                if ((wrap.width() - options.width)/2 > 20)
                    bulletSpace = (wrap.width() - options.width)/2;
                wrap.find(".md-bullets").css({'left':bulletSpace,'right':bulletSpace});
                wrap.find(".md-thumb").css({'left':bulletSpace,'right':bulletSpace});
            }
            if(options.responsive && options.fullwidth && (wrap.width() < options.width))
                $(".md-objects", self).width(slideWidth);
            wrap.height(slideHeight);
            $(".md-slide-item", self).height(slideHeight);

            resizeBackgroundImage();
            resizeThumbDiv();
            resizeFontSize();
            resizePadding();
            setThumnail()
        }
        function resizeBackgroundImage() {
            $(".md-slide-item", self).each(function() {
                var $background = $(".md-mainimg img", this);

                if ($background.length == 1) {
                    if($background.data("defW") && $background.data("defH")) {
                        var width = $background.data("defW"),
                            height = $background.data("defH");
                        changeImagePosition($background, width, height);
                    }
                }
                else
                    $(".md-mainimg", $(this)).width($(".md-slide-item:visible", self).width()).height($(".md-slide-item:visible", self).height())
            });
        }
        function preloadImages() {
            var count = $(".md-slide-item .md-mainimg img", self).length;
            self.data('count', count);
            if(self.data('count') == 0)
                slideReady();
            $(".md-slide-item .md-mainimg img", self).each(function() {
                $(this).load(function() {
                    var $image = $(this);
                    if(!$image.data('defW')) {
                        var dimensions = getImgSize($image.attr("src"));
                        changeImagePosition($image, dimensions.width, dimensions.height);
                        $image.data({
                            'defW': dimensions.width,
                            'defH': dimensions.height
                        });
                    }
                    self.data('count', self.data('count') - 1);
                    if(self.data('count') == 0)
                        slideReady();
                });
                if(this.complete) $(this).load();
            });
        }
        function slideReady() {
            self.removeClass("loading-image");
            setTimer();
        }
        function changeImagePosition($background, width, height) {
            var panelWidth = $(".md-slide-item:visible", self).width(),
                panelHeight = $(".md-slide-item:visible", self).height();

            if(height > 0 && panelHeight > 0) {
                if (((width / height) > (panelWidth / panelHeight))) {
                    var left = panelWidth - (panelHeight / height) * width;
                    $background.css({width: "auto", height: panelHeight + "px"});
                    if(left < 0) {
                        $background.css({left: (left/2) + "px", top: 0 });
                    } else {
                        $background.css({left: 0, top: 0 });
                    }
                } else {
                    var top = panelHeight - (panelWidth / width) * height;
                    $background.css({width: panelWidth + "px", height: "auto"});
                    if(top < 0) {
                        $background.css({top: (top/2) + "px", left: 0 });
                    } else {
                        $background.css({left: 0, top: 0 });
                    }
                }
            }
        }
        function resizeFontSize() {
            var fontDiff = 1;
            if (parseInt($.browser.version, 10) < 9)
                fontDiff = 6;
            if (wrap.width() < options.width) {
                $(".md-objects", self).css({'font-size': wrap.width() / options.width * 100 - fontDiff + '%'});
            } else {
                $(".md-objects", self).css({'font-size': 100 - fontDiff + '%'});
            }
        }
        function resizePadding() {
            if (wrap.width() < options.width && options.responsive) {
                $(".md-objects div.md-object", self).each(function() {
                    var objectRatio = wrap.width() / options.width,
                        $_object = $(this),
                        objectPadding = [];
                    if ($_object.data('padding-top')) objectPadding['padding-top'] = $_object.data('padding-top') * objectRatio;
                    if ($_object.data('padding-right')) objectPadding['padding-right'] = $_object.data('padding-right') * objectRatio;
                    if ($_object.data('padding-bottom')) objectPadding['padding-bottom'] = $_object.data('padding-bottom') * objectRatio;
                    if ($_object.data('padding-left')) objectPadding['padding-left'] = $_object.data('padding-left') * objectRatio;
                    if ($_object.find('a').length) {
                        $_object.find('a').css(objectPadding);
                    } else {
                        $_object.css(objectPadding);
                    }

                })
            } else {
                $(".md-objects div.md-object", self).each(function() {
                    var $_object = $(this),
                        objectPadding = [];
                    if ($_object.data('padding-top')) objectPadding['padding-top'] = $_object.data('padding-top');
                    if ($_object.data('padding-right')) objectPadding['padding-right'] = $_object.data('padding-right');
                    if ($_object.data('padding-bottom')) objectPadding['padding-bottom'] = $_object.data('padding-bottom');
                    if ($_object.data('padding-left')) objectPadding['padding-left'] = $_object.data('padding-left');
                    if ($_object.find('a').length) {
                        $_object.find('a').css(objectPadding);
                    } else {
                        $_object.css(objectPadding);
                    }
                })
            }
        }
        function setThumnail() {
            if(options.showThumb && !options.showBullet) {
                thumbHeight = self.data('thumb-height');
                if(options.posThumb == '1') {
                    thumbBottom = thumbHeight / 2;
                    wrap.find(".md-thumb").css({'height': thumbHeight + 10,'bottom': -thumbBottom - 10});
                    wrap.css({'margin-bottom': thumbBottom + 10})
                } else {
                    wrap.find(".md-thumb").css({'height': thumbHeight + 10,'bottom': -(thumbHeight + 40)});
                    wrap.css({'margin-bottom': thumbHeight + 50})
                }
            }
        }
        function getImgSize(imgSrc) {
            var newImg = new Image();
            newImg.src = imgSrc;
            var dimensions = {height: newImg.height, width: newImg.width};
            return dimensions;
        }

        $(document).ready(function() {
            init();
        })
    }
    $.fn.reverse = [].reverse;
    //Image Preloader Function
    var ImagePreload = function (p_aImages, p_pfnPercent, p_pfnFinished) {
        this.m_pfnPercent = p_pfnPercent;
        this.m_pfnFinished = p_pfnFinished;
        this.m_nLoaded = 0;
        this.m_nProcessed = 0;
        this.m_aImages = new Array;
        this.m_nICount = p_aImages.length;
        for (var i = 0; i < p_aImages.length; i++) this.Preload(p_aImages[i])
    };

    ImagePreload.prototype = {
        Preload: function (p_oImage) {
            var oImage = new Image;
            this.m_aImages.push(oImage);
            oImage.onload = ImagePreload.prototype.OnLoad;
            oImage.onerror = ImagePreload.prototype.OnError;
            oImage.onabort = ImagePreload.prototype.OnAbort;
            oImage.oImagePreload = this;
            oImage.bLoaded = false;
            oImage.source = p_oImage;
            oImage.src = p_oImage
        },
        OnComplete: function () {
            this.m_nProcessed++;
            if (this.m_nProcessed == this.m_nICount) this.m_pfnFinished();
            else this.m_pfnPercent(Math.round((this.m_nProcessed / this.m_nICount) * 10))
        },
        OnLoad: function () {
            this.bLoaded = true;
            this.oImagePreload.m_nLoaded++;
            this.oImagePreload.OnComplete()
        },
        OnError: function () {
            this.bError = true;
            this.oImagePreload.OnComplete()
        },
        OnAbort: function () {
            this.bAbort = true;
            this.oImagePreload.OnComplete()
        }
    }
    $.fn.mdvideobox = function (opt) {
        $(this).each(function() {
            function init() {
                if($("#md-overlay").length == 0) {
                    var  _overlay = $('<div id="md-overlay" class="md-overlay"></div>').hide().click(closeMe);
                    var _container = $('<div id="md-videocontainer" class="md-videocontainer"><div id="md-video-embed"></div><div class="md-description clearfix"><div class="md-caption"></div><a id="md-closebtn" class="md-closebtn" href="#"></a></div></div>');
                    _container.css({'width': options.initialWidth + 'px', 'height': options.initialHeight + 'px', 'display': 'none'});
                    $("#md-closebtn", _container).click(closeMe);
                    $("body").append(_overlay).append(_container);
                }
                overlay = $("#md-overlay");
                container = $("#md-videocontainer");
                videoembed = $("#md-video-embed", container);
                caption = $(".md-caption", container);
                element.click(activate);
            }

            function closeMe()
            {
                overlay.fadeTo("fast", 0, function(){$(this).css('display','none')});
                videoembed.html('');
                container.hide();
                return false;
            }

            function activate()
            {
                options.click.call();
                overlay.css({'height': $(window).height()+'px'});
                var top = ($(window).height() / 2) - (options.initialHeight / 2);
                var left = ($(window).width() / 2) - (options.initialWidth / 2);
                container.css({top: top, left: left}).show();
                videoembed.css({'background': '#fff url(css/loading.gif) no-repeat center', 'height': options.contentsHeight, 'width': options.contentsWidth});
                overlay.css('display','block').fadeTo("fast", options.defaultOverLayFade);
                caption.html(title);
                videoembed.fadeIn("slow",function() { insert(); });
                return false;
            }

            function insert()
            {
                videoembed.css('background','#fff');
                embed = '<iframe src="' + videoSrc + '" width="' + options.contentsWidth + '" height="' + options.contentsHeight + '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                videoembed.html(embed);
            }

            var options = $.extend({
                initialWidth: 640,
                initialHeight: 400,
                contentsWidth: 640,
                contentsHeight: 350,
                defaultOverLayFade: 0.8,
                click: function() {}
            }, opt);
            var overlay, container, caption, videoembed, embed;
            var element = $(this);
            var videoSrc = element.attr("href");
            var title = element.attr("title");
            //lets start it
            init();
        });
    }
})(jQuery);
