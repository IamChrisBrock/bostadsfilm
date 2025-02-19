<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-url" content="<?php echo esc_attr(get_template_directory_uri()); ?>">

    <?php wp_head(); ?>
</head>
<?php
// Debug body classes
$debug_classes = get_body_class();
echo '<!-- Body Classes: ' . implode(' ', $debug_classes) . ' -->';
?>
<body <?php body_class(); ?>>
<div id="preloader">
    <div class="loader">
    <?php echo do_shortcode('[lottie file="' . get_template_directory_uri() .'/assets/lottie/loading-house.json" width="60px" height="60px"]');?>
    </div>
</div>

<header>
    
<?php
$menu_classes = ['main_menu_nav_wrapper'];

// Add menu style class based on body class
$body_classes = get_body_class();
if (in_array('menu-dynamic', $body_classes)) {
    $menu_classes[] = 'menu-dynamic';
}
if (in_array('transparent-background', $body_classes)) {
    $menu_classes[] = 'transparent-background';
}

?>
<div class="<?php echo esc_attr(implode(' ', $menu_classes)); ?>">
    <div class="main_menu_nav_container">

        <!-- Logo on the left -->
        <?php
        $logo_url = get_theme_mod('main_menu_logo_image');
        // Debug the logo URL and path
        error_log('Logo URL: ' . $logo_url);
        
        if ($logo_url) {
            $logo_path = str_replace(get_site_url(), ABSPATH, $logo_url);
            error_log('Logo Path: ' . $logo_path);
            
            // Try to get the file contents
            $svg_content = @file_get_contents($logo_path);
            if ($svg_content !== false && strpos($svg_content, '<svg') !== false) {
                // Clean and prepare the SVG
                $svg = $svg_content;
                // Add our class
                $svg = str_replace('<svg', '<svg class="main_menu_logo"', $svg);
                // Replace any existing colors with currentColor
                $svg = preg_replace('/fill="[^"]*"/', 'fill="currentColor"', $svg);
                $svg = preg_replace('/stroke="[^"]*"/', 'stroke="currentColor"', $svg);
                // Add fill to elements that might not have them
                $svg = str_replace('<path', '<path fill="currentColor"', $svg);
                $svg = str_replace('<rect', '<rect fill="currentColor"', $svg);
                $svg = str_replace('<circle', '<circle fill="currentColor"', $svg);
                $svg = str_replace('<polygon', '<polygon fill="currentColor"', $svg);
                // Remove any hardcoded styles
                $svg = preg_replace('/style="[^"]*"/', '', $svg);
                // Output the cleaned SVG
                echo $svg;
            } else {
                // If we couldn't load the SVG, try direct URL
                $svg_content = @file_get_contents($logo_url);
                if ($svg_content !== false && strpos($svg_content, '<svg') !== false) {
                    // Process and output SVG as above
                    $svg = $svg_content;
                    $svg = str_replace('<svg', '<svg class="main_menu_logo"', $svg);
                    $svg = preg_replace('/fill="[^"]*"/', 'fill="currentColor"', $svg);
                    $svg = preg_replace('/stroke="[^"]*"/', 'stroke="currentColor"', $svg);
                    $svg = str_replace('<path', '<path fill="currentColor"', $svg);
                    $svg = str_replace('<rect', '<rect fill="currentColor"', $svg);
                    $svg = str_replace('<circle', '<circle fill="currentColor"', $svg);
                    $svg = str_replace('<polygon', '<polygon fill="currentColor"', $svg);
                    $svg = preg_replace('/style="[^"]*"/', '', $svg);
                    echo $svg;
                } else {
                    // Fallback to regular image tag
                    echo '<img class="main_menu_logo" src="' . esc_url($logo_url) . '" alt="Logo">';
                }
            }
        }
        ?>

        <!-- Navigation Menu -->
        <nav class="main-nav">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'nav-menu',
                'container'      => false, // We already have a <nav> element
                'fallback_cb'    => 'wp_page_menu',
            ));
            ?>
        </nav>

       
       
        
    </div>
</div>

<!-- Mobile Menu -->
<div class="mobile-menu-container" id="mobile-menu">
     <!-- Hamburger Icon -->
<div class="hamburger-menu" id="menu-toggle">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    <nav class="mobile-nav">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_class'     => 'nav-menu',
            'container'      => false,
            'fallback_cb'    => 'wp_page_menu',
        ));
        ?>
    </nav>
</div>

</header>