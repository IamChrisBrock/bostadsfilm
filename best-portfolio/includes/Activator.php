<?php
namespace BestPortfolio;

/**
 * Fired during plugin activation and deactivation.
 *
 * This class defines all code necessary to run during the plugin's activation and
 * deactivation. It handles:
 * - Database table creation
 * - Default options setup
 * - Capability setup
 * - Flushing rewrite rules
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class Activator {

    /**
     * Runs during plugin activation.
     *
     * This function is called when the plugin is activated. It handles:
     * - Creating necessary database tables
     * - Setting up default options
     * - Setting up roles and capabilities
     * - Flushing rewrite rules for custom post types
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Create necessary database tables
        self::create_tables();

        // Set up default options
        self::setup_options();

        // Set up capabilities
        self::setup_capabilities();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Runs during plugin deactivation.
     *
     * This function is called when the plugin is deactivated. It handles:
     * - Cleaning up any necessary data
     * - Removing capabilities if needed
     * - Flushing rewrite rules
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Clean up plugin-specific capabilities
        self::cleanup_capabilities();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create any necessary database tables.
     *
     * @since    1.0.0
     * @access   private
     */
    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Example table creation (uncomment and modify as needed)
        /*
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}best_portfolio_gallery (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            media_id bigint(20) NOT NULL,
            sort_order int(11) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        */
    }

    /**
     * Set up default plugin options.
     *
     * @since    1.0.0
     * @access   private
     */
    private static function setup_options() {
        $defaults = array(
            'gallery_columns' => 3,
            'items_per_page' => 12,
            'enable_lightbox' => true,
            'enable_filtering' => true,
            'enable_sorting' => true,
            'image_size' => 'large',
            'layout_style' => 'grid',
        );

        foreach ($defaults as $key => $value) {
            if (false === get_option('best_portfolio_' . $key)) {
                add_option('best_portfolio_' . $key, $value);
            }
        }
    }

    /**
     * Set up custom capabilities for portfolio management.
     *
     * @since    1.0.0
     * @access   private
     */
    private static function setup_capabilities() {
        $admin = get_role('administrator');

        $capabilities = array(
            'edit_portfolio',
            'read_portfolio',
            'delete_portfolio',
            'edit_portfolios',
            'edit_others_portfolios',
            'publish_portfolios',
            'read_private_portfolios',
            'delete_portfolios',
            'delete_private_portfolios',
            'delete_published_portfolios',
            'delete_others_portfolios',
            'edit_private_portfolios',
            'edit_published_portfolios',
        );

        foreach ($capabilities as $cap) {
            $admin->add_cap($cap);
        }
    }

    /**
     * Clean up custom capabilities.
     *
     * @since    1.0.0
     * @access   private
     */
    private static function cleanup_capabilities() {
        // Optionally remove capabilities during deactivation
        // Usually, it's better to leave them in case of reactivation
    }
}
