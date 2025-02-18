document.addEventListener('DOMContentLoaded', function() {
    // Initialize GLightbox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });

    // Initialize Masonry for masonry grids only
    const masonryGrids = document.querySelectorAll('.masonry-grid');
    
    masonryGrids.forEach(function(grid) {
        // Initialize Masonry with options
        const masonryInstance = new Masonry(grid, {
            itemSelector: '.grid-item',
            columnWidth: '.grid-sizer',
            percentPosition: true,
            transitionDuration: '0.3s'
        });

        // Initialize images loaded to trigger masonry layout
        imagesLoaded(grid).on('progress', function() {
            masonryInstance.layout();
        });
    });
});
