<?php
/**
 * The template for displaying portfolio archives
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="best-portfolio-container">
            <?php if (have_posts()) : ?>
                <header class="page-header">
                    <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
                    <?php
                    // Get portfolio categories
                    $categories = get_terms(array(
                        'taxonomy' => 'portfolio_category',
                        'hide_empty' => true,
                    ));
                    
                    if (!empty($categories) && !is_wp_error($categories)) : ?>
                        <div class="best-portfolio-filters">
                            <button class="filter-btn active" data-filter="all"><?php esc_html_e('All', 'best-portfolio'); ?></button>
                            <?php foreach ($categories as $category) : ?>
                                <button class="filter-btn" data-filter="<?php echo esc_attr($category->slug); ?>">
                                    <?php echo esc_html($category->name); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </header>

                <div class="best-portfolio-grid">
                    <?php while (have_posts()) : the_post(); 
                        // Get categories for this post
                        $post_categories = get_the_terms(get_the_ID(), 'portfolio_category');
                        $category_classes = '';
                        if ($post_categories && !is_wp_error($post_categories)) {
                            foreach($post_categories as $category) {
                                $category_classes .= ' category-' . $category->slug;
                            }
                        }
                    ?>
                        <div class="best-portfolio-item<?php echo esc_attr($category_classes); ?>" data-link="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php endif; ?>
                            <div class="best-portfolio-item-overlay">
                                <h2><?php the_title(); ?></h2>
                                <?php if ($post_categories && !is_wp_error($post_categories)) : ?>
                                    <div class="portfolio-categories">
                                        <?php foreach($post_categories as $category) : ?>
                                            <span class="category-tag"><?php echo esc_html($category->name); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php the_posts_navigation(array(
                    'prev_text' => __('← Older items', 'best-portfolio'),
                    'next_text' => __('Newer items →', 'best-portfolio'),
                )); ?>

            <?php else : ?>
                <p class="no-items-found"><?php esc_html_e('No portfolio items found.', 'best-portfolio'); ?></p>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
jQuery(document).ready(function($) {
    // Portfolio filtering
    $('.filter-btn').on('click', function() {
        var filter = $(this).data('filter');
        
        // Update active state of filter buttons
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        if (filter === 'all') {
            $('.best-portfolio-item').fadeIn(300);
        } else {
            $('.best-portfolio-item').hide();
            $('.best-portfolio-item.category-' + filter).fadeIn(300);
        }
    });

    // Portfolio item click handler
    $('.best-portfolio-item').on('click', function() {
        window.location.href = $(this).data('link');
    });
});
</script>

<?php get_footer(); ?>
