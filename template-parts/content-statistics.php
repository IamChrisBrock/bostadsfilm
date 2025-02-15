<?php
// Query statistics posts
$args = array(
    'post_type' => 'statistics',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
);
$statistics = new WP_Query($args);

if ($statistics->have_posts()) :
    echo '<div class="statistics-section">';
    echo '<div class="container fade-in-top"><div class="row justify-content-center">';

    while ($statistics->have_posts()) : $statistics->the_post();
        $media_type = get_post_meta(get_the_ID(), '_statistics_media_type', true) ?: 'image';
        $text = get_post_meta(get_the_ID(), '_statistics_text', true);
        $show_title = get_post_meta(get_the_ID(), '_statistics_show_title', true) !== '0';
        
        echo '<div class="col-12 col-md-3">';
        echo '<div class="statistic-item text-center fade-in-top">';
        
        // Display media (image or lottie)
        if ($media_type === 'lottie') {
            $lottie_file_id = get_post_meta(get_the_ID(), '_statistics_lottie_file', true);
            if ($lottie_file_id) {
                $lottie_url = wp_get_attachment_url($lottie_file_id);
                echo '<div class="lottie-container" data-animation-path="' . esc_url($lottie_url) . '"></div>';
            }
        } elseif ($media_type === 'image') {
            $image_id = get_post_meta(get_the_ID(), '_statistics_image', true);
            if ($image_id) {
                $image_url = wp_get_attachment_url($image_id);
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title()) . '" class="statistic-image">';
            }
        }
        
        // Display title if enabled
        if ($show_title) {
            $title = get_the_title();
            if (!empty($title) && $title !== 'Auto Draft') {
                echo '<h3 class="statistic-title mt-3">' . esc_html($title) . '</h3>';
            }
        }
        
        // Display text if set
        if (!empty($text)) {
            echo '<p class="statistic-text">' . esc_html($text) . '</p>';
        }
        
        // Display source if set
        $source = get_post_meta(get_the_ID(), '_statistics_source', true);
        if (!empty($source)) {
            echo '<p class="statistic-source">(' . esc_html($source) . ')</p>';
        }
        
        echo '</div></div>';
    endwhile;

    echo '</div></div></div>';
    wp_reset_postdata();
endif;
?>
