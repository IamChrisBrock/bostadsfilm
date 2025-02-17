<?php
/**
 * Template for displaying project gallery archive
 */

get_header();
?>
<div class="header-wrapper portfolio-header-wrapper">
    <div class="container">
        <h1><?php _e('Our Portfolio', 'filmestate'); ?></h1>
        <div class="portfolio-description">
            <?php echo term_description(); ?>
        </div>
    </div>
</div>

<div class="portfolio-archive">
    <div class="container">
        <div class="view-switch">
            <label class="switch">
                <input type="checkbox" id="view-mode-toggle">
                <span class="slider round"></span>
            </label>
            <span class="switch-label">Projects</span>
        </div>
</div>

        <div class="portfolio-projects-grid">
            <?php
            // Check if we're in media view
            $view_mode = isset($_GET['view']) ? $_GET['view'] : 'projects';
            
            if ($view_mode === 'media') {
                // Use media grid template
                get_template_part('template-parts/media-grid');
            } else {
                // Show projects grid
                get_template_part('template-parts/projects-grid');
            }
                
                // Pagination
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('Previous', 'filmestate'),
                    'next_text' => __('Next', 'filmestate'),
                ));
                
            else :
                ?>
                <p><?php _e('No projects found.', 'filmestate'); ?></p>
                <?php
            endif;
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
?>
