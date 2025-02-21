/**
 * Gallery Filtering System
 */
(function($) {
    'use strict';

    class GalleryFilter {
        constructor() {
            console.log('Initializing GalleryFilter');
            this.filterForm = $('.gallery-filters');
            this.galleryGrid = $('.gallery-grid');
            this.pagination = $('.pagination');
            this.isLoading = false;
            this.currentPage = 1;
            
            // Create loading animation container
            this.loadingIndicator = $('<div>', {
                class: 'gallery-loading-indicator',
                css: {
                    display: 'none',
                    position: 'fixed',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    width: '100px',  // Reduced size
                    height: '100px',  // Reduced size
                    zIndex: 1000,
                    background: 'transparent',  // Ensure background is transparent
                    pointerEvents: 'none'  // Don't block clicks
                }
            }).appendTo('body');

            // Initialize loading animation with error handling
            const animationPath = document.querySelector('meta[name="theme-url"]').content + '/assets/lottie/loading-house.json';
            console.log('Loading animation from:', animationPath);

            this.loadingAnimation = lottie.loadAnimation({
                container: this.loadingIndicator[0],
                renderer: 'svg',
                loop: true,
                autoplay: false,
                path: animationPath
            });

            // Add event listeners for debugging
            this.loadingAnimation.addEventListener('data_ready', () => {
                console.log('Lottie data loaded successfully');
            });

            this.loadingAnimation.addEventListener('data_failed', () => {
                console.error('Failed to load Lottie animation');
            });

            this.loadingAnimation.addEventListener('DOMLoaded', () => {
                console.log('Lottie DOM elements loaded');
            });
            
            this.initEvents();
            this.initInfiniteScroll();
        }

        initEvents() {
            // Handle tag selection
            this.filterForm.find('input[type="checkbox"]').on('change', () => {
                this.currentPage = 1;
                this.updateFilters();
            });

            // Handle sorting changes
            this.filterForm.find('.orderby-select, .order-select').on('change', (e) => {
                console.log('Sort change:', e.target.name, e.target.value);
                this.currentPage = 1;
                this.updateFilters();
            });
        }

        initInfiniteScroll() {
            $(window).on('scroll', () => {
                if (this.isLoading) return;

                const scrollPos = $(window).scrollTop() + $(window).height();
                const triggerPos = $(document).height() - 200;

                if (scrollPos > triggerPos) {
                    this.loadMoreGalleries();
                }
            });
        }

        updateFilters() {
            this.isLoading = true;
            
            // Get selected tags
            const selectedTags = [];
            this.filterForm.find('input[type="checkbox"]:checked').each(function() {
                selectedTags.push($(this).val());
            });

            // Get sorting options with debug
            const orderby = this.filterForm.find('select[name="orderby"]').val();
            const order = this.filterForm.find('select[name="order"]').val();
            console.log('Sorting params - orderby:', orderby, 'order:', order);

            // Update URL
            const params = new URLSearchParams(window.location.search);
            params.set('gallery_tags', selectedTags.join(','));
            params.set('orderby', orderby);
            params.set('order', order);
            params.set('page', this.currentPage);
            
            window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);

            // Show loading state
            console.log('Starting AJAX request...');
            this.galleryGrid.addClass('loading');
            this.loadingIndicator.show();
            
            // Reset and play animation
            this.loadingAnimation.goToAndPlay(0, true);
            console.log('Playing animation...');

            // Make AJAX request
            $.ajax({
                url: galleryFilters.ajaxurl,
                type: 'POST',
                data: {
                    action: 'filter_galleries',
                    nonce: galleryFilters.nonce,
                    tags: selectedTags.join(','),
                    orderby: orderby,
                    order: order,
                    page: this.currentPage
                },
                success: (response) => {
                    if (response.success) {
                        // Log debug info
                        console.log('AJAX success, server response:', response.data.debug);
                        
                        const $newContent = $(response.data.html);
                        
                        // Pre-load images before showing content
                        const imagePromises = [];
                        $newContent.find('img').each(function() {
                            const promise = new Promise((resolve) => {
                                const img = new Image();
                                img.onload = resolve;
                                img.src = this.src;
                            });
                            imagePromises.push(promise);
                        });

                        // Once all images are loaded, show the content
                        Promise.all(imagePromises).then(() => {
                            console.log('Images loaded, updating gallery with sorted content');
                            // Clear existing content and update
                            // Since the response already includes the row div, we just need to replace the content
                            this.galleryGrid.empty().append($newContent);

                            if (this.currentPage >= response.data.max_pages) {
                                $(window).off('scroll');
                            }
                        });
                    }
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    console.error('AJAX error:', textStatus, errorThrown);
                },
                complete: () => {
                    console.log('AJAX request completed');
                    this.isLoading = false;
                    this.galleryGrid.removeClass('loading');
                    this.loadingIndicator.hide();
                    this.loadingAnimation.stop();
                    console.log('Animation stopped');
                }
            });
        }

        loadMoreGalleries() {
            this.currentPage++;
            this.updateFilters();
        }
    }

    // Initialize on document ready
    $(document).ready(() => {
        new GalleryFilter();
    });

})(jQuery);
