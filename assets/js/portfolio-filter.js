jQuery(document).ready(function($) {
    // Initialize Select2 for tag filter
    $('#tag-filter').select2({
        placeholder: 'Filter by tags...',
        allowClear: true
    });

    // Cache DOM elements
    const $grid = $('#portfolio-items');
    const $search = $('#portfolio-search');
    const $tagFilter = $('#tag-filter');
    const $sortFilter = $('#sort-filter');
    const $viewModeSwitch = $('#view-mode-switch');
    let currentMode = 'projects'; // or 'content'

    // Initialize loading state
    let isLoading = false;
    const $loadingIndicator = $('<div class="loading-indicator">Loading...</div>').insertAfter($grid);
    $loadingIndicator.hide();

    // Debounce function for search
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // Function to fetch filtered results
    async function fetchFilteredResults() {
        if (isLoading) return;
        isLoading = true;
        $loadingIndicator.show();
        $grid.css('opacity', '0.5');

        const searchQuery = $search.val();
        const selectedTags = $tagFilter.val() || [];
        const sortBy = $sortFilter.val();
        const viewMode = $viewModeSwitch.is(':checked') ? 'content' : 'projects';

        try {
            const response = await $.ajax({
                url: portfolio_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'filter_portfolio',
                    nonce: portfolio_ajax.nonce,
                    search: searchQuery,
                    tags: selectedTags,
                    sort: sortBy,
                    mode: viewMode
                }
            });

            if (response.success) {
                $grid.html(response.data.html);
                
                // Update URL with filters
                const params = new URLSearchParams();
                if (searchQuery) params.set('search', searchQuery);
                if (selectedTags.length) params.set('tags', selectedTags.join(','));
                if (sortBy) params.set('sort', sortBy);
                if (viewMode !== 'projects') params.set('mode', viewMode);
                
                const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
                window.history.pushState({}, '', newUrl);
            }
        } catch (error) {
            console.error('Error fetching results:', error);
        } finally {
            isLoading = false;
            $loadingIndicator.hide();
            $grid.css('opacity', '1');
        }
    }

    // Event listeners
    $search.on('input', debounce(fetchFilteredResults, 500));
    $tagFilter.on('change', fetchFilteredResults);
    $sortFilter.on('change', fetchFilteredResults);
    $viewModeSwitch.on('change', function() {
        const isContent = $(this).is(':checked');
        $(this).siblings('.switch-label').text(isContent ? 'Content' : 'Projects');
        fetchFilteredResults();
    });

    // Initialize filters from URL params
    const params = new URLSearchParams(window.location.search);
    if (params.has('search')) $search.val(params.get('search'));
    if (params.has('tags')) $tagFilter.val(params.get('tags').split(',')).trigger('change');
    if (params.has('sort')) $sortFilter.val(params.get('sort'));
    if (params.has('mode')) {
        const mode = params.get('mode');
        $viewModeSwitch.prop('checked', mode === 'content').trigger('change');
    }

    // Initial load
    fetchFilteredResults();
});
