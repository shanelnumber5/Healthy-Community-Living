"use strict";

//--------------------------------------------------------------------//
// Global helper functions
//--------------------------------------------------------------------//

/**
 * Matches polyfill.
 *
 * @since 1.0.0
 */
if (!Element.prototype.matches) {
  Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}
/**
 * Closest polyfill.
 *
 * @since 1.0.0
 */


if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;

    do {
      if (el.matches(s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);

    return null;
  };
}
/**
 * Foreach polyfill.
 *
 * @since 1.1.0
 */


if (window.NodeList && !NodeList.prototype.forEach) {
  NodeList.prototype.forEach = Array.prototype.forEach;
}
/**
 * Element.prototype.classList for IE8/9, Safari.
 *
 * @since 1.10
 */


(function () {
  // Helpers.
  var trim = function trim(s) {
    return s.replace(/^\s+|\s+$/g, '');
  },
      regExp = function regExp(name) {
    return new RegExp('(^|\\s+)' + name + '(\\s+|$)');
  },
      forEach = function forEach(list, fn, scope) {
    for (var i = 0; i < list.length; i++) {
      fn.call(scope, list[i]);
    }
  }; // Class list object with basic methods.


  function ClassList(element) {
    this.element = element;
  }

  ClassList.prototype = {
    add: function add() {
      forEach(arguments, function (name) {
        if (!this.contains(name)) {
          this.element.className = trim(this.element.className + ' ' + name);
        }
      }, this);
    },
    remove: function remove() {
      forEach(arguments, function (name) {
        this.element.className = trim(this.element.className.replace(regExp(name), ' '));
      }, this);
    },
    toggle: function toggle(name) {
      return this.contains(name) ? (this.remove(name), false) : (this.add(name), true);
    },
    contains: function contains(name) {
      return regExp(name).test(this.element.className);
    },
    item: function item(i) {
      return this.element.className.split(/\s+/)[i] || null;
    },
    // bonus
    replace: function replace(oldName, newName) {
      this.remove(oldName), this.add(newName);
    }
  }; // IE8/9, Safari
  // Remove this if statements to override native classList.

  if (!('classList' in Element.prototype)) {
    // Use this if statement to override native classList that does not have for example replace() method.
    // See browser compatibility: https://developer.mozilla.org/en-US/docs/Web/API/Element/classList#Browser_compatibility.
    // if (!('classList' in Element.prototype) ||
    //     !('classList' in Element.prototype && Element.prototype.classList.replace)) {
    Object.defineProperty(Element.prototype, 'classList', {
      get: function get() {
        return new ClassList(this);
      }
    });
  } // For others replace() support.


  if (window.DOMTokenList && !DOMTokenList.prototype.replace) {
    DOMTokenList.prototype.replace = ClassList.prototype.replace;
  }
})();
/**
 * Index polyfill.
 *
 * @since 1.0.0
 */


var sinatraGetIndex = function sinatraGetIndex(el) {
  var i = 0;

  while (el = el.previousElementSibling) {
    i++;
  }

  return i;
};
/**
 * Slide Up animation.
 *
 * @since 1.0.0
 *
 * @param  {[type]} target   Element to slide.
 * @param  {Number} duration Animation duration.
 */


var sinatraSlideUp = function sinatraSlideUp(target) {
  var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;
  target.style.transitionProperty = 'height, margin, padding';
  target.style.transitionDuration = duration + 'ms';
  target.style.boxSizing = 'border-box';
  target.style.height = target.offsetHeight + 'px';
  target.offsetHeight;
  target.style.overflow = 'hidden';
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  window.setTimeout(function () {
    target.style.display = null;
    target.style.removeProperty('height');
    target.style.removeProperty('padding-top');
    target.style.removeProperty('padding-bottom');
    target.style.removeProperty('margin-top');
    target.style.removeProperty('margin-bottom');
    target.style.removeProperty('overflow');
    target.style.removeProperty('transition-duration');
    target.style.removeProperty('transition-property');
  }, duration);
};
/**
 * Slide Down animation.
 *
 * @since 1.0.0
 *
 * @param  {[type]} target   Element to slide.
 * @param  {Number} duration Animation duration.
 */


var sinatraSlideDown = function sinatraSlideDown(target) {
  var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 500;
  target.style.removeProperty('display');
  var display = window.getComputedStyle(target).display;

  if (display === 'none') {
    display = 'block';
  }

  target.style.display = display;
  var height = target.offsetHeight;
  target.style.overflow = 'hidden';
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  target.offsetHeight;
  target.style.boxSizing = 'border-box';
  target.style.transitionProperty = 'height, margin, padding';
  target.style.transitionDuration = duration + 'ms';
  target.style.height = height + 'px';
  target.style.removeProperty('padding-top');
  target.style.removeProperty('padding-bottom');
  target.style.removeProperty('margin-top');
  target.style.removeProperty('margin-bottom');
  window.setTimeout(function () {
    target.style.removeProperty('height');
    target.style.removeProperty('overflow');
    target.style.removeProperty('transition-duration');
    target.style.removeProperty('transition-property');
  }, duration);
};
/**
 * MoveTo - A lightweight scroll animation javascript library without any dependency.
 * Version 1.8.3 (21-07-2019 00:32)
 * Licensed under MIT
 * Copyright 2019 Hasan Aydoğdu <hsnaydd@gmail.com>
 */


var sinatraScrollTo = function () {
  /**
   * Defaults
   * @type {object}
   */
  var defaults = {
    tolerance: 0,
    duration: 800,
    easing: 'easeOutQuart',
    container: window,
    callback: function callback() {}
  };
  /**
   * easeOutQuart Easing Function
   * @param  {number} t - current time
   * @param  {number} b - start value
   * @param  {number} c - change in value
   * @param  {number} d - duration
   * @return {number} - calculated value
   */

  function easeOutQuart(t, b, c, d) {
    t /= d;
    t--;
    return -c * (t * t * t * t - 1) + b;
  }
  /**
   * Merge two object
   *
   * @param  {object} obj1
   * @param  {object} obj2
   * @return {object} merged object
   */


  function mergeObject(obj1, obj2) {
    var obj3 = {};
    Object.keys(obj1).forEach(function (propertyName) {
      obj3[propertyName] = obj1[propertyName];
    });
    Object.keys(obj2).forEach(function (propertyName) {
      obj3[propertyName] = obj2[propertyName];
    });
    return obj3;
  }
  /**
   * Converts camel case to kebab case
   * @param  {string} val the value to be converted
   * @return {string} the converted value
   */


  function kebabCase(val) {
    return val.replace(/([A-Z])/g, function ($1) {
      return '-' + $1.toLowerCase();
    });
  }
  /**
   * Count a number of item scrolled top
   * @param  {Window|HTMLElement} container
   * @return {number}
   */


  function countScrollTop(container) {
    if (container instanceof HTMLElement) {
      return container.scrollTop;
    }

    return container.pageYOffset;
  }
  /**
   * sinatraScrollTo Constructor
   * @param {object} options Options
   * @param {object} easeFunctions Custom ease functions
   */


  function sinatraScrollTo() {
    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    var easeFunctions = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    this.options = mergeObject(defaults, options);
    this.easeFunctions = mergeObject({
      easeOutQuart: easeOutQuart
    }, easeFunctions);
  }
  /**
   * Register a dom element as trigger
   * @param  {HTMLElement} dom Dom trigger element
   * @param  {function} callback Callback function
   * @return {function|void} unregister function
   */


  sinatraScrollTo.prototype.registerTrigger = function (dom, callback) {
    var _this = this;

    if (!dom) {
      return;
    }

    var href = dom.getAttribute('href') || dom.getAttribute('data-target'); // The element to be scrolled

    var target = href && href !== '#' ? document.getElementById(href.substring(1)) : document.body;
    var options = mergeObject(this.options, _getOptionsFromTriggerDom(dom, this.options));

    if (typeof callback === 'function') {
      options.callback = callback;
    }

    var listener = function listener(e) {
      e.preventDefault();

      _this.move(target, options);
    };

    dom.addEventListener('click', listener, false);
    return function () {
      return dom.removeEventListener('click', listener, false);
    };
  };
  /**
   * Move
   * Scrolls to given element by using easeOutQuart function
   * @param  {HTMLElement|number} target Target element to be scrolled or target position
   * @param  {object} options Custom options
   */


  sinatraScrollTo.prototype.move = function (target) {
    var _this2 = this;

    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

    if (target !== 0 && !target) {
      return;
    }

    options = mergeObject(this.options, options);
    var distance = typeof target === 'number' ? target : target.getBoundingClientRect().top;
    var from = countScrollTop(options.container);
    var startTime = null;
    var lastYOffset;
    distance -= options.tolerance; // rAF loop

    var loop = function loop(currentTime) {
      var currentYOffset = countScrollTop(_this2.options.container);

      if (!startTime) {
        // To starts time from 1, we subtracted 1 from current time
        // If time starts from 1 The first loop will not do anything,
        // because easing value will be zero
        startTime = currentTime - 1;
      }

      var timeElapsed = currentTime - startTime;

      if (lastYOffset) {
        if (distance > 0 && lastYOffset > currentYOffset || distance < 0 && lastYOffset < currentYOffset) {
          return options.callback(target);
        }
      }

      lastYOffset = currentYOffset;

      var val = _this2.easeFunctions[options.easing](timeElapsed, from, distance, options.duration);

      options.container.scroll(0, val);

      if (timeElapsed < options.duration) {
        window.requestAnimationFrame(loop);
      } else {
        options.container.scroll(0, distance + from);
        options.callback(target);
      }
    };

    window.requestAnimationFrame(loop);
  };
  /**
   * Adds custom ease function
   * @param {string}   name Ease function name
   * @param {function} fn   Ease function
   */


  sinatraScrollTo.prototype.addEaseFunction = function (name, fn) {
    this.easeFunctions[name] = fn;
  };
  /**
   * Returns options which created from trigger dom element
   * @param  {HTMLElement} dom Trigger dom element
   * @param  {object} options The instance's options
   * @return {object} The options which created from trigger dom element
   */


  function _getOptionsFromTriggerDom(dom, options) {
    var domOptions = {};
    Object.keys(options).forEach(function (key) {
      var value = dom.getAttribute('data-mt-'.concat(kebabCase(key)));

      if (value) {
        domOptions[key] = isNaN(value) ? value : parseInt(value, 10);
      }
    });
    return domOptions;
  }

  return sinatraScrollTo;
}();
/**
 * Get all of an element's parent elements up the DOM tree
 *
 * @since 1.0.0
 *
 * @param  {Node}   elem     The element.
 * @param  {String} selector Selector to match against [optional].
 * @return {Array}           The parent elements.
 */


var sinatraGetParents = function sinatraGetParents(elem, selector) {
  // Element.matches() polyfill.
  if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.matchesSelector || Element.prototype.mozMatchesSelector || Element.prototype.msMatchesSelector || Element.prototype.oMatchesSelector || Element.prototype.webkitMatchesSelector || function (s) {
      var matches = (this.document || this.ownerDocument).querySelectorAll(s),
          i = matches.length;

      while (--i >= 0 && matches.item(i) !== this) {}

      return i > -1;
    };
  } // Setup parents array.


  var parents = []; // Get matching parent elements.

  for (; elem && elem !== document; elem = elem.parentNode) {
    // Add matching parents to array.
    if (selector) {
      if (elem.matches(selector)) {
        parents.push(elem);
      }
    } else {
      parents.push(elem);
    }
  }

  return parents;
}; // CustomEvent() constructor functionality in Internet Explorer 9 and higher.


(function () {
  if (typeof window.CustomEvent === 'function') return false;

  function CustomEvent(event, params) {
    params = params || {
      bubbles: false,
      cancelable: false,
      detail: undefined
    };
    var evt = document.createEvent('CustomEvent');
    evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
    return evt;
  }

  CustomEvent.prototype = window.Event.prototype;
  window.CustomEvent = CustomEvent;
})();
/**
 * Trigger custom JS Event.
 *
 * @since 1.0.0
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent
 * @param {Node} el Dom Node element on which the event is to be triggered.
 * @param {Node} typeArg A DOMString representing the name of the event.
 * @param {String} A CustomEventInit dictionary, having the following fields:
 *			"detail", optional and defaulting to null, of type any, that is an event-dependent value associated with the event.
 */


var sinatraTriggerEvent = function sinatraTriggerEvent(el, typeArg) {
  var customEventInit = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var event = new CustomEvent(typeArg, customEventInit);
  el.dispatchEvent(event);
}; // Main


(function () {
  //--------------------------------------------------------------------//
  // Variable caching
  //--------------------------------------------------------------------//
  var sinatraScrollButton = document.querySelector('#si-scroll-top');
  var pageWrapper = document.getElementById('page'); //--------------------------------------------------------------------//
  // Local helper functions
  //--------------------------------------------------------------------//

  /**
   * Submenu overflow helper
   *
   * @since 1.0.0
   */

  var sinatraSmartSubmenus = function sinatraSmartSubmenus() {
    if (document.body.classList.contains('sinatra-is-mobile')) {
      return;
    }

    var el, elPosRight, elPosLeft, winRight;
    winRight = window.innerWidth;
    document.querySelectorAll('.sub-menu').forEach(function (item) {
      // Set item to be visible so we can grab offsets
      item.style.visibility = 'visible'; // Left offset

      var rect = item.getBoundingClientRect();
      elPosLeft = rect.left + window.pageXOffset; // Right offset

      elPosRight = elPosLeft + rect.width; // Remove styles

      item.removeAttribute('style'); // Decide where to open

      if (elPosRight > winRight) {
        item.closest('li').classList.add('opens-left');
      } else if (elPosLeft < 0) {
        item.closest('li').classList.add('opens-right');
      }
    });
  };
  /**
   * Debounce functions for better performance
   * (c) 2018 Chris Ferdinandi, MIT License, https://gomakethings.com
   *
   * @since 1.0.0
   *
   * @param  {Function} fn The function to debounce
   */


  var sinatraDebounce = function sinatraDebounce(fn) {
    // Setup a timer
    var timeout; // Return a function to run debounced

    return function () {
      // Setup the arguments
      var context = this;
      var args = arguments; // If there's a timer, cancel it

      if (timeout) {
        window.cancelAnimationFrame(timeout);
      } // Setup the new requestAnimationFrame()


      timeout = window.requestAnimationFrame(function () {
        fn.apply(this, args);
      });
    };
  };
  /**
   * Handles Scroll to Top button click
   *
   * @since 1.0.0
   */


  var sinatraScrollTopButton = function sinatraScrollTopButton() {
    if (null === sinatraScrollButton) {
      return;
    }

    if (window.pageYOffset > 450 || document.documentElement.scrollTop > 450) {
      sinatraScrollButton.classList.add('si-visible');
    } else {
      sinatraScrollButton.classList.remove('si-visible');
    }
  };
  /**
   * Handles Sticky Header functionality.
   *
   * @since 1.0.0
   */


  var sinatraStickyHeader = function sinatraStickyHeader() {
    // Check if sticky is enabled.
    if (!sinatra_vars['sticky-header']['enabled']) {
      return;
    }

    var header = document.getElementById('sinatra-header');
    var headerInner = document.getElementById('sinatra-header-inner');
    var wpadminbar = document.getElementById('wpadminbar'); // Check for header layout 3.

    if (document.body.classList.contains('sinatra-header-layout-3')) {
      header = document.querySelector('#sinatra-header .si-nav-container');
      headerInner = document.querySelector('#sinatra-header .si-nav-container .si-container');
    } // Mobile nav active.


    if (window.outerWidth <= sinatra_vars['responsive-breakpoint']) {
      var header = document.getElementById('sinatra-header');
      var headerInner = document.getElementById('sinatra-header-inner');
    } // Check if elements exist.


    if (null === header || null === headerInner) {
      return;
    } // Calculate the initial sticky position.


    var stickyPosition = header.getBoundingClientRect().top;
    var sticky = stickyPosition - tolerance <= 0;
    var tolerance;
    var stickyPlaceholder; // Check if there is a top bar.

    if (null === wpadminbar) {
      tolerance = 0;
    } else if (window.outerWidth <= 600) {
      tolerance = 0;
    } else {
      tolerance = wpadminbar.getBoundingClientRect().height;
    }

    var checkPosition = function checkPosition() {
      if (null === wpadminbar) {
        tolerance = 0;
      } else if (window.outerWidth <= 600) {
        tolerance = 0;
      } else {
        tolerance = wpadminbar.getBoundingClientRect().height;
      }

      stickyPosition = header.getBoundingClientRect().top;
      sticky = stickyPosition - tolerance <= 0;
      maybeStickHeader();
    };

    var maybeStickHeader = function maybeStickHeader() {
      var hideOn = sinatra_vars['sticky-header']['hide_on']; // Desktop.

      if (hideOn.includes('desktop') && window.innerWidth >= 992) {
        sticky = false;
      } // Tablet.


      if (hideOn.includes('tablet') && window.innerWidth >= 481 && window.innerWidth < 992) {
        sticky = false;
      } // Mobile.


      if (hideOn.includes('mobile') && window.innerWidth < 481) {
        sticky = false;
      }

      if (sticky) {
        if (!document.body.classList.contains('si-sticky-header')) {
          stickyPlaceholder = document.createElement('div');
          stickyPlaceholder.setAttribute('id', 'si-sticky-placeholder');
          stickyPlaceholder.style.height = headerInner.getBoundingClientRect().height + 'px';
          header.appendChild(stickyPlaceholder);
          document.body.classList.add('si-sticky-header'); // Add sticky header offset variable.

          document.body.style.setProperty('--si-sticky-h-offset', header.offsetHeight + 20 + 'px');
        }
      } else {
        if (document.body.classList.contains('si-sticky-header')) {
          document.body.classList.remove('si-sticky-header');
          document.getElementById('si-sticky-placeholder').remove();
        } // Remove sticky header offset variable.


        document.body.style.removeProperty('--si-sticky-h-offset');
      }
    }; // Debounce scroll.


    if ('true' !== header.getAttribute('data-scroll-listener')) {
      window.addEventListener('scroll', function () {
        sinatraDebounce(checkPosition());
      });
      header.setAttribute('data-scroll-listener', 'true');
    } // Debounce resize.


    if ('true' !== header.getAttribute('data-resize-listener')) {
      window.addEventListener('resize', function () {
        sinatraDebounce(checkPosition());
      });
      header.setAttribute('data-resize-listener', 'true');
    } // Trigger scroll.


    sinatraTriggerEvent(window, 'scroll');
  };
  /**
   * Handles smooth scrolling of elements that have 'si-smooth-scroll' class.
   *
   * @since 1.0.0
   */


  var sinatraSmoothScroll = function sinatraSmoothScroll() {
    var scrollTo = new sinatraScrollTo({
      tolerance: null === document.getElementById('wpadminbar') ? 0 : document.getElementById('wpadminbar').getBoundingClientRect().height
    });
    var scrollTriggers = document.getElementsByClassName('si-smooth-scroll');

    for (var i = 0; i < scrollTriggers.length; i++) {
      scrollTo.registerTrigger(scrollTriggers[i]);
    }
  };
  /**
   * Menu accessibility.
   *
   * @since 1.0.0
   */


  var sinatraMenuAccessibility = function sinatraMenuAccessibility() {
    if (!document.body.classList.contains('si-menu-accessibility')) {
      return;
    }

    document.querySelectorAll('.sinatra-nav').forEach(function (menu) {
      // aria-haspopup
      menu.querySelectorAll('ul').forEach(function (subMenu) {
        subMenu.parentNode.setAttribute('aria-haspopup', 'true');
      }); // Dropdown visibility on focus

      menu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('focus', sinatraMenuFocus, true);
        link.addEventListener('blur', sinatraMenuFocus, true);
      });
    });
  };
  /**
   * Helper function that toggles .hovered on focused/blurred menu items.
   *
   * @since 1.0.0
   */


  function sinatraMenuFocus() {
    var self = this; // Move up until we find .sinatra-nav

    while (!self.classList.contains('sinatra-nav')) {
      if ('li' === self.tagName.toLowerCase()) {
        if (!self.classList.contains('hovered')) {
          self.classList.add('hovered');
        } else {
          self.classList.remove('hovered');
        }
      }

      self = self.parentElement;
    }
  }
  /**
   * Helps with accessibility for keyboard only users.
   *
   * @since 1.0.0
   */


  var sinatraKeyboardFocus = function sinatraKeyboardFocus() {
    document.body.addEventListener('keydown', function (e) {
      document.body.classList.add('using-keyboard');
    });
    document.body.addEventListener('mousedown', function (e) {
      document.body.classList.remove('using-keyboard');
    });
  };
  /**
   * Calculates screen width without scrollbars.
   *
   * @since 1.1.4
   */


  var sinatraCalcScreenWidth = function sinatraCalcScreenWidth() {
    document.body.style.setProperty('--si-screen-width', document.body.clientWidth + 'px');
  };
  /**
   * Adds visibility delay on navigation submenus.
   *
   * @since 1.0.0
   */


  var sinatraDropdownDelay = function sinatraDropdownDelay() {
    var hoverTimer = null;
    document.querySelectorAll('.sinatra-nav .menu-item-has-children').forEach(function (item) {
      item.addEventListener('mouseenter', function () {
        document.querySelectorAll('.menu-item-has-children').forEach(function (subitem) {
          subitem.classList.remove('hovered');
        });
      });
    });
    document.querySelectorAll('.sinatra-nav .menu-item-has-children').forEach(function (item) {
      item.addEventListener('mouseleave', function () {
        item.classList.add('hovered');

        if (null !== hoverTimer) {
          clearTimeout(hoverTimer);
          hoverTimer = null;
        }

        hoverTimer = setTimeout(function () {
          item.classList.remove('hovered');
          item.querySelectorAll('.menu-item-has-children').forEach(function (childItem) {
            childItem.classList.remove('hovered');
          });
        }, 700);
      });
    });
  };
  /**
   * Adds visibility delay for cart widget dropdown.
   *
   * @since 1.1.0
   */


  var sinatraCartDropdownDelay = function sinatraCartDropdownDelay() {
    var hoverTimer = null;
    document.querySelectorAll('.si-header-widget__cart .si-widget-wrapper').forEach(function (item) {
      item.addEventListener('mouseenter', function () {
        item.classList.remove('dropdown-visible');
      });
    });
    document.querySelectorAll('.si-header-widget__cart .si-widget-wrapper').forEach(function (item) {
      item.addEventListener('mouseleave', function () {
        item.classList.add('dropdown-visible');

        if (null !== hoverTimer) {
          clearTimeout(hoverTimer);
          hoverTimer = null;
        }

        hoverTimer = setTimeout(function () {
          item.classList.remove('dropdown-visible');
        }, 700);
      });
    });
  };
  /**
   * Handles header search widget functionality.
   *
   * @since 1.0.0
   */


  var sinatraHeaderSearch = function sinatraHeaderSearch() {
    var searchButton = document.querySelectorAll('.si-search');

    if (0 === searchButton.length) {
      return;
    }

    searchButton.forEach(function (item) {
      item.addEventListener('click', function (e) {
        e.preventDefault();

        if (item.classList.contains('sinatra-active')) {
          close_search(item);
        } else {
          show_search(item);
        }
      });
    }); // Show search.

    var show_search = function show_search(item) {
      // Make search visible
      document.body.classList.add('si-search-visible');
      setTimeout(function () {
        // Highlight the search icon
        item.classList.add('sinatra-active'); // Focus the input

        if (null !== item.nextElementSibling && null !== item.nextElementSibling.querySelector('input')) {
          item.nextElementSibling.querySelector('input').focus();
          item.nextElementSibling.querySelector('input').select();
        }
      }, 100); // Attach the ESC listener

      document.addEventListener('keydown', esc_close_search); // Attach the outside click listener

      pageWrapper.addEventListener('click', outside_close_search);
    }; // Close search


    var close_search = function close_search(item) {
      // Animate out
      document.body.classList.remove('si-search-visible'); // Unhighlight the search icon

      item.classList.remove('sinatra-active'); // Unhook the ESC listener

      document.removeEventListener('keydown', esc_close_search); // Unhook the click listener

      pageWrapper.removeEventListener('click', outside_close_search);
    }; // Esc support to close search


    var esc_close_search = function esc_close_search(e) {
      if (e.keyCode == 27) {
        document.querySelectorAll('.si-search').forEach(function (item) {
          close_search(item);
        });
      }
    }; // Close search when clicked anywhere outside the search box


    var outside_close_search = function outside_close_search(e) {
      if (null === e.target.closest('.si-search-container') && null === e.target.closest('.si-search')) {
        document.querySelectorAll('.si-search').forEach(function (item) {
          close_search(item);
        });
      }
    };
  };
  /**
   * Handles mobile menu functionality.
   *
   * @since 1.0.0
   */


  var sinatraMobileMenu = function sinatraMobileMenu() {
    var page = pageWrapper,
        nav = document.querySelector('#sinatra-header-inner .sinatra-nav'),
        current;
    document.querySelectorAll('.si-mobile-nav > button').forEach(function (item) {
      item.addEventListener('click', function (e) {
        e.preventDefault();

        if (document.body.parentNode.classList.contains('is-mobile-menu-active')) {
          close_menu();
        } else {
          show_menu();
        }
      }, false);
    }); // Helper functions.

    var show_menu = function show_menu(e) {
      // Add the active class.
      document.body.parentNode.classList.add('is-mobile-menu-active'); // Hook the ESC listener

      document.addEventListener('keyup', esc_close_menu); // Hook the click listener

      if (null !== page) {
        page.addEventListener('click', outside_close_menu);
      } // Hook the click listener for submenu toggle.


      document.querySelectorAll('#sinatra-header .sinatra-nav').forEach(function (item) {
        item.addEventListener('click', submenu_toggle);
      }); // Slide down the menu.

      sinatraSlideDown(nav, 350);
    };

    var close_menu = function close_menu(e) {
      // Remove the active class.
      document.body.parentNode.classList.remove('is-mobile-menu-active'); // Unhook the ESC listener

      document.removeEventListener('keyup', esc_close_menu); // Unhook the click listener

      if (null !== page) {
        page.removeEventListener('click', outside_close_menu);
      } // Close submenus


      document.querySelectorAll('#sinatra-header .sinatra-nav > ul > .si-open').forEach(function (item) {
        submenu_display_toggle(item);
      });
      nav.style.display = null;
      nav.querySelectorAll('.hovered').forEach(function (li) {
        li.classList.remove('hovered');
      });

      if (document.body.classList.contains('sinatra-is-mobile')) {
        // Unhook the click listener for submenu toggle
        document.querySelectorAll('#sinatra-header .sinatra-nav').forEach(function (item) {
          item.removeEventListener('click', submenu_toggle);
        }); // Slide up the menu

        sinatraSlideUp(nav, 250);
      }
    };

    var outside_close_menu = function outside_close_menu(e) {
      if (null === e.target.closest('.si-hamburger') && null === e.target.closest('.site-navigation')) {
        close_menu();
      }
    };

    var esc_close_menu = function esc_close_menu(e) {
      if (e.keyCode == 27) {
        close_menu();
      }
    };

    var submenu_toggle = function submenu_toggle(e) {
      if (e.target.parentElement.querySelectorAll('.sub-menu').length) {
        e.preventDefault();
        submenu_display_toggle(e.target.parentElement);
      }
    }; // Show or hide the sub menu.


    var submenu_display_toggle = function submenu_display_toggle(current) {
      if (current.classList.contains('si-open')) {
        current.classList.remove('si-open');
        current.querySelectorAll('.sub-menu').forEach(function (submenu) {
          submenu.style.display = null;
        }); // Close all submenus automatically.

        current.querySelectorAll('li').forEach(function (item) {
          item.classList.remove('si-open');
          item.querySelectorAll('.sub-menu').forEach(function (submenu) {
            submenu.style.display = null;
          });
        });
      } else {
        current.querySelectorAll('.sub-menu').forEach(function (submenu) {
          // Target first level elements only.
          if (current === submenu.parentElement) {
            submenu.style.display = 'block';
          }
        });
        current.classList.add('si-open');
      }
    }; // Create custom event for closing mobile menu.


    document.addEventListener('sinatra-close-mobile-menu', close_menu);
  };
  /**
   * Sinatra preloader.
   *
   * @since 1.0.0
   */


  var sinatraPreloader = function sinatraPreloader() {
    var timeout = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
    var preloader = document.getElementById('si-preloader');

    if (null === preloader) {
      return;
    }

    var delay = 250;

    var hide_preloader = function hide_preloader() {
      if (document.body.classList.contains('si-loaded')) {
        return;
      } // Start fade out animation.


      document.body.classList.add('si-loading');
      setTimeout(function () {
        // Fade out animation completed - set display none
        document.body.classList.replace('si-loading', 'si-loaded'); // Dispatch event when preloader is done

        sinatraTriggerEvent(document.body, 'si-preloader-done');
      }, delay);
    }; // Set timeout or hide immediately


    if (timeout > 0) {
      setTimeout(function () {
        hide_preloader();
      }, timeout);
    } else {
      hide_preloader();
    }

    return false;
  };
  /**
   * Handles comments toggle functionality.
   *
   * @since 1.0.0
   */


  var sinatraToggleComments = function sinatraToggleComments() {
    if (!document.body.classList.contains('sinatra-has-comments-toggle')) {
      return;
    }

    if (null == document.getElementById('sinatra-comments-toggle')) {
      return;
    }

    var toggleComments = function toggleComments(e) {
      if ('undefined' !== typeof e) {
        e.preventDefault();
      }

      if (document.body.classList.contains('comments-visible')) {
        document.body.classList.remove('comments-visible');
        document.getElementById('sinatra-comments-toggle').querySelector('span').innerHTML = sinatra_vars.strings.comments_toggle_show;
      } else {
        document.body.classList.add('comments-visible');
        document.getElementById('sinatra-comments-toggle').querySelector('span').innerHTML = sinatra_vars.strings.comments_toggle_hide;
      }
    };

    if (null !== document.getElementById('sinatra-comments-toggle') && (-1 !== location.href.indexOf('#comment') || -1 !== location.href.indexOf('respond'))) {
      toggleComments();
    }

    document.getElementById('sinatra-comments-toggle').addEventListener('click', toggleComments);
  };
  /**
   * Handles toggling and smooth scrolling when clicked on "Comments" link
   *
   * @since 1.0.0
   */


  var sinatraCommentsClick = function sinatraCommentsClick() {
    var commentsLink = document.querySelector('.single .comments-link');

    if (null === commentsLink) {
      return;
    }

    commentsLink.addEventListener('click', function (e) {
      // Show comments if hidden under a toggle
      if (document.body.classList.contains('sinatra-has-comments-toggle') && !document.body.classList.contains('comments-visible')) {
        document.getElementById('sinatra-comments-toggle').click();
      }
    });
  };
  /**
   * Removes inline styles on menus on resize.
   *
   * @since 1.1.0
   */


  var sinatraCheckMobileMenu = function sinatraCheckMobileMenu() {
    // Update body class if mobile breakpoint is reached.
    if (window.innerWidth <= sinatra_vars['responsive-breakpoint']) {
      document.body.classList.add('sinatra-is-mobile');
    } else {
      if (document.body.classList.contains('sinatra-is-mobile')) {
        document.body.classList.remove('sinatra-is-mobile');
        sinatraTriggerEvent(document, 'sinatra-close-mobile-menu');
      }
    }
  }; //--------------------------------------------------------------------//
  // Events
  //--------------------------------------------------------------------//
  // DOM ready


  document.addEventListener('DOMContentLoaded', function () {
    sinatraPreloader(5000);
    sinatraMenuAccessibility();
    sinatraKeyboardFocus();
    sinatraScrollTopButton();
    sinatraSmoothScroll();
    sinatraDropdownDelay();
    sinatraToggleComments();
    sinatraHeaderSearch();
    sinatraMobileMenu();
    sinatraCheckMobileMenu();
    sinatraSmartSubmenus();
    sinatraCommentsClick();
    sinatraCartDropdownDelay();
    sinatraStickyHeader();
    sinatraCalcScreenWidth();
  }); // Window load

  window.addEventListener('load', function () {
    sinatraPreloader();
  }); // Scroll

  window.addEventListener('scroll', function () {
    sinatraDebounce(sinatraScrollTopButton());
  }); // Resize

  window.addEventListener('resize', function () {
    sinatraDebounce(sinatraSmartSubmenus());
    sinatraDebounce(sinatraCheckMobileMenu());
    sinatraDebounce(sinatraCalcScreenWidth());
  }); // Sinatra ready

  sinatraTriggerEvent(document.body, 'si-ready'); //--------------------------------------------------------------------//
  // Global
  //--------------------------------------------------------------------//

  window.sinatra = window.sinatra || {}; // Make these function global.

  window.sinatra.preloader = sinatraPreloader;
  window.sinatra.stickyHeader = sinatraStickyHeader;
})();