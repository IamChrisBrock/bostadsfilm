<?php
/**
 * Template part for displaying a single gallery item
 * Used in both the gallery page template and AJAX responses
 */

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
<div class="col-md-6 col-lg-4">
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
