<?php
/**
 * Admin options page and functions
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.4.0
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
			echo '<h1 class="wcifd main">' . esc_html__( 'WooCommerce Importer for Danea', 'wc-importer-for-danea' ) . '</h1>';

		echo '</div>';

		echo '<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>';

		echo '<h2 id="wcifd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">';
			echo '<a href="#" data-link="wcifd-suppliers" class="nav-tab nav-tab-active" onclick="return false;">' . esc_html__( 'Suppliers', 'wc-importer-for-danea' ) . '</a>';
			echo '<a href="#" data-link="wcifd-products" class="nav-tab" onclick="return false;">' . esc_html__( 'Products', 'wc-importer-for-danea' ) . '</a>';
			echo '<a href="#" data-link="wcifd-clients" class="nav-tab" onclick="return false;">' . esc_html__( 'Clients', 'wc-importer-for-danea' ) . '</a>    ';
			echo '<a href="#" data-link="wcifd-orders" class="nav-tab" onclick="return false;">' . esc_html__( 'Orders', 'wc-importer-for-danea' ) . '</a>';

		if ( function_exists( 'woocommerce_role_based_price' ) && get_option( 'wc_rbp_general' ) ) {

			echo '<a href="#" data-link="wcifd-rbp" class="nav-tab" onclick="return false;">' . esc_html__( 'WooCommerce Role Based Price', 'wc-importer-for-danea' ) . '</a>';

		}
			echo '<a href="#" data-link="wcifd-tools" class="nav-tab" onclick="return false;">' . esc_html__( 'Tools', 'wc-importer-for-danea' ) . '</a>';
		echo '</h2>';
	}

    /**
     * Go premium button
     *
     * @return void
     */
    public static function go_premium() {

        $title       = __( 'This is a premium functionality, click here for more information', 'wp-restaurant-booking' );
        $output      = '<span class="wcifd label label-warning premium">';
            $output .= '<a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium" target="_blank" title="' . esc_attr( $title ) . '">Premium</a>';
        $output     .= '</span>';

        $allowed = array(
            'span' => array(
                'class' => array(),
            ),
            'a'    => array(
                'target' => array(),
                'title'  => array(),
                'href'   => array(),
            ),
        );

        echo wp_kses( $output, $allowed );
    }
}

