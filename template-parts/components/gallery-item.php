<?php
/**
 * Gallery Item Component Template
 * 
 * File Dependencies:
 * - inc/components/class-gallery-item.php (Main class)
 * - assets/css/project-galleries.css (Styles)
 * 
 * Used by:
 * - template-parts/gallery-grid.php (Through class-gallery-item.php)
 * 
 * @var int    $post_id          The post ID
 * @var string $title            The gallery title
 * @var string $permalink        The gallery permalink
 * @var string $preview_image_url The preview image URL
 * @var int    $media_count      Number of media items
 * @var bool   $has_media        Whether the gallery has media
 * @var string $excerpt          The gallery excerpt
 */
?>
<div class="gallery-item-wrapper">
    <article id="post-<?php echo esc_attr($post_id); ?>" <?php post_class('gallery-item', $post_id); ?>>
        <a href="<?php echo esc_url($permalink); ?>" class="gallery-item-link">
            <?php if ($preview_image_url) : ?>
                <div class="gallery-item-thumbnail">
                    <img src="<?php echo esc_url($preview_image_url); ?>" 
                         alt="<?php echo esc_attr($title); ?>"
                         class="img-fluid"
                         onload="this.classList.add('loaded');">
                    <?php if ($has_media) : ?>
                        <span class="media-count">
                            <?php echo esc_html($media_count); ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php elseif (has_post_thumbnail($post_id)) : ?>
                <div class="gallery-item-thumbnail">
                    <?php echo get_the_post_thumbnail($post_id, 'large', array('class' => 'img-fluid')); ?>
                    <?php if ($has_media) : ?>
                        <span class="media-count">
                            <?php echo esc_html($media_count); ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="gallery-item-content">
                <h2 class="gallery-item-title"><?php echo esc_html($title); ?></h2>
                <?php if ($excerpt) : ?>
                    <div class="gallery-item-excerpt">
                        <?php echo wp_kses_post($excerpt); ?>
                    </div>
                <?php endif; ?>
            </div>
        </a>
    </article>
</div>
