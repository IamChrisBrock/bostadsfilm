<?php
/**
 * Register custom taxonomies for the theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the Project Tags taxonomy
 */
function register_project_tags_taxonomy() {
    $labels = array(
        'name'                       => _x('Project Tags', 'Taxonomy general name', 'filmestate'),
        'singular_name'              => _x('Project Tag', 'Taxonomy singular name', 'filmestate'),
        'search_items'               => __('Search Project Tags', 'filmestate'),
        'popular_items'              => __('Popular Project Tags', 'filmestate'),
        'all_items'                  => __('All Project Tags', 'filmestate'),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __('Edit Project Tag', 'filmestate'),
        'update_item'                => __('Update Project Tag', 'filmestate'),
        'add_new_item'               => __('Add New Project Tag', 'filmestate'),
        'new_item_name'              => __('New Project Tag Name', 'filmestate'),
        'separate_items_with_commas' => __('Separate project tags with commas', 'filmestate'),
        'add_or_remove_items'        => __('Add or remove project tags', 'filmestate'),
        'choose_from_most_used'      => __('Choose from the most used project tags', 'filmestate'),
        'menu_name'                  => __('Project Tags', 'filmestate'),
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => true,
        'show_in_rest'      => true, // Enable Gutenberg editor
        'rewrite'           => array('slug' => 'project-tag'),
    );

    register_taxonomy('project_tags', array('project_gallery', 'attachment'), $args);
}
add_action('init', 'register_project_tags_taxonomy');
