jQuery(document).ready(function($) {
    // Initialize Masonry for specific modes
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

        var $grid = $('.mode-' + mode);
        
        // Wait for all images to load
        $grid.imagesLoaded(function() {
            $grid.masonry(options);
        });

        // Update layout when images load
        $grid.find('img').each(function() {
            $(this).on('load', function() {
                $grid.masonry('layout');
            });
        });
    }

    // Initialize regular grid layout
    function initGridLayout() {
        var $grid = $('.gallery-grid:not(.mode-masonry):not(.mode-pinterest)');
        if (!$grid.length) return;

        // Wait for all images to load before revealing grid
        $grid.css('opacity', '0');
        $grid.imagesLoaded(function() {
            $grid.css('opacity', '1');
        });

        // Update layout when window resizes
        $(window).on('resize', function() {
            $grid.find('img').css('height', 'auto');
        });
    }

    // Initialize masonry for specific modes
    if ($('.mode-masonry').length) {
        initMasonry('masonry');
    }
    if ($('.mode-pinterest').length) {
        initMasonry('pinterest');
    }

    // Initialize regular grid layout
    initGridLayout();
});
