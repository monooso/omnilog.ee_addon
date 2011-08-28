/**
 * Common JS behaviours.
 *
 * @author          Stephen Lewis
 * @package         Omnilog
 */

(function($) {

    $(document).ready(function() {

        // All a bit cowboyed, but it's just for the demo, so not that bothered.
        $('a[href*="demo=notify_custom"]').click(function() {
            var $link = $(this);
            var email  = $link.closest('tr').find('input[name="email"]').val();

            $link.attr('href', $link.attr('href') + encodeURIComponent(email));
        });

    });

})(jQuery);


/* End of file      : cp.js */
/* File location    : themes/third_party/omnilog/common/js/cp.js */
