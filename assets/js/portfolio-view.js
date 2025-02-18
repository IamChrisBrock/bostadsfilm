jQuery(document).ready(function($) {
    const $viewModeToggle = $('#view-mode-toggle');
    const $switchLabel = $('.switch-label');
    const $portfolioItems = $('#portfolio-items');
    
    // Initialize view state from URL
    const urlParams = new URLSearchParams(window.location.search);
    const isMediaView = urlParams.get('view') === 'media';
    
    // Set initial toggle state
    $viewModeToggle.prop('checked', isMediaView);
    $switchLabel.text(isMediaView ? 'Media' : 'Projects');
    
    // Set initial grid class
    $portfolioItems
        .removeClass('portfolio-projects-grid portfolio-grid masonry-grid full-width-mode')
        .addClass(isMediaView ? '' : 'portfolio-projects-grid');
    
    // Initialize GLightbox
    let lightbox = null;
    function initLightbox() {
        if (window.GLightbox) {
            if (lightbox) {
                lightbox.destroy();
            }
            lightbox = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true
            });
        }
    }
    initLightbox();
    
    function updateView(isMediaView) {
        
        
        // Update label
        $switchLabel.text(isMediaView ? 'Media' : 'Projects');
        
        // Create URL for the AJAX request
        const requestUrl = new URL(window.location);
        if (isMediaView) {
            requestUrl.searchParams.set('view', 'media');
        } else {
            // For projects view, force a clean URL
            requestUrl.searchParams.delete('view');
            requestUrl.searchParams.delete('paged');
        }
        
        
        
        // Update browser URL without reloading
        window.history.pushState({}, '', requestUrl);
        
        // Show loading state with placeholder
        const placeholderHTML = '<div class="loading-placeholder">' + 
            Array(8).fill('<div class="placeholder-item"></div>').join('') + 
            '</div>';
        $portfolioItems
            .addClass('loading')
            .html(placeholderHTML);
        
        // Reset container classes
        $portfolioItems
            .removeClass('portfolio-projects-grid')
            .removeClass('portfolio-grid')
            .removeClass('masonry-grid')
            .removeClass('full-width-mode');
            
        // Add appropriate class for the view
        if (!isMediaView) {
            $portfolioItems.addClass('portfolio-projects-grid');
        }
        
        // Fetch new content via AJAX using the same URL
        $.ajax({
            url: requestUrl.toString(),
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                
                
                // Extract the portfolio items content from the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(response, 'text/html');
                
                
                // Try both jQuery and native selectors
                // Try to find the portfolio content in different ways
                let newContent = '';
                
                // First try: direct portfolio-items
                const portfolioItems = doc.querySelector('#portfolio-items');
                if (portfolioItems) {
                    newContent = portfolioItems.innerHTML;
                } else {
                    // Second try: look for media grid content
                    const mediaGrid = doc.querySelector('.portfolio-grid');
                    if (mediaGrid) {
                        newContent = mediaGrid.outerHTML;
                    } else {
                        
                        return;
                    }
                }
                
                
                
                // Clear existing content
                $portfolioItems.empty();
                
                if (isMediaView) {
                    // For media view, look for the media grid content
                    const mediaGrid = doc.querySelector('.portfolio-grid');
                    if (mediaGrid) {
                        $portfolioItems.html(mediaGrid.outerHTML);
                    } else {
                        console.error('Could not find media grid content');
                        return;
                    }
                } else {
                    // For projects view, look for projects content
                    const projectsGrid = doc.querySelector('.portfolio-projects-grid');
                    if (projectsGrid) {
                        $portfolioItems.addClass('portfolio-projects-grid').html(projectsGrid.innerHTML);
                    } else {
                        console.error('Could not find projects grid content');
                        return;
                    }
                }
                
                if (isMediaView) {
                    // Initialize lazy loading for media items
                    const mediaItems = document.querySelectorAll('.lazy-media');
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const mediaItem = entry.target;
                                if (mediaItem.tagName.toLowerCase() === 'img') {
                                    mediaItem.src = mediaItem.dataset.src;
                                } else if (mediaItem.tagName.toLowerCase() === 'video') {
                                    mediaItem.src = mediaItem.dataset.src;
                                    mediaItem.load();
                                }
                                mediaItem.classList.add('loaded');
                                observer.unobserve(mediaItem);
                            }
                        });
                    });

                    mediaItems.forEach(item => observer.observe(item));
                    
                    // Initialize Masonry grid
                    const $grid = $('.masonry-grid');
                    if ($grid.length) {
                        $grid.imagesLoaded(() => {
                            $grid.masonry({
                                itemSelector: '.grid-item',
                                columnWidth: '.grid-sizer',
                                percentPosition: true
                            });
                        });
                    }
                    
                    // Initialize lightbox
                    initLightbox();
                }
            },
            error: function() {
                alert('Failed to load content. Please try again.');
            },
            complete: function() {
                $portfolioItems.removeClass('loading');
            }
        });
    }
    
    // Handle toggle change
    $viewModeToggle.on('change', function() {
        const isChecked = $(this).is(':checked');
        
        updateView(isChecked);
    });
    
    // Initialize view based on URL parameter
    if (urlParams.get('view') === 'media') {
        $viewModeToggle.prop('checked', true);
        updateView(true); // Actually load the media view
    } else {
        $viewModeToggle.prop('checked', false);
        $switchLabel.text('Projects');
    }
});

