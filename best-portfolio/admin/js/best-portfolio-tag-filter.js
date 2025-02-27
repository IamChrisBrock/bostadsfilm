/**
 * Best Portfolio Tag Filter
 *
 * This file handles all tag filtering functionality for both galleries and items.
 * Features:
 * - Real-time tag filtering
 * - AND/OR logic support
 * - Animated transitions
 * - Error handling
 * - Loading states
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/admin/js
 */

(function($) {
    'use strict';

    // Main tag filter class
    const BestPortfolioTagFilter = {
        /**
         * Initialize the tag filter functionality
         */
        init: function() {
            this.initializeVariables();
            this.bindEvents();
            this.initializeTagCloud();
        },

        /**
         * Initialize class variables
         */
        initializeVariables: function() {
            this.galleryFilter = $('.best-portfolio-gallery-filter');
            this.itemFilter = $('.best-portfolio-item-filter');
            this.filterContainers = $('.best-portfolio-filter-container');
            this.activeFilters = {
                gallery: [],
                item: []
            };
            this.filterRelations = {
                gallery: 'OR',
                item: 'OR'
            };
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            // Gallery filter events
            this.galleryFilter.on('change', 'input[type="checkbox"]', (e) => this.handleTagChange(e, 'gallery'));
            this.galleryFilter.on('change', 'input[type="radio"]', (e) => this.handleRelationChange(e, 'gallery'));
            this.galleryFilter.on('click', '.apply-filter', () => this.applyFilter('gallery'));
            this.galleryFilter.on('click', '.clear-filter', () => this.clearFilter('gallery'));

            // Item filter events
            this.itemFilter.on('change', 'input[type="checkbox"]', (e) => this.handleTagChange(e, 'item'));
            this.itemFilter.on('change', 'input[type="radio"]', (e) => this.handleRelationChange(e, 'item'));
            this.itemFilter.on('click', '.apply-filter', () => this.applyFilter('item'));
            this.itemFilter.on('click', '.clear-filter', () => this.clearFilter('item'));

            // Handle tag clicks in tag cloud
            $('.best-portfolio-tag-cloud').on('click', '.tag-cloud-link', this.handleTagCloudClick.bind(this));
        },

        /**
         * Initialize tag cloud functionality
         */
        initializeTagCloud: function() {
            $('.best-portfolio-tag-cloud .tag-cloud-link').each(function() {
                const count = $(this).data('count');
                const size = Math.max(12, Math.min(22, 12 + Math.log(count) * 2));
                $(this).css('font-size', size + 'px');
            });
        },

        /**
         * Handle tag checkbox change
         * @param {Event} e Event object
         * @param {string} type Filter type ('gallery' or 'item')
         */
        handleTagChange: function(e, type) {
            const checkbox = $(e.target);
            const tagId = checkbox.val();
            
            if (checkbox.is(':checked')) {
                this.activeFilters[type].push(tagId);
            } else {
                this.activeFilters[type] = this.activeFilters[type].filter(id => id !== tagId);
            }

            this.updateFilterUI(type);
        },

        /**
         * Handle relation radio button change
         * @param {Event} e Event object
         * @param {string} type Filter type ('gallery' or 'item')
         */
        handleRelationChange: function(e, type) {
            this.filterRelations[type] = $(e.target).val();
            this.updateFilterUI(type);
        },

        /**
         * Handle tag cloud link clicks
         * @param {Event} e Event object
         */
        handleTagCloudClick: function(e) {
            e.preventDefault();
            const link = $(e.target);
            const tagId = link.data('tag-id');
            const type = link.closest('.best-portfolio-tag-cloud').data('type');

            // Toggle tag selection
            const index = this.activeFilters[type].indexOf(tagId);
            if (index === -1) {
                this.activeFilters[type].push(tagId);
                link.addClass('selected');
            } else {
                this.activeFilters[type].splice(index, 1);
                link.removeClass('selected');
            }

            this.applyFilter(type);
        },

        /**
         * Apply the current filter
         * @param {string} type Filter type ('gallery' or 'item')
         */
        applyFilter: function(type) {
            const container = type === 'gallery' ? this.galleryFilter : this.itemFilter;
            container.addClass('filtering');

            $.ajax({
                url: bestPortfolioAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: `best_portfolio_filter_${type}s`,
                    nonce: bestPortfolioAdmin.nonce,
                    tags: this.activeFilters[type],
                    relation: this.filterRelations[type]
                },
                success: (response) => {
                    if (response.success) {
                        this.updateResults(type, response.data);
                    } else {
                        this.handleError(response.data.message);
                    }
                },
                error: (xhr, status, error) => {
                    this.handleError('An error occurred while filtering. Please try again.');
                },
                complete: () => {
                    container.removeClass('filtering');
                }
            });
        },

        /**
         * Clear all active filters
         * @param {string} type Filter type ('gallery' or 'item')
         */
        clearFilter: function(type) {
            const container = type === 'gallery' ? this.galleryFilter : this.itemFilter;
            
            // Reset checkboxes
            container.find('input[type="checkbox"]').prop('checked', false);
            
            // Reset radio to OR
            container.find('input[type="radio"][value="OR"]').prop('checked', true);
            
            // Clear active filters
            this.activeFilters[type] = [];
            this.filterRelations[type] = 'OR';
            
            // Update UI
            this.updateFilterUI(type);
            
            // Apply empty filter to show all items
            this.applyFilter(type);
        },

        /**
         * Update the filter UI
         * @param {string} type Filter type ('gallery' or 'item')
         */
        updateFilterUI: function(type) {
            const container = type === 'gallery' ? this.galleryFilter : this.itemFilter;
            
            // Update active tag indicators
            container.find('.tag-checkbox').each((_, el) => {
                const checkbox = $(el).find('input[type="checkbox"]');
                $(el).toggleClass('active', checkbox.is(':checked'));
            });

            // Update tag cloud
            $(`.best-portfolio-tag-cloud[data-type="${type}"] .tag-cloud-link`).each((_, el) => {
                const tagId = $(el).data('tag-id');
                $(el).toggleClass('selected', this.activeFilters[type].includes(tagId));
            });

            // Update filter count
            const count = this.activeFilters[type].length;
            container.find('.active-filter-count').text(count > 0 ? `(${count})` : '');
        },

        /**
         * Update the results display
         * @param {string} type Filter type ('gallery' or 'item')
         * @param {Object} data Response data containing filtered results
         */
        updateResults: function(type, data) {
            const container = $(`.best-portfolio-${type}-grid`);
            const items = type === 'gallery' ? data.galleries : data.items;
            
            // Clear existing items with fade
            container.fadeOut(200, () => {
                container.empty();

                // Add new items
                items.forEach(item => {
                    const itemHtml = this.createItemHtml(type, item);
                    container.append(itemHtml);
                });

                // Show container with fade
                container.fadeIn(200);

                // Initialize any necessary item functionality
                if (type === 'item') {
                    this.initializeItemFunctionality();
                }
            });

            // Update count
            $(`.${type}-count`).text(items.length);
        },

        /**
         * Create HTML for a single item
         * @param {string} type Item type ('gallery' or 'item')
         * @param {Object} item Item data
         * @returns {string} HTML string
         */
        createItemHtml: function(type, item) {
            if (type === 'gallery') {
                return `
                    <div class="gallery-card" data-gallery-id="${item.id}">
                        <div class="gallery-thumbnail">
                            ${item.thumbnail ? `<img src="${item.thumbnail}" alt="${item.title}">` : ''}
                        </div>
                        <div class="gallery-details">
                            <h3>${item.title}</h3>
                            <a href="${item.permalink}" class="button">View Gallery</a>
                        </div>
                    </div>`;
            } else {
                return `
                    <div class="gallery-item-card" data-item-id="${item.id}">
                        <div class="item-thumbnail">
                            ${item.thumbnail ? `<img src="${item.thumbnail}" alt="${item.title}">` : ''}
                        </div>
                        <div class="item-details">
                            <h4>${item.title}</h4>
                            <span class="item-type">${item.type}</span>
                        </div>
                    </div>`;
            }
        },

        /**
         * Initialize functionality specific to gallery items
         */
        initializeItemFunctionality: function() {
            // Add any special initialization for gallery items
            // For example, lightbox initialization
        },

        /**
         * Handle error messages
         * @param {string} message Error message to display
         */
        handleError: function(message) {
            // Show error message to user
            alert(message);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        BestPortfolioTagFilter.init();
    });

})(jQuery);
