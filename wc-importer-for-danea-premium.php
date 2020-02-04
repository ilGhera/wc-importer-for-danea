<?php
/**
 * Plugin Name: WC Importer for Danea - Premium
 * Plugin URI: https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/
 * Description: If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Importer for Danea - Premium!
 * You'll be able to import suppliers, clients and products.
 * Author: ilGhera
 * Version: 1.2.0
 * Author URI: https://ilghera.com
 * Requires at least: 4.0
 * Tested up to: 5.3
 * WC tested up to: 3
 * Text Domain: wcifd
 */


/*Evito accesso diretto*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Attivazione plugin
 */
function load_wc_importer_for_danea_premium() {

	/*Function check */
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	}

	/*Disattiva il plugin free se presente*/
	if ( is_plugin_active( 'wc-importer-for-danea/wc-importer-for-danea.php' ) || function_exists( 'load_wc_importer_for_danea' ) ) {
		deactivate_plugins( 'wc-importer-for-danea/wc-importer-for-danea.php' );
		remove_action( 'plugins_loaded', 'load_wc_importer_for_danea' );
		wp_redirect( admin_url( 'plugins.php?plugin_status=all&paged=1&s' ) );

	}

	/*Dichiarazioni costanti*/
	define( 'WCIFD_DIR', plugin_dir_path( __FILE__ ) );
	define( 'WCIFD_URI', plugin_dir_url( __FILE__ ) );
	define( 'WCIFD_INCLUDES', WCIFD_DIR . 'includes/' );
	define( 'WCIFD_ADMIN', WCIFD_DIR . 'admin/' );

	/*Internationalization*/
	load_plugin_textdomain( 'wcifd', false, basename( dirname( __FILE__ ) ) . '/languages' );

	/*Richiamo file necessari*/
	require( WCIFD_ADMIN . 'wcifd-admin.php' );
	require( WCIFD_INCLUDES . 'wcifd-functions.php' );
	require( WCIFD_INCLUDES . 'wcifd-products-images.php' );
	require( WCIFD_INCLUDES . 'wcifd-single-product-image.php' );
	require( WCIFD_INCLUDES . 'wcifd-orphan-images.php' );
	require( WCIFD_INCLUDES . 'wcifd-import-users.php' );
	require( WCIFD_INCLUDES . 'wcifd-import-products.php' );
	require( WCIFD_INCLUDES . 'wcifd-catalog-update.php' );
	require( WCIFD_INCLUDES . 'wcifd-import-single-product.php' );
	require( WCIFD_INCLUDES . 'wcifd-product-meta-lookup.php' );
	require( WCIFD_INCLUDES . 'wcifd-delete-single-product.php' );
	require( WCIFD_INCLUDES . 'wcifd-import-orders.php' );

}
add_action( 'plugins_loaded', 'load_wc_importer_for_danea_premium', 1 );


/*Richiamo "Update-Checker"*/
require( plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php' );
$wcifdUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://www.ilghera.com/wp-update-server-2/?action=get_metadata&slug=wc-importer-for-danea-premium',
	__FILE__,
	'wc-importer-for-danea-premium'
);

$wcifdUpdateChecker->addQueryArgFilter( 'wcifd_secure_update_check' );
function wcifd_secure_update_check( $queryArgs ) {
	$key = base64_encode( get_option( 'wcifd-premium-key' ) );

	if ( $key ) {
		$queryArgs['premium-key'] = $key;
	}
	return $queryArgs;
}
