<?php
namespace BestPortfolio\Portfolio;

/**
 * Gallery Tags Management
 *
 * This class handles the taxonomy for gallery tags. These tags are specifically
 * for categorizing galleries within the Best Portfolio plugin. This allows for
 * filtering and organizing galleries based on custom tags.
 *
 * Taxonomy: best_portfolio_gallery_tag
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes/Portfolio
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class GalleryTags {

    /**
     * The taxonomy name for gallery tags.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $taxonomy    The taxonomy identifier
     */
    private $taxonomy = 'best_portfolio_gallery_tag';

    /**
     * Initialize the class and register the taxonomy.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('init', array($this, 'register_taxonomy'));
    }

    /**
     * Register the gallery tags taxonomy.
     *
     * @since    1.0.0
     */
    public function register_taxonomy() {
        $labels = array(
            'name'                       => _x('Gallery Tags', 'taxonomy general name', 'best-portfolio'),
            'singular_name'              => _x('Gallery Tag', 'taxonomy singular name', 'best-portfolio'),
            'search_items'               => __('Search Gallery Tags', 'best-portfolio'),
            'popular_items'              => __('Popular Gallery Tags', 'best-portfolio'),
            'all_items'                  => __('All Gallery Tags', 'best-portfolio'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Gallery Tag', 'best-portfolio'),
            'update_item'                => __('Update Gallery Tag', 'best-portfolio'),
            'add_new_item'               => __('Add New Gallery Tag', 'best-portfolio'),
            'new_item_name'              => __('New Gallery Tag Name', 'best-portfolio'),
            'separate_items_with_commas' => __('Separate gallery tags with commas', 'best-portfolio'),
            'add_or_remove_items'        => __('Add or remove gallery tags', 'best-portfolio'),
            'choose_from_most_used'      => __('Choose from the most used gallery tags', 'best-portfolio'),
            'menu_name'                  => __('Gallery Tags', 'best-portfolio'),
        );

        $args = array(
            'labels'            => $labels,
            'hierarchical'      => false,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => true,
            'show_in_rest'      => true,
            'rewrite'           => array('slug' => 'gallery-tag'),
        );

        register_taxonomy($this->taxonomy, 'best_portfolio_gallery', $args);
    }

    /**
     * Get all gallery tags.
     *
     * @since    1.0.0
     * @return   array    Array of gallery tag terms
     */
    public function get_all_tags() {
        return get_terms(array(
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => false,
        ));
    }

    /**
     * Get tags for a specific gallery.
     *
     * @since    1.0.0
     * @param    int      $gallery_id    The gallery post ID
     * @return   array    Array of gallery tag terms
     */
    public function get_gallery_tags($gallery_id) {
        return wp_get_post_terms($gallery_id, $this->taxonomy);
    }

    /**
     * Add tags to a gallery.
     *
     * @since    1.0.0
     * @param    int      $gallery_id    The gallery post ID
     * @param    array    $tags          Array of tag names or IDs
     * @return   array|WP_Error    Array of term taxonomy IDs or WP_Error
     */
    public function add_gallery_tags($gallery_id, $tags) {
        return wp_set_post_terms($gallery_id, $tags, $this->taxonomy, true);
    }

    /**
     * Remove tags from a gallery.
     *
     * @since    1.0.0
     * @param    int      $gallery_id    The gallery post ID
     * @param    array    $tags          Array of tag names or IDs to remove
     * @return   array|WP_Error    Array of term taxonomy IDs or WP_Error
     */
    public function remove_gallery_tags($gallery_id, $tags) {
        $current_tags = wp_get_post_terms($gallery_id, $this->taxonomy, array('fields' => 'ids'));
        $tags_to_remove = wp_list_pluck(wp_get_post_terms($gallery_id, $this->taxonomy, array(
            'include' => $tags,
            'fields' => 'ids'
        )), 'term_id');
        
        $new_tags = array_diff($current_tags, $tags_to_remove);
        return wp_set_post_terms($gallery_id, $new_tags, $this->taxonomy, false);
    }
}
