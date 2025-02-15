<?php
function my_theme_customize_register($wp_customize) {
    // Add color settings with better sanitization
    $wp_customize->add_setting('page_background_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_setting('primary_background_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_setting('secondary_background_color', array(
        'default' => '#f0f0f0',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_setting('third_background_color', array(
        'default' => '#e0e0e0',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_setting('primary_headline_color', array(
        'default' => '#444841',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_setting('secondary_headline_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_setting('primary_text_color', array(
        'default' => '#444841',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_setting('secondary_text_color', array(
        'default' => '#666666',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_setting('third_text_color', array(
        'default' => '#999999',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_setting('primary_link_color', array(
        'default' => '#60655b',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_setting('primary_link_hover_color', array(
        'default' => '#000',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_setting('secondary_link_color', array(
        'default' => '#8f938c',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_setting('secondary_link_hover_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_setting('highlight_color', array(
        'default' => '#ff9900',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    // Add color controls
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'page_background_color_control', array(
        'label' => 'Page Background Color',
        'section' => 'colors',
        'settings' => 'page_background_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_background_color_control', array(
        'label' => 'Primary Background Color',
        'section' => 'colors',
        'settings' => 'primary_background_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_background_color_control', array(
        'label' => 'Secondary Background Color',
        'section' => 'colors',
        'settings' => 'secondary_background_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'third_background_color_control', array(
        'label' => 'Third Background Color',
        'section' => 'colors',
        'settings' => 'third_background_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_headline_color_control', array(
        'label' => 'Primary Headline Color',
        'section' => 'colors',
        'settings' => 'primary_headline_color',
    )));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_headline_color_control', array(
        'label' => 'Secondary Headline Color',
        'section' => 'colors',
        'settings' => 'secondary_headline_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_text_color_control', array(
        'label' => 'Primary Text Color',
        'section' => 'colors',
        'settings' => 'primary_text_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_text_color_control', array(
        'label' => 'Secondary Text Color',
        'section' => 'colors',
        'settings' => 'secondary_text_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'third_text_color_control', array(
        'label' => 'Third Text Color',
        'section' => 'colors',
        'settings' => 'third_text_color',
    )));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_link_color_control', array(
        'label' => 'Primary Link Color',
        'section' => 'colors',
        'settings' => 'primary_link_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_link_hover_color_control', array(
        'label' => 'Primary Link Hover Color',
        'section' => 'colors',
        'settings' => 'primary_link_hover_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_link_color_control', array(
        'label' => 'Secondary Link Color',
        'section' => 'colors',
        'settings' => 'secondary_link_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_link_hover_color_control', array(
        'label' => 'Secondary Link Hover Color',
        'section' => 'colors',
        'settings' => 'secondary_link_hover_color',
    )));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'highlight_color_control', array(
        'label' => 'Highlight Color',
        'section' => 'colors',
        'settings' => 'highlight_color',
    )));
}
add_action('customize_register', 'my_theme_customize_register');

