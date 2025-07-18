<?php
/**
 * Comments functionality
 *
 * @package GP_Child_Theme
 */

if ( ! defined( 'ABSPath' ) ) {
	exit; // Exit if accessed directly.
}

// Filter to modify the comment fields
add_filter( 'comment_form_default_fields', 'gp_modify_comment_fields' );
add_filter( 'preprocess_comment', 'gp_handle_anonymous_comment' );
add_filter( 'comment_post_redirect', 'gp_redirect_to_comment', 10, 2 );

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

/**
 * Redirect to the new comment after submission.
 *
 * @param string $location The redirect location.
 * @param object $comment The comment object.
 * @return string The modified redirect location.
 */
function gp_redirect_to_comment( $location, $comment ) {
    return get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID;
}
