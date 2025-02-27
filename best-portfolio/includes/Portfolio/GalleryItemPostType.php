<?php
namespace BestPortfolio\Portfolio;

/**
 * Gallery Item Post Type Registration and Management
 *
 * This class handles the Gallery Item post type which represents individual
 * media items within a gallery. Each item can be of different types including
 * images, videos, URLs, embeds, and styled text.
 *
 * Post Type: best_portfolio_item
 * Meta Prefix: _best_portfolio_item_
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes/Portfolio
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class GalleryItemPostType {

    /**
     * The post type name/slug.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $post_type    The post type name
     */
    private $post_type = 'best_portfolio_item';

    /**
     * Meta prefix for all gallery item meta fields.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $meta_prefix    Prefix for meta fields
     */
    private $meta_prefix = '_best_portfolio_item_';

    /**
     * Available media types for gallery items.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $media_types    Array of available media types
     */
    private $media_types = array(
        'image'        => 'Image',
        'video'        => 'Video',
        'url'          => 'URL',
        'youtube'      => 'YouTube',
        'vimeo'        => 'Vimeo',
        'styled_text'  => 'Styled Text',
        'gif'          => 'GIF',
        'lottie'       => 'Lottie Animation'
    );

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Initialize any necessary properties
    }

    /**
     * Register the Gallery Item post type.
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => __('Gallery Items', 'best-portfolio'),
            'singular_name'         => __('Gallery Item', 'best-portfolio'),
            'menu_name'            => __('Gallery Items', 'best-portfolio'),
            'name_admin_bar'       => __('Gallery Item', 'best-portfolio'),
            'add_new'              => __('Add New', 'best-portfolio'),
            'add_new_item'         => __('Add New Gallery Item', 'best-portfolio'),
            'new_item'             => __('New Gallery Item', 'best-portfolio'),
            'edit_item'            => __('Edit Gallery Item', 'best-portfolio'),
            'view_item'            => __('View Gallery Item', 'best-portfolio'),
            'all_items'            => __('All Gallery Items', 'best-portfolio'),
            'search_items'         => __('Search Gallery Items', 'best-portfolio'),
            'not_found'            => __('No gallery items found.', 'best-portfolio'),
            'not_found_in_trash'   => __('No gallery items found in Trash.', 'best-portfolio'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=best_portfolio', // Make it a submenu of Portfolio
            'query_var'           => true,
            'rewrite'             => array('slug' => 'gallery-item'),
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array(
                'title',
                'editor',
                'thumbnail',
                'custom-fields',
                'tags'
            ),
            'taxonomies'         => array('best_portfolio_item_tag'),
            'show_in_rest'       => true,
        );

        register_post_type($this->post_type, $args);
    }

    /**
     * Register meta fields for the Gallery Item post type.
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_meta_fields() {
        // Gallery ID reference
        register_post_meta($this->post_type, $this->meta_prefix . 'gallery_id', array(
            'type'              => 'integer',
            'description'       => 'ID of the parent gallery',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'absint',
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));

        // Media type
        register_post_meta($this->post_type, $this->meta_prefix . 'type', array(
            'type'              => 'string',
            'description'       => 'Type of media (image, video, url, etc.)',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => array($this, 'sanitize_media_type'),
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));

        // Media URL
        register_post_meta($this->post_type, $this->meta_prefix . 'media_url', array(
            'type'              => 'string',
            'description'       => 'URL to media file',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'esc_url_raw',
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));

        // Embed code
        register_post_meta($this->post_type, $this->meta_prefix . 'embed_code', array(
            'type'              => 'string',
            'description'       => 'Embed code for external media',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => array($this, 'sanitize_embed_code'),
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));

        // Thumbnail URL
        register_post_meta($this->post_type, $this->meta_prefix . 'thumbnail_url', array(
            'type'              => 'string',
            'description'       => 'Preview image URL',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'esc_url_raw',
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));

        // Sort order
        register_post_meta($this->post_type, $this->meta_prefix . 'sort_order', array(
            'type'              => 'integer',
            'description'       => 'Custom sort order',
            'single'            => true,
            'show_in_rest'      => true,
            'default'           => 0,
            'sanitize_callback' => 'absint',
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));

        // Custom style
        register_post_meta($this->post_type, $this->meta_prefix . 'custom_style', array(
            'type'              => 'string',
            'description'       => 'Custom styling attributes',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => array($this, 'sanitize_custom_style'),
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));
    }

    /**
     * Add meta boxes for gallery item fields.
     *
     * @since    1.0.0
     * @access   public
     */
    public function add_meta_boxes() {
        add_meta_box(
            'best_portfolio_item_details',
            __('Gallery Item Details', 'best-portfolio'),
            array($this, 'render_item_details_meta_box'),
            $this->post_type,
            'normal',
            'high'
        );

        add_meta_box(
            'best_portfolio_item_tags',
            __('Item Tags', 'best-portfolio'),
            array($this, 'render_item_tags_meta_box'),
            $this->post_type,
            'side',
            'default'
        );
    }

    /**
     * Render the item details meta box content.
     *
     * @since    1.0.0
     * @access   public
     * @param    WP_Post    $post    The post object.
     */
    public function render_item_details_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('best_portfolio_item_details_meta_box', 'best_portfolio_item_details_nonce');

        // Get current values
        $gallery_id = get_post_meta($post->ID, $this->meta_prefix . 'gallery_id', true);
        $type = get_post_meta($post->ID, $this->meta_prefix . 'type', true);
        $media_url = get_post_meta($post->ID, $this->meta_prefix . 'media_url', true);
        $embed_code = get_post_meta($post->ID, $this->meta_prefix . 'embed_code', true);
        $thumbnail_url = get_post_meta($post->ID, $this->meta_prefix . 'thumbnail_url', true);
        $sort_order = get_post_meta($post->ID, $this->meta_prefix . 'sort_order', true);
        $custom_style = get_post_meta($post->ID, $this->meta_prefix . 'custom_style', true);

        // Get all galleries for the dropdown
        $galleries = get_posts(array(
            'post_type' => 'best_portfolio_gallery',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        ?>
        <div class="best-portfolio-item-details">
            <p>
                <label for="gallery-id"><?php _e('Gallery:', 'best-portfolio'); ?></label>
                <select name="<?php echo $this->meta_prefix; ?>gallery_id" id="gallery-id" class="widefat">
                    <option value=""><?php _e('Select a Gallery', 'best-portfolio'); ?></option>
                    <?php
                    foreach ($galleries as $gallery) {
                        printf(
                            '<option value="%s" %s>%s</option>',
                            esc_attr($gallery->ID),
                            selected($gallery_id, $gallery->ID, false),
                            esc_html($gallery->post_title)
                        );
                    }
                    ?>
                </select>
            </p>

            <p>
                <label for="media-type"><?php _e('Media Type:', 'best-portfolio'); ?></label>
                <select name="<?php echo $this->meta_prefix; ?>type" id="media-type" class="widefat">
                    <?php
                    foreach ($this->media_types as $value => $label) {
                        printf(
                            '<option value="%s" %s>%s</option>',
                            esc_attr($value),
                            selected($type, $value, false),
                            esc_html($label)
                        );
                    }
                    ?>
                </select>
            </p>

            <p>
                <label for="media-url"><?php _e('Media URL:', 'best-portfolio'); ?></label>
                <input type="url"
                       name="<?php echo $this->meta_prefix; ?>media_url"
                       id="media-url"
                       value="<?php echo esc_url($media_url); ?>"
                       class="widefat">
            </p>

            <p>
                <label for="embed-code"><?php _e('Embed Code:', 'best-portfolio'); ?></label>
                <textarea name="<?php echo $this->meta_prefix; ?>embed_code"
                          id="embed-code"
                          class="widefat"
                          rows="4"><?php echo esc_textarea($embed_code); ?></textarea>
            </p>

            <p>
                <label for="thumbnail-url"><?php _e('Thumbnail URL:', 'best-portfolio'); ?></label>
                <input type="url"
                       name="<?php echo $this->meta_prefix; ?>thumbnail_url"
                       id="thumbnail-url"
                       value="<?php echo esc_url($thumbnail_url); ?>"
                       class="widefat">
            </p>

            <p>
                <label for="sort-order"><?php _e('Sort Order:', 'best-portfolio'); ?></label>
                <input type="number"
                       name="<?php echo $this->meta_prefix; ?>sort_order"
                       id="sort-order"
                       value="<?php echo esc_attr($sort_order); ?>"
                       class="widefat"
                       min="0"
                       step="1">
            </p>

            <p>
                <label for="custom-style"><?php _e('Custom Style:', 'best-portfolio'); ?></label>
                <textarea name="<?php echo $this->meta_prefix; ?>custom_style"
                          id="custom-style"
                          class="widefat"
                          rows="4"><?php echo esc_textarea($custom_style); ?></textarea>
                <span class="description"><?php _e('Enter custom CSS styles for this item.', 'best-portfolio'); ?></span>
            </p>
        </div>
        <?php
    }

    /**
     * Save gallery item meta box data.
     *
     * @since    1.0.0
     * @access   public
     * @param    int       $post_id    The post ID.
     */
    public function save_meta_boxes($post_id) {
        // Check if our nonce is set
        if (!isset($_POST['best_portfolio_item_details_nonce'])) {
            return;
        }

        // Verify that the nonce is valid
        if (!wp_verify_nonce($_POST['best_portfolio_item_details_nonce'], 'best_portfolio_item_details_meta_box')) {
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

        // Update gallery ID
        if (isset($_POST[$this->meta_prefix . 'gallery_id'])) {
            update_post_meta(
                $post_id,
                $this->meta_prefix . 'gallery_id',
                absint($_POST[$this->meta_prefix . 'gallery_id'])
            );
        }

        // Update media type
        if (isset($_POST[$this->meta_prefix . 'type'])) {
            update_post_meta(
                $post_id,
                $this->meta_prefix . 'type',
                $this->sanitize_media_type($_POST[$this->meta_prefix . 'type'])
            );
        }

        // Update media URL
        if (isset($_POST[$this->meta_prefix . 'media_url'])) {
            update_post_meta(
                $post_id,
                $this->meta_prefix . 'media_url',
                esc_url_raw($_POST[$this->meta_prefix . 'media_url'])
            );
        }

        // Update embed code
        if (isset($_POST[$this->meta_prefix . 'embed_code'])) {
            update_post_meta(
                $post_id,
                $this->meta_prefix . 'embed_code',
                $this->sanitize_embed_code($_POST[$this->meta_prefix . 'embed_code'])
            );
        }

        // Update thumbnail URL
        if (isset($_POST[$this->meta_prefix . 'thumbnail_url'])) {
            update_post_meta(
                $post_id,
                $this->meta_prefix . 'thumbnail_url',
                esc_url_raw($_POST[$this->meta_prefix . 'thumbnail_url'])
            );
        }

        // Update sort order
        if (isset($_POST[$this->meta_prefix . 'sort_order'])) {
            update_post_meta(
                $post_id,
                $this->meta_prefix . 'sort_order',
                absint($_POST[$this->meta_prefix . 'sort_order'])
            );
        }

        // Update custom style
        if (isset($_POST[$this->meta_prefix . 'custom_style'])) {
            update_post_meta(
                $post_id,
                $this->meta_prefix . 'custom_style',
                $this->sanitize_custom_style($_POST[$this->meta_prefix . 'custom_style'])
            );
        }
    }

    /**
     * Sanitize media type.
     *
     * @since    1.0.0
     * @access   private
     * @param    string    $type    The media type to sanitize.
     * @return   string             Sanitized media type.
     */
    private function sanitize_media_type($type) {
        return array_key_exists($type, $this->media_types) ? $type : 'image';
    }

    /**
     * Sanitize embed code.
     *
     * @since    1.0.0
     * @access   private
     * @param    string    $code    The embed code to sanitize.
     * @return   string             Sanitized embed code.
     */
    private function sanitize_embed_code($code) {
        return wp_kses(
            $code,
            array(
                'iframe' => array(
                    'src'             => array(),
                    'width'           => array(),
                    'height'          => array(),
                    'frameborder'     => array(),
                    'allowfullscreen' => array(),
                ),
                'script' => array(
                    'src'  => array(),
                    'type' => array(),
                ),
            )
        );
    }

    /**
     * Sanitize custom style.
     *
     * @since    1.0.0
     * @access   private
     * @param    string    $style    The custom style to sanitize.
     * @return   string              Sanitized custom style.
     */
    private function sanitize_custom_style($style) {
        // Remove potentially harmful CSS
        $style = wp_strip_all_tags($style);
        
        // Basic CSS validation
        if (preg_match('/[<>{}]/', $style)) {
            return '';
        }

        return $style;
    }
}
