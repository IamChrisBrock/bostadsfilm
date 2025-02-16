<?php
/**
 * Template Name: Portfolio Gallery
 */

get_header();
?>

<div class="portfolio-gallery">
    <div class="container">
        <?php


        <?php
        // Get all project galleries
        $projects = new WP_Query(array(
            'post_type' => 'project_gallery',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ));

        if ($projects->have_posts()) :
            while ($projects->have_posts()) : $projects->the_post();
                $display_mode = get_post_meta(get_the_ID(), '_project_gallery_display_mode', true) ?: 'square';
                ?>
                <div class="project-section">
                    <h2><?php the_title(); ?></h2>
                    <div class="portfolio-grid" data-display-mode="<?php echo esc_attr($display_mode); ?>">
                        <?php
                        // Get media items
                        $media_ids = get_post_meta(get_the_ID(), '_project_gallery_media', true);
                        if ($media_ids) {
                            $media_ids = explode(',', $media_ids);
                            foreach ($media_ids as $media_id) {
                                // Get media tags
                                $media_tags = wp_get_post_terms(get_the_ID(), 'project_tags', array('fields' => 'slugs'));
                                $tags_class = !empty($media_tags) ? implode(' ', $media_tags) : '';
                                
                                // Get media type and URL
                                $type = wp_attachment_is('video', $media_id) ? 'video' : 'image';
                                $url = wp_get_attachment_url($media_id);
                                $thumbnail = $type === 'video' ? 
                                    wp_get_attachment_image_src(get_post_thumbnail_id($media_id), 'large') : 
                                    wp_get_attachment_image_src($media_id, 'large');
                                
                                ?>
                                <div class="portfolio-item <?php echo esc_attr($tags_class); ?>" data-type="<?php echo esc_attr($type); ?>">
                                    <a href="<?php echo esc_url($url); ?>" class="glightbox" data-gallery="portfolio-<?php echo get_the_ID(); ?>">
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
                        }
                        ?>
                    </div>
                </div>
            <?php
            endwhile;
            wp_reset_postdata();
        endif;
            }
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
