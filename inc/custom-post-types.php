<?php
/**
 * Register custom post types for the theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the Project Gallery post type
 */
function register_project_gallery_post_type() {
    $labels = array(
        'name'                  => _x('Project Galleries', 'Post type general name', 'filmestate'),
        'singular_name'         => _x('Project Gallery', 'Post type singular name', 'filmestate'),
        'menu_name'            => _x('Project Galleries', 'Admin Menu text', 'filmestate'),
        'name_admin_bar'       => _x('Project Gallery', 'Add New on Toolbar', 'filmestate'),
        'add_new'              => __('Add New', 'filmestate'),
        'add_new_item'         => __('Add New Project Gallery', 'filmestate'),
        'new_item'             => __('New Project Gallery', 'filmestate'),
        'edit_item'            => __('Edit Project Gallery', 'filmestate'),
        'view_item'            => __('View Project Gallery', 'filmestate'),
        'all_items'            => __('All Project Galleries', 'filmestate'),
        'search_items'         => __('Search Project Galleries', 'filmestate'),
        'not_found'            => __('No project galleries found.', 'filmestate'),
        'not_found_in_trash'   => __('No project galleries found in Trash.', 'filmestate'),
        'featured_image'       => _x('Project Cover Image', 'Overrides the "Featured Image" phrase', 'filmestate'),
        'set_featured_image'   => _x('Set cover image', 'Overrides the "Set featured image" phrase', 'filmestate'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase', 'filmestate'),
        'archives'             => _x('Project Gallery archives', 'The post type archive label used in nav menus', 'filmestate'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'project'),
        'capability_type'   => 'post',
        'has_archive'       => true,
        'hierarchical'      => false,
        'menu_position'     => 5,
        'menu_icon'         => 'dashicons-format-gallery',
        'supports'          => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'      => true, // Enable Gutenberg editor
    );

    register_post_type('project_gallery', $args);
}
add_action('init', 'register_project_gallery_post_type');
