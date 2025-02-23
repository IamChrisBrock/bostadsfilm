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
       

    </div>
    <?php if (has_post_thumbnail()) : ?>
        <div class="header-background">
            <?php the_post_thumbnail('full'); ?>
            <div class="overlay"></div>
        </div>
    <?php endif; ?>
</header>
<div class="container back-link-wrapper">
<?php
    // Get the portfolio page URL
    $portfolio_id = get_post_meta(get_the_ID(), '_portfolio_page_id', true);
    $portfolio_url = $portfolio_id ? get_permalink($portfolio_id) : get_post_type_archive_link('project_gallery');
?>

    <a href="<?php echo esc_url($portfolio_url); ?>" class="back-link">
    <div class="lottie-hover-link lottie-back-arrow" 
             data-lottie-path="<?php echo get_template_directory_uri(); ?>/assets/lottie/back-arrow.json"
             data-color="#ccc"
             data-hover-color="#333"></div>
        <span class="back-link-text"><?php _e('Back to Portfolio', 'filmestate'); ?></span>
    </a>
</div>

<div class="container single-gallery-text">
    <div class="row">
        <div class="col-12">
<div class="single-gallery-introduction-text">
    <?php the_content(); ?>
            </div>
        </div>
    </div>
    <div class="row single-gallery-info-boxes">
        <?php for ($i = 1; $i <= 3; $i++) : 
            $headline = get_post_meta(get_the_ID(), '_project_gallery_info_box_' . $i . '_headline', true);
            $content = get_post_meta(get_the_ID(), '_project_gallery_info_box_' . $i . '_content', true);
            if (!empty($headline) || !empty($content)) :
        ?>
            <div class="col-12 col-md-4">
                <div class="single-gallery-info-box" data-box="<?php echo $i; ?>">
                    <div class="info-box-header">
                        <h3><?php echo esc_html($headline); ?></h3>
                        <span class="toggle-icon">+</span>
                    </div>
                    <div class="info-box-content">
                        <div class="info-box-content-inner">
                            <?php echo wpautop(wp_kses_post($content)); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            endif;
        endfor; 
        ?>
    </div>
</div>


<article id="post-<?php the_ID(); ?>" <?php post_class('single-gallery-view'); ?>>
    <?php
    $display_mode = get_post_meta(get_the_ID(), '_project_gallery_display_mode', true) ?: 'square';
    $container_class = 'container';
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
                        <div class="single-gallery-item <?php echo $display_mode === 'full' ? 'full-width' : ''; ?>" data-type="<?php echo esc_attr($type); ?>">
                            <?php
                            // Always wrap in a lightbox link, but with different data attributes for video/image
                            $lightbox_attrs = array(
                                'class' => 'glightbox',
                                'data-gallery' => 'gallery-' . get_the_ID()
                            );
                            
                            if ($type === 'video') {
                                // For videos, render with poster image
                                $poster = $thumbnail ? $thumbnail[0] : '';
                                ?>
                                <video class="js-player" playsinline controls poster="<?php echo esc_url($poster); ?>">
                                    <source src="<?php echo esc_url($url); ?>" type="<?php echo esc_attr($mime_type); ?>">
                                </video>
                                <?php
                                // Add hidden lightbox element for gallery sequence
                                $lightbox_attrs['style'] = 'display: none;';
                                $lightbox_attrs['data-type'] = 'video';
                                $lightbox_attrs['href'] = esc_url($url);
                                $lightbox_attrs['class'] = 'glightbox';
                                if ($thumbnail) {
                                    $lightbox_attrs['data-poster'] = esc_url($thumbnail[0]);
                                }
                                echo '<a ' . implode(' ', array_map(
                                    function($key) use ($lightbox_attrs) {
                                        return $key . '="' . esc_attr($lightbox_attrs[$key]) . '"';
                                    },
                                    array_keys($lightbox_attrs)
                                )) . '></a>';
                            } else {
                                // For images, make them open in lightbox
                                $lightbox_attrs['href'] = esc_url($url);
                                
                                // Build attributes string
                                $attrs = '';
                                foreach ($lightbox_attrs as $key => $value) {
                                    $attrs .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
                                }
                                ?>
                                <a <?php echo $attrs; ?>>
                                    <img src="<?php echo esc_url($thumbnail[0]); ?>" 
                                         alt="<?php echo esc_attr(get_post_meta($media_id, '_wp_attachment_image_alt', true)); ?>"
                                         loading="lazy"
                                         class="img-fluid">
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div><!-- .gallery-class -->
            </div><!-- .single-gallery-content -->
        <?php endif; ?>

        <div class="single-gallery-footer">
           
        </div>
    </div>
</article>

<?php
endwhile;

get_footer();
?>

get_footer();
?>

get_footer();
?>
