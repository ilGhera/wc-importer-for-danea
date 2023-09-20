<?php
/**
 * Aggiornaemnto del catalogo prodotti
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.2
 */

/**
 * Aggiornamento prodotti
 *
 * @param file $file l'xml proveniente da Danea Easyfatt.
 *
 * @return void
 */
function wcifd_catalog_update( $file ) {

	/*Opzioni admin*/
	$regular_price_list = get_option( 'wcifd-regular-price-list' );
	$sale_price_list    = get_option( 'wcifd-sale-price-list' );
	$size_type          = get_option( 'wcifd-size-type' );
	$weight_type        = get_option( 'wcifd-weight-type' );
	$deleted_products   = get_option( 'wcifd-deleted-products' );
	$replace_products   = get_option( 'wcifd-replace-products' );

	/*WooCommerce Role Based Price*/
	$wc_rbp = get_wc_rbp();

	$results = simplexml_load_file( $file );

	/*Cancellazione prodotti esistenti*/
	if ( $replace_products && 'full' === strval( $results->attributes()->Mode[0] ) ) {

		wcifd_delete_all_products();

	}

	/*Verifica che si tratti di un aggiornamento o dell'intero catalogo prodotti*/
	$products = $results->Products ? $results->Products : $results->UpdatedProducts;

    /* Set transient for progress bar */
    set_transient( 'wcifd-total-actions', count( $products->children() ), DAY_IN_SECONDS );

	foreach ( $products->children() as $product ) {

		/*Gestione iva*/
		$tax_attributes = null;
		if ( isset( $product->Vat ) ) {

			$tax_attributes = $product->Vat->attributes();

		}

		$data = array(
			'product'            => $product,
			'regular_price_list' => $regular_price_list,
			'sale_price_list'    => $sale_price_list,
			'size_type'          => $size_type,
			'weight_type'        => $weight_type,
			'tax_attributes'     => $tax_attributes,
			'deleted_products'   => $deleted_products,
			'wc_rbp'             => $wc_rbp,

		);

		$hash  = md5( json_encode( $data ) );
		$class = new WCIFD_Temporary_Data();

		/*Aggiungo i dati temporanei nella tabella dedicata*/
		$class->wcifd_add_temporary_data( $hash, json_encode( $data ) );

		/*Importazione singolo prodotto*/
		as_enqueue_async_action(
			'wcifd_import_product_event',
			array(
				'hash' => $hash,
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

