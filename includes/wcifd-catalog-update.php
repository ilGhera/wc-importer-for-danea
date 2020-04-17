<?php
/**
 * Aggiornaemnto del catalogo prodotti
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @since 1.3.0
 *
 * @param file $file l'xml proveniente da Danea Easyfatt.
 */
function wcifd_catalog_update( $file ) {

	/*Opzioni admin*/
	$regular_price_list = get_option( 'wcifd-regular-price-list' );
	$sale_price_list    = get_option( 'wcifd-sale-price-list' );
	$size_type          = get_option( 'wcifd-size-type' );
	$weight_type        = get_option( 'wcifd-weight-type' );
	$deleted_products   = get_option( 'wcifd-deleted-products' );

	/*WooCommerce Role Based Price*/
	$wc_rbp = get_wc_rbp();

	$results = simplexml_load_file( $file );

	/*Verifica che si tratti di un aggiornamento o dell'intero catalogo prodotti*/
	$products = $results->Products ? $results->Products : $results->UpdatedProducts;

	foreach ( $products->children() as $product ) {

		/*Gestione iva*/
		$tax_attributes = null;
		if ( isset( $product->Vat ) ) {

			$tax_attributes = json_encode( $product->Vat->attributes() );

		}

		/*Importazione singolo prodotto*/
		as_enqueue_async_action(
			'wcifd_import_product_event',
			array(
				json_encode( $product ),
				$regular_price_list,
				$sale_price_list,
				$size_type,
				$weight_type,
				$tax_attributes,
				$deleted_products,
				$wc_rbp,
			),
			'wcifd-import-product'
		);
	}

	/*Cancellazione prodotti*/
	if ( isset( $results->DeletedProducts ) ) {

		foreach ( $results->DeletedProducts->children() as $del_product ) {

			if ( isset( $del_product->Code ) ) {

				as_enqueue_async_action(
					'wcifd_delete_product_event',
					array(
						json_encode( $del_product->Code ),
					),
					'wcifd-delete-product'
				);
			}

		}

	}

}
