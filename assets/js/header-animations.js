/**
 * Header Animations
 */
(function($) {
    'use strict';

    function initHeaderAnimation() {
        const headers = $('.portfolio-gallery-header, .single-gallery-header');
        
        if (headers.length) {
            // Wait for page load + 1.2 seconds before animating
            setTimeout(() => {
                headers.addClass('header-collapsed');
            }, 1200);
        }
    }

    // Initialize on document ready
    $(document).ready(() => {
        initHeaderAnimation();
    });

})(jQuery);
