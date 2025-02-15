<?php
class Media_Text_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'media_text';
    }

    public function get_title() {
        return 'Media Text';
    }

    public function get_icon() {
        return 'eicon-video-camera';
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
            'text_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .animated-text-line' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => 'Text Typography',
                'selector' => '{{WRAPPER}} .animated-text-line',
            ]
        );

        $this->add_control(
            'word_spacing',
            [
                'label' => 'Word Spacing',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['em'],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 2,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 0.2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .animated-word' => 'margin: 0 {{SIZE}}{{UNIT}};',
                ],
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
                .elementor-widget-media_text .animated-word,
                .elementor-widget-media_text .word-text {
                    opacity: 1 !important;
                }
            </style>';
        }

        if (!empty($settings['section_title'])) {
            echo '<h2 class="media-text-section-title text-center mb-5">' . esc_html($settings['section_title']) . '</h2>';
        }

        include get_template_directory() . '/template-parts/content-media-text.php';
    }
}
