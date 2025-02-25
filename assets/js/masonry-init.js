jQuery(document).ready(function($) {
    // Initialize Masonry for specific modes
    function initMasonry(mode) {
        var $grid = $('.mode-' + mode);
        if (!$grid.length) return;

        // Hide grid initially
        $grid.css('opacity', '0');

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

        // Create resize handler
        var resizeHandler = debounce(function() {
            if (!$grid.data('masonry')) return;
            $grid.masonry('layout');
        }, 250);

        // Initialize Masonry with initial options
        var masonryInstance = $grid.masonry({
            itemSelector: '.single-gallery-item',
            percentPosition: true,
            columnWidth: '.single-gallery-item',
            gutter: mode === 'pinterest' ? 20 : 1,
            transitionDuration: 0, // Start with no transition
            initLayout: false // Don't layout immediately
        });

        // Track loaded items
        var totalItems = $grid.find('.single-gallery-item').length;
        var loadedItems = 0;

        // Function to check if all items are loaded
        function checkAllLoaded() {
            loadedItems++;
            if (loadedItems === totalItems) {
                // All items loaded, do final layout
                $grid.masonry('layout');
                
                // Now enable transitions for smooth updates
                $grid.find('.single-gallery-item').css('transition', 'all 0.4s ease-in-out');
                
                // Show grid
                $grid.css('opacity', '1');
                
                // Bind resize handler
                $(window).off('resize.masonry-' + mode)
                        .on('resize.masonry-' + mode, resizeHandler);
            }
        }

        // Wait for all images to load
        $grid.find('.single-gallery-item').each(function() {
            var $item = $(this);
            var $img = $item.find('img');
            var $video = $item.find('video');

            if ($img.length) {
                if ($img[0].complete) {
                    $item.addClass('loaded');
                    checkAllLoaded();
                } else {
                    $img.on('load', function() {
                        $item.addClass('loaded');
                        checkAllLoaded();
                    });
                }
            } else if ($video.length) {
                if ($video[0].readyState >= 1) {
                    $item.addClass('loaded');
                    checkAllLoaded();
                } else {
                    $video.on('loadedmetadata', function() {
                        $item.addClass('loaded');
                        checkAllLoaded();
                    });
                }
            } else {
                // Text blocks or other non-media items
                $item.addClass('loaded');
                checkAllLoaded();
            }
        });
    }

    // Initialize regular grid layout
    function initGridLayout() {
        var $grid = $('.gallery-grid:not(.mode-masonry):not(.mode-pinterest)');
        if (!$grid.length) return;

        // Wait for all images to load before revealing grid
        $grid.css('opacity', '0');
        $grid.imagesLoaded().progress(function(instance, image) {
            // Add loaded class to each image as it loads
            $(image.img).closest('.single-gallery-item').addClass('loaded');
        }).done(function() {
            $grid.css('opacity', '1');
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
