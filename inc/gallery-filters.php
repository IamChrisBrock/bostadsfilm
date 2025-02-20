<?php
/**
 * Gallery Filtering System
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Modify the main query for gallery filtering
 */
function modify_gallery_query($query) {
    // Only modify gallery queries
    if (!is_admin() && 
        ($query->is_post_type_archive('project_gallery') || 
         $query->is_tax('project_tags') ||
         (is_page_template('page-templates/template-gallery.php') && $query->is_main_query()))) {
        
        // Handle tag filtering from URL parameters
        $selected_tags = isset($_GET['gallery_tags']) ? explode(',', sanitize_text_field($_GET['gallery_tags'])) : array();
        
        if (!empty($selected_tags)) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'project_tags',
                    'field'    => 'slug',
                    'terms'    => $selected_tags,
                    'operator' => 'AND', // Require all selected tags
                )
            ));
        }

        // Apply sorting
        $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
        $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';
        
        $query->set('orderby', $orderby);
        $query->set('order', $order);
    }
}
add_action('pre_get_posts', 'modify_gallery_query');

/**
 * AJAX handler for gallery filtering
 */
use Inkperial\Components\Gallery_Item;

function filter_galleries() {
    check_ajax_referer('gallery_filter', 'nonce');

    $args = array(
        'post_type' => 'project_gallery',
        'posts_per_page' => get_option('posts_per_page'),
        'paged' => isset($_POST['page']) ? absint($_POST['page']) : 1,
    );

    // Add tag filtering
    if (!empty($_POST['tags'])) {
        $tags = array_map('sanitize_text_field', explode(',', $_POST['tags']));
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'project_tags',
                'field'    => 'slug',
                'terms'    => $tags,
                'operator' => 'AND',
            )
        );
    }

    // Add sorting
    if (!empty($_POST['orderby'])) {
        $args['orderby'] = sanitize_text_field($_POST['orderby']);
        $args['order'] = !empty($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
    }

    $query = new WP_Query($args);
    
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $gallery_item = new Gallery_Item(get_post());
            $gallery_item->render();
        }
        wp_reset_postdata();
    } else {
        echo '<p class="no-results">' . __('No galleries found matching your criteria.', 'filmestate') . '</p>';
    }
    $html = ob_get_clean();
    
    wp_send_json_success(array(
        'html' => $html,
        'max_pages' => $query->max_num_pages,
    ));
}
add_action('wp_ajax_filter_galleries', 'filter_galleries');
add_action('wp_ajax_nopriv_filter_galleries', 'filter_galleries');

/**
 * Get all available gallery tags
 */
function get_gallery_tags() {
    global $wpdb;
    
    // Get all terms that are directly assigned to project_gallery posts
    $query = $wpdb->prepare(
        "SELECT DISTINCT t.* FROM {$wpdb->terms} t
        INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
        INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
        INNER JOIN {$wpdb->posts} p ON tr.object_id = p.ID
        WHERE tt.taxonomy = %s
        AND p.post_type = %s
        AND p.post_status = 'publish'",
        'project_tags',
        'project_gallery'
    );
    
    $terms = $wpdb->get_results($query);
    
    if (!$terms || is_wp_error($terms)) {
        $tags = array();
    } else {
        $tags = array_map(function($term) {
            // Add count property to match get_terms output
            $term->count = get_term($term->term_id, 'project_tags')->count;
            return $term;
        }, $terms);
    }

    $tag_list = array();
    foreach ($tags as $tag) {
        $tag_list[] = array(
            'slug' => $tag->slug,
            'name' => $tag->name,
            'count' => $tag->count,
        );
    }

    return $tag_list;
}

/**
 * Add filter UI to gallery pages
 */
function add_gallery_filter_ui() {
    if (is_post_type_archive('project_gallery') || 
        is_tax('project_tags') || 
        is_page_template('page-templates/template-gallery.php')) {
        
        $tags = get_gallery_tags();
        $selected_tags = isset($_GET['gallery_tags']) ? explode(',', sanitize_text_field($_GET['gallery_tags'])) : array();
        
        ?>
        <div class="gallery-filters">
            <div class="container">
                <div class="filter-wrapper">
                    <div class="filter-tags">
                        <h3><?php _e('Filter by Tags', 'filmestate'); ?></h3>
                        <div class="tag-list">
                            <?php foreach ($tags as $tag) : ?>
                                <label class="tag-item">
                                    <input type="checkbox" 
                                           name="gallery_tags[]" 
                                           value="<?php echo esc_attr($tag['slug']); ?>"
                                           <?php checked(in_array($tag['slug'], $selected_tags)); ?>>
                                    <span class="tag-name"><?php echo esc_html($tag['name']); ?></span>
                                    <span class="tag-count">(<?php echo esc_html($tag['count']); ?>)</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="filter-sort">
                        <select name="orderby" class="orderby-select">
                            <option value="date" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'date'); ?>>
                                <?php _e('Sort by Date', 'filmestate'); ?>
                            </option>
                            <option value="title" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'title'); ?>>
                                <?php _e('Sort by Title', 'filmestate'); ?>
                            </option>
                        </select>
                        
                        <select name="order" class="order-select">
                            <option value="DESC" <?php selected(isset($_GET['order']) ? $_GET['order'] : '', 'DESC'); ?>>
                                <?php _e('Descending', 'filmestate'); ?>
                            </option>
                            <option value="ASC" <?php selected(isset($_GET['order']) ? $_GET['order'] : '', 'ASC'); ?>>
                                <?php _e('Ascending', 'filmestate'); ?>
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
// Filters are now added directly in templates
