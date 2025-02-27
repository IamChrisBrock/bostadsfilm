<?php
/**
 * Best Portfolio
 *
 * @package           BestPortfolio
 * @author            Chris Brock
 * @copyright         2024 Chris Brock
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Best Portfolio
 * Plugin URI:        https://bestportfolio.com
 * Description:       A professional portfolio and gallery management system for WordPress with advanced features, custom layouts, and media management.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Chris Brock
 * Author URI:        https://chrisbrock.com
 * Text Domain:       best-portfolio
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('BEST_PORTFOLIO_VERSION', '1.0.0');

/**
 * Plugin base path.
 * This constant is used throughout the plugin to reference file paths.
 */
define('BEST_PORTFOLIO_PATH', plugin_dir_path(__FILE__));

/**
 * Plugin base URL.
 * This constant is used throughout the plugin to reference URLs for assets.
 */
define('BEST_PORTFOLIO_URL', plugin_dir_url(__FILE__));

/**
 * Load the autoloader
 * Uses Composer's autoloader for clean class loading
 */
require_once BEST_PORTFOLIO_PATH . 'vendor/autoload.php';

/**
 * Load plugin classes
 */
require_once BEST_PORTFOLIO_PATH . 'includes/licensing/LicenseManager.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 * 
 * @since 1.0.0
 * @return void
 */
function activate_best_portfolio() {
    require_once BEST_PORTFOLIO_PATH . 'includes/Activator.php';
    BestPortfolio\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-activator.php
 * 
 * @since 1.0.0
 * @return void
 */
function deactivate_best_portfolio() {
    require_once BEST_PORTFOLIO_PATH . 'includes/Activator.php';
    BestPortfolio\Activator::deactivate();
}

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_best_portfolio');
register_deactivation_hook(__FILE__, 'deactivate_best_portfolio');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 * @return   void
 */
function run_best_portfolio() {
    // Initialize Plugin Core first to register post types
    $plugin = new BestPortfolio\Core();
    $plugin->run();

    // Then initialize Licensing
    $license_manager = new BestPortfolio\Licensing\LicenseManager();
    $license_manager->init();
}

// Let's get started!
run_best_portfolio();
