<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Admin' ) ) {
	class PSCW_PRODUCT_SIZE_CHART_F_WOO_Admin {
		public function __construct() {
			add_action( 'init', array( $this, 'init' ) );
		}

		public function init() {
			$this->load_plugin_textdomain();
		}

		public function load_plugin_textdomain() {
			$locale = determine_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'product-size-chart-for-woo' );

			unload_textdomain( 'product-size-chart-for-woo' );
			load_textdomain( 'product-size-chart-for-woo', WP_LANG_DIR . '/product-size-chart-for-woo/product-size-chart-for-woo-' . $locale . '.mo' );
			load_plugin_textdomain( 'product-size-chart-for-woo', false, plugin_basename( dirname( PSCW_SIZE_CHART_PLUGIN_FILE ) ) . '/languages' );
		}
	}
}