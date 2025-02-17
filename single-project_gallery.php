<?php
/**
 * Template for displaying single project gallery
 */

// Debug output
echo '<!-- Template: single-project_gallery.php -->'; // This will be visible in page source

get_header();
?>

<div class="header-wrapper project-header-wrapper">
    <div class="container">
        <h1><?php the_title(); ?></h1>
        <?php if (has_excerpt()): ?>
            <div class="project-description">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="project-content" class="single-project-gallery">

<div class="container" style="padding-left:0px;padding-right:0px;">
        

        <?php
        // Get display mode
        $display_mode = get_post_meta(get_the_ID(), '_project_gallery_display_mode', true) ?: 'full';
        
        // Get media items
        $media_ids = get_post_meta(get_the_ID(), '_project_gallery_media', true);
        if ($media_ids) {
            ?>
            <?php
            get_template_part('template-parts/media-grid', null, [
                'media_ids' => $media_ids,
                'display_mode' => $display_mode,
                'gallery_name' => 'single-project'
            ]);
            ?>
            <?php
        }
        
        // Display content if any
        if (get_the_content()) {
            ?>
            <div class="project-content">
                <?php the_content(); ?>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php
get_footer();
?>
