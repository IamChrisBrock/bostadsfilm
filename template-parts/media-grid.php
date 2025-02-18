<?php
/**
 * Template part for displaying media grid
 * 
 * @param array $media_ids Array of media IDs
 * @param string $display_mode Display mode (optional)
 * @param string $gallery_name Gallery name for lightbox (optional)
 */

// Set defaults
$display_mode = isset($args['display_mode']) ? $args['display_mode'] : 'grid';
$gallery_name = isset($args['gallery_name']) ? $args['gallery_name'] : 'media-gallery';
$media_ids = isset($args['media_ids']) ? $args['media_ids'] : [];

if (!empty($media_ids)) :
    if (is_string($media_ids)) {
        $media_ids = explode(',', $media_ids);
    }
    
    // Set grid class based on display mode
    $grid_class = 'portfolio-grid';
    if ($display_mode === 'full') {
        $grid_class .= ' full-width-mode';
    } elseif ($display_mode === 'masonry') {
        $grid_class .= ' masonry-grid';
    }
    ?>
    <div id="project-content" class="single-project-gallery">
        <div class="container" style="padding-left:0px;padding-right:0px;">
            <div class="<?php echo esc_attr($grid_class); ?>">
    <?php if ($display_mode === 'masonry') : ?>
        <div class="grid-sizer col-md-6 col-lg-4"></div>
    <?php endif; ?>
        <?php foreach ($media_ids as $media_id) :
            // Get media type and URL
            $type = wp_attachment_is('video', $media_id) ? 'video' : 'image';
            $url = wp_get_attachment_url($media_id);
            
            // Set column class based on display mode
            $col_class = $display_mode === 'full' ? 'col-12' : 'col-md-6 col-lg-4';
            ?>
            <div class="<?php echo esc_attr($col_class); ?><?php echo $display_mode === 'masonry' ? ' grid-item' : ''; ?>">
                <?php if ($type === 'video') : ?>
                    <div class="portfolio-item video-item" data-type="video">
                        <video controls preload="none" playsinline class="w-100 lazy-media" data-src="<?php echo esc_url($url); ?>">
                            <source src="<?php echo esc_url($url); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                        <!-- Hidden link for lightbox gallery -->
                        <a href="<?php echo esc_url($url); ?>" class="glightbox hidden" data-type="video" data-gallery="<?php echo esc_attr($gallery_name); ?>"></a>
                    </div>
                <?php else :
                    $thumbnail = wp_get_attachment_image_src($media_id, 'large');
                    if ($thumbnail) : ?>
                        <div class="portfolio-item" data-type="image">
                            <a href="<?php echo esc_url($url); ?>" class="glightbox" data-gallery="<?php echo esc_attr($gallery_name); ?>">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'/%3E" data-src="<?php echo esc_url($thumbnail[0]); ?>" alt="<?php echo esc_attr(get_the_title($media_id)); ?>" class="w-100 lazy-media" loading="lazy">
                            </a>
                        </div>
                    <?php endif;
                endif; ?>
            </div>
        <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
