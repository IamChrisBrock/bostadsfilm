/**
 * Gallery Filtering System
 */
(function($) {
    'use strict';

    class GalleryFilter {
        constructor() {
            this.filterForm = $('.gallery-filters');
            this.galleryGrid = $('.gallery-grid');
            this.pagination = $('.pagination');
            this.isLoading = false;
            this.currentPage = 1;
            
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
            this.galleryGrid.addClass('loading');

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
                        if (this.currentPage === 1) {
                            this.galleryGrid.html(response.data.html);
                        } else {
                            this.galleryGrid.append(response.data.html);
                        }

                        if (this.currentPage >= response.data.max_pages) {
                            $(window).off('scroll');
                        }
                    }
                },
                complete: () => {
                    this.isLoading = false;
                    this.galleryGrid.removeClass('loading');
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
