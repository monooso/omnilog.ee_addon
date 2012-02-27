/**
 * Common JS behaviours.
 *
 * @author          Stephen Lewis
 * @package         Omnilog
 */

(function($) {

  /**
   * Initialises the demo.
   */
  function iniDemo() {
    // All a bit cowboyed, but it's just for the demo, so not that bothered.
    $('a[href*="demo=notify_custom"]').click(function() {
      var $link = $(this);
      var email  = $link.closest('tr').find('input[name="email"]').val();

      $link.attr('href', $link.attr('href') + encodeURIComponent(email));
    });
  }


  /**
   * Initialises the 'extended data'.
   */
  function iniExtendedData() {
    $('.experienceinternet .extended_data').each(function() {
      var $td = $(this);
      var $body, $link;

      /*
       * Rather rudimentary way of ensuring that the extended data doesn't get
       * initialised twice on the main log page, when the accessory is also
       * present. You'd think EE would be smart enough to not include the same
       * JS file twice, but apparently not.
       */

      if ($td.hasClass('initialized')) {
        return;
      }

      $td.addClass('initialized');

      // Create the new HTML elements.
      $td.wrapInner('<div class="extended_data_body" />');
      $td.append('<a href="#" class="extended_data_toggle">'
        + EE.omnilog.lang.lblShow + '</a>');

      // Hide extended data by default.
      $body = $td.find('.extended_data_body');
      $link = $td.find('.extended_data_toggle');

      $body.hide();

      $link.click(function() {
        var linkText;
        var isOpen = ($body.css('display').toLowerCase() == 'block');

        $body.slideToggle(250, function() {
          linkText = $body.css('display') == 'block'
            ? EE.omnilog.lang.lblHide
            : EE.omnilog.lang.lblShow;

          $link.text(linkText);
        });

        return false;
      });
    });
  }


  // Start the ball rolling...
  $(document).ready(function() {
    iniDemo();
    iniExtendedData();
  });

})(jQuery);


/* End of file      : cp.js */
/* File location    : themes/third_party/omnilog/common/js/cp.js */
