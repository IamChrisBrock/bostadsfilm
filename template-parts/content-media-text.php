<?php
// Check if we're in Elementor editor
$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

$args = array(
    'post_type' => 'media_text',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'post_status' => $is_edit_mode ? array('publish', 'draft') : 'publish',
);
$media_texts = new WP_Query($args);

if ($media_texts->have_posts()) :
    echo '<div class="container">';
    
    while ($media_texts->have_posts()) : $media_texts->the_post();
        $media_type = get_post_meta(get_the_ID(), '_media_text_media_type', true) ?: 'image';
        $animated_text = get_post_meta(get_the_ID(), '_media_text_animated_text', true);
        
        echo '<div class="row align-items-center">';
        
        // Display media
        if ($media_type === 'image') {
            $image_id = get_post_meta(get_the_ID(), '_media_text_image', true);
            if ($image_id) {
                echo '<div class="col-12 col-md-6 animated-text-media-wrapper">';
                $image_url = wp_get_attachment_url($image_id);
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" />';
                echo '</div>';
            }
        } else {
            $video_id = get_post_meta(get_the_ID(), '_media_text_video', true);
            if ($video_id) {
                echo '<div class="col-12 col-md-6 animated-text-media-wrapper">';
                $video_url = wp_get_attachment_url($video_id);
                $video_ext = pathinfo($video_url, PATHINFO_EXTENSION);
                echo '<video autoplay loop muted playsinline>';
                echo '<source src="' . esc_url($video_url) . '" type="video/' . esc_attr($video_ext) . '">';
                echo '</video>';
                echo '</div>';
            }
        }
        
        // Display animated text
        if ($animated_text) {
            echo '<div class="col-12 col-md-6 animated-text-wrapper">';
            $lines = explode("\n", $animated_text);
            foreach ($lines as $line) {
                echo '<p class="animated-text-line">';
                $words = array_filter(explode(' ', trim($line)));
                foreach ($words as $word) {
                    if (trim($word) !== '') {
                        // Show at full opacity in editor mode
                        $opacity_style = $is_edit_mode ? '' : 'style="opacity: 0;"';
                        echo '<span class="animated-word" ' . $opacity_style . '><span class="word-text">' . esc_html(trim($word)) . '</span></span>';
                    }
                }
                echo '</p>';
            }
            echo '</div>';
        }
        
        echo '</div>';
    endwhile;
    
    echo '</div>';
    wp_reset_postdata();
endif;
?>
