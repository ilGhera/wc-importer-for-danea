<?php
/**
 * Plugin Name: ilGhera Danea Importer for WooCommerce
 * Plugin URI: https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/
 * Description: If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software
 * you definitely need ilGhera Danea Importer for WooCommerce!
 * You'll be able to import client and suppliers width this free version, also products and orders with the premium one.
 * Version: 1.4.2
 * Requires at least: 6.0
 * Tested up to: 6.9
 * WC tested up to: 10
 * Author: ilGhera
 * Author URI: https://ilghera.com
 * Text Domain: wc-importer-for-danea
 * Domain Path: /languages
 *
 * @package wc-importer-for-danea
 */

/* Ensure WordPress is loaded. */
defined( 'ABSPATH' ) || exit;

/* Load Core Plugin Classes */
if ( ! defined( 'WCIFD_DIR' ) ) {
	define( 'WCIFD_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WCIFD_CLASSES' ) ) {
	define( 'WCIFD_CLASSES', WCIFD_DIR . 'classes/' );
}
if ( ! defined( 'WCIFD_ADMIN' ) ) {
	define( 'WCIFD_ADMIN', WCIFD_DIR . 'admin/' );
}
if ( ! defined( 'WCIFD_INCLUDES' ) ) {
	define( 'WCIFD_INCLUDES', WCIFD_DIR . 'includes/' );
}

/* Load all core class files that will be instantiated by the main plugin class. */
require_once WCIFD_ADMIN . 'class-wcifd-admin.php';
require_once WCIFD_CLASSES . 'class-wcifd-functions.php';
require_once WCIFD_CLASSES . 'class-wcifd-import-users.php';

/**
 * WCIFD_Plugin
 *
 * This is the main class for the ilGhera Danea Importer for WooCommerce plugin.
 * It handles the plugin's core functionalities, initialization, and manages other classes.
 * Implements a singleton pattern to ensure only one instance of the plugin runs.
 *
 * @since 1.4.0
 */
final class WCIFD_Plugin {

	/**
	 * Stores the single instance of the plugin class.
	 *
	 * @var WCIFD_Plugin|null
	 */
	private static $instance = null;

	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	private $version = '1.4.2';

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	private function __construct() {

		/* Define additional constants if needed, though most are defined globally above. */
		$this->define_remaining_constants();

		/* Set up all necessary WordPress hooks for plugin initialization. */
		$this->setup_hooks();
	}

	/**
	 * Gets the single instance of the plugin.
	 *
	 * @return WCIFD_Plugin
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Defines any remaining constants not defined globally.
	 *
	 * @return void
	 */
	private function define_remaining_constants() {

		if ( ! defined( 'WCIFD_URI' ) ) {
			define( 'WCIFD_URI', plugin_dir_url( __FILE__ ) );
		}
		if ( ! defined( 'WCIFD_VERSION' ) ) {
			define( 'WCIFD_VERSION', $this->version );
		}
	}

	/**
	 * Sets up all WordPress action and filter hooks.
	 *
	 * @return void
	 */
	private function setup_hooks() {

		/* Hook for early plugin loading. */
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 0 );

		/* Main plugin initialization hook. Runs later than plugins_loaded. */
		add_action( 'init', array( $this, 'on_init' ), PHP_INT_MIN );

		/* HPOS compatibility hook. */
		add_action( 'before_woocommerce_init', array( $this, 'hpos_compatibility' ) );
	}

	/**
	 * Method hooked to 'plugins_loaded'.
	 *
	 * @return void
	 */
	public function on_plugins_loaded() {

		/* Ensure 'is_plugin_active' function is available for activation logic. */
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
	}

	/**
	 * Method hooked to 'init'.
	 *
	 * @return void
	 */
	public function on_init() {

		/* Load plugin text domain for internationalization. */
		load_plugin_textdomain( 'wc-importer-for-danea', false, basename( dirname( __FILE__ ) ) . '/languages' );

		/* Instantiate core classes */
		new WCIFD_Admin();
		new WCIFD_Functions( true );
	}

	/**
	 * Declares compatibility with WooCommerce's High-Performance Order Storage (HPOS).
	 *
	 * @return void
	 */
	public function hpos_compatibility() {

		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
}

/* Plugin Initialization */
WCIFD_Plugin::get_instance();
