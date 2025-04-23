<?php
/**
 * Plugin Name: WC Importer for Danea - Premium
 * Plugin URI: https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/
 * Description: If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need ilGhera WooCommerce Importer for Danea - Premium!
 * You'll be able to import suppliers, clients and products.
 * Version: 1.6.4
 * Requires at least: 4.0
 * Tested up to: 6.4
 * WC tested up to: 8
 * Author: ilGhera
 * Author URI: https://ilghera.com
 * Text Domain: wc-importer-for-danea
 * Domain Path: /languages
 *
 * @package wc-importer-for-danea-premium
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin activation
 *
 * @return void
 */
function load_wc_importer_for_danea_premium() {

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	/* Deactivate the free version of the plugin */
	if ( function_exists( 'load_wc_importer_for_danea' ) ) {
		deactivate_plugins( 'wc-importer-for-danea/wc-importer-for-danea.php' );
		remove_action( 'plugins_loaded', 'load_wc_importer_for_danea' );
		wp_safe_redirect( admin_url( 'plugins.php?plugin_status=all&paged=1&s' ) );

	}

	/* Constant variables */
	define( 'WCIFD_DIR', plugin_dir_path( __FILE__ ) );
	define( 'WCIFD_URI', plugin_dir_url( __FILE__ ) );
	define( 'WCIFD_INCLUDES', WCIFD_DIR . 'includes/' );
	define( 'WCIFD_CLASSES', WCIFD_DIR . 'classes/' );
	define( 'WCIFD_ADMIN', WCIFD_DIR . 'admin/' );
	define( 'WCIFD_VERSION', '1.6.4' );

	/*Internationalization*/
	load_plugin_textdomain( 'wc-importer-for-danea', false, basename( dirname( __FILE__ ) ) . '/languages' );

	require_once WCIFD_DIR . 'libraries/action-scheduler/action-scheduler.php';
	require_once WCIFD_ADMIN . 'class-wcifd-admin.php';
	require_once WCIFD_ADMIN . 'ilghera-notice/class-ilghera-notice.php';
	require_once WCIFD_CLASSES . 'class-wcifd-functions.php';
	require_once WCIFD_INCLUDES . 'wcifd-delete-all-products.php';
	require_once WCIFD_CLASSES . 'class-wcifd-temporary-data.php';
	require_once WCIFD_INCLUDES . 'wcifd-catalog-update.php';
	require_once WCIFD_CLASSES . 'class-wcifd-import-products.php';
	require_once WCIFD_CLASSES . 'class-wcifd-import-single-product.php';
	require_once WCIFD_CLASSES . 'class-wcifd-product-meta-lookup.php';
	require_once WCIFD_CLASSES . 'class-wcifd-progress-bar.php';
	require_once WCIFD_INCLUDES . 'wcifd-products-images.php';
	require_once WCIFD_INCLUDES . 'wcifd-single-product-image.php';
	require_once WCIFD_INCLUDES . 'wcifd-orphan-images.php';
	require_once WCIFD_INCLUDES . 'wcifd-import-users.php';
	require_once WCIFD_INCLUDES . 'wcifd-delete-single-product.php';
	require_once WCIFD_INCLUDES . 'wcifd-import-orders.php';

}
add_action( 'after_setup_theme', 'load_wc_importer_for_danea_premium' );

/**
 * Plugin Update Checker
 *
 * @return void
 */
require plugin_dir_path( __FILE__ ) . 'vendor/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$wcifd_update_checker = PucFactory::buildUpdateChecker(
	'https://www.ilghera.com/wp-update-server-2/?action=get_metadata&slug=wc-importer-for-danea-premium',
	__FILE__,
	'wc-importer-for-danea-premium'
);

$wcifd_update_checker->addQueryArgFilter( 'wcifd_secure_update_check' );

/**
 * Use the premium key with PUC
 *
 * @param array $args the query args.
 *
 * @return array
 */
function wcifd_secure_update_check( $args ) {

	$key = base64_encode( get_option( 'wcifd-premium-key' ) );

	if ( $key ) {
		$args['premium-key'] = $key;
	}

	return $args;

}

