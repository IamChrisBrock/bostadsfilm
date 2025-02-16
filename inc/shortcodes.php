<?php
/**
 * Register shortcodes for the theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Project Gallery shortcode
 */
function project_gallery_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
        'columns' => 3
    ), $atts);

    ob_start();

    // Get project
    $project = get_post($atts['id']);
    if (!$project || $project->post_type !== 'project_gallery') {
        return '';
    }

    // Get media items
    $media_ids = get_post_meta($project->ID, '_project_gallery_media', true);
    if (!$media_ids) {
        return '';
    }

    $media_ids = explode(',', $media_ids);



    // Display gallery
    echo '<div class="portfolio-grid columns-' . esc_attr($atts['columns']) . '">';
    foreach ($media_ids as $media_id) {
        $type = wp_attachment_is('video', $media_id) ? 'video' : 'image';
        $url = wp_get_attachment_url($media_id);
        $thumbnail = $type === 'video' ? 
            wp_get_attachment_image_src(get_post_thumbnail_id($media_id), 'large') : 
            wp_get_attachment_image_src($media_id, 'large');
        
        // Get media tags
        $media_tags = wp_get_post_terms($project->ID, 'project_tags', array('fields' => 'slugs'));
        $tags_class = !empty($media_tags) ? implode(' ', $media_tags) : '';
        
        ?>
        <div class="portfolio-item <?php echo esc_attr($tags_class); ?>" data-type="<?php echo esc_attr($type); ?>">
            <a href="<?php echo esc_url($url); ?>" class="glightbox" data-gallery="portfolio-<?php echo esc_attr($project->ID); ?>">
                <img src="<?php echo esc_url($thumbnail[0]); ?>" alt="<?php echo esc_attr(get_the_title($media_id)); ?>">
                <?php if ($type === 'video'): ?>
                    <span class="video-overlay">
                        <i class="fas fa-play"></i>
                    </span>
                <?php endif; ?>
            </a>
        </div>
        <?php
    }
    echo '</div>';

    return ob_get_clean();
}
add_shortcode('gallery_project', 'project_gallery_shortcode');
