<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Includes folder
 *
 * @version        1.0.0
 * @package        reviews-plus/includes
 * @author        Norbert Dreszer
 */
//add_action( 'pre_get_comments', 'ic_filter_ic_revs' );

/**
 * Exclude reviews from comment list and include in review list
 * @param object $instance
 */
function ic_filter_ic_revs( $instance ) {
	if ( is_admin() && !isset( $_GET[ 'page' ] ) ) {
		$instance->query_vars[ "meta_query" ] = array( array( 'key' => 'ic_review_rating', 'compare' => 'NOT EXISTS' ) );
	} else if ( is_admin() && (isset( $_GET[ 'page' ] ) && strpos( $_GET[ 'page' ], 'ic_reviews_' ) !== false) ) {
		$instance->query_vars[ "meta_query" ] = array( array( 'key' => 'ic_review_rating', 'compare' => 'EXISTS' ) );
	}
}

add_filter( 'comment_class', 'ic_add_reviews_reply_class', 10, 3 );

function ic_add_reviews_reply_class( $classes, $class, $comment_id ) {
	$review = get_comment( $comment_id );
	if ( $review->comment_parent && is_ic_review_comment( $comment_id ) ) {
		$classes[] = 'review-reply';
	}
	return $classes;
}

add_action( 'admin_init', 'ic_redirect_ic_revs_admin' );

function ic_redirect_ic_revs_admin() {
	global $pagenow;
	if ( $pagenow == 'edit-comments.php' && isset( $_GET[ 'p' ] ) && isset( $_GET[ 'comment_status' ] ) ) {
		$post_id = intval( $_GET[ 'p' ] );
		if ( !empty( $post_id ) ) {
			$post_type = get_post_type( $post_id );
			if ( ic_string_contains( $post_type, 'al_product' ) ) {
				$status = strval( $_GET[ 'comment_status' ] );
				if ( $post_type != 'al_product' ) {
					if ( $post_type == 'post' ) {
						wp_redirect( admin_url( 'edit.php?page=ic_reviews_' . $post_type . '&p=' . $post_id . '&comment_status=' . $status . '&comment_type=ic_rev_' . $post_type ) );
					} else {
						wp_redirect( admin_url( 'edit.php?post_type=' . $post_type . '&page=ic_reviews_' . $post_type . '&p=' . $post_id . '&comment_status=' . $status . '&comment_type=ic_rev_' . $post_type ) );
					}
				} else {
					wp_redirect( admin_url( 'edit.php?post_type=' . $post_type . '&page=ic_reviews_' . $post_type . '&p=' . $post_id . '&comment_status=' . $status . '&comment_type=ic_rev' ) );
				}
				exit;
			}
		}
	} else if ( $pagenow == 'comment.php' && isset( $_GET[ 'c' ] ) && isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'editcomment' && !isset( $_GET[ 'edit_review' ] ) ) {
		$comment_id = intval( $_GET[ 'c' ] );
		if ( !empty( $comment_id ) ) {
			if ( is_ic_review_comment( $comment_id ) ) {
				$url = add_query_arg( 'edit_review', $comment_id );
				wp_safe_redirect( $url );
				exit;
			}
		}
	}
}

//add_filter( 'wp_redirect', 'ic_reviews_actions_redirect' );
/**
 * Modify redirect after review moderation from email
 *
 * @param type $redirect
 * @return type
 */
function ic_reviews_actions_redirect( $redirect ) {
	if ( isset( $_GET[ 'c' ] ) && isset( $_GET[ 'action' ] ) && isset( $_GET[ '_wp_http_referer' ] ) && isset( $_GET[ 'noredir' ] ) ) {
		$comment_id = intval( $_GET[ 'c' ] );
		if ( !empty( $comment_id ) ) {
			if ( ic_string_contains( $_GET[ '_wp_http_referer' ], 'review' ) && ic_string_contains( $redirect, 'edit-comments.php' ) && is_ic_review_comment( $comment_id ) ) {
				$redirect		 = str_replace( 'edit-comments.php', 'edit.php', $redirect );
				$comment		 = get_comment( $comment_id );
				$post_type		 = get_post_type( $comment->comment_post_ID );
				$comment_type	 = get_comment_type( $comment_id );
				$redirect		 = add_query_arg( array( 'post_type' => $post_type, 'page' => 'ic_reviews_' . $post_type, 'comment_type' => $comment_type ), $redirect );
			}
		}
	}
	return $redirect;
}

//add_action( 'product_settings_menu', 'register_ic_revs_menu', 9 );
add_action( 'admin_menu', 'register_ic_revs_menu', 15 );

function register_ic_revs_menu() {
	$post_types = get_ic_review_active_post_types();
	foreach ( $post_types as $type ) {
		$page = 'edit.php';
		if ( $type != 'post' ) {
			$page .= '?post_type=' . $type;
		}
		$hook = add_submenu_page( $page, __( 'Reviews', 'reviews-plus' ), __( 'Reviews', 'reviews-plus' ), 'moderate_comments', 'ic_reviews_' . $type, 'ic_admin_reviews_list' );
		add_action( "load-$hook", 'ic_ic_revs_options' );
	}
	$hook = add_submenu_page( 'edit.php?post_type=al_product', __( 'Reviews', 'reviews-plus' ), __( 'Reviews', 'reviews-plus' ), 'edit_products', 'ic_reviews_al_product', 'ic_admin_reviews_list' );
	add_action( "load-$hook", 'ic_ic_revs_options' );
}

function ic_ic_revs_options() {
	add_screen_option( 'per_page' );
	get_current_screen()->add_help_tab( array(
		'id'		 => 'overview',
		'title'		 => __( 'Overview', 'reviews-plus' ),
		'content'	 =>
		'<p>' . __( 'You can manage reviews made on your site similar to the way you manage posts and other content. This screen is customizable in the same ways as other management screens, and you can act on reviews using the on-hover action links or the Bulk Actions.', 'reviews-plus' ) . '</p>' .
		'<p><strong>' . __( 'The reviews administration is based on WordPress native comments system administration.', 'reviews-plus' ) . '</strong></p>'
	) );
	get_current_screen()->add_help_tab( array(
		'id'		 => 'moderating-comments',
		'title'		 => __( 'Moderating Reviews', 'reviews-plus' ),
		'content'	 =>
		'<p>' . __( 'A red bar on the left means the review is waiting for you to moderate it.', 'reviews-plus' ) . '</p>' .
		'<p>' . __( 'In the <strong>Author</strong> column, in addition to the author&#8217;s name, email address, and blog URL, the reviewer&#8217;s IP address is shown. Clicking on this link will show you all the reviews made from this IP address.', 'reviews-plus' ) . '</p>' .
		'<p>' . __( 'In the <strong>Rating</strong> column, shows the star rating left for particular review.', 'reviews-plus' ) . '</p>' .
		'<p>' . __( 'In the <strong>Review</strong> column, above each review it says &#8220;Submitted on,&#8221; followed by the date and time the review was left on your site. Clicking on the date/time link will take you to that review on your live site. Hovering over any review gives you options to approve, reply (and approve), quick edit, edit, spam mark, or trash that review.', 'reviews-plus' ) . '</p>' .
		'<p>' . __( 'In the <strong>Review Item</strong> column, there are three elements. The text is the name of the item that inspired the review, and links to the item editor for that entry. The View Item link leads to that item on your live site. The small bubble with the number in it shows the number of approved reviws that post has received. If there are pending reviews, a red notification circle with the number of pending reviews is displayed. Clicking the notification circle will filter the reviews screen to show only pending reviews on that item.', 'reviews-plus' ) . '</p>' .
		'<p>' . __( 'Many people take advantage of keyboard shortcuts to moderate their reviews more quickly. Use the link to the side to learn more.' ) . '</p>'
	) );

	get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information see the WordPress comment system documentation:', 'reviews-plus' ) . '</strong></p>' .
	'<p>' . sprintf( __( '%sDocumentation on Comments%s', 'reviews-plus' ), '<a href="https://codex.wordpress.org/Administration_Screens#Comments" target="_blank">', '</a>' ) . '</p>' .
	'<p>' . sprintf( __( '%sDocumentation on Comment Spam%s', 'reviews-plus' ), '<a href="https://codex.wordpress.org/Comment_Spam" target="_blank">', '</a>' ) . '</p>' .
	'<p>' . sprintf( __( '%sDocumentation on Keyboard Shortcuts%s', 'reviews-plus' ), '<a href="https://codex.wordpress.org/Keyboard_Shortcuts" target="_blank">', '</a>' ) . '</p>' .
	'<p>' . sprintf( __( '%sSupport Forums%s', 'reviews-plus' ), '<a href="https://wordpress.org/support/" target="_blank">', '</a>' ) . '</p>'
	);
}

add_action( 'admin_init', 'ic_ic_revs_admin_init', 98 );

function ic_ic_revs_admin_init() {
	if ( is_ic_revs_admin_screen() ) {
		if ( isset( $_GET[ 'edit_review' ] ) ) {
			$review_id = intval( $_GET[ 'edit_review' ] );
			ic_edit_review_screen( $review_id );
		} else {
			global $ic_ic_revs_table;
			add_filter( 'comment_row_actions', 'ic_reviews_row_actions', 10, 2 );
			$ic_ic_revs_table = new IC_Reviews_List_Table( );
			add_action( 'admin_init', 'ic_reviews_admin_init', 99 );
		}
	}
}

add_action( 'admin_init', 'ic_add_reviews_submenu_param' );

/**
 * Adds comment_type parameter to the url
 */
function ic_add_reviews_submenu_param() {
	if ( is_ic_revs_admin_screen() && !isset( $_GET[ 'comment_type' ] ) ) {
		$comment_type	 = ic_get_comment_type();
		$url			 = esc_url_raw( add_query_arg( 'comment_type', $comment_type ) );
		wp_redirect( $url );
		exit;
	}
}

function ic_reviews_row_actions( $actions, $comment ) {
	if ( ic_string_contains( $comment->comment_type, 'ic_rev' ) ) {
		$actions[ 'edit' ] = '<a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$comment->comment_ID&edit_review=$comment->comment_ID" ) ) . '" aria-label="' . __( 'Edit this review' ) . '">' . __( 'Edit' ) . '</a>';
	}
	return $actions;
}

add_filter( 'gettext', 'ic_reviews_edit_title', 10, 3 );

/**
 * Changes comment to review on review edit screen
 *
 * @param type $translated
 * @param type $text
 * @param type $textdomain
 * @return type
 */
function ic_reviews_edit_title( $translated, $text, $textdomain ) {
	if ( is_ic_review_mod_screen() ) {
		if ( strpos( $text, 'Edit Comment' ) !== false ) {
			$translated = str_replace( 'Edit Comment', __( 'Edit Review', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'Moderate Comment' ) !== false ) {
			$translated = str_replace( 'Moderate Comment', __( 'Moderate Review', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'You are about to approve the following comment:' ) !== false ) {
			$translated = str_replace( 'You are about to approve the following comment:', __( 'You are about to approve the following review:', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'Approve Comment' ) !== false ) {
			$translated = str_replace( 'Approve Comment', __( 'Approve Review', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'In Response To' ) !== false ) {
			$translated = str_replace( 'In Response To', __( 'Reviewed', 'reviews-plus' ), $text );
		}
	}
	return $translated;
}

//add_action( 'in_admin_header', 'ic_reviews_highlight_edit_menu' );
add_filter( 'parent_file', 'ic_reviews_highlight_edit_menu' );

function ic_reviews_highlight_edit_menu( $parent_file = null ) {
	if ( is_ic_review_mod_screen() ) {
		global $parent_file, $submenu_file;
		$comment_id		 = intval( $_GET[ 'c' ] );
		$comment		 = get_comment( $comment_id );
		$post_type		 = get_post_type( $comment->comment_post_ID );
		$parent_file	 = 'edit.php?post_type=' . $post_type;
		$submenu_file	 = 'ic_reviews_' . $post_type;
	}
	return $parent_file;
}

function ic_edit_review_screen( $review_id ) {
	if ( !$comment = get_comment( $review_id ) ) {
		comment_footer_die( __( 'Invalid review ID.' ) . sprintf( ' <a href="%s">' . __( 'Go back' ) . '</a>.', 'javascript:history.go(-1)' ) );
	}

	if ( !current_user_can( 'edit_comment', $review_id ) ) {
		comment_footer_die( __( 'Sorry, you are not allowed to edit this review.' ) );
	}

	if ( 'trash' == $comment->comment_approved ) {
		comment_footer_die( __( 'This review is in the Trash. Please move it out of the Trash if you want to edit it.' ) );
	}

	$comment = get_comment_to_edit( $review_id );
	include( ABSPATH . 'wp-admin/edit-form-comment.php' );
}

add_action( 'current_screen', 'ic_inititalize_reviews_plus_columns' );

function ic_inititalize_reviews_plus_columns() {
	if ( function_exists( 'get_current_screen' ) && is_ic_revs_admin_screen() ) {
		$screen = get_current_screen();
		if ( isset( $screen->id ) ) {
			add_filter( 'manage_' . $screen->id . '_columns', 'ic_ic_revs_columns', 99 );
		}
	}
}

function ic_ic_revs_columns( $columns ) {
	$new_columns = array(
		'cb'		 => isset( $columns[ 'cb' ] ) ? $columns[ 'cb' ] : '',
		'author'	 => isset( $columns[ 'author' ] ) ? $columns[ 'author' ] : '',
		'rating'	 => __( 'Rating', 'reviews-plus' ),
		'comment'	 => isset( $columns[ 'comment' ] ) ? $columns[ 'comment' ] : '',
		'response'	 => isset( $columns[ 'response' ] ) ? $columns[ 'response' ] : '',
	);
	$columns	 = $new_columns;

	return $columns;
}

add_filter( 'manage_edit-comments_columns', 'ic_ic_revs_ajax_columns' );

function ic_ic_revs_ajax_columns( $columns ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		if ( isset( $_POST[ 'comment_ID' ] ) ) {
			$comment_id = intval( $_POST[ 'comment_ID' ] );
			if ( !empty( $comment_id ) ) {
				$comment_type = get_comment_type( $comment_id );
				if ( ic_string_contains( $comment_type, 'ic_rev' ) ) {
					$new_columns = array(
						'cb'		 => $columns[ 'cb' ],
						'author'	 => $columns[ 'author' ],
						'rating'	 => __( 'Rating', 'reviews-plus' ),
						'comment'	 => $columns[ 'comment' ],
						'response'	 => $columns[ 'response' ]
					);
					$columns	 = $new_columns;
				}
			}
		}
	}
	return $columns;
}

add_filter( 'manage_comments_custom_column', 'ic_ic_revs_column', 10, 2 );

function ic_ic_revs_column( $column, $review_id ) {
	if ( is_ic_revs_admin_screen() || (defined( 'DOING_AJAX' ) && DOING_AJAX) ) {
		if ( 'rating' == $column ) {
			echo '<div class="review-title">' . ic_get_ic_rev_title( $review_id ) . '</div>';
			echo ic_ic_rev_rating( $review_id );
		}
	}
}

/**
 * Change comments to reviews text on reviews admin page
 *
 * @param type $translated
 * @param type $text
 * @param type $textdomain
 * @return type
 */
function ic_ic_revs_labels( $translated, $text, $textdomain ) {
	if ( is_admin() && $textdomain == 'default' && is_ic_revs_admin_screen() ) {
		if ( strpos( $text, 'comments' ) !== false ) {
			$translated = str_replace( 'comments', __( 'reviews', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'Comments' ) !== false ) {
			$translated = str_replace( 'Comments', __( 'Reviews', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'comment' ) !== false ) {
			$translated = str_replace( 'comment', __( 'review', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'Comment' ) !== false ) {
			$translated = str_replace( 'Comment', __( 'Review', 'reviews-plus' ), $text );
		}
	}
	return $translated;
}

/**
 * Change comments to reviews text on reviews admin page
 *
 * @param type $translated
 * @param type $text
 * @param type $textdomain
 * @return type
 */
function ic_force_ic_revs_labels( $translated, $text, $textdomain ) {
	if ( is_admin() && $textdomain == 'default' ) {
		if ( strpos( $text, 'comments' ) !== false ) {
			$translated = str_replace( 'comments', __( 'reviews', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'Comments' ) !== false ) {
			$translated = str_replace( 'Comments', __( 'Reviews', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'comment' ) !== false ) {
			$translated = str_replace( 'comment', __( 'review', 'reviews-plus' ), $text );
		} else if ( strpos( $text, 'Comment' ) !== false ) {
			$translated = str_replace( 'Comment', __( 'Review', 'reviews-plus' ), $text );
		}
	}
	return $translated;
}

function ic_reviews_admin_init() {
	global $ic_ic_revs_table, $wpdb;
	$doaction = $ic_ic_revs_table->current_action();
	if ( $doaction ) {
		check_admin_referer( 'bulk-reviews' );

		if ( 'delete_all' == $doaction && !empty( $_REQUEST[ 'pagegen_timestamp' ] ) ) {
			$comment_status	 = wp_unslash( $_REQUEST[ 'comment_status' ] );
			$delete_time	 = wp_unslash( $_REQUEST[ 'pagegen_timestamp' ] );
			$comment_ids	 = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = %s AND %s > comment_date_gmt", $comment_status, $delete_time ) );
			$doaction		 = 'delete';
		} elseif ( isset( $_REQUEST[ 'delete_comments' ] ) ) {
			$comment_ids = $_REQUEST[ 'delete_comments' ];
			$doaction	 = ( $_REQUEST[ 'action' ] != -1 ) ? $_REQUEST[ 'action' ] : $_REQUEST[ 'action2' ];
		} elseif ( isset( $_REQUEST[ 'ids' ] ) ) {
			$comment_ids = array_map( 'absint', explode( ',', $_REQUEST[ 'ids' ] ) );
		} elseif ( wp_get_referer() ) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		$approved	 = $unapproved	 = $spammed	 = $unspammed	 = $trashed	 = $untrashed	 = $deleted	 = 0;

		$redirect_to = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'spammed', 'unspammed', 'approved', 'unapproved', 'ids' ), wp_get_referer() );
		$redirect_to = add_query_arg( 'paged', $pagenum, $redirect_to );

		foreach ( $comment_ids as $comment_id ) { // Check the permissions on each
			if ( !current_user_can( 'edit_comment', $comment_id ) )
				continue;

			switch ( $doaction ) {
				case 'approve' :
					wp_set_comment_status( $comment_id, 'approve' );
					$approved++;
					break;
				case 'unapprove' :
					wp_set_comment_status( $comment_id, 'hold' );
					$unapproved++;
					break;
				case 'spam' :
					wp_spam_comment( $comment_id );
					$spammed++;
					break;
				case 'unspam' :
					wp_unspam_comment( $comment_id );
					$unspammed++;
					break;
				case 'trash' :
					wp_trash_comment( $comment_id );
					$trashed++;
					break;
				case 'untrash' :
					wp_untrash_comment( $comment_id );
					$untrashed++;
					break;
				case 'delete' :
					wp_delete_comment( $comment_id );
					$deleted++;
					break;
			}
		}

		if ( $approved )
			$redirect_to = add_query_arg( 'approved', $approved, $redirect_to );
		if ( $unapproved )
			$redirect_to = add_query_arg( 'unapproved', $unapproved, $redirect_to );
		if ( $spammed )
			$redirect_to = add_query_arg( 'spammed', $spammed, $redirect_to );
		if ( $unspammed )
			$redirect_to = add_query_arg( 'unspammed', $unspammed, $redirect_to );
		if ( $trashed )
			$redirect_to = add_query_arg( 'trashed', $trashed, $redirect_to );
		if ( $untrashed )
			$redirect_to = add_query_arg( 'untrashed', $untrashed, $redirect_to );
		if ( $deleted )
			$redirect_to = add_query_arg( 'deleted', $deleted, $redirect_to );
		if ( $trashed || $spammed )
			$redirect_to = add_query_arg( 'ids', join( ',', $comment_ids ), $redirect_to );

		wp_safe_redirect( $redirect_to );
		exit;
	} elseif ( !empty( $_GET[ '_wp_http_referer' ] ) ) {
		wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER[ 'REQUEST_URI' ] ) ) );
		exit;
	}
}

function ic_admin_reviews_list() {
	global $post_id, $comment_status, $search, $comment_type, $ic_ic_revs_table;

	$comment_type = ic_get_comment_type();
	wp_cache_delete( "comments-{$post_id}", 'counts' );
	add_filter( 'gettext', 'ic_ic_revs_labels', 10, 3 );


//require_once( ABSPATH . 'wp-admin/edit-comments.php' );




	$pagenum = $ic_ic_revs_table->get_pagenum();


	$ic_ic_revs_table->prepare_items();


	if ( $post_id )
		$title	 = sprintf( __( 'Reviews on &#8220;%s&#8221;' ), wp_html_excerpt( _draft_or_post_title( $post_id ), 50, '&hellip;' ) );
	else
		$title	 = __( 'Reviews' );
	?>

	<div class="wrap">
		<h1><?php
			if ( $post_id )
				echo sprintf( __( 'Reviews on &#8220;%s&#8221;' ), sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post_id ), wp_html_excerpt( _draft_or_post_title( $post_id ), 50, '&hellip;' )
				)
				);
			else
				_e( 'Reviews' );

			if ( isset( $_REQUEST[ 's' ] ) && $_REQUEST[ 's' ] )
				echo '<span class="subtitle">' . sprintf( __( 'Search results for &#8220;%s&#8221;' ), wp_html_excerpt( esc_html( wp_unslash( $_REQUEST[ 's' ] ) ), 50, '&hellip;' ) ) . '</span>';
			?>
		</h1>

		<?php
		if ( isset( $_REQUEST[ 'error' ] ) ) {
			$error		 = (int) $_REQUEST[ 'error' ];
			$error_msg	 = '';
			switch ( $error ) {
				case 1 :
					$error_msg	 = __( 'Invalid review ID.' );
					break;
				case 2 :
					$error_msg	 = __( 'You are not allowed to edit reviews on this product.' );
					break;
			}
			if ( $error_msg )
				echo '<div id="moderated" class="error"><p>' . $error_msg . '</p></div>';
		}

		if ( isset( $_REQUEST[ 'approved' ] ) || isset( $_REQUEST[ 'deleted' ] ) || isset( $_REQUEST[ 'trashed' ] ) || isset( $_REQUEST[ 'untrashed' ] ) || isset( $_REQUEST[ 'spammed' ] ) || isset( $_REQUEST[ 'unspammed' ] ) || isset( $_REQUEST[ 'same' ] ) ) {
			$approved	 = isset( $_REQUEST[ 'approved' ] ) ? (int) $_REQUEST[ 'approved' ] : 0;
			$deleted	 = isset( $_REQUEST[ 'deleted' ] ) ? (int) $_REQUEST[ 'deleted' ] : 0;
			$trashed	 = isset( $_REQUEST[ 'trashed' ] ) ? (int) $_REQUEST[ 'trashed' ] : 0;
			$untrashed	 = isset( $_REQUEST[ 'untrashed' ] ) ? (int) $_REQUEST[ 'untrashed' ] : 0;
			$spammed	 = isset( $_REQUEST[ 'spammed' ] ) ? (int) $_REQUEST[ 'spammed' ] : 0;
			$unspammed	 = isset( $_REQUEST[ 'unspammed' ] ) ? (int) $_REQUEST[ 'unspammed' ] : 0;
			$same		 = isset( $_REQUEST[ 'same' ] ) ? (int) $_REQUEST[ 'same' ] : 0;

			if ( $approved > 0 || $deleted > 0 || $trashed > 0 || $untrashed > 0 || $spammed > 0 || $unspammed > 0 || $same > 0 ) {
				if ( $approved > 0 )
					$messages[] = sprintf( _n( '%s review approved', '%s reviews approved', $approved ), $approved );

				if ( $spammed > 0 ) {
					$ids		 = isset( $_REQUEST[ 'ids' ] ) ? $_REQUEST[ 'ids' ] : 0;
					$messages[]	 = sprintf( _n( '%s review marked as spam.', '%s reviews marked as spam.', $spammed ), $spammed ) . ' <a href="' . esc_url( wp_nonce_url( "edit.php?post_type=al_product&page=ic_reviews_al_product&doaction=undo&action=unspam&ids=$ids", "bulk-comments" ) ) . '">' . __( 'Undo' ) . '</a><br />';
				}

				if ( $unspammed > 0 )
					$messages[] = sprintf( _n( '%s review restored from the spam', '%s reviews restored from the spam', $unspammed ), $unspammed );

				if ( $trashed > 0 ) {
					$ids		 = isset( $_REQUEST[ 'ids' ] ) ? $_REQUEST[ 'ids' ] : 0;
					$messages[]	 = sprintf( _n( '%s review moved to the Trash.', '%s reviews moved to the Trash.', $trashed ), $trashed ) . ' <a href="' . esc_url( wp_nonce_url( "edit.php?post_type=al_product&page=ic_reviews_al_product&doaction=undo&action=untrash&ids=$ids", "bulk-comments" ) ) . '">' . __( 'Undo' ) . '</a><br />';
				}

				if ( $untrashed > 0 )
					$messages[] = sprintf( _n( '%s review restored from the Trash', '%s reviews restored from the Trash', $untrashed ), $untrashed );

				if ( $deleted > 0 )
					$messages[] = sprintf( _n( '%s review permanently deleted', '%s reviews permanently deleted', $deleted ), $deleted );

				if ( $same > 0 && $comment = get_comment( $same ) ) {
					switch ( $comment->comment_approved ) {
						case '1' :
							$messages[]	 = __( 'This review is already approved.' ) . ' <a href="' . esc_url( admin_url( "edit.php?post_type=al_product&page=ic_reviews_al_product&comment_type=ic_rev&edit_review=$same" ) ) . '">' . __( 'Edit review' ) . '</a>';
							break;
						case 'trash' :
							$messages[]	 = __( 'This review is already in the Trash.' ) . ' <a href="' . esc_url( admin_url( 'edit.php?post_type=al_product&page=ic_reviews_al_product&comment_status=trash' ) ) . '"> ' . __( 'View Trash' ) . '</a>';
							break;
						case 'spam' :
							$messages[]	 = __( 'This review is already marked as spam.' ) . ' <a href="' . esc_url( admin_url( "edit.php?post_type=al_product&page=ic_reviews_al_product&comment_type=ic_rev&edit_review=$same" ) ) . '">' . __( 'Edit comment' ) . '</a>';
							break;
					}
				}

				echo '<div id="moderated" class="updated notice is-dismissible"><p>' . implode( "<br/>\n", $messages ) . '</p></div>';
			}
		}
		?>

		<?php $ic_ic_revs_table->views(); ?>

		<form id="comments-form" method="get">

			<?php $ic_ic_revs_table->search_box( __( 'Search Reviews' ), 'comment' ); ?>

			<?php if ( $post_id ) : ?>
				<input type="hidden" name="p" value="<?php echo esc_attr( intval( $post_id ) ); ?>" />
				<?php
			endif;

			if ( isset( $_GET[ 'post_type' ] ) ) {
				$post_type = esc_attr( $_GET[ 'post_type' ] );
			} else {
				$post_type = 'post';
			}
			?>
			<input type="hidden" name="comment_status" value="<?php echo esc_attr( $comment_status ); ?>" />
			<input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr( current_time( 'mysql', 1 ) ); ?>" />
			<input type="hidden" name="page" value="<?php echo esc_attr( $_GET[ 'page' ] ) ?>" />
			<input type="hidden" name="post_type" value="<?php echo $post_type ?>" />
			<input type="hidden" name="comment_type" value="<?php echo esc_attr( $_GET[ 'comment_type' ] ) ?>" />

			<input type="hidden" name="_total" value="<?php echo esc_attr( $ic_ic_revs_table->get_pagination_arg( 'total_items' ) ); ?>" />
			<input type="hidden" name="_per_page" value="<?php echo esc_attr( $ic_ic_revs_table->get_pagination_arg( 'per_page' ) ); ?>" />
			<input type="hidden" name="_page" value="<?php echo esc_attr( $ic_ic_revs_table->get_pagination_arg( 'page' ) ); ?>" />

			<?php if ( isset( $_REQUEST[ 'paged' ] ) ) { ?>
				<input type="hidden" name="paged" value="<?php echo esc_attr( absint( $_REQUEST[ 'paged' ] ) ); ?>" />
			<?php } ?>

			<?php $ic_ic_revs_table->display(); ?>
		</form>
	</div>

	<div id="ajax-response"></div>

	<?php
	wp_comment_reply( '-1', true, 'detail' );
	wp_comment_trashnotice();
}
