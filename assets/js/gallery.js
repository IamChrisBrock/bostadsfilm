document.addEventListener('DOMContentLoaded', function() {
    // Initialize GLightbox for all gallery items
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: true,
        width: '90vw',
        height: 'auto',
        cssEfects: {
            fade: { in: 'fadeIn', out: 'fadeOut' }
        },
        onOpen: () => {
            document.body.classList.add('glightbox-open');
        },
        onClose: () => {
            document.body.classList.remove('glightbox-open');
        },
        plyr: {
            css: 'https://cdn.plyr.io/3.6.8/plyr.css',
            js: 'https://cdn.plyr.io/3.6.8/plyr.js',
            config: {
                ratio: '16:9',
                fullscreen: { enabled: true }
            }
        }
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
                const item = entry.target;
                const type = item.closest('.single-gallery-item').dataset.type;
                
                if (type === 'text') {
                    // Text blocks are always loaded
                    item.classList.add('loaded');
                    observer.unobserve(item);
                } else {
                    const src = item.dataset.src;
                    if (item.tagName.toLowerCase() === 'video') {
                        // Handle video lazy loading
                        item.src = src;
                        item.load();
                    } else {
                        // Handle image lazy loading
                        item.src = src;
                    }
                    item.classList.add('loaded');
                    observer.unobserve(item);
                }


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
