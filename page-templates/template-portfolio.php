<?php
/**
 * Template Name: Portfolio Page
 * 
 * This template can be edited with Elementor and displays the project galleries
 */

get_header();
?>

<?php while (have_posts()) : the_post(); ?>

<div class="header-wrapper portfolio-header-wrapper">
    <div class="container">
        <h1><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
            <div class="portfolio-description">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container">
<?php 
// This is required for Elementor
the_content();

endwhile; ?>
</div>
<?php

// Query for project galleries
$args = array(
    'post_type' => 'project_gallery',
    'posts_per_page' => 12,
    'orderby' => 'date',
    'order' => 'DESC'
);

$project_query = new WP_Query($args);
?>
<div id="portfolio-content" class="portfolio-archive">
    <div class="container" style="padding-left:0px;padding-right:0px;">
        <div class="portfolio-header">
            <div class="view-switch">
                <label class="switch">
                    <input type="checkbox" id="view-mode-toggle">
                    <span class="slider round"></span>
                </label>
                <span class="switch-label">Projects</span>
            </div>
            
            <div class="media-tags">
                <?php
                // Get all project posts
                $all_projects_query = new WP_Query(array(
                    'post_type' => 'project_gallery',
                    'posts_per_page' => -1
                ));

                $all_tags = array();
                
                if ($all_projects_query->have_posts()) :
                    while ($all_projects_query->have_posts()) : $all_projects_query->the_post();
                        // Get media IDs
                        $media_ids = get_post_meta(get_the_ID(), '_project_gallery_media', true);
                        if ($media_ids) {
                            $media_ids = explode(',', $media_ids);
                            foreach ($media_ids as $media_id) {
                                // Get all taxonomies for this attachment
                                $taxonomies = get_object_taxonomies('attachment');
                                error_log('Available taxonomies for attachment: ' . print_r($taxonomies, true));
                                
                                // Get terms for each taxonomy
                                foreach ($taxonomies as $taxonomy) {
                                    $terms = wp_get_object_terms($media_id, $taxonomy);
                                    if (!empty($terms) && !is_wp_error($terms)) {
                                        foreach ($terms as $term) {
                                            $all_tags[$term->slug] = $term->name;
                                        }
                                    }
                                }
                            }
                        }
                    endwhile;
                    wp_reset_postdata();
                endif;
                
                // Debug output
                error_log('Found tags: ' . print_r($all_tags, true));
                
                if (!empty($all_tags)) :
                    foreach ($all_tags as $slug => $name) : ?>
                        <button class="tag-filter" data-tag="<?php echo esc_attr($slug); ?>">
                            <?php echo esc_html($name); ?>
                        </button>
                    <?php endforeach;
                endif;
                ?>
            </div>
        </div>

        <?php
        // Check if this is an AJAX request
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        error_log('AJAX request: ' . ($is_ajax ? 'yes' : 'no'));

        if ($project_query->have_posts()) :
            // Check if we're in media view
            $view_mode = isset($_GET['view']) && $_GET['view'] === 'media' ? 'media' : 'projects';
            error_log('View mode: ' . $view_mode . ', GET[view]: ' . (isset($_GET['view']) ? $_GET['view'] : 'not set'));
            
            // Get active tags from URL
            $active_tags = isset($_GET['tags']) ? explode(',', $_GET['tags']) : array();
            
            if ($view_mode === 'media') {
                // Get all project posts without pagination
                $all_projects_query = new WP_Query(array(
                    'post_type' => 'project_gallery',
                    'posts_per_page' => -1,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));

                // Collect media IDs that match the active tags
                $all_media_ids = array();
                $processed_media_ids = array(); // Track processed media to avoid duplicates
                
                if ($all_projects_query->have_posts()) {
                    while ($all_projects_query->have_posts()) : $all_projects_query->the_post();
                        $media_ids = get_post_meta(get_the_ID(), '_project_gallery_media', true);
                        if ($media_ids) {
                            $media_ids = explode(',', $media_ids);
                            
                            // Process each media item
                            foreach ($media_ids as $media_id) {
                                // Skip if we've already processed this media item
                                if (in_array($media_id, $processed_media_ids)) {
                                    continue;
                                }
                                
                                $should_include = true;
                                
                                // If there are active tags, check if media has all of them
                                if (!empty($active_tags)) {
                                    // Get all terms for this media item
                                    $all_terms = array();
                                    $taxonomies = get_object_taxonomies('attachment');
                                    
                                    foreach ($taxonomies as $taxonomy) {
                                        $terms = wp_get_object_terms($media_id, $taxonomy, array('fields' => 'slugs'));
                                        if (!is_wp_error($terms)) {
                                            $all_terms = array_merge($all_terms, $terms);
                                        }
                                    }
                                    
                                    // Check if media has all active tags
                                    foreach ($active_tags as $tag) {
                                        if (!in_array($tag, $all_terms)) {
                                            $should_include = false;
                                            break;
                                        }
                                    }
                                }
                                
                                // Include media if it matches all criteria
                                if ($should_include) {
                                    $all_media_ids[] = $media_id;
                                    $processed_media_ids[] = $media_id; // Mark as processed
                                }
                            }
                        }
                    endwhile;
                    wp_reset_postdata();
                }
                
                // Remove any remaining duplicates and reindex array
                $all_media_ids = array_values(array_unique($all_media_ids));
                
                // Debug output
                error_log('Active tags: ' . print_r($active_tags, true));
                error_log('Filtered media IDs: ' . print_r($all_media_ids, true));
                
                // Media view markup
                ?>
                <div id="portfolio-items">
                    <?php
                    get_template_part('template-parts/media-grid', null, array(
                        'media_ids' => $all_media_ids,
                        'display_mode' => 'full',
                        'gallery_name' => 'portfolio-media'
                    ));
                    ?>
                </div>
                <?php
                
            } else {
                // Projects view markup
                ?>
                <div class="portfolio-projects-grid" id="portfolio-items">
                    <?php
                    while ($project_query->have_posts()) : $project_query->the_post();
                        // Get the first media item as preview
                        $media_ids = get_post_meta(get_the_ID(), '_project_gallery_media', true);
                        $preview_image = '';
                        
                        if ($media_ids) {
                            $media_ids = explode(',', $media_ids);
                            $first_media = $media_ids[0];
                            
                            // Check if it's a video
                            if (wp_attachment_is('video', $first_media)) {
                                // If video, try to get its thumbnail
                                $preview_image = get_post_thumbnail_id($first_media);
                                if (!$preview_image) {
                                    // If no video thumbnail, use post thumbnail
                                    $preview_image = get_post_thumbnail_id();
                                }
                            } else {
                                // If image, use it directly
                                $preview_image = $first_media;
                            }
                        } else {
                            // Fallback to post thumbnail
                            $preview_image = get_post_thumbnail_id();
                        }
                        
                        // Get image URL
                        $image_url = wp_get_attachment_image_src($preview_image, 'large');
                        $image_url = $image_url ? $image_url[0] : '';
                        ?>
                        <div class="article-wrapper">
                            <article class="portfolio-item">
                                <a href="<?php the_permalink(); ?>" class="portfolio-item-link">
                                    <div class="portfolio-item-image">
                                        <?php if ($image_url) : ?>
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
                                        <?php endif; ?>
                                    </div>
                                    <div class="portfolio-item-overlay">
                                        <div class="portfolio-item-content">
                                            <h2><?php the_title(); ?></h2>
                                            <?php if (has_excerpt()) : ?>
                                                <div class="portfolio-item-excerpt">
                                                    <?php the_excerpt(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </article>
                            <div class="portfolio-projects-description">
                                <h2><?php the_title(); ?></h2>
                                <span><?php the_excerpt(); ?></span>
                            </div>
                        </div>
                    <?php
                    endwhile;
                    
                    // Pagination only in projects view
                    echo '<div class="pagination">';
                    echo paginate_links(array(
                        'total' => $project_query->max_num_pages,
                        'prev_text' => __('Previous', 'filmestate'),
                        'next_text' => __('Next', 'filmestate'),
                    ));
                    echo '</div>';
                    ?>
                </div>
                <?php
            }
            
            wp_reset_postdata();
            
        else :
                ?>
                <p><?php _e('No projects found.', 'filmestate'); ?></p>
                <?php
            endif;
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
?>
