<?php
/**
 * Template Name: Gallery Page
 * Description: A template for displaying project galleries in a customizable grid layout
 */

get_header();

// Get the page content first
while (have_posts()) : the_post(); ?>

<header class="full-window-header portfolio-gallery-header">
    <div class="header-content">
        <h1 class="single-gallery-title"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
            <div class="single-gallery-description">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
        <div class="portfolio-gallery-text">
        <?php the_content(); ?>
        </div>
    </div>
    <?php if (has_post_thumbnail()) : ?>
        <div class="header-background">
            <?php the_post_thumbnail('full'); ?>
            <div class="overlay"></div>
        </div>
    <?php endif; ?>
</header>

<?php endwhile; 

// Query for project galleries
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$orderby = get_post_meta(get_the_ID(), '_gallery_orderby', true) ?: 'date';
$order = get_post_meta(get_the_ID(), '_gallery_order', true) ?: 'DESC';

$gallery_query = new WP_Query(array(
    'post_type' => 'project_gallery',
    'posts_per_page' => -1,
    'orderby' => $orderby,
    'order' => $order,
    'paged' => $paged
));

// Setup the query for the gallery grid
global $wp_query;
$main_query = $wp_query;
$wp_query = $gallery_query;

// Include the shared gallery grid template
get_template_part('template-parts/gallery-grid');

// Restore the main query
$wp_query = $main_query;
wp_reset_postdata();

 get_footer(); ?>
