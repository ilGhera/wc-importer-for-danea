<?php
/**
 * Plugin Name: WC Importer for Danea - Premium
 * Plugin URI: http://www.ilghera.com/product/woocommerce-importer-for-danea-premium/
 * Description: If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Importer for Danea - Premium!
 * You'll be able to import suppliers, clients and products.
 * Author: ilGhera
 * Version: 0.9.7
 * Author URI: http://ilghera.com 
 * Requires at least: 4.0
 * Tested up to: 4.8
 */


//NO DIRECT ACCESS
if ( !defined( 'ABSPATH' ) ) exit;


//LOAD THE PLUGIN
function load_wc_importer_for_danea_premium() {

	//FUNCTION CHECK 
	if ( !function_exists( 'is_plugin_active' ) ) {
    	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
 	}

 	//OFF THE FREE ONE
	if( is_plugin_active('wc-importer-for-danea/wc-importer-for-danea.php') || function_exists('load_wc_importer_for_danea') ) {
		deactivate_plugins('wc-importer-for-danea/wc-importer-for-danea.php');
	    remove_action( 'plugins_loaded', 'load_wc_importer_for_danea' );
	    wp_redirect(admin_url('plugins.php?plugin_status=all&paged=1&s'));

	}

	//INTERNATIONALIZATION
	load_plugin_textdomain('wcifd', false, basename( dirname( __FILE__ ) ) . '/languages' );

	//RICHIAMO FILE NECESSARI
	include( plugin_dir_path( __FILE__ ) . 'includes/wcifd-admin-functions.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/wcifd-functions.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/wcifd-products-images.php');

}
add_action( 'plugins_loaded', 'load_wc_importer_for_danea_premium', 1 );	


//RICHIAMO "UPDATE-CHECKER"
require( plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php');
$wcifdUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://www.ilghera.com/wp-update-server-2/?action=get_metadata&slug=wc-importer-for-danea-premium',
    __FILE__,
    'wc-importer-for-danea-premium'
);

$wcifdUpdateChecker->addQueryArgFilter('wcifd_secure_update_check');
function wcifd_secure_update_check($queryArgs) {
    $key = base64_encode( get_option('wcifd-premium-key') );

    if($key) {
        $queryArgs['premium-key'] = $key;
    }
    return $queryArgs;
}