<?php
/**
 * Delete a single product from which the E-commerce flag has been removed in Danea Easyfatt
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

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

			/* Delete product */
			wp_delete_post( $product_id, true );

			/* Meta lookup table update */
			new WCIFD_Product_Meta_Lookup( array( 'product_id' => $product_id ), 'delete' );

			/* Delete product variations if any */
			WCIFD_Functions::delete_variations( $product_id );

		}
	}

}
add_action( 'wcifd_delete_product_event', 'wcifd_delete_single_product', 10, 1 );

