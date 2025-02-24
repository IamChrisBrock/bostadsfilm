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

    // Lazy load gallery items
    const galleryItems = document.querySelectorAll('.single-gallery-item');
    
    const itemObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const item = entry.target;
                const media = item.querySelector('img, video');

                if (media) {
                    // Set loading attribute for native lazy loading
                    media.loading = 'lazy';

                    const loadHandler = () => {
                        requestAnimationFrame(() => {
                            item.classList.add('loaded');
                        });
                    };

                    // For images
                    if (media.tagName === 'IMG') {
                        if (media.complete) {
                            loadHandler();
                        } else {
                            media.addEventListener('load', loadHandler);
                        }
                    }
                    // For videos
                    else if (media.tagName === 'VIDEO') {
                        const video = media;
                        if (!video.classList.contains('plyr--setup')) {
                            new Plyr(video, {
                                controls: [
                                    'play-large',
                                    'play',
                                    'progress',
                                    'current-time',
                                    'mute',
                                    'volume',
                                    'fullscreen'
                                ],
                                hideControls: false
                            });
                            video.classList.add('plyr--setup');
                        }
                        // Videos might take time to load metadata
                        if (video.readyState >= 1) {
                            loadHandler();
                        } else {
                            video.addEventListener('loadedmetadata', loadHandler);
                        }
                    }
                }

                observer.unobserve(item);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '200px'
    });

    galleryItems.forEach(function(item) {
        itemObserver.observe(item);
    });
});

