<?php
/**
 * Plugin Name: WC Importer for Danea - Premium
 * Plugin URI: https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/
 * Description: If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software
 * you definitely need ilGhera WooCommerce Importer for Danea - Premium!
 * You'll be able to import suppliers, clients and products.
 * Version: 1.7.3
 * Requires at least: 6.0
 * Tested up to: 6.8
 * WC tested up to: 9
 * Author: ilGhera
 * Author URI: https://ilghera.com
 * Text Domain: wc-importer-for-danea
 * Domain Path: /languages
 *
 * @package wc-importer-for-danea-premium
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
require_once WCIFD_CLASSES . 'class-wcifd-as-cleaner.php';
require_once WCIFD_ADMIN . 'class-wcifd-admin.php';
require_once WCIFD_ADMIN . 'ilghera-notice/class-ilghera-notice.php';
require_once WCIFD_CLASSES . 'class-wcifd-functions.php';
require_once WCIFD_CLASSES . 'class-wcifd-temporary-data.php';
require_once WCIFD_CLASSES . 'class-wcifd-catalog-update.php';
require_once WCIFD_CLASSES . 'class-wcifd-import-products.php';
require_once WCIFD_CLASSES . 'class-wcifd-import-single-product.php';
require_once WCIFD_CLASSES . 'class-wcifd-progress-bar.php';
require_once WCIFD_CLASSES . 'class-wcifd-orphan-images.php';
require_once WCIFD_CLASSES . 'class-wcifd-products-images.php';
require_once WCIFD_CLASSES . 'class-wcifd-single-product-image.php';
require_once WCIFD_CLASSES . 'class-wcifd-import-orders.php';
require_once WCIFD_CLASSES . 'class-wcifd-import-users.php';

/* Load the Action Scheduler library if not already loaded by WooCommerce or another plugin. */
require_once WCIFD_DIR . 'libraries/action-scheduler/action-scheduler.php';

/* Load the Plugin Update Checker library. */
require_once WCIFD_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';

/**
 * WCIFD_Plugin
 *
 * This is the main class for the WC Importer for Danea Premium plugin.
 * It handles the plugin's core functionalities, initialization, and manages other classes.
 * Implements a singleton pattern to ensure only one instance of the plugin runs.
 *
 * @since 1.7.0
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
	private $version = '1.7.3';

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

        /* Handle the single product delete event. */
        add_action( 'wcifd_delete_product_event', array( 'WCIFD_Catalog_Update', 'delete_single_product' ), 10, 1 );

		/* Setup the Plugin Update Checker. */
		$this->setup_update_checker();
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

		/* Deactivate the free version of the plugin if it's active. */
		if ( function_exists( 'load_wc_importer_for_danea' ) ) {
			deactivate_plugins( 'wc-importer-for-danea/wc-importer-for-danea.php' );
			remove_action( 'plugins_loaded', 'load_wc_importer_for_danea' );
			wp_safe_redirect( admin_url( 'plugins.php?plugin_status=all&paged=1&s' ) );
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
		new WCIFD_AS_Cleaner();
		new WCIFD_Admin();
		new WCIFD_Functions( true );
		new WCIFD_Temporary_Data( true );
		new WCIFD_Import_Products();
		new WCIFD_Import_Single_Product();
		new WCIFD_Progress_Bar();
		new WCIFD_Orphan_Images();
		new WCIFD_Single_Product_Image();
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

	/**
	 * Sets up the Plugin Update Checker.
	 *
	 * @return void
	 */
	private function setup_update_checker() {

		/* Use the PucFactory from the included library. */
		$wcifd_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			'https://www.ilghera.com/wp-update-server-2/?action=get_metadata&slug=wc-importer-for-danea-premium',
			__FILE__,
			'wc-importer-for-danea-premium'
		);

		/* Add a filter to pass the premium key during update checks. */
		$wcifd_update_checker->addQueryArgFilter( array( $this, 'secure_update_check' ) );
	}

	/**
	 * Adds the premium key to the update checker query arguments.
	 * This method is a callback for the PUC filter.
	 *
	 * @param array $args The query arguments for the update check.
	 *
	 * @return array The filtered query arguments.
	 */
	public function secure_update_check( $args ) {
		$key = base64_encode( get_option( 'wcifd-premium-key' ) );
		if ( $key ) {
			$args['premium-key'] = $key;
		}
		return $args;
	}
}

/* Plugin Initialization */
WCIFD_Plugin::get_instance();

/*
 * This hook ensures that the custom cron job is removed cleanly when your plugin is deactivated,
 * which is crucial for proper plugin management.
 */
register_deactivation_hook( __FILE__, array( 'WCIFD_AS_Cleaner', 'deactivate_plugin' ) );

