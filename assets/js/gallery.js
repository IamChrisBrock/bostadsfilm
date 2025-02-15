document.addEventListener('DOMContentLoaded', function() {
    // Initialize GLightbox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });

    // Initialize Masonry
    const grid = document.querySelector('.portfolio-grid');
    const masonry = new Masonry(grid, {
        itemSelector: '.portfolio-item',
        columnWidth: '.portfolio-item',
        percentPosition: true,
        gutter: 30
    });

    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');

            portfolioItems.forEach(item => {
                if (filter === 'all') {
                    item.style.display = 'block';
                } else {
                    if (item.classList.contains(filter)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                }
            });

            // Re-layout Masonry
            masonry.layout();
        });
    });

    // Initialize images loaded to trigger masonry layout
    imagesLoaded(grid).on('progress', function() {
        masonry.layout();
    });
});
