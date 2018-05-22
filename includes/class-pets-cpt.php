<?php

/**
 * File where we define CPT, Taxonomies etc.
 */

Namespace Pets;

if( ! defined( 'ABSPATH' ) ) {
    return;
}

class CPT {

    /**
     * Initialize Pets CPT
     * @return void 
     */
    public function init() {
        register_post_type( 'pets', array(
            'labels'            => array(
                'name'               => __( 'Pets', 'pets' ),
                'singular_name'      => __( 'Pets', 'pets' ),
                'all_items'          => __( 'All Pets', 'pets' ),
                'new_item'           => __( 'New Pets', 'pets' ),
                'add_new'            => __( 'Add New', 'pets' ),
                'add_new_item'       => __( 'Add New Pets', 'pets' ),
                'edit_item'          => __( 'Edit Pets', 'pets' ),
                'view_item'          => __( 'View Pets', 'pets' ),
                'search_items'       => __( 'Search Pets', 'pets' ),
                'not_found'          => __( 'No Pets found', 'pets' ),
                'not_found_in_trash' => __( 'No Pets found in trash', 'pets' ),
                'parent_item_colon'  => __( 'Parent Pets', 'pets' ),
                'menu_name'          => __( 'Pets', 'pets' ),
            ),
            'public'            => true,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_in_nav_menus' => true,
            'supports'          => array( 'title', 'editor', 'thumbnail' ),
            'has_archive'       => true,
            'rewrite'           => true,
            'query_var'         => true,
            'menu_icon'         => 'dashicons-twitter',
            'show_in_rest'      => true,
            'rest_base'         => 'pets',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        ) );

        register_taxonomy( 'breed', array( 'pets' ), array(
            'hierarchical'      => true,
            'public'            => true,
            'show_in_nav_menus' => true,
            'show_ui'           => true,
            'show_admin_column' => false,
            'query_var'         => true,
            'rewrite'           => true,
            'capabilities'      => array(
                'manage_terms'  => 'edit_posts',
                'edit_terms'    => 'edit_posts',
                'delete_terms'  => 'edit_posts',
                'assign_terms'  => 'edit_posts'
            ),
            'labels'            => array(
                'name'                       => __( 'Breed', 'pets' ),
                'singular_name'              => _x( 'Breed', 'taxonomy general name', 'pets' ),
                'search_items'               => __( 'Search Breed', 'pets' ),
                'popular_items'              => __( 'Popular Breed', 'pets' ),
                'all_items'                  => __( 'All Breed', 'pets' ),
                'parent_item'                => __( 'Parent Breed', 'pets' ),
                'parent_item_colon'          => __( 'Parent Breed:', 'pets' ),
                'edit_item'                  => __( 'Edit Breed', 'pets' ),
                'update_item'                => __( 'Update Breed', 'pets' ),
                'add_new_item'               => __( 'New Breed', 'pets' ),
                'new_item_name'              => __( 'New Breed', 'pets' ),
                'separate_items_with_commas' => __( 'Separate Breed with commas', 'pets' ),
                'add_or_remove_items'        => __( 'Add or remove Breed', 'pets' ),
                'choose_from_most_used'      => __( 'Choose from the most used Breed', 'pets' ),
                'not_found'                  => __( 'No Breed found.', 'pets' ),
                'menu_name'                  => __( 'Breed', 'pets' ),
            ),
            'show_in_rest'      => true,
            'rest_base'         => 'breed',
            'rest_controller_class' => 'WP_REST_Terms_Controller',
        ) );

        register_taxonomy( 'pet-color', array( 'pets' ), array(
            'hierarchical'      => false,
            'public'            => true,
            'show_in_nav_menus' => true,
            'show_ui'           => true,
            'show_admin_column' => false,
            'query_var'         => true,
            'rewrite'           => true,
            'capabilities'      => array(
                'manage_terms'  => 'edit_posts',
                'edit_terms'    => 'edit_posts',
                'delete_terms'  => 'edit_posts',
                'assign_terms'  => 'edit_posts'
            ),
            'labels'            => array(
                'name'                       => __( 'Colors', 'pets' ),
                'singular_name'              => _x( 'Color', 'taxonomy general name', 'pets' ),
                'search_items'               => __( 'Search Colors', 'pets' ),
                'popular_items'              => __( 'Popular Colors', 'pets' ),
                'all_items'                  => __( 'All Colors', 'pets' ),
                'parent_item'                => __( 'Parent Color', 'pets' ),
                'parent_item_colon'          => __( 'Parent Color:', 'pets' ),
                'edit_item'                  => __( 'Edit Color', 'pets' ),
                'update_item'                => __( 'Update Color', 'pets' ),
                'add_new_item'               => __( 'New Color', 'pets' ),
                'new_item_name'              => __( 'New Color', 'pets' ),
                'separate_items_with_commas' => __( 'Separate Colors with commas', 'pets' ),
                'add_or_remove_items'        => __( 'Add or remove Colors', 'pets' ),
                'choose_from_most_used'      => __( 'Choose from the most used Colors', 'pets' ),
                'not_found'                  => __( 'No Colors found.', 'pets' ),
                'menu_name'                  => __( 'Colors', 'pets' ),
            ),
            'show_in_rest'      => true,
            'rest_base'         => 'pet-color',
            'rest_controller_class' => 'WP_REST_Terms_Controller',
        ) );
    }

    /**
     * Update messages related to our post types
     * @param  array  $messages Array with CPT update messages
     * @return array            Array with new messages
     */
    public function update_messages( $messages = array() ) {
        global $post;

        $permalink = get_permalink( $post );

        $messages['pets'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf( __('Pet updated. <a target="_blank" href="%s">View Pet</a>', 'pets'), esc_url( $permalink ) ),
            2 => __('Custom field updated.', 'pets'),
            3 => __('Custom field deleted.', 'pets'),
            4 => __('Pet updated.', 'pets'),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ? sprintf( __('Pet restored to revision from %s', 'pets'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
            6 => sprintf( __('Pet published. <a href="%s">View Pet</a>', 'pets'), esc_url( $permalink ) ),
            7 => __('Pet saved.', 'pets'),
            8 => sprintf( __('Pet submitted. <a target="_blank" href="%s">Preview Pets</a>', 'pets'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
            9 => sprintf( __('Pet scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Pet</a>', 'pets'),
            // translators: Publish box date format, see http://php.net/date
            date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
            10 => sprintf( __('Pet draft updated. <a target="_blank" href="%s">Preview Pet</a>', 'pets'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
        );

        return $messages;
    }

}