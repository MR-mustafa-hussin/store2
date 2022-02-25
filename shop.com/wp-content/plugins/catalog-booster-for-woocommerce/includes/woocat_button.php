<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages Discounts payment form
 *
 * Here Discounts payment form is defined and managed.
 *
 * @version		1.0.0
 * @package		woocommerce-catalog-booster/includes
 * @author 		Norbert Dreszer
 */
add_action( 'wp', 'ic_woocat_enable_button' );

function ic_woocat_enable_button() {
	$ic_woocat = ic_woocat_settings();
	if ( !empty( $ic_woocat[ 'button' ][ 'enable' ] ) ) {
		add_action( 'woocommerce_single_product_summary', 'ic_woocat_show_button' );
	}
}

function ic_woocat_show_button() {
	$ic_woocat = ic_woocat_settings();
	if ( !empty( $ic_woocat[ 'button' ][ 'enable' ] ) ) {
		$url = ic_woocat_get_button_url();
		if ( !empty( $url ) ) {
			?>
			<a href="<?php echo $url ?>" class="button"><?php echo $ic_woocat[ 'button' ][ 'label' ] ?></a>
			<?php
		}
	}
}

function ic_woocat_get_button_url() {
	$ic_woocat	 = ic_woocat_settings();
	$url		 = '';
	if ( !empty( $ic_woocat[ 'button' ][ 'enable' ] ) ) {
		$url = $ic_woocat[ 'button' ][ 'url' ];
		if ( !empty( $ic_woocat[ 'button' ][ 'individual' ] ) ) {
			$url = get_post_meta( get_the_ID(), 'ic_woocat_url', true );
			if ( empty( $url ) && !empty( $ic_woocat[ 'button' ][ 'use_default' ] ) ) {
				$url = $ic_woocat[ 'button' ][ 'url' ];
			}
		}
	}
	return $url;
}

add_action( 'add_meta_boxes_product', 'ic_woocat_button_metabox' );

function ic_woocat_button_metabox( $post ) {
	$ic_woocat = ic_woocat_settings();
	if ( !empty( $ic_woocat[ 'button' ][ 'individual' ] ) ) {
		add_meta_box( 'ic_woocat_button', __( 'Button', 'catalog-booster-for-woocommerce' ), 'ic_woocat_button_metabox_content', 'product', 'side', 'default' );
	}
}

function ic_woocat_button_metabox_content() {
	echo '<table>';
	$url = ic_woocat_get_button_url();
	implecode_settings_text( __( 'Button URL', 'catalog-booster-for-woocommerce' ), 'ic_woocat_url', $url );
	echo '</table>';
}

add_action( 'woocommerce_process_product_meta', 'ic_woocat_save_meta' );

function ic_woocat_save_meta( $product_id ) {
	if ( isset( $_POST[ 'ic_woocat_url' ] ) ) {
		$url = esc_url( $_POST[ 'ic_woocat_url' ] );
		update_post_meta( $product_id, 'ic_woocat_url', $url );
	}
}
