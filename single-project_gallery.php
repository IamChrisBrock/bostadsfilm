<?php
/**
 * Template for displaying single project gallery
 */

// Debug output
echo '<!-- Template: single-project_gallery.php -->'; // This will be visible in page source

get_header();
?>

<div class="header-wrapper project-header-wrapper">
    <div class="container">
        <h1><?php the_title(); ?></h1>
        <?php if (has_excerpt()): ?>
            <div class="project-description">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="project-content" class="single-project-gallery">
    <div class="container">
        

        <?php
        // Get display mode
        $display_mode = get_post_meta(get_the_ID(), '_project_gallery_display_mode', true) ?: 'square';
        
        // Get media items
        $media_ids = get_post_meta(get_the_ID(), '_project_gallery_media', true);
        if ($media_ids) {
            ?>
            <div class="portfolio-grid" data-display-mode="<?php echo esc_attr($display_mode); ?>">
                <?php
                $media_ids = explode(',', $media_ids);
                foreach ($media_ids as $media_id) {
                    // Get media type and URL
                    $type = wp_attachment_is('video', $media_id) ? 'video' : 'image';
                    $url = wp_get_attachment_url($media_id);
                    $thumbnail = $type === 'video' ? 
                        wp_get_attachment_image_src(get_post_thumbnail_id($media_id), 'large') : 
                        wp_get_attachment_image_src($media_id, 'large');
                    
                    ?>
                    <div class="portfolio-item" data-type="<?php echo esc_attr($type); ?>">
                        <a href="<?php echo esc_url($url); ?>" class="glightbox" data-gallery="single-project">
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
                ?>
            </div>
            <?php
        }
        
        // Display content if any
        if (get_the_content()) {
            ?>
            <div class="project-content">
                <?php the_content(); ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php
get_footer();
?>
