<?php
/**
 * Template for displaying project gallery archive
 */

get_header();

// Get archive title and description
$archive_title = get_the_archive_title();
$archive_description = get_the_archive_description();
?>

<header class="full-window-header portfolio-gallery-header">
    <div class="header-content">
        <h1 class="single-gallery-title">Portfolio</h1>
        <?php if ($archive_description) : ?>
            <div class="single-gallery-description">
                <?php echo wp_kses_post($archive_description); ?>
            </div>
        <?php endif; ?>
    </div>
</header>

<?php
// Include the shared gallery grid template
get_template_part('template-parts/gallery-grid');

get_footer();
