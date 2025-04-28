<?php
/**
 * Update products catalog
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.2
 */

/**
 * Update products catalog
 *
 * @param file $file   the file imported. 
 * @param bool $is_csv csv with true, XML instead. 
 *
 * @return void
 */
function wcifd_catalog_update( $file, $is_csv = false ) {

    $products = array();

	/* Admin options */
	$regular_price_list = get_option( 'wcifd-regular-price-list' );
	$sale_price_list    = get_option( 'wcifd-sale-price-list' );
	$size_type          = get_option( 'wcifd-size-type' );
	$weight_type        = get_option( 'wcifd-weight-type' );
	$deleted_products   = get_option( 'wcifd-deleted-products' );
	$replace_products   = get_option( 'wcifd-replace-products' );

	/* WooCommerce Role Based Price */
	$wc_rbp = WCIFD_Functions::get_wc_rbp();

    if ( $is_csv ) {

        /* Handle CSV file */
        $products = [];

        if ( false !== ( $handle = fopen( $file, 'r' ) ) ) {

            $headers = fgetcsv( $handle );

            while ( false !== ( $row = fgetcsv( $handle ) ) ) {

                $product = new stdClass();

                foreach ( $headers as $index => $header ) {

                    $product->{ $header } = $row[ $index ];
                }
                $products[] = $product;
            }

            fclose( $handle );
        }

    } else {
        
        /* Handle XML file */
        $results = simplexml_load_file( $file );

        /* Delete products not found */
        if ( $replace_products && 'full' === strval( $results->attributes()->Mode[0] ) ) {

            wcifd_delete_all_products();

        }

        /* Check if the update is full or not */
        $products = $results->Products ? $results->Products : $results->UpdatedProducts;
        $products = $products->children();
    }


	/* Set transient for progress bar */
	set_transient( 'wcifd-total-actions', count( $products ), DAY_IN_SECONDS );

	foreach ( $products as $product ) {

		/* Vat */
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
            'is_csv'             => $is_csv,
		);

		$hash  = md5( wp_json_encode( $data ) );
		$class = new WCIFD_Temporary_Data();

		/* Add temporary data to the dedicated table */
		$class->wcifd_add_temporary_data( $hash, wp_json_encode( $data ) );

		/* Import single product */
		as_enqueue_async_action(
			'wcifd_import_product_event',
			array(
				'hash' => $hash,
			),
			'wcifd-import-product'
		);

	}

	/* Delete products */
	if ( isset( $results->DeletedProducts ) ) {

        /* Set transient for progress bar */
        set_transient( 'wcifd-total-delete-actions', count( $results->DeletedProducts->children() ), DAY_IN_SECONDS );

		foreach ( $results->DeletedProducts->children() as $del_product ) {

			if ( isset( $del_product->Code ) ) {

				as_enqueue_async_action(
					'wcifd_delete_product_event',
					array(
						wp_json_encode( $del_product->Code ),
					),
					'wcifd-delete-product'
				);
			}
		}
	}

}

