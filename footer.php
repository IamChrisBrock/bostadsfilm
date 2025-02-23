<footer>
    <?php
    // Get the ACF field value for the background image
    $footer_background_id = get_theme_mod('footer_background_image');
    $footer_background = $footer_background_id ? wp_get_attachment_url($footer_background_id) : '';
    
    // Check if the image exists and retrieve the URL and dimensions
    if ($footer_background) {
        $image_meta = wp_get_attachment_metadata($footer_background_id);
        $background_url = esc_url($footer_background);
        
        if ($image_meta) {
            $image_width = $image_meta['width'];
            $image_height = $image_meta['height'];
            
            // Calculate the aspect ratio (height / width)
            $aspect_ratio = $image_height / $image_width;

            // Set the inline style to calculate height based on width (100%)
            $footer_height = $aspect_ratio * 100; // This will give you the percentage height
            $background_style = "background-image: url('$background_url'); min-height: {$footer_height}vw;";
        } else {
            // Fallback if we can't get image dimensions
            $background_style = "background-image: url('$background_url'); min-height: 50vw;";
        }
    } else {
        // Fallback to default background if none is set
        $background_style = 'min-height: 50vw;';
    }
    ?>

    <!-- Footer div with background image -->
    <div class="custom-footer footer-contact-form-padding" style="<?php echo $background_style; ?>">
        <div class="footer-content">
            <div class="container">
                <div class="row">
                    <!-- Left Column - Sitemap -->
                    <div class="col-12 col-md-4 footer-left">
                        <div class="row">
                    <div clas="col-12 col-md-6"><h3 class="footer-sitemap-title">Sitemap</h3></div>
                    </div>
                        <div class="row sitemap-link-row">
                            <?php
                            // Get footer menu items
                            $menu_items = wp_get_nav_menu_items('footer-menu');
                            
                            if ($menu_items) {
                                // Calculate the split point for two columns
                                $total_items = count($menu_items);
                                $items_per_column = ceil($total_items / 2);
                                
                                // First column
                               
                                echo '<div class="col-6">';
                                echo '<ul class="footer-links">';
                                
                                foreach (array_slice($menu_items, 0, $items_per_column) as $menu_item) {
                                    echo '<li class="menu-item menu-item-type-custom menu-item-object-custom footer-link-item"><a href="' . esc_url($menu_item->url) . '">' . esc_html($menu_item->title) . '</a></li>';
                                }
                                
                                echo '</ul>';
                                echo '</div>';
                                
                                // Second column (if there are more items)
                                if ($total_items > $items_per_column) {
                                    echo '<div class="col-6">';
                                    echo '<ul class="footer-links">';
                                    
                                    foreach (array_slice($menu_items, $items_per_column) as $menu_item) {
                                        echo '<li class="menu-item menu-item-type-custom menu-item-object-custom footer-link-item"><a href="' . esc_url($menu_item->url) . '">' . esc_html($menu_item->title) . '</a></li>';
                                    }
                                    
                                    echo '</ul>';
                                    echo '</div>';
                                }
                            } else {
                                // Fallback if no menu is set
                                echo '<div class="col-12">';
                                echo '<p>Please set up the footer menu in WordPress admin.</p>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Center Column - Logo or Content -->
                    <div class="col-12 col-md-4 footer-center">
                        <div class="footer-center-content">
                            <?php 
                            $footer_logo_id = get_theme_mod('footer_logo');
                            if ($footer_logo_id) {
                                $footer_logo_url = wp_get_attachment_image_url($footer_logo_id, 'full');
                                $logo_width = get_theme_mod('footer_logo_width', '150');
                                echo '<img src="' . esc_url($footer_logo_url) . '" alt="' . esc_attr(get_bloginfo('name')) . ' Footer Logo" class="footer-logo" style="max-width: ' . esc_attr($logo_width) . 'px !important;">';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Right Column - Contact Info -->
                    <div class="col-12 col-md-4 footer-right">
                        <div class="footer-contact-info">
                            <h3><?php echo esc_html(get_theme_mod('company_name', get_bloginfo('name'))); ?></h3>
                            <address>
                                <?php echo nl2br(esc_html(get_theme_mod('footer_address', 'Your Address Here'))); ?>
                            </address>
                            <div class="footer-contact-data">
                            <?php if (get_theme_mod('footer_phone')) : ?>
                                <p class="footer-phone">
                                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', get_theme_mod('footer_phone'))); ?>">
                                        <?php echo esc_html(get_theme_mod('footer_phone')); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if (get_theme_mod('footer_email')) : ?>
                                <p class="footer-email">
                                    <a href="mailto:<?php echo esc_attr(get_theme_mod('footer_email')); ?>">
                                        <?php echo esc_html(get_theme_mod('footer_email')); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>


<?php wp_footer(); ?>
</body>
</html>