<?php
/**
 * Plugin Name: WC Importer for Danea
 * Plugin URI: http://www.ilghera.com/product/woocommerce-importer-for-danea/
 * Description: If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Importer for Danea.
 * You'll be able to import suppliers, clients and products.
 * Author: ilGhera
 * Version: 0.9.2
 * Author URI: http://ilghera.com 
 * Requires at least: 4.0
 * Tested up to: 4.7.3
 */


//NO DIRECT ACCESS
if ( !defined( 'ABSPATH' ) ) exit;


//LOAD THE PLUGIN
function load_wc_importer_for_danea() {

	//INTERNATIONALIZATION
	load_plugin_textdomain('wcifd', false, basename( dirname( __FILE__ ) ) . '/languages' );

	//GET REQUIRED FILES
	include( plugin_dir_path( __FILE__ ) . 'includes/wcifd-admin-functions.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/wcifd-functions.php');

}
add_action( 'plugins_loaded', 'load_wc_importer_for_danea', 100 );	