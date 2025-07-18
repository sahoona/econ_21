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
            'Name',
            ( $req ? ' <span class="required">*</span>' : '' ),
            esc_attr( $commenter['comment_author'] ),
            $aria_req
        )
    );

    $fields['email'] = sprintf(
        '<p class="comment-form-email">%s %s</p>',
        sprintf(
            '<label for="email">%s%s</label> <input id="email" name="email" type="email" value="%s" size="30" aria-describedby="email-notes"%s />',
            'Email',
            ( $req ? ' <span class="required">*</span>' : '' ),
            esc_attr( $commenter['comment_author_email'] ),
            $aria_req
        ),
        sprintf(
            '<span class="comment-form-email-privacy"><input id="wp-comment-email-privacy" name="wp-comment-email-privacy" type="checkbox" value="true" /> <label for="wp-comment-email-privacy">%s</label></span>',
            'I do not wish to disclose my email address.'
        )
    );

    $fields['url'] = sprintf(
        '<p class="comment-form-url">%s</p>',
        sprintf(
            '<label for="url">%s</label><input id="url" name="url" type="url" value="%s" size="30" />',
            'Website',
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
        'Your email address will not be published.' .
        '</p>';
    $defaults['comment_notes_after'] = '';
    $defaults['label_submit'] = 'Post Comment';
    $defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment">' . 'Comment' . '</label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>';
    $defaults['fields']['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . ( isset($_COOKIE['comment_author_'.COOKIEHASH]) ? ' checked="checked"' : '' ) . ' /> <label for="wp-comment-cookies-consent">' . 'Save my name, email, and website in this browser for the next time I comment.' . '</label></p>';
    $defaults['title_reply'] = 'Leave a Reply';
    $defaults['title_reply_to'] = 'Leave a Reply to %s';
    $defaults['cancel_reply_link'] = 'Cancel Reply';
    $defaults['comment_moderation'] = 'Your comment is awaiting moderation.';

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
        // Temporarily disable the email requirement for this specific comment
        add_filter( 'pre_option_require_name_email', '__return_false', 9999 );
        $commentdata['comment_author_email'] = '';
        // IMPORTANT: You might need to remove this filter later depending on your needs.
        // For example, using remove_filter after wp_insert_comment.
    }
    return $commentdata;
}

add_action( 'wp_insert_comment', 'gp_remove_anonymous_comment_filter', 9999, 2 );

function gp_remove_anonymous_comment_filter( $id, $comment ) {
    if ( isset( $_POST['wp-comment-email-privacy'] ) && $_POST['wp-comment-email-privacy'] === 'true' ) {
        remove_filter( 'pre_option_require_name_email', '__return_false', 9999 );
    }
}

function gp_limit_comment_depth( $args ) {
    $args['max_depth'] = 2;
    $args['callback'] = 'gp_custom_comment';
    return $args;
}
add_filter( 'wp_list_comments_args', 'gp_limit_comment_depth' );

function gp_custom_comment( $comment, $args, $depth ) {
    ?>
    <li <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
        <article id="div-comment-<?php comment_ID() ?>" class="comment-body">
            <div class="comment-author vcard">
                <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
                <div class="comment-author-info">
                    <span class="fn"><?php echo get_comment_author_link(); ?></span>
                    <span class="comment-meta commentmetadata">
                        <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
                            <?php
                            /* translators: 1: date, 2: time */
                            printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time() ); ?>
                        </a>
                        <?php edit_comment_link( __( '(Edit)' ), '  ', '' ); ?>
                    </span>
                </div>
            </div>

            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

            <div class="reply">
                <?php comment_reply_link( array_merge( $args, array( 'add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            </div>
        </article>
    <?php
}
