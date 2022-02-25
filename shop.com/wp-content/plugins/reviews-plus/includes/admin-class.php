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
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if ( !class_exists( 'WP_Comments_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-comments-list-table.php' );
}

class IC_Reviews_List_Table extends WP_Comments_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @global int $post_id
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct( $args = array() ) {
		global $post_id;

		$post_id = isset( $_REQUEST[ 'p' ] ) ? absint( $_REQUEST[ 'p' ] ) : 0;

		if ( get_option( 'show_avatars' ) )
			add_filter( 'comment_author', 'floated_admin_avatar' );
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		WP_List_Table::__construct( array(
			'plural'	 => 'reviews',
			'singular'	 => 'review',
			'ajax'		 => true,
			'screen'	 => isset( $args[ 'screen' ] ) ? $args[ 'screen' ] : $screen,
		) );
	}

	/**
	 *
	 * @global int $post_id
	 *
	 * @return array
	 */
	public function get_columns() {
		global $post_id;

		$columns = array();

		if ( $this->checkbox )
			$columns[ 'cb' ] = '<input type="checkbox" />';

		$columns[ 'author' ]	 = __( 'Author' );
		$columns[ 'rating' ]	 = __( 'Rating', 'reviews-plus' );
		$columns[ 'comment' ]	 = __( 'Review', 'reviews-plus' );

		if ( !$post_id ) {
			$columns[ 'response' ] = __( 'Review item', 'product-review' );
		}

		return $columns;
	}

	/**
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can( 'edit_post' );
	}

	/**
	 *
	 * @global int    $post_id
	 * @global string $comment_status
	 * @global string $search
	 * @global string $comment_type
	 */
	public function prepare_items() {
		global $post_id, $comment_status, $search, $comment_type;

		$comment_status	 = isset( $_REQUEST[ 'comment_status' ] ) ? sanitize_text_field( $_REQUEST[ 'comment_status' ] ) : 'all';
		if ( !in_array( $comment_status, array( 'all', 'moderated', 'approved', 'spam', 'trash' ) ) )
			$comment_status	 = 'all';

		//$comment_type = ic_get_comment_type();

		$search = ( isset( $_REQUEST[ 's' ] ) ) ? sanitize_text_field( $_REQUEST[ 's' ] ) : '';

		$post_type = ( isset( $_REQUEST[ 'post_type' ] ) ) ? sanitize_key( $_REQUEST[ 'post_type' ] ) : '';

		$user_id = ( isset( $_REQUEST[ 'user_id' ] ) ) ? absint( $_REQUEST[ 'user_id' ] ) : '';

		$orderby = ( isset( $_REQUEST[ 'orderby' ] ) ) ? sanitize_text_field( $_REQUEST[ 'orderby' ] ) : '';
		$order	 = ( isset( $_REQUEST[ 'order' ] ) ) ? sanitize_text_field( $_REQUEST[ 'order' ] ) : '';

		$comments_per_page = $this->get_per_page( $comment_status );

		$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( isset( $_REQUEST[ 'number' ] ) ) {
			$number = (int) $_REQUEST[ 'number' ];
		} else {
			$number = $comments_per_page + min( 8, $comments_per_page ); // Grab a few extra
		}

		$page = $this->get_pagenum();

		if ( isset( $_REQUEST[ 'start' ] ) ) {
			$start = intval( $_REQUEST[ 'start' ] );
		} else {
			$start = ( $page - 1 ) * $comments_per_page;
		}

		if ( $doing_ajax && isset( $_REQUEST[ 'offset' ] ) ) {
			$start += intval( $_REQUEST[ 'offset' ] );
		}

		$status_map = array(
			'moderated'	 => 'hold',
			'approved'	 => 'approve',
			'all'		 => '',
		);

		$args		 = array(
			'status'	 => isset( $status_map[ $comment_status ] ) ? $status_map[ $comment_status ] : $comment_status,
			'search'	 => $search,
			'user_id'	 => $user_id,
			'offset'	 => $start,
			'number'	 => $number,
			'post_id'	 => $post_id,
			'type'		 => $comment_type,
			'orderby'	 => $orderby,
			'order'		 => $order,
			'post_type'	 => $post_type,
		);
		remove_all_filters( 'comments_clauses' );
		$_comments	 = get_comments( $args );

		if ( is_array( $_comments ) ) {
			update_comment_cache( $_comments );

			$this->items		 = array_slice( $_comments, 0, $comments_per_page );
			$this->extra_items	 = array_slice( $_comments, $comments_per_page );

			$_comment_post_ids = array_unique( wp_list_pluck( $_comments, 'comment_post_ID' ) );

			$this->pending_count = get_pending_comments_num( $_comment_post_ids );
		}

		$total_comments = get_comments( array_merge( $args, array(
			'count'	 => true,
			'offset' => 0,
			'number' => 0
		) ) );

		$this->set_pagination_args( array(
			'total_items'	 => $total_comments,
			'per_page'		 => $comments_per_page,
		) );
	}

	/**
	 *
	 * @global int $post_id
	 * @global string $comment_status
	 * @global string $comment_type
	 */
	protected function get_views() {
		global $post_id, $comment_status, $comment_type;

		$status_links	 = array();
		$num_comments	 = ( $post_id ) ? wp_count_comments( $post_id ) : wp_count_comments();
		//, number_format_i18n($num_comments->moderated) ), "<span class='comment-count'>" . number_format_i18n($num_comments->moderated) . "</span>"),
		//, number_format_i18n($num_comments->spam) ), "<span class='spam-comment-count'>" . number_format_i18n($num_comments->spam) . "</span>")
		$stati			 = array(
			/* translators: %s: all comments count */
			'all' => _nx_noop(
			'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', 'comments'
			), // singular not used

			/* translators: %s: pending comments count */
			'moderated'	 => _nx_noop(
			'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'comments'
			),
			/* translators: %s: approved comments count */
			'approved'	 => _nx_noop(
			'Approved <span class="count">(%s)</span>', 'Approved <span class="count">(%s)</span>', 'comments'
			),
			/* translators: %s: spam comments count */
			'spam'		 => _nx_noop(
			'Spam <span class="count">(%s)</span>', 'Spam <span class="count">(%s)</span>', 'comments'
			),
			/* translators: %s: trashed comments count */
			'trash'		 => _nx_noop(
			'Trash <span class="count">(%s)</span>', 'Trash <span class="count">(%s)</span>', 'comments'
			)
		);

		if ( !EMPTY_TRASH_DAYS ) {
			unset( $stati[ 'trash' ] );
		}
		if ( isset( $_GET[ 'post_type' ] ) ) {
			$post_type	 = sanitize_text_field( $_GET[ 'post_type' ] );
			$page		 = 'edit.php?post_type=' . $post_type;
		} else {
			$post_type	 = 'post';
			$page		 = 'edit.php';
		}
		$link = $page . '&page=ic_reviews_' . $post_type;
		if ( !empty( $comment_type ) && 'all' != $comment_type ) {
			$link = add_query_arg( 'comment_type', $comment_type, $link );
		}

		foreach ( $stati as $status => $label ) {
			$class = ( $status == $comment_status ) ? ' class="current"' : '';

			if ( !isset( $num_comments->$status ) )
				$num_comments->$status	 = 10;
			$link					 = add_query_arg( 'comment_status', $status, $link );
			if ( $post_id )
				$link					 = add_query_arg( 'p', absint( $post_id ), $link );
			/*
			  // I toyed with this, but decided against it. Leaving it in here in case anyone thinks it is a good idea. ~ Mark
			  if ( !empty( $_REQUEST['s'] ) )
			  $link = add_query_arg( 's', esc_attr( wp_unslash( $_REQUEST['s'] ) ), $link );
			 */
			$status_links[ $status ] = "<a href='$link'$class>" . sprintf(
			translate_nooped_plural( $label, $num_comments->$status ), sprintf( '<span class="%s-count">%s</span>', ( 'moderated' === $status ) ? 'pending' : $status, number_format_i18n( $num_comments->$status )
			)
			) . '</a>';
		}

		/**
		 * Filter the comment status links.
		 *
		 * @since 2.5.0
		 *
		 * @param array $status_links An array of fully-formed status links. Default 'All'.
		 *                            Accepts 'All', 'Pending', 'Approved', 'Spam', and 'Trash'.
		 */
		return apply_filters( 'comment_status_links', $status_links );
	}

	//This is only for bulk edit nonce
	/**
	 * @access public
	 */
	public function display() {

		wp_nonce_field( "fetch-list-" . get_class( $this ), '_ajax_fetch_list_nonce' );
		//$ic_list_table	 = new IC_List_Table();
		$this->display_tablenav( 'top' );
		?>
		<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>

			<tbody id="the-comment-list" data-wp-lists="list:comment">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>

			<tbody id="the-extra-comment-list" data-wp-lists="list:comment" style="display: none;">
				<?php
				$this->items = $this->extra_items;
				$this->display_rows();
				?>
			</tbody>

			<tfoot>
				<tr>
					<?php $this->print_column_headers( false ); ?>
				</tr>
			</tfoot>

		</table>
		<?php
		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Display a comment count bubble
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param int $post_id          The post ID.
	 * @param int $pending_comments Number of pending comments.
	 */
	protected function comments_bubble( $post_id, $pending_comments ) {
		$approved_comments = get_comments_number();

		$approved_comments_number	 = number_format_i18n( $approved_comments );
		$pending_comments_number	 = number_format_i18n( $pending_comments );

		$approved_only_phrase	 = sprintf( _n( '%s review', '%s reviews', $approved_comments ), $approved_comments_number );
		$approved_phrase		 = sprintf( _n( '%s approved review', '%s approved reviews', $approved_comments ), $approved_comments_number );
		$pending_phrase			 = sprintf( _n( '%s pending review', '%s pending reviews', $pending_comments ), $pending_comments_number );

		// No reviews at all.
		if ( !$approved_comments && !$pending_comments ) {
			printf( '<span aria-hidden="true">—</span><span class="screen-reader-text">%s</span>', __( 'No reviews' )
			);
			// Approved comments have different display depending on some conditions.
		} elseif ( $approved_comments ) {
			printf( '<a href="%s" class="post-com-count post-com-count-approved"><span class="comment-count-approved" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>', esc_url( add_query_arg( array( 'p' => $post_id, 'comment_status' => 'approved' ), admin_url( 'edit.php?post_type=al_product&page=ic_reviews_al_product' ) ) ), $approved_comments_number, $pending_comments ? $approved_phrase : $approved_only_phrase
			);
		} else {
			printf( '<span class="post-com-count post-com-count-no-comments"><span class="comment-count comment-count-no-comments" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>', $approved_comments_number, $pending_comments ? __( 'No approved reviews' ) : __( 'No reviews' )
			);
		}

		if ( $pending_comments ) {
			printf( '<a href="%s" class="post-com-count post-com-count-pending"><span class="comment-count-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>', esc_url( add_query_arg( array( 'p' => $post_id, 'comment_status' => 'moderated' ), admin_url( 'edit.php?post_type=al_product&page=ic_reviews_al_product' ) ) ), $pending_comments_number, $pending_phrase
			);
		}
	}

	/**
	 * @access public
	 */
	public function column_response( $comment ) {
		$post = get_post();

		if ( !$post ) {
			return;
		}

		if ( isset( $this->pending_count[ $post->ID ] ) ) {
			$pending_comments = $this->pending_count[ $post->ID ];
		} else {
			$_pending_count_temp				 = get_pending_comments_num( array( $post->ID ) );
			$pending_comments					 = $this->pending_count[ $post->ID ]	 = $_pending_count_temp[ $post->ID ];
		}

		if ( current_user_can( 'edit_post', $post->ID ) ) {
			$post_link = "<a href='" . get_edit_post_link( $post->ID ) . "' class='comments-edit-item-link'>";
			$post_link .= esc_html( get_the_title( $post->ID ) ) . '</a>';
		} else {
			$post_link = esc_html( get_the_title( $post->ID ) );
		}

		echo '<div class="response-links">';
		if ( 'attachment' == $post->post_type && ( $thumb = wp_get_attachment_image( $post->ID, array( 80, 60 ), true ) ) ) {
			echo $thumb;
		}
		echo $post_link;
		$post_type_object = get_post_type_object( $post->post_type );
		echo "<a href='" . get_permalink( $post->ID ) . "' class='comments-view-item-link'>" . $post_type_object->labels->view_item . '</a>';
		echo '<span class="post-com-count-wrapper">';
		$this->comments_bubble( $post->ID, $pending_comments );
		echo '</span> ';
		echo '</div>';
	}

	public function column_author( $comment ) {
		global $comment_status;

		$author_url = get_comment_author_url( $comment );

		$author_url_display = untrailingslashit( preg_replace( '|^http(s)?://(www\.)?|i', '', $author_url ) );
		if ( strlen( $author_url_display ) > 50 ) {
			$author_url_display = wp_html_excerpt( $author_url_display, 49, '&hellip;' );
		}

		echo "<strong>";
		comment_author( $comment );
		echo '</strong><br />';
		if ( !empty( $author_url_display ) ) {
			printf( '<a href="%s">%s</a><br />', esc_url( $author_url ), esc_html( $author_url_display ) );
		}

		if ( current_user_can( 'moderate_comments' ) ) {
			if ( !empty( $comment->comment_author_email ) ) {
				/* This filter is documented in wp-includes/comment-template.php */
				$email = apply_filters( 'comment_email', $comment->comment_author_email, $comment );

				if ( !empty( $email ) && '@' !== $email ) {
					printf( '<a href="%1$s">%2$s</a><br />', esc_url( 'mailto:' . $email ), esc_html( $email ) );
				}
			}
		}
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {

		if ( 'top' == $which )
			wp_nonce_field( 'bulk-' . $this->_args[ 'plural' ] );
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<div class="alignleft actions bulkactions">
				<?php $this->bulk_actions( $which ); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>

			<br class="clear" />
		</div>
		<?php
	}

	/**
	 * extra_tablenav without comment types drop-down
	 *
	 * @global string $comment_status
	 * @global string $comment_type
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		global $comment_status, $comment_type;
		?>
		<div class="alignleft actions">
			<?php
			if ( ( 'spam' == $comment_status || 'trash' == $comment_status ) && current_user_can( 'moderate_comments' ) ) {
				wp_nonce_field( 'bulk-destroy', '_destroy_nonce' );
				$title = ( 'spam' == $comment_status ) ? esc_attr__( 'Empty Spam' ) : esc_attr__( 'Empty Trash' );
				submit_button( $title, 'apply', 'delete_all', false );
			}
			/**
			 * Fires after the Filter submit button for comment types.
			 *
			 * @since 2.5.0
			 *
			 * @param string $comment_status The comment status name. Default 'All'.
			 */
			do_action( 'manage_comments_nav', $comment_status );
			echo '</div>';
		}

	}

	/**
	 * Post Comments List Table class.
	 *
	 * @package WordPress
	 * @subpackage List_Table
	 * @since 3.1.0
	 * @access private
	 *
	 * @see WP_Comments_Table
	 */
	class IC_ic_revs_List_Table extends IC_Reviews_List_Table {

		/**
		 *
		 * @return array
		 */
		protected function get_column_info() {
			$comment_type = ic_get_comment_type();
			return array(
				array(
					'author'	 => __( 'Author', 'reviews-plus' ),
					'rating'	 => __( 'Rating', 'reviews-plus' ),
					'comment'	 => __( 'Review', 'reviews-plus' ),
				),
				array(),
				array(),
				$comment_type,
			);
		}

		public function column_rating( $comment ) {
			echo '<div style="text-align:center;font-weight:bold;">' . ic_get_ic_rev_title( $comment->comment_ID ) . '</div>';
			echo ic_ic_rev_rating( $comment->comment_ID );
		}

		/**
		 *
		 * @return array
		 */
		protected function get_table_classes() {
			$classes	 = parent::get_table_classes();
			$classes[]	 = 'wp-list-table';
			$classes[]	 = 'comments-box';
			return $classes;
		}

		/**
		 *
		 * @param bool $output_empty
		 */
		public function display( $output_empty = false ) {
			$singular = $this->_args[ 'singular' ];
			wp_nonce_field( "fetch-list-" . get_class( $this ), '_ajax_fetch_list_nonce' );
			?>
			<table class="<?php echo implode( ' ', $this->get_table_classes() ); ?>" style="display:none;">
				<tbody id="the-comment-list"<?php
					   if ( $singular ) {
						   echo " data-wp-lists='list:$singular'";
					   }
					   ?>>
						   <?php
						   if ( !$output_empty ) {
							   $this->display_rows_or_placeholder();
						   }
						   ?>
				</tbody>
			</table>
			<?php
		}

		/**
		 *
		 * @param bool $comment_status
		 * @return int
		 */
		public function get_per_page( $comment_status = false ) {
			return 10;
		}

	}

	add_action( 'wp_ajax_get_reviews', 'ic_ajax_get_reviews' );

	/**
	 * Ajax handler for getting comments.
	 *
	 * @since 3.1.0
	 *
	 * @global WP_List_Table $wp_list_table
	 * @global int           $post_id
	 *
	 * @param string $action Action to perform.
	 */
	function ic_ajax_get_reviews( $action ) {
		add_filter( 'gettext', 'ic_ic_revs_labels', 10, 3 );

		global $wp_list_table, $post_id;
		if ( empty( $action ) )
			$action = 'get-comments';

		check_ajax_referer( $action );

		if ( empty( $post_id ) && !empty( $_REQUEST[ 'p' ] ) ) {
			$id		 = absint( $_REQUEST[ 'p' ] );
			if ( !empty( $id ) )
				$post_id = $id;
		}

		if ( empty( $post_id ) ) {
			wp_die( -1 );
		}

		$wp_list_table = new IC_ic_revs_List_Table( array( 'screen' => 'edit-comments' ) );


		if ( !current_user_can( 'edit_post', $post_id ) ) {
			wp_die( -1 );
		}

		$wp_list_table->prepare_items();

		if ( !$wp_list_table->has_items() )
			wp_die( 1 );

		$x = new WP_Ajax_Response();
		ob_start();
		foreach ( $wp_list_table->items as $comment ) {
			if ( !current_user_can( 'edit_comment', $comment->comment_ID ) ) {
				continue;
			}
			get_comment( $comment );
			$wp_list_table->single_row( $comment );
		}
		$comment_list_item = ob_get_clean();

		$x->add( array(
			'what'	 => 'comments',
			'data'	 => $comment_list_item
		) );
		$x->send();
	}

	add_filter( 'comment_feed_where', 'ic_hide_ic_revs_from_feeds', 10, 2 );

	/**
	 * Exclude reviews (comments) on from showing in comment feeds
	 *
	 * @param array $where
	 * @param obj $wp_comment_query WordPress Comment Query Object
	 * @return array $where
	 */
	function ic_hide_ic_revs_from_feeds( $where, $wp_comment_query ) {
		global $wpdb;
		$post_types = get_ic_review_active_post_types();

		$where .= $wpdb->prepare( " AND comment_type != %s", 'ic_rev' );
		foreach ( $post_types as $type ) {
			$where .= $wpdb->prepare( " AND comment_type != %s", 'ic_rev_' . $type );
		}
		return $where;
	}

	add_filter( 'wp_count_comments', 'ic_remove_ic_revs_in_comment_counts', 10, 2 );

	/**
	 * Remove product reviews from the wp_count_comments function
	 *
	 * @access public
	 * @param array $stats (empty from core filter)
	 * @param int $post_id Post ID
	 * @return array Array of comment counts
	 */
	function ic_remove_ic_revs_in_comment_counts( $stats, $post_id ) {
		global $wpdb, $comment_type;

		$count = wp_cache_get( "comments-{$post_id}", 'counts' );
		if ( false !== $count ) {
			return $count;
		}

		$post_id = (int) $post_id;

		$stats = wp_cache_get( "comments-{$post_id}", 'counts' );

		if ( false !== $stats ) {
			return $stats;
		}
		$where					 = 'WHERE';
		$active_comment_types	 = ic_get_active_comment_types();
		foreach ( $active_comment_types as $key => $active_comment_type ) {
			if ( !empty( $key ) ) {
				$where .= ' AND';
			}
			if ( $comment_type == $active_comment_type ) {
				$where .= ' comment_type = "' . $active_comment_type . '"';
			} else {
				$where .= ' comment_type != "' . $active_comment_type . '"';
			}
		}

		if ( $post_id > 0 ) {
			$where .= $wpdb->prepare( " AND comment_post_ID = %d", $post_id );
		}

		$totals = (array) $wpdb->get_results( "
		SELECT comment_approved, COUNT( * ) AS total
		FROM {$wpdb->comments}
		{$where}
		GROUP BY comment_approved
	", ARRAY_A );

		$comment_count = array(
			'approved'				 => 0,
			'awaiting_moderation'	 => 0,
			'spam'					 => 0,
			'trash'					 => 0,
			'post-trashed'			 => 0,
			'total_comments'		 => 0,
			'all'					 => 0,
		);

		foreach ( $totals as $row ) {
			switch ( $row[ 'comment_approved' ] ) {
				case 'trash':
					$comment_count[ 'trash' ]				 = $row[ 'total' ];
					break;
				case 'post-trashed':
					$comment_count[ 'post-trashed' ]		 = $row[ 'total' ];
					break;
				case 'spam':
					$comment_count[ 'spam' ]				 = $row[ 'total' ];
					$comment_count[ 'total_comments' ] += $row[ 'total' ];
					break;
				case '1':
					$comment_count[ 'approved' ]			 = $row[ 'total' ];
					$comment_count[ 'total_comments' ] += $row[ 'total' ];
					$comment_count[ 'all' ] += $row[ 'total' ];
					break;
				case '0':
					$comment_count[ 'awaiting_moderation' ]	 = $row[ 'total' ];
					$comment_count[ 'total_comments' ] += $row[ 'total' ];
					$comment_count[ 'all' ] += $row[ 'total' ];
					break;
				default:
					break;
			}
		}
		$stats					 = $comment_count;
		$stats[ 'moderated' ]	 = $stats[ 'awaiting_moderation' ];
		unset( $stats[ 'awaiting_moderation' ] );

		$stats_object = (object) $stats;
		wp_cache_set( "comments-{$post_id}", $stats_object, 'counts' );

		return $stats_object;
	}
