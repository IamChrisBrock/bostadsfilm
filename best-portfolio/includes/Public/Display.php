<?php
namespace BestPortfolio\Public;

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/public
 */
class Display {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        $css_file = BEST_PORTFOLIO_PATH . 'assets/css/best-portfolio-public.css';
        if (file_exists($css_file)) {
            wp_enqueue_style(
                'best-portfolio-public',
                BEST_PORTFOLIO_URL . 'assets/css/best-portfolio-public.css',
                array(),
                BEST_PORTFOLIO_VERSION,
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        $js_file = BEST_PORTFOLIO_PATH . 'assets/js/best-portfolio-public.js';
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'best-portfolio-public',
                BEST_PORTFOLIO_URL . 'assets/js/best-portfolio-public.js',
                array('jquery'),
                BEST_PORTFOLIO_VERSION,
                true
            );
        }
    }

    /**
     * Load single portfolio template.
     *
     * @param string $template The path of the template to include.
     * @return string
     */
    public function load_single_template($template) {
        if (is_singular('best_portfolio')) {
            $custom_template = BEST_PORTFOLIO_PATH . 'templates/single-portfolio.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }

    /**
     * Load archive portfolio template.
     *
     * @param string $template The path of the template to include.
     * @return string
     */
    public function load_archive_template($template) {
        if (is_post_type_archive('best_portfolio')) {
            $custom_template = BEST_PORTFOLIO_PATH . 'templates/archive-portfolio.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }
}
