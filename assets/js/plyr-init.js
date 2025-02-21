document.addEventListener('DOMContentLoaded', function() {
    // Initialize Plyr for videos in the grid
    const gridVideos = document.querySelectorAll('.js-player');
    if (gridVideos.length > 0) {
        gridVideos.forEach(video => {
            if (!video.classList.contains('plyr--setup')) {
                const player = new Plyr(video, {
                    controls: [
                        'play-large',
                        'play',
                        'progress',
                        'current-time',
                        'mute',
                        'volume',
                        'fullscreen'
                    ],
                    hideControls: false,
                    resetOnEnd: true,
                    keyboard: { focused: true, global: false }
                });
                video.classList.add('plyr--setup');

                // Update aspect ratio when metadata is loaded
                video.addEventListener('loadedmetadata', function() {
                    if (video.videoWidth && video.videoHeight) {
                        video.style.aspectRatio = `${video.videoWidth}/${video.videoHeight}`;
                    }
                });
            }
        });
    }
});
