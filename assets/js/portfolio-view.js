jQuery(document).ready(function($) {
    const $viewModeToggle = $('#view-mode-toggle');
    const $switchLabel = $('.switch-label');
    const $portfolioItems = $('#portfolio-items');
    const $portfolioGrid = $('.portfolio-grid');
    
    // Initialize GLightbox
    let lightbox = null;
    if (window.GLightbox) {
        lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true
        });
    }
    
    function updateView(isMediaView) {
        // Immediately update label and URL
        $switchLabel.text(isMediaView ? 'Media' : 'Projects');
        const url = new URL(window.location);
        url.searchParams.set('view', isMediaView ? 'media' : 'projects');
        window.history.pushState({}, '', url);
        
        // Show loading state immediately
        $portfolioItems.addClass('loading');
        
        // Make AJAX call to get content
        $.ajax({
            url: portfolio_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'toggle_portfolio_view',
                nonce: portfolio_ajax.nonce,
                view: isMediaView ? 'media' : 'projects'
            },
            success: function(response) {
                if (response.success) {
                    // Update content
                    $portfolioItems.html(response.data.html);
                    
                    // Only reinitialize GLightbox if switching to media view
                    if (isMediaView && lightbox) {
                        lightbox.destroy();
                        lightbox = GLightbox({
                            selector: '.glightbox',
                            touchNavigation: true,
                            loop: true
                        });
                    }
                }
            },
            complete: function() {
                // Remove loading state
                $portfolioItems.removeClass('loading');
            }
        });
    }
    
    // Handle toggle change
    $viewModeToggle.on('change', function() {
        updateView(this.checked);
    });
    
    // Initialize view based on URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const viewParam = urlParams.get('view');
    if (viewParam === 'media') {
        $viewModeToggle.prop('checked', true).trigger('change');
    }
});

