<?php
/**
 * Eliminazione del singolo prodotto a cui sia stato tolto il flag E-commerce in Danea Easyfatt
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

/**
 * Eliminazione prodotto
 *
 * @param string $product_sku lo sku del singolo prodotto da eliminare, codificato in json.
 *
 * @return void
 */
function wcifd_delete_single_product( $product_sku ) {

	$sku = json_decode( $product_sku, true );

	if ( isset( $sku[0] ) ) {

		$product_id = wcifd_search_product( $sku[0] );

		if ( $product_id ) {

			/*Eliminazione prodotto*/
			wp_delete_post( $product_id, true );

			/*Aggiornamento meta lookup table*/
			new WCIFD_Product_Meta_Lookup( array( 'product_id' => $product_id ), 'delete' );

			/*Se presenti elimino le variazionid i prodotto*/
			wcifd_delete_variations( $product_id );

		}
	}

}
add_action( 'wcifd_delete_product_event', 'wcifd_delete_single_product', 10, 1 );
