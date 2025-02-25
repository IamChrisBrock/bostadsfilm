<?php
/**
 * Gallery Page Settings
 * 
 * Handles gallery page settings in the WordPress admin area
 * 
 * File Dependencies:
 * - template-gallery.php (Uses settings for display)
 * - template-parts/gallery-grid.php (Uses settings for grid layout)
 * 
 * Used by:
 * - WordPress admin area for gallery pages
 * - Gallery templates for display options
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add meta box for gallery page settings
 */
function add_gallery_settings_meta_box() {
    add_meta_box(
        'gallery_settings',
        __('Gallery Settings', 'filmestate'),
        'render_gallery_settings_meta_box',
        'page',
        'normal',
        'high',
        array('template' => 'page-templates/template-gallery.php')
    );
}
add_action('add_meta_boxes', 'add_gallery_settings_meta_box');

/**
 * Render gallery settings meta box
 */
function render_gallery_settings_meta_box($post) {
    // Only show for gallery template
    if (get_page_template_slug($post->ID) !== 'page-templates/template-gallery.php') {
        echo '<p>' . __('These settings are only available for the Gallery Page template.', 'filmestate') . '</p>';
        return;
    }

    wp_nonce_field('gallery_settings_meta_box', 'gallery_settings_meta_box_nonce');

    // Get saved values
    $posts_per_page = get_post_meta($post->ID, '_gallery_posts_per_page', true);
    $columns = get_post_meta($post->ID, '_gallery_columns', true) ?: 3;
    $order = get_post_meta($post->ID, '_gallery_order', true) ?: 'DESC';
    $orderby = get_post_meta($post->ID, '_gallery_orderby', true) ?: 'date';
    $thumbnail_style = get_post_meta($post->ID, '_gallery_thumbnail_style', true) ?: '16:9';
    ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="gallery_posts_per_page"><?php _e('Galleries Per Page', 'filmestate'); ?></label>
            </th>
            <td>
                <input type="number" 
                       id="gallery_posts_per_page" 
                       name="gallery_posts_per_page" 
                       value="<?php echo esc_attr($posts_per_page); ?>" 
                       min="-1" 
                       class="small-text">
                <p class="description">
                    <?php _e('Number of galleries to show per page. Use -1 to show all.', 'filmestate'); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="gallery_columns"><?php _e('Columns', 'filmestate'); ?></label>
            </th>
            <td>
                <select id="gallery_columns" name="gallery_columns">
                    <?php for ($i = 2; $i <= 4; $i++) : ?>
                        <option value="<?php echo $i; ?>" <?php selected($columns, $i); ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="gallery_orderby"><?php _e('Order By', 'filmestate'); ?></label>
            </th>
            <td>
                <select id="gallery_orderby" name="gallery_orderby">
                    <option value="date" <?php selected($orderby, 'date'); ?>><?php _e('Date', 'filmestate'); ?></option>
                    <option value="title" <?php selected($orderby, 'title'); ?>><?php _e('Title', 'filmestate'); ?></option>
                    <option value="menu_order" <?php selected($orderby, 'menu_order'); ?>><?php _e('Menu Order', 'filmestate'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="gallery_order"><?php _e('Order', 'filmestate'); ?></label>
            </th>
            <td>
                <select id="gallery_order" name="gallery_order">
                    <option value="DESC" <?php selected($order, 'DESC'); ?>><?php _e('Descending', 'filmestate'); ?></option>
                    <option value="ASC" <?php selected($order, 'ASC'); ?>><?php _e('Ascending', 'filmestate'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="gallery_thumbnail_style"><?php _e('Thumbnail Style', 'filmestate'); ?></label>
            </th>
            <td>
                <select id="gallery_thumbnail_style" name="gallery_thumbnail_style">
                    <option value="16:9" <?php selected($thumbnail_style, '16:9'); ?>><?php _e('16:9 Widescreen', 'filmestate'); ?></option>
                    <option value="square" <?php selected($thumbnail_style, 'square'); ?>><?php _e('Square', 'filmestate'); ?></option>
                </select>
                <p class="description"><?php _e('Choose the aspect ratio for gallery thumbnails.', 'filmestate'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save gallery settings
 */
function save_gallery_settings_meta($post_id) {
    // Security checks
    if (!isset($_POST['gallery_settings_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['gallery_settings_meta_box_nonce'], 'gallery_settings_meta_box')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Only save for gallery template
    if (get_page_template_slug($post_id) !== 'page-templates/template-gallery.php') {
        return;
    }

    // Save settings
    $fields = array(
        'gallery_posts_per_page' => 'intval',
        'gallery_columns' => 'intval',
        'gallery_order' => 'sanitize_text_field',
        'gallery_orderby' => 'sanitize_text_field',
        'gallery_thumbnail_style' => 'sanitize_text_field'
    );

    foreach ($fields as $field => $sanitize_callback) {
        if (isset($_POST[$field])) {
            $value = call_user_func($sanitize_callback, $_POST[$field]);
            update_post_meta($post_id, '_' . $field, $value);
        }
    }
}
add_action('save_post', 'save_gallery_settings_meta');
