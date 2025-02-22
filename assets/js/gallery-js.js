document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lottie animation for portfolio link
    const portfolioArrow = document.querySelector('.portfolio-link-arrow');
    if (portfolioArrow) {
        const animation = lottie.loadAnimation({
            container: portfolioArrow,
            renderer: 'svg',
            loop: false,
            autoplay: false,
            path: portfolioArrow.dataset.animationPath
        });

        // Play animation on hover
        portfolioArrow.closest('.portfolio-link').addEventListener('mouseenter', () => {
            animation.setDirection(1);
            animation.play();
        });

        portfolioArrow.closest('.portfolio-link').addEventListener('mouseleave', () => {
            animation.setDirection(-1);
            animation.play();
        });
    }

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

    galleryItems.forEach(item => itemObserver.observe(item));

    // Initialize GLightbox
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: false,
        preload: false,
        moreLength: 0,
        slideEffect: 'slide',
        openEffect: 'fade',
        closeEffect: 'fade',
        draggable: true,
        zoomable: true,
        dragAutoSnap: true,
        descPosition: 'bottom',
        videosWidth: '960px',
        plyr: {
            css: false,
            js: false,
            config: {
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
            }
        }
    });
});

