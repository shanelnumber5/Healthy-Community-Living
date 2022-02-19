isIE = false;
var isiPad = (navigator.userAgent.match(/iPad/i) != null);
function validatedata($attr, $defaultValue) {
  if ($attr !== undefined) {
    return $attr
  }
  return $defaultValue;
}


(function($) {

  // All your code here
  $.fn.isAfter = function(sel) {
    return this.prevAll(sel).length !== 0;
  }
  $.fn.isBefore = function(sel) {
    return this.nextAll(sel).length !== 0;
  }

  $(document).ready(function() {
    var base_path = Drupal.settings.basePath;
    // begin document ready
    $("#showHeaderSearch").click(function() {
      var $this = $(this);
      var $searchform = $this.parent().find(".header-search");
      $searchform.fadeToggle(250, function() {

        if (($searchform).is(":visible")) {
          $this.find(".fa-search").removeClass("fa-search").addClass("fa-times");

          if (!isIE) {
            $searchform.find("[type=text]").focus();
          }
        } else {
          $this.find(".fa-times").removeClass("fa-times").addClass("fa-search");
        }
      });

      return false;


    });

    $("[data-toggle='tooltip']").tooltip();
    if ($(".octagon").length) {
      $(".octagon .svg-load").load(base_path + "sites/all/themes/pluto/images/octagon1.svg");
    }

    $('#toTop').click(function() {
      $("body,html").animate({scrollTop: 0}, 600);
      return false;
    });

    $(window).scroll(function() {
      if ($(this).scrollTop() != 0) {
        $("#toTop").fadeIn(300);
      } else {
        $("#toTop").fadeOut(250);
      }
    });






    $('.page-builder-row-section').each(function() {
      var current_row = $(this);
      if (current_row.find('.googleMap').length) {
        current_row.addClass('content-layer brightText google-map-layer');
      }
    });

    /// contact form
    $('.webform-client-form #webform-component-name, .webform-client-form #webform-component-email').addClass('col-md-6 item-wrap').wrapAll('<div class="row"></div>');
    $('.google-map-layer').each(function() {

      var $current_map = $(this);
      $current_map.find('.form-submit').addClass('btn-primary');
      $current_map.find('.block-webform').wrap('<div class="wow fadeInUpBig placeOver"><div class="container"><div class="row"><div class="col-md-8 col-md-push-2"></div></div></div></div>');
      $current_map.find('.showMap').click(function() {
        var $this = jQuery(this);
        var $parent = $current_map;
        var $form = $current_map.find(".placeOver");
        var old_text = $this.text();
        $parent.find(".bg-layer, .placeOver").fadeToggle(250, function() {
          if (($form).is(":visible")) {
            $this.text($this.attr("data-old"));
            $parent.find('.page-builder-element-title').css('visibility', 'visible');
          } else {
            $parent.find('.page-builder-element-title').css('visibility', 'hidden');
            $this.attr("data-old", old_text);
            $this.text($this.attr("data-text"));
          }
        });

        return false;
      });
    });



    if (($().appear) && ($(".progress").length > 0)) {
      jQuery('.progress').appear(function() {
        var $this = jQuery(this);
        $this.each(function() {
          var $innerbar = $this.find(".progress-bar");
          var percentage = $innerbar.attr("data-percentage");

          $innerbar.addClass("animating").css("width", percentage + "%");

          $innerbar.on('transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd', function() {
            $this.find(".pro-level").fadeIn(600);
          });

        });
      }, {accY: -100});
    }

    /* ================== */
    /* ==== COUNT TO ==== */

    if (($().appear) && ($(".timerCounter").length > 0)) {
      $('.timerCounter').appear(function() {
        $('.timerVal').each(function() {
          $(this).countTo();
        })
      })
    }


    var $logoimage = '';
    var oldsrc = '';

    function swapMenu(mode) {

      var animDuration = 50;
      if (isiPad) {
        animDuration = 0;
      }

      if (mode == "init") {
        $logoimage = $(".navbar-brand img");
        oldsrc = $logoimage.attr('src');
      }

      if ((mode == "standardMenu") && (!($.browser.mobile))) {
        $onepagerNav.removeClass("navbar-transparent");
        if (!($logoimage.hasClass("swaped"))) {

          $logoimage.fadeOut(animDuration, function() {
            $logoimage.attr('src', $logoimage.parent().attr("data-logo"));
            $logoimage.fadeIn(animDuration).addClass("swaped");
          });
        }
      }
      if ((mode == "fixedMenu") && (!($.browser.mobile))) {
        $onepagerNav.addClass("navbar-transparent");
        $logoimage.attr('src', oldsrc);
        $logoimage.removeClass("swaped");
      }
    }


    var onepagerNavClass = "navbar-fixed-top";
    var $onepagerNav = $(".onepage." + onepagerNavClass);

    if (($onepagerNav.length > 0)) {

      var scrollOffset = 0;
      var navHeightSpecial = 0;
      navHeightSpecial = parseInt($('.navbar-default').height());

      if (!($.browser.mobile)) {

        // ipad landscape
        if ($(window).width() < 800) {
          navHeightSpecial = parseInt($('.navbar-default').height());
        }

      } else {
        $(".navbar-fixed-top").removeClass(onepagerNavClass).removeClass("navbar-transparent").addClass("navbar-static-top");
        $logoimage = $(".navbar-brand img");
        $logoimage.fadeOut(50, function() {
          $logoimage.attr('src', $logoimage.parent().attr("data-logo"));
          $logoimage.fadeIn(50).addClass("swaped");
        });


        scrollOffset = parseInt($('.navbar-default').height());
      }

      $('nav.onepage ul.menu li a').click(function() {

        // if mobile and menu open - hide it after click
        var $togglebtn = $(".navbar-toggle")

        if (!($togglebtn.hasClass("collapsed")) && ($togglebtn.is(":visible"))) {
          $(".navbar-toggle").trigger("click");
        }

        var $this = $(this);

        var content = $this.attr('href');

        var myUrl = content.substring(content.indexOf("#") + 1);

        var $content_id = $('#' + myUrl);
        if ($($content_id).length > 0) {

          if (myUrl !== '') {

            if ($.browser.mobile) {

              navHeightSpecial = parseInt($('.navbar-default').height());
            }

            var goPosition = $content_id.offset().top + scrollOffset - navHeightSpecial;

            $('html,body').stop().animate({scrollTop: goPosition}, 1000, 'easeInOutExpo', function() {
              $this.closest("li").addClass("active");
            });


          } else {
            window.location = content;
          }

          return false;
        }
      });





      $(window).on('scroll', function() {

        var menuEl, mainMenu = $onepagerNav.find('ul.menu'), mainMenuHeight = mainMenu.outerHeight() + 5;
        var menuElements = mainMenu.find('a');

        var scrollElements = menuElements.map(function() {

          var content = $(this).attr("href");
          //var myUrl = content.match(/^#([^\/]+)$/i);
          var myUrl = content.substring(content.indexOf("#") + 1);

          if (myUrl !== '') {
            myUrl = '#' + myUrl;
            var item = $(myUrl);
            if (item.length) {
              return item;
            }

          }
        });

        var fromTop = $(window).scrollTop() + mainMenuHeight;

        var currentEl = scrollElements.map(function() {
          if ($(this).offset().top < fromTop) {
            return this;
          }
        });

        currentEl = currentEl[currentEl.length - 1];
        var id = currentEl && currentEl.length ? currentEl[0].id : "";
        // console.log(menuEl+'__'+id);
        if (menuEl !== id) {
          menuElements.parent().removeClass("active").end().filter("a[href$='" + id + "']").parent().addClass("menu-onepage active");
        }

        var scroll = $(window).scrollTop();
        if (scroll > 0) {
          swapMenu("standardMenu");
        } else {
          swapMenu("fixedMenu");
        }

      });


      swapMenu("init");
      var scroll = $(window).scrollTop();
      if ((scroll > 0) && (!isiPad)) {
        swapMenu("standardMenu");
      }

      // ipad hack to swap menus
      document.addEventListener("touchmove", ScrollStart, false);

    }
    function ScrollStart() {
      swapMenu("standardMenu");
    }


    // height 100% of section
    if ($(".page-builder-row-section.height100").length > 0) {

      $(".page-builder-row-section.height100").each(function() {

        var $this = $(this);
        $("#boxedWrapper, body").css("height", "100%");

        var menuHeight = 0;
        if ($this.isAfter(".navbar-default")) {
          menuHeight = $(".navbar-default").outerHeight();
        }
        if ($(".navbar-default").hasClass("navbar-fixed-top")) {
          menuHeight = 0;
        }



        var sliderHeight = $('#boxedWrapper').height() - menuHeight;
        var $slider = $this.find(".flexslider");

        $($this, $slider).css("height", sliderHeight);

      })
    }


    new WOW({
      boxClass: 'wow', // default
      animateClass: 'animated', // default
      offset: 100 // default
    }).init();



//*********** your custom code must place above here................ *************

  });





  $(window).load(function() {
    if ($('.googleMap').length) {
      loadGoogleMap();
      // StyleGoogleMapForm();
    }
  });


})(jQuery);


function StyleGoogleMapForm() {
  jQuery('.node-webform').each(function() {
    var current_form = jQuery(this);

    if (current_form.find('.googleMap').length) {
      var node_id = 'wrap-' + current_form.attr('id');
      current_form.wrap('<div id="' + node_id + '" class="content-layer brightText"></div>');
      current_form.find('.block-webform').wrap('<div class="placeOver"><div class="container"><div class="row"><div class="col-md-8 col-md-push-2"></div></div></div></div>');
      current_form.find('.form-submit').addClass('btn-primary');
      current_form.find('.content').css('padding-bottom', 0);
      current_form.find('.showMap').click(function() {
        var $this = jQuery(this);
        var $parent = current_form;
        var $form = current_form.find(".placeOver");

        $parent.find(".bg-layer, .placeOver").fadeToggle(250, function() {
          if (($form).is(":visible")) {
            $this.text($this.attr("data-old"));
          } else {
            $this.attr("data-old", $this.text());
            $this.text($this.attr("data-text"));
          }
        });

        return false;
      });
    }

  });
}
// *** start google maps
function loadGoogleMap() {
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +
          'callback=initmap';
  document.body.appendChild(script);

}

function initmap() {
  "use strict";
  jQuery(".googleMap").each(function() {
    var atcenter = "";
    var $this = jQuery(this);
    var location = $this.data("location");

    var offset = -30;

    if (validatedata($this.data("offset"))) {
      offset = $this.data("offset");
    }

    if (validatedata(location)) {

      $this.gmap3({
        marker: {
          //latLng: [40.616439, -74.035540],
          address: location,
          options: {
            visible: false
          },
          callback: function(marker) {
            atcenter = marker.getPosition();
          }
        },
        map: {
          options: {
            //maxZoom:11,
            zoom: 18,
            mapTypeId: google.maps.MapTypeId.SATELLITE,
            // ('ROADMAP', 'SATELLITE', 'HYBRID','TERRAIN');
            scrollwheel: false,
            disableDoubleClickZoom: false,
            //disableDefaultUI: true,
            mapTypeControlOptions: {
              //mapTypeIds: [google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.HYBRID],
              //style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
              //position: google.maps.ControlPosition.RIGHT_CENTER
              mapTypeIds: []
            }
          },
          events: {
            idle: function() {
              if (!$this.data('idle')) {
                $this.gmap3('get').panBy(0, offset);
                $this.data('idle', true);
              }
            }
          }
        },
        overlay: {
          //latLng: [40.616439, -74.035540],
          address: location,
          options: {
            content: '<div class="customMarker"><span class="fa fa-map-marker"></span><i></i></div>',
            offset: {
              y: -70,
              x: -25
            }
          }
        }
        //},"autofit"
      });

      // center on resize
      google.maps.event.addDomListener(window, "resize", function() {
        //var userLocation = new google.maps.LatLng(53.8018,-1.553);
        setTimeout(function() {
          $this.gmap3('get').setCenter(atcenter);
          $this.gmap3('get').panBy(0, offset);
        }, 400);

      });

      // set height
      $this.css("min-height", $this.data("height") + "px");
    }

  });
}
// **** end google map.

(function ($) {

  Drupal.behaviors.exampleModule = {
    attach: function (context, settings) {
        
		/* ====== Popup box ========== */
		$('.popup-youtube, .popup-vimeo, .popup-gmaps, .popup-video').magnificPopup({
		  disableOn: 700,
		  type: 'iframe',
		  fixedContentPos: false,
		  fixedBgPos: false,
		  removalDelay: 300,
		  mainClass: 'mfp-fade',
		  preloader: false
		});
		/* ======= End popup Box ======= */
	  
    }
  };

})(jQuery);

