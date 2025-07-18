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

    // Check for cached reading time
    $cached_data = get_transient( 'gp_reading_time_' . $post_id );
    if ( false !== $cached_data ) {
        return $cached_data;
    }

    $content = get_post_field( 'post_content', $post_id );
    $word_count = gp_custom_word_count($content);
    $reading_time = ceil( $word_count / 225 );
    $reading_time = max( 1, $reading_time );

    $data = array(
        'time' => $reading_time . ' min read',
        'words' => $word_count
    );

    // Cache the reading time for 24 hours
    set_transient( 'gp_reading_time_' . $post_id, $data, 24 * HOUR_IN_SECONDS );

    return $data;
}

// Clear the cached reading time when a post is updated
function gp_clear_reading_time_cache( $post_id ) {
    delete_transient( 'gp_reading_time_' . $post_id );
}
add_action( 'save_post', 'gp_clear_reading_time_cache' );

// Enable comments on all posts
function gp_enable_comments( $post ) {
    if ( $post instanceof WP_Post && $post->post_type == 'post' && comments_open( $post->ID ) ) {
        // Comments are already open
        return;
    }

    if ( $post instanceof WP_Post && $post->post_type == 'post' ) {
        // Open comments
        wp_update_post( array(
            'ID' => $post->ID,
            'comment_status' => 'open',
            'ping_status' => 'open'
        ) );
    }
}
add_action( 'the_post', 'gp_enable_comments' );

// Filter to modify the comment fields
add_filter( 'comment_form_default_fields', 'gp_modify_comment_fields' );
add_filter( 'preprocess_comment', 'gp_handle_anonymous_comment' );

/**
 * Modify the default comment fields.
 *
 * @param array $fields The default comment fields.
 * @return array The modified comment fields.
 */
function gp_modify_comment_fields( $fields ) {
    $commenter = wp_get_current_commenter();
    $req       = get_option( 'require_name_email' );
    $aria_req  = ( $req ? " aria-required='true'" : '' );

    // Add a class to the comment notes
    add_filter( 'comment_form_defaults', 'gp_comment_form_defaults' );

    // Unset the original email and URL fields
    unset( $fields['email'], $fields['url'] );

    $fields['author'] = sprintf(
        '<p class="comment-form-author">%s</p>',
        sprintf(
            '<label for="author">%s%s</label> <input id="author" name="author" type="text" value="%s" size="30"%s />',
            __( 'Name', 'gp-child-theme' ),
            ( $req ? ' <span class="required">*</span>' : '' ),
            esc_attr( $commenter['comment_author'] ),
            $aria_req
        )
    );

    $fields['email'] = sprintf(
        '<p class="comment-form-email">%s %s</p>',
        sprintf(
            '<label for="email">%s%s</label> <input id="email" name="email" type="email" value="%s" size="30" aria-describedby="email-notes"%s />',
            __( 'Email', 'gp-child-theme' ),
            ( $req ? ' <span class="required">*</span>' : '' ),
            esc_attr( $commenter['comment_author_email'] ),
            $aria_req
        ),
        sprintf(
            '<span class="comment-form-email-privacy"><input id="wp-comment-email-privacy" name="wp-comment-email-privacy" type="checkbox" value="true" /> <label for="wp-comment-email-privacy">%s</label></span>',
            __( 'Do not wish to disclose.', 'gp-child-theme' )
        )
    );

    $fields['url'] = sprintf(
        '<p class="comment-form-url">%s</p>',
        sprintf(
            '<label for="url">%s</label><input id="url" name="url" type="url" value="%s" size="30" />',
            __( 'Website', 'gp-child-theme' ),
            esc_attr( $commenter['comment_author_url'] )
        )
    );

    return $fields;
}

/**
 * Modify the comment form defaults.
 *
 * @param array $defaults The default comment form arguments.
 * @return array The modified comment form arguments.
 */
function gp_comment_form_defaults( $defaults ) {
    $defaults['comment_notes_before'] = '<p class="comment-notes">' .
        __( 'Your email address will not be published.', 'gp-child-theme' ) .
        '</p>';
    return $defaults;
}

/**
 * Handle anonymous comments.
 *
 * If the "Do not wish to disclose" checkbox is checked,
 * this function will remove the email address from the comment data.
 *
 * @param array $commentdata Comment data.
 * @return array Comment data.
 */
function gp_handle_anonymous_comment( $commentdata ) {
    if ( isset( $_POST['wp-comment-email-privacy'] ) && $_POST['wp-comment-email-privacy'] === 'true' ) {
        $commentdata['comment_author_email'] = '';
    }
    return $commentdata;
}
