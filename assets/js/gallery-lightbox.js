document.addEventListener('DOMContentLoaded', function() {
    // Initialize Plyr for grid videos
    const gridVideos = document.querySelectorAll('.js-player:not(.plyr--setup)');
    gridVideos.forEach(video => {
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
    });

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

