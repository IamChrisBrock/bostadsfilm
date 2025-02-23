jQuery(document).ready(function($) {
    function setContentHeight($box, isOpening) {
        const $content = $box.find('.info-box-content');
        const $inner = $box.find('.info-box-content-inner');
        
        if (isOpening) {
            // Get the height of the inner content
            const contentHeight = $inner.outerHeight();
            // Set the specific height
            $content.css('height', contentHeight + 'px');
        } else {
            // Animate back to 0
            $content.css('height', '0');
        }
    }

    // Handle info box clicks
    $('.info-box-header').on('click', function() {
        const $box = $(this).closest('.single-gallery-info-box');
        
        if ($box.hasClass('active')) {
            // Close this box
            setContentHeight($box, false);
            $box.removeClass('active');
        } else {
            // Open this box
            $box.addClass('active');
            setContentHeight($box, true);
        }
    });

    // Handle window resize
    let resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            $('.single-gallery-info-box.active').each(function() {
                setContentHeight($(this), true);
            });
        }, 250);
    });
});

