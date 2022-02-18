(function ($) {

  Drupal.accordion = Drupal.accordion || {};

  /**
   * Toggle the visibility of an individual accordion using smooth animations.
   */
  Drupal.accordion.toggle = function (accordion, context, accordion_type_info) {
    var $accordion = $(accordion);
    if ($accordion.is('.collapsed')) {
      // Collapse all items in the accordion group.
      $('.accordion[data-accordions-group="' + $accordion.attr('data-accordions-group') + '"]', context).each(function () {
        Drupal.accordion.collapse.call(this, accordion_type_info);
      });
      // Then expand this item.
      Drupal.accordion.expand.call(accordion, accordion_type_info);
    }
    else {
      Drupal.accordion.collapse.call(accordion, accordion_type_info);
    }
  };

  Drupal.accordion.expand = function (accordion_type_info) {
    var accordion = this;
    var $accordion = $(accordion);
    var $content = $accordion.closestDescendant(accordion_type_info.content).hide();
    $accordion
      .removeClass('collapsed')
      .trigger({ type: 'collapsed', value: false });
    $('span.accordions-label-prefix', $accordion.closestDescendant(accordion_type_info.label)).html(Drupal.t('Hide'));
    $content.slideDown({
      duration: 100,
      easing: 'linear',
      complete: function () {
        Drupal.collapseScrollIntoView(accordion);
        accordion.animating = false;
      },
      step: function () {
        // Scroll the accordion into view.
        Drupal.collapseScrollIntoView(accordion);
      }
    });
  };

  Drupal.accordion.collapse = function (accordion_type_info) {
    var accordion = this;
    var $accordion = $(accordion);
    $accordion.trigger({ type: 'collapsed', value: true });
    $accordion.closestDescendant(accordion_type_info.content).slideUp({
      duration: 100,
      easing: 'linear',
      complete: function () {
        $accordion.addClass('collapsed');
        $('span.accordions-label-prefix', $accordion.closestDescendant(accordion_type_info.label)).html(Drupal.t('Show'));
        accordion.animating = false;
      }
    });
  };

  /**
   * Based on Drupal.behaviors.collapse
   */
  Drupal.behaviors.accordions = {
    attach: function (context, settings) {
      if (settings.accordions != 'undefined'  && typeof settings.accordions != 'undefined') {
        $('.accordion', context).once('collapse', function () {
          var accordion = this;
          var $accordion = $(accordion);
          var accordion_type = $accordion.attr('data-accordions-type');
          if (settings.accordions.hasOwnProperty(accordion_type)) {
            // Turn the title into a clickable link.
            var $title = $accordion.closestDescendant(settings.accordions[accordion_type].label); // @todo currently only works for accordion items with titles.
            if ($title.length > 0) {
              // Expand accordion if it contains an element that is targeted by the
              // URI fragment identifier.
              var anchor = location.hash && location.hash != '#' ? location.hash : '';
              if ('#' + $accordion.attr('id') == anchor || $accordion.find(anchor).length) {
                $accordion.removeClass('collapsed');
              }
              // Collapse accordion initially unless specified as initially expanded.
              else if (!$accordion.attr('data-accordions-initial')) {
                Drupal.accordion.collapse.call(accordion, settings.accordions[accordion_type]);
              }

              $('<span class="accordions-label-prefix element-invisible"></span>')
                .append($accordion.hasClass('collapsed') ? Drupal.t('Show') : Drupal.t('Hide'))
                .prependTo($title)
                .after(' ');

              // .wrapInner() does not retain bound events.
              var $link = $('<a class="accordions-label" href="#"></a>')
                .prepend($title.contents())
                .appendTo($title)
                .click(function () {
                  // Don't animate multiple times.
                  if (!accordion.animating) {
                    accordion.animating = true;
                    Drupal.accordion.toggle(accordion, context, settings.accordions[accordion_type]);
                  }
                  return false;
                });
            }
            else {
              // @todo Remove this code that removes accordion items without
              // titles from the accordion group once we support those.
              $accordion.removeAttr('data-accordions-group');
            }
          }
        });
      }
    }
  };

  // THIS PART COPIED FROM https://github.com/tlindig/jquery-closest-descendant
  /**
   * closestDescendant - 0.1.1 - 2013-04-09
   * https://github.com/tlindig/jquery-closest-descendant
   *
   * Copyright (c) 2013 Tobias Lindig
   * http://tlindig.de/
   *
   * Licensed MIT
   */
  /**
   * Get the first element(s) that matches the selector by traversing down
   * through descendants in the DOM tree level by level. It use a breadth
   * first search (BFS), that mean it will stop search and not going deeper in
   * the current subtree if the first matching descendant was found.
   *
   * @param  {selectors} selector -required- a jQuery selector
   * @param  {boolean} findAll -optional- default is false, if true, every
   *                           subtree will be visited until first match
   * @return {jQuery} matched element(s)
   */
  $.fn.closestDescendant = $.fn.closestDescendant || function(selector, findAll) {

    if (!selector || selector === '') {
      return $();
    }

    findAll = findAll ? true : false;

    var resultSet = $();

    this.each(function() {

      var $this = $(this);

      // breadth first search for every matched node,
      // go deeper, until a child was found in the current subtree or the leave was reached.
      var queue = [];
      queue.push( $this );
      while ( queue.length > 0 ) {
        var node = queue.shift();
        var children = node.children();
        for ( var i = 0; i < children.length; ++i ) {
          var $child = $(children[i]);
          if ( $child.is( selector ) ) {
            resultSet.push( $child[0] ); //well, we found one
            if ( ! findAll ) {
              return false;//stop processing
            }
          } else {
            queue.push( $child ); //go deeper
          }
        }
      }
    });

    return resultSet;
  };

})(jQuery);
