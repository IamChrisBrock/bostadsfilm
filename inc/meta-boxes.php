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

    // Get selected thumbnail ID
    $selected_thumbnail_id = get_post_meta($post->ID, '_gallery_thumbnail_id', true);

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
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }
            .media-item {
                flex: 0 0 300px;
                background: #fff;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .text-block {
                flex: 0 0 300px;
            }
            .text-block .wp-editor-wrap {
                margin-bottom: 10px;
            }
            .media-preview {
                width: 100%;
                background: #f0f0f0;
                margin-bottom: 10px;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .media-preview img,
            .media-preview video {
                max-width: 100%;
                height: auto;
                display: block;
            }
            .project-gallery-toolbar {
                margin-bottom: 15px;
                display: flex;
                gap: 10px;
            }
            .project-gallery-media-wrapper .button {
                margin: 0;
            }
            .project-gallery-instructions {
                margin-bottom: 15px;
                color: #666;
            }
            .media-thumbnail-selection {
                margin-top: 10px;
                padding: 5px 0;
                border-top: 1px solid #eee;
            }
            .media-thumbnail-selection label {
                display: flex;
                align-items: center;
                gap: 5px;
                color: #666;
                font-size: 12px;
            }
        </style>

        <div class="project-gallery-toolbar">
            <button type="button" class="button" id="add-project-media">Add Media</button>
            <button type="button" class="button add-text">Add Text</button>
        </div>

        <div class="project-gallery-instructions">
            <p><?php _e('Drag and drop to reorder items. Add media or text blocks to create your gallery.', 'filmestate'); ?></p>
        </div>

        <div id="project-gallery-media-container" class="sortable-media-container">
            <?php
            if (!empty($media_ids)) {
                foreach ($media_ids as $item_id) {
                    if (!$item_id) continue;

                    // Determine content type
                    $content_type = 'media'; // Default type
                    if (strpos($item_id, 'text_') === 0) {
                        $content_type = 'text';
                    } else {
                        $content_type = wp_attachment_is('video', $item_id) ? 'video' : 'image';
                    }

                    // Handle text blocks
                    if ($content_type === 'text') {
                        $content = get_post_meta($post->ID, '_text_block_' . $item_id, true);
                        if ($content) {
                            ?>
                            <div class="media-item text-block" data-id="<?php echo esc_attr($item_id); ?>">
                                <div class="text-block-content">
                                    <textarea id="<?php echo esc_attr($item_id); ?>" name="text_block_<?php echo esc_attr($item_id); ?>"><?php echo esc_textarea($content); ?></textarea>
                                </div>
                                <div class="media-item-actions">
                                    <button type="button" class="button remove-item">Remove</button>
                                </div>
                            </div>
                            <?php
                            
                            // Initialize the editor after rendering
                            ?>
                            <script>
                            jQuery(document).ready(function($) {
                                wp.editor.initialize('<?php echo esc_js($item_id); ?>', {
                                    tinymce: {
                                        toolbar1: 'formatselect bold italic | alignleft aligncenter alignright | bullist numlist | link',
                                        block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3',
                                        height: 200,
                                        menubar: false,
                                        setup: function(editor) {
                                            editor.on('change', function() {
                                                var content = editor.getContent();
                                                $('#<?php echo esc_js($item_id); ?>').val(content);
                                                updateMediaOrder();
                                            });
                                        }
                                    },
                                    quicktags: true,
                                    mediaButtons: false
                                });
                            });
                            </script>
                            <?php
                            continue;
                        }
                    }

                    // Get media information
                    $attachment = get_post($item_id);
                    if (!$attachment) continue;

                    $thumbnail = false;
                    
                    if ($content_type === 'video') {
                        // Try to get video thumbnail in multiple ways
                        $thumbnail = false;
                        
                        // First try: Check if video has a poster/thumbnail set
                        $poster = get_post_meta($item_id, '_thumbnail_id', true);
                        if ($poster) {
                            $thumbnail = wp_get_attachment_image_src($poster, 'medium');
                        }
                        
                        // Second try: Get the first frame as thumbnail
                        if (!$thumbnail) {
                            $video_metadata = wp_get_attachment_metadata($item_id);
                            if (!empty($video_metadata['thumbnail'])) {
                                $upload_dir = wp_upload_dir();
                                $thumbnail = array(
                                    $upload_dir['baseurl'] . '/' . $video_metadata['thumbnail'],
                                    150,
                                    150
                                );
                            }
                        }

                        // If still no thumbnail for video, use default image
                        if (!$thumbnail) {
                            $thumbnail = array(wp_mime_type_icon($item_id), 64, 64);
                        }
                    } else if ($content_type === 'image') {
                        $thumbnail = wp_get_attachment_image_src($item_id, 'medium');
                    }

                    if (!$thumbnail) continue;

                    // Render media item
                    ?>
                    <div class="media-item" data-id="<?php echo esc_attr($item_id); ?>" data-type="<?php echo esc_attr($content_type); ?>">
                        <div class="media-preview">
                            <?php if ($content_type === 'video'): ?>
                                <div class="video-preview">
                                    <video preload="metadata" controls>
                                        <source src="<?php echo esc_url(wp_get_attachment_url($item_id)); ?>" type="<?php echo esc_attr($attachment->post_mime_type); ?>">
                                    </video>
                                </div>
                            <?php else: ?>
                                <img src="<?php echo esc_url($thumbnail[0]); ?>" alt="<?php echo esc_attr(get_the_title($item_id)); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="media-thumbnail-selection">
                            <label>
                                <input type="radio" name="gallery_thumbnail" value="<?php echo esc_attr($item_id); ?>" <?php checked($selected_thumbnail_id, $item_id); ?>>
                                <?php _e('Use as gallery thumbnail', 'filmestate'); ?>
                            </label>
                        </div>
                        <span class="media-type"><?php echo esc_html(ucfirst($content_type)); ?></span>
                        <button type="button" class="remove-item" title="<?php esc_attr_e('Remove', 'filmestate'); ?>">&times;</button>
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

    // Save media IDs and text blocks
    if (isset($_POST['project_gallery_media'])) {
        $media_ids = sanitize_text_field($_POST['project_gallery_media']);
        update_post_meta($post_id, '_project_gallery_media', $media_ids);
        
        // Save text blocks
        $media_ids_array = explode(',', $media_ids);
        foreach ($media_ids_array as $item_id) {
            if (strpos($item_id, 'text_') === 0) {
                $content_key = 'text_block_' . $item_id;
                if (isset($_POST[$content_key])) {
                    $content = wp_kses_post($_POST[$content_key]);
                    update_post_meta($post_id, '_' . $content_key, $content);
                }
            }
        }
    }

    // Save gallery thumbnail selection
    if (isset($_POST['gallery_thumbnail'])) {
        update_post_meta($post_id, '_gallery_thumbnail_id', sanitize_text_field($_POST['gallery_thumbnail']));
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
        <?php for ($i = 1; $i <= 3; $i++) : ?>
            <div class="info-box-section">
                <h4><?php printf(__('Info Box %d', 'filmestate'), $i); ?></h4>
                <p>
                    <label for="project_gallery_info_box_<?php echo $i; ?>_headline"><?php _e('Headline:', 'filmestate'); ?></label><br>
                    <input type="text" id="project_gallery_info_box_<?php echo $i; ?>_headline" 
                           name="project_gallery_info_box_<?php echo $i; ?>_headline" 
                           value="<?php echo esc_attr($info_boxes[$i]['headline']); ?>" 
                           class="widefat">
                </p>
                <p>
                    <label for="project_gallery_info_box_<?php echo $i; ?>_content"><?php _e('Content:', 'filmestate'); ?></label><br>
                    <textarea id="project_gallery_info_box_<?php echo $i; ?>_content" 
                              name="project_gallery_info_box_<?php echo $i; ?>_content" 
                              class="widefat" rows="4"><?php echo esc_textarea($info_boxes[$i]['content']); ?></textarea>
                </p>
            </div>
        <?php endfor; ?>
    </div>
    <style>
        .info-boxes-wrapper {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin: 0 -10px;
            padding: 10px;
        }
        .info-box-section {
            flex: 1;
            min-width: 250px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 3px;
        }
        .info-box-section h4 {
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e5e5;
        }
        .info-box-section textarea {
            min-height: 80px;
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
