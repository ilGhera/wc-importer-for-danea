<?php
/**
 * Delete all WooCommerce products receiving the entire catalog from Danea Easyfatt
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Delete products
 *
 * @return void
 */
function wcifd_delete_all_products() {

	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	);

	$product_ids = get_posts( $args );

	if ( $product_ids ) {

		foreach ( $product_ids as $product_id ) {

			/* Get product */
			$product = wc_get_product( $product_id );

			if ( $product ) {

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
}

