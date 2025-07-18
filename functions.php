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

// -----------------------------------------------------------------------------
// Core Theme Setup
// -----------------------------------------------------------------------------

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


// -----------------------------------------------------------------------------
// Comments Customization
// -----------------------------------------------------------------------------

/**
 * Master function to hook all comment-related customizations.
 */
function gp_comments_customizations() {
    add_filter( 'comment_form_default_fields', 'gp_modify_comment_fields' );
    add_filter( 'comment_form_defaults', 'gp_customize_comment_form' );
    add_filter( 'preprocess_comment', 'gp_handle_anonymous_comment' );
    add_action( 'wp_insert_comment', 'gp_remove_anonymous_comment_filter', 10, 2 );
    add_filter( 'wp_list_comments_args', 'gp_customize_comments_display' );
}
add_action( 'after_setup_theme', 'gp_comments_customizations' );

/**
 * Customizes the display of comments (callback, depth, order).
 *
 * @param array $args Arguments for wp_list_comments().
 * @return array Modified arguments.
 */
function gp_customize_comments_display( $args ) {
    $args['callback'] = 'gp_custom_comment_html';
    $args['max_depth'] = 2; // Limits comment threading to two levels.
    $args['reverse_top_level'] = false; // Ensures newest comments are at the top.
    return $args;
}

/**
 * Generates the custom HTML for each comment.
 *
 * @param object $comment The comment object.
 * @param array  $args    An array of arguments.
 * @param int    $depth   The depth of the comment.
 */
function gp_custom_comment_html( $comment, $args, $depth ) {
    ?>
    <li <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
        <article id="div-comment-<?php comment_ID() ?>" class="comment-body">
            <div class="comment-author vcard">
                <?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
                <div class="comment-author-info">
                    <span class="fn"><?php echo get_comment_author_link(); ?></span>
                    <span class="comment-meta commentmetadata">
                        <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
                            <?php
                            // Translators: 1: date, 2: time.
                            printf(
                                '%1$s at %2$s',
                                get_comment_date( 'F j, Y' ), // e.g., "September 1, 2023"
                                date_i18n( 'g:i a', get_comment_time( 'U' ) ) // e.g., "3:30 pm"
                            );
                            ?>
                        </a>
                        <?php edit_comment_link( '(Edit)', ' &nbsp;&nbsp;', '' ); ?>
                    </span>
                </div>
            </div>

            <?php if ( '0' == $comment->comment_approved ) : ?>
                <p class="comment-awaiting-moderation">Your comment is awaiting moderation.</p>
            <?php endif; ?>

            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

            <div class="reply">
                <?php
                comment_reply_link(
                    array_merge(
                        $args,
                        array(
                            'add_below' => 'div-comment',
                            'depth'     => $depth,
                            'max_depth' => $args['max_depth'],
                            'reply_text'=> 'Reply',
                        )
                    )
                );
                ?>
            </div>
        </article>
    <?php
}

/**
 * Modifies the default comment form fields (Name, Email, Website).
 *
 * @param array $fields The default comment fields.
 * @return array The modified fields.
 */
function gp_modify_comment_fields( $fields ) {
    $commenter = wp_get_current_commenter();
    $req       = get_option( 'require_name_email' );
    $aria_req  = ( $req ? " aria-required='true'" : '' );

    $fields['author'] = sprintf(
        '<p class="comment-form-author"><label for="author">%s%s</label> <input id="author" name="author" type="text" value="%s" size="30"%s /></p>',
        'Name',
        ( $req ? ' <span class="required">*</span>' : '' ),
        esc_attr( $commenter['comment_author'] ),
        $aria_req
    );

    $fields['email'] = sprintf(
        '<p class="comment-form-email"><label for="email">%s%s</label> <input id="email" name="email" type="email" value="%s" size="30" aria-describedby="email-notes"%s /></p>',
        'Email',
        ( $req ? ' <span class="required">*</span>' : '' ),
        esc_attr( $commenter['comment_author_email'] ),
        $aria_req
    );

    $fields['url'] = sprintf(
        '<p class="comment-form-url"><label for="url">%s</label><input id="url" name="url" type="url" value="%s" size="30" /></p>',
        'Website',
        esc_attr( $commenter['comment_author_url'] )
    );

    $fields['cookies'] = sprintf(
        '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"%s /> <label for="wp-comment-cookies-consent">%s</label></p>',
        ( isset( $_COOKIE['comment_author_' . COOKIEHASH] ) ? ' checked="checked"' : '' ),
        'Save my name, email, and website in this browser for the next time I comment.'
    );

    return $fields;
}


/**
 * Customizes the overall comment form (notes, submit button, etc.).
 *
 * @param array $defaults The default comment form arguments.
 * @return array The modified arguments.
 */
function gp_customize_comment_form( $defaults ) {
    $defaults['comment_field'] = sprintf(
        '<p class="comment-form-comment"><label for="comment">%s</label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>',
        'Comment'
    );
    $defaults['comment_notes_before'] = '<p class="comment-notes">Your email address will not be published.</p>';
    $defaults['comment_notes_after'] = ''; // Removes the "You may use these HTML tags..." message.
    $defaults['label_submit'] = 'Post Comment';
    $defaults['title_reply'] = 'Leave a Reply';
    $defaults['title_reply_to'] = 'Leave a Reply to %s';
    $defaults['cancel_reply_link'] = 'Cancel Reply';
    $defaults['id_form'] = 'commentform';
    $defaults['id_submit'] = 'submit';
    $defaults['title_reply_before'] = '<h3 id="reply-title" class="comment-reply-title">';
    $defaults['title_reply_after'] = '</h3>';

    return $defaults;
}

/**
 * Handles anonymous comments by clearing the email field if a privacy checkbox is ticked.
 *
 * @param array $commentdata Comment data.
 * @return array Modified comment data.
 */
function gp_handle_anonymous_comment( $commentdata ) {
    if ( isset( $_POST['wp-comment-email-privacy'] ) && $_POST['wp-comment-email-privacy'] === 'true' ) {
        // Temporarily disable the email requirement for this specific comment.
        add_filter( 'pre_option_require_name_email', '__return_false', 9999 );
        $commentdata['comment_author_email'] = '';
    }
    return $commentdata;
}

/**
 * Removes the temporary filter after an anonymous comment is inserted.
 *
 * @param int    $id      The comment ID.
 * @param object $comment The comment object.
 */
function gp_remove_anonymous_comment_filter( $id, $comment ) {
    if ( isset( $_POST['wp-comment-email-privacy'] ) && $_POST['wp-comment-email-privacy'] === 'true' ) {
        remove_filter( 'pre_option_require_name_email', '__return_false', 9999 );
    }
}

// -----------------------------------------------------------------------------
// Other Theme Functions
// -----------------------------------------------------------------------------

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
