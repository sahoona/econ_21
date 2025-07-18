<?php
/**
 * GP Child Theme Functions
 *
 * @package    GP_Child_Theme
 * @version    22.7.16
 * @author     sh k & GP AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Load core theme functionality
require_once get_stylesheet_directory() . '/includes/core-setup.php';
require_once get_stylesheet_directory() . '/includes/assets.php';
require_once get_stylesheet_directory() . '/includes/optimization.php';
require_once get_stylesheet_directory() . '/includes/seo.php';
require_once get_stylesheet_directory() . '/includes/layout-hooks.php';
require_once get_stylesheet_directory() . '/includes/template-tags.php';
require_once get_stylesheet_directory() . '/includes/toc.php';
require_once get_stylesheet_directory() . '/includes/post-features.php';
require_once get_stylesheet_directory() . '/includes/related-posts.php';
require_once get_stylesheet_directory() . '/includes/ajax-handlers.php';
require_once get_stylesheet_directory() . '/includes/helpers.php';
require_once get_stylesheet_directory() . '/includes/admin.php';
require_once get_stylesheet_directory() . '/includes/customizer.php';
require_once get_stylesheet_directory() . '/includes/widgets.php';

// Load components
require_once get_stylesheet_directory() . '/components/ads/ads.php';

function custom_excerpt_length( $length ) {
    return 60;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/**
 * Get the estimated reading time for a post.
 *
 * @param int|null $post_id The ID of the post. Defaults to the current post.
 * @return string The estimated reading time in the format "X min read".
 */
function gp_get_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $content = get_post_field( 'post_content', $post_id );
    // More robust word count for multi-byte characters (like Korean)
    $decoded_content = html_entity_decode( strip_tags( $content ) );
    $word_count = count(preg_split('/\s+/u', $decoded_content, -1, PREG_SPLIT_NO_EMPTY));
    $reading_time = ceil( $word_count / 225 ); // Based on includes/post-features.php
    $reading_time = max( 1, $reading_time );
    return $reading_time . ' min read';
}
