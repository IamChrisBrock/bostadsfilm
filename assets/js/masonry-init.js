jQuery(document).ready(function($) {
    // Initialize Masonry for specific modes
    function initMasonry(mode) {
        var $grid = $('.mode-' + mode);
        if (!$grid.length) return;

        // Hide grid initially
        $grid.css('opacity', '0');
        
        // Store initial positions
        $grid.find('.single-gallery-item').each(function() {
            $(this).css({
                position: 'absolute',
                left: $(this).offset().left + 'px',
                top: $(this).offset().top + 'px'
            });
        });

        var options = {
            itemSelector: '.single-gallery-item',
            percentPosition: true,
            columnWidth: '.single-gallery-item',
            gutter: mode === 'pinterest' ? 20 : 1,
            transitionDuration: 0,
            initLayout: true,
            resize: false
        };

        // Initialize Masonry with no animation
        var masonryInstance = $grid.masonry(options);
        
        // Add CSS transitions after initial layout
        setTimeout(function() {
            $grid.find('.single-gallery-item').css({
                transition: 'all 0.4s ease-in-out'
            });
            $grid.css('opacity', '1');
        }, 100);

        // Debounce function
        function debounce(func, wait) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    func.apply(context, args);
                }, wait);
            };
        }

        // Handle resize
        var resizeHandler = debounce(function() {
            if (!$grid.data('masonry')) return;
            $grid.masonry('layout');
        }, 250);

        // Remove any existing handlers and add new one
        $(window).off('resize.masonry-' + mode)
                .on('resize.masonry-' + mode, resizeHandler);

        // Initial layout after images load
        $grid.imagesLoaded().done(function() {
            resizeHandler();
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
