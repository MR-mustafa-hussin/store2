<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 *
 *  @version       1.0.0
 *  @package
 *  @author        impleCode
 *
 */

class ic_epc_block_edit {

	function __construct() {
		add_action( 'ic_after_layout_integration_setting_html', array( $this, 'edit_settings' ) );
		add_filter( 'catalog_multiple_settings', array( $this, 'default_edit' ) );
		add_action( 'init', array( $this, 'init' ), 10, 3 );
	}

	function init() {
		if ( !$this->enabled() ) {
			return;
		}
		add_filter( 'ic_epc_allow_gutenberg', array( $this, 'ret_true' ) );

		global $ic_register_product;
		if ( !empty( $ic_register_product ) ) {
			remove_action( 'current_screen', array( $ic_register_product, 'edit_screen' ) );
			add_action( 'do_meta_boxes', array( $ic_register_product, 'change_image_box' ) );
			add_action( 'add_product_metaboxes', array( $this, 'modify_boxes' ) );
			add_action( 'add_meta_boxes', array( $this, 'modify_boxes' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'modify_editor' ) );
			if ( !class_exists( 'jQuery_Migrate_Helper' ) ) {
				add_filter( 'ic_product_short_desc_input', array( $this, 'excerpt_textarea' ) );
			}
		}
	}

	function excerpt_textarea() {
		global $post;
		ob_start();
		post_excerpt_meta_box( $post );
		return ob_get_clean();
	}

	function modify_boxes() {
		remove_meta_box( 'al_product_desc', 'al_product', 'normal' );
	}

	function modify_editor() {
		if ( is_ic_edit_product_screen() || is_ic_new_product_screen() ) {
			wp_enqueue_script( 'ic_epc_modify_editor', AL_PLUGIN_BASE_PATH . 'includes/blocks/js/modify-editor.js' . ic_filemtime( AL_BASE_PATH . '/includes/blocks/js/modify-editor.js' ), array( 'wp-edit-post' ), IC_EPC_VERSION, true );
		}
	}

	function edit_settings( $settings ) {
		?>
		<h3><?php _e( 'Edit Mode', 'ecommerce-product-catalog' ); ?></h3>
		<table><?php
			implecode_settings_radio( __( 'Product Edit Mode', 'ecommerce-product-catalog' ), 'archive_multiple_settings[edit_mode]', $settings[ 'edit_mode' ], array( 'classic' => __( 'Classic Editor', 'ecommerce-product-catalog' ), 'blocks' => __( 'Blocks', 'ecommerce-product-catalog' ) . ' (Gutenberg)' ), 1, __( 'Choose how would you like to edit the products.', 'ecommerce-product-catalog' ) );
			?>
		</table>
		<?php
	}

	function default_edit( $settings ) {
		$settings[ 'edit_mode' ] = !empty( $settings[ 'edit_mode' ] ) ? $settings[ 'edit_mode' ] : 'classic';
		return $settings;
	}

	function ret_true() {
		return true;
	}

	function enabled() {
		$archive_multiple_settings = get_multiple_settings();
		if ( $archive_multiple_settings[ 'edit_mode' ] === 'blocks' ) {
			return true;
		}
		return false;
	}

}

$ic_epc_block_edit = new ic_epc_block_edit;
