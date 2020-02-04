<?php
/**
 * Pagina opzioni/ strumenti
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @since 1.2.0
 */

/**
 * Registrazione script necessario al menu di navigazione
 */
function wcifd_register_scripts() {

	$screen = get_current_screen();
	if ( $screen->id === 'woocommerce_page_wc-importer-for-danea' ) {

		wp_enqueue_style( 'wcifd-style', WCIFD_URI . 'css/wc-importer-for-danea.css' );
		wp_enqueue_script( 'wcifd-admin-nav', WCIFD_URI . 'js/wcifd-admin-nav.js', array( 'jquery' ), '1.0', true );

		wp_enqueue_style( 'tzcheckbox-style', WCIFD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.css' );
		wp_enqueue_script( 'tzcheckbox', WCIFD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.js', array( 'jquery' ) );
		wp_enqueue_script( 'tzcheckbox-script', WCIFD_URI . 'js/tzCheckbox/js/script.js', array( 'jquery' ) );
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
 * Pagina opzioni
 */
function wcifd_options() {

	/*Controllo se l'utente ha i diritti d'accessso necessari*/
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( __( 'It seems like you don\'t have permission to see this page', 'wcifd' ) );
	}

	/*Inizio template di pagina*/
	echo '<div class="wrap">';
	echo '<div class="wrap-left">';

	/*Controllo se woocommerce e' installato*/
	if ( ! class_exists( 'WooCommerce' ) ) { ?>

		<div id="message" class="error"><p><strong>
			<?php echo __( 'ATTENTION! It seems like Woocommerce is not installed.', 'wcifd' ); ?>
		</strong></p></div>

		<?php
		exit;
	}
	?>
		

	<div id="wcifd-generale">
	<?php
		/*Header*/
		echo '<h1 class="wcifd main">' . __( 'Woocommmerce Importer for Danea - Premium', 'wcifd' ) . '</h1>';

		/*Plugin premium key*/
		$key = sanitize_text_field( get_option( 'wcifd-premium-key' ) );
	if ( isset( $_POST['wcifd-premium-key'] ) ) {
		$key = sanitize_text_field( $_POST['wcifd-premium-key'] );
		update_option( 'wcifd-premium-key', $key );
	}
		echo '<form id="wcifd-options" method="post" action="">';
		echo '<label>' . __( 'Premium Key', 'wcifd' ) . '</label>';
		echo '<input type="text" class="regular-text" name="wcifd-premium-key" id="wcifd-premium-key" placeholder="' . __( 'Add your Premium Key', 'wcifd' ) . '" value="' . $key . '" />';
		echo '<p class="description">' . __( 'Add your Premium Key and keep update your copy of <strong>Woocommerce Importer for Danea - Premium</strong>.', 'wcifd' ) . '</p>';
		echo '<input type="hidden" name="done" value="1" />';
		echo '<input type="submit" class="button button-primary" value="' . __( 'Save ', 'wcifd' ) . '" />';
		echo '</form>';
	?>
	</div>
			
	<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
	<h2 id="wcifd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="#" data-link="wcifd-suppliers" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __( 'Suppliers', 'wcifd' ); ?></a>
		<a href="#" data-link="wcifd-products" class="nav-tab" onclick="return false;"><?php echo __( 'Products', 'wcifd' ); ?></a>
		<a href="#" data-link="wcifd-clients" class="nav-tab" onclick="return false;"><?php echo __( 'Clients', 'wcifd' ); ?></a>    
		<a href="#" data-link="wcifd-orders" class="nav-tab" onclick="return false;"><?php echo __( 'Orders', 'wcifd' ); ?></a>
		<?php if ( function_exists( 'woocommerce_role_based_price' ) && get_option( 'wc_rbp_general') ) { ?>
			<a href="#" data-link="wcifd-rbp" class="nav-tab" onclick="return false;"><?php echo __( 'WooCommerce Role Based Price', 'wcifd' ); ?></a>
		<?php } ?>
	</h2>
	  
	  
	<!-- IMPORTAZIONE RIVENDITORI -->     	  
	<div id="wcifd-suppliers" class="wcifd-admin" style="display: block;">

		<?php include( WCIFD_ADMIN . 'wcifd-import-supplier-template.php' ); ?>
	 
	</div>


	<!-- IMPORTAZIONE PRODOTTI -->
	<div id="wcifd-products" class="wcifd-admin">
	 
		<?php include( WCIFD_ADMIN . 'wcifd-import-products-template.php' ); ?>		

	</div>
	

	<!-- IMPORT CLIENTS AS WORDPRESS USERS -->     
	<div id="wcifd-clients" class="wcifd-admin">

		<?php include( WCIFD_ADMIN . 'wcifd-import-clients-template.php' ); ?>

	</div>


	<!-- IMPORT ORDERS AS WOOCOMMERCE ORDERS -->
	<div id="wcifd-orders" class="wcifd-admin">
	 
		<?php include( WCIFD_ADMIN . 'wcifd-import-orders-template.php' ); ?>	

	</div>

	<!-- WOOCOMMERCE ROLE BASED PRICE -->
	<div id="wcifd-rbp" class="wcifd-admin">
	 
		<?php include( WCIFD_ADMIN . 'wcifd-role-based-price.php' ); ?>	

	</div>


	</div><!--WRAP-LEFT-->
	
	<div class="wrap-right">
		<iframe width="300" height="900" scrolling="no" src="https://www.ilghera.com/images/wcifd-premium-iframe.html"></iframe>
	</div>
	<div class="clear"></div>
	
 </div><!--WRAP-->
	
	
	<?php

}

/**
 * Messaggio aggiornamento
 */
function wcifd_update_message2( $plugin_data, $response ) {

	$message = null;
	$key = get_option( 'wcifd-premium-key' );

	$message = null;

	if ( ! $key ) {

		$message = 'A <b>Premium Key</b> is required for keeping this plugin up to date. Please, add yours in the <a href="' . admin_url() . 'admin.php/?page=wc-importer-for-danea">options page</a> or click <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">here</a> for prices and details.';

	} else {

		$decoded_key = explode( '|', base64_decode( $key ) );
		$bought_date = date( 'd-m-Y', strtotime( $decoded_key[1] ) );
		$limit = strtotime( $bought_date . ' + 365 day' );
		$now = strtotime( 'today' );

		if ( $limit < $now ) {
			$message = 'It seems like your <strong>Premium Key</strong> is expired. Please, click <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">here</a> for prices and details.';
		} elseif ( ! in_array( $decoded_key[2], array( 1572, 1582 ) ) ) {
			$message = 'It seems like your <strong>Premium Key</strong> is not valid. Please, click <a href="https://www.ilghera.com/product/woocommerce-importer-for-danea-premium/" target="_blank">here</a> for prices and details.';
		}
	}
	echo ( $message ) ? '<br><span class="wcifd-alert">' . $message . '</span>' : '';

}
add_action( 'in_plugin_update_message-wc-importer-for-danea-premium/wc-importer-for-danea-premium.php', 'wcifd_update_message2', 10, 2 );
