<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since             1.0.0
 * @package           Zobnin_Text_Attributes_For_WooCommerce
 * @subpackage 				Zobnin_Text_Attributes_For_WooCommerce/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

class Zobnin_Text_Attributes_For_WooCommerce_Admin {

	/** @var null|static */
	protected static $_instance = null;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since      1.0.0
	 * @access  	 public
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * @return Zobnin_Text_Attributes_For_WooCommerce_Admin
	 * @access  public
	 */
	public static function instance( $plugin_name = '', $version = '' ) {
		if ( ! empty( static::$_instance ) ) {
			return static::$_instance;
		}
		static::$_instance = new Zobnin_Text_Attributes_For_WooCommerce_Admin( $plugin_name, $version );
		return static::$_instance;
	}

	/**
	 * Add new types for an attributes.
	 *
	 * @since  	1.0.0
	 * @access  public
	 * @return  array  Types
	 */
	public function add_types( $array ) {
		$array[ 'text' ] =  __( 'Text', 'text-attributes-for-woocommerce' );
		$array[ 'number' ] =  __( 'Number', 'text-attributes-for-woocommerce' );
		$array[ 'textarea' ] =  __( 'Textarea', 'text-attributes-for-woocommerce' );
		return $array ;
	}

	/**
	 * Display attribute input code.
	 *
	 * @since  	1.0.0
	 * @access  public
	 */
	public function show_attribute_input( $attribute_taxonomy, $i, $attribute ) {
		switch( $attribute_taxonomy->attribute_type ) {
			case 'text': include 'partials/inputs/text.php'; break;
			case 'number': include 'partials/inputs/number.php'; break; 
			case 'textarea': include 'partials/inputs/textarea.php'; break; 
		}
	}

	/**
	 * Return the first attribute value from an array.
	 *
	 * @since  	1.0.0
	 * @access  private
	 * @return  string  Attribute value
	 */
	private function get_attribute_value( $attribute ) {
		return esc_attr( wp_list_pluck( $attribute->get_terms(), 'name' )[ 0 ] );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @access  public
	 */
	public function enqueue_scripts($hook) {
		if ( $hook === 'post.php' ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/text-attributes-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );
		}
	}
}
