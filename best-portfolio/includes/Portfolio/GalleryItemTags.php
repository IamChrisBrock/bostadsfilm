<?php
namespace BestPortfolio\Portfolio;

/**
 * Gallery Item Tags Management
 *
 * This class handles the taxonomy for gallery item tags. These tags are specifically
 * for categorizing individual items within galleries. This allows for granular
 * filtering and organization of media items within the Best Portfolio plugin.
 *
 * Taxonomy: best_portfolio_item_tag
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes/Portfolio
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class GalleryItemTags {

    /**
     * The taxonomy name for gallery item tags.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $taxonomy    The taxonomy identifier
     */
    private $taxonomy = 'best_portfolio_item_tag';

    /**
     * Initialize the class and register the taxonomy.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('init', array($this, 'register_taxonomy'));
    }

    /**
     * Register the gallery item tags taxonomy.
     *
     * @since    1.0.0
     */
    public function register_taxonomy() {
        $labels = array(
            'name'                       => _x('Item Tags', 'taxonomy general name', 'best-portfolio'),
            'singular_name'              => _x('Item Tag', 'taxonomy singular name', 'best-portfolio'),
            'search_items'               => __('Search Item Tags', 'best-portfolio'),
            'popular_items'              => __('Popular Item Tags', 'best-portfolio'),
            'all_items'                  => __('All Item Tags', 'best-portfolio'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Item Tag', 'best-portfolio'),
            'update_item'                => __('Update Item Tag', 'best-portfolio'),
            'add_new_item'               => __('Add New Item Tag', 'best-portfolio'),
            'new_item_name'              => __('New Item Tag Name', 'best-portfolio'),
            'separate_items_with_commas' => __('Separate item tags with commas', 'best-portfolio'),
            'add_or_remove_items'        => __('Add or remove item tags', 'best-portfolio'),
            'choose_from_most_used'      => __('Choose from the most used item tags', 'best-portfolio'),
            'menu_name'                  => __('Item Tags', 'best-portfolio'),
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
            'rewrite'           => array('slug' => 'item-tag'),
        );

        register_taxonomy($this->taxonomy, 'best_portfolio_item', $args);
    }

    /**
     * Get all gallery item tags.
     *
     * @since    1.0.0
     * @return   array    Array of item tag terms
     */
    public function get_all_tags() {
        return get_terms(array(
            'taxonomy'   => $this->taxonomy,
            'hide_empty' => false,
        ));
    }

    /**
     * Get tags for a specific gallery item.
     *
     * @since    1.0.0
     * @param    int      $item_id    The gallery item post ID
     * @return   array    Array of item tag terms
     */
    public function get_item_tags($item_id) {
        return wp_get_post_terms($item_id, $this->taxonomy);
    }

    /**
     * Add tags to a gallery item.
     *
     * @since    1.0.0
     * @param    int      $item_id    The gallery item post ID
     * @param    array    $tags       Array of tag names or IDs
     * @return   array|WP_Error    Array of term taxonomy IDs or WP_Error
     */
    public function add_item_tags($item_id, $tags) {
        return wp_set_post_terms($item_id, $tags, $this->taxonomy, true);
    }

    /**
     * Remove tags from a gallery item.
     *
     * @since    1.0.0
     * @param    int      $item_id    The gallery item post ID
     * @param    array    $tags       Array of tag names or IDs to remove
     * @return   array|WP_Error    Array of term taxonomy IDs or WP_Error
     */
    public function remove_item_tags($item_id, $tags) {
        $current_tags = wp_get_post_terms($item_id, $this->taxonomy, array('fields' => 'ids'));
        $tags_to_remove = wp_list_pluck(wp_get_post_terms($item_id, $this->taxonomy, array(
            'include' => $tags,
            'fields' => 'ids'
        )), 'term_id');
        
        $new_tags = array_diff($current_tags, $tags_to_remove);
        return wp_set_post_terms($item_id, $new_tags, $this->taxonomy, false);
    }

    /**
     * Get items by tags.
     *
     * @since    1.0.0
     * @param    array    $tags          Array of tag IDs or names
     * @param    string   $relation      The logical relationship between tags ('AND' or 'OR')
     * @return   array    Array of gallery item posts
     */
    public function get_items_by_tags($tags, $relation = 'AND') {
        $args = array(
            'post_type' => 'best_portfolio_item',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => $this->taxonomy,
                    'field'    => is_numeric($tags[0]) ? 'term_id' : 'slug',
                    'terms'    => $tags,
                    'operator' => $relation === 'AND' ? 'AND' : 'IN',
                ),
            ),
        );

        return get_posts($args);
    }

    /**
     * Get popular tags.
     *
     * @since    1.0.0
     * @param    int      $limit    Number of tags to return
     * @return   array    Array of most used tags
     */
    public function get_popular_tags($limit = 10) {
        return get_terms(array(
            'taxonomy'   => $this->taxonomy,
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => $limit,
            'hide_empty' => true,
        ));
    }
}
