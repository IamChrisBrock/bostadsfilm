document.addEventListener('DOMContentLoaded', function() {
    // Initialize GLightbox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });

    // Initialize Masonry
    const grid = document.querySelector('.portfolio-grid');

    // Initialize images loaded to trigger masonry layout
    imagesLoaded(grid).on('progress', function() {
        masonry.layout();
    });
});
