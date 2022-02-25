<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Backwards compatibility functions
 *
 * @created May 13, 2015
 * @package woocommerce-catalog-booster/functions
 */

add_action( 'admin_notices', 'ic_woocat_notices' );

function ic_woocat_notices() {
	?>
	<div id="ic_woocat_message" class="error woocommerce-missing">
		<p><?php _e( 'Without WooCommerce installed Catalog Booster has no effect on your site and can be deactivated.', 'catalog-booster-for-woocommerce' ) ?></p>
	</div>
	<?php
}
