<?php
/**
 * Layout hooks and filters
 *
 * @package GP_Child_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Layout setup
function gp_layout_setup() {
    remove_action( 'generate_after_entry_title', 'generate_post_meta' );
    remove_action( 'generate_after_entry_header', 'generate_post_meta' );
    remove_action( 'generate_after_entry_header', 'generate_post_image' );
    add_filter( 'generate_show_post_navigation', '__return_false' );

    add_action( 'generate_before_entry_title', 'gp_breadcrumb_output', 5 );
    add_action( 'generate_after_entry_title', 'gp_meta_after_title', 10 );
    add_action( 'generate_after_entry_header', 'gp_featured_image_output', 15 );
    add_action( 'generate_after_entry_header', 'gp_insert_toc', 20 );

    add_action( 'generate_after_entry_content', 'gp_child_display_after_content_widget_area', 8 );
    add_action( 'generate_after_entry_content', 'gppress_tags_before_related', 9);
    add_action( 'generate_after_entry_content', 'gp_render_post_footer_sections', 11 );
    add_action( 'generate_after_entry_content', 'gp_series_posts_output', 15 );
    add_action( 'generate_after_entry_content', 'gp_custom_post_navigation_output', 20 );

    add_action( 'generate_before_entry_content', 'gp_featured_image_output', 5 );

    add_action( 'wp_footer', 'gp_add_footer_elements_and_scripts' );

    add_action( 'wp_footer', 'gp_add_footer_elements_and_scripts' );
}
add_action( 'wp', 'gp_layout_setup' );


// add_filter( 'generate_copyright', '__return_empty_string' );
add_filter( 'generate_show_categories', '__return_false' );
add_filter( 'generate_footer_entry_meta_items', function($items) { return array_diff($items, ['categories', 'tags', 'comments']); } );
add_filter( 'excerpt_length', function($length) { return 55; }, 999 );
add_filter( 'generate_excerpt_more_output', function() { return ' …'; } );

function gp_add_elements_to_excerpt( $excerpt ) {
    if ( is_singular() || ! in_the_loop() ) {
        return $excerpt;
    }

    ob_start();
    gp_read_more_btn_output();
    gp_add_tags_to_list();
    gp_add_star_rating_to_list();
    $additional_elements = ob_get_clean();

    return $excerpt . $additional_elements;
}
add_filter( 'the_excerpt', 'gp_add_elements_to_excerpt' );
