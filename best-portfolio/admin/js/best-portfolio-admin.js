/**
 * Best Portfolio Admin JavaScript
 *
 * This file contains all the JavaScript functionality for the admin interface,
 * including:
 * - Drag and drop sorting for galleries and items
 * - AJAX handlers for updating sort order
 * - Media upload integration
 * - Dynamic form handling
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/admin/js
 */

(function($) {
    'use strict';

    // Main admin class
    const BestPortfolioAdmin = {
        /**
         * Initialize the admin functionality
         */
        init: function() {
            this.initSortableGalleries();
            this.initSortableItems();
            this.initMediaUploader();
            this.initEventListeners();
        },

        /**
         * Initialize sortable galleries within a portfolio
         */
        initSortableGalleries: function() {
            $('.best-portfolio-galleries .galleries-list').sortable({
                handle: '.sort-handle',
                placeholder: 'best-portfolio-sortable-placeholder',
                tolerance: 'pointer',
                axis: 'y',
                update: function(event, ui) {
                    const galleryOrder = $(this).sortable('toArray', { attribute: 'data-gallery-id' });
                    BestPortfolioAdmin.updateGalleryOrder(galleryOrder);
                }
            });
        },

        /**
         * Initialize sortable items within a gallery
         */
        initSortableItems: function() {
            $('.gallery-items-grid').sortable({
                handle: '.sort-handle',
                placeholder: 'best-portfolio-sortable-placeholder',
                tolerance: 'pointer',
                update: function(event, ui) {
                    const itemOrder = $(this).sortable('toArray', { attribute: 'data-item-id' });
                    BestPortfolioAdmin.updateItemOrder(itemOrder);
                }
            });
        },

        /**
         * Initialize WordPress media uploader
         */
        initMediaUploader: function() {
            let mediaUploader;

            $('.upload-media-button').on('click', function(e) {
                e.preventDefault();

                const button = $(this);
                const mediaType = button.data('media-type') || 'image';
                const targetInput = $(`#${button.data('target')}`);
                const previewImg = $(`#${button.data('preview')}`);

                // If uploader exists, open it
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                // Create new uploader
                mediaUploader = wp.media({
                    title: 'Select Media',
                    button: {
                        text: 'Use this media'
                    },
                    multiple: false,
                    library: {
                        type: mediaType
                    }
                });

                // When media is selected
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    targetInput.val(attachment.url);
                    
                    if (previewImg.length && attachment.type === 'image') {
                        previewImg.attr('src', attachment.url);
                    }
                });

                mediaUploader.open();
            });
        },

        /**
         * Initialize various event listeners
         */
        initEventListeners: function() {
            // Media type change handler
            $('#media-type').on('change', function() {
                const mediaType = $(this).val();
                $('.media-field').hide();
                $(`.media-field.media-${mediaType}`).show();
            });

            // Add new gallery button
            $('.add-gallery').on('click', function() {
                BestPortfolioAdmin.addNewGallery();
            });

            // Add new item button
            $('.add-gallery-item').on('click', function() {
                BestPortfolioAdmin.addNewGalleryItem();
            });

            // Delete handlers
            $(document).on('click', '.delete-gallery', function() {
                if (confirm('Are you sure you want to delete this gallery?')) {
                    $(this).closest('.gallery-item').remove();
                }
            });

            $(document).on('click', '.delete-gallery-item', function() {
                if (confirm('Are you sure you want to delete this item?')) {
                    $(this).closest('.gallery-item-card').remove();
                }
            });
        },

        /**
         * Update gallery sort order via AJAX
         * @param {Array} galleryOrder Array of gallery IDs in new order
         */
        updateGalleryOrder: function(galleryOrder) {
            const container = $('.best-portfolio-galleries');
            container.addClass('best-portfolio-loading');

            $.ajax({
                url: bestPortfolioAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'best_portfolio_update_gallery_order',
                    nonce: bestPortfolioAdmin.nonce,
                    gallery_order: galleryOrder
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        alert(bestPortfolioAdmin.messages.sorting_success);
                    } else {
                        // Show error message
                        alert(bestPortfolioAdmin.messages.sorting_error);
                        // Revert the sort
                        $('.best-portfolio-galleries .galleries-list').sortable('cancel');
                    }
                },
                error: function() {
                    // Show error message
                    alert(bestPortfolioAdmin.messages.sorting_error);
                    // Revert the sort
                    $('.best-portfolio-galleries .galleries-list').sortable('cancel');
                },
                complete: function() {
                    container.removeClass('best-portfolio-loading');
                }
            });
        },

        /**
         * Update gallery item sort order via AJAX
         * @param {Array} itemOrder Array of item IDs in new order
         */
        updateItemOrder: function(itemOrder) {
            const container = $('.gallery-items-grid');
            container.addClass('best-portfolio-loading');

            $.ajax({
                url: bestPortfolioAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'best_portfolio_update_gallery_item_order',
                    nonce: bestPortfolioAdmin.nonce,
                    item_order: itemOrder
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        alert(bestPortfolioAdmin.messages.sorting_success);
                    } else {
                        // Show error message
                        alert(bestPortfolioAdmin.messages.sorting_error);
                        // Revert the sort
                        $('.gallery-items-grid').sortable('cancel');
                    }
                },
                error: function() {
                    // Show error message
                    alert(bestPortfolioAdmin.messages.sorting_error);
                    // Revert the sort
                    $('.gallery-items-grid').sortable('cancel');
                },
                complete: function() {
                    container.removeClass('best-portfolio-loading');
                }
            });
        },

        /**
         * Add a new gallery to the portfolio
         */
        addNewGallery: function() {
            // Template for new gallery
            const template = wp.template('gallery-item');
            const newGallery = {
                id: 'temp-' + Date.now(),
                title: 'New Gallery'
            };

            // Add new gallery to the list
            $('.galleries-list').append(template(newGallery));
        },

        /**
         * Add a new item to the gallery
         */
        addNewGalleryItem: function() {
            // Template for new item
            const template = wp.template('gallery-item-card');
            const newItem = {
                id: 'temp-' + Date.now(),
                title: 'New Item',
                type: 'image'
            };

            // Add new item to the grid
            $('.gallery-items-grid').append(template(newItem));
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        BestPortfolioAdmin.init();
    });

})(jQuery);
