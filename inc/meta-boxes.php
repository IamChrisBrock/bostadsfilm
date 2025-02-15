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
