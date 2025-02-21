<?php
error_log('Loading single.php template');

get_header();

if ( have_posts() ) : 
    while ( have_posts() ) : the_post();
        the_content();  // This outputs the post content
    endwhile;
endif;

get_footer();