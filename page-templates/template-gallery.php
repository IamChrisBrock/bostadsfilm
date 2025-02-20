<?php
/**
 * Template Name: Gallery Page
 * Description: A template for displaying project galleries in a customizable grid layout
 */

use Inkperial\Components\Gallery_Item;

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
                        $gallery_item = new Gallery_Item(get_post());
                        $gallery_item->render();
                    endwhile; 
               ?>
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
