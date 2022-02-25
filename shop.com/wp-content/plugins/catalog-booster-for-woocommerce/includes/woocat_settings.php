<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages scheduled_disc settings
 *
 * Here all scheduled_disc settings are defined and managed.
 *
 * @version		1.0.0
 * @package		implecode-scheduled_disc-getaway/includes
 * @author 		Norbert Dreszer
 */
add_action( 'admin_menu', 'ic_woocat_settings_menu', 99 );

function ic_woocat_settings_menu() {
	add_submenu_page( 'woocommerce', __( 'Catalog Booster', 'catalog-booster-for-woocommerce' ), __( 'Catalog Booster', 'catalog-booster-for-woocommerce' ), 'manage_woocommerce', 'ic-catalog-mode', 'ic_woocat_settings_page' );
}

add_action( 'admin_init', 'ic_woocat_settings_register' );

function ic_woocat_settings_register() {
	register_setting( 'ic_woocat', 'ic_woocat' );
}

add_action( 'admin_print_footer_scripts', 'ic_woocat_hide_tooltips' );

function ic_woocat_hide_tooltips() {
	if ( !empty( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wc-admin' ) {
		implecode_wp_tooltip_hide( 'toplevel_page_woocommerce' );
	}
}

function ic_woocat_settings_page() {
	implecode_wp_tooltip_hide( 'toplevel_page_woocommerce' );
	implecode_wp_tooltip_hide( 'toplevel_page_woocommerce .wp-submenu li:last' );
	?>
	<div id="implecode_settings" class="wrap">
		<h2><?php _e( 'Settings', 'catalog-booster-for-woocommerce' ) ?> - <?php _e( 'WooCommerce Catalog Booster', 'catalog-booster-for-woocommerce' ) ?></h2>
		<nav class="nav-tab-wrapper">
			<?php do_action( 'woocat-settings-menu' ); ?>
		</nav>
		<div class="settings-wrapper" style="clear:both;">
			<div class="setting-content">
				<?php
				$tab		 = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
				$ic_woocat	 = ic_woocat_settings();
				?>
				<form method = "post" action = "options.php">
					<?php
					settings_fields( 'ic_woocat' );
					foreach ( $ic_woocat as $tab_key => $settings_cat ) {
						if ( $tab_key != $tab ) {
							foreach ( $settings_cat as $key => $value ) {
								echo '<input type="hidden" name="ic_woocat[' . $tab_key . '][' . $key . ']" value="' . $value . '">';
							}
						}
					}
					?>
					<?php
					if ( empty( $tab ) || $tab == 'general' ) {
						?>
						<h2><?php _e( 'General Options', 'catalog-booster-for-woocommerce' ) ?></h2>

						<?php
						echo '<table>';
						implecode_settings_checkbox( __( 'Disable Shopping Cart', 'catalog-booster-for-woocommerce' ), 'ic_woocat[general][disable_cart]', $ic_woocat[ 'general' ][ 'disable_cart' ] );
						implecode_settings_checkbox( __( 'Disable Price', 'catalog-booster-for-woocommerce' ), 'ic_woocat[general][disable_price]', $ic_woocat[ 'general' ][ 'disable_price' ] );
						implecode_settings_checkbox( __( 'Disable Rating', 'catalog-booster-for-woocommerce' ), 'ic_woocat[general][disable_rating]', $ic_woocat[ 'general' ][ 'disable_rating' ] );
						implecode_settings_checkbox( __( 'Disable Reviews', 'catalog-booster-for-woocommerce' ), 'ic_woocat[general][disable_reviews]', $ic_woocat[ 'general' ][ 'disable_reviews' ] );
						implecode_settings_checkbox( __( 'Apply also in dashboard', 'catalog-booster-for-woocommerce' ), 'ic_woocat[general][in_dashboard]', $ic_woocat[ 'general' ][ 'in_dashboard' ] );
						if ( class_exists( 'ic_shopping_ecommerce_cart' ) ) {
							implecode_settings_radio( __( 'Cart Mode', 'catalog-booster-for-woocommerce' ), 'ic_woocat[general][cart_mode]', $ic_woocat[ 'general' ][ 'cart_mode' ], array( 'woo' => 'WooCommerce', 'epc' => 'eCommerce Product Catalog' ) );
						}
						echo '</table>';
					}
					?>
					<?php
					do_action( 'ic_woocat_settings_content', $tab, $ic_woocat );
					?>

					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'catalog-booster-for-woocommerce' ); ?>" />
					</p>
				</form>
			</div>
		</div>
		<div style="clear:both; height: 50px;"></div>
		<div class="plugin-logo">
			<a href="https://implecode.com/#cam=woocat-catalog-settings-link&key=logo-link"><img class="en" src="<?php echo IC_WOOCAT_BASE_URL . 'img/implecode.png'; ?>" width="282px" alt="impleCode"/></a>
		</div>
	</div>

	<?php
}

function ic_woocat_settings() {
	$ic_woocat = get_option( 'ic_woocat', array() );

	$ic_woocat[ 'general' ][ 'disable_cart' ]	 = isset( $ic_woocat[ 'general' ][ 'disable_cart' ] ) ? $ic_woocat[ 'general' ][ 'disable_cart' ] : '';
	$ic_woocat[ 'general' ][ 'disable_price' ]	 = isset( $ic_woocat[ 'general' ][ 'disable_price' ] ) ? $ic_woocat[ 'general' ][ 'disable_price' ] : '';
	$ic_woocat[ 'general' ][ 'disable_rating' ]	 = isset( $ic_woocat[ 'general' ][ 'disable_rating' ] ) ? $ic_woocat[ 'general' ][ 'disable_rating' ] : '';
	$ic_woocat[ 'general' ][ 'disable_reviews' ] = isset( $ic_woocat[ 'general' ][ 'disable_reviews' ] ) ? $ic_woocat[ 'general' ][ 'disable_reviews' ] : '';
	$ic_woocat[ 'general' ][ 'in_dashboard' ]	 = isset( $ic_woocat[ 'general' ][ 'in_dashboard' ] ) ? $ic_woocat[ 'general' ][ 'in_dashboard' ] : '';
	$ic_woocat[ 'general' ][ 'cart_mode' ]		 = isset( $ic_woocat[ 'general' ][ 'cart_mode' ] ) ? $ic_woocat[ 'general' ][ 'cart_mode' ] : 'epc';

	$ic_woocat[ 'button' ][ 'enable' ]		 = isset( $ic_woocat[ 'button' ][ 'enable' ] ) ? $ic_woocat[ 'button' ][ 'enable' ] : '';
	$ic_woocat[ 'button' ][ 'url' ]			 = isset( $ic_woocat[ 'button' ][ 'url' ] ) ? $ic_woocat[ 'button' ][ 'url' ] : '';
	$ic_woocat[ 'button' ][ 'individual' ]	 = isset( $ic_woocat[ 'button' ][ 'individual' ] ) ? $ic_woocat[ 'button' ][ 'individual' ] : '';
	$ic_woocat[ 'button' ][ 'use_default' ]	 = isset( $ic_woocat[ 'button' ][ 'use_default' ] ) ? $ic_woocat[ 'button' ][ 'use_default' ] : '';
	$ic_woocat[ 'button' ][ 'label' ]		 = !empty( $ic_woocat[ 'button' ][ 'label' ] ) ? $ic_woocat[ 'button' ][ 'label' ] : __( 'Read More', 'catalog-booster-for-woocommerce' );

	$ic_woocat[ 'catalog' ][ 'enable' ] = !empty( $ic_woocat[ 'catalog' ][ 'enable' ] ) ? $ic_woocat[ 'catalog' ][ 'enable' ] : '';

	return $ic_woocat;
}

add_action( 'woocat-settings-menu', 'ic_woocat_general_menu' );
if ( function_exists( 'add_product_catalog_upgrade_url' ) ) {
	add_action( 'woocat-settings-menu', 'add_product_catalog_upgrade_url', 99 );
}

function ic_woocat_general_menu() {
	$tab		 = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
	$general	 = '';
	$listing	 = '';
	$button		 = '';
	$page		 = '';
	$catalog	 = '';
	$extensions	 = '';
	$help		 = '';
	if ( empty( $tab ) || $tab == 'general' ) {
		$general = 'nav-tab-active';
	} else if ( $tab == 'button' ) {
		$button = 'nav-tab-active';
	} else if ( $tab == 'product_page' ) {
		$page = 'nav-tab-active';
	} else if ( $tab == 'listing' ) {
		$listing = 'nav-tab-active';
	} else if ( $tab == 'woocat_extensions' ) {
		$extensions = 'nav-tab-active';
	} else if ( $tab == 'woocat_help' ) {
		$help = 'nav-tab-active';
	} else {
		$catalog = 'nav-tab-active';
	}
	?>
	<a id="general-settings" class="nav-tab <?php echo $general ?>" href="<?php echo admin_url( 'admin.php?page=ic-catalog-mode&tab=general' ) ?>"><?php _e( 'General', 'catalog-booster-for-woocommerce' ); ?></a>
	<a id="general-settings" class="nav-tab <?php echo $button ?>" href="<?php echo admin_url( 'admin.php?page=ic-catalog-mode&tab=button' ) ?>"><?php _e( 'Button', 'catalog-booster-for-woocommerce' ); ?></a>
	<a id="general-settings" class="nav-tab <?php echo $listing ?>" href="<?php echo admin_url( 'admin.php?page=ic-catalog-mode&tab=listing&submenu=archive-design' ) ?>"><?php _e( 'Product Listing', 'catalog-booster-for-woocommerce' ); ?></a>
	<a id="general-settings" class="nav-tab <?php echo $page ?>" href="<?php echo admin_url( 'admin.php?page=ic-catalog-mode&tab=product_page' ) ?>"><?php _e( 'Product Page', 'catalog-booster-for-woocommerce' ); ?></a>
	<a id="general-settings" class="nav-tab <?php echo $catalog ?>" href="<?php echo admin_url( 'admin.php?page=ic-catalog-mode&tab=catalog' ) ?>"><?php _e( 'Additional Catalog', 'catalog-booster-for-woocommerce' ); ?></a>
	<?php if ( function_exists( 'start_implecode_install' ) ) { ?>
		<a id="woocatextensions-settings" class="nav-tab <?php echo $extensions ?>" href="<?php echo admin_url( 'admin.php?page=ic-catalog-mode&tab=woocat_extensions' ) ?>"><?php _e( 'Addons & Integrations', 'catalog-booster-for-woocommerce' ); ?></a>
		<?php
	}
	?>
	<a id="woocathelp-settings" class="nav-tab <?php echo $help ?>" href="<?php echo admin_url( 'admin.php?page=ic-catalog-mode&tab=woocat_help' ) ?>"><?php _e( 'Help', 'catalog-booster-for-woocommerce' ); ?></a>
	<?php
}

add_action( 'ic_woocat_settings_content', 'ic_woocat_product_listing_settings' );

function ic_woocat_product_listing_settings( $tab ) {
	if ( $tab == 'listing' ) {
		?>
		<h2><?php _e( 'Product Listing Templates', 'catalog-booster-for-woocommerce' ) ?></h2>
		<?php
		if ( !function_exists( 'impleCode_EPC' ) ) {
			implecode_info( sprintf( __( '%1$seCommerce Product Catalog%2$s is required in order to activate enhanced product listing views. %1$sInstall it for free%2$s from WordPress repository.', 'catalog-booster-for-woocommerce' ), '<a href="' . admin_url( 'plugin-install.php?s=ecommerce+product+catalog+by+implecode&tab=search&type=term' ) . '">', '</a>' ) );
			?>
			<h2>Here is what you will be able to enable for all or selected WooCommerce products:</h2>
			<h3>Modern Grid</h3>
			<img src="https://ps.w.org/ecommerce-product-catalog/assets/screenshot-1.png" />
			<h3>Classic Grid</h3>
			<img src="https://ps.w.org/ecommerce-product-catalog/assets/screenshot-4.png" />
			<h3>Classic List</h3>
			<img src="https://ps.w.org/ecommerce-product-catalog/assets/screenshot-5.png" />
			<p>and many more.</p>
			<?php
		} else {
			settings_fields( 'product_design' );
			ic_listing_design_settings();
		}
	}
}

add_action( 'ic_woocat_settings_content', 'ic_woocat_product_page_settings' );

function ic_woocat_product_page_settings( $tab ) {
	if ( $tab == 'product_page' ) {
		if ( !function_exists( 'impleCode_EPC' ) ) {
			?>
			<h2><?php _e( 'Product Page Templates', 'catalog-booster-for-woocommerce' ) ?></h2>
			<?php
			implecode_info( sprintf( __( '%1$seCommerce Product Catalog%2$s is required in order to activate enhanced product page templates. %1$sInstall it for free%2$s from WordPress repository.', 'catalog-booster-for-woocommerce' ), '<a href="' . admin_url( 'plugin-install.php?s=ecommerce+product+catalog+by+implecode&tab=search&type=term' ) . '">', '</a>' ) );
			?>
			<h2>Here is what you will be able to enable for all WooCommerce products:</h2>
			<h3>Tabbed Page</h3>
			<img src="https://ps.w.org/ecommerce-product-catalog/assets/screenshot-2.png" />
			<h3>Simple Page</h3>
			<img src="https://ps.w.org/ecommerce-product-catalog/assets/screenshot-3.png" />
			<?php
		} else {
			settings_fields( 'single_design' );
			ic_product_page_design_settings();
		}
	}
}

add_action( 'ic_product_gallery_settings', 'ic_woocat_gallery_settings' );

function ic_woocat_gallery_settings( $single_options ) {
	implecode_settings_checkbox( __( 'Use WooCommerce Gallery', 'catalog-booster-for-woocommerce' ), 'ic_woocat_woo_gallery', ic_woocat_woo_gallery_enabled(), 1, __( 'It will replace the catalog gallery with default WooCommerce gallery. Lightbox gallery should be disabled with this enabled.', 'catalog-booster-for-woocommerce' ) );
}

function ic_woocat_woo_gallery_enabled() {
	$enabled = get_option( 'ic_woocat_woo_gallery', 0 );
	return $enabled;
}

add_action( 'ic_woocat_settings_content', 'ic_woocat_button_settings', 10, 2 );

function ic_woocat_button_settings( $tab, $ic_woocat ) {
	if ( $tab == 'button' ) {
		?>
		<h2><?php _e( 'Product Button Options', 'catalog-booster-for-woocommerce' ) ?></h2>
		<?php
		echo '<table>';
		implecode_settings_checkbox( __( 'Enable catalog button', 'catalog-booster-for-woocommerce' ), 'ic_woocat[button][enable]', $ic_woocat[ 'button' ][ 'enable' ] );
		implecode_settings_text( __( 'Button Label', 'catalog-booster-for-woocommerce' ), 'ic_woocat[button][label]', $ic_woocat[ 'button' ][ 'label' ] );
		implecode_settings_text( __( 'Default button URL', 'catalog-booster-for-woocommerce' ), 'ic_woocat[button][url]', $ic_woocat[ 'button' ][ 'url' ] );
		implecode_settings_checkbox( __( 'Unique URL for each Product', 'catalog-booster-for-woocommerce' ), 'ic_woocat[button][individual]', $ic_woocat[ 'button' ][ 'individual' ] );
		implecode_settings_checkbox( __( 'Use default if empty', 'catalog-booster-for-woocommerce' ), 'ic_woocat[button][use_default]', $ic_woocat[ 'button' ][ 'use_default' ] );
		echo '</table>';
	}
}

add_action( 'ic_woocat_settings_content', 'ic_woocat_product_catalog_settings', 10, 2 );

function ic_woocat_product_catalog_settings( $tab, $ic_woocat ) {
	if ( $tab == 'catalog' ) {
		?>
		<h2><?php _e( 'Additional Catalog', 'catalog-booster-for-woocommerce' ) ?></h2>
		<?php
		if ( !function_exists( 'impleCode_EPC' ) ) {
			implecode_info( sprintf( __( '%1$seCommerce Product Catalog%2$s is required in order to activate additional catalog. %1$sInstall it for free%2$s from WordPress repository.', 'catalog-booster-for-woocommerce' ), '<a href="' . admin_url( 'plugin-install.php?s=ecommerce+product+catalog+by+implecode&tab=search&type=term' ) . '">', '</a>' ) );
			?>
			<p><?php _e( 'You will be able to create a separate catalog outside of WooCommerce.', 'catalog-booster-for-woocommerce' ) ?></p>
			<?php
		} else {
			if ( empty( $ic_woocat[ 'catalog' ][ 'enable' ] ) ) {
				implecode_info( __( 'If you enable this option you will be able to create a separate catalog outside of WooCommerce. This will also enable more catalog settings.', 'catalog-booster-for-woocommerce' ) );
			}
			echo '<table>';
			implecode_settings_checkbox( __( 'Enable Additional Catalog', 'catalog-booster-for-woocommerce' ), 'ic_woocat[catalog][enable]', $ic_woocat[ 'catalog' ][ 'enable' ] );
			if ( !empty( $ic_woocat[ 'catalog' ][ 'enable' ] ) ) {
				echo '<tr>';
				echo '<td colspan="2" style="text-align:center"><a class="button" href="' . admin_url( 'edit.php?post_type=al_product&page=product-settings.php' ) . '">' . __( 'Catalog Settings', 'catalog-booster-for-woocommerce' ) . '</a></td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}
}

add_action( 'ic_woocat_settings_content', 'ic_woocat_extensions_settings', 10, 2 );

function ic_woocat_extensions_settings( $tab, $ic_woocat ) {
	if ( $tab == 'woocat_extensions' ) {
		if ( !function_exists( 'start_implecode_install' ) ) {
			return;
		}
		?>
		<div id="implecode_settings" class="wrap">
			<?php do_action( 'ic_cat_extensions_page_start' ) ?>
			<h3><?php _e( 'All premium extensions come with premium support provided by dev team.<br>Feel free to contact impleCode for configuration help, troubleshooting, installation assistance and any other plugin support at any time!', 'ecommerce-product-catalog' ) ?></h3>
			<div class="extension-list">
				<?php
				start_implecode_install();
				start_free_implecode_install();
				if ( false === ($extensions = get_site_transient( 'implecode_extensions_data' )) ) {
					$extensions_remote_url	 = apply_filters( 'ic_extensions_remote_url', 'provide_extensions' );
					$extensions				 = wp_remote_get( 'https://app.implecode.com/index.php?' . $extensions_remote_url );
					if ( !is_wp_error( $extensions ) && 200 == wp_remote_retrieve_response_code( $extensions ) ) {
						$extensions = json_decode( wp_remote_retrieve_body( $extensions ), true );
						if ( $extensions ) {
							set_site_transient( 'implecode_extensions_data', $extensions, WEEK_IN_SECONDS );
						}
					} else {
						$extensions = implecode_extensions();
					}
				}
				$all_ic_plugins = array();
				if ( function_exists( 'get_implecode_active_plugins' ) ) {
					$all_ic_plugins = get_implecode_active_plugins();
				}
				$all_ic_plugins = array_merge( get_implecode_active_free_plugins(), $all_ic_plugins );

				$not_active_ic_plugins = get_implecode_not_active_plugins();

				do_action( 'ic_before_extensions_list', $tab );
				$extensions			 = apply_filters( 'ic_cat_extensions', $extensions );
				$count				 = 1;
				$extensions_by_type	 = array();
				$number				 = 2;
				foreach ( $extensions as $extension ) {
					$extension[ 'type' ] = isset( $extension[ 'type' ] ) ? $extension[ 'type' ] : 'premium';
					if ( $count % $number == 0 && !empty( $extensions_by_type ) ) {
						if ( !empty( $extensions_by_type[ 0 ] ) ) {
							echo extension_box( $extensions_by_type[ 0 ], $all_ic_plugins, $not_active_ic_plugins );
							unset( $extensions_by_type[ 0 ] );
							$number++;
						}
						if ( !empty( $extensions_by_type ) ) {
							$extensions_by_type = array_values( $extensions_by_type );
						}
						$count++;
					} else if ( $extension[ 'type' ] == 'free' ) {
						$extensions_by_type[] = $extension;
						continue;
					}
					echo extension_box( $extension, $all_ic_plugins, $not_active_ic_plugins );
					$count++;
				}
				ic_show_affiliate_content();
				?>
			</div>

			<div style="clear:both; height: 50px;"></div>
			<div class="plugin-logo">
				<a href="https://implecode.com/#cam=catalog-settings-link&key=logo-link"><img class="en" src="<?php echo IC_WOOCAT_BASE_URL . 'img/implecode.png'; ?>" width="282px" alt="impleCode"/></a>
			</div>
		</div>
		<?php
	}
}

add_action( 'ic_woocat_settings_content', 'ic_woocat_help_settings', 10, 2 );

function ic_woocat_help_settings( $tab, $ic_woocat ) {
	if ( $tab == 'woocat_help' ) {
		?>
		</form>
		<style>#implecode_settings .settings-wrapper p.submit {display: none}</style>
		<div id="implecode_settings" class="wrap">
			<?php
			do_action( 'ic_extensions_page_help_top' );
			?>
			<div class="help">
				<?php
				do_action( 'ic_extensions_page_help_text' );
				?>
				<h3><?php _e( 'Getting Started', 'ecommerce-product-catalog' ) ?></h3>
				<p>With WooCommerce Catalog Booster you can:</p>
				<ol>
					<li>Disable Price</li>
					<li>Disable Shopping Cart</li>
					<li>Add a custom link button for each product</li>
					<li>Disable rating & reviews</li>
				</ol>
				<p>If you also install free <a href="<?php echo admin_url( 'plugin-install.php?s=implecode+eCommerce+Product+Catalog+Plugin+for+WordPress+powerful+and+free&tab=search&type=term' ) ?>">eCommerce Product Catalog</a> you will be able to:</p>
				<ol>
					<li>Completely change the design of product list, category pages and individual product pages</li>
					<li>Add new user experience for product search and filtering</li>
					<li>Create a complete separate catalog which will work even if you disable WooCommerce</li>
					<li>Add inquiry buttons, quote cart or even a completely new user shopping experience</li>
					<li>Many more countless possibilities with <a target="_blank" href="https://implecode.com/wordpress/plugins/#cam=woocat-extensions-help&key=extensions">impleCode extensions</a></li>
				</ol>
				<h3><?php _e( 'How to get help?', 'ecommerce-product-catalog' ) ?></h3>
				<p>The developers provide both free and premium support:</p>
				<a class="button-secondary" target="_blank" href="https://wordpress.org/support/plugin/catalog-booster-for-woocommerce/">Free Support Forum</a>
				<a class="button-secondary" target="_blank" href="https://implecode.com/support/#cam=woocat-extensions-help&key=support">Premium Support Service</a>
				<?php if ( function_exists( 'start_implecode_install' ) ) { ?>
					<h3><?php _e( 'How to Install the extension?', 'ecommerce-product-catalog' ) ?></h3>
					<ol>
						<li>Go to the <a href="<?php echo admin_url( 'admin.php?page=ic-catalog-mode&tab=woocat_extensions' ) ?>">extensions page</a></li>
						<li>Click the "Get your key" button on the extension that you want to install;</li>
						<li>You will be redirected to the impleCode website. Read the extension description, choose license type, click the Add to Cart button and fill the form;</li>
						<li>Your license key will be immediately sent to you by email provided in the previous step;</li>
						<li>Copy and Paste the license key to the license key field on the extension that you want to install;</li>
						<li>Click the install button and wait until the installation process is done. The installer will establish a secure connection with impleCode to get the installation files;</li>
						<li>Click the activation button;</li>
						<li>That's it. Enjoy!</li>
					</ol>
					<p>In case you prefer to install the extension manually you will get also the installation files in the customer panel. See <a href="https://implecode.com/wordpress/product-catalog/plugin-installation-guide/#cam=woocat-extensions-help&key=manual-installation#manual">manual installation guide</a> for this.</p>
					<p>Please see the <a href="https://implecode.com/faq/#cam=woocat-extensions-help&key=faq">FAQ</a> for additional information</p>
				<?php } ?>
			</div>
		</div>
		<?php
	}
}

add_action( 'product-settings-list', 'ic_woo_cat_epc_register_settings' );

function ic_woo_cat_epc_register_settings() {
	register_setting( 'product_design', 'ic_enable_listing_for_woo' );
	register_setting( 'single_design', 'ic_enable_page_for_woo' );
	register_setting( 'single_design', 'ic_woocat_woo_gallery' );
}

add_action( 'listing_design_settings_start', 'ic_woo_cat_epc_listing_settings' );

function ic_woo_cat_epc_listing_settings() {
	$woo_listing_enabled = ic_is_listing_for_woo_enabled();
	echo '<table>';
	implecode_settings_checkbox( __( 'Enable for WooCommerce', 'catalog-booster-for-woocommerce' ), 'ic_enable_listing_for_woo', $woo_listing_enabled );
	echo '</table>';
}

add_action( 'page_design_settings_start', 'ic_woo_cat_epc_page_settings' );

function ic_woo_cat_epc_page_settings() {
	$woo_page_enabled = ic_is_page_for_woo_enabled();
	echo '<table>';
	implecode_settings_checkbox( __( 'Enable for WooCommerce', 'catalog-booster-for-woocommerce' ), 'ic_enable_page_for_woo', $woo_page_enabled );
	echo '</table>';
}

function ic_is_listing_for_woo_enabled() {
	$enabled = get_option( 'ic_enable_listing_for_woo', 0 );
	return $enabled;
}

function ic_is_page_for_woo_enabled() {
	$enabled = get_option( 'ic_enable_page_for_woo', 0 );
	return $enabled;
}

add_filter( 'is_ic_catalog_admin_page', 'ic_woo_settings_admin_page' );

function ic_woo_settings_admin_page( $return ) {
	if ( is_admin() && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'ic-catalog-mode' ) {
		return true;
	}
	return $return;
}

add_filter( 'plugin_action_links_' . plugin_basename( IC_WOOCAT_MAIN_FILE ), 'ic_woo_cat_links' );

/**
 * Shows settings link on plugin list
 *
 * @param array $links
 * @return type
 */
function ic_woo_cat_links( $links ) {
	$links[] = '<a href="' . get_admin_url( null, 'admin.php?page=ic-catalog-mode' ) . '">Settings</a>';
	return array_reverse( $links );
}
