<?php
/**
 * The template for displaying single portfolio items
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('best-portfolio-single'); ?>>
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </header>

                <div class="entry-content">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="best-portfolio-featured-image">
                            <?php the_post_thumbnail('full'); ?>
                        </div>
                    <?php endif; ?>

                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </main>
</div>

<?php get_footer(); ?>
