<?php
namespace BestPortfolio\Public;

/**
 * Template Loader for Best Portfolio
 *
 * Handles loading of templates from the plugin directory
 *
 * @since      1.0.0
 * @package    BestPortfolio
 * @subpackage BestPortfolio/includes/Public
 * @author     Chris Brock <chris@chrisbrock.com>
 */
class TemplateLoader {

    /**
     * The path to the plugin templates directory
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $template_path    Path to templates directory
     */
    private $template_path;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->template_path = BEST_PORTFOLIO_PATH . 'templates/';
    }

    /**
     * Initialize template loading hooks
     *
     * @since    1.0.0
     */
    public function init() {
        add_filter('template_include', array($this, 'load_template'));
        add_filter('archive_template', array($this, 'load_archive_template'));
        add_filter('single_template', array($this, 'load_single_template'));
    }

    /**
     * Load template from plugin directory
     *
     * @since    1.0.0
     * @param    string    $template    Template path
     * @return   string    Modified template path
     */
    public function load_template($template) {
        global $post;

        if (is_post_type_archive('best_portfolio')) {
            $archive_template = $this->template_path . 'archive-portfolio.php';
            if (file_exists($archive_template)) {
                return $archive_template;
            }
        }

        if (is_singular('best_portfolio')) {
            $single_template = $this->template_path . 'single-portfolio.php';
            if (file_exists($single_template)) {
                return $single_template;
            }
        }

        return $template;
    }

    /**
     * Load archive template
     *
     * @since    1.0.0
     * @param    string    $template    Template path
     * @return   string    Modified template path
     */
    public function load_archive_template($template) {
        if (is_post_type_archive('best_portfolio')) {
            $archive_template = $this->template_path . 'archive-portfolio.php';
            if (file_exists($archive_template)) {
                return $archive_template;
            }
        }
        return $template;
    }

    /**
     * Load single template
     *
     * @since    1.0.0
     * @param    string    $template    Template path
     * @return   string    Modified template path
     */
    public function load_single_template($template) {
        global $post;

        if (is_singular('best_portfolio')) {
            $single_template = $this->template_path . 'single-portfolio.php';
            if (file_exists($single_template)) {
                return $single_template;
            }
        }
        return $template;
    }
}
