<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Includes folder
 *
 * @version        1.0.0
 * @package        reviews-plus/includes
 * @author        Norbert Dreszer
 */
add_action( 'after_product_details', 'ic_add_ic_rev_form', 30 );

/**
 * Inserts default comments template on product page
 *
 */
function ic_add_ic_rev_form() {
	if ( comments_open() ) {
		remove_filter( 'comments_template', 'ic_clear_default_comments_form', 21 );
		comments_template( '', true );
		if ( is_main_query() ) {
			//remove_filter( 'comments_template', 'ic_replace_default_comments_form', 20 );
			add_filter( 'comments_template', 'ic_clear_default_comments_form', 21 );
		}
	}
}

/**
 * Shows reviews add form
 *
 * @param type $post_id
 * @param type $post_type
 */
function ic_reviews_form( $post_id = null, $post_type = null ) {
	if ( empty( $post_type ) && ! empty( $post_id ) ) {
		$post_type = get_post_type( $post_id );
	}
	$comment_type = ic_get_comment_type( $post_id, $post_type );
	add_filter( 'comment_form_default_fields', 'ic_revs_default_fields' );
	$redirect = '';
	$labels   = ic_get_reviews_labels();
	if ( ! empty( $post_id ) ) {
		$redirect = '<input type="hidden" name="ic_revs_redirect" value="' . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING'] . '">';
	}
	comment_form( array(
		'comment_notes_after' => ' ',
		'title_reply'         => do_shortcode( $labels['review_reply_title'] ),
		'must_log_in'         => '<p class="must-log-in">' . sprintf(
			/* translators: %s: login URL */
				__( 'You must be <a href="%s">logged in</a> to post a review.', 'reviews-plus' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( get_the_ID() ) ) )
			) . '</p>',
		'label_submit'        => $labels['review_submit_label'],
		'comment_field'       => $redirect . '<input type="hidden" name="comment_type" value="' . $comment_type . '"><input type="hidden" name="review_type" value="' . $comment_type . '">' . ic_review_rating_html() . '<p class="comment-form-title"><label for="title">' . $labels['review_title'] . '</label> <input type="text" value="" name="ic_review_title" id="title"></p><p class="comment-form-comment"><label for="comment">' . $labels['review_text'] . ' <span class="required">*</span></label> <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>'
	), $post_id );
	remove_filter( 'comment_form_default_fields', 'ic_revs_default_fields' );
}

function ic_review_rating_html() {
	$reviews_settings = get_ic_reviews_sep_settings();
	if ( empty( $reviews_settings['disable_rating'] ) ) {
		$labels = ic_get_reviews_labels();

		return '<p class="review-rating allow-edit"><label class="rating-label" for="ic_review_rating">' . $labels['rating_tite'] . '</label><input type="hidden" name="ic_review_rating"><span class="rate-1" data-rating="1"></span><span class="rate-2" data-rating="2"></span><span class="rate-3" data-rating="3"></span><span class="rate-4" data-rating="4"></span><span class="rate-5" data-rating="5"></span></p>';
	}
}

add_filter( 'get_the_excerpt', 'ic_check_get_excerpt', - 1 );

/**
 * Checks if excerpt is being called instead of content
 *
 * @param type $excertp
 *
 * @return type
 * @global boolean $ic_is_excerpt
 */
function ic_check_get_excerpt( $excertp ) {
	global $ic_is_excerpt;
	$ic_is_excerpt = true;

	return $excertp;
}

add_filter( 'get_the_excerpt', 'ic_uncheck_get_excerpt', 999 );

/**
 * Checks if excerpt is being called instead of content
 *
 * @param type $excertp
 *
 * @return type
 * @global boolean $ic_is_excerpt
 */
function ic_uncheck_get_excerpt( $excertp ) {
	global $ic_is_excerpt;
	$ic_is_excerpt = false;

	return $excertp;
}

add_filter( 'comments_template', 'ic_replace_default_comments_form', 20 );

/**
 * Override default comments template
 *
 * @param type $path
 *
 * @return type
 */
function ic_replace_default_comments_form( $path ) {
	if ( is_ic_post_type_review_enabled() || ( function_exists( 'is_ic_product_page' ) && is_ic_product_page() ) ) {
		return AL_REVIEWS_BASE_PATH . '/includes/review-form.php';
	}

	return $path;
}

/**
 * Clear default comments template
 *
 */
function ic_clear_default_comments_form() {
	return AL_REVIEWS_BASE_PATH . '/includes/empty-review-form.php';
}

function ic_reviews_list( $post_ids = null, $post_type = null, $tax_ids = null, $taxonomy = null ) {
	$comments = null;
	if ( ! empty( $post_ids ) || ! empty( $post_type ) || ! empty( $tax_ids ) ) {
		if ( ! empty( $tax_ids ) ) {
			if ( empty( $taxonomy ) ) {
				$post_types   = get_ic_review_active_post_types();
				$post_types[] = 'al_product';
				foreach ( $post_types as $type ) {
					$taxonomies = get_object_taxonomies( $type );
					if ( ! empty( $taxonomies ) ) {
						$taxonomy = reset( $taxonomies );
						break;
					}
				}
			}
			$args     = array(
				'post_type'      => $post_type,
				'tax_query'      => array(
					array(
						'taxonomy' => $taxonomy,
						'terms'    => $tax_ids,
						'field'    => 'term_id',
					),
				),
				'fields'         => 'ids',
				'post_status'    => 'publish',
				'posts_per_page' => 1000
			);
			$post_ids = get_posts( $args );
		}
		$args     = array(
			'post__in'  => $post_ids,
			'post_type' => $post_type,
			'status'    => 'approve'
		);
		$comments = get_comments( $args );
	}
	if ( ic_show_old_comments() ) {
		$display_comment_type = 'all';
	} else {
		$display_comment_type = ic_get_comment_type();
	}
	?>
    <div class="review-list">
		<?php
		wp_list_comments( array(
			'style'      => 'div',
			'short_ping' => true,
			'type'       => $display_comment_type,
			'reply_text' => __( 'Reply', 'reviews-plus' ),
			'callback'   => 'implecode_reviews',
		), $comments );
		?>
    </div><!-- .comment-list -->
	<?php
}

function ic_reviews_navigation( $where = 'above' ) {
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
		?>
        <nav id="review-nav-<?php echo $where ?>" class="review-navigation" role="navigation">
            <h1 class="screen-reader-text"><?php _e( 'Reviews navigation', 'reviews-plus' ); ?></h1>
            <div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Reviews', 'reviews-plus' ) ); ?></div>
            <div class="nav-next"><?php next_comments_link( __( 'Newer Reviews &rarr;', 'reviews-plus' ) ); ?></div>
        </nav><!-- #review-nav-above -->
		<?php
	}
}

function implecode_reviews( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	?>
<div <?php comment_class( 'ic_rev' ); ?> id="review-<?php comment_ID() ?>">
    <div class="review-left">
        <div class="reviewer-name">
            <span><?php echo get_comment_author_link() ?></span>
        </div>
        <div class="review-avatar">
			<?php
			echo get_avatar( $comment->comment_author_email, '68', '', get_the_author_meta( 'display_name' ) );
			?>
        </div>
    </div>
    <div class="review-right">
        <div class="review-rating">
			<?php echo ic_ic_rev_rating( $comment->comment_ID ) ?>
        </div>
        <div class="review-title">
			<?php echo ic_get_ic_rev_title( $comment->comment_ID ) ?>
        </div>
		<?php if ( $comment->comment_approved == '0' ) { ?>
			<?php implecode_info( __( 'Your review is awaiting moderation.', 'reviews-plus' ) ) ?>
		<?php } ?>

        <div class="review-text">
			<?php comment_text(); ?>
        </div>
        <div class="review-time">
			<?php echo ic_time_ago(); ?>
        </div>
		<?php
		$reviews_settings = get_ic_reviews_sep_settings();
		if ( current_user_can( 'administrator' ) || ! empty( $reviews_settings['reply_enabled'] ) ) {
			?>
            <div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth'     => $depth,
				                                                     'max_depth' => $args['max_depth'],
				                                                     'add_below' => 'review'
				) ) ) ?>
            </div>
			<?php
		}
		?>
    </div>
	<?php
}

add_filter( 'preprocess_comment', 'ic_save_ic_rev_type', - 1 );

/**
 * Saves product review comment type when submiting on front-end
 *
 * @param string $args
 *
 * @return string
 */
function ic_save_ic_rev_type( $args ) {
	if ( isset( $_POST['review_type'] ) && ic_string_contains( $_POST['review_type'], 'ic_rev' ) ) {
		$args['comment_type'] = sanitize_text_field( $_POST['review_type'] );
	}

	return $args;
}

add_action( 'comment_post', 'ic_save_ic_rev_rating' );

/**
 * Saves review rating value
 *
 * @param type $comment_ID
 */
function ic_save_ic_rev_rating( $comment_ID ) {
	if ( isset( $_POST['ic_review_rating'] ) ) {
		$rating = ic_sanitize_rating( $_POST['ic_review_rating'] );
		if ( ! empty( $rating ) ) {
			update_comment_meta( $comment_ID, 'ic_review_rating', $rating );
		}
	}
	if ( isset( $_POST['ic_review_title'] ) ) {
		$title = esc_attr( sanitize_text_field( $_POST['ic_review_title'] ) );
		if ( ! empty( $title ) ) {
			update_comment_meta( $comment_ID, 'ic_review_title', $title );
		}
	}
}

function ic_revs_default_fields( $fields ) {
	$commenter        = wp_get_current_commenter();
	$req              = get_option( 'require_name_email' );
	$aria_req         = ( $req ? " aria-required='true'" : '' );
	$fields['email']  = '<p class="comment-form-email"><label for="email">' . __( 'Email', 'reviews-plus' ) . ' ' .
	                    ( $req ? '<span class="required">*</span>' : '' ) . '</label> <input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) .
	                    '" size="30"' . $aria_req . ' /></p>';
	$fields['author'] = '<p class="comment-form-author"><label for="author">' . __( 'Name', 'reviews-plus' ) . ' ' .
	                    ( $req ? '<span class="required">*</span>' : '' ) . '</label> <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
	                    '" size="30"' . $aria_req . ' /></p>';
	unset( $fields['url'] );

	return $fields;
}
