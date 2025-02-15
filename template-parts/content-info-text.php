<?php
// Query info texts
$args = array(
    'post_type' => 'info_text',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
);
$info_texts = new WP_Query($args);

if ($info_texts->have_posts()) :
    echo '<div class="info-text-section">';
    echo '<div class="container fade-in-top"><div class="row justify-content-center">';

    while ($info_texts->have_posts()) : $info_texts->the_post();
        echo '<div class="col-12 col-md-4 col-lg">';
        echo '<div class="info-text-item fade-in-top">';
        
        // Display title if set
        $title = get_the_title();
        if (!empty($title) && $title !== 'Auto Draft') {
            echo '<h3 class="info-text-title">' . esc_html($title) . '</h3>';
        }
        
        // Display the default WordPress content
        $content = get_the_content();
        if (!empty($content)) {
            echo '<div class="info-text-content">' . apply_filters('the_content', $content) . '</div>';
        }
        
        echo '</div></div>';
    endwhile;

    echo '</div></div></div>';
    wp_reset_postdata();
endif;
?>
