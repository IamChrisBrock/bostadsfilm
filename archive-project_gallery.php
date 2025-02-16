<?php
/**
 * Template for displaying project gallery archive
 */

get_header();
?>

<div class="header-wrapper portfolio-header-wrapper">
    <div class="container">
        <h1><?php _e('Our Portfolio', 'filmestate'); ?></h1>
        <div class="portfolio-description">
            <?php echo term_description(); ?>
        </div>
    </div>
</div>

<div class="portfolio-archive">
    <div class="container">
        <div class="portfolio-grid" data-display-mode="grid">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
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
                    
                <?php
                endwhile;
                
                // Pagination
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('Previous', 'filmestate'),
                    'next_text' => __('Next', 'filmestate'),
                ));
                
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
