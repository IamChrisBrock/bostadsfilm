<?php
/**
 * Template part for displaying gallery grid
 * Used by both template-gallery.php and archive-project_gallery.php
 *
 * File Dependencies:
 * - inc/components/class-gallery-item.php (Individual gallery items)
 * - template-parts/components/gallery-item.php (Item template)
 * - inc/gallery-filters.php (Filter functionality)
 * - assets/js/gallery-filters.js (Filter interaction)
 * - assets/css/project-galleries.css (Grid styles)
 * 
 * Used by:
 * - template-gallery.php (Main gallery template)
 * - archive-project_gallery.php (Archive page)
 */

use Inkperial\Components\Gallery_Item;

// Get display options (can be customized via ACF or page meta)
$posts_per_page = get_post_meta(get_the_ID(), '_gallery_posts_per_page', true) ?: -1;
$columns = get_post_meta(get_the_ID(), '_gallery_columns', true) ?: 3;
$order = get_post_meta(get_the_ID(), '_gallery_order', true) ?: 'DESC';
$orderby = get_post_meta(get_the_ID(), '_gallery_orderby', true) ?: 'date';
?>

<div class="gallery-page">
    <?php 
    // Add filter UI
    if (function_exists('add_gallery_filter_ui')) {
        add_gallery_filter_ui();
    }
    ?>
    
    <div class="gallery-page-content">
            <?php if (have_posts()) : ?>
                <div class="gallery-grid columns-<?php echo esc_attr($columns); ?>">
                    <?php while (have_posts()) : the_post(); 
                        $gallery_item = new Gallery_Item(get_post());
                        $gallery_item->render();
                    endwhile; ?>
                </div>

                <?php the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('Previous', 'filmestate'),
                    'next_text' => __('Next', 'filmestate'),
                )); ?>
                
            <?php else : ?>
                <p class="no-galleries"><?php _e('No galleries found.', 'filmestate'); ?></p>
            <?php endif; ?>
    </div>
</div>
