<?php
/**
 * Delete a single product from which the E-commerce flag has been removed in Danea Easyfatt
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Delete product
 *
 * @param string $product_sku the sku of the single product to be deleted, encoded in json.
 *
 * @return void
 */
function wcifd_delete_single_product( $product_sku ) {

	$sku = json_decode( $product_sku, true );

	if ( isset( $sku[0] ) ) {

		$product_id = WCIFD_Functions::search_product( $sku[0] );

		if ( $product_id ) {

			$product = wc_get_product( $product_id );

			/* Delete product */
			$deleted = $product->delete( true );

			if ( $deleted ) {

				/* Delete transients */
				wc_delete_product_transients( $product_id );

			} else {

				error_log( 'WCIFD ERROR | Eliminazione prodotto | ID: ' . $product_id );
			}
		}
	}
}
add_action( 'wcifd_delete_product_event', 'wcifd_delete_single_product', 10, 1 );

