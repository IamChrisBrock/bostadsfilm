<?php
/**
 * Template part for displaying gallery items
 */
?>

<div class="col-md-6 col-lg-4">
    <article id="post-<?php the_ID(); ?>" <?php post_class('archive-gallery-item'); ?>>
        <a href="<?php the_permalink(); ?>" class="archive-gallery-link">
            <?php if (has_post_thumbnail()) : ?>
                <div class="archive-gallery-thumbnail">
                    <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                    <?php
                    // Display tags
                    $tags = get_the_terms(get_the_ID(), 'project_tags');
                    if ($tags && !is_wp_error($tags)) : ?>
                        <div class="gallery-tags">
                            <?php foreach ($tags as $tag) : ?>
                                <span class="gallery-tag"><?php echo esc_html($tag->name); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
