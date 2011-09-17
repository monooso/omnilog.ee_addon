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

        $('.experienceinternet .extended_data_toggle').click(function(){

        	$(this).siblings('.extended_data_hidden').toggle();
        	
        	$(this).children('span.view').toggle();  
        	$(this).children('span.hide').toggle();  

            return false;
   		});

    });

})(jQuery);


/* End of file      : cp.js */
/* File location    : themes/third_party/omnilog/common/js/cp.js */
