<?php
namespace BestPortfolio;

/**
 * Portfolio Post Type Registration and Management
 *
 * This class handles everything related to the portfolio custom post type including:
 * - Post type registration
 * - Custom taxonomies (categories, tags)
 * - Admin columns
 * - Post type specific settings
 * - Archive and single view modifications
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes
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
     * The single item label.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $single_label    The single item label (Portfolio Item)
     */
    private $single_label;

    /**
     * The plural item label.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plural_label    The plural item label (Portfolio Items)
     */
    private $plural_label;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->single_label = __('Portfolio Item', 'best-portfolio');
        $this->plural_label = __('Portfolio Items', 'best-portfolio');
    }

    /**
     * Register the portfolio custom post type.
     *
     * This function is responsible for registering the custom post type
     * with WordPress. It sets up all the labels, capabilities, and options
     * for the portfolio post type.
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => $this->plural_label,
            'singular_name'         => $this->single_label,
            'menu_name'            => __('Portfolio', 'best-portfolio'),
            'name_admin_bar'       => $this->single_label,
            'add_new'              => __('Add New', 'best-portfolio'),
            'add_new_item'         => sprintf(__('Add New %s', 'best-portfolio'), $this->single_label),
            'new_item'             => sprintf(__('New %s', 'best-portfolio'), $this->single_label),
            'edit_item'            => sprintf(__('Edit %s', 'best-portfolio'), $this->single_label),
            'view_item'            => sprintf(__('View %s', 'best-portfolio'), $this->single_label),
            'all_items'            => sprintf(__('All %s', 'best-portfolio'), $this->plural_label),
            'search_items'         => sprintf(__('Search %s', 'best-portfolio'), $this->plural_label),
            'parent_item_colon'    => sprintf(__('Parent %s:', 'best-portfolio'), $this->plural_label),
            'not_found'            => sprintf(__('No %s found.', 'best-portfolio'), strtolower($this->plural_label)),
            'not_found_in_trash'   => sprintf(__('No %s found in Trash.', 'best-portfolio'), strtolower($this->plural_label)),
            'archives'             => sprintf(__('%s Archives', 'best-portfolio'), $this->single_label),
            'attributes'           => sprintf(__('%s Attributes', 'best-portfolio'), $this->single_label),
            'insert_into_item'     => sprintf(__('Insert into %s', 'best-portfolio'), strtolower($this->single_label)),
            'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', 'best-portfolio'), strtolower($this->single_label)),
        );

        $supports = array(
            'title',           // Post title
            'editor',          // Content editor
            'excerpt',         // Excerpt
            'thumbnail',       // Featured images
            'author',          // Author
            'revisions',       // Revisions
            'custom-fields',   // Custom fields
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array(
                'slug'       => 'portfolio',
                'with_front' => true,
            ),
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-portfolio',
            'supports'            => $supports,
            'show_in_rest'        => true, // Enable Gutenberg editor
            'rest_base'           => 'portfolio',
            'template'            => array(
                array('core/paragraph'),
                array('core/gallery'),
            ),
            'template_lock'       => false,
        );

        register_post_type($this->post_type, $args);
    }

    /**
     * Register portfolio-specific taxonomies.
     *
     * This function registers two taxonomies:
     * 1. Portfolio Categories (hierarchical, like standard categories)
     * 2. Portfolio Tags (non-hierarchical, like standard tags)
     *
     * @since    1.0.0
     * @access   public
     */
    public function register_taxonomies() {
        // Register the Category taxonomy
        $category_labels = array(
            'name'              => __('Portfolio Categories', 'best-portfolio'),
            'singular_name'     => __('Portfolio Category', 'best-portfolio'),
            'search_items'      => __('Search Portfolio Categories', 'best-portfolio'),
            'all_items'         => __('All Portfolio Categories', 'best-portfolio'),
            'parent_item'       => __('Parent Portfolio Category', 'best-portfolio'),
            'parent_item_colon' => __('Parent Portfolio Category:', 'best-portfolio'),
            'edit_item'         => __('Edit Portfolio Category', 'best-portfolio'),
            'update_item'       => __('Update Portfolio Category', 'best-portfolio'),
            'add_new_item'      => __('Add New Portfolio Category', 'best-portfolio'),
            'new_item_name'     => __('New Portfolio Category Name', 'best-portfolio'),
            'menu_name'         => __('Categories', 'best-portfolio'),
        );

        register_taxonomy('best_portfolio_category', $this->post_type, array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'portfolio-category'),
            'show_in_rest'      => true,
        ));

        // Register the Tag taxonomy
        $tag_labels = array(
            'name'              => __('Portfolio Tags', 'best-portfolio'),
            'singular_name'     => __('Portfolio Tag', 'best-portfolio'),
            'search_items'      => __('Search Portfolio Tags', 'best-portfolio'),
            'all_items'         => __('All Portfolio Tags', 'best-portfolio'),
            'edit_item'         => __('Edit Portfolio Tag', 'best-portfolio'),
            'update_item'       => __('Update Portfolio Tag', 'best-portfolio'),
            'add_new_item'      => __('Add New Portfolio Tag', 'best-portfolio'),
            'new_item_name'     => __('New Portfolio Tag Name', 'best-portfolio'),
            'menu_name'         => __('Tags', 'best-portfolio'),
        );

        register_taxonomy('best_portfolio_tag', $this->post_type, array(
            'hierarchical'      => false,
            'labels'            => $tag_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'portfolio-tag'),
            'show_in_rest'      => true,
        ));
    }

    /**
     * Customize the admin columns for the portfolio post type.
     *
     * @since    1.0.0
     * @access   public
     * @param    array    $columns    The existing columns.
     * @return   array                Modified columns.
     */
    public function customize_admin_columns($columns) {
        $new_columns = array();
        
        // Thumbnail first
        $new_columns['cb'] = $columns['cb'];
        $new_columns['thumbnail'] = __('Thumbnail', 'best-portfolio');
        
        // Title after thumbnail
        $new_columns['title'] = $columns['title'];
        
        // Custom columns
        $new_columns['categories'] = __('Categories', 'best-portfolio');
        $new_columns['tags'] = __('Tags', 'best-portfolio');
        $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    /**
     * Handle the content for custom admin columns.
     *
     * @since    1.0.0
     * @access   public
     * @param    string    $column     The column slug.
     * @param    int       $post_id    The post ID.
     */
    public function handle_admin_columns($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '—';
                }
                break;

            case 'categories':
                $terms = get_the_terms($post_id, 'best_portfolio_category');
                if ($terms && !is_wp_error($terms)) {
                    $category_links = array();
                    foreach ($terms as $term) {
                        $category_links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(get_term_link($term)),
                            esc_html($term->name)
                        );
                    }
                    echo implode(', ', $category_links);
                } else {
                    echo '—';
                }
                break;

            case 'tags':
                $terms = get_the_terms($post_id, 'best_portfolio_tag');
                if ($terms && !is_wp_error($terms)) {
                    $tag_links = array();
                    foreach ($terms as $term) {
                        $tag_links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url(get_term_link($term)),
                            esc_html($term->name)
                        );
                    }
                    echo implode(', ', $tag_links);
                } else {
                    echo '—';
                }
                break;
        }
    }

    /**
     * Make columns sortable in the admin area.
     *
     * @since    1.0.0
     * @access   public
     * @param    array    $columns    The sortable columns.
     * @return   array                Modified sortable columns.
     */
    public function make_columns_sortable($columns) {
        $columns['categories'] = 'categories';
        $columns['tags'] = 'tags';
        return $columns;
    }
}
