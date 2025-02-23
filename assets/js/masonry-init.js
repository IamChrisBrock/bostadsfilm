jQuery(document).ready(function($) {
    function initMasonry(mode) {
        var options = {
            itemSelector: '.single-gallery-item',
            percentPosition: true,
            columnWidth: '.single-gallery-item'
        };

        // Add specific options based on mode
        if (mode === 'pinterest') {
            options.gutter = 20;
        } else {
            options.gutter = 1;
        }

        var $grid = $('.mode-' + mode).masonry(options);

        // Layout Masonry after each image loads
        $grid.imagesLoaded().progress(function() {
            $grid.masonry('layout');
        });
    }

    // Initialize masonry for both modes
    if ($('.mode-masonry').length) {
        initMasonry('masonry');
    }
    if ($('.mode-pinterest').length) {
        initMasonry('pinterest');
    }
});
