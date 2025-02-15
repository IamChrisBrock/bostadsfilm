<?php
class Info_Text_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'info_text';
    }

    public function get_title() {
        return 'Info Text';
    }

    public function get_icon() {
        return 'eicon-text';
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
                'default' => '',
                'placeholder' => 'Enter section title',
            ]
        );

        $this->end_controls_section();

        // Style Tab
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
                'selectors' => [
                    '{{WRAPPER}} .info-text-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => 'Title Typography',
                'selector' => '{{WRAPPER}} .info-text-title',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => 'Content Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .info-text-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => 'Content Typography',
                'selector' => '{{WRAPPER}} .info-text-content',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

        // Add editor-specific styles
        if ($is_edit_mode) {
            echo '<style>
                .elementor-element-' . $this->get_id() . ' .info-text-section {
                    opacity: 1 !important;
                    transform: none !important;
                    visibility: visible !important;
                }
                .elementor-element-' . $this->get_id() . ' .info-text-item {
                    opacity: 1 !important;
                    transform: none !important;
                    visibility: visible !important;
                }
            </style>';
        }

        // Query info texts with proper status handling
        $args = array(
            'post_type' => 'info_text',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_status' => $is_edit_mode ? array('publish', 'draft') : 'publish',
        );

        $info_texts = new WP_Query($args);

        if ($is_edit_mode && !$info_texts->have_posts()) {
            echo '<div class="elementor-alert elementor-alert-warning">No info text posts found. Please create some info text posts to display here.</div>';
            return;
        }

        echo '<div class="info-text-section">';
        
        if (!empty($settings['section_title'])) {
            echo '<h2 class="info-text-section-title text-center mb-5">' . esc_html($settings['section_title']) . '</h2>';
        }

        if ($info_texts->have_posts()) :
            // Don't use fade-in classes in editor mode
            $container_class = $is_edit_mode ? 'container' : 'container fade-in-top';
            echo '<div class="' . $container_class . '"><div class="row justify-content-center">';

            while ($info_texts->have_posts()) : $info_texts->the_post();
                // Remove all animation classes in editor mode
                $col_class = $is_edit_mode ? 'col-12 col-md-4 col-lg' : 'col-12 col-md-4 col-lg fade-in-top';
                $item_class = $is_edit_mode ? 'info-text-item' : 'info-text-item fade-in-top';
                
                echo '<div class="' . $col_class . '">';
                echo '<div class="' . $item_class . '">';
                
                // Display title if set
                $title = get_the_title();
                if (!empty($title) && $title !== 'Auto Draft') {
                    echo '<h3 class="info-text-title">' . esc_html($title) . '</h3>';
                }
                
                // Display the default WordPress content
                $content = get_the_content();
                if (!empty($content)) {
                    echo '<div class="info-text-content">' . apply_filters('the_content', $content) . '</div>';
                }
                
                echo '</div></div>';
            endwhile;

            echo '</div></div>';
        endif;

        echo '</div>';
        wp_reset_postdata();
    }
}
