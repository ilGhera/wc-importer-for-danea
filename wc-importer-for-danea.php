<?php
/**
 * Plugin Name: WC Importer for Danea - Premium
 * Plugin URI: https://www.ilghera.com/product/woocommerce-importer-for-danea/
 * Description: If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Importer for Danea.
 * You'll be able to import suppliers, clients and products.
 * Author: ilGhera
 * Version: 1.1.0
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
function load_wc_importer_for_danea() {

	/*Function check */
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
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
add_action( 'plugins_loaded', 'load_wc_importer_for_danea', 100 );	
