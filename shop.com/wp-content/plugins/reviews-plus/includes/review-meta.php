<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Review metabox folder
 *
 * @version        1.0.0
 * @package        reviews-plus/includes
 * @author        Norbert Dreszer
 */
add_action( 'add_meta_boxes', 'ic_remove_discussion_box', 30 );

/**
 * Replaces default comment metaboxes with review metabox
 */
function ic_remove_discussion_box() {
	$post_types = get_ic_review_active_post_types();
	if ( function_exists( 'product_post_type_array' ) ) {
		$post_types = array_merge( product_post_type_array(), $post_types );
	}
	foreach ( $post_types as $type ) {
		remove_meta_box( 'commentstatusdiv', $type, 'normal' );
		remove_meta_box( 'commentsdiv', $type, 'normal' );
		add_meta_box( 'commentsdiv', __( 'Reviews', 'reviews-plus' ), 'ic_revs_meta_box', $type, 'normal' );
	}
	if ( is_ic_review_edit_screen() ) {
		add_meta_box( 'ic_review_metabox', __( 'Review Details', 'reviews-plus' ), 'ic_review_metabox', 'comment', 'normal' );
	}
}

function ic_review_metabox( $comment ) {
	if ( ic_string_contains( $comment->comment_type, 'ic_rev' ) ) {
		echo ic_ic_rev_rating( $comment->comment_ID, 1, __( 'Rating', 'reviews-plus' ) . ':' );
		echo ic_ic_rev_title( $comment->comment_ID, __( 'Title', 'reviews-plus' ) . ':' );
	}
}

/**
 * Custom comments meta box for product reviews
 * @param type $post
 */
function ic_revs_meta_box( $post ) {
	add_filter( 'gettext', 'ic_ic_revs_labels', 10, 3 );
	ic_reset_comment_counters();
	wp_nonce_field( 'get-comments', 'add_comment_nonce', false );
	$post_type	 = $post->post_type;
	$show_where	 = get_ic_review_show_where_post_types();
	$show_on	 = get_ic_review_show_active_post_types();
	if ( !in_array( $post_type, $show_on ) || empty( $show_where[ $post_type ] ) || $show_where[ $post_type ] !== 'all' ) {
		?>
		<p class="meta-options"><label for="comment_status" class="selectit"><input name="comment_status" type="checkbox" id="comment_status" value="open" <?php checked( $post->comment_status, 'open' ); ?> /> <?php _e( 'Allow reviews.', 'reviews-plus' ) ?></label><br /></p>
		<?php
	}
	/* REQUIRES ADDITIONAL INTEGRATION
	  <p class="hide-if-no-js" id="add-new-comment">
	  <a class="button" href="#commentstatusdiv" onclick="window.commentReply && commentReply.addcomment(<?php echo $post->ID; ?> );
	  return false;"><?php _e( 'Add review', 'reviews-plus' ); ?></a>
	  </p> */
	?>
	<?php
	$comment_type	 = ic_get_comment_type();
	$arg			 = array( 'post_id' => $post->ID, 'post_type' => $post->post_type, 'number' => 1, 'count' => true, 'type' => $comment_type );
	$total			 = get_comments( $arg );
	//print_r( $arg );
	$wp_list_table	 = new IC_ic_revs_List_Table();

	$wp_list_table->display( true );

	if ( 1 > $total ) {
		echo '<p id="no-comments">' . __( 'No reviews yet.', 'reviews-plus' ) . '</p>';
	} else {
		$hidden = get_hidden_meta_boxes( get_current_screen() );
		if ( !in_array( 'commentsdiv', $hidden ) ) {
			?>
			<script type="text/javascript">jQuery( document ).ready( function () {
			                    reviewsBox.get(<?php echo $total; ?>, 10 );
			                } );</script>
			<?php
		}
		?>
		<p class="hide-if-no-js" id="show-comments">
		   <a href="#commentstatusdiv" onclick="reviewsBox.get(<?php echo $total; ?> );
		                   return false;"><?php _e( 'Show reviews', 'reviews-plus' ); ?></a> <span class="spinner"></span>
		</p><?php
	}
	ic_reviews_trashnotice();
}

add_filter( 'wp_comment_reply', 'ic_reviews_reply', 10, 2 );

/**
 * Custom comment reply in admin for product reviews
 *
 * @global type $wp_list_table
 * @param type $content
 * @param type $args
 * @return type
 */
function ic_reviews_reply( $content, $args ) {
	if ( function_exists( 'is_ic_catalog_admin_page' ) && is_ic_catalog_admin_page() ) {
		global $wp_list_table;
		$mode		 = $args[ 'mode' ];
		$checkbox	 = $args[ 'checkbox' ];
		$position	 = $args[ 'position' ];
		if ( !$wp_list_table ) {
			if ( $mode == 'single' ) {
				$wp_list_table = new IC_ic_revs_List_Table();
			} else {
				$wp_list_table = new IC_Reviews_List_Table();
			}
		}
		$comment_type		 = ic_get_comment_type();
		ob_start();
		?>
		<form method="get">
			<table style="display:none;">
				<tbody id="com-reply">
					<tr id="replyrow" class="inline-edit-row" style="display:none;">
						<td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="colspanchange">
							<div id="replyhead" style="display:none;"><h5><?php _e( 'Reply to Review', 'reviews-plus' ); ?></h5></div>
							<div id="addhead" style="display:none;"><h5><?php _e( 'Add new Review', 'reviews-plus' ); ?></h5></div>
							<div id="edithead" style="display:none;">
								<div class="inside">
									<label for="author"><?php _e( 'Name', 'reviews-plus' ) ?></label>
									<input type="text" name="newcomment_author" size="50" value="" id="author-name" />
								</div>

								<div class="inside">
									<label for="author-email"><?php _e( 'E-mail', 'reviews-plus' ) ?></label>
									<input type="text" name="newcomment_author_email" size="50" value="" id="author-email" />
								</div>

								<div class="inside">
									<label for="author-url"><?php _e( 'URL', 'reviews-plus' ) ?></label>
									<input type="text" id="author-url" name="newcomment_author_url" class="code" size="103" value="" />
								</div>

								<div class="inside">
									<label for="review-title"><?php _e( 'Title', 'reviews-plus' ) ?></label>
									<input type="text" id="review-title" name="ic_review_title" class="code" size="103" value="" />
								</div>
								<div class="inside rating-container">
									<label for="review-rating"><?php _e( 'Rating', 'reviews-plus' ) ?></label> <?php echo ic_ic_rev_rating( 0, 1 ) ?>
								</div>
								<div style="clear:both;"></div>
							</div>

							<div id="replycontainer">
								<?php
								//echo '<div class="reply-rating" style="display: none">' . sprintf( __( '%sYour rating%s', 'reviews-plus' ), '<p class="rating-label">', ':</p> ' . ic_ic_rev_rating( 0, 1 ) ) . '</div>';
								$quicktags_settings	 = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
								wp_editor( '', 'replycontent', array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => $quicktags_settings ) );
								?>
							</div>

							<p id="replysubmit" class="submit">
								<a href="#comments-form" class="save button-primary alignright">
									<span id="addbtn" style="display:none;"><?php _e( 'Add Review', 'reviews-plus' ); ?></span>
									<span id="savebtn" style="display:none;"><?php _e( 'Update Review', 'reviews-plus' ); ?></span>
									<span id="replybtn" style="display:none;"><?php _e( 'Submit Reply', 'reviews-plus' ); ?></span></a>
								<a href="#comments-form" class="cancel button-secondary alignleft"><?php _e( 'Cancel', 'reviews-plus' ); ?></a>
								<span class="waiting spinner"></span>
								<span class="error" style="display:none;"></span>
								<br class="clear" />
							</p>

							<input type="hidden" name="action" id="action" value="" />
							<input type="hidden" name="comment_type" value="<?php echo $comment_type ?>" />
							<input type="hidden" name="comment_ID" id="comment_ID" value="" />
							<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="" />
							<input type="hidden" name="status" id="status" value="" />
							<input type="hidden" name="position" id="position" value="<?php echo $position; ?>" />
							<input type="hidden" name="checkbox" id="checkbox" value="<?php echo $checkbox ? 1 : 0; ?>" />
							<input type="hidden" name="mode" id="mode" value="<?php echo esc_attr( $mode ); ?>" />
							<?php
							wp_nonce_field( 'replyto-comment', '_ajax_nonce-replyto-comment', false );
							if ( current_user_can( 'unfiltered_html' ) )
								wp_nonce_field( 'unfiltered-html-comment', '_wp_unfiltered_html_comment', false );
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		<?php
		$content			 = ob_get_clean();
	}
	return $content;
}

/**
 * Output 'undo move to trash' text for reviews
 *
 * @since 2.9.0
 */
function ic_reviews_trashnotice() {
	?>
	<div class="hidden" id="trash-undo-holder">
		<div class="trash-undo-inside"><?php printf( __( 'Review by %s moved to the trash.', 'reviews-plus' ), '<strong></strong>' ); ?> <span class="undo untrash"><a href="#"><?php _e( 'Undo' ); ?></a></span></div>
	</div>
	<div class="hidden" id="spam-undo-holder">
		<div class="spam-undo-inside"><?php printf( __( 'Review by %s marked as spam.', 'reviews-plus' ), '<strong></strong>' ); ?> <span class="undo unspam"><a href="#"><?php _e( 'Undo' ); ?></a></span></div>
	</div>
	<?php
}

add_action( 'edit_comment', 'ic_reviews_update_save', 10, 2 );

/**
 * Handles manual comment update in admin
 *
 * @param type $comment_ID
 * @param type $comment
 */
function ic_reviews_update_save( $comment_ID, $comment ) {
	if ( ic_string_contains( $comment[ 'comment_type' ], 'ic_rev' ) ) {
		ic_save_ic_rev_rating( $comment_ID );
	}
}
