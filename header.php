<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-url" content="<?php echo esc_attr(get_template_directory_uri()); ?>">

    <?php wp_head(); ?>
</head>
<div id="preloader">
    <div class="loader">
    <?php echo do_shortcode('[lottie file="' . get_template_directory_uri() .'/assets/lottie/loading-house.json" width="60px" height="60px"]');?>

    </div>
</div>

<body <?php body_class(); ?>>

<header>
    
<div class="main_menu_nav_wrapper">
    <div class="main_menu_nav_container">

        <!-- Logo on the left -->
        <img class="main_menu_logo" src="<?php echo esc_url(get_theme_mod('main_menu_logo_image')); ?>" alt="Logo">

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