(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize any interactive features
        $('.best-portfolio-item').on('click', function(e) {
            if ($(this).data('link')) {
                window.location.href = $(this).data('link');
            }
        });
    });
})(jQuery);
