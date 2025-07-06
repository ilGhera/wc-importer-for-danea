<?php
/**
 * Admin options page and functions
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Admin
 *
 * @since 1.7.0
 */
class WCIFD_Admin {

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'in_plugin_update_message-wc-importer-for-danea-premium/wc-importer-for-danea-premium.php', array( $this, 'check_update_message' ), 10, 2 );

	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( 'woocommerce_page_wc-importer-for-danea' === $screen->id ) {

			/* css */
			wp_enqueue_style( 'wcifd-style', WCIFD_URI . 'css/wc-importer-for-danea.css', array(), WCIFD_VERSION );
			wp_enqueue_style( 'chosen-style', WCIFD_URI . '/vendor/harvesthq/chosen/chosen.min.css', array(), WCIFD_VERSION );
			wp_enqueue_style( 'tzcheckbox-style', WCIFD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.css', array(), WCIFD_VERSION );

			/* js */
			wp_enqueue_script( 'wcifd-admin-nav', WCIFD_URI . 'js/wcifd-admin-nav.js', array( 'jquery' ), WCIFD_VERSION, true );
			wp_enqueue_script( 'chosen', WCIFD_URI . '/vendor/harvesthq/chosen/chosen.jquery.min.js', array( 'jquery' ), WCIFD_VERSION, false );
			wp_enqueue_script( 'tzcheckbox', WCIFD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.js', array( 'jquery' ), WCIFD_VERSION, false );
			wp_enqueue_script( 'tzcheckbox-script', WCIFD_URI . 'js/tzCheckbox/js/script.js', array( 'jquery' ), WCIFD_VERSION, false );
		}

	}

	/**
	 * Add the submenu
	 *
	 * @return void
	 */
	public function add_menu() {

		add_submenu_page( 'woocommerce', 'WCIFD Options', 'WC Importer for Danea', 'manage_woocommerce', 'wc-importer-for-danea', array( $this, 'setup_options_page' ) );

	}

	/**
	 * The plugin options page
	 *
	 * @return void
	 */
	public function setup_options_page() {

		/* Check current user permissions */
		if ( ! current_user_can( 'manage_woocommerce' ) ) {

			wp_die( esc_html__( 'It seems like you don\'t have permission to see this page', 'wc-importer-for-danea' ) );

		}

		/* Start page template */
		echo '<div class="wrap">';

			echo '<div class="wrap-left">';

		/* Check if WooCommerce is active */
		if ( ! class_exists( 'WooCommerce' ) ) {

			echo '<div id="message" class="error">';
				echo '<p>';
					echo '<strong>' . esc_html__( 'WARNING! It seems like WooCommerce is not installed.', 'wc-importer-for-danea' ) . '</strong>';
				echo '</p>';
			echo '</div>';

			exit;

		}

				$this->tab_menu();

				include WCIFD_ADMIN . 'templates/wcifd-import-suppliers-template.php';
				include WCIFD_ADMIN . 'templates/wcifd-import-products-template.php';
				include WCIFD_ADMIN . 'templates/wcifd-import-clients-template.php';
				include WCIFD_ADMIN . 'templates/wcifd-import-orders-template.php';
				include WCIFD_ADMIN . 'templates/wcifd-role-based-price-template.php';
				include WCIFD_ADMIN . 'templates/wcifd-role-based-pricing-template.php';
				include WCIFD_ADMIN . 'templates/wcifd-tools.php';

			echo '</div>'; // wrap-left.

			echo '<div class="wrap-right">';
				echo '<iframe width="300" height="900" scrolling="no" src="https://www.ilghera.com/images/wcifd-premium-iframe.html"></iframe>';
			echo '</div>'; // wrap-right.
			echo '<div class="clear"></div>';

		echo '</div>'; // wrap.

	}

	/**
	 * The tab menu
	 *
	 * @return void
	 */
	public function tab_menu() {

		echo '<div id="wcifd-general">';

			/* Header */
			echo '<h1 class="wcifd main">' . esc_html__( 'WooCommerce Importer for Danea - Premium', 'wc-importer-for-danea' ) . '</h1>';

			/* The premium key form */
			$this->premium_key_form();

		echo '</div>';

		echo '<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>';

		echo '<h2 id="wcifd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">';
			echo '<a href="#" data-link="wcifd-suppliers" class="nav-tab nav-tab-active" onclick="return false;">' . esc_html__( 'Suppliers', 'wc-importer-for-danea' ) . '</a>';
			echo '<a href="#" data-link="wcifd-products" class="nav-tab" onclick="return false;">' . esc_html__( 'Products', 'wc-importer-for-danea' ) . '</a>';
			echo '<a href="#" data-link="wcifd-clients" class="nav-tab" onclick="return false;">' . esc_html__( 'Clients', 'wc-importer-for-danea' ) . '</a>    ';
			echo '<a href="#" data-link="wcifd-orders" class="nav-tab" onclick="return false;">' . esc_html__( 'Orders', 'wc-importer-for-danea' ) . '</a>';

		if ( function_exists( 'woocommerce_role_based_price' ) && get_option( 'wc_rbp_general' ) ) {

            /* WooCommerce Role Based Price */
			echo '<a href="#" data-link="wcifd-rbp" class="nav-tab" onclick="return false;">' . esc_html__( 'WooCommerce Role Based Price', 'wc-importer-for-danea' ) . '</a>';

		} elseif ( class_exists( 'WOOCOMMERCE_ROLE_BASED_PRICING' ) ) {

            /* WooCommerce User Role Based Pricing */
			echo '<a href="#" data-link="wcifd-rbp" class="nav-tab" onclick="return false;">' . esc_html__( 'WooCommerce User Role Based Pricing', 'wc-importer-for-danea' ) . '</a>';

		}
			echo '<a href="#" data-link="wcifd-tools" class="nav-tab" onclick="return false;">' . esc_html__( 'Tools', 'wc-importer-for-danea' ) . '</a>';
		echo '</h2>';
	}

	/**
	 * The premium key form
	 *
	 * @return void
	 */
	public function premium_key_form() {

		/* Plugin premium key */
		$key = sanitize_text_field( get_option( 'wcifd-premium-key' ) );

		if ( isset( $_POST['wcifd-premium-key'], $_POST['wcifd-premium-key-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-premium-key-nonce'] ) ), 'wcifd-premium-key' ) ) {
			$key = sanitize_text_field( wp_unslash( $_POST['wcifd-premium-key'] ) );
			update_option( 'wcifd-premium-key', $key );
		}

		echo '<form id="wcifd-options" method="post" action="">';
			echo '<label>' . esc_html__( 'Premium Key', 'wc-importer-for-danea' ) . '</label>';
			echo '<input type="text" class="regular-text" name="wcifd-premium-key" id="wcifd-premium-key" placeholder="' . esc_html__( 'Add your Premium Key', 'wc-importer-for-danea' ) . '" value="' . esc_attr( $key ) . '" />';
			echo '<p class="description">' . wp_kses_post( __( 'Add your Premium Key and keep update your copy of <strong>Woocommerce Importer for Danea - Premium</strong>.', 'wc-importer-for-danea' ) ) . '</p>';
			echo '<input type="hidden" name="done" value="1" />';

			wp_nonce_field( 'wcifd-premium-key', 'wcifd-premium-key-nonce' );

			echo '<input type="submit" class="button button-primary" value="' . esc_attr__( 'Save ', 'wc-importer-for-danea' ) . '" />';
		echo '</form>';
	}

	/**
	 * Message to the admin in case of update not downlodable for bad or missed premium key
	 *
	 * @param  array $plugin_data the plugin data.
	 * @param  array $response    the response data.
	 *
	 * @return void
	 */
	public function check_update_message( $plugin_data, $response ) {

		$message = null;
		$key     = get_option( 'wcifd-premium-key' );

		$message = null;

		if ( ! $key ) {

			$message = 'A <b>Premium Key</b> is required for keeping this plugin up to date. Please, add yours in the <a href="' . admin_url() . 'admin.php/?page=wc-importer-for-danea">options page</a> or click <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">here</a> for prices and details.';

		} else {

			$decoded_key = explode( '|', base64_decode( $key ) );
			$bought_date = date( 'd-m-Y', strtotime( $decoded_key[1] ) );
			$limit       = strtotime( $bought_date . ' + 365 day' );
			$now         = strtotime( 'today' );

			if ( $limit < $now ) {
				$message = 'It seems like your <strong>Premium Key</strong> is expired. Please, click <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">here</a> for prices and details.';
			} elseif ( ! in_array( $decoded_key[2], array( 1572, 1582 ), true ) ) {
				$message = 'It seems like your <strong>Premium Key</strong> is not valid. Please, click <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">here</a> for prices and details.';
			}
		}
		echo ( $message ) ? '<br><span class="wcifd-alert">' . wp_kses_post( $message ) . '</span>' : '';

	}

}

