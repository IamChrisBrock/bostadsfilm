<?php
/**
 * Template part for displaying projects grid
 * 
 * @param WP_Query $query Optional custom query, will use main query if not provided
 */

$portfolio_query = isset($args['query']) ? $args['query'] : $GLOBALS['wp_query'];

if ($portfolio_query->have_posts()) :
    while ($portfolio_query->have_posts()) : $portfolio_query->the_post();
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
    wp_reset_postdata();
else :
    echo '<p>' . esc_html__('No projects found.', 'filmestate') . '</p>';
endif;
?>
