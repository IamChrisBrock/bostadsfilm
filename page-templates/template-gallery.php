<?php
/**
 * Template Name: Gallery Page
 * Description: A template for displaying project galleries in a customizable grid layout
 */

get_header();

// Get display options (can be customized via ACF or page meta)
$posts_per_page = get_post_meta(get_the_ID(), '_gallery_posts_per_page', true) ?: -1;
$columns = get_post_meta(get_the_ID(), '_gallery_columns', true) ?: 3;
$order = get_post_meta(get_the_ID(), '_gallery_order', true) ?: 'DESC';
$orderby = get_post_meta(get_the_ID(), '_gallery_orderby', true) ?: 'date';

// Query for project galleries
$gallery_query = new WP_Query(array(
    'post_type' => 'project_gallery',
    'posts_per_page' => $posts_per_page,
    'orderby' => $orderby,
    'order' => $order
));

while (have_posts()) : the_post(); ?>

<header class="full-window-header single-gallery-header">
    <div class="header-content">
        <h1 class="single-gallery-title"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
            <div class="single-gallery-description">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
        <?php the_content(); ?>
    </div>
    <?php if (has_post_thumbnail()) : ?>
        <div class="header-background">
            <?php the_post_thumbnail('full'); ?>
            <div class="overlay"></div>
        </div>
    <?php endif; ?>
</header>

<?php endwhile; ?>

<div class="gallery-page">
    <?php 
    // Add filter UI
    if (function_exists('add_gallery_filter_ui')) {
        add_gallery_filter_ui();
    }
    ?>
    
    <div class="gallery-page-content">
        <div class="container">
            <?php if ($gallery_query->have_posts()) : ?>
                <div class="row gallery-grid columns-<?php echo esc_attr($columns); ?>">
                    <?php while ($gallery_query->have_posts()) : $gallery_query->the_post(); 
                        // Get the first media item as preview
                        $media_ids = get_post_meta(get_the_ID(), '_project_gallery_media', true);
                        $media_ids = $media_ids ? explode(',', $media_ids) : array();
                        $preview_image = '';
                        
                        if (!empty($media_ids)) {
                            $first_media = $media_ids[0];
                            $type = wp_attachment_is('video', $first_media) ? 'video' : 'image';
                            
                            if ($type === 'video') {
                                $preview_image = get_post_thumbnail_id($first_media) ? 
                                    wp_get_attachment_image_src(get_post_thumbnail_id($first_media), 'large') :
                                    null;
                            } else {
                                $preview_image = wp_get_attachment_image_src($first_media, 'large');
                            }
                        }
                    ?>
                        <div class="col-md-6 col-lg-<?php echo 12/$columns; ?>">
                            <article id="post-<?php the_ID(); ?>" <?php post_class('gallery-item'); ?>>
                                <a href="<?php the_permalink(); ?>" class="gallery-item-link">
                                    <?php if ($preview_image) : ?>
                                        <div class="gallery-item-thumbnail">
                                            <img src="<?php echo esc_url($preview_image[0]); ?>" 
                                                 alt="<?php echo esc_attr(get_the_title()); ?>"
                                                 class="img-fluid">
                                            <?php if (!empty($media_ids)) : ?>
                                                <span class="media-count">
                                                    <i class="fas fa-images"></i>
                                                    <?php echo count($media_ids); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif (has_post_thumbnail()) : ?>
                                        <div class="gallery-item-thumbnail">
                                            <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                                            <?php if (!empty($media_ids)) : ?>
                                                <span class="media-count">
                                                    <i class="fas fa-images"></i>
                                                    <?php echo count($media_ids); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="gallery-item-content">
                                        <h2 class="gallery-item-title"><?php the_title(); ?></h2>
                                        <?php if (has_excerpt()) : ?>
                                            <div class="gallery-item-excerpt">
                                                <?php the_excerpt(); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <?php if ($posts_per_page != -1) : ?>
                    <div class="gallery-pagination">
                        <?php 
                        echo paginate_links(array(
                            'total' => $gallery_query->max_num_pages,
                            'current' => max(1, get_query_var('paged')),
                            'prev_text' => __('Previous', 'filmestate'),
                            'next_text' => __('Next', 'filmestate'),
                        ));
                        ?>
                    </div>
                <?php endif; ?>
                
            <?php else : ?>
                <p class="no-galleries"><?php _e('No galleries found.', 'filmestate'); ?></p>
            <?php endif; 
            wp_reset_postdata(); 
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
