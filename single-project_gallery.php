<?php
/**
 * Template for displaying single project gallery
 */

get_header();

while (have_posts()) : the_post();
    // Get gallery media
    $media_ids = get_post_meta(get_the_ID(), '_project_gallery_media', true);
    $media_ids = $media_ids ? explode(',', $media_ids) : array();
?>

<header class="full-window-header single-gallery-header">
    <div class="header-content">
        <h1 class="single-gallery-title"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
            <div class="single-gallery-description">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
        <?php
        // Display tags if they exist
        $tags = get_the_terms(get_the_ID(), 'project_tags');
        if ($tags && !is_wp_error($tags)) : ?>
            <div class="single-gallery-tags">
                <?php foreach ($tags as $tag) : ?>
                    <span class="single-gallery-tag"><?php echo esc_html($tag->name); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if (has_post_thumbnail()) : ?>
        <div class="header-background">
            <?php the_post_thumbnail('full'); ?>
            <div class="overlay"></div>
        </div>
    <?php endif; ?>
</header>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-gallery-view'); ?>>
    <div class="container">

        <?php if (!empty($media_ids)) : ?>
            <div class="single-gallery-content">
                <div class="row single-gallery-grid">
                    <?php foreach ($media_ids as $media_id) :
                        $type = wp_attachment_is('video', $media_id) ? 'video' : 'image';
                        $url = wp_get_attachment_url($media_id);
                        $thumbnail = wp_get_attachment_image_src($media_id, 'large');
                        if (!$thumbnail) continue;
                    ?>
                        <div class="col-md-6 col-lg-4 single-gallery-item" data-type="<?php echo esc_attr($type); ?>">
                            <?php if ($type === 'video') : ?>
                                <div class="video-wrapper">
                                    <video controls preload="none" poster="<?php echo esc_url($thumbnail[0]); ?>">
                                        <source src="<?php echo esc_url($url); ?>" type="video/mp4">
                                    </video>
                                </div>
                            <?php else : ?>
                                <a href="<?php echo esc_url($url); ?>" class="glightbox" data-gallery="gallery-<?php the_ID(); ?>">
                                    <img src="<?php echo esc_url($thumbnail[0]); ?>" 
                                         alt="<?php echo esc_attr(get_post_meta($media_id, '_wp_attachment_image_alt', true)); ?>"
                                         class="img-fluid">
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="single-gallery-footer">
            <?php the_content(); ?>
        </div>
    </div>
</article>

<?php
endwhile;

get_footer();
?>
