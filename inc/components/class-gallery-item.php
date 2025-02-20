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
        
        if (!empty($this->media_ids)) {
            $first_media = $this->media_ids[0];
            $type = wp_attachment_is('video', $first_media) ? 'video' : 'image';
            
            if ($type === 'video') {
                $this->preview_image = get_post_thumbnail_id($first_media) ? 
                    wp_get_attachment_image_src(get_post_thumbnail_id($first_media), 'large') :
                    null;
            } else {
                $this->preview_image = wp_get_attachment_image_src($first_media, 'large');
            }
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
