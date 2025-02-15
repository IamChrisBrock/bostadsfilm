<?php
class Elementor_Services_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'services_section';
    }

    public function get_title() {
        return 'Services Section';
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return ['filmestate'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Content',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => 'Show Section Title',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Yes',
                'label_off' => 'No',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'section_title',
            [
                'label' => 'Section Title',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => get_option('cpt_name', 'Services'),
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => 'Number of Services',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => -1,
                'max' => 20,
                'step' => 1,
                'default' => -1,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Query services posts
        $args = array(
            'post_type' => 'services',
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => 'menu_order',
            'order' => 'ASC'
        );

        $services = new WP_Query($args);

        if ($services->have_posts()) :
            ?>
            <div class="container-fluid">
                <?php if ($settings['show_title'] === 'yes') : ?>
                    <div class="row">
                        <div class="col-12">
                            <h1 class="section-title"><?php echo esc_html($settings['section_title']); ?></h1>
                        </div>
                    </div>
                <?php endif;

                $i = 0;
                while ($services->have_posts()) :
                    $services->the_post();
                    $layout = get_post_meta(get_the_ID(), '_service_layout', true) ?: 'Left';
                    $color_theme = get_post_meta(get_the_ID(), '_service_color_theme', true) ?: 'Primary';
                    $is_first_service = $i === 0;
                    $i++;

                    // Determine classes based on layout and color theme
                    $service_order_class = ($layout === 'Left') ? 'order-1' : 'order-1 order-lg-2';
                    $service_text_wrapper_class = ($layout === 'Left') ? 'right-spacing service-text-wrapper' : 'left-spacing service-text-wrapper-right';
                    $color_theme_class = ($color_theme === 'Primary') ? 'primary-colors' : 'secondary-colors';
                    $link_color_class = ($color_theme === 'Primary') ? 'primary-link-colors' : 'secondary-link-colors';
                    $image_wrapper_class = ($layout === 'Left') ? 'left-spacing' : 'right-spacing';

                    // Portfolio link HTML
                    $portfolio_link = sprintf(
                        '<div class="container portfolio-link-container">
                            <a class="portfolio-link %s" href="">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="portfolio-link-icon-wrapper">
                                            <div class="portfolio-link-icon">%s</div>
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
                        </div>',
                        esc_attr($link_color_class),
                        file_get_contents(get_template_directory() . "/assets/images/portfolio-link-icon.svg")
                    );

                    // Post content
                    $post_content = sprintf(
                        '<div class="text-content">%s</div>',
                        get_the_content()
                    );

                    $lottie_file_id = get_post_meta(get_the_ID(), '_service_lottie_file', true);
                    $lottie_url = $lottie_file_id ? wp_get_attachment_url($lottie_file_id) : '';
                    ?>
                    
                    <div class="row service-post-container d-flex align-items-center <?php echo esc_attr($color_theme_class); ?>">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-xl-6 col-lg-4 hidden-for-observer <?php echo esc_attr($service_order_class . " " . $service_text_wrapper_class); ?> <?php echo esc_attr($layout === 'Left' ? 'fade-in-left' : 'fade-in-right'); ?>">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-12 col-xl-6 col-lg-12">
                                                <div class="my-custom-lottie <?php echo esc_attr($layout === 'Left' ? 'order-1' : 'order-2'); ?>" 
                                                     always-play="true" 
                                                     data-lottie-color="<?php echo esc_attr(($color_theme_class == "primary-colors") ? $primary_text_color : $secondary_text_color); ?>" 
                                                     data-lottie-url="<?php echo esc_url($lottie_url); ?>">
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-6 col-lg-12">
                                                <div class="service-title-wrapper <?php echo esc_attr($layout === 'Left' ? 'order-2 left-margin-25' : 'order-1 right-margin-25'); ?>">
                                                    <h3 class="content-title service-title"><?php echo get_the_title(); ?></h3>
                                                    <?php echo $post_content; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-6 col-lg-8 hidden-for-observer <?php echo esc_attr(($layout === 'Left' ? 'order-2' : 'order-1') . ' ' . $image_wrapper_class); ?> <?php echo esc_attr($layout === 'Left' ? 'fade-in-left' : 'fade-in-right'); ?>">
                                    <div class="image-container zoom-effect">
                                        <?php echo get_the_post_thumbnail(); ?>
                                    </div>
                                    <?php echo $portfolio_link; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
            ?>
            </div>
            <?php
        endif;
    }
}
