(function ($) {
  Drupal.behaviors.time_spent = {
  attach: function(context) {
    //detect if is in an iframe, like overlay module
    var isInIFrame = (window.location != window.parent.location) ? true : false;
    var timer = setInterval( time_spent_ajax, (Drupal.settings.time_spent.timer * 1000));
    setTimeout(function() {clearInterval(timer);}, Drupal.settings.time_spent.limit * 1000 * 60);
    window.parent.enabled = true;
    if(!isInIFrame){
    }
    else{
      //if a page is loaded into a overlay iframe
      //cancell the timespent from the page under overlay
      //parent.clearInterval(timer);
      window.parent.enabled = false;
    }    
    $('#overlay-close').click(function() {
      window.parent.enabled = true;
    });
    //polling every minute
    function time_spent_ajax() {
      if(isInIFrame || window.parent.enabled){
        $.ajax({
          type: 'get',
          url: Drupal.settings.basePath + 'js/time_spent/ajax/' + Drupal.settings.time_spent.nid + '/' + Drupal.settings.time_spent.sectoken, 
          dataType: 'json', 
          data: 'js=1' 
        });
      }
    }
   }
};
})(jQuery);