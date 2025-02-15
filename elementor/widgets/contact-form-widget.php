<?php
class Contact_Form_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'contact_form';
    }

    public function get_title() {
        return 'Contact Form';
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
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
            'headline',
            [
                'label' => 'Headline',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => "Let's Talk",
                'placeholder' => 'Enter headline',
            ]
        );

        $this->add_control(
            'subtext',
            [
                'label' => 'Subtext',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Get in touch with us',
                'placeholder' => 'Enter subtext',
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
            'background_color',
            [
                'label' => 'Background Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f7f1e9',
                'selectors' => [
                    '{{WRAPPER}} .custom-contact-form' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => 'Text Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#444841',
                'selectors' => [
                    '{{WRAPPER}} .custom-contact-form' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-headline' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-subtext' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'headline_typography',
                'label' => 'Headline Typography',
                'selector' => '{{WRAPPER}} .form-headline',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'subtext_typography',
                'label' => 'Subtext Typography',
                'selector' => '{{WRAPPER}} .form-subtext',
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
                .elementor-widget-contact_form .custom-contact-form {
                    opacity: 1 !important;
                    transform: none !important;
                }
            </style>';
        }
        
        // Get the Contact Form 7 form
        $args = array(
            'post_type' => 'wpcf7_contact_form',
            'posts_per_page' => 1,
            'title' => 'Custom Contact Form'
        );
        
        $form = get_posts($args);
        
        if ($form) {
            $form = $form[0];
            ?>
            <section class="contact-section">
                <div class="custom-contact-form<?php echo $is_edit_mode ? '' : ' fade-in-bottom'; ?>">
                    <h2 class="form-headline"><?php echo esc_html($settings['headline']); ?></h2>
                    <p class="form-subtext"><?php echo esc_html($settings['subtext']); ?></p>
                    <?php echo do_shortcode('[contact-form-7 id="' . $form->ID . '" title="' . $form->post_title . '"]'); ?>
                </div>
            </section>
            <?php
        }
    }
}
