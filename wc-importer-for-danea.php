<?php
/**
 * Plugin Name: ilGhera WooCommerce Importer for Danea
 * Plugin URI: https://www.ilghera.com/product/woocommerce-importer-for-danea/
 * Description: If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need ilGhera WooCommerce Importer for Danea.
 * You'll be able to import suppliers, clients and products.
 * Author: ilGhera
 * Version: 1.3.2
 * Author URI: https://ilghera.com
 * Requires at least: 4.0
 * Tested up to: 6.6
 * WC tested up to: 9
 * Text Domain: wc-importer-for-danea
 *
 * @package wc-importer-for-danea
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
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	/*Dichiarazioni costanti*/
	define( 'WCIFD_DIR', plugin_dir_path( __FILE__ ) );
	define( 'WCIFD_URI', plugin_dir_url( __FILE__ ) );
	define( 'WCIFD_INCLUDES', WCIFD_DIR . 'includes/' );
	define( 'WCIFD_ADMIN', WCIFD_DIR . 'admin/' );
	define( 'WCIFD_VERSION', '1.3.1' );

	/*Internationalization*/
	load_plugin_textdomain( 'wc-importer-for-danea', false, basename( dirname( __FILE__ ) ) . '/languages' );

	/*Richiamo file necessari*/
	require_once WCIFD_ADMIN . 'wcifd-admin.php';
	require_once WCIFD_INCLUDES . 'wcifd-functions.php';
	require_once WCIFD_INCLUDES . 'wcifd-import-users.php';

}
add_action( 'plugins_loaded', 'load_wc_importer_for_danea', 100 );

