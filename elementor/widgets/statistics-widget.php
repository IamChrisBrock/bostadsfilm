<?php
class Elementor_Statistics_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'statistics_section';
    }

    public function get_title() {
        return 'Statistics Section';
    }

    public function get_icon() {
        return 'eicon-number-field';
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
            'section_title',
            [
                'label' => 'Section Title',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Statistics',
            ]
        );



        $this->add_control(
            'show_titles',
            [
                'label' => 'Show Individual Titles',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Show',
                'label_off' => 'Hide',
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => 'Style',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Title Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .statistics-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .statistic-text' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'source_color',
            [
                'label' => 'Source Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .statistic-source' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Check if we're in editor mode
        $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

        // Save the original query
        global $wp_query;
        $original_query = $wp_query;

        // Query statistics posts
        $args = array(
            'post_type' => 'statistics',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_status' => $is_edit_mode ? array('publish', 'draft') : 'publish',
        );

        $wp_query = new WP_Query($args);
        
        // Add editor-specific styles
        if ($is_edit_mode) {
            echo '<style>
                .elementor-element-' . $this->get_id() . ' .statistics-section {
                    opacity: 1 !important;
                    transform: none !important;
                    visibility: visible !important;
                }
                .elementor-element-' . $this->get_id() . ' .statistic-item {
                    opacity: 1 !important;
                    transform: none !important;
                    visibility: visible !important;
                }
            </style>';
        }

        // Start the statistics section
        echo '<div class="statistics-section">';

        if ($is_edit_mode && !$wp_query->have_posts()) {
            echo '<div class="elementor-alert elementor-alert-warning">No statistics posts found. Please create some statistics posts to display here.</div>';
            return;
        }

        // Display section title if set
        if (!empty($settings['section_title'])) {
            echo '<h2 class="statistics-title text-center mb-5">' . esc_html($settings['section_title']) . '</h2>';
        }

        if ($wp_query->have_posts()) :
            // Don't use fade-in classes in editor mode
            $container_class = $is_edit_mode ? 'container' : 'container fade-in-top';
            echo '<div class="' . $container_class . '"><div class="row justify-content-center">';

            while ($wp_query->have_posts()) : $wp_query->the_post();
            
                
                $post_id = get_the_ID();
                $media_type = get_post_meta($post_id, '_statistics_media_type', true) ?: 'image';
                
                // Debug info in editor mode
                if ($is_edit_mode) {
                    echo '<!-- Statistics Post ID: ' . $post_id . ' -->';
                }
                
                // Remove all animation classes in editor mode
                $col_class = $is_edit_mode ? 'col-12 col-md-4 col-lg' : 'col-12 col-md-4 col-lg fade-in-top';
                $item_class = $is_edit_mode ? 'statistic-item text-center' : 'statistic-item text-center fade-in-top';
                
                echo '<div class="' . $col_class . '">';
                echo '<div class="' . $item_class . '" data-post-id="' . $post_id . '">';
                
                // Display media (image or lottie)
                if ($media_type === 'lottie') {
                    $lottie_file_id = get_post_meta($post_id, '_statistics_lottie_file', true);
                    if ($lottie_file_id) {
                        $lottie_url = wp_get_attachment_url($lottie_file_id);
                        if ($lottie_url) {
                            echo '<div class="lottie-container" data-animation-path="' . esc_url($lottie_url) . '"></div>';
                        } elseif ($is_edit_mode) {
                            echo '<div class="elementor-alert elementor-alert-warning">Lottie file not found</div>';
                        }
                    } elseif ($is_edit_mode) {
                        echo '<div class="elementor-alert elementor-alert-info">No Lottie file selected</div>';
                    }
                } elseif ($media_type === 'image') {
                    $image_id = get_post_meta($post_id, '_statistics_image', true);
                    if ($image_id) {
                        $image_url = wp_get_attachment_url($image_id);
                        if ($image_url) {
                            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr(get_the_title()) . '" class="statistic-image">';
                        } elseif ($is_edit_mode) {
                            echo '<div class="elementor-alert elementor-alert-warning">Image file not found</div>';
                        }
                    } elseif ($is_edit_mode) {
                        echo '<div class="elementor-alert elementor-alert-info">No image selected</div>';
                    }
                }
                
                // Display title if enabled and set
                if ($settings['show_titles'] === 'yes') {
                    $post = get_post();
                    if ($post && !empty($post->post_title) && $post->post_title !== 'Auto Draft') {
                        echo '<h3 class="statistic-title mt-3">' . esc_html($post->post_title) . '</h3>';
                    }
                }
                
                // Display text if set
                $text = get_post_meta(get_the_ID(), '_statistics_text', true);
                if (!empty($text)) {
                    echo '<p class="statistic-text">' . esc_html($text) . '</p>';
                }
                
                // Display source if set
                $source = get_post_meta(get_the_ID(), '_statistics_source', true);
                if (!empty($source)) {
                    echo '<p class="statistic-source">(' . esc_html($source) . ')</p>';
                }
                echo '</div></div>';
            endwhile;

            echo '</div></div>';
        endif;

        // Restore the original query
        $wp_query = $original_query;
        wp_reset_postdata();
    }
}
