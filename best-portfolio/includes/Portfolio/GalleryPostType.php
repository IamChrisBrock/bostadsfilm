<?php
namespace BestPortfolio\Portfolio;

/**
 * Gallery Post Type Registration and Management
 *
 * This class handles the Gallery post type which serves as a container
 * for Gallery Items. Each Gallery belongs to a Portfolio and can contain
 * multiple Gallery Items.
 *
 * Post Type: best_portfolio_gallery
 * Meta Prefix: _best_portfolio_gallery_
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes/Portfolio
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class GalleryPostType {

    /**
     * The post type name/slug.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $post_type    The post type name
     */
    private $post_type = 'best_portfolio_gallery';

    /**
     * Meta prefix for all gallery meta fields.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $meta_prefix    Prefix for meta fields
     */
    private $meta_prefix = '_best_portfolio_gallery_';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Initialize any necessary properties
    }

    /**
     * Register the Gallery post type.
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => __('Galleries', 'best-portfolio'),
            'singular_name'         => __('Gallery', 'best-portfolio'),
            'menu_name'            => __('Galleries', 'best-portfolio'),
            'name_admin_bar'       => __('Gallery', 'best-portfolio'),
            'add_new'              => __('Add New', 'best-portfolio'),
            'add_new_item'         => __('Add New Gallery', 'best-portfolio'),
            'new_item'             => __('New Gallery', 'best-portfolio'),
            'edit_item'            => __('Edit Gallery', 'best-portfolio'),
            'view_item'            => __('View Gallery', 'best-portfolio'),
            'all_items'            => __('All Galleries', 'best-portfolio'),
            'search_items'         => __('Search Galleries', 'best-portfolio'),
            'not_found'            => __('No galleries found.', 'best-portfolio'),
            'not_found_in_trash'   => __('No galleries found in Trash.', 'best-portfolio'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=best_portfolio', // Make it a submenu of Portfolio
            'query_var'           => true,
            'rewrite'             => array('slug' => 'gallery'),
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
            'taxonomies'         => array('best_portfolio_gallery_tag'),
            'show_in_rest'       => true,
        );

        register_post_type($this->post_type, $args);
    }

    /**
     * Register meta fields for the Gallery post type.
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_meta_fields() {
        // Portfolio ID reference
        register_post_meta($this->post_type, $this->meta_prefix . 'portfolio_id', array(
            'type'              => 'integer',
            'description'       => 'ID of the parent portfolio',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'absint',
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));

        // Sort order
        register_post_meta($this->post_type, $this->meta_prefix . 'sort_order', array(
            'type'              => 'integer',
            'description'       => 'Custom sort order for the gallery',
            'single'            => true,
            'show_in_rest'      => true,
            'default'           => 0,
            'sanitize_callback' => 'absint',
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));
    }

    /**
     * Add meta boxes for additional gallery fields.
     *
     * @since    1.0.0
     * @access   public
     */
    public function add_meta_boxes() {
        add_meta_box(
            'best_portfolio_gallery_items',
            __('Gallery Items', 'best-portfolio'),
            array($this, 'render_gallery_items_meta_box'),
            $this->post_type,
            'normal',
            'high'
        );

        add_meta_box(
            'best_portfolio_gallery_settings',
            __('Gallery Settings', 'best-portfolio'),
            array($this, 'render_gallery_settings_meta_box'),
            $this->post_type,
            'side',
            'default'
        );

        add_meta_box(
            'best_portfolio_gallery_tags',
            __('Gallery Tags', 'best-portfolio'),
            array($this, 'render_gallery_tags_meta_box'),
            $this->post_type,
            'side',
            'default'
        );
    }

    /**
     * Render the gallery items meta box content.
     *
     * @since    1.0.0
     * @access   public
     * @param    WP_Post    $post    The post object.
     */
    public function render_gallery_items_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('best_portfolio_gallery_items_meta_box', 'best_portfolio_gallery_items_nonce');

        // Get existing gallery items
        $items = $this->get_gallery_items($post->ID);

        // Meta box content
        ?>
        <div id="best-portfolio-gallery-items" class="best-portfolio-gallery-items-wrapper">
            <div class="gallery-items-list sortable" data-gallery-id="<?php echo esc_attr($post->ID); ?>">
                <?php
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $this->render_gallery_item($item);
                    }
                }
                ?>
            </div>
            <button type="button" class="button button-primary add-gallery-item">
                <?php _e('Add New Item', 'best-portfolio'); ?>
            </button>
        </div>
        <?php
    }

    /**
     * Render the gallery settings meta box content.
     *
     * @since    1.0.0
     * @access   public
     * @param    WP_Post    $post    The post object.
     */
    public function render_gallery_settings_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('best_portfolio_gallery_settings_meta_box', 'best_portfolio_gallery_settings_nonce');

        // Get the current portfolio ID
        $portfolio_id = get_post_meta($post->ID, $this->meta_prefix . 'portfolio_id', true);
        $sort_order = get_post_meta($post->ID, $this->meta_prefix . 'sort_order', true);

        // Get all portfolios for the dropdown
        $portfolios = get_posts(array(
            'post_type' => 'best_portfolio',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        ?>
        <p>
            <label for="portfolio-id"><?php _e('Portfolio:', 'best-portfolio'); ?></label>
            <select name="<?php echo $this->meta_prefix; ?>portfolio_id" id="portfolio-id" class="widefat">
                <option value=""><?php _e('Select a Portfolio', 'best-portfolio'); ?></option>
                <?php
                foreach ($portfolios as $portfolio) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($portfolio->ID),
                        selected($portfolio_id, $portfolio->ID, false),
                        esc_html($portfolio->post_title)
                    );
                }
                ?>
            </select>
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
        <?php
    }

    /**
     * Get all items associated with a gallery.
     *
     * @since    1.0.0
     * @access   private
     * @param    int       $gallery_id    The gallery ID.
     * @return   array                    Array of gallery item objects.
     */
    private function get_gallery_items($gallery_id) {
        $args = array(
            'post_type'      => 'best_portfolio_item',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'meta_query'     => array(
                array(
                    'key'     => '_best_portfolio_item_gallery_id',
                    'value'   => $gallery_id,
                    'compare' => '='
                )
            ),
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_best_portfolio_item_sort_order',
            'order'          => 'ASC'
        );

        return get_posts($args);
    }

    /**
     * Save gallery meta box data.
     *
     * @since    1.0.0
     * @access   public
     * @param    int       $post_id    The post ID.
     */
    public function save_meta_boxes($post_id) {
        // Check if our nonce is set
        if (!isset($_POST['best_portfolio_gallery_settings_nonce'])) {
            return;
        }

        // Verify that the nonce is valid
        if (!wp_verify_nonce($_POST['best_portfolio_gallery_settings_nonce'], 'best_portfolio_gallery_settings_meta_box')) {
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

        // Save portfolio ID
        if (isset($_POST[$this->meta_prefix . 'portfolio_id'])) {
            update_post_meta(
                $post_id,
                $this->meta_prefix . 'portfolio_id',
                absint($_POST[$this->meta_prefix . 'portfolio_id'])
            );
        }

        // Save sort order
        if (isset($_POST[$this->meta_prefix . 'sort_order'])) {
            update_post_meta(
                $post_id,
                $this->meta_prefix . 'sort_order',
                absint($_POST[$this->meta_prefix . 'sort_order'])
            );
        }
    }
}
