<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require( PSCW_SIZE_CHART_PLUGIN_DIR . 'includes/function.php' );
if ( class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Function' ) ) {
	new PSCW_PRODUCT_SIZE_CHART_F_WOO_Function();
}
require( PSCW_SIZE_CHART_PLUGIN_DIR . 'admin/admin.php' );
if ( class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Admin' ) ) {
	new PSCW_PRODUCT_SIZE_CHART_F_WOO_Admin();
}
require( PSCW_SIZE_CHART_PLUGIN_DIR . 'admin/setting.php' );
if ( class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Setting' ) ) {
	new PSCW_PRODUCT_SIZE_CHART_F_WOO_Setting();
}
require( PSCW_SIZE_CHART_PLUGIN_DIR . 'admin/design-new.php' );
if ( class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Design' ) ) {
	new PSCW_PRODUCT_SIZE_CHART_F_WOO_Design();
}
require( PSCW_SIZE_CHART_PLUGIN_DIR . 'front-end/front-end.php' );
if ( class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Front_end' ) ) {
	new PSCW_PRODUCT_SIZE_CHART_F_WOO_Front_end();
}
require( PSCW_SIZE_CHART_PLUGIN_DIR . 'includes/support.php' );
if ( class_exists( 'VillaTheme_Support' ) ) {
	new VillaTheme_Support(
		array(
			'support'   => 'https://wordpress.org/support/plugin/product-size-chart-for-woo/',
			'docs'      => 'https://docs.villatheme.com/product-size-chart-for-woocommerce/',
			'review'    => 'https://wordpress.org/support/plugin/product-size-chart-for-woo/reviews/?rate=5#rate-response',
			'css'       => PSCW_SIZE_CHART_PLUGIN_URL . "css/",
			'image'     => "",
			'slug'      => 'pscw-size-chart-setting',
			'menu_slug' => 'pscw-size-chart',
			'version'   => PSCW_SIZE_CHART_VERSION
		)
	);
}
