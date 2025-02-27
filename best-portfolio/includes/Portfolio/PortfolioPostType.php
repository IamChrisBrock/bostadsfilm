<?php
namespace BestPortfolio\Portfolio;

/**
 * Portfolio Post Type Registration and Management
 *
 * This class handles the main Portfolio post type which serves as the parent
 * container for Galleries. Each Portfolio can contain multiple Galleries,
 * which in turn contain Gallery Items.
 *
 * Post Type: best_portfolio
 * Meta Prefix: _best_portfolio_
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes/Portfolio
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class PortfolioPostType {

    /**
     * The post type name/slug.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $post_type    The post type name (best_portfolio)
     */
    private $post_type = 'best_portfolio';

    /**
     * Meta prefix for all portfolio meta fields.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $meta_prefix    Prefix for meta fields
     */
    private $meta_prefix = '_best_portfolio_';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Add filter for managing admin columns
        add_filter('manage_' . $this->post_type . '_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_' . $this->post_type . '_posts_custom_column', array($this, 'render_custom_columns'), 10, 2);
        
        // Add sorting functionality
        add_filter('manage_edit-' . $this->post_type . '_sortable_columns', array($this, 'set_sortable_columns'));
        
        // Add filters to the admin list page
        add_action('restrict_manage_posts', array($this, 'add_admin_filters'));
        add_filter('parse_query', array($this, 'filter_admin_list'));
    }

    /**
     * Register the Portfolio post type.
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => __('Portfolios', 'best-portfolio'),
            'singular_name'         => __('Portfolio', 'best-portfolio'),
            'menu_name'            => __('Portfolio', 'best-portfolio'),
            'name_admin_bar'       => __('Portfolio', 'best-portfolio'),
            'add_new'              => __('Add New', 'best-portfolio'),
            'add_new_item'         => __('Add New Portfolio', 'best-portfolio'),
            'new_item'             => __('New Portfolio', 'best-portfolio'),
            'edit_item'            => __('Edit Portfolio', 'best-portfolio'),
            'view_item'            => __('View Portfolio', 'best-portfolio'),
            'all_items'            => __('All Portfolios', 'best-portfolio'),
            'search_items'         => __('Search Portfolios', 'best-portfolio'),
            'not_found'            => __('No portfolios found.', 'best-portfolio'),
            'not_found_in_trash'   => __('No portfolios found in Trash.', 'best-portfolio'),
            'featured_image'        => __('Portfolio Cover Image', 'best-portfolio'),
            'set_featured_image'    => __('Set cover image', 'best-portfolio'),
            'remove_featured_image' => __('Remove cover image', 'best-portfolio'),
            'use_featured_image'    => __('Use as cover image', 'best-portfolio'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'portfolio'),
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'revisions'
            ),
            'show_in_rest'      => true,
        );

        register_post_type($this->post_type, $args);
    }

    /**
     * Register taxonomies for the Portfolio post type.
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_taxonomies() {
        if (!function_exists('register_taxonomy')) {
            require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
        }

        // Register portfolio category taxonomy
        $labels = array(
            'name'              => __('Portfolio Categories', 'best-portfolio'),
            'singular_name'     => __('Portfolio Category', 'best-portfolio'),
            'search_items'      => __('Search Categories', 'best-portfolio'),
            'all_items'         => __('All Categories', 'best-portfolio'),
            'parent_item'       => __('Parent Category', 'best-portfolio'),
            'parent_item_colon' => __('Parent Category:', 'best-portfolio'),
            'edit_item'         => __('Edit Category', 'best-portfolio'),
            'update_item'       => __('Update Category', 'best-portfolio'),
            'add_new_item'      => __('Add New Category', 'best-portfolio'),
            'new_item_name'     => __('New Category Name', 'best-portfolio'),
            'menu_name'         => __('Categories', 'best-portfolio'),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'portfolio-category'),
            'show_in_rest'      => true,
        );

        register_taxonomy('portfolio_category', array($this->post_type), $args);

        // Register portfolio tag taxonomy
        $labels = array(
            'name'              => __('Portfolio Tags', 'best-portfolio'),
            'singular_name'     => __('Portfolio Tag', 'best-portfolio'),
            'search_items'      => __('Search Tags', 'best-portfolio'),
            'all_items'         => __('All Tags', 'best-portfolio'),
            'edit_item'         => __('Edit Tag', 'best-portfolio'),
            'update_item'       => __('Update Tag', 'best-portfolio'),
            'add_new_item'      => __('Add New Tag', 'best-portfolio'),
            'new_item_name'     => __('New Tag Name', 'best-portfolio'),
            'menu_name'         => __('Tags', 'best-portfolio'),
        );

        $args = array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'portfolio-tag'),
            'show_in_rest'      => true,
        );

        register_taxonomy('portfolio_tag', array($this->post_type), $args);
    }

    /**
     * Register meta fields for the Portfolio post type.
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_meta_fields() {
        register_post_meta($this->post_type, $this->meta_prefix . 'created_at', array(
            'type'              => 'string',
            'description'       => 'Date when the portfolio was created',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));

        register_post_meta($this->post_type, $this->meta_prefix . 'updated_at', array(
            'type'              => 'string',
            'description'       => 'Date when the portfolio was last updated',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            }
        ));
    }

    /**
     * Add meta boxes for additional portfolio fields.
     *
     * @since    1.0.0
     * @access   public
     */
    public function add_meta_boxes() {
        add_meta_box(
            'best_portfolio_galleries',
            __('Portfolio Galleries', 'best-portfolio'),
            array($this, 'render_galleries_meta_box'),
            $this->post_type,
            'normal',
            'high'
        );
    }

    /**
     * Render the galleries meta box content.
     *
     * @since    1.0.0
     * @access   public
     * @param    WP_Post    $post    The post object.
     */
    public function render_galleries_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('best_portfolio_galleries_meta_box', 'best_portfolio_galleries_nonce');

        // Get existing galleries for this portfolio
        $galleries = $this->get_portfolio_galleries($post->ID);

        // Meta box content
        ?>
        <div id="best-portfolio-galleries" class="best-portfolio-galleries-wrapper">
            <div class="galleries-list" data-portfolio-id="<?php echo esc_attr($post->ID); ?>">
                <?php
                if (!empty($galleries)) {
                    foreach ($galleries as $gallery) {
                        $this->render_gallery_item($gallery);
                    }
                }
                ?>
            </div>
            <button type="button" class="button button-primary add-gallery">
                <?php _e('Add New Gallery', 'best-portfolio'); ?>
            </button>
        </div>

        <script type="text/template" id="tmpl-gallery-item">
            <div class="gallery-item" data-gallery-id="{{ data.id }}">
                <h3 class="gallery-title">{{ data.title }}</h3>
                <div class="gallery-actions">
                    <button type="button" class="button edit-gallery"><?php _e('Edit', 'best-portfolio'); ?></button>
                    <button type="button" class="button button-link-delete delete-gallery"><?php _e('Delete', 'best-portfolio'); ?></button>
                </div>
            </div>
        </script>
        <?php
    }

    /**
     * Get all galleries associated with a portfolio.
     *
     * @since    1.0.0
     * @access   private
     * @param    int       $portfolio_id    The portfolio ID.
     * @return   array                      Array of gallery objects.
     */
    private function get_portfolio_galleries($portfolio_id) {
        $args = array(
            'post_type'      => 'best_portfolio_gallery',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'meta_query'     => array(
                array(
                    'key'     => '_best_portfolio_gallery_portfolio_id',
                    'value'   => $portfolio_id,
                    'compare' => '='
                )
            ),
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_best_portfolio_gallery_sort_order',
            'order'          => 'ASC'
        );

        return get_posts($args);
    }

    /**
     * Render a single gallery item in the meta box.
     *
     * @since    1.0.0
     * @access   private
     * @param    WP_Post    $gallery    The gallery post object.
     */
    private function render_gallery_item($gallery) {
        ?>
        <div class="gallery-item" data-gallery-id="<?php echo esc_attr($gallery->ID); ?>">
            <h3 class="gallery-title"><?php echo esc_html($gallery->post_title); ?></h3>
            <div class="gallery-actions">
                <button type="button" class="button edit-gallery"><?php _e('Edit', 'best-portfolio'); ?></button>
                <button type="button" class="button button-link-delete delete-gallery"><?php _e('Delete', 'best-portfolio'); ?></button>
            </div>
        </div>
        <?php
    }

    /**
     * Save portfolio meta box data.
     *
     * @since    1.0.0
     * @access   public
     * @param    int       $post_id    The post ID.
     */
    public function save_meta_boxes($post_id) {
        // Check if our nonce is set
        if (!isset($_POST['best_portfolio_galleries_nonce'])) {
            return;
        }

        // Verify that the nonce is valid
        if (!wp_verify_nonce($_POST['best_portfolio_galleries_nonce'], 'best_portfolio_galleries_meta_box')) {
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

        // Update the updated_at timestamp
        update_post_meta($post_id, $this->meta_prefix . 'updated_at', current_time('mysql'));

        // If this is a new post, set the created_at timestamp
        if (!get_post_meta($post_id, $this->meta_prefix . 'created_at', true)) {
            update_post_meta($post_id, $this->meta_prefix . 'created_at', current_time('mysql'));
        }
    }

    /**
     * Set custom columns for the portfolio list table.
     *
     * @since    1.0.0
     * @access   public
     * @param    array    $columns    Array of column names.
     * @return   array               Modified array of column names.
     */
    public function set_custom_columns($columns) {
        $date = $columns['date'];
        unset($columns['date']);

        $columns['thumbnail'] = __('Cover Image', 'best-portfolio');
        $columns['categories'] = __('Categories', 'best-portfolio');
        $columns['tags'] = __('Tags', 'best-portfolio');
        $columns['galleries'] = __('Galleries', 'best-portfolio');
        $columns['created'] = __('Created', 'best-portfolio');
        $columns['date'] = __('Last Modified', 'best-portfolio');

        return $columns;
    }

    /**
     * Render custom column content.
     *
     * @since    1.0.0
     * @access   public
     * @param    string    $column     Column name.
     * @param    int       $post_id    Post ID.
     */
    public function render_custom_columns($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '<span class="dashicons dashicons-format-image"></span>';
                }
                break;

            case 'categories':
                $terms = get_the_terms($post_id, 'portfolio_category');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_links = array();
                    foreach ($terms as $term) {
                        $term_links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(add_query_arg(array('portfolio_category' => $term->slug))),
                            esc_html($term->name)
                        );
                    }
                    echo implode(', ', $term_links);
                }
                break;

            case 'tags':
                $terms = get_the_terms($post_id, 'portfolio_tag');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_links = array();
                    foreach ($terms as $term) {
                        $term_links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(add_query_arg(array('portfolio_tag' => $term->slug))),
                            esc_html($term->name)
                        );
                    }
                    echo implode(', ', $term_links);
                }
                break;

            case 'galleries':
                $galleries = $this->get_portfolio_galleries($post_id);
                echo count($galleries);
                break;

            case 'created':
                $created_at = get_post_meta($post_id, $this->meta_prefix . 'created_at', true);
                if ($created_at) {
                    echo date_i18n(get_option('date_format'), strtotime($created_at));
                }
                break;
        }
    }

    /**
     * Set sortable columns.
     *
     * @since    1.0.0
     * @access   public
     * @param    array    $columns    Array of sortable columns.
     * @return   array               Modified array of sortable columns.
     */
    public function set_sortable_columns($columns) {
        $columns['created'] = 'created';
        return $columns;
    }

    /**
     * Add filters to the admin list page.
     *
     * @since    1.0.0
     * @access   public
     * @param    string    $post_type    The post type being filtered.
     */
    public function add_admin_filters($post_type) {
        if ($post_type !== $this->post_type) {
            return;
        }

        // Category filter
        $taxonomy = 'portfolio_category';
        $tax = get_taxonomy($taxonomy);
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        wp_dropdown_categories(array(
            'show_option_all' => __('All Categories', 'best-portfolio'),
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => $selected,
            'hierarchical' => true,
            'depth' => 3,
            'show_count' => true,
            'hide_empty' => true,
        ));

        // Tag filter
        $taxonomy = 'portfolio_tag';
        $tax = get_taxonomy($taxonomy);
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        wp_dropdown_categories(array(
            'show_option_all' => __('All Tags', 'best-portfolio'),
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => $selected,
            'hierarchical' => false,
            'show_count' => true,
            'hide_empty' => true,
        ));
    }

    /**
     * Filter the admin list based on selected filters.
     *
     * @since    1.0.0
     * @access   public
     * @param    WP_Query    $query    The query object.
     */
    public function filter_admin_list($query) {
        global $pagenow;

        if (!is_admin() || $pagenow !== 'edit.php' || !$query->is_main_query() || !isset($query->query['post_type']) || $query->query['post_type'] !== $this->post_type) {
            return;
        }

        // Handle sorting
        if (isset($query->query['orderby'])) {
            switch ($query->query['orderby']) {
                case 'created':
                    $query->set('meta_key', $this->meta_prefix . 'created_at');
                    $query->set('orderby', 'meta_value');
                    break;
            }
        }
    }
}
