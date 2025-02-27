<?php
namespace BestPortfolio;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks. It also serves as the primary orchestrator for the plugin's
 * functionality.
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class Core {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * Define the core functionality of the plugin.
     *
     * Load dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->register_post_types();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     * - Loader. Orchestrates the hooks of the plugin.
     * - I18n. Defines internationalization functionality.
     * - Admin. Defines all hooks for the admin area.
     * - Public. Defines all hooks for the public side of the site.
     * - Post_Types. Registers custom post types.
     * - Taxonomies. Registers custom taxonomies.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        // Load required classes
        require_once BEST_PORTFOLIO_PATH . 'includes/Loader.php';
        require_once BEST_PORTFOLIO_PATH . 'includes/I18n.php';

        // Load Portfolio classes
        require_once BEST_PORTFOLIO_PATH . 'includes/Portfolio/PortfolioPostType.php';
        require_once BEST_PORTFOLIO_PATH . 'includes/Portfolio/GalleryPostType.php';
        require_once BEST_PORTFOLIO_PATH . 'includes/Portfolio/GalleryItemPostType.php';
        require_once BEST_PORTFOLIO_PATH . 'includes/Portfolio/GalleryTags.php';
        require_once BEST_PORTFOLIO_PATH . 'includes/Portfolio/GalleryItemTags.php';
        require_once BEST_PORTFOLIO_PATH . 'includes/Portfolio/TagFilter.php';

        // Load Public and Admin classes
        require_once BEST_PORTFOLIO_PATH . 'includes/Public/Display.php';
        require_once BEST_PORTFOLIO_PATH . 'includes/admin/AdminAssets.php';

        // Create the loader that will maintain and register all hooks for the plugin
        $this->loader = new Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the I18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new I18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new \BestPortfolio\Admin\AdminAssets('best-portfolio', BEST_PORTFOLIO_VERSION);

        // Register AJAX handlers first
        $this->loader->add_action('init', $plugin_admin, 'register_ajax_handlers');

        // Then register admin scripts and styles
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new \BestPortfolio\Public\Display();

        // Public scripts and styles
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Template handling
        $this->loader->add_filter('single_template', $plugin_public, 'load_single_template');
        $this->loader->add_filter('archive_template', $plugin_public, 'load_archive_template');
    }

    /**
     * Register portfolio post type and its taxonomies.
     *
     * @since    1.0.0
     * @access   private
     */
    private function register_post_types() {
        $portfolio = new \BestPortfolio\Portfolio\PortfolioPostType();

        // Register the post type and taxonomies
        $this->loader->add_action('init', $portfolio, 'register_post_type', 0);
        $this->loader->add_action('init', $portfolio, 'register_taxonomies', 1);

        // Initialize template loader
        $template_loader = new \BestPortfolio\Public\TemplateLoader();
        $template_loader->init();

        // Customize admin columns
        $this->loader->add_filter('manage_best_portfolio_posts_columns', $portfolio, 'customize_admin_columns');
        $this->loader->add_action('manage_best_portfolio_posts_custom_column', $portfolio, 'handle_admin_columns', 10, 2);
        $this->loader->add_filter('manage_edit-best_portfolio_sortable_columns', $portfolio, 'make_columns_sortable');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }
}
