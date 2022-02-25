<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages product reviews functions
 *
 * @version        1.0.0
 * @package        reviews-plus/functions
 * @author        Norbert Dreszer
 */
add_filter( 'ic_products_type_support', 'ic_enable_comments_support' );

/**
 * Adds support for comments to product post type
 *
 * @param array $support
 *
 * @return string
 */
function ic_enable_comments_support( $support ) {
	$support[] = 'comments';

	return $support;
}

add_action( 'init', 'ic_enable_reviews_post_type' );

/**
 * Enables comments on each reviews enabled post type
 *
 */
function ic_enable_reviews_post_type() {
	$post_types = get_ic_review_active_post_types();
	foreach ( $post_types as $post_type ) {
		add_post_type_support( $post_type, 'comments' );
	}
}

/**
 * Return product review value
 *
 * @param type $review_id
 *
 * @return type
 */
function ic_get_ic_rev_rating( $review_id ) {
	$rating          = empty( $review_id ) ? 0 : intval( get_comment_meta( $review_id, 'ic_review_rating', true ) );
	$filtered_rating = apply_filters( 'ic_get_ic_rev_rating', $rating, $review_id );

	return ic_sanitize_rating( $filtered_rating );
}

/**
 * Checks if the rating value is correct
 *
 * @param $rating
 *
 * @return int
 */
function ic_sanitize_rating( $rating ) {
	$sanitized_rating = intval( $rating );
	if ( $sanitized_rating > 5 || $sanitized_rating < 0 ) {
		return 0;
	}

	return $sanitized_rating;
}

/**
 * Returns review rating HTML
 *
 * @param int $review_id
 *
 * @return string
 */
function ic_ic_rev_rating( $review_id = 0, $editable = null, $label = null, $rating = null ) {
	$reviews_settings = get_ic_reviews_sep_settings();
	if ( ! empty( $reviews_settings['disable_rating'] ) ) {
		return;
	}
	if ( $rating === null ) {
		$rating = empty( $review_id ) ? 0 : ic_get_ic_rev_rating( $review_id );
	}
	if ( ! empty( $editable ) ) {
		$return = '<p class="review-rating allow-edit"><input type="hidden" name="ic_review_rating" value="' . $rating . '">';
	} else {
		$return = '<p class="review-rating" data-current_rating="' . $rating . '">';
	}
	if ( $label == 1 ) {
		$label = __( 'Your Rating', 'reviews-plus' );
	}
	if ( ! empty( $label ) ) {
		$return .= '<label class="rating-label" for="ic_review_rating">' . $label . '</label>';
	}
	for ( $i = 1; $i <= $rating; $i ++ ) {
		$return .= '<span class="rating-on rate-' . $i . '" data-rating="' . $i . '"></span>';
	}
	$rounded_rating = round( $rating );
	if ( $rounded_rating !== $rating ) {
		if ( abs( $rounded_rating - $rating ) > 0.2 ) {
			$return .= '<span class="rating-off rate-half" data-rating="' . $i . '"></span>';
		} else {
			$rating = floor( $rating );
		}
	}
	$off_rating = 5 - $rating;
	for ( $i = 1; $i <= $off_rating; $i ++ ) {
		$a      = $i + $rating;
		$return .= '<span class="rating-off rate-' . $a . '" data-rating="' . $a . '"></span>';
	}

	$return .= '</p>';

	return $return;
}

/**
 * Returns revuew title
 *
 * @param type $review_id
 *
 * @return type
 */
function ic_get_ic_rev_title( $review_id ) {
	$title = empty( $review_id ) ? '' : get_comment_meta( $review_id, 'ic_review_title', true );

	return $title;
}

/**
 * Returns review title edit HTML
 *
 * @param type $review_id
 * @param type $label
 *
 * @return string
 */
function ic_ic_rev_title( $review_id, $label = null ) {
	if ( $label == 1 ) {
		$label = __( 'Your Review Title', 'reviews-plus' );
	}
	$return = '<p class="comment-form-title">';
	if ( ! empty( $label ) ) {
		$return .= '<label for="review-title">' . $label . '</label> ';
	}
	$return .= '<input type="text" value="' . ic_get_ic_rev_title( $review_id ) . '" name="ic_review_title" id="review-title">';
	$return .= '</p>';

	return $return;
}

add_action( 'pre_get_comments', 'ic_remove_reviews_from_anywhere' );

/**
 * Manages product reviews visibility
 *
 * @param type $query
 *
 * @return type
 */
function ic_remove_reviews_from_anywhere( $query ) {
	$comment_type  = ic_get_comment_type();
	$comment_types = ic_get_active_comment_types();
	if ( ( $key = array_search( $comment_type, $comment_types ) ) !== false ) {
		unset( $comment_types[ $key ] );
	}
	if ( ! is_ic_post_type_review_enabled() && ! is_ic_revs_admin_screen() && ( ! function_exists( 'is_ic_catalog_admin_page' ) || ( function_exists( 'is_ic_catalog_admin_page' ) && ! is_ic_catalog_admin_page() ) ) ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['p'] ) ) {
			$post_id           = intval( $_REQUEST['p'] );
			$post_type         = get_post_type( $post_id );
			$review_post_types = get_ic_review_active_post_types();
			if ( ic_string_contains( $post_type, 'al_product' ) || in_array( $post_type, $review_post_types ) ) {
				return;
			}
		}
		$query->query_vars['type__not_in'] = $comment_types;
//$query->query_vars[ 'type__not_in' ] = array( 'ic_rev' );
	} else if ( is_ic_post_type_review_enabled() || ( function_exists( 'is_ic_product_page' ) && is_ic_product_page() ) ) {
//$query->query_vars[ 'type' ] = $comment_types;
		if ( ! ic_show_old_comments() ) {
			$query->query_vars['type'] = array( $comment_type );
		}
	}
}

function ic_reset_comment_counters() {
	global $comment_type, $post_id;
	$comment_type = ic_get_comment_type();
	wp_cache_delete( "comments-{$post_id}", 'counts' );
}

if ( ! function_exists( 'ic_time_ago' ) ) {

	function ic_time_ago( $type = 'comment' ) {
		$d = 'comment' == $type ? 'get_comment_time' : 'get_post_time';

		return human_time_diff( $d( 'U' ), current_time( 'timestamp' ) ) . " " . __( 'ago', 'reviews-plus' );
	}

}

function ic_get_comment_type( $object = null, $post_type = null ) {
	$comment_type = '';
	if ( empty( $object ) && is_admin() && isset( $_GET['comment_type'] ) ) {
		$comment_type = sanitize_text_field( $_GET['comment_type'] );
	} else {
		if ( empty( $post_type ) ) {
			if ( ! empty( $object ) ) {
				if ( is_int( $object ) ) {
					$post_type = get_post_type( $object );
				} else {
					$post_type = $object;
				}
			} else {
				$post_type = get_post_type();
			}
			if ( isset( $_GET['post_type'] ) && empty( $post_type ) && ! empty( $_GET['post_type'] ) ) {
				$post_type = sanitize_text_field( $_GET['post_type'] );
			}
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'ic_reviews_post' ) {
				$post_type = 'post';
			}
		}
		if ( ! empty( $post_type ) ) {
			if ( $post_type == 'al_product' ) {
				$comment_type = 'ic_rev';
			} else {
				$comment_type = 'ic_rev_' . $post_type;
			}
		}
	}

	return apply_filters( 'ic_reviews_comment_type', $comment_type );
}

/**
 * Returns active comment types array
 *
 * @return string
 */
function ic_get_active_comment_types() {
	$types = array( 'ic_rev' );
	if ( function_exists( 'get_ic_review_active_post_types' ) ) {
		$post_types = get_ic_review_active_post_types();
		foreach ( $post_types as $type ) {
			$types[] = 'ic_rev_' . $type;
		}
	}

	return $types;
}

/**
 * Returns review rating totals
 *
 * @param type $product_id
 *
 * @return int
 */
function ic_get_product_review_totals( $product_id ) {
	$comment_type = ic_get_comment_type( $product_id );
	$args         = array( 'type' => $comment_type, 'post_id' => $product_id, 'status' => 'approve' );
	$reviews      = get_comments( $args );
	$total        = array( 0, 0, 0, 0, 0, 0, 'total' => 0 );
	foreach ( $reviews as $review ) {
		$rating           = ic_get_ic_rev_rating( $review->comment_ID );
		$total[ $rating ] += 1;
		if ( ! empty( $rating ) ) {
			$total['total'] += 1;
		}
	}

	return $total;
}

add_action( 'ic_before_reviews', 'ic_show_reviews_totals' );

function ic_show_reviews_totals() {
	$product_id = get_the_ID();
	echo ic_get_review_totals_html( $product_id );
}

add_shortcode( 'average_rating', 'ic_average_review_shortcode' );

/**
 * Shows average star rating for entry
 *
 * @param type $atts
 *
 * @return type
 */
function ic_average_review_shortcode( $atts ) {
	$args       = shortcode_atts( array( 'id' => get_the_ID() ), $atts );
	$product_id = intval( $args['id'] );
	if ( ! empty( $product_id ) ) {
		return ic_get_reviews_average_html( $product_id );
	}
}

/**
 * Returns average star rating html
 *
 * @param type $product_id
 *
 * @return type
 */
function ic_get_reviews_average_html( $product_id = null ) {
	if ( empty( $product_id ) ) {
		$product_id = get_the_ID();
	}
	$average = ic_get_reviews_average( $product_id );

	return ic_ic_rev_rating( 0, false, null, $average );
}

/**
 * Returns average rating number
 *
 * @param type $product_id
 *
 * @return int
 */
function ic_get_reviews_average( $product_id ) {
	$totals        = ic_get_product_review_totals( $product_id );
	$total_summary = $totals['total'];
	if ( ! empty( $total_summary ) ) {
		unset( $totals['total'] );
		unset( $totals[0] );
		krsort( $totals );
		$total_achieved = 0;
		foreach ( $totals as $key => $total ) {
			$total_achieved += $key * $total;
		}
		$max         = $total_summary * 5;
		$total_score = number_format( ( $total_achieved / $max ) * 5, 1 );

		return $total_score;
	}

	return 0;
}

function ic_get_review_totals_html( $product_id ) {
	$totals        = ic_get_product_review_totals( $product_id );
	$total_summary = $totals['total'];
	if ( empty( $total_summary ) ) {
		return;
	}
	$html = '<div class="review-totals">';

	unset( $totals['total'] );
	unset( $totals[0] );
	krsort( $totals );
	$total_achieved = 0;
	$reviews_break  = '<div class="reviews-break">';
	foreach ( $totals as $key => $total ) {
		$stars_label    = ic_get_stars_label( $key );
		$reviews_break  .= '<div class="review-total-' . $key . ' review-total-row"><div class="stars-count">' . $stars_label . '</div> ' . ic_stars_qty_graph( $total, $total_summary ) . ' <div class="row-total">' . $total . '</div></div>';
		$total_achieved += $key * $total;
	}
	$reviews_break .= '</div>';
	$attr          = '';
	if ( ! function_exists( 'is_ic_product_page' ) || ( function_exists( 'is_ic_product_page' ) && ! is_ic_product_page() ) ) {
		$attr = ' itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"';
	}
	$html .= '<div class="reviews-summary"><span' . $attr . '>';
	if ( ! empty( $attr ) ) {
		$html .= '<meta itemprop="itemReviewed" content="' . get_the_title( $product_id ) . '">';
	}
	$max         = $total_summary * 5;
	$total_score = number_format( ( $total_achieved / $max ) * 5, 1 );
	$attr1       = '';
	$attr2       = '';
	$attr3       = '';
	if ( ! empty( $attr ) ) {
		$attr1 = ' itemprop="ratingValue"';
		$attr2 = ' itemprop="bestRating"';
		$attr3 = ' itemprop="ratingCount"';
	}
	$span1 = '<span' . $attr1 . '>' . $total_score . '</span>';
	$span2 = '<span' . $attr2 . '>5</span>';
	$span3 = '<span' . $attr3 . '>' . $total_summary . '</span>';
	$html  .= sprintf( __( 'Average Rating: <strong>%s out of %s</strong> (%s votes)', 'reviews-plus' ), $span1, $span2, $span3 );
	$html  .= '</span></div>';
	$html  .= $reviews_break;
	$html  .= '</div>';

	return $html;
}

add_action( 'ic_structured_data', 'ic_reviews_structured_data' );

function ic_reviews_structured_data( $product_id ) {
	$totals        = ic_get_product_review_totals( $product_id );
	$total_summary = $totals['total'];
	if ( empty( $total_summary ) ) {
		return;
	}
	unset( $totals['total'] );
	unset( $totals[0] );
	$max            = $total_summary * 5;
	$total_achieved = 0;
	foreach ( $totals as $key => $total ) {
		if ( is_numeric( $key ) ) {
			$total_achieved += $key * $total;
		}
	}
	$total_score = number_format( ( $total_achieved / $max ) * 5, 1 );
	echo '"aggregateRating":{
		"@type":"AggregateRating",
		"ratingValue":"' . $total_score . '",
		"ratingCount":"' . $total_summary . '",
		"bestRating":"5"
		},';
}

/**
 * Defines stars labels
 *
 * @param type $key
 *
 * @return type
 */
function ic_get_stars_label( $key ) {
	switch ( $key ) {
		case 1:
			$label = __( '1 star', 'reviews-plus' );
			break;
		case 2:
			$label = __( '2 stars', 'reviews-plus' );
			break;
		case 3:
			$label = __( '3 stars', 'reviews-plus' );
			break;
		case 4:
			$label = __( '4 stars', 'reviews-plus' );
			break;
		case 5:
			$label = __( '5 stars', 'reviews-plus' );
			break;
	}

	return $label;
}

function ic_stars_qty_graph( $el_total, $total ) {
	$html       = '';
	$percentage = 0;
	if ( ! empty( $total ) ) {
		$percentage = number_format( ( $el_total / $total ) * 100 );
	}
	$html = '<div class="graph-container"><span class="grey-graph"><span class="orange-graph" style="width:' . $percentage . '%"></span></span></div>';

	return $html;
}

function ic_ic_rev_rating_stars( $how_many ) {
	$return = '<p class="review-rating" data-current_rating="' . $how_many . '">';
	for ( $i = 1; $i <= $how_many; $i ++ ) {
		$return .= '<span class="rating-on rate-' . $i . '" data-rating="' . $i . '"></span>';
	}
	$return .= '</p>';

	return $return;
}

add_filter( 'comment_post_redirect', 'ic_revs_comment_redirect' );

function ic_revs_comment_redirect( $location ) {
	$location = str_replace( 'comment', 'review', $location );
	if ( ! empty( $_POST['ic_revs_redirect'] ) ) {
		$new      = explode( '#', $location );
		$new[0]   = esc_url( $_POST['ic_revs_redirect'] );
		$location = $new[0] . '#' . $new[1];
	}

	return $location;
}

add_action( 'admin_init', 'ic_revs_replace_comments_labels' );

function ic_revs_replace_comments_labels() {
	if ( ic_ic_review_post_type_screen() ) {
		add_filter( 'gettext', 'ic_force_ic_revs_labels', 10, 3 );
	}
}

//add_filter( 'notify_moderator', 'ic_disable_default_moderation_message', 10, 2 );
//add_filter( 'notify_post_author', 'ic_disable_default_moderation_message', 10, 2 );

/**
 * Disables default comment notification for reviews
 *
 * @param type $notify
 * @param type $comment_id
 *
 * @return boolean
 */
function ic_disable_default_moderation_message( $notify, $comment_id ) {
	if ( $notify ) {
		if ( is_ic_review_comment( $comment_id ) ) {
			return false;
		}
	}

	return $notify;
}

add_filter( 'comment_notification_recipients', 'ic_review_notification_recipients', 10, 2 );
add_filter( 'comment_moderation_recipients', 'ic_review_notification_recipients', 10, 2 );

/**
 * Defines review notification recipients
 *
 * @param type $emails
 * @param type $comment_id
 *
 * @return type
 */
function ic_review_notification_recipients( $emails, $comment_id ) {
	if ( is_ic_review_comment( $comment_id ) ) {

	}

	return $emails;
}

add_filter( 'comment_notification_text', 'ic_review_notification_text', 10, 2 );

/**
 * Defines comment notification text
 *
 * @param type $text
 * @param type $comment_id
 *
 * @return type
 */
function ic_review_notification_text( $notify_message, $comment_id ) {
	if ( is_ic_review_comment( $comment_id ) ) {
		$comment        = get_comment( $comment_id );
		$product_name   = ic_get_review_product_name( $comment_id );
		$notify_message = sprintf( __( 'New review on %s' ), $product_name ) . "\r\n";
		/* translators: 1: comment author, 2: author IP, 3: author domain */
		$comment_author_domain = @gethostbyaddr( $comment->comment_author_IP );
		$notify_message        .= sprintf( __( 'Author: %1$s (IP: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message        .= sprintf( __( 'Email: %s' ), $comment->comment_author_email ) . "\r\n";
//$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
		$review_content = wp_specialchars_decode( $comment->comment_content );
		$notify_message .= sprintf( __( 'Review: %s' ), "\r\n" . $review_content ) . "\r\n\r\n";
		$post_type      = get_post_type( $comment->comment_post_ID );
		$post_type_o    = get_post_type_object( $post_type );
		$notify_message .= sprintf( __( 'You can see all reviews on this %s here:' ), $post_type_o->labels->singular_name ) . "\r\n";
		$notify_message .= get_permalink( $comment->comment_post_ID ) . "#reviews\r\n\r\n";
		$notify_message .= sprintf( __( 'Permalink: %s' ), get_comment_link( $comment ) ) . "\r\n";
		/*
		  $post					 = get_post( $comment->comment_post_ID );
		  if ( user_can( $post->post_author, 'edit_comment', $comment->comment_ID ) ) {
		  if ( EMPTY_TRASH_DAYS ) {
		  $notify_message .= sprintf( __( 'Trash it: %s' ), admin_url( "comment.php?action=trash&c={$comment->comment_ID}&trash_review={$comment_id}#wpbody-content" ) ) . "\r\n";
		  } else {
		  $notify_message .= sprintf( __( 'Delete it: %s' ), admin_url( "comment.php?action=delete&c={$comment->comment_ID}&delete_review={$comment_id}#wpbody-content" ) ) . "\r\n";
		  }
		  $notify_message .= sprintf( __( 'Spam it: %s' ), admin_url( "comment.php?action=spam&c={$comment->comment_ID}&spam_review={$comment_id}#wpbody-content" ) ) . "\r\n";
		  }
		 *
		 */
	}

	return $notify_message;
}

add_filter( 'comment_notification_subject', 'ic_review_notification_subject', 10, 2 );

/**
 * Defines comment notification subject
 *
 * @param type $subject
 * @param type $comment_id
 *
 * @return type
 */
function ic_review_notification_subject( $subject, $comment_id ) {
	if ( is_ic_review_comment( $comment_id ) ) {
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$subject  = sprintf( __( '[%1$s] Review: "%2$s"' ), $blogname, ic_get_review_product_name( $comment_id ) );
	}

	return $subject;
}

add_filter( 'comment_moderation_text', 'ic_review_moderation_text', 10, 2 );

/**
 * Defines comment moderation text
 *
 * @param type $text
 * @param type $comment_id
 *
 * @return type
 */
function ic_review_moderation_text( $notify_message, $comment_id ) {
	if ( is_ic_review_comment( $comment_id ) ) {
		$comment               = get_comment( $comment_id );
		$product_name          = ic_get_review_product_name( $comment_id );
		$notify_message        = sprintf( __( 'A new review on the %s is waiting for your approval' ), $product_name ) . "\r\n";
		$notify_message        .= get_permalink( $comment->comment_post_ID ) . "\r\n\r\n";
		$comment_author_domain = @gethostbyaddr( $comment->comment_author_IP );
		$notify_message        .= sprintf( __( 'Author: %1$s (IP: %2$s, %3$s)' ), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
		$notify_message        .= sprintf( __( 'Email: %s' ), $comment->comment_author_email ) . "\r\n";
//$notify_message .= sprintf( __( 'URL: %s' ), $comment->comment_author_url ) . "\r\n";
		$review_content = wp_specialchars_decode( $comment->comment_content );
		$notify_message .= sprintf( __( 'Review: %s' ), "\r\n" . $review_content ) . "\r\n\r\n";
		/*
		 * NEEDS IMPROVEMENTS ON THE FUNCTIONALITY AFTER CLICKING ON IT
		  $notify_message .= sprintf( __( 'Approve it: %s' ), admin_url( "comment.php?action=approve&c={$comment_id}&approve_review={$comment_id}#wpbody-content" ) ) . "\r\n";
		  if ( EMPTY_TRASH_DAYS ) {
		  $notify_message .= sprintf( __( 'Trash it: %s' ), admin_url( "comment.php?action=trash&c={$comment_id}&trash_review={$comment_id}#wpbody-content" ) ) . "\r\n";
		  } else {
		  $notify_message .= sprintf( __( 'Delete it: %s' ), admin_url( "comment.php?action=delete&c={$comment_id}&delete_review={$comment_id}#wpbody-content" ) ) . "\r\n";
		  }
		  $notify_message .= sprintf( __( 'Spam it: %s' ), admin_url( "comment.php?action=spam&c={$comment_id}&spam_review={$comment_id}#wpbody-content" ) ) . "\r\n";
		 *
		 */
		global $wpdb;
		$comment_type    = get_comment_type( $comment_id );
		$post_type       = get_post_type( $comment->comment_post_ID );
		$reviews_waiting = $wpdb->get_var( "SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0' AND comment_type = '$comment_type'" );
		$notify_message  .= sprintf( _n( 'Currently %s review is waiting for approval. Please visit the moderation panel:', 'Currently %s reviews are waiting for approval. Please visit the moderation panel:', $reviews_waiting ), number_format_i18n( $reviews_waiting ) ) . "\r\n";
		$notify_message  .= admin_url( "edit.php?post_type=" . $post_type . "&page=ic_reviews_" . $post_type . "&comment_type=" . $comment_type . "&comment_status=moderated#wpbody-content" ) . "\r\n";
	}

	return $notify_message;
}

add_filter( 'comment_moderation_subject', 'ic_review_moderation_subject', 10, 2 );

/**
 * Defines comment moderation subject
 *
 * @param type $subject
 * @param type $comment_id
 *
 * @return type
 */
function ic_review_moderation_subject( $subject, $comment_id ) {
	if ( is_ic_review_comment( $comment_id ) ) {
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$subject  = sprintf( __( '[%1$s] Please moderate review of %2$s' ), $blogname, ic_get_review_product_name( $comment_id ) );
	}

	return $subject;
}

/**
 * Returns review product name
 *
 * @param type $review_id
 *
 * @return type
 */
function ic_get_review_product_name( $review_id ) {
	$comment = get_comment( $review_id );
	$post    = get_post( $comment->comment_post_ID );

	return $post->post_title;
}

add_filter( 'comments_open', 'ic_revs_force_open', 10, 2 );

function ic_revs_force_open( $open, $post_id ) {
	$post_type  = get_post_type( $post_id );
	$show_where = get_ic_review_show_where_post_types();
	if ( ! empty( $show_where[ $post_type ] ) && $show_where[ $post_type ] === 'all' ) {
		return true;
	}

	return $open;
}

add_action( 'single_names_table', 'ic_reviews_labels_settings' );

function ic_reviews_labels_settings( $single_names ) {
	?>
    <tr>
        <td><?php _e( 'Reviews Header', 'reviews-plus' ); ?></td>
        <td><input type="text" name="single_names[reviews]"
                   value="<?php echo esc_html( $single_names['reviews'] ); ?>"/></td>
    </tr>
    <tr>
        <td><?php _e( 'Rating Title', 'reviews-plus' ); ?></td>
        <td><input type="text" name="single_names[rating_tite]"
                   value="<?php echo esc_html( $single_names['rating_tite'] ); ?>"/></td>
    </tr>
    <tr>
        <td><?php _e( 'Review title label', 'reviews-plus' ); ?></td>
        <td><input type="text" name="single_names[review_title]"
                   value="<?php echo esc_html( $single_names['review_title'] ); ?>"/></td>
    </tr>
    <tr>
        <td><?php _e( 'Review text label', 'reviews-plus' ); ?></td>
        <td><input type="text" name="single_names[review_text]"
                   value="<?php echo esc_html( $single_names['review_text'] ); ?>"/></td>
    </tr>
    <tr>
        <td><?php _e( 'Closed Reviews', 'reviews-plus' ); ?></td>
        <td><input type="text" name="single_names[reviews_closed]"
                   value="<?php echo esc_html( $single_names['reviews_closed'] ); ?>"/></td>
    </tr>
    <tr>
        <td><?php _e( 'Review Reply Title', 'reviews-plus' ); ?></td>
        <td><input type="text" name="single_names[review_reply_title]"
                   value="<?php echo esc_html( $single_names['review_reply_title'] ); ?>"/></td>
    </tr>
    <tr>
        <td><?php _e( 'Review Submit Label', 'reviews-plus' ); ?></td>
        <td><input type="text" name="single_names[review_submit_label]"
                   value="<?php echo esc_html( $single_names['review_submit_label'] ); ?>"/></td>
    </tr>
	<?php
}

function ic_get_reviews_labels() {
	if ( function_exists( 'get_single_names' ) ) {
		$labels = get_single_names();
	} else {
		$labels = ic_reviews_standard_labels();
	}

	return $labels;
}

add_filter( 'ic_default_single_names', 'ic_reviews_standard_labels' );

function ic_reviews_standard_labels( $single_names = array() ) {
	$single_names['reviews']             = __( 'Reviews', 'reviews-plus' );
	$single_names['rating_tite']         = __( 'Your Rating', 'reviews-plus' );
	$single_names['review_title']        = __( 'Your Review Title', 'reviews-plus' );
	$single_names['review_text']         = __( 'Your Review', 'reviews-plus' );
	$single_names['reviews_closed']      = __( 'Reviews are closed.', 'reviews-plus' );
	$single_names['review_reply_title']  = sprintf( __( 'Review %s.', 'reviews-plus' ), '[product_name]' );
	$single_names['review_submit_label'] = __( 'Submit Review', 'reviews-plus' );

	return $single_names;
}

add_action( 'init', 'ic_reviews_add_shortcodes' );

function ic_reviews_add_shortcodes() {
	if ( ! shortcode_exists( 'product_name' ) ) {
		add_shortcode( 'product_name', 'get_the_title' );
	}
}
