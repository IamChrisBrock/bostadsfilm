document.addEventListener('DOMContentLoaded', function() {
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
        descPosition: 'bottom'
    });
});
