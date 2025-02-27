<?php
namespace BestPortfolio\Licensing;

/**
 * The licensing functionality of the plugin.
 *
 * Handles all license-related functionality including:
 * - License activation/deactivation
 * - License validation
 * - Premium feature access control
 * - Update checking and delivery
 * - License expiration management
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes/licensing
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class LicenseManager {

    /**
     * The single instance of the class.
     *
     * @since    1.0.0
     * @access   protected
     * @var      LicenseManager    $instance    The single instance of the class.
     */
    protected static $instance = null;

    /**
     * The license key option name in the WordPress options table.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $license_key_option    The license key option name.
     */
    private $license_key_option = 'best_portfolio_license_key';

    /**
     * The license status option name in the WordPress options table.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $license_status_option    The license status option name.
     */
    private $license_status_option = 'best_portfolio_license_status';

    /**
     * The license expiry option name in the WordPress options table.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $license_expiry_option    The license expiry option name.
     */
    private $license_expiry_option = 'best_portfolio_license_expiry';

    /**
     * Initialize the licensing system.
     *
     * @since    1.0.0
     */
    public function init() {
        // Add license management page
        add_action('admin_menu', array($this, 'add_license_menu'));
        
        // Add license settings and handlers
        add_action('admin_init', array($this, 'register_license_settings'));
        add_action('admin_init', array($this, 'handle_license_actions'));
        add_action('admin_init', array($this, 'check_license'));
        add_action('admin_init', array($this, 'setup_auto_updater'));

        // Add admin notices
        add_action('admin_notices', array($this, 'show_license_notices'));
    }

    /**
     * Add the license management menu item.
     *
     * @since    1.0.0
     */
    public function add_license_menu() {
        add_submenu_page(
            'options-general.php',
            __('Best Portfolio License', 'best-portfolio'),
            __('Best Portfolio', 'best-portfolio'),
            'manage_options',
            'best-portfolio-license',
            array($this, 'render_license_page')
        );
    }



    /**
     * Render the license management page.
     *
     * @since    1.0.0
     */
    public function render_license_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('best_portfolio_license');
                do_settings_sections('best_portfolio_license');
                submit_button(__('Save License', 'best-portfolio'));
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register the license settings.
     *
     * @since    1.0.0
     */
    /**
     * Check license status
     *
     * @since    1.0.0
     */
    public function check_license() {
        $license_key = get_option($this->license_key_option);
        $license_status = get_option($this->license_status_option);
        $license_expiry = get_option($this->license_expiry_option);

        if (!$license_key || $license_status !== 'valid') {
            // License is not active
            return;
        }

        if ($license_expiry && strtotime($license_expiry) < time()) {
            // License has expired
            update_option($this->license_status_option, 'expired');
        }
    }

    /**
     * Setup automatic updates
     *
     * @since    1.0.0
     */
    public function setup_auto_updater() {
        if (get_option($this->license_status_option) === 'valid') {
            // Add filters for automatic updates
            add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_updates'));
            add_filter('plugins_api', array($this, 'plugins_api_filter'), 10, 3);
        }
    }

    /**
     * Show license-related notices
     *
     * @since    1.0.0
     */
    public function show_license_notices() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $license_status = get_option($this->license_status_option);

        if ($license_status === 'expired') {
            echo '<div class="notice notice-error"><p>' . 
                 esc_html__('Your Best Portfolio license has expired. Please renew your license to continue receiving updates and support.', 'best-portfolio') . 
                 '</p></div>';
        } elseif ($license_status !== 'valid') {
            echo '<div class="notice notice-warning"><p>' . 
                 esc_html__('Please activate your Best Portfolio license to receive updates and support.', 'best-portfolio') . 
                 '</p></div>';
        }
    }

    /**
     * Check for updates
     *
     * @since    1.0.0
     * @param    object    $transient    The transient data for plugin updates
     * @return   object    Modified transient data
     */
    public function check_for_updates($transient) {
        // Here you would typically check your license server for updates
        // For now, we'll return the transient as is
        return $transient;
    }

    /**
     * Filter for plugins API
     *
     * @since    1.0.0
     * @param    false|object|array    $result    The result object or array
     * @param    string               $action    The API function being performed
     * @param    object               $args      Plugin arguments
     * @return   object|bool                     Plugin info or false
     */
    public function plugins_api_filter($result, $action, $args) {
        // Here you would typically provide plugin information from your license server
        // For now, we'll return the result as is
        return $result;
    }

    public function register_license_settings() {
        // Register core options
        register_setting('best_portfolio_license', $this->license_key_option);
        register_setting('best_portfolio_license', $this->license_status_option);
        register_setting('best_portfolio_license', $this->license_expiry_option);

        // Add settings section
        add_settings_section(
            'best_portfolio_license_section',
            __('License Settings', 'best-portfolio'),
            array($this, 'render_license_section'),
            'best_portfolio_license'
        );

        // Add license key field
        add_settings_field(
            'best_portfolio_license_key',
            __('License Key', 'best-portfolio'),
            array($this, 'render_license_field'),
            'best_portfolio_license',
            'best_portfolio_license_section'
        );
    }

    /**
     * Render the license settings section description.
     *
     * @since    1.0.0
     */
    public function render_license_section() {
        echo '<p>' . esc_html__('Enter your license key to enable automatic updates and premium features.', 'best-portfolio') . '</p>';
    }

    /**
     * Render the license key field.
     *
     * @since    1.0.0
     */
    public function render_license_field() {
        $license = get_option($this->license_key_option);
        ?>
        <input type="text"
               name="<?php echo esc_attr($this->license_key_option); ?>"
               value="<?php echo esc_attr($license); ?>"
               class="regular-text"
               placeholder="<?php esc_attr_e('Enter your license key', 'best-portfolio'); ?>"
        />
        <?php
    }

    /**
     * Validate a license key with the licensing server.
     *
     * @since    1.0.0
     * @param    string    $license_key    The license key to validate.
     * @return   array    Response array containing success/error status and message.
     */
    private function validate_license($license_key) {
        // For now, return a mock successful response
        // TODO: Implement actual license validation with a remote server
        return array(
            'success' => true,
            'message' => __('License activated successfully', 'best-portfolio'),
            'expiry' => date('Y-m-d', strtotime('+1 year')),
            'status' => 'valid'
        );
    }

    /**
     * Handle license activation and deactivation actions.
     *
     * @since    1.0.0
     */
    public function handle_license_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Handle activation
        if (isset($_POST['best_portfolio_activate_license'])) {
            $this->activate_license();
        }

        // Handle deactivation
        if (isset($_POST['best_portfolio_deactivate_license'])) {
            $this->deactivate_license();
        }
    }

    /**
     * Activate the license.
     *
     * @since    1.0.0
     */
    private function activate_license() {
        if (!check_admin_referer('best_portfolio_license_nonce', 'best_portfolio_license_nonce')) {
            return;
        }

        $license_key = trim(get_option($this->license_key_option));

        // TODO: Make API call to your licensing server to validate and activate the license
        $response = $this->validate_license($license_key);

        if ($response) {
            update_option($this->license_status_option, 'valid');
            update_option($this->license_expiry_option, strtotime('+1 year'));
            $this->add_notice('success', __('License activated successfully!', 'best-portfolio'));
        } else {
            update_option($this->license_status_option, 'invalid');
            $this->add_notice('error', __('Failed to activate license. Please check your license key.', 'best-portfolio'));
        }
    }

    /**
     * Deactivate the license.
     *
     * @since    1.0.0
     */
    private function deactivate_license() {
        if (!check_admin_referer('best_portfolio_license_nonce', 'best_portfolio_license_nonce')) {
            return;
        }

        // TODO: Make API call to your licensing server to deactivate the license
        update_option($this->license_status_option, 'inactive');
        delete_option($this->license_expiry_option);
        $this->add_notice('success', __('License deactivated successfully.', 'best-portfolio'));
    }

    /**
     * Check if the license is valid.
     *
     * @since    1.0.0
     * @return   boolean    True if license is valid, false otherwise.
     */
    public function is_license_valid() {
        $status = get_option($this->license_status_option);
        $expiry = get_option($this->license_expiry_option);

        if ($status !== 'valid') {
            return false;
        }

        if ($expiry && time() > $expiry) {
            update_option($this->license_status_option, 'expired');
            return false;
        }

        return true;
    }

    /**
     * Add an admin notice.
     *
     * @since    1.0.0
     * @param    string    $type       The type of notice (error, warning, success).
     * @param    string    $message    The notice message.
     */
    private function add_notice($type, $message) {
        add_settings_error(
            'best_portfolio_license',
            'best_portfolio_license_' . $type,
            $message,
            $type === 'success' ? 'updated' : 'error'
        );
    }

    /**
     * Get the single instance of this class.
     *
     * @since    1.0.0
     * @return   License_Manager    The single instance of this class.
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
