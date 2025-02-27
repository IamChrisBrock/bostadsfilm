<?php
namespace BestPortfolio\Portfolio;

/**
 * Tag Filter Management
 *
 * This class handles the filtering functionality for both gallery and item tags.
 * It provides methods to:
 * - Filter galleries by tags
 * - Filter items by tags
 * - Combine gallery and item tag filters
 * - Generate tag clouds and filter UI
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes/Portfolio
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class TagFilter {

    /**
     * Gallery Tags instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      GalleryTags    $gallery_tags    The gallery tags instance
     */
    private $gallery_tags;

    /**
     * Gallery Item Tags instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      GalleryItemTags    $item_tags    The gallery item tags instance
     */
    private $item_tags;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->gallery_tags = new GalleryTags();
        $this->item_tags = new GalleryItemTags();

        // Add AJAX handlers
        add_action('wp_ajax_best_portfolio_filter_galleries', array($this, 'ajax_filter_galleries'));
        add_action('wp_ajax_nopriv_best_portfolio_filter_galleries', array($this, 'ajax_filter_galleries'));
        add_action('wp_ajax_best_portfolio_filter_items', array($this, 'ajax_filter_items'));
        add_action('wp_ajax_nopriv_best_portfolio_filter_items', array($this, 'ajax_filter_items'));
    }

    /**
     * Get galleries filtered by tags.
     *
     * @since    1.0.0
     * @param    array     $tags       Array of tag IDs or slugs
     * @param    string    $relation   Relationship between tags (AND/OR)
     * @return   array     Array of filtered gallery posts
     */
    public function filter_galleries_by_tags($tags, $relation = 'AND') {
        $args = array(
            'post_type' => 'best_portfolio_gallery',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'best_portfolio_gallery_tag',
                    'field'    => is_numeric($tags[0]) ? 'term_id' : 'slug',
                    'terms'    => $tags,
                    'operator' => $relation === 'AND' ? 'AND' : 'IN',
                ),
            ),
        );

        return get_posts($args);
    }

    /**
     * Get items filtered by tags.
     *
     * @since    1.0.0
     * @param    array     $tags       Array of tag IDs or slugs
     * @param    string    $relation   Relationship between tags (AND/OR)
     * @return   array     Array of filtered gallery item posts
     */
    public function filter_items_by_tags($tags, $relation = 'AND') {
        $args = array(
            'post_type' => 'best_portfolio_item',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'best_portfolio_item_tag',
                    'field'    => is_numeric($tags[0]) ? 'term_id' : 'slug',
                    'terms'    => $tags,
                    'operator' => $relation === 'AND' ? 'AND' : 'IN',
                ),
            ),
        );

        return get_posts($args);
    }

    /**
     * Generate HTML for gallery tag cloud.
     *
     * @since    1.0.0
     * @param    array     $args    Arguments for tag cloud
     * @return   string    HTML output of tag cloud
     */
    public function get_gallery_tag_cloud($args = array()) {
        $defaults = array(
            'smallest' => 12,
            'largest'  => 22,
            'unit'     => 'px',
            'number'   => 45,
            'format'   => 'flat',
            'taxonomy' => 'best_portfolio_gallery_tag',
            'echo'     => false,
        );

        $args = wp_parse_args($args, $defaults);
        return wp_tag_cloud($args);
    }

    /**
     * Generate HTML for item tag cloud.
     *
     * @since    1.0.0
     * @param    array     $args    Arguments for tag cloud
     * @return   string    HTML output of tag cloud
     */
    public function get_item_tag_cloud($args = array()) {
        $defaults = array(
            'smallest' => 12,
            'largest'  => 22,
            'unit'     => 'px',
            'number'   => 45,
            'format'   => 'flat',
            'taxonomy' => 'best_portfolio_item_tag',
            'echo'     => false,
        );

        $args = wp_parse_args($args, $defaults);
        return wp_tag_cloud($args);
    }

    /**
     * Generate filter UI for galleries.
     *
     * @since    1.0.0
     * @return   string    HTML for gallery filter interface
     */
    public function render_gallery_filter() {
        $tags = $this->gallery_tags->get_all_tags();
        
        ob_start();
        ?>
        <div class="best-portfolio-gallery-filter" data-filter-type="gallery">
            <h4><?php _e('Filter Galleries by Tags', 'best-portfolio'); ?></h4>
            <div class="filter-options">
                <label>
                    <input type="radio" name="gallery_filter_relation" value="OR" checked>
                    <?php _e('Match Any Tag', 'best-portfolio'); ?>
                </label>
                <label>
                    <input type="radio" name="gallery_filter_relation" value="AND">
                    <?php _e('Match All Tags', 'best-portfolio'); ?>
                </label>
            </div>
            <div class="tag-list">
                <?php foreach ($tags as $tag) : ?>
                    <label class="tag-checkbox">
                        <input type="checkbox" name="gallery_tags[]" value="<?php echo esc_attr($tag->term_id); ?>">
                        <?php echo esc_html($tag->name); ?>
                        <span class="count">(<?php echo esc_html($tag->count); ?>)</span>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button apply-filter"><?php _e('Apply Filter', 'best-portfolio'); ?></button>
            <button type="button" class="button clear-filter"><?php _e('Clear', 'best-portfolio'); ?></button>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate filter UI for gallery items.
     *
     * @since    1.0.0
     * @return   string    HTML for item filter interface
     */
    public function render_item_filter() {
        $tags = $this->item_tags->get_all_tags();
        
        ob_start();
        ?>
        <div class="best-portfolio-item-filter" data-filter-type="item">
            <h4><?php _e('Filter Items by Tags', 'best-portfolio'); ?></h4>
            <div class="filter-options">
                <label>
                    <input type="radio" name="item_filter_relation" value="OR" checked>
                    <?php _e('Match Any Tag', 'best-portfolio'); ?>
                </label>
                <label>
                    <input type="radio" name="item_filter_relation" value="AND">
                    <?php _e('Match All Tags', 'best-portfolio'); ?>
                </label>
            </div>
            <div class="tag-list">
                <?php foreach ($tags as $tag) : ?>
                    <label class="tag-checkbox">
                        <input type="checkbox" name="item_tags[]" value="<?php echo esc_attr($tag->term_id); ?>">
                        <?php echo esc_html($tag->name); ?>
                        <span class="count">(<?php echo esc_html($tag->count); ?>)</span>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button apply-filter"><?php _e('Apply Filter', 'best-portfolio'); ?></button>
            <button type="button" class="button clear-filter"><?php _e('Clear', 'best-portfolio'); ?></button>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX handler for gallery filtering.
     *
     * @since    1.0.0
     */
    public function ajax_filter_galleries() {
        check_ajax_referer('best_portfolio_filter', 'nonce');

        $tags = isset($_POST['tags']) ? array_map('absint', $_POST['tags']) : array();
        $relation = isset($_POST['relation']) ? sanitize_text_field($_POST['relation']) : 'OR';

        $galleries = $this->filter_galleries_by_tags($tags, $relation);
        
        wp_send_json_success(array(
            'galleries' => array_map(function($gallery) {
                return array(
                    'id' => $gallery->ID,
                    'title' => $gallery->post_title,
                    'permalink' => get_permalink($gallery->ID),
                    'thumbnail' => get_the_post_thumbnail_url($gallery->ID, 'medium'),
                );
            }, $galleries)
        ));
    }

    /**
     * AJAX handler for item filtering.
     *
     * @since    1.0.0
     */
    public function ajax_filter_items() {
        check_ajax_referer('best_portfolio_filter', 'nonce');

        $tags = isset($_POST['tags']) ? array_map('absint', $_POST['tags']) : array();
        $relation = isset($_POST['relation']) ? sanitize_text_field($_POST['relation']) : 'OR';

        $items = $this->filter_items_by_tags($tags, $relation);
        
        wp_send_json_success(array(
            'items' => array_map(function($item) {
                return array(
                    'id' => $item->ID,
                    'title' => $item->post_title,
                    'type' => get_post_meta($item->ID, '_best_portfolio_item_type', true),
                    'thumbnail' => get_the_post_thumbnail_url($item->ID, 'medium'),
                );
            }, $items)
        ));
    }
}
