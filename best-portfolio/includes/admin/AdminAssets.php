<?php
namespace BestPortfolio\Admin;

/**
 * Admin Assets Manager
 *
 * This class handles all admin-side assets including CSS and JavaScript files.
 * It's responsible for:
 * - Loading jQuery UI for sortable functionality
 * - Registering and enqueuing custom admin styles
 * - Registering and enqueuing custom admin scripts
 * - Managing AJAX handlers for sorting operations
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes/Admin
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class AdminAssets {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version           The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        if (!is_admin()) {
            return;
        }

        // jQuery UI styles for sortable functionality
        wp_enqueue_style(
            'jquery-ui-sortable-css',
            '//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css',
            array(),
            '1.13.2',
            'all'
        );

        // Custom admin styles
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/css/best-portfolio-admin.css',
            array('jquery-ui-sortable-css'),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        if (!is_admin()) {
            return;
        }

        // jQuery UI Sortable
        wp_enqueue_script('jquery-ui-sortable');

        // Custom admin script
        wp_enqueue_script(
            $this->plugin_name . '-admin',
            plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/js/best-portfolio-admin.js',
            array('jquery', 'jquery-ui-sortable'),
            $this->version,
            true
        );

        // Localize the script with necessary data
        wp_localize_script(
            $this->plugin_name . '-admin',
            'bestPortfolioAdmin',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('best_portfolio_admin_nonce'),
                'messages' => array(
                    'sorting_error' => __('Error updating sort order. Please try again.', 'best-portfolio'),
                    'sorting_success' => __('Sort order updated successfully.', 'best-portfolio'),
                )
            )
        );
    }

    /**
     * Register AJAX handlers for sorting operations.
     *
     * @since    1.0.0
     */
    public function register_ajax_handlers() {
        add_action('wp_ajax_best_portfolio_update_gallery_order', array($this, 'update_gallery_order'));
        add_action('wp_ajax_best_portfolio_update_gallery_item_order', array($this, 'update_gallery_item_order'));
    }

    /**
     * Handle AJAX request to update gallery sort order.
     *
     * @since    1.0.0
     */
    public function update_gallery_order() {
        // Check nonce
        if (!check_ajax_referer('best_portfolio_admin_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
        }

        // Check permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        // Get and validate data
        $gallery_order = isset($_POST['gallery_order']) ? $_POST['gallery_order'] : array();
        if (!is_array($gallery_order)) {
            wp_send_json_error(array('message' => 'Invalid data format'));
        }

        // Update sort order for each gallery
        foreach ($gallery_order as $position => $gallery_id) {
            update_post_meta($gallery_id, '_best_portfolio_gallery_sort_order', absint($position));
        }

        wp_send_json_success(array('message' => 'Sort order updated successfully'));
    }

    /**
     * Handle AJAX request to update gallery item sort order.
     *
     * @since    1.0.0
     */
    public function update_gallery_item_order() {
        // Check nonce
        if (!check_ajax_referer('best_portfolio_admin_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
        }

        // Check permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        // Get and validate data
        $item_order = isset($_POST['item_order']) ? $_POST['item_order'] : array();
        if (!is_array($item_order)) {
            wp_send_json_error(array('message' => 'Invalid data format'));
        }

        // Update sort order for each item
        foreach ($item_order as $position => $item_id) {
            update_post_meta($item_id, '_best_portfolio_item_sort_order', absint($position));
        }

        wp_send_json_success(array('message' => 'Sort order updated successfully'));
    }
}
