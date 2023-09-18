<?php
/**
 * Pagina opzioni/ strumenti
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.3.1
 */

/**
 * Registrazione script necessario al menu di navigazione
 */
function wcifd_register_scripts() {

	$screen = get_current_screen();
	if ( 'woocommerce_page_wc-importer-for-danea' === $screen->id ) {

		wp_enqueue_style( 'wcifd-style', WCIFD_URI . 'css/wc-importer-for-danea.css', array(), WCIFD_VERSION );
		wp_enqueue_script( 'wcifd-admin-nav', WCIFD_URI . 'js/wcifd-admin-nav.js', array( 'jquery' ), WCIFD_VERSION, true );

		wp_enqueue_style( 'chosen-style', WCIFD_URI . '/vendor/harvesthq/chosen/chosen.min.css', array(), WCIFD_VERSION );
		wp_enqueue_script( 'chosen', WCIFD_URI . '/vendor/harvesthq/chosen/chosen.jquery.min.js', array( 'jquery' ), WCIFD_VERSION, false );

		wp_enqueue_style( 'tzcheckbox-style', WCIFD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.css', array(), WCIFD_VERSION );
		wp_enqueue_script( 'tzcheckbox', WCIFD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.js', array( 'jquery' ), WCIFD_VERSION, false );
		wp_enqueue_script( 'tzcheckbox-script', WCIFD_URI . 'js/tzCheckbox/js/script.js', array( 'jquery' ), WCIFD_VERSION, false );
	}

}
add_action( 'admin_enqueue_scripts', 'wcifd_register_scripts' );


/**
 * Voce di menu
 */
function wcifd_add_menu() {
	$wcifd_page = add_submenu_page( 'woocommerce', 'WCIFD Options', 'WC Importer for Danea', 'manage_woocommerce', 'wc-importer-for-danea', 'wcifd_options' );

	return $wcifd_page;
}
add_action( 'admin_menu', 'wcifd_add_menu' );


/**
 * Go premium button
 */
function go_premium() {

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


/**
 * Pagina opzioni
 */
function wcifd_options() {

	/*Controllo se l'utente ha i diritti d'accessso necessari*/
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'It seems like you don\'t have permission to see this page', 'wc-importer-for-danea' ) );
	}

	/*Inizio template di pagina*/
	echo '<div class="wrap">';
	echo '<div class="wrap-left">';

	/*Controllo se woocommerce e' installato*/
	if ( ! class_exists( 'WooCommerce' ) ) { ?>

		<div id="message" class="error"><p><strong>
			<?php esc_html_e( 'ATTENTION! It seems like Woocommerce is not installed.', 'wc-importer-for-danea' ); ?>
		</strong></p></div>

		<?php
		exit;
	}
	?>

	<div id="wcifd-generale">
		<?php
		/*Header*/
		echo '<h1 class="wcifd main">' . esc_html__( 'Woocommmerce Importer for Danea', 'wc-importer-for-danea' ) . '</h1>';
		?>
	</div>

	<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>

	<h2 id="wcifd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="#" data-link="wcifd-suppliers" class="nav-tab nav-tab-active" onclick="return false;"><?php esc_html_e( 'Suppliers', 'wc-importer-for-danea' ); ?></a>
		<a href="#" data-link="wcifd-products" class="nav-tab" onclick="return false;"><?php esc_html_e( 'Products', 'wc-importer-for-danea' ); ?></a>
		<a href="#" data-link="wcifd-clients" class="nav-tab" onclick="return false;"><?php esc_html_e( 'Clients', 'wc-importer-for-danea' ); ?></a>    
		<a href="#" data-link="wcifd-orders" class="nav-tab" onclick="return false;"><?php esc_html_e( 'Orders', 'wc-importer-for-danea' ); ?></a>
		<?php if ( function_exists( 'woocommerce_role_based_price' ) && get_option( 'wc_rbp_general' ) ) { ?>
			<a href="#" data-link="wcifd-rbp" class="nav-tab" onclick="return false;"><?php esc_html_e( 'WooCommerce Role Based Price', 'wc-importer-for-danea' ); ?></a>
		<?php } ?>
	</h2>

	<!-- IMPORTAZIONE RIVENDITORI -->     	  
	<div id="wcifd-suppliers" class="wcifd-admin" style="display: block;">

		<?php include WCIFD_ADMIN . 'wcifd-import-supplier-template.php'; ?>

	</div>

	<!-- IMPORTAZIONE PRODOTTI -->
	<div id="wcifd-products" class="wcifd-admin">

		<?php include WCIFD_ADMIN . 'wcifd-import-products-template.php'; ?>		

	</div>

	<!-- IMPORT CLIENTS AS WordPress USERS -->     
	<div id="wcifd-clients" class="wcifd-admin">

		<?php include WCIFD_ADMIN . 'wcifd-import-clients-template.php'; ?>

	</div>

	<!-- IMPORT ORDERS AS WOOCOMMERCE ORDERS -->
	<div id="wcifd-orders" class="wcifd-admin">

		<?php include WCIFD_ADMIN . 'wcifd-import-orders-template.php'; ?>	

	</div>

	<!-- WOOCOMMERCE ROLE BASED PRICE -->
	<div id="wcifd-rbp" class="wcifd-admin">

		<?php include WCIFD_ADMIN . 'wcifd-role-based-price.php'; ?>	

	</div>

	</div><!--WRAP-LEFT-->

	<div class="wrap-right">
		<iframe width="300" height="1200" scrolling="no" src="https://www.ilghera.com/images/wcifd-iframe.html"></iframe>
	</div>
	<div class="clear"></div>

	</div><!--WRAP-->

	<?php

}

