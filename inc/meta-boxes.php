<?php
/**
 * Register meta boxes for the theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register meta boxes for project gallery
 */
function register_project_gallery_meta_boxes() {
    add_meta_box(
        'project_gallery_media',
        __('Project Media Gallery', 'filmestate'),
        'render_project_gallery_media_box',
        'project_gallery',
        'normal',
        'high'
    );

    add_meta_box(
        'project_gallery_display_mode',
        __('Gallery Display Mode', 'filmestate'),
        'render_project_gallery_display_mode_box',
        'project_gallery',
        'side',
        'high'
    );

    add_meta_box(
        'project_gallery_info_boxes',
        __('Project Info Boxes', 'filmestate'),
        'render_project_gallery_info_boxes',
        'project_gallery',
        'normal',
        'high'
    );
}

/**
 * Enqueue necessary scripts and styles for the admin
 */
function enqueue_project_gallery_admin_scripts($hook) {
    global $post;

    // Only enqueue on post.php and post-new.php for project_gallery post type
    if (!($hook == 'post.php' || $hook == 'post-new.php') || 
        !is_object($post) || $post->post_type !== 'project_gallery') {
        return;
    }

    // Enqueue WordPress media scripts
    wp_enqueue_media();

    // Enqueue MediaElement.js
    wp_enqueue_style('mediaelement');
    wp_enqueue_script('mediaelement');
    wp_enqueue_script('wp-mediaelement');

    // Enqueue jQuery UI
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('jquery-ui-autocomplete');
    
    // Enqueue jQuery UI styles
    wp_enqueue_style('wp-jquery-ui-dialog');

    // Enqueue our custom admin script
    wp_enqueue_script('project-gallery-admin',
        get_template_directory_uri() . '/assets/js/admin.js',
        array('jquery', 'jquery-ui-sortable', 'media-upload', 'mediaelement', 'wp-mediaelement'),
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'enqueue_project_gallery_admin_scripts');
add_action('add_meta_boxes', 'register_project_gallery_meta_boxes');

/**
 * Render the media gallery meta box
 */
function render_project_gallery_media_box($post) {
    // Add nonce for security
    wp_nonce_field('project_gallery_media_nonce', 'project_gallery_media_nonce');

    // Get saved media IDs
    $media_ids = get_post_meta($post->ID, '_project_gallery_media', true);
    $media_ids = $media_ids ? explode(',', $media_ids) : array();

    // Output the media gallery interface
    ?>
    <div class="pg project-gallery-media-wrapper">
        <style>
            .project-gallery-media-wrapper {
                padding: 10px;
                background: #fff;
                border-radius: 3px;
            }
            #project-gallery-media-container {
                margin: 15px 0;
                min-height: 100px;
                border: 2px dashed #ddd;
                padding: 15px;
                background: #fafafa;
            }
            .project-gallery-media-wrapper .button {
                margin-top: 10px;
            }
            .project-gallery-instructions {
                margin-bottom: 15px;
                color: #666;
            }
        </style>

        <div class="project-gallery-instructions">
            <p><?php _e('Drag and drop to reorder media items. Click "Add Media" to upload new images or videos.', 'filmestate'); ?></p>
        </div>

        <div id="project-gallery-media-container" class="sortable-media-container">
            <?php
            if (!empty($media_ids)) {
                foreach ($media_ids as $media_id) {
                    if (!$media_id) continue;

                    $type = wp_attachment_is('video', $media_id) ? 'video' : 'image';
                    $thumbnail = false;

                    if ($type === 'video') {
                        // Try to get video thumbnail in multiple ways
                        $thumbnail = false;
                        
                        // First try: Check if video has a poster/thumbnail set
                        $poster = get_post_meta($media_id, '_thumbnail_id', true);
                        if ($poster) {
                            $thumbnail = wp_get_attachment_image_src($poster, 'thumbnail');
                        }
                        
                        // Second try: Get the first frame as thumbnail
                        if (!$thumbnail) {
                            $video_metadata = wp_get_attachment_metadata($media_id);
                            if (!empty($video_metadata['thumbnail'])) {
                                $upload_dir = wp_upload_dir();
                                $thumbnail = array(
                                    $upload_dir['baseurl'] . '/' . $video_metadata['thumbnail'],
                                    150,
                                    150
                                );
                            }
                        }
                        
                        // Third try: Use default video icon
                        if (!$thumbnail) {
                            $thumbnail = array(
                                includes_url('images/media/video.png'),
                                150,
                                150
                            );
                        }
                    } else {
                        $thumbnail = wp_get_attachment_image_src($media_id, 'thumbnail');
                    }

                    if (!$thumbnail) continue;

                    $title = get_the_title($media_id);
                    ?>
                    <div class="media-item" data-id="<?php echo esc_attr($media_id); ?>" data-type="<?php echo esc_attr($type); ?>">
                        <?php if ($type === 'video'): 
                            $video_url = wp_get_attachment_url($media_id);
                        ?>
                        <div class="media-preview">
                            <video class="wp-video-shortcode" preload="metadata" controls="controls">
                                <source type="<?php echo esc_attr(get_post_mime_type($media_id)); ?>" src="<?php echo esc_url($video_url); ?>" />
                            </video>
                        </div>
                        <?php else: ?>
                            <div class="media-preview">
                                <img src="<?php echo esc_url($thumbnail[0]); ?>" alt="<?php echo esc_attr($title); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="media-tags">
                            <input type="text" class="media-tag-input" placeholder="<?php esc_attr_e('Add tags...', 'filmestate'); ?>" data-attachment-id="<?php echo esc_attr($media_id); ?>">
                            <div class="media-tag-list">
                                <?php
                                $tags = wp_get_object_terms($media_id, 'project_tags');
                                foreach ($tags as $tag) {
                                    echo '<span class="media-tag">' . esc_html($tag->name) . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <span class="media-type"><?php echo esc_html(ucfirst($type)); ?></span>
                        <button type="button" class="remove-media" title="<?php esc_attr_e('Remove', 'filmestate'); ?>">&times;</button>
                    </div>
                    <?php
                }
            }
            ?>
        </div>

        <input type="hidden" name="project_gallery_media" id="project-gallery-media" value="<?php echo esc_attr(implode(',', $media_ids)); ?>">
        <button type="button" class="button button-primary" id="add-project-media">
            <span class="dashicons dashicons-plus" style="margin: 4px 5px 0 -2px;"></span>
            <?php _e('Add Media', 'filmestate'); ?>
        </button>
    </div>
    <?php
}

/**
 * Save the media gallery data
 */
function save_project_gallery_meta($post_id) {
    // Security checks
    if (!isset($_POST['project_gallery_media_nonce']) || 
        !wp_verify_nonce($_POST['project_gallery_media_nonce'], 'project_gallery_media_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save media IDs
    if (isset($_POST['project_gallery_media'])) {
        update_post_meta($post_id, '_project_gallery_media', sanitize_text_field($_POST['project_gallery_media']));
    }
}
add_action('save_post_project_gallery', 'save_project_gallery_meta');

/**
 * Render the display mode meta box
 */
function render_project_gallery_display_mode_box($post) {
    wp_nonce_field('project_gallery_display_mode_nonce', 'project_gallery_display_mode_nonce');
    $display_mode = get_post_meta($post->ID, '_project_gallery_display_mode', true) ?: 'square';
    ?>
    <div class="display-mode-selector">
        <p><strong><?php _e('Select how the gallery should be displayed:', 'filmestate'); ?></strong></p>
        <label>
            <input type="radio" name="project_gallery_display_mode" value="square" <?php checked($display_mode, 'square'); ?>>
            <i class="fas fa-th"></i> <?php _e('Square Grid', 'filmestate'); ?>
        </label><br>
        <label>
            <input type="radio" name="project_gallery_display_mode" value="full" <?php checked($display_mode, 'full'); ?>>
            <i class="fas fa-bars"></i> <?php _e('Full Width', 'filmestate'); ?>
        </label><br>
        <label>
            <input type="radio" name="project_gallery_display_mode" value="masonry" <?php checked($display_mode, 'masonry'); ?>>
            <i class="fas fa-th-large"></i> <?php _e('Masonry Grid', 'filmestate'); ?>
        </label><br>
        <label>
            <input type="radio" name="project_gallery_display_mode" value="pinterest" <?php checked($display_mode, 'pinterest'); ?>>
            <i class="fab fa-pinterest"></i> <?php _e('Pinterest Style', 'filmestate'); ?>
        </label>
    </div>
    <style>
        .display-mode-selector label {
            display: block;
            margin: 8px 0;
            cursor: pointer;
        }
        .display-mode-selector i {
            width: 20px;
            color: #666;
        }
    </style>
    <?php
}

/**
 * Save the display mode meta box data
 */
function save_project_gallery_display_mode($post_id) {
    if (!isset($_POST['project_gallery_display_mode_nonce']) ||
        !wp_verify_nonce($_POST['project_gallery_display_mode_nonce'], 'project_gallery_display_mode_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['project_gallery_display_mode'])) {
        update_post_meta(
            $post_id,
            '_project_gallery_display_mode',
            sanitize_text_field($_POST['project_gallery_display_mode'])
        );
    }
}
add_action('save_post_project_gallery', 'save_project_gallery_display_mode');

/**
 * Render the info boxes meta box
 */
function render_project_gallery_info_boxes($post) {
    wp_nonce_field('project_gallery_info_boxes_nonce', 'project_gallery_info_boxes_nonce');

    // Get saved values
    $info_boxes = array();
    for ($i = 1; $i <= 3; $i++) {
        $info_boxes[$i] = array(
            'headline' => get_post_meta($post->ID, '_project_gallery_info_box_' . $i . '_headline', true),
            'content' => get_post_meta($post->ID, '_project_gallery_info_box_' . $i . '_content', true)
        );
    }
    ?>
    <div class="info-boxes-wrapper">
        <div class="info-boxes-grid">
            <?php for ($i = 1; $i <= 3; $i++) : ?>
                <div class="info-box-section">
                    <h4><?php printf(__('Info Box %d', 'filmestate'), $i); ?></h4>
                    <p>
                        <label for="project_gallery_info_box_<?php echo $i; ?>_headline"><?php _e('Headline:', 'filmestate'); ?></label>
                        <input type="text" id="project_gallery_info_box_<?php echo $i; ?>_headline" 
                               name="project_gallery_info_box_<?php echo $i; ?>_headline" 
                               value="<?php echo esc_attr($info_boxes[$i]['headline']); ?>" 
                               class="widefat">
                    </p>
                    <p>
                        <label for="project_gallery_info_box_<?php echo $i; ?>_content"><?php _e('Content:', 'filmestate'); ?></label>
                        <textarea id="project_gallery_info_box_<?php echo $i; ?>_content" 
                                  name="project_gallery_info_box_<?php echo $i; ?>_content" 
                                  class="widefat" rows="4"><?php echo esc_textarea($info_boxes[$i]['content']); ?></textarea>
                    </p>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <style>
        .info-boxes-wrapper {
            padding: 10px;
            margin: -6px -12px -12px;
        }
        .info-boxes-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .info-box-section {
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 3px;
            padding: 15px;
        }
        .info-box-section h4 {
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e5e5;
        }
        @media screen and (max-width: 1200px) {
            .info-boxes-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media screen and (max-width: 782px) {
            .info-boxes-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php
}

/**
 * Save the info boxes meta box data
 */
function save_project_gallery_info_boxes($post_id) {
    // Check if our nonce is set
    if (!isset($_POST['project_gallery_info_boxes_nonce'])) {
        return;
    }

    // Verify that the nonce is valid
    if (!wp_verify_nonce($_POST['project_gallery_info_boxes_nonce'], 'project_gallery_info_boxes_nonce')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save info boxes data
    for ($i = 1; $i <= 3; $i++) {
        $headline_key = 'project_gallery_info_box_' . $i . '_headline';
        $content_key = 'project_gallery_info_box_' . $i . '_content';

        if (isset($_POST[$headline_key])) {
            update_post_meta($post_id, '_' . $headline_key, sanitize_text_field($_POST[$headline_key]));
        }
        if (isset($_POST[$content_key])) {
            update_post_meta($post_id, '_' . $content_key, wp_kses_post($_POST[$content_key]));
        }
    }
}
add_action('save_post_project_gallery', 'save_project_gallery_info_boxes');
