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
            
            // Create a simple loading indicator
            this.loadingIndicator = $('<div>', {
                class: 'gallery-loading-indicator',
                text: 'Loading...',
                css: {
                    display: 'none',
                    position: 'fixed',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    padding: '10px 20px',
                    background: 'rgba(0,0,0,0.8)',
                    color: 'white',
                    borderRadius: '5px',
                    zIndex: 1000
                }
            }).appendTo('body');
            
            this.initEvents();
            this.initInfiniteScroll();
        }

        initEvents() {
            // Handle tag selection
            this.filterForm.find('input[type="checkbox"]').on('change', () => {
                this.currentPage = 1;
                this.updateFilters();
            });

            // Handle sorting
            this.filterForm.find('select').on('change', () => {
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

            // Get sorting options
            const orderby = this.filterForm.find('.orderby-select').val();
            const order = this.filterForm.find('.order-select').val();

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
                            if (this.currentPage === 1) {
                                this.galleryGrid.html($newContent);
                            } else {
                                this.galleryGrid.append($newContent);
                            }

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
