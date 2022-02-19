(function($, sr) {
  var debounce = function(func, threshold, execAsap) {
    var timeout;
    return function debounced() {
      var obj = this, args = arguments;
      function delayed() {
        if (!execAsap)
          func.apply(obj, args);
        timeout = null;
      }
      ;
      if (timeout)
        clearTimeout(timeout);
      else if (execAsap)
        func.apply(obj, args);
      timeout = setTimeout(delayed, threshold || 100);
    };
  }
// smartresize 
  jQuery.fn[sr] = function(fn) {
    return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
  };
})(jQuery, 'smartresize');
(function($) {
  var views_views_isotope = false;
  Drupal.behaviors.viewsBsIsotope = {
    attach: function(context, settings) {
      $(function() {

        if (!views_views_isotope) {
          views_views_isotope = true;
          $.each(settings.viewsBs.isotope, function(id, isotope) {
            try {
              var $container = $('#views-bs-isotope-' + isotope.id);
              var defaultOptions = {
                itemSelector: '.isotope-element',
                layoutMode: 'masonry',
                //filter: '*',
                //resizable: false,
                masonry: {}
              };
              var isotopeOptions = {};
              $(window).smartresize(function() {
				 
                $container.isotope({
                  // update columnWidth to a percentage of container width
                  masonry: {}
                });
              });

              if (isotope.use_infinitescroll) {

                $container.parent().parent().parent().parent('.view').find('.pager').hide(); // hide pager
                var loading_img = '<div id="views_infinite_scroll-ajax-loader"><i class="fa fa-spinner fa-spin"></i></div>';
                var content_append = '#views-bs-isotope-' + isotope.id;
                var content_image = 'div.view-id-' + isotope.view_name + '.view-display-id-' + isotope.display + ' div.view-content';
                $.autopager({
                  link: 'li.pager-next a:first',
                  content: '.isotope-element',
                  // enable/disable scroll loading
                  autoLoad: true,
                  // initial page number 
                  page: 0,
                  // where contents would be appended.
                  // use "appendTo" or "insertBefore"
                  appendTo: content_append,
                  // insertBefore: '#footer', 

                  // a callback function to be triggered when loading start 

                  start: function(current, next) {
                    $(content_image).after(loading_img);
                  },
                  // a function to be executed when next page was loaded. 
                  // "this" points to the element of loaded content.
                  load: function(current, next) {
                    $('div#views_infinite_scroll-ajax-loader').remove();
                    $new_elements = $(this);
                    $new_elements.imagesLoaded(function() {
                      $container.isotope('insert', $new_elements);

                    });

                    Drupal.attachBehaviors();
                  }
                });

              }


              $container.imagesLoaded(function() {
                $container.isotope(defaultOptions);

              });


              var $optionSets = jQuery('#views-bs-filters-' + isotope.id), isOptionLinkClicked = false;
              function changeSelectedLink($elem) {
                // remove selected class on previous item
                $elem.parents('.option-set').find('.btn-primary').removeClass('btn-primary');
                // set selected class on new item
                $elem.addClass('btn-primary');
              }

              $optionSets.find('a').click(function() {

                var $this = jQuery(this);


                // don't proceed if already selected
                if ($this.hasClass('btn-primary')) {
                  return;
                }

                changeSelectedLink($this);
                // get href attr, remove leading #
                var href = $this.attr('href').replace(/^#/, ''), // convert href into object
                        // i.e. 'filter=.inner-transition' -> { filter: '.inner-transition' }
                        option = jQuery.deparam(href, true);
                // apply new option to previous
                jQuery.extend(isotopeOptions, option);
                // set hash, triggers hashchange on window
                jQuery.bbq.pushState(isotopeOptions);
                isOptionLinkClicked = true;
                return false;
              });
              var hashChanged = false;
              jQuery(window).bind('hashchange', function(event) {
                // get options object from hash
                var hashOptions = window.location.hash ? jQuery.deparam.fragment(window.location.hash, true) : {}, // do not animate first call
                        aniEngine = hashChanged ? 'best-available' : 'none', // apply defaults where no option was specified
                        options = jQuery.extend({}, defaultOptions, hashOptions, {animationEngine: aniEngine});
                // apply options from hash
                $container.isotope(options);
                // save options
                isotopeOptions = hashOptions;
                // if option link was not clicked
                // then we'll need to update selected links
                if (!isOptionLinkClicked) {

                  // iterate over options
                  var hrefObj, hrefValue, $selectedLink;
                  for (var key in options) {
                    hrefObj = {};
                    hrefObj[ key ] = options[ key ];
                    // convert object into parameter string
                    // i.e. { filter: '.inner-transition' } -> 'filter=.inner-transition'
                    hrefValue = jQuery.param(hrefObj);
                    // get matching link
                    $selectedLink = $optionSets.find('a[href="#' + hrefValue + '"]');
                    changeSelectedLink($selectedLink);
                  }
                }

                isOptionLinkClicked = false;
                hashChanged = true;
              })// trigger hashchange to capture any hash data on init
                      .trigger('hashchange');



            }
            catch (err) {
              console.log(err);
            }
          });

        }

      });
    }
  };
})(jQuery);
