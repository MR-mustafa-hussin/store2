<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
/**
 * eCommerce Product Catalog Uninstall
 *
 * Uninstalling eCommerce Product Catalog deletes user roles and options.
 *
 * @package     ecommerce-product-catalog/uninstall
 * @version     2.3.7
 */
if ( !defined( 'MAILER_DRAGON_BASE_PATH' ) ) {
	$settings = get_option( 'ic_mailer_settings' );
	if ( !empty( $settings[ 'delete_all' ] ) ) {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'ic_mailer' );" );
		$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );


		delete_option( 'ic_mailer_dragon_install' );
		delete_option( 'ic_mailer_settings' );
		delete_option( 'ic_mailer_custom' );
		delete_option( 'ic_mailers_published' );

		$args		 = array( 'role' => 'mailer_subscriber' );
		$subscribers = get_users( $args );
		foreach ( $subscribers as $user ) {
			if ( count( $user->roles ) == 1 ) {
				wp_delete_user( $user->ID );
			}
		}
		remove_role( 'mailer_subscriber' );
	}
}