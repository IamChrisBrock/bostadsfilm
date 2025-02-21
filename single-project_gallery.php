<?php
/**
 * Template for displaying single project gallery
 */

get_header();

while (have_posts()) : the_post();
    // Get gallery media
    $media_ids = get_post_meta(get_the_ID(), '_project_gallery_media', true);
    error_log('Raw media IDs: ' . print_r($media_ids, true));
    $media_ids = $media_ids ? explode(',', $media_ids) : array();
    error_log('Processed media IDs: ' . print_r($media_ids, true));
?>

<header class="full-window-header single-gallery-header">
    <div class="header-content">
        <h1 class="single-gallery-title"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
            <div class="single-gallery-description">
                <?php the_excerpt(); ?>
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
    <?php
    $display_mode = get_post_meta(get_the_ID(), '_project_gallery_display_mode', true) ?: 'square';
    $container_class = $display_mode === 'full' ? 'container' : 'container';
    $gallery_class = 'single-gallery-grid mode-' . $display_mode;
    ?>
    <div class="<?php echo esc_attr($container_class); ?>">
        <?php if (!empty($media_ids)) : ?>
            <div class="single-gallery-content">
                <div class="<?php echo esc_attr($gallery_class); ?>">
                    <?php foreach ($media_ids as $media_id) :
                        $mime_type = get_post_mime_type($media_id);
                        $type = (strpos($mime_type, 'video/') === 0) ? 'video' : 'image';
                        $url = wp_get_attachment_url($media_id);
                        $thumbnail = wp_get_attachment_image_src($media_id, 'large');
                        
                        // Debug output
                        // echo (sprintf(
                        //     'Media ID: %d, MIME Type: %s, Is Video: %s, URL: %s', 
                        //     $media_id, 
                        //     $mime_type, 
                        //     $type === 'video' ? 'yes' : 'no',
                        //     $url
                        // ))."<br>";
                        
                        // Only skip if it's an image without thumbnail
                        if (!$thumbnail && $type !== 'video') {
                            // echo (sprintf('No thumbnail for image ID: %d', $media_id));
                            continue;
                        }
                    ?>
                        <div class="single-gallery-item <?php echo $display_mode === 'full' ? 'full-width' : 'col-md-6 col-lg-4'; ?>" data-type="<?php echo esc_attr($type); ?>">
                            <?php
                            // Always wrap in a lightbox link, but with different data attributes for video/image
                            $lightbox_attrs = array(
                                'class' => 'glightbox',
                                'data-gallery' => 'gallery-' . get_the_ID()
                            );
                            
                            if ($type === 'video') {
                                // For videos, use video type
                                $lightbox_attrs['data-type'] = 'video';
                                $lightbox_attrs['href'] = esc_url($url);
                                if ($thumbnail) {
                                    $lightbox_attrs['data-poster'] = esc_url($thumbnail[0]);
                                }
                            } else {
                                // For images, make them open in lightbox
                                $lightbox_attrs['href'] = esc_url($url);
                            }
                            
                            // Build attributes string
                            $attrs = '';
                            foreach ($lightbox_attrs as $key => $value) {
                                $attrs .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
                            }
                            ?>
                            <a <?php echo $attrs; ?>>
                                <?php if ($type === 'video') : ?>
                                   
                                        <div class="plyr__video-embed js-player">
                                            <video controls playsinline
                                                <?php if ($thumbnail): ?>
                                                    poster="<?php echo esc_url($thumbnail[0]); ?>"
                                                <?php endif; ?>>
                                                <source src="<?php echo esc_url($url); ?>" type="<?php echo esc_attr($mime_type); ?>">
                                            </video>
                                        </div>
                                    
                                <?php else : ?>
                                    <img src="<?php echo esc_url($thumbnail[0]); ?>" 
                                         alt="<?php echo esc_attr(get_post_meta($media_id, '_wp_attachment_image_alt', true)); ?>"
                                         class="img-fluid">
                                <?php endif; ?>
                            </a>
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
