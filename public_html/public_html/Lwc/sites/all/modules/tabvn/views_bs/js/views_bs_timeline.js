
(function($) {
  var views_views_timline = false;

  Drupal.behaviors.viewsBsTimline = {
    attach: function(context, settings) {
      $(function() {
        if (!views_views_timline) {
          views_views_timline = true;
          $.each(settings.viewsBs.timeline, function(id, timeline) {
            try {
              var $container = $('div.view-id-' + timeline.view_name + '.view-display-id-' + timeline.display + ' div.view-content');

              if (timeline.use_infinitescroll) {
                $container.parent().find('.pager').hide(); // hide pager
                var loading_img = '<div id="views_infinite_scroll-ajax-loader"><i class="fa fa-spinner fa-spin"></i></div>';
                var content_append = 'div.view-id-' + timeline.view_name + '.view-display-id-' + timeline.display + ' div.view-content';
                var content_image = 'div.view-id-' + timeline.view_name + '.view-display-id-' + timeline.display + ' div.view-content';
                $.autopager({
                  link: 'li.pager-next a:first',
                  content: '.views-bs-timeline-wrapper',
                  // enable/disable scroll loading
                  autoLoad: true,
                  // initial page number 
                  page: 0,
                  // where contents would be appended.
                  // use "appendTo" or "insertBefore"
                  appendTo: content_append,

                  start: function(current, next) {
                    $(content_image).after(loading_img);
                  },
                  // a function to be executed when next page was loaded. 
                  // "this" points to the element of loaded content.
                  load: function(current, next) {
                    $('div#views_infinite_scroll-ajax-loader').remove();
                    Drupal.attachBehaviors();
                  }
                });


              }
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
