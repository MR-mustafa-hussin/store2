<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Registers Mailer Dragon post type
 *
 * @version		1.0.0
 * @package		mailer-dragon/includes
 * @author 		Norbert Dreszer
 */
class ic_mailer_register {

	public function __construct() {
		add_action( 'init', array( $this, 'register_mailer_dragon' ) );
		add_action( 'post_updated', array( $this, 'email_save' ), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'email_messages' ) );
		add_action( 'manage_ic_mailer_posts_custom_column', array( $this, 'manage_email_columns' ), 10, 2 );
		add_filter( 'manage_edit-ic_mailer_columns', array( $this, 'add_email_columns' ) );
	}

	public function register_mailer_dragon() {
		$args = array(
			'labels'				 => $this->email_labels(),
			'description'			 => __( 'Description.', 'mailer-dragon' ),
			'public'				 => false,
			'publicly_queryable'	 => false,
			'show_ui'				 => true,
			'show_in_menu'			 => true,
			'query_var'				 => false,
			'register_meta_box_cb'	 => array( $this, 'add_email_metaboxes' ),
			'capability_type'		 => 'email',
			'capabilities'			 => array(
				'publish_posts'			 => 'publish_emails',
				'edit_posts'			 => 'edit_emails',
				'edit_others_posts'		 => 'edit_others_emails',
				'edit_published_posts'	 => 'edit_published_emails',
				'edit_private_posts'	 => 'edit_private_emails',
				'delete_posts'			 => 'delete_emails',
				'delete_others_posts'	 => 'delete_others_emails',
				'delete_private_posts'	 => 'delete_private_emails',
				'delete_published_posts' => 'delete_published_emails',
				'read_private_posts'	 => 'read_private_emails',
				'edit_post'				 => 'edit_email',
				'delete_post'			 => 'delete_email',
				'read_post'				 => 'read_email',
			),
			'map_meta_cap'			 => true,
			'has_archive'			 => false,
			'hierarchical'			 => false,
			'supports'				 => array( 'title', 'editor' ),
			'menu_icon'				 => 'dashicons-email-alt'
		);
		register_post_type( 'ic_mailer', $args );
	}

	public function email_labels() {
		$labels = array(
			'name'					 => _x( 'Newsletter', 'post type general name', 'mailer-dragon' ),
			'singular_name'			 => _x( 'Email', 'post type singular name', 'mailer-dragon' ),
			'menu_name'				 => _x( 'Newsletter', 'admin menu', 'mailer-dragon' ),
			'name_admin_bar'		 => _x( 'Email', 'add new on admin bar', 'mailer-dragon' ),
			'add_new'				 => _x( 'Add New', 'email', 'mailer-dragon' ),
			'add_new_item'			 => __( 'Add New Email', 'mailer-dragon' ),
			'new_item'				 => __( 'New Email', 'mailer-dragon' ),
			'edit_item'				 => __( 'Edit Email', 'mailer-dragon' ),
			'view_item'				 => __( 'View Email', 'mailer-dragon' ),
			'view_items'			 => __( 'View Emails', 'mailer-dragon' ),
			'all_items'				 => __( 'All Emails', 'mailer-dragon' ),
			'search_items'			 => __( 'Search Emails', 'mailer-dragon' ),
			'parent_item_colon'		 => __( 'Parent Emails:', 'mailer-dragon' ),
			'not_found'				 => __( 'No emails found.', 'mailer-dragon' ),
			'not_found_in_trash'	 => __( 'No emails found in Trash.', 'mailer-dragon' ),
			'attributes'			 => __( 'Email attributes', 'mailer-dragon' ),
			'insert_into_item'		 => __( 'Insert into email', 'mailer-dragon' ),
			'uploaded_to_this_item'	 => __( 'Uploaded to this email', 'mailer-dragon' ),
			'featured_image'		 => __( 'Email image', 'mailer-dragon' ),
			'set_featured_image'	 => __( 'Set email image', 'mailer-dragon' ),
			'remove_featured_image'	 => __( 'Remove email image', 'mailer-dragon' ),
			'use_featured_image'	 => __( 'Use as email image', 'mailer-dragon' ),
			'archives'				 => __( 'Email Archives', 'mailer-dragon' )
		);
		return $labels;
	}

	public function email_messages( $messages ) {
		$post		 = get_post();
		$post_type	 = get_post_type( $post );
		//$post_type_object	 = get_post_type_object( $post_type );

		if ( 'ic_mailer' === $post_type ) {
			$messages[ 'ic_mailer' ] = array(
				0	 => '', // Unused. Messages start at index 1.
				1	 => __( 'Email updated for future submissions.', 'mailer-dragon' ),
				2	 => __( 'Custom field updated.', 'mailer-dragon' ),
				3	 => __( 'Custom field deleted.', 'mailer-dragon' ),
				4	 => __( 'Email updated for future submissions.', 'mailer-dragon' ),
				/* translators: %s: date and time of the revision */
				5	 => isset( $_GET[ 'revision' ] ) ? sprintf( __( 'Email restored to revision from %s', 'mailer-dragon' ), wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false,
				6	 => __( 'Email published and will be sent continuously to selected receipments until deleted or deactivated.', 'mailer-dragon' ),
				7	 => __( 'Email saved.', 'mailer-dragon' ),
				8	 => __( 'Email submitted.', 'mailer-dragon' ),
				9	 => sprintf(
				__( 'Email scheduled for: <strong>%1$s</strong>.', 'mailer-dragon' ),
				// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i', 'mailer-dragon' ), strtotime( $post->post_date ) )
				),
				10	 => __( 'Email draft updated.', 'mailer-dragon' )
			);
		}

		return $messages;
	}

	public function add_email_metaboxes() {
		add_meta_box( 'ic_mailer_groups', __( 'Delivery Filters', 'mailer-dragon' ), array( $this, 'email_groups' ), 'ic_mailer', 'side', 'default' );
	}

	public function email_groups( $email ) {
		echo '<input type="hidden" name="ic_mailer_nonce" id="ic_mailer_nonce" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
		echo '<table class="email-filters" style="display: none">';
		do_action( 'ic_mailer_groups', $email->ID );
		echo '</table>';
	}

	public function add_email_columns( $email_columns ) {
		//$new_columns			 = $product_columns;
		foreach ( $email_columns as $index => $column ) {
			if ( $index == 'cb' ) {
				$new_columns[ $index ] = $column;
			} else if ( $index == 'title' ) {
				$new_columns[ $index ]			 = __( 'Email Topic', 'mailer-dragon' );
				$new_columns[ 'delivered_qty' ]	 = __( 'Successful Submissions', 'mailer-dragon' );
				$new_columns[ 'pending_users' ]	 = __( 'Pending Submissions', 'mailer-dragon' );
				$new_columns[ 'delayed_users' ]	 = __( 'Delayed Submissions', 'mailer-dragon' );
				$new_columns[ 'next' ]			 = __( 'Next Submission', 'mailer-dragon' );
				$new_columns					 = apply_filters( 'email_columns_after_name', $new_columns );
			} else if ( $index == 'date' ) {
				$new_columns			 = apply_filters( 'email_columns_before_date', $new_columns );
				$new_columns[ $index ]	 = $column;
			} else {
				$new_columns[ $index ] = $column;
			}
		}
		return apply_filters( 'email_columns', $new_columns );
	}

	public function manage_email_columns( $column_name, $email_id ) {
		$receivers_count = ic_mailer_count_receivers( $email_id );
		switch ( $column_name ) {
			case 'delivered_qty':
				echo ic_mailer_count_receivers_done( $email_id );
				break;
			case 'pending_users':
				echo $receivers_count;
				break;
			case 'delayed_users':
				echo ic_mailer_count_delayed( $email_id );
				break;
			case 'next':
				$next_submission = wp_next_scheduled( 'ic_hourly_scheduled_events' );
				if ( is_int( $next_submission ) && !empty( $receivers_count ) ) {
					echo date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $next_submission );
				} else {
					$nearest = ic_mailer_nearest_delivery( $email_id );
					if ( !empty( $nearest ) ) {
						$nearest = $nearest + ic_mailer_frequency() * DAY_IN_SECONDS;
						echo date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), $nearest );
					} else {
						_e( 'Not necessary', 'mailer-dragon' );
					}
				}
				break;

			default:
				break;
		}
	}

	public function email_save( $email_id, $post ) {
		if ( $post->post_type == 'ic_mailer' ) {
			$ic_mailer_nonce = isset( $_POST[ 'ic_mailer_nonce' ] ) ? $_POST[ 'ic_mailer_nonce' ] : '';
			if ( !empty( $ic_mailer_nonce ) && !wp_verify_nonce( $ic_mailer_nonce, plugin_basename( __FILE__ ) ) ) {
				return $post->ID;
			}
			if ( !isset( $_POST[ 'action' ] ) || (isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] != 'editpost') ) {
				return $post->ID;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post->ID;
			}
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				return $post->ID;
			}
			if ( !current_user_can( 'edit_post', $post->ID ) ) {
				return $post->ID;
			}
			$sanitize					 = new ic_mailer_sanitize;
			$email_meta[ 'ic_mailer' ]	 = isset( $_POST[ 'ic_mailer' ] ) && !empty( $_POST[ 'ic_mailer' ] ) ? $sanitize->text_number( $_POST[ 'ic_mailer' ] ) : '';
			$email_meta					 = apply_filters( 'email_meta_save', $email_meta, $post );
			foreach ( $email_meta as $key => $value ) {
				if ( in_array( $key, get_post_custom_keys( $post->ID ) ) ) {
					$current_value = get_post_meta( $post->ID, $key, true );
				}
				if ( isset( $value ) && !isset( $current_value ) ) {
					add_post_meta( $post->ID, $key, $value, true );
				} else if ( isset( $value ) && $value !== $current_value ) {
					update_post_meta( $post->ID, $key, $value );
				} else if ( !isset( $value ) && $current_value ) {
					delete_post_meta( $post->ID, $key );
				}
			}
			do_action( 'email_edit_save', $post );
		}
	}

}

$email_register = new ic_mailer_register;
