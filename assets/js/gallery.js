document.addEventListener('DOMContentLoaded', function() {
    // Initialize GLightbox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });

    // Initialize Masonry for masonry grids only
    const masonryGrids = document.querySelectorAll('.masonry-grid');
    const masonryInstances = new Map();
    
    masonryGrids.forEach(function(grid) {
        // Initialize Masonry with options
        const masonryInstance = new Masonry(grid, {
            itemSelector: '.grid-item',
            columnWidth: '.grid-sizer',
            percentPosition: true,
            transitionDuration: '0.3s'
        });

        masonryInstances.set(grid, masonryInstance);

        // Initialize images loaded to trigger masonry layout
        imagesLoaded(grid).on('progress', function() {
            masonryInstance.layout();
        });
    });

    // Lazy loading implementation
    const lazyLoadMedia = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const media = entry.target;
                const src = media.dataset.src;

                if (media.tagName.toLowerCase() === 'video') {
                    // Handle video lazy loading
                    media.src = src;
                    media.load();
                } else {
                    // Handle image lazy loading
                    media.src = src;
                }

                media.classList.add('loaded');
                observer.unobserve(media);

                // Trigger masonry layout after media loads
                const gridParent = media.closest('.masonry-grid');
                if (gridParent && masonryInstances.has(gridParent)) {
                    const masonry = masonryInstances.get(gridParent);
                    if (media.tagName.toLowerCase() === 'img') {
                        media.onload = () => masonry.layout();
                    } else {
                        masonry.layout();
                    }
                }
            }
        });
    };

    // Create intersection observer
    const mediaObserver = new IntersectionObserver(lazyLoadMedia, {
        root: null,
        rootMargin: '50px',
        threshold: 0.1
    });

    // Observe all lazy media elements
    document.querySelectorAll('.lazy-media').forEach(media => {
        mediaObserver.observe(media);
    });
});
