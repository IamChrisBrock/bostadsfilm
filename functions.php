<?php
add_filter('show_admin_bar', '__return_false');

// Autoloader for component classes
spl_autoload_register(function ($class) {
    // Base directory for components
    $base_dir = get_template_directory() . '/inc/';

    // Only handle our namespace
    if (strpos($class, 'Inkperial\\Components\\') !== 0) {
        return;
    }

    // Remove namespace prefix
    $relative_class = str_replace('Inkperial\\Components\\', '', $class);

    // Convert class name to file path
    $file = $base_dir . 'components/class-' . strtolower(str_replace('_', '-', $relative_class)) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Include theme files
require get_template_directory() . '/inc/colors.php';
require get_template_directory() . '/inc/gallery-settings.php';
require get_template_directory() . '/inc/gallery-filters.php';
require get_template_directory() . '/inc/custom-post-types.php';
require get_template_directory() . '/inc/custom-taxonomies.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/shortcodes.php';
function mytheme_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('menus');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array('search-form', 'comment-form', 'gallery', 'caption'));
    add_theme_support('elementor');  // Elementor support
}

add_action('after_setup_theme', 'mytheme_theme_setup');

// Enqueue CSS
function mytheme_enqueue_css() {
    wp_enqueue_style('menu-css', get_template_directory_uri() . '/assets/css/menu.css');
    wp_enqueue_style('custom-contact-form-7-css', get_template_directory_uri() . '/assets/css/cf7-custom.css');
    
    // Enqueue project galleries styles on relevant pages
    if (is_post_type_archive('project_gallery') || 
        is_singular('project_gallery') || 
        is_page_template('page-templates/template-gallery.php')) {
        wp_enqueue_style('project-galleries', get_template_directory_uri() . '/assets/css/project-galleries.css');
    }
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
    wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');

}


add_action('wp_enqueue_scripts', 'mytheme_enqueue_css');

// Debug template loading
function debug_template_selection($template) {
    if (is_singular('project_gallery')) {
        error_log('Selected template for project gallery: ' . $template);
    }
    if (is_post_type_archive('project_gallery')) {
        error_log('Selected template for project gallery archive: ' . $template);
    }
    return $template;
}
add_filter('template_include', 'debug_template_selection');

// Force single project gallery template
function force_project_gallery_template($template) {
    if (is_singular('project_gallery')) {
        $new_template = locate_template(array('single-project_gallery.php'));
        if ($new_template) {
            return $new_template;
        }
    }
    if (is_post_type_archive('project_gallery')) {
        $new_template = locate_template(array('archive-project_gallery.php'));
        if ($new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('single_template', 'force_project_gallery_template');

// Enqueue Styles and Scripts
function mytheme_enqueue_scripts() {
    wp_enqueue_style('mytheme-style', get_stylesheet_uri());
    wp_enqueue_script('fade-in-script', get_template_directory_uri() . '/assets/js/section-observer-fade-in.js', array(), false, true);
    wp_enqueue_script('main-js', get_template_directory_uri() . '/assets/js/main.js');
    
    // Load Lottie globally since it's used in multiple places
    wp_enqueue_script('lottie-js', 'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js', array(), '5.12.2', true);


    // Gallery scripts and styles
        wp_enqueue_script('masonry', 'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js', array('jquery'), '4.2.2', true);
        wp_enqueue_script('imagesloaded', 'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js', array('jquery'), '5.0.0', true);
        wp_enqueue_script('glightbox', 'https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js', array(), '3.2.0', true);
        wp_enqueue_style('glightbox', 'https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/css/glightbox.min.css');
        
        // Gallery filters
        if (is_post_type_archive('project_gallery') || is_tax('project_tags') || is_page_template('page-templates/template-gallery.php')) {
            wp_enqueue_script('gallery-filters', get_template_directory_uri() . '/assets/js/gallery-filters.js', array('jquery', 'lottie-js'), '1.0', true);
            wp_localize_script('gallery-filters', 'galleryFilters', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('gallery_filter')
            ));
        }
    
    }

    // Admin scripts
    if (is_admin()) {
        wp_enqueue_script('admin-js', get_template_directory_uri() . '/assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), '1.0', true);
    }


add_action('wp_enqueue_scripts', 'mytheme_enqueue_scripts');

// Now hook the dynamic color function separately
add_action('wp_enqueue_scripts', 'my_dynamic_colors');

function my_dynamic_colors() {
    global $page_bg_color, $primary_bg_color, $secondary_bg_color, $third_bg_color;
    global $primary_text_color, $secondary_text_color, $third_text_color;
    global $primary_link_color, $primary_link_hover_color, $secondary_link_color, $secondary_link_hover_color, $highlight_color;

    // Get the colors from the Customizer with proper sanitization
    $page_bg_color = sanitize_hex_color(get_theme_mod('page_background_color', '#ffffff'));
    $primary_bg_color = sanitize_hex_color(get_theme_mod('primary_background_color', '#f7f1e9'));
    $secondary_bg_color = sanitize_hex_color(get_theme_mod('secondary_background_color', '#272b24'));
    $third_bg_color = sanitize_hex_color(get_theme_mod('third_background_color', '#e0e0e0'));

    $primary_headline_color = sanitize_hex_color(get_theme_mod('primary_headline_color', '#444841'));
    $secondary_headline_color = sanitize_hex_color(get_theme_mod('secondary_headline_color', '#ffffff'));
    $primary_text_color = sanitize_hex_color(get_theme_mod('primary_text_color', '#666666'));
    $secondary_text_color = sanitize_hex_color(get_theme_mod('secondary_text_color', '#ffffff'));
    $third_text_color = sanitize_hex_color(get_theme_mod('third_text_color', '#ff0000'));

    $primary_link_color = sanitize_hex_color(get_theme_mod('primary_link_color', '#60655b'));
    $primary_link_hover_color = sanitize_hex_color(get_theme_mod('primary_link_hover_color', '#454d3d'));
    $secondary_link_color = sanitize_hex_color(get_theme_mod('secondary_link_color', '#8f938c'));
    $secondary_link_hover_color = sanitize_hex_color(get_theme_mod('secondary_link_hover_color', '#ebf3e4'));
    $highlight_color = sanitize_hex_color(get_theme_mod('highlight_color', '#ff9900'));

    // Generate CSS with variables
    $css = "
        :root {
            --page-bg-color: $page_bg_color;
            --primary-bg-color: $primary_bg_color;
            --secondary-bg-color: $secondary_bg_color;
            --third-bg-color: $third_bg_color;
            --primary-headline-color: $primary_headline_color;
            --secondary-headline-color: $secondary_headline_color;
            --primary-text-color: $primary_text_color;
            --secondary-text-color: $secondary_text_color;
            --third-text-color: $third_text_color;
            --primary-link-color: $primary_link_color;
            --primary-link-hover-color: $primary_link_hover_color;
            --secondary-link-color: $secondary_link_color;
            --secondary-link-hover-color: $secondary_link_hover_color;
            --highlight-color: $highlight_color;
        }

        body {
            background-color: var(--primary-bg-color);
            color: var(--primary-text-color);
        }

        a {
            color: var(--primary-link-color);
        }

        a:hover {
            color: var(--primary-link-hover-color);
        }

        .highlight {
            background-color: var(--highlight-color);
        }
    ";

    wp_add_inline_style('mytheme-style', $css);
}

// Register Menus
function mytheme_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mytheme'),
        'footer'  => __('Footer Menu', 'mytheme'),
    ));
}

// Add menu style meta box
function add_menu_style_meta_box() {
    // Add to pages
    add_meta_box(
        'menu_style_meta_box',
        'Menu Style',
        'render_menu_style_meta_box',
        'page',
        'side',
        'high'
    );

    // Add to project gallery
    add_meta_box(
        'menu_style_meta_box',
        'Menu Style',
        'render_menu_style_meta_box',
        'project_gallery',
        'side',
        'high'
    );

    // Debug which post types have the meta box
    add_action('wp_footer', function() {
        $screen = get_current_screen();
        if ($screen) {
            echo "<!-- Current Screen ID: {$screen->id} -->";
        }
    });
}
add_action('add_meta_boxes', 'add_menu_style_meta_box');

// Render menu style meta box
function render_menu_style_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('menu_style_meta_box', 'menu_style_meta_box_nonce');

    // Get current value and add debug comment
    $menu_style = get_post_meta($post->ID, '_menu_style', true);
    echo '<!-- Current menu style: ' . esc_html($menu_style) . ' -->';

    // Set default to white if not set
    if (empty($menu_style)) {
        $menu_style = 'white';
    }
    ?>
    <p>
        <label for="menu_style">Menu Text Color:</label><br>
        <select name="menu_style" id="menu_style">
            <option value="white" <?php selected($menu_style, 'white'); ?>>Always White</option>
            <option value="dynamic" <?php selected($menu_style, 'dynamic'); ?>>Dynamic (Primary Color on Transparent)</option>
        </select>
    </p>
    <?php
}

// Save menu style meta box data
function save_menu_style_meta_box($post_id) {
    // Add debug logging
    error_log('Attempting to save menu style for post ' . $post_id);

    // Check if our nonce is set and verify it
    if (!isset($_POST['menu_style_meta_box_nonce'])) {
        error_log('Menu style nonce not set');
        return;
    }

    if (!wp_verify_nonce($_POST['menu_style_meta_box_nonce'], 'menu_style_meta_box')) {
        error_log('Menu style nonce verification failed');
        return;
    }

    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        error_log('This is an autosave');
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        error_log('User cannot edit post');
        return;
    }

    // Save the menu style
    if (isset($_POST['menu_style'])) {
        $style = sanitize_text_field($_POST['menu_style']);
        update_post_meta($post_id, '_menu_style', $style);
        error_log('Saved menu style: ' . $style);
    } else {
        error_log('Menu style not set in POST data');
    }

    // Verify it was saved
    $saved_style = get_post_meta($post_id, '_menu_style', true);
    error_log('Retrieved saved style: ' . $saved_style);
}
add_action('save_post', 'save_menu_style_meta_box');

// Add body classes for menu backgrounds
function add_menu_body_classes($classes) {
    // Get the current post ID and debug info
    $post_id = get_queried_object_id();
    $menu_style = get_post_meta($post_id, '_menu_style', true);
    
    // Add debug comment
    add_action('wp_footer', function() use ($post_id, $menu_style) {
        echo "<!-- Debug: Post ID: {$post_id}, Menu Style: {$menu_style} -->";
    });

    // Check if we're on a portfolio page
    $is_portfolio = is_page_template('page-templates/template-portfolio.php') ||
                   is_post_type_archive('project_gallery') ||
                   is_singular('project_gallery');

    // Always add transparent-background on portfolio pages
    if ($is_portfolio) {
        $classes[] = 'transparent-background';
    }

    // Add menu-dynamic class if set
    if ($menu_style === 'dynamic') {
        $classes[] = 'menu-dynamic';
    }

    return $classes;
}
add_filter('body_class', 'add_menu_body_classes');

add_action('init', 'mytheme_register_menus');

// Include Custom Elementor Widgets
// function mytheme_include_elementor_widgets() {
//     require_once get_template_directory() . '/elementor-widgets/video-showcase.php';
// }
// add_action('elementor/widgets/widgets_registered', 'mytheme_include_elementor_widgets');

// Elementor-specific setup



function mytheme_elementor_setup() {
    // Enable Elementor experimental features
    add_theme_support('elementor-experiments', [
        'container' => true, // Elementor container mode
        'theme-builder' => true, // Elementor theme builder support
    ]);
    // Allow Elementor headers/footers (optional)
    add_theme_support('elementor-header-footer');
    add_theme_support('elementor-full-width');
}

// Register Filmestate widget category
function register_filmestate_widget_category($elements_manager) {
    $elements_manager->add_category(
        'filmestate',
        [
            'title' => esc_html__('Filmestate', 'filmestate'),
            'icon' => 'fa fa-plug',
        ]
    );
}
add_action('elementor/elements/categories_registered', 'register_filmestate_widget_category');

add_action('after_setup_theme', 'mytheme_elementor_setup');

// Disable Theme Styles/Scripts if Elementor is active
function mytheme_disable_theme_styles_scripts() {
    if (function_exists('elementor_load_plugin_textdomain')) {
        wp_dequeue_style('mytheme-style');
        wp_dequeue_script('mytheme-script');
    }
}
add_action('wp_enqueue_scripts', 'mytheme_disable_theme_styles_scripts', 20);

function mytheme_animation_scripts(){
    wp_enqueue_script('scroll-line-animation', get_template_directory_uri() . '/assets/js/scroll-line.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'mytheme_animation_scripts');


// CUSTOMIZATIONS
function enqueue_bootstrap() {
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css', array(), '5.3.0-alpha1');
    // Enqueue Bootstrap JS (with Popper.js)
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.0-alpha1', true);
}
add_action('wp_enqueue_scripts', 'enqueue_bootstrap');

// Fonts
function custom_fonts(){
//    wp_enqueue_style('custom-fonts', get_template_directory_uri() . '/assets/fonts/MADE-TOMMY-Regular.woff', array(), null);
}
add_action('wp_enqueue_scripts', 'custom_fonts');
function custom_google_fonts() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap');
}
add_action('wp_enqueue_scripts', 'custom_google_fonts');

// Customizers
function my_theme_customizer( $wp_customize ) {
    // Footer Background Image
    $wp_customize->add_section('footer_section', array(
        'title'    => 'Footer Settings',
        'priority' => 30,
    ));

    $wp_customize->add_setting('footer_background_image', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'footer_background_image', array(
        'label'    => 'Footer Background Image',
        'section'  => 'footer_section',
        'settings' => 'footer_background_image',
        'mime_type' => 'image',
    )));
    /** HEADER */
    // Create a section for the header background settings
    $wp_customize->add_section( 'header_background_section', array(
        'title'    => __( 'Header Video / Image', 'mytheme' ),
        'priority' => 30,
    ));

    // Add setting for background video file upload
    $wp_customize->add_setting( 'header_video_file', array(
        'default'   => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize, 'header_video_file', array(
        'label'     => __( 'Upload Header Video (MP4)', 'mytheme' ),
        'section'   => 'header_background_section',
        'settings'  => 'header_video_file',
        'mime_type' => 'video/mp4',
    )));

    // Add setting for background image upload
    $wp_customize->add_setting( 'header_background_image', array(
        'default'   => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'header_background_image', array(
        'label'     => __( 'Upload Header Background Image', 'mytheme' ),
        'section'   => 'header_background_section',
        'settings'  => 'header_background_image',
    )));

    /** MENU */
    // Create a section for the Main Menu Logo
    $wp_customize->add_section( 'main_menu_logo_section', array(
        'title'    => __( 'Menu Logo', 'mytheme' ),
        'priority' => 30,
    ));
     // Add setting Main Menu Logo upload
     $wp_customize->add_setting( 'main_menu_logo_image', array(
        'default'   => '',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'main_menu_logo_image', array(
        'label'     => __( 'Upload Menu Logo', 'mytheme' ),
        'section'   => 'main_menu_logo_section',
        'settings'  => 'main_menu_logo_image',
    )));
    // CPT Customizer
     // Add setting for CPT name
     $wp_customize->add_section('custom_cpt_section', array(
        'title'    => __('Custom Post Type Settings', 'mytheme'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('cpt_name', array(
        'default' => 'Services',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('cpt_name', array(
        'label'    => __('Custom Post Type Name', 'mytheme'),
        'section'  => 'custom_cpt_section',
        'type'     => 'text',
    ));
    /** Contact Form 7 */
    // Add section for Contact Form
    $wp_customize->add_section('contact_form_section', array(
        'title' => 'Contact Form',
        'priority' => 30,
    ));

    // Headline
    $wp_customize->add_setting('contact_headline', array(
        'default' => 'Contact Us',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_headline', array(
        'label' => 'Headline',
        'section' => 'contact_form_section',
        'type' => 'text',
    ));

    // Subtext
    $wp_customize->add_setting('contact_subtext', array(
        'default' => 'We’d love to hear from you!',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('contact_subtext', array(
        'label' => 'Subtext',
        'section' => 'contact_form_section',
        'type' => 'text',
    ));

    // Color Overrides
    $wp_customize->add_setting('contact_primary_bg_color', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'contact_primary_bg_color', array(
        'label' => 'Primary Background Color',
        'section' => 'contact_form_section',
    )));

    $wp_customize->add_setting('contact_primary_text_color', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'contact_primary_text_color', array(
        'label' => 'Primary Text Color',
        'section' => 'contact_form_section',
    )));
}
add_action( 'customize_register', 'my_theme_customizer' );


/** CUSTOM POST TYPES */
function custom_post_type_services() {
    // Get the dynamic name from the Customizer
    $cpt_name = get_option('cpt_name', 'Services'); // Default to "Services" if not set

    $args = array(
        'labels' => array(
            'name'               => $cpt_name, 
            'singular_name'      => $cpt_name,
        ),
        'public' => true,
        'menu_icon' => 'dashicons-admin-tools',
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true, // Enable Gutenberg editor support
        'register_meta_box_cb' => 'add_services_metaboxes', // Add meta boxes callback
    );

    register_post_type('services', $args);
}
add_action('init', 'custom_post_type_services');

// Add meta boxes for services post type
function add_services_metaboxes() {
    add_meta_box(
        'service_settings',
        'Service Settings',
        'render_service_settings',
        'services',
        'normal',
        'high'
    );
    
    add_meta_box(
        'service_lottie',
        'Lottie Animation',
        'render_service_lottie',
        'services',
        'normal',
        'high'
    );
}

// Render service settings meta box
function render_service_settings($post) {
    // Add nonce for security
    wp_nonce_field('service_settings_nonce', 'service_settings_nonce');

    // Get saved values
    $layout = get_post_meta($post->ID, '_service_layout', true) ?: 'Left';
    $color_theme = get_post_meta($post->ID, '_service_color_theme', true) ?: 'Primary';

    // Output the form fields
    ?>
    <div class="service-settings">
        <p>
            <label for="service_layout">Layout:</label>
            <select name="service_layout" id="service_layout">
                <option value="Left" <?php selected($layout, 'Left'); ?>>Left</option>
                <option value="Right" <?php selected($layout, 'Right'); ?>>Right</option>
            </select>
        </p>
        <p>
            <label for="service_color_theme">Color Theme:</label>
            <select name="service_color_theme" id="service_color_theme">
                <option value="Primary" <?php selected($color_theme, 'Primary'); ?>>Primary</option>
                <option value="Secondary" <?php selected($color_theme, 'Secondary'); ?>>Secondary</option>
            </select>
        </p>
    </div>
    <style>
        .service-settings label {
            display: inline-block;
            min-width: 100px;
            font-weight: bold;
        }
        .service-settings select {
            min-width: 200px;
        }
    </style>
    <?php
}

// Save service meta box data
function save_service_settings($post_id) {
    // Check if our nonce is set and verify it
    if (!isset($_POST['service_settings_nonce']) || 
        !wp_verify_nonce($_POST['service_settings_nonce'], 'service_settings_nonce')) {
        return;
    }

    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the layout
    if (isset($_POST['service_layout'])) {
        update_post_meta(
            $post_id,
            '_service_layout',
            sanitize_text_field($_POST['service_layout'])
        );
    }

    // Save the color theme
    if (isset($_POST['service_color_theme'])) {
        update_post_meta(
            $post_id,
            '_service_color_theme',
            sanitize_text_field($_POST['service_color_theme'])
        );
    }
}
add_action('save_post_services', 'save_service_settings');

// Render Lottie file meta box
function render_service_lottie($post) {
    // Add nonce for security
    wp_nonce_field('service_lottie_nonce', 'service_lottie_nonce');

    // Get saved value
    $lottie_file = get_post_meta($post->ID, '_service_lottie_file', true);
    $lottie_url = $lottie_file ? wp_get_attachment_url($lottie_file) : '';

    // Output the form fields
    ?>
    <div class="service-lottie">
        <p>
            <label for="service_lottie_file">Lottie Animation File:</label>
            <input type="hidden" id="service_lottie_file" name="service_lottie_file" value="<?php echo esc_attr($lottie_file); ?>">
            <input type="text" id="service_lottie_file_url" value="<?php echo esc_url($lottie_url); ?>" readonly style="width: 80%;">
            <button type="button" class="button" id="upload_lottie_button">Upload File</button>
            <button type="button" class="button" id="remove_lottie_button" <?php echo empty($lottie_file) ? 'style="display:none;"' : ''; ?>>Remove File</button>
        </p>
    </div>
    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;

        $('#upload_lottie_button').click(function(e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Choose Lottie Animation File',
                button: {
                    text: 'Use this file'
                },
                library: {
                    type: 'application/json'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#service_lottie_file').val(attachment.id);
                $('#service_lottie_file_url').val(attachment.url);
                $('#remove_lottie_button').show();
            });

            mediaUploader.open();
        });

        $('#remove_lottie_button').click(function() {
            $('#service_lottie_file').val('');
            $('#service_lottie_file_url').val('');
            $(this).hide();
        });
    });
    </script>
    <style>
        .service-lottie label {
            display: inline-block;
            min-width: 120px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        #service_lottie_file_url {
            margin-right: 10px;
            margin-bottom: 5px;
        }
        #remove_lottie_button {
            margin-left: 5px;
        }
    </style>
    <?php
}

// Save Lottie file meta box data
function save_service_lottie($post_id) {
    // Check if our nonce is set and verify it
    if (!isset($_POST['service_lottie_nonce']) || 
        !wp_verify_nonce($_POST['service_lottie_nonce'], 'service_lottie_nonce')) {
        return;
    }

    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the Lottie file ID
    if (isset($_POST['service_lottie_file'])) {
        update_post_meta(
            $post_id,
            '_service_lottie_file',
            sanitize_text_field($_POST['service_lottie_file'])
        );
    }
}
add_action('save_post_services', 'save_service_lottie');

// Register Statistics Custom Post Type
function custom_post_type_statistics() {
    $labels = array(
        'name'               => 'Statistics',
        'singular_name'      => 'Statistic',
        'menu_name'          => 'Statistics',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Statistic',
        'edit_item'          => 'Edit Statistic',
        'new_item'           => 'New Statistic',
        'view_item'          => 'View Statistic',
        'search_items'       => 'Search Statistics',
        'not_found'          => 'No statistics found',
        'not_found_in_trash' => 'No statistics found in Trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-chart-area',
        'supports'            => array('title', 'editor', 'thumbnail'),
        'rewrite'            => array('slug' => 'statistics'),
        'show_in_nav_menus'  => false
    );

    register_post_type('statistics', $args);
}
add_action('init', 'custom_post_type_statistics');

// Add ACF fields for Statistics
// Add meta boxes for statistics post type
function add_statistics_metaboxes() {
    add_meta_box(
        'statistics_details',
        'Statistic Details',
        'render_statistics_details',
        'statistics',
        'normal',
        'high'
    );

    add_meta_box(
        'statistics_media',
        'Statistic Media',
        'render_statistics_media',
        'statistics',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_statistics_metaboxes');

// Render statistics details meta box
function render_statistics_details($post) {
    wp_nonce_field('statistics_details_nonce', 'statistics_details_nonce');

    $show_title = get_post_meta($post->ID, '_statistics_show_title', true);
    $show_title = $show_title !== '' ? $show_title : '1'; // Default to true
    $text = get_post_meta($post->ID, '_statistics_text', true);
    $source = get_post_meta($post->ID, '_statistics_source', true);

    ?>
    <div class="statistics-details">
        <p>
            <label>
                <input type="checkbox" name="statistics_show_title" value="1" <?php checked($show_title, '1'); ?>>
                Show Title
            </label>
        </p>
        <p>
            <label for="statistics_text">Text:</label><br>
            <input type="text" id="statistics_text" name="statistics_text" value="<?php echo esc_attr($text); ?>" style="width: 100%;">
        </p>
        <p>
            <label for="statistics_source">Source:</label><br>
            <input type="text" id="statistics_source" name="statistics_source" value="<?php echo esc_attr($source); ?>" style="width: 100%;">
            <span class="description">Enter the source of this statistic</span>
        </p>
    </div>
    <style>
        .statistics-details label {
            font-weight: bold;
        }
        .statistics-details .description {
            font-style: italic;
            color: #666;
        }
    </style>
    <?php
}

// Render statistics media meta box
function render_statistics_media($post) {
    wp_nonce_field('statistics_media_nonce', 'statistics_media_nonce');

    $media_type = get_post_meta($post->ID, '_statistics_media_type', true) ?: 'image';
    $image_id = get_post_meta($post->ID, '_statistics_image', true);
    $lottie_file_id = get_post_meta($post->ID, '_statistics_lottie_file', true);

    $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
    $lottie_url = $lottie_file_id ? wp_get_attachment_url($lottie_file_id) : '';
    ?>
    <div class="statistics-media">
        <p>
            <label for="statistics_media_type">Media Type:</label><br>
            <select name="statistics_media_type" id="statistics_media_type">
                <option value="image" <?php selected($media_type, 'image'); ?>>Image</option>
                <option value="lottie" <?php selected($media_type, 'lottie'); ?>>Lottie Animation</option>
            </select>
        </p>

        <div id="image_section" class="media-section" <?php echo $media_type !== 'image' ? 'style="display:none;"' : ''; ?>>
            <p>
                <label>Image:</label><br>
                <input type="hidden" id="statistics_image" name="statistics_image" value="<?php echo esc_attr($image_id); ?>">
                <img id="statistics_image_preview" src="<?php echo esc_url($image_url); ?>" style="max-width:200px;<?php echo empty($image_url) ? 'display:none;' : ''; ?>">
                <button type="button" class="button" id="upload_image_button">Upload Image</button>
                <button type="button" class="button" id="remove_image_button" <?php echo empty($image_id) ? 'style="display:none;"' : ''; ?>>Remove Image</button>
            </p>
        </div>

        <div id="lottie_section" class="media-section" <?php echo $media_type !== 'lottie' ? 'style="display:none;"' : ''; ?>>
            <p>
                <label>Lottie Animation File:</label><br>
                <input type="hidden" id="statistics_lottie_file" name="statistics_lottie_file" value="<?php echo esc_attr($lottie_file_id); ?>">
                <input type="text" id="statistics_lottie_url" value="<?php echo esc_url($lottie_url); ?>" readonly style="width: 80%;">
                <button type="button" class="button" id="upload_lottie_button">Upload File</button>
                <button type="button" class="button" id="remove_lottie_button" <?php echo empty($lottie_file_id) ? 'style="display:none;"' : ''; ?>>Remove File</button>
            </p>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;

        // Handle media type selection
        $('#statistics_media_type').change(function() {
            $('.media-section').hide();
            if (this.value === 'image') {
                $('#image_section').show();
            } else if (this.value === 'lottie') {
                $('#lottie_section').show();
            }
        });

        // Image upload
        $('#upload_image_button').click(function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#statistics_image').val(attachment.id);
                $('#statistics_image_preview').attr('src', attachment.url).show();
                $('#remove_image_button').show();
            });

            mediaUploader.open();
        });

        $('#remove_image_button').click(function() {
            $('#statistics_image').val('');
            $('#statistics_image_preview').attr('src', '').hide();
            $(this).hide();
        });

        // Lottie file upload
        $('#upload_lottie_button').click(function(e) {
            e.preventDefault();
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Choose Lottie Animation File',
                button: {
                    text: 'Use this file'
                },
                library: {
                    type: 'application/json'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#statistics_lottie_file').val(attachment.id);
                $('#statistics_lottie_url').val(attachment.url);
                $('#remove_lottie_button').show();
            });

            mediaUploader.open();
        });

        $('#remove_lottie_button').click(function() {
            $('#statistics_lottie_file').val('');
            $('#statistics_lottie_url').val('');
            $(this).hide();
        });
    });
    </script>

    <style>
        .statistics-media label {
            font-weight: bold;
        }
        .statistics-media select {
            min-width: 200px;
        }
        .statistics-media img {
            margin: 10px 0;
        }
        .statistics-media .button {
            margin-right: 5px;
        }
    </style>
    <?php
}

// Save statistics meta box data
function save_statistics_meta($post_id) {
    // Check nonces
    if ((!isset($_POST['statistics_details_nonce']) || !wp_verify_nonce($_POST['statistics_details_nonce'], 'statistics_details_nonce')) &&
        (!isset($_POST['statistics_media_nonce']) || !wp_verify_nonce($_POST['statistics_media_nonce'], 'statistics_media_nonce'))) {
        return;
    }

    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save details
    update_post_meta($post_id, '_statistics_show_title', isset($_POST['statistics_show_title']) ? '1' : '0');
    
    if (isset($_POST['statistics_text'])) {
        update_post_meta($post_id, '_statistics_text', sanitize_text_field($_POST['statistics_text']));
    }
    
    if (isset($_POST['statistics_source'])) {
        update_post_meta($post_id, '_statistics_source', sanitize_text_field($_POST['statistics_source']));
    }

    // Save media
    if (isset($_POST['statistics_media_type'])) {
        update_post_meta($post_id, '_statistics_media_type', sanitize_text_field($_POST['statistics_media_type']));
    }
    
    if (isset($_POST['statistics_image'])) {
        update_post_meta($post_id, '_statistics_image', sanitize_text_field($_POST['statistics_image']));
    }
    
    if (isset($_POST['statistics_lottie_file'])) {
        update_post_meta($post_id, '_statistics_lottie_file', sanitize_text_field($_POST['statistics_lottie_file']));
    }
}
add_action('save_post_statistics', 'save_statistics_meta');

// Register Info Text Custom Post Type
function custom_post_type_info_text() {
    $labels = array(
        'name'               => 'Info Texts',
        'singular_name'      => 'Info Text',
        'menu_name'          => 'Info Texts',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Info Text',
        'edit_item'          => 'Edit Info Text',
        'new_item'           => 'New Info Text',
        'view_item'          => 'View Info Text',
        'search_items'       => 'Search Info Texts',
        'not_found'          => 'No info texts found',
        'not_found_in_trash' => 'No info texts found in Trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-text',
        'supports'            => array('title', 'editor'),
        'rewrite'            => array('slug' => 'info-text'),
        'show_in_nav_menus'  => false
    );

    register_post_type('info_text', $args);
}
add_action('init', 'custom_post_type_info_text');

// Register Media Text Custom Post Type
function custom_post_type_media_text() {
    $labels = array(
        'name'               => 'Media Texts',
        'singular_name'      => 'Media Text',
        'menu_name'          => 'Media Texts',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Media Text',
        'edit_item'          => 'Edit Media Text',
        'new_item'           => 'New Media Text',
        'view_item'          => 'View Media Text',
        'search_items'       => 'Search Media Texts',
        'not_found'          => 'No media texts found',
        'not_found_in_trash' => 'No media texts found in Trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => false,
        'publicly_queryable'  => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-format-video',
        'supports'            => array('title'),
        'rewrite'            => array('slug' => 'media-text'),
        'show_in_nav_menus'  => false
    );

    register_post_type('media_text', $args);
}
add_action('init', 'custom_post_type_media_text');

// Register meta boxes for media text post type
function register_media_text_meta_boxes() {
    add_meta_box(
        'media_text_details',
        'Media Text Details',
        'render_media_text_meta_box',
        'media_text',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'register_media_text_meta_boxes');

// Render meta box for media text
function render_media_text_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('media_text_meta_box', 'media_text_meta_box_nonce');

    // Get current values
    $media_type = get_post_meta($post->ID, '_media_text_media_type', true) ?: 'image';
    $animated_text = get_post_meta($post->ID, '_media_text_animated_text', true);
    $image_id = get_post_meta($post->ID, '_media_text_image', true);
    $video_id = get_post_meta($post->ID, '_media_text_video', true);

    // Media Type
    echo '<p><label for="media_text_media_type">Media Type:</label><br>';
    echo '<select name="media_text_media_type" id="media_text_media_type">';
    echo '<option value="image"' . selected($media_type, 'image', false) . '>Image</option>';
    echo '<option value="video"' . selected($media_type, 'video', false) . '>Video</option>';
    echo '</select></p>';

    // Image Upload
    echo '<div class="media-upload-section' . ($media_type === 'image' ? '' : ' hidden') . '" id="image_section">';
    echo '<p><label>Image:</label><br>';
    echo '<input type="hidden" name="media_text_image" id="media_text_image" value="' . esc_attr($image_id) . '">';
    if ($image_id) {
        echo '<img src="' . esc_url(wp_get_attachment_url($image_id)) . '" style="max-width: 200px;">';
    }
    echo '<br><button type="button" class="upload-media-button button">Upload Image</button></p>';
    echo '</div>';

    // Video Upload
    echo '<div class="media-upload-section' . ($media_type === 'video' ? '' : ' hidden') . '" id="video_section">';
    echo '<p><label>Video:</label><br>';
    echo '<input type="hidden" name="media_text_video" id="media_text_video" value="' . esc_attr($video_id) . '">';
    if ($video_id) {
        echo '<video style="max-width: 200px;" controls><source src="' . esc_url(wp_get_attachment_url($video_id)) . '"></video>';
    }
    echo '<br><button type="button" class="upload-media-button button">Upload Video</button></p>';
    echo '</div>';

    // Animated Text
    echo '<p><label for="media_text_animated_text">Animated Text:</label><br>';
    echo '<textarea name="media_text_animated_text" id="media_text_animated_text" rows="5" style="width: 100%;">' . esc_textarea($animated_text) . '</textarea>';
    echo '<small>Enter each line of text on a new line.</small></p>';

    // Add JavaScript for media upload and type switching
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Handle media type switching
        $('#media_text_media_type').on('change', function() {
            $('.media-upload-section').addClass('hidden');
            if (this.value === 'image') {
                $('#image_section').removeClass('hidden');
            } else {
                $('#video_section').removeClass('hidden');
            }
        });

        // Handle media upload
        $('.upload-media-button').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var mediaType = $('#media_text_media_type').val();
            var frame = wp.media({
                title: 'Select ' + mediaType.charAt(0).toUpperCase() + mediaType.slice(1),
                multiple: false,
                library: { type: mediaType }
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                var container = button.closest('.media-upload-section');
                var input = container.find('input[type="hidden"]');
                input.val(attachment.id);

                // Update preview
                if (mediaType === 'image') {
                    container.find('img').remove();
                    container.find('.upload-media-button').before('<img src="' + attachment.url + '" style="max-width: 200px;"><br>');
                } else {
                    container.find('video').remove();
                    container.find('.upload-media-button').before('<video style="max-width: 200px;" controls><source src="' + attachment.url + '"></video><br>');
                }
            });

            frame.open();
        });
    });
    </script>
    <style>
    .hidden { display: none; }
    </style>
    <?php
}

// Save media text meta box data
function save_media_text_meta_box($post_id) {
    // Security checks
    if (!isset($_POST['media_text_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['media_text_meta_box_nonce'], 'media_text_meta_box') ||
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||
        !current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save media type
    if (isset($_POST['media_text_media_type'])) {
        update_post_meta($post_id, '_media_text_media_type', sanitize_text_field($_POST['media_text_media_type']));
    }

    // Save image ID
    if (isset($_POST['media_text_image'])) {
        update_post_meta($post_id, '_media_text_image', absint($_POST['media_text_image']));
    }

    // Save video ID
    if (isset($_POST['media_text_video'])) {
        update_post_meta($post_id, '_media_text_video', absint($_POST['media_text_video']));
    }

    // Save animated text
    if (isset($_POST['media_text_animated_text'])) {
        update_post_meta($post_id, '_media_text_animated_text', wp_kses_post($_POST['media_text_animated_text']));
    }
}
add_action('save_post_media_text', 'save_media_text_meta_box');

// Register Elementor Statistics Widget
function register_statistics_widget($widgets_manager) {
    require_once(__DIR__ . '/elementor/widgets/statistics-widget.php');
    $widgets_manager->register(new \Elementor_Statistics_Widget());
}
add_action('elementor/widgets/register', 'register_statistics_widget');

// Register Elementor Animated Line Widget
function register_animated_line_widget($widgets_manager) {
    require_once(__DIR__ . '/elementor/widgets/animated-line-widget.php');
    $widgets_manager->register(new \Elementor_Animated_Line_Widget());
}
add_action('elementor/widgets/register', 'register_animated_line_widget');

// Register Elementor Info Text Widget
function register_info_text_widget($widgets_manager) {
    require_once(__DIR__ . '/elementor/widgets/info-text-widget.php');
    $widgets_manager->register(new \Info_Text_Widget());
}
add_action('elementor/widgets/register', 'register_info_text_widget');

// Register Elementor Media Text Widget
function register_media_text_widget($widgets_manager) {
    require_once(__DIR__ . '/elementor/widgets/media-text-widget.php');
    $widgets_manager->register(new \Media_Text_Widget());
}
add_action('elementor/widgets/register', 'register_media_text_widget');

// Register Elementor Services Widget
function register_services_widget($widgets_manager) {
    require_once get_template_directory() . '/elementor/widgets/services-widget.php';
    $widgets_manager->register(new \Elementor_Services_Widget());
}
add_action('elementor/widgets/register', 'register_services_widget');

// Register Elementor Contact Form Widget
function register_contact_form_widget($widgets_manager) {
    require_once(__DIR__ . '/elementor/widgets/contact-form-widget.php');
    $widgets_manager->register(new Contact_Form_Widget());
}
add_action('elementor/widgets/register', 'register_contact_form_widget');

// Enqueue animated line script
function enqueue_animated_line_script() {
    wp_enqueue_script('animated-line', get_template_directory_uri() . '/assets/js/animated-line.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_animated_line_script');

// Enqueue animated words script
function enqueue_animated_words_script() {
    // Enqueue GSAP Core
    wp_enqueue_script('gsap-core', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js', array(), null, true);
    
    // Enqueue ScrollTrigger plugin
    wp_enqueue_script('gsap-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js', array('gsap-core'), null, true);
    
    // Enqueue our animation script
    wp_enqueue_script('animated-words', get_template_directory_uri() . '/assets/js/animated-words.js', array('gsap-core', 'gsap-scrolltrigger'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_animated_words_script');





/* Allow .json files in file upload */ 
function enqueue_lottie_assets() {
    // First load the Lottie library
    wp_enqueue_script('lottie-js', 'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js', array(), null, true);
    
    // Then load our custom Lottie scripts
    wp_enqueue_script('lottie-hover-script', get_template_directory_uri() . '/assets/js/lottie-hover.js', array('lottie-js'), null, true);
    wp_enqueue_script('custom-lottie', get_template_directory_uri() . '/assets/js/lottie-viewport.js', array('lottie-js'), null, true);
    
    // Pass the Lottie JSON file path to JavaScript
    wp_localize_script('lottie-hover-script', 'lottieData', array(
        'lottieArrowPath' => get_template_directory_uri() . '/assets/lottie/lottie-arrow.json'
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_lottie_assets');
// Lottie Short Code so Lottie Files can easily be re-used and used
function lottie_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'file' => '',      // Path to the JSON file
            'width' => '100%', // Default width
            'height' => '300px', // Default height
            'speed' => '1',    // Playback speed
            'loop' => 'true',  // Loop animation
            'autoplay' => 'true' // Auto start
        ), 
        $atts, 
        'lottie'
    );

    // Get the file path (local file in theme directory or full URL)
    $json_url = esc_url($atts['file']);

    // Check if the file path is provided
    if (empty($json_url)) {
        return '<p style="color:red;">Lottie file URL is missing.</p>';
    }

    // Enqueue Lottie script only once
    wp_enqueue_script('lottie-web', 'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js', array(), null, true);

    // Unique ID for multiple instances
    $unique_id = 'lottie-' . uniqid();

    // Output Lottie container with JavaScript
    ob_start(); ?>
    <div id="<?php echo esc_attr($unique_id); ?>" style="width: <?php echo esc_attr($atts['width']); ?>; height: <?php echo esc_attr($atts['height']); ?>;"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            lottie.loadAnimation({
                container: document.getElementById("<?php echo esc_attr($unique_id); ?>"),
                renderer: "svg",
                loop: <?php echo esc_attr($atts['loop']); ?>,
                autoplay: <?php echo esc_attr($atts['autoplay']); ?>,
                path: "<?php echo esc_url($json_url); ?>",
                rendererSettings: {
                    preserveAspectRatio: "xMidYMid meet"
                }
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('lottie', 'lottie_shortcode');

function allow_json_uploads($mime_types) {
    $mime_types['json'] = 'application/json'; // Allow JSON uploads
    return $mime_types;
}
add_filter('upload_mimes', 'allow_json_uploads');

// Allow SVG and JSON uploads
function allow_svg_and_json_uploads($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    $mimes['json'] = 'application/json';
    return $mimes;
}
add_filter('upload_mimes', 'allow_svg_and_json_uploads');

// Fix SVG and JSON upload checks
function fix_upload_checks($data, $file, $filename, $mimes, $real_mime) {
    if (strpos($filename, '.json') !== false) {
        $data['ext'] = 'json';
        $data['type'] = 'application/json';
    }
    
    if (strpos($filename, '.svg') !== false) {
        $data['ext'] = 'svg';
        $data['type'] = 'image/svg+xml';
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'fix_upload_checks', 10, 5);

// Add SVG dimensions support
function fix_svg_size_attributes($image, $attachment_id, $size, $icon) {
    if (is_array($image) && preg_match('/\.svg$/i', $image[0]) && $image[1] <= 1) {
        try {
            $svg_file = get_attached_file($attachment_id);
            if ($svg_file) {
                $svg = simplexml_load_file($svg_file);
                $attrs = $svg->attributes();
                $viewbox = explode(' ', $attrs->viewBox);
                $image[1] = isset($attrs->width) && preg_match('/^[\d.]+$/', $attrs->width) ? (int) $attrs->width : (count($viewbox) == 4 ? (int) $viewbox[2] : null);
                $image[2] = isset($attrs->height) && preg_match('/^[\d.]+$/', $attrs->height) ? (int) $attrs->height : (count($viewbox) == 4 ? (int) $viewbox[3] : null);
            }
        } catch (Exception $e) {}
    }
    return $image;
}
add_filter('wp_get_attachment_image_src', 'fix_svg_size_attributes', 10, 4);

/* Plugins used in Theme */
function mytheme_recommend_plugins() {
    if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        echo '<div class="notice notice-warning"><p>Install and activate <strong>Contact Form 7</strong> for full contact form functionality.</p></div>';
    }
    if (!is_plugin_active('advanced-custom-fields/acf.php') && !is_plugin_active('advanced-custom-fields-pro/acf.php')) {
        echo '<div class="notice notice-warning"><p>Install and activate <strong>Advanced Custom Fields</strong> for full theme functionality.</p></div>';
    }
    if (!is_plugin_active('post-types-order/post-types-order.php')) {
        echo '<div class="notice notice-error"><p>Install and activate <strong>Post Types Order</strong> plugin. This plugin is required for proper post ordering functionality in the theme.</p></div>';
    }
}
add_action('admin_notices', 'mytheme_recommend_plugins');


/** Contact Form */
function create_custom_contact_form7() {
    // Check if Contact Form 7 is active
    if (!is_plugin_active('contact-form-7/wp-contact-form-7.php')) {
        return;
    }
    // Check if the form has already been created
    $form_created = get_theme_mod('custom_contact_form_created', false);

    if (!$form_created) {
        // Check if the form already exists
        $form_title = 'Custom Contact Form';
        $form = get_page_by_title($form_title, OBJECT, 'wpcf7_contact_form');

        if (!$form) {
            // Form content
            $form_content = '
            
                <div class="form-grid">
                    <div class="form-column">
                        [text* first-name placeholder "First Name"]
                        [email* email placeholder "Email"]
                        [text* subject placeholder "Subject"]
                    </div>
                    <div class="form-column">
                        [text* last-name placeholder "Last Name"]
                        [tel* phone placeholder "Phone Number"]
                    </div>
                </div>
                [textarea* message placeholder "Your Message"]
                [submit "Contact Us"]
            
            ';

            // Create the form
            $form_id = wp_insert_post(array(
                'post_title' => $form_title,
                'post_content' => $form_content,
                'post_type' => 'wpcf7_contact_form',
                'post_status' => 'publish',
            ));

            // Set form settings
            if ($form_id) {
                update_post_meta($form_id, '_form', $form_content);
                update_post_meta($form_id, '_mail', array(
                    'active' => true,
                    'subject' => '[your-subject]',
                    'sender' => '[your-name] <[your-email]>',
                    'body' => 'From: [your-name] <[your-email]>\nSubject: [your-subject]\n\nMessage Body:\n[your-message]\n\n--\nThis e-mail was sent from a contact form on your website.',
                    'recipient' => get_option('admin_email'),
                    'additional_headers' => 'Reply-To: [your-email]',
                    'attachments' => '',
                    'use_html' => false,
                    'exclude_blank' => false,
                ));
                update_post_meta($form_id, '_mail_2', array(
                    'active' => false,
                ));
                update_post_meta($form_id, '_messages', array(
                    'mail_sent_ok' => 'Thank you for your message. It has been sent.',
                    'mail_sent_ng' => 'There was an error trying to send your message. Please try again later.',
                    'validation_error' => 'One or more fields have an error. Please check and try again.',
                    'spam' => 'There was an error trying to send your message. Please try again later.',
                    'acceptance_missing' => 'Please accept the terms to proceed.',
                    'invalid_required' => 'Please fill out this field.',
                    'invalid_too_long' => 'This field is too long.',
                    'invalid_too_short' => 'This field is too short.',
                ));
            }
        }

        // Mark the form as created
        set_theme_mod('custom_contact_form_created', true);
    }
}
add_action('init', 'create_custom_contact_form7');
function custom_contact_form7() {
    // Retrieve global colors
    global $primary_bg_color, $secondary_bg_color, $third_bg_color;
    global $primary_text_color, $secondary_text_color, $third_text_color;
    global $link_color, $link_hover_color, $highlight_color;

    // Retrieve headline and subtext from theme options
    $headline = get_theme_mod('contact_headline', 'Contact Us');
    $subtext = get_theme_mod('contact_subtext', 'We’d love to hear from you!');

    // Get the form by title
    $form_title = 'Custom Contact Form';
    $form = get_page_by_title($form_title, OBJECT, 'wpcf7_contact_form');

    if ($form) {
        // Replace placeholders in the Contact Form 7 template
        $form_output = '
        <section id="get-in-touch">
        
        
        <div class="custom-contact-form fade-in-bottom" style="background-color: ' . esc_attr($primary_bg_color) . '; color: ' . esc_attr($primary_text_color) . ';">
            <h2 class="form-headline">' . esc_html($headline) . '</h2>
            <p  class="form-subtext">' . esc_html($subtext) . '</p>
            ' . do_shortcode('[contact-form-7 id="' . $form->ID . '" title="' . $form_title . '"]') . '
    
        </div>
        </section>
        ';

        return $form_output;
    } else {
        return '<p>Contact form not found.</p>';
    }
}
add_shortcode('custom_contact_form', 'custom_contact_form7');

// AJAX handlers for media tags
function add_media_tag() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'project_gallery_media_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $attachment_id = intval($_POST['attachment_id']);
    $tag = sanitize_text_field($_POST['tag']);

    if (!$attachment_id || !$tag) {
        wp_send_json_error('Invalid data');
    }

    // Check if tag already exists on this attachment
    $existing_tags = wp_get_object_terms($attachment_id, 'project_tags', array('fields' => 'names'));
    
    if (in_array($tag, array_map('strtolower', $existing_tags))) {
        wp_send_json_error('Tag already exists');
        return;
    }

    $result = wp_set_object_terms($attachment_id, $tag, 'project_tags', true);
    
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    wp_send_json_success();
}
add_action('wp_ajax_add_media_tag', 'add_media_tag');

function remove_media_tag() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'project_gallery_media_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $attachment_id = intval($_POST['attachment_id']);
    $tag = sanitize_text_field($_POST['tag']);

    if (!$attachment_id || !$tag) {
        wp_send_json_error('Invalid data');
    }

    $result = wp_remove_object_terms($attachment_id, $tag, 'project_tags');
    
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    wp_send_json_success();
}
add_action('wp_ajax_remove_media_tag', 'remove_media_tag');

// AJAX handler for tag suggestions
function get_tag_suggestions() {
    if (!isset($_GET['term'])) {
        wp_send_json_error('No search term provided');
    }

    $term = sanitize_text_field($_GET['term']);
    
    $tags = get_terms(array(
        'taxonomy' => 'project_tags',
        'hide_empty' => false,
        'search' => $term,
        'number' => 10
    ));

    $suggestions = array();
    foreach ($tags as $tag) {
        $suggestions[] = array(
            'value' => $tag->name,
            'label' => $tag->name
        );
    }

    wp_send_json_success($suggestions);
}
add_action('wp_ajax_get_tag_suggestions', 'get_tag_suggestions');

/**
 * Ensure correct post type is queried on portfolio archive
 */
function modify_project_gallery_archive_query($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('project_gallery')) {
        $query->set('post_type', 'project_gallery');
        $query->set('posts_per_page', 12); // Adjust this number as needed
    }
}
add_action('pre_get_posts', 'modify_project_gallery_archive_query');


?>