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
global $wp_version;
?>
<div class="wrap about__container">

	<div class="about__header" style="padding-top: 2em">
		<?php if ( version_compare( $wp_version, 5.4 ) !== -1 ) { ?>
			<div class="ic-welcome-bg">
			<?php } ?>
			<div class="about__header-text">
				That's it. Enjoy sales and beauty!
			</div>

			<div class="about__header-title">
				<p>
					<?php echo IC_CATALOG_PLUGIN_NAME ?>
					<span><?php echo IC_EPC_VERSION ?></span>
				</p>
			</div>
			<?php if ( version_compare( $wp_version, 5.4 ) !== -1 ) { ?>
			</div>
		<?php } ?>

		<nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Secondary menu' ); ?>">
			<?php
			$getting_started = '';
			$whats_new		 = '';
			if ( empty( $_GET[ 'tab' ] ) ) {
				$getting_started = ' nav-tab-active';
			} elseif ( $_GET[ 'tab' ] === 'new' ) {
				$whats_new = ' nav-tab-active';
			}
			?>
			<a href="<?php echo admin_url( 'edit.php?post_type=al_product&page=implecode_welcome' ) ?>" class="nav-tab<?php echo $getting_started ?>" aria-current="page"><?php _e( 'Getting Started', 'ecommerce-product-catalog' ) ?></a>
			<a href="<?php echo admin_url( 'edit.php?post_type=al_product&page=implecode_welcome&tab=new' ) ?>" class="nav-tab<?php echo $whats_new ?>"><?php _e( 'What&#8217;s New', 'ecommerce-product-catalog' ) ?></a>
			<a href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php' ) ?>" class="nav-tab"><?php _e( 'Settings', 'ecommerce-product-catalog' ); ?></a>
			<a href="<?php echo admin_url( 'edit.php?post_type=al_product&page=extensions.php' ) ?>" class="nav-tab"><?php _e( 'Add-ons & Integrations', 'ecommerce-product-catalog' ); ?></a>
			<a href="<?php echo admin_url( 'edit.php?post_type=al_product&page=extensions.php&tab=help' ) ?>" class="nav-tab"><?php _e( 'Help', 'ecommerce-product-catalog' ); ?></a>
		</nav>
	</div>

	<?php
	if ( !empty( $getting_started ) ) {
		if ( !empty( $_GET[ 'selected_mode' ] ) ) {
			require_once(AL_BASE_PATH . '/includes/welcome/mode-selected.php' );
		} else {
			require_once(AL_BASE_PATH . '/includes/welcome/start.php' );
		}
	} elseif ( !empty( $whats_new ) ) {
		require_once(AL_BASE_PATH . '/includes/welcome/new.php' );
	}
	?>
	<hr />

	<div class="return-to-dashboard">
		<a href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php' ) ?>"><?php _e( 'Go to settings', 'ecommerce-product-catalog' ) ?></a>
	</div>
</div>
<?php
