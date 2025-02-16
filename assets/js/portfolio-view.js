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
        // Update label and grid mode
        $switchLabel.text(isMediaView ? 'Media' : 'Projects');
        $portfolioGrid.attr('data-display-mode', isMediaView ? 'media' : 'grid');
        
        // Show loading state with fade effect
        $portfolioItems.fadeOut(300, function() {
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
                        $portfolioItems.html(response.data.html);
                        
                        // Reinitialize GLightbox for new media items
                        if (lightbox) {
                            lightbox.destroy();
                            lightbox = GLightbox({
                                selector: '.glightbox',
                                touchNavigation: true,
                                loop: true
                            });
                        }
                        
                        // Save view state in URL without page reload
                        const url = new URL(window.location);
                        url.searchParams.set('view', isMediaView ? 'media' : 'projects');
                        window.history.pushState({}, '', url);
                    }
                },
                complete: function() {
                    $portfolioItems.removeClass('loading').fadeIn(300);
                }
            });
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

