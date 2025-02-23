<?php
/**
 * Gallery Item Component
 * 
 * Represents a single gallery item with its data and rendering logic
 */

namespace Inkperial\Components;

class Gallery_Item {
    private $post;
    private $media_ids;
    private $preview_image;

    /**
     * Constructor
     * 
     * @param WP_Post $post The post object
     */
    public function __construct($post = null) {
        $this->post = $post ?: get_post();
        $this->setup_data();
    }

    /**
     * Set up the gallery item data
     */
    private function setup_data() {
        $this->media_ids = get_post_meta($this->post->ID, '_project_gallery_media', true);
        $this->media_ids = $this->media_ids ? explode(',', $this->media_ids) : array();
        $this->setup_preview_image();
    }

    /**
     * Set up the preview image
     */
    private function setup_preview_image() {
        $this->preview_image = '';
        
        // Check if there's a selected thumbnail
        $selected_thumbnail_id = get_post_meta($this->post->ID, '_gallery_thumbnail_id', true);
        
        if ($selected_thumbnail_id && in_array($selected_thumbnail_id, $this->media_ids)) {
            $media_id = $selected_thumbnail_id;
        } elseif (!empty($this->media_ids)) {
            $media_id = $this->media_ids[0];
        } else {
            return;
        }
        
        $type = wp_attachment_is('video', $media_id) ? 'video' : 'image';
        
        if ($type === 'video') {
            // Try to get video thumbnail from metadata first
            $video_metadata = wp_get_attachment_metadata($media_id);
            if (!empty($video_metadata['thumbnail'])) {
                $thumbnail_path = $video_metadata['thumbnail'];
                $upload_dir = wp_upload_dir();
                $thumbnail_url = $upload_dir['baseurl'] . '/' . $thumbnail_path;
                $this->preview_image = array($thumbnail_url, 1920, 1080); // Default video dimensions
            } else {
                // Fallback to post thumbnail if available
                $post_thumbnail_id = get_post_thumbnail_id($media_id);
                if ($post_thumbnail_id) {
                    $this->preview_image = wp_get_attachment_image_src($post_thumbnail_id, 'large');
                } else {
                    // If no thumbnail is found, try to get a frame from the video
                    $video_url = wp_get_attachment_url($media_id);
                    if ($video_url) {
                        $this->preview_image = array($video_url . '#t=0.1', 1920, 1080); // Get frame at 0.1 seconds
                    }
                }
            }
        } else {
            $this->preview_image = wp_get_attachment_image_src($media_id, 'large');
        }
    }

    /**
     * Get the media count
     * 
     * @return int Number of media items
     */
    public function get_media_count() {
        return count($this->media_ids);
    }

    /**
     * Check if the item has media
     * 
     * @return boolean
     */
    public function has_media() {
        return !empty($this->media_ids);
    }

    /**
     * Get the preview image URL
     * 
     * @return string|null
     */
    public function get_preview_image_url() {
        return $this->preview_image ? $this->preview_image[0] : null;
    }

    /**
     * Render the gallery item
     */
    public function render() {
        $template_data = array(
            'post_id' => $this->post->ID,
            'title' => get_the_title($this->post),
            'permalink' => get_permalink($this->post),
            'preview_image_url' => $this->get_preview_image_url(),
            'media_count' => $this->get_media_count(),
            'has_media' => $this->has_media(),
            'excerpt' => has_excerpt($this->post) ? get_the_excerpt($this->post) : '',
        );

        // Make template data available to the template
        extract($template_data);
        
        include get_template_directory() . '/template-parts/components/gallery-item.php';
    }
}
