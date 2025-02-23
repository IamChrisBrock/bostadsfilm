<?php
/* Template Name: Landing Page */
get_header(); ?>
<?php 
 global $primary_bg_color, $secondary_bg_color, $third_bg_color;
 global $primary_headline_color, $primary_text_color, $secondary_text_color, $third_text_color;
 global $link_color, $link_hover_color, $highlight_color;
?>
<div id="content" class="landing-page">
    <?php
    // Get customizer values for video file and image
    $video_file = get_theme_mod('header_video_file');
    $image_url = get_theme_mod('header_background_image');
    ?>
    <div class="header-wrapper">
        <h1 class="header-title">Turning listings into must-sees.</h1>
        <?php
        if ($video_file): ?>
            <div class="header-background">
            <video autoplay muted playsinline preload="auto" poster="<?php echo esc_url(get_theme_mod('header_background_image')); ?>">
    <source src="<?php echo esc_url($video_file); ?>" type="video/mp4">
    Your browser does not support the video tag.
</video>
            </div>
        <?php elseif ($image_url): ?>
            <div class="header-background" style="background-image: url('<?php echo esc_url($image_url); ?>');"></div>
        <?php else: ?>
            <div class="header-background">
                <!-- Default header content or fallback -->
            </div>
        <?php endif; ?>
    </div>
    <!-- Services -->
    
    <?php
    $args = array(
        'post_type' => 'services',
        'posts_per_page' => -1,
    );
    $services = new WP_Query($args);

    if ($services->have_posts()):
        $cpt_name = get_option('cpt_name', 'Services'); // Default to "Services" if not set
       
        ?>
        <section id="services" class="services">
        <div class="section-transition">
        <div class="scroll-line" style="margin-top:25px;" id="scroll-line"></div>
        
    </div>
            <div class="container-fluid">
                   
                                           
                       
            <?php
$i = 0;
while ($services->have_posts()):
    $services->the_post();
    $layout = get_post_meta(get_the_ID(), '_service_layout', true) ?: 'Left';
    $color_theme = get_post_meta(get_the_ID(), '_service_color_theme', true) ?: 'Primary';
    $is_first_service = $i === 0;
    $i++;

      // Determine the order and spacing based on layout
      $service_order_class = ($layout === 'Left') ? 'order-1' : 'order-1 order-lg-2';
      $service_text_wrapper_class = ($layout === 'Left') ? 'right-spacing service-text-wrapper' : 'left-spacing service-text-wrapper-right';
      $color_theme_class = ($color_theme === 'Primary') ? 'primary-colors' : 'secondary-colors';
      $link_color_class = ($color_theme === 'Primary') ? 'primary-link-colors' : 'secondary-link-colors';
      $image_wrapper_class = ($layout === 'Left') ? 'left-spacing' : 'right-spacing';

    // HTML for portfolio link
    $portfolio_link = '
        <div class="container portfolio-link-container">
        <a class="portfolio-link '.$link_color_class.'" href="portfolio">
            <div class="row">
            <div class="col-auto">
            
                <div class="portfolio-link-icon-wrapper">
                    <div class="portfolio-link-icon">
                        ' . file_get_contents(get_template_directory() . "/assets/images/portfolio-link-icon.svg") . '
                    </div>
                </div>
                <div class="portfolio-text-wrapper">
                    <div class="portfolio-link-text">View more examples</div>
                    <div class="portfolio-link-subtext">in the portfolio</div>
                </div>
                </div>
                
                <div class="col-12 col-sm-auto">
                <div class="portfolio-link-arrow hover-lottie"></div>
                </div>
                
                
                
            
            </div>
            </a>
        </div>';

    // HTML for the post content
    $post_content = '
        <div class="text-content">
            ' . get_the_content() . '
        </div>';

  
    

    // HTML for the service post container

$lottie_file_id = get_post_meta(get_the_ID(), '_service_lottie_file', true);
$lottie_url = $lottie_file_id ? wp_get_attachment_url($lottie_file_id) : '';


?>
    
    <div class="row service-post-container d-flex align-items-center fade-in-parent <?php echo $color_theme_class;?>">
    <div class="container">
    <?php if($is_first_service){?>
        <div class="row">
            <div class="col-12">
            <h1 class="section-title"><?php 
            echo $cpt_name;
    ?></h1></div></div><?php }?> 
        <div class="row">
    <div class="col-12 col-xl-6 col-lg-4 hidden-for-observer fade-in-item <?php echo $service_order_class . " " . $service_text_wrapper_class;?> <?php echo ($layout === 'Left' ? 'fade-in-left' : 'fade-in-right')?>">
    <div class="container">
    <div class="row">
   
        <div class="col-12 col-xl-6 col-lg-12">
        <div class="my-custom-lottie <?php echo ($layout === 'Left' ? 'order-1' : 'order-2') ?>" always-play="true" data-lottie-color="<?php echo ($color_theme_class == "primary-colors")? $primary_text_color : $secondary_text_color ?>" data-lottie-url="<?php echo esc_url($lottie_url);?>"></div>
    </div>
    
    <div class="col-12 col-xl-6 col-lg-12">
        <div class="service-title-wrapper 
            <?php echo ($layout === 'Left' ? 'order-2' : 'order-1') ?> <?php echo ($layout === 'Left' ? 'left-margin-25' : 'right-margin-25')?>">
            <h3 class="content-title service-title" style="--primary-headline-color: <?php echo $primary_headline_color; ?>; --secondary-headline-color: <?php echo $secondary_headline_color; ?>;"><?php echo get_the_title();?></h3>
            <?php echo $post_content;?>
        </div>
    </div>
    </div>
    </div>
    </div>
                    
    
    <div class="col-12 col-xl-6 col-lg-8 hidden-for-observer fade-in-item <?php echo ($layout === 'Left' ? 'order-2' : 'order-1') . ' ' . $image_wrapper_class; ?> <?php echo ($layout === 'Left' ? 'fade-in-left' : 'fade-in-right')?>">
        <div class="image-container zoom-effect">
                    <?php echo get_the_post_thumbnail();?>
         </div>
         <?php echo $portfolio_link;?>
    </div>
    </div>
    </div>
</div>
<?php
endwhile;
wp_reset_postdata();


?>


                    
                </div>




                <?php
                        
                        echo '</section>';
    else:
        echo '<p>No services found</p>';
    endif;
    ?>



        <!-- <section id="info">
            <h2>Info</h2>
        </section>

        <section id="team">
            <h2>Team</h2>
        </section>
        <section id="prices">
            <h2>Prices</h2>
        </section>
        <section id="contactUs">
            <h2>Contact Us</h2>
        </section> -->
        
</div>


<?php

if (have_posts()):
    while (have_posts()):
        the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile; endif; ?>


<?php get_footer(); ?>