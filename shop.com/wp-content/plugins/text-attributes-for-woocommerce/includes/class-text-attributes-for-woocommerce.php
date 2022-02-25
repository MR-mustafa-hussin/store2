<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since             1.0.0
 * @package           Zobnin_Text_Attributes_For_WooCommerce
 * @subpackage 				Zobnin_Text_Attributes_For_WooCommerce/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
  die;
}

class Zobnin_Text_Attributes_For_WooCommerce {

  /**
   * The loader that's responsible for maintaining and registering all hooks that power
   * the plugin.
   *
   * @since		1.0.0
   * @access	protected
   * @var			Zobnin_Text_Attributes_For_WooCommerce_Loader $loader Maintains and registers all hooks for the plugin.
   */
  protected $loader;

  /**
   * The unique identifier of this plugin.
   *
   * @since  	1.0.0
   * @access  protected
   * @var    	string $plugin_name The string used to uniquely identify this plugin.
   */
  protected $plugin_name;

  /**
   * The current version of the plugin.
   *
   * @since  	1.0.0
   * @access  protected
   * @var    	string $version The current version of the plugin.
   */
  protected $version;

  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the admin area.
   *
   * @param string $version
   *
   * @since  1.0.0
   */
  public function __construct( $version = '1.0.0' ) {
    $this->plugin_name = 'text-attributes-for-woocommerce';
    $this->version = $version;

    $this->load_dependencies();
    $this->set_locale();
    $this->define_admin_hooks();
  }

  /**
   * Load the required dependencies for this plugin.
   *
   * Include the following files that make up the plugin:
   *
   * - Zobnin_Text_Attributes_For_WooCommerce_Loader. Orchestrates the hooks of the plugin.
   * - Zobnin_Text_Attributes_For_WooCommerce_i18n. Defines internationalization functionality.
   * - Zobnin_Text_Attributes_For_WooCommerce_Admin. Defines all hooks for the admin area.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   *
   * @since  1.0.0
   * @access   private
   */
  private function load_dependencies() {
    // fire up the loader
    $this->loader = new Zobnin_Text_Attributes_For_WooCommerce_Loader();
  }

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the Zobnin_Text_Attributes_For_WooCommerce_i18n class in order to set the domain and to register the hook
   * with WordPress.
   *
   * @since  1.0.0
   * @access   private
   */
  private function set_locale() {
    $plugin_i18n = new Zobnin_Text_Attributes_For_WooCommerce_i18n();
    $this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );
  }

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

    $plugin_admin = Zobnin_Text_Attributes_For_WooCommerce_Admin::instance( $this->get_plugin_name(), $this->get_version() );
    
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    $this->loader->add_filter( 'product_attributes_type_selector', $plugin_admin, 'add_types' );
    $this->loader->add_action( 'woocommerce_product_option_terms', $plugin_admin, 'show_attribute_input', 10, 3 );    
  }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since  1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since   1.0.0
	 * @return  string  The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since   1.0.0
	 * @return  Zobnin_Text_Attributes_For_WooCommerce_Loader  Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since   1.0.0
	 * @return  string  The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
