<?php
/**
 * Template for displaying project gallery archive
 */

get_header();
?>

<div class="archive-galleries">
    <?php 
    // Add filter UI
    if (function_exists('add_gallery_filter_ui')) {
        add_gallery_filter_ui();
    }
    ?>
    
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="row">
                <?php while (have_posts()) : the_post(); ?>
                    <div class="col-md-6 col-lg-4">
                        <article id="post-<?php the_ID(); ?>" <?php post_class('archive-gallery-item'); ?>>
                            <a href="<?php the_permalink(); ?>" class="archive-gallery-link">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="archive-gallery-thumbnail">
                                        <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="archive-gallery-content">
                                    <h2 class="archive-gallery-title"><?php the_title(); ?></h2>
                                    <?php if (has_excerpt()) : ?>
                                        <div class="archive-gallery-excerpt">
                                            <?php the_excerpt(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <?php the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('Previous', 'filmestate'),
                'next_text' => __('Next', 'filmestate'),
            )); ?>
            
        <?php else : ?>
            <p><?php _e('No project galleries found.', 'filmestate'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
