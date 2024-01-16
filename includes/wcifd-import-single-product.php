<?php
/**
 * Importazione singolo prodotto
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.4
 */

/**
 * Importazione prodotto
 *
 * @param  string $hash il codice identificativo del prodotto.
 *
 * @return void
 */
function wcifd_import_single_product( $hash ) {

	$temp                  = new WCIFD_Temporary_Data();
	$data                  = $temp->wcifd_get_temporary_data( $hash );
	$product               = isset( $data['product'] ) ? $data['product'] : '';
	$notes_as_descriptions = get_option( 'wcifd-notes-as-description' );
	$short_description_opt = get_option( 'wcifd-short-description' );

	/*Termina se il prodotto non esiste*/
	if ( ! $product ) {

		/*Cancello i dati temporanei dalla tabella dedicata*/
		$temp->wcifd_delete_temporary_data( $hash );

		return;

	}

	$sku                = isset( $product['Code'] ) ? $product['Code'] : '';
	$sku                = str_replace( '\\', '\\\\', $sku );
	$title              = isset( $product['Description'] ) ? htmlentities( $product['Description'] ) : '';
	$category           = isset( $product['Category'] ) ? $product['Category'] : '';
	$sub_category       = isset( $product['Subcategory'] ) ? $product['Subcategory'] : '';
	$producer_name      = isset( $product['ProducerName'] ) ? $product['ProducerName'] : '';
	$supplier_name      = isset( $product['SupplierName'] ) ? $product['SupplierName'] : '';
	$sup_product_code   = isset( $product['SupplierProductCode'] ) ? $product['SupplierProductCode'] : '';
	$get_tax            = isset( $product['Vat'] ) ? $product['Vat'] : '';
	$tax                = ( is_array( $get_tax ) && isset( $get_tax[0] ) ) ? $get_tax[0] : $get_tax;
	$stock              = isset( $product['AvailableQty'] ) ? $product['AvailableQty'] : '';
	$um                 = isset( $product['Um'] ) ? $product['Um'] : '';
	$size_um            = isset( $product['SizeUm'] ) ? $product['SizeUm'] : '';
	$weight_um          = isset( $product['WeightUm'] ) ? $product['WeightUm'] : '';
	$image_file_name    = isset( $product['ImageFileName'] ) ? sanitize_title( $product['ImageFileName'] ) : '';
	$regular_price_list = isset( $data['regular_price_list'] ) ? $data['regular_price_list'] : '';
	$sale_price_list    = isset( $data['sale_price_list'] ) ? $data['sale_price_list'] : '';
	$size_type          = isset( $data['size_type'] ) ? $data['size_type'] : '';
	$weight_type        = isset( $data['weight_type'] ) ? $data['weight_type'] : '';
	$tax_attributes     = isset( $data['tax_attributes'] ) ? $data['tax_attributes'] : '';
	$deleted_products   = isset( $data['deleted_products'] ) ? $data['deleted_products'] : '';
	$wc_rbp             = isset( $data['wc_rbp'] ) ? $data['wc_rbp'] : '';
	$parent_sku         = null;
	$var_attributes     = null;
	$variable_product   = null;
	$parent_product_id  = null;

	/* Descrizione prodotto */
	$description = null;

	if ( isset( $product['DescriptionHtml'] ) && is_string( $product['DescriptionHtml'] ) ) {

		$description = wp_filter_post_kses( $product['DescriptionHtml'] );

	} elseif ( $notes_as_descriptions && isset( $product['Notes'] ) && is_string( $product['Notes'] ) ) {

		$description = wp_filter_post_kses( $product['Notes'] );

	} else {

		$description = $title;

	}

	/* Descrizione breve prodotto */
	$short_description = '';

	if ( 'excerpt' === $short_description_opt ) {

		$short_description = wcifd_get_short_description( $description );

	} elseif ( 'notes' === $short_description_opt && isset( $product['Notes'] ) && is_string( $product['Notes'] ) ) {

		$short_description = wp_filter_post_kses( $product['Notes'] );

	}

	/* Prodotti variabili precedentemente esportati */
	if ( ! $notes_as_descriptions && 'notes' !== $short_description_opt && isset( $product['Notes'] ) && is_string( $product['Notes'] ) ) {

		$notes = json_decode( $product['Notes'], true );

		if ( is_array( $notes ) ) {

			/*Parent sku*/
			if ( isset( $notes['parent_sku'] ) && '' !== $notes['parent_sku'] ) {

				$parent_sku = $notes['parent_sku'];

			} elseif ( isset( $notes['parent_id'] ) && '' !== $notes['parent_id'] ) {

				$parent_product_id = $notes['parent_id'];

			}

			if ( $parent_sku ) {
				$parent_product_id = wcifd_search_product( $parent_sku );
			}

			$var_attributes = isset( $notes['var_attributes'] ) ? $notes['var_attributes'] : null;

			/*Prodotto variabile*/
			if ( isset( $notes['product_type'] ) && 'variable' === $notes['product_type'] ) {
				if ( isset( $notes['attributes'] ) ) {
					$variable_product    = true;
					$imported_attributes = $notes['attributes'];
				}
			}
		}
	}

	/*Post status - Le variazioni devono essere pubblicate*/
	$new_products_status = get_option( 'wcifd-publish-new-products' ) ? 'publish' : 'draft';
	$status              = ( $var_attributes ) ? 'publish' : $new_products_status;

	/*Verifico la presenza del prodotto*/
	$id   = wcifd_search_product( $sku, $parent_product_id );
	$type = ( wp_get_post_parent_id( $id ) || $parent_product_id ) ? 'product_variation' : 'product';

	/*Gestione magazzino*/
	$manage_stock = get_option( 'woocommerce_manage_stock' ); // WC option.

	if ( 'yes' === $manage_stock ) {
		$manage_stock = ( isset( $product['ManageWarehouse'] ) && 'true' === $product['ManageWarehouse'] ) ? 'yes' : 'no'; // Single product from Danea.
	}

	$stock_status = 'instock';

	if ( 'yes' === $manage_stock && 1 > $stock ) {
		$stock_status = 'outofstock';
	}

	/*Dimensione prodotto*/
	$length = wcifd_get_product_size( $product, $size_type, 'z' );
	$width  = wcifd_get_product_size( $product, $size_type, 'x' );
	$height = wcifd_get_product_size( $product, $size_type, 'y' );

	/*Peso del prodotto*/
	if ( 'gross-weight' === $weight_type ) {
		$weight = isset( $product['GrossWeight'] ) ? $product['GrossWeight'] : '';
	} else {
		$weight = isset( $product['NetWeight'] ) ? $product['NetWeight'] : '';
	}

	/*Autore del post come fornitore*/
	$author = ( 1 === intval( get_option( 'wcifd-use-suppliers' ) ) && isset( $product['SupplierCode'] ) ) ? $product['SupplierCode'] : get_option( 'wcifd-current-user' );

	/*Imposte incluse*/
	$tax_included = get_option( 'wcifd-tax-included' );

	/*Prezzo di listino e prezzo scontato*/
	$regular_price = wcifd_get_list_price( $product, $regular_price_list, $tax_included );
	$sale_price    = wcifd_get_list_price( $product, $sale_price_list, $tax_included );
	$on_sale       = $sale_price ? 1 : 0;

	/*Variazione taglia e colore di Danea*/
	$variants = null;
	if ( isset( $product['Variants'] ) ) {

		$variants = $product['Variants'];

	} elseif ( isset( $product['Variant'] ) ) {
		foreach ( $product['Variant'] as $variant ) {
			$variants[] = $variant;
		}
	}

	if ( $variants ) {
		update_post_meta( $id, 'wcifd-danea-size-color', 1 );
	}

	/*Verifica classe di tassazione*/
	$tax_status = 'none';
	$tax_class  = '';
	$perc       = isset( $tax_attributes['@attributes']['Perc'] ) ? $tax_attributes['@attributes']['Perc'] : '';
	$class      = isset( $tax_attributes['@attributes']['Class'] ) ? $tax_attributes['@attributes']['Class'] : '';

	if ( '0' !== $perc || ( 'Escluso' !== $class && 'NonSoggetto' !== $class ) ) {
		$tax_status = 'taxable';
		$tax_class  = wcifd_get_tax_rate_class( $tax, strval( $perc ) );
	}

	/* Opzione evita creazione nuovi prodotti se non disponibili a magazzino */
	$products_not_available = get_option( 'wcifd-products-not-available' );

	/*Inizio aggiornamento prodotto o creazione se non presente*/
	if ( ! $id ) {

		/* Se impostato dall'admin, non creare nuovi prodotti se non disponibili a magazzino */
		if ( $products_not_available && 1 > $stock ) {
			return;
		}

		$args = array(
			'post_author'  => $author,
			'post_title'   => $title,
			'post_type'    => $type,
			'post_parent'  => $parent_product_id,
			'post_content' => $description,
			'post_excerpt' => $short_description,
			'post_status'  => $status,
			'meta_input'   => array(
				'_sku'           => $sku,
				'_tax_status'    => $tax_status,
				'_tax_class'     => $tax_class,
				'_stock'         => $stock,
				'_manage_stock'  => $manage_stock,
				'_stock_status'  => $stock_status,
				'_visibility'    => 'visible',
				'_regular_price' => $regular_price,
				'_price'         => $regular_price,
				'_sell_price'    => $regular_price,
				'_width'         => $width,
				'_height'        => $height,
				'_length'        => $length,
				'_weight'        => $weight,
                '_wcifd-um'      => $um,

			),
		);

		if ( $sale_price ) {
			$args['meta_input']['_sale_price'] = $sale_price;
			$args['meta_input']['_sell_price'] = $sale_price;
			$args['meta_input']['_price']      = $sale_price;
		} else {
			$args['meta_input']['_sale_price'] = '';
		}

		/*WooCommerce Role Based Price*/
		if ( is_array( $wc_rbp ) && ! empty( $wc_rbp ) ) {

			foreach ( $wc_rbp as $role => $price_types ) {
				foreach ( $price_types as $key => $value ) {

					$wc_rbp_price = wcifd_get_list_price( $product, $value, $tax_included );

					if ( $wc_rbp_price ) {

						$args['meta_input']['_enable_role_based_price']           = 1;
						$args['meta_input']['_role_based_price'][ $role ][ $key ] = $wc_rbp_price;

					}
				}
			}
		}

		/*Inserimento nuovo prodotto*/
		$product_id = wp_insert_post( $args, true );

		if ( is_wp_error( $product_id ) ) {

			error_log( 'WCIFD ERROR | Inserimento prodotto | Sku: ' . $sku . ' | ' . print_r( $product_id->get_error_message(), true ) );

			return;

		} else {

			if ( $variable_product ) {

				/*Aggiornamento prodotto padre*/
				wp_set_object_terms( $product_id, 'variable', 'product_type' );

				if ( $imported_attributes ) {

					foreach ( $imported_attributes as $key => $value ) {

						$is_taxonomy = false === strpos( $key, 'pa_' ) ? false : true;
						$attr_value  = $is_taxonomy ? null : $value;
						$attr_value  = is_array( $attr_value ) ? implode( ' | ', $attr_value ) : $attr_value;

						$attr = array(
							$key => array(
								'name'         => $key,
								'value'        => $attr_value,
								'is_visible'   => 1,
								'is_variation' => 1,
								'is_taxonomy'  => $is_taxonomy,
							),
						);

						if ( get_post_meta( $product_id, '_product_attributes', true ) ) {
							$metas = get_post_meta( $product_id, '_product_attributes', true );
						} else {
							$metas = array();
						}

						$metas[ $key ] = $attr[ $key ];
						update_post_meta( $product_id, '_product_attributes', $metas );

						if ( $is_taxonomy ) {

							wp_set_object_terms( $product_id, $value, $key );

						}
					}
				}
			} elseif ( $parent_product_id && $var_attributes ) {

				foreach ( $var_attributes as $key => $value ) {

					update_post_meta( $product_id, 'attribute_' . $key, $value );

					$is_taxonomy = false === strpos( $key, 'pa_' ) ? false : true;

					if ( $is_taxonomy ) {

						$attr = array(
							$key => array(
								'name'         => $value,
								'value'        => '',
								'is_visible'   => 1,
								'is_variation' => 1,
								'is_taxonomy'  => 1,
							),
						);

						if ( get_post_meta( $product_id, '_product_attributes', true ) ) {
							$metas = get_post_meta( $product_id, '_product_attributes', true );
						} else {
							$metas = array();
						}

						$metas[ $key ] = $attr[ $key ];
						update_post_meta( $product_id, '_product_attributes', $metas );

					}
				}
			}

			/*Aggiornamento meta lookup table*/
			$lookup_data = array(
				'product_id'     => $product_id,
				'sku'            => $sku,
				'virtual'        => 0,
				'downloadable'   => 0,
				'min_price'      => $args['meta_input']['_price'],
				'max_price'      => $args['meta_input']['_price'],
				'onsale'         => $on_sale,
				'stock_quantity' => $stock,
				'stock_status'   => $stock_status,
				'rating_count'   => 0,
				'average_rating' => 0.00,
				'total_sales'    => 0,

			);

			new WCIFD_Product_Meta_Lookup( $lookup_data );

		}
	} else {

		/*Non aggiornare il prodotto se nel cestino*/
		$status = 1 === intval( $deleted_products ) ? 'trash' : '';

        $wc_product = wc_get_product( $id );

		if ( $wc_product->get_status() !== $status ) {

			/*Verifico se i backorders sono attivati*/
			if ( 'outofstock' === $stock_status ) {
				if ( in_array( $wc_product->get_backorders(), array( 'yes', 'notify' ) ) ) {
					$stock_status = 'onbackorder';
				}
			}

            $wc_product->set_sku( $sku );
            $wc_product->set_tax_status( $tax_status );
            $wc_product->set_tax_class( $tax_class );
            $wc_product->set_stock_quantity( $stock );
            $wc_product->set_manage_stock( $manage_stock );
            $wc_product->set_stock_status( $stock_status );
            $wc_product->set_catalog_visibility( 'visible' );
            $wc_product->set_regular_price( $regular_price );
            $wc_product->set_price( $regular_price );
            $wc_product->set_width( $width );
            $wc_product->set_height( $height );
            $wc_product->set_length( $length );
            $wc_product->set_weight( $weight );
            $wc_product->update_meta_data( '_wcifd-um', $um );

			if ( $sale_price ) {
                $wc_product->set_sale_price( $sale_price );
                $wc_product->set_price( $sale_price );
				/* $args['_sell_price'] = $sale_price; */
			} else {
                $wc_product->set_sale_price( '' );
			}

			/*Nome prodotto*/
			if ( ! get_option( 'wcifd-exclude-title' ) ) {
                $wc_product->set_name( $title );
			}

			/*URL prodotto*/
			if ( ! get_option( 'wcifd-exclude-url' ) ) {
				$wc_product->set_slug( sanitize_title_with_dashes( wp_strip_all_tags( $title ) ) );
			}

			/*Descrizione prodotto*/
			if ( ! get_option( 'wcifd-exclude-description' ) ) {
				$wc_product->set_description( $description );
				$wc_product->set_short_description( $short_description );
			}

			if ( $variants ) {
				wc_delete_product_transients( $id );
				wp_cache_delete( 'alloptions', 'options' );
			}

			/*WooCommerce Role Based Price*/
			if ( is_array( $wc_rbp ) && ! empty( $wc_rbp ) ) {

                $role_based_price = array();

				foreach ( $wc_rbp as $role => $price_types ) {

					foreach ( $price_types as $key => $value ) {

						$wc_rbp_price = wcifd_get_list_price( $product, $value, $tax_included );

						if ( $wc_rbp_price ) {

							update_post_meta( $id, '_enable_role_based_price', 1 );

                            /* Add the single role price to the array */
                            $role_based_price[ $role ][ $key ] = $wc_rbp_price;

						}
					}

                    /* Update role based price */
                    update_post_meta( $id, '_role_based_price', $role_based_price );

					if ( $variants && function_exists( 'wc_rbp_delete_variation_data' ) ) {

						wc_rbp_delete_variation_data( $id, $role );

					}
				}
			}

			/* $product_id = wp_update_post( $args, true ); */

			/*Aggiornamento prodotto*/
            $product_id = $wc_product->save();

			if ( is_wp_error( $product_id ) ) {

				error_log( 'WCIFD ERROR | Aggiornamento prodotto | Sku: ' . $sku . ' | ' . print_r( $product_id->get_error_message(), true ) );

				return;
            }

		} else {

			return;

		}
	}

	/*Categorie prodotto*/
	if ( $category ) {

		/*Categoria*/
		$category_term = wcifd_add_taxonomy_term( $product_id, $category, 0 );

		if ( $sub_category && ! is_wp_error( $category_term ) && isset( $category_term['term_id'] ) ) {

			$more_terms = array();

			/*Prima sottocategoria*/
			$more_terms[1] = wcifd_add_taxonomy_term( $product_id, $sub_category, $category_term['term_id'], true );

			/*Sottocategorie successive*/
			for ( $i = 2; $i < 10; $i++ ) {
				$sub_name = 'Subcategory' . $i;
				if ( isset( $product[ $sub_name ] ) ) {

					$more_terms[ $i ] = wcifd_add_taxonomy_term( $product_id, $product[ $sub_name ], $more_terms[ $i - 1 ]['term_id'], true );

				}
			}
		}
	}

	/*Salvo le informazioni relative all'immagine se presente*/
	if ( get_option( 'wcifd-import-images' ) ) {

		if ( $image_file_name ) {

			/*Aggiungo i dati temporanei alla tabella dedicata per l'abbinamento immagine/ prodotto*/
			$temp->wcifd_add_temporary_image( $hash, $product_id, $image_file_name );

		} else {

			/*Rimuovo immagine prodotto*/
			if ( has_post_thumbnail( $product_id ) ) {
				$attachment_id = get_post_thumbnail_id( $product_id );
				wp_delete_attachment( $attachment_id, true );
			}
		}
	}

	/*Attributi disponibili per il prodotto*/
	$attributes = get_post_meta( $product_id, '_product_attributes', true ) ? get_post_meta( $product_id, '_product_attributes', true ) : array();

	/*Attributi aggiuntivi*/
	$more_attributes = array(
		'producer'         => $producer_name,
		'supplier'         => $supplier_name,
		'sup-product-code' => $sup_product_code,
	);

	foreach ( $more_attributes as $key => $value ) {

		if ( $value ) {

			$is_visible = get_option( 'wcifd-display-' . $key ) ? get_option( 'wcifd-display-' . $key ) : '0';

			wp_set_object_terms( $product_id, array( $value ), 'pa_' . $key );

			$attributes[ 'pa_' . $key ] = array(
				'name'         => 'pa_' . $key,
				'value'        => '',
				'is_visible'   => $is_visible,
				'is_variation' => '0',
				'is_taxonomy'  => '1',
			);

		} else {

			unset( $attributes[ 'pa_' . $key ] );

		}
	}

	/*Custom fields*/
	$tags = array();

	for ( $i = 1; $i < 5; $i++ ) {

		$field_name   = 'CustomField' . $i;
		$pa_name      = 'pa_' . strtolower( $field_name );
		$custom_field = isset( $product[ $field_name ] ) ? $product[ $field_name ] : '';

		if ( $custom_field ) {

			$fields_options = get_option( 'wcifd-custom-fields' );
			$import         = isset( $fields_options[ $i ]['import'] ) ? $fields_options[ $i ]['import'] : false;
			$cf_name        = isset( $fields_options[ $i ]['name'] ) ? $fields_options[ $i ]['name'] : false;
			$append         = isset( $fields_options[ $i ]['append'] ) ? $fields_options[ $i ]['append'] : false;
			$split          = isset( $fields_options[ $i ]['split'] ) ? $fields_options[ $i ]['split'] : false;
			$is_visible     = isset( $fields_options[ $i ]['display'] ) ? $fields_options[ $i ]['display'] : '0';

			if ( 'attribute' === $import ) {

				/* Remove tag */
				if ( term_exists( $custom_field, 'product_tag' ) ) {

					wp_remove_object_terms( $product_id, array( $custom_field ), 'product_tag' );

				}

				if ( $split ) {

					$values = array_map( 'trim', explode( ',', $custom_field ) );

					if ( is_array( $values ) ) {

						/* Set attribute */
						$set_attr2 = wp_set_object_terms( $product_id, $values, $pa_name );

					}
				} else {

					/* Set attribute */
					$set_attr = wp_set_object_terms( $product_id, array( $custom_field ), $pa_name );

				}

				$attributes[ $pa_name ] = array(
					'name'         => $pa_name,
					'value'        => '',
					'is_visible'   => $is_visible,
					'is_variation' => '0',
					'is_taxonomy'  => '1',
				);

			} elseif ( 'tag' === $import ) {

				/* Remove attribute */
				if ( isset( $attributes[ $pa_name ] ) ) {
					unset( $attributes[ $pa_name ] );
				}

				wp_remove_object_terms( $product_id, array( $custom_field ), $pa_name );

				/* Set tag */
				$tags[] = $custom_field;

			} else {

				/* Remove all */
				if ( isset( $attributes[ $pa_name ] ) ) {
					unset( $attributes[ $pa_name ] );
				}

				wp_remove_object_terms( $product_id, array( $custom_field ), $pa_name );
				wp_remove_object_terms( $product_id, array( $custom_field ), 'product_tag' );

			}
		} else {

			/* Remove all */
			unset( $attributes[ $pa_name ] );
			wp_remove_object_terms( $product_id, array( $custom_field ), $pa_name );
			wp_remove_object_terms( $product_id, array( $custom_field ), 'product_tag' );

		}
	}

	/* Update tags */
	if ( ! empty( $tags ) ) {

		wp_set_object_terms( $product_id, $tags, 'product_tag', $append );

	}

	update_post_meta( $product_id, '_product_attributes', $attributes );

	/*Variabili di prodotto*/
	if ( $variants ) {

		/*Aggiornamento prodotto padre*/
		wp_set_object_terms( $product_id, 'variable', 'product_type' );

		$avail_colors = array();
		$avail_sizes  = array();

		$v = 1;

		/*Definisco l'array delle variazioni*/
		$variants_array = $variants;
		if ( isset( $variants['Variant'][0] ) && is_array( $variants['Variant'][0] ) ) {
			$variants_array = $variants['Variant'];
		}

		/* Verifico opzione aggiornamento prezzi delle variazioni di prodotto */
		$exclude_variations_prices = get_option( 'wcifd-products-variations-prices' );

		foreach ( $variants_array as $variant ) {

			$barcode      = isset( $variant['Barcode'] ) ? $variant['Barcode'] : '';
			$var_id       = wcifd_search_product( $barcode );
			$in_stock     = isset( $variant['AvailableQty'] ) ? $variant['AvailableQty'] : '';
			$man_stock    = 'yes';
			$stock_status = ( $in_stock ) ? 'instock' : 'outofstock';

			/*Verifico se i backorders sono attivati*/
			if ( 'outofstock' === $stock_status ) {
				$backorders = get_post_meta( $var_id, '_backorders', true );
				if ( 'yes' === $backorders || 'notify' === $backorders ) {
					$stock_status = 'onbackorder';
				}
			}

			/*Attributi*/
			$size  = isset( $variant['Size'] ) ? $variant['Size'] : '-';
			$color = isset( $variant['Color'] ) ? $variant['Color'] : '-';

			/*Aggiunta nuova taglia*/
			if ( '-' !== $size && ! in_array( $size, $avail_sizes, true ) ) {
				$avail_sizes[] = $size;
			}

			/*Aggiunta nuovo colore*/
			if ( '-' !== $color && ! in_array( $color, $avail_colors, true ) ) {
				$avail_colors[] = $color;
			}

			/*Post_meta della variazione di prodotto*/
			$meta_input = array(
				'_sku'          => $barcode,
				'_stock'        => $in_stock,
				'_stock_status' => $stock_status,
				'_manage_stock' => $man_stock,
			);

			if ( ! $var_id || ( $var_id && ! $exclude_variations_prices ) ) {

				$meta_input['_regular_price'] = $regular_price;

				if ( $sale_price ) {
					$meta_input['_sale_price'] = $sale_price;
					$meta_input['_sell_price'] = $sale_price;
					$meta_input['_price']      = $sale_price;
				} else {
					$meta_input['_sale_price'] = '';
					$meta_input['_sell_price'] = $regular_price;
					$meta_input['_price']      = $regular_price;
				}

				/*WooCommerce Role Based Price*/
				if ( is_array( $wc_rbp ) && ! empty( $wc_rbp ) ) {

					foreach ( $wc_rbp as $role => $price_types ) {
						foreach ( $price_types as $key => $value ) {

							$wc_rbp_price = wcifd_get_list_price( $product, $value, $tax_included );

							if ( $wc_rbp_price ) {

								$meta_input['_enable_role_based_price']           = 1;
								$meta_input['_role_based_price'][ $role ][ $key ] = $wc_rbp_price;

							}
						}
					}
				}
			}

			/*Aggiunta attributo ai post_meta della variazione*/
			if ( $avail_colors ) {
				$meta_input['attribute_pa_color'] = sanitize_title( $color );
			}
			if ( $avail_sizes ) {
				$meta_input['attribute_pa_size'] = sanitize_title( $size );
			}

			/*Aggiunta peso e misure*/
			if ( $weight ) {
				$meta_input['_weight'] = $weight;
			}

			if ( $length ) {
				$meta_input['_length'] = $length;
			}

			if ( $width ) {
				$meta_input['_width'] = $width;
			}

			if ( $height ) {
				$meta_input['_height'] = $height;
			}

			if ( ! $var_id ) {

				/* Se impostato dall'admin, non creare nuove variazioni se non disponibili a magazzino */
				if ( $products_not_available && 1 > $in_stock ) {
					continue;
				}

				/*Aggiunta nuova variazione*/
				$var_args = array(
					'post_author'  => $author,
					'post_name'    => 'danea-product-' . $product_id . '-variation-' . ( $v++ ),
					'post_type'    => 'product_variation',
					'post_parent'  => $product_id,
					'post_content' => $description,
					'post_status'  => 'publish',
					'meta_input'   => $meta_input,

				);

				/* Inserimento nuova variazione */
				$var_id = wp_insert_post( $var_args, true );

				if ( is_wp_error( $var_id ) ) {

					error_log( 'WCIFD ERROR | Inserimento variazione prodotto | Sku: ' . $barcode . ' | ' . print_r( $var_id->get_error_message(), true ) );

					return;

				}

				/*Aggiornamento meta lookup table*/
				$lookup_data = array(
					'product_id'     => $var_id,
					'sku'            => $barcode,
					'min_price'      => $meta_input['_price'],
					'max_price'      => $meta_input['_price'],
					'onsale'         => $on_sale,
					'stock_quantity' => $in_stock,
					'stock_status'   => $stock_status,
				);

				new WCIFD_Product_Meta_Lookup( $lookup_data );

			} else {

				/*Aggiornamento variazione*/
				if ( 'trash' !== get_post_status( $var_id ) ) {

					$var_args = array(
						'ID'           => $var_id,
						'post_author'  => $author,
						'post_name'    => 'danea-product-' . $product_id . '-variation-' . ( $v++ ),
						'post_type'    => 'product_variation',
						'post_parent'  => $product_id,
						'post_content' => $description,
						'post_status'  => 'publish',
						'meta_input'   => $meta_input,

					);

					/* Aggiornamento variazione */
					$var_id = wp_update_post( $var_args );

					if ( is_wp_error( $var_id ) ) {

						error_log( 'WCIFD ERROR | Aggiornamento variazione prodotto | Sku: ' . $barcode . ' | ' . print_r( $var_id->get_error_message(), true ) );

						return;

					}

					/*Aggiornamento meta lookup table*/
					$lookup_data = array(
						'product_id'     => $var_id,
						'sku'            => $barcode,
						'onsale'         => $on_sale,
						'stock_quantity' => $in_stock,
						'stock_status'   => $stock_status,
					);

					/* Solo se non attivata l'esclusione dei prezzi della variazioni */
					if ( ! $exclude_variations_prices ) {
						$lookup_data['min_price'] = $meta_input['_price'];
						$lookup_data['max_price'] = $meta_input['_price'];
					}

					new WCIFD_Product_Meta_Lookup( $lookup_data, 'update' );

				}
			}

			if ( $var_id ) {

				/*Termine di tassonomia (attributo) assegnato alla variazione di prodotto*/
				if ( $avail_colors ) {
					wp_set_object_terms( $var_id, $avail_colors, 'pa_color' );
				}
				if ( $avail_sizes ) {
					wp_set_object_terms( $var_id, $avail_sizes, 'pa_size' );
				}

				/*Attributi della variazione di prodotto*/
				$attr = array();

				if ( $color ) {
					$attr['pa_color'] = array(
						'name'         => sanitize_title( $color ),
						'value'        => '',
						'is_visible'   => 1,
						'is_variation' => 1,
						'is_taxonomy'  => 1,
					);
				}

				if ( $size ) {
					$attr['pa_size'] = array(
						'name'         => sanitize_title( $size ),
						'value'        => '',
						'is_visible'   => 1,
						'is_variation' => 1,
						'is_taxonomy'  => 1,
					);
				}

				if ( $attr ) {
					update_post_meta( $var_id, '_product_attributes', $attr );
				}
			}
		}

		/*Attributi disponibili per il prodotto padre*/
		$attributes = get_post_meta( $product_id, '_product_attributes', true ) ? get_post_meta( $product_id, '_product_attributes', true ) : array();

		if ( $avail_colors ) {
			wp_set_object_terms( $product_id, $avail_colors, 'pa_color' );

			$attributes['pa_color'] = array(
				'name'         => 'pa_color',
				'value'        => '',
				'position'     => 0,
				'is_visible'   => 1,
				'is_variation' => 1,
				'is_taxonomy'  => 1,
			);
		}

		if ( $avail_sizes ) {
			wp_set_object_terms( $product_id, $avail_sizes, 'pa_size' );

			$attributes['pa_size'] = array(
				'name'         => 'pa_size',
				'value'        => '',
				'position'     => 1,
				'is_visible'   => 1,
				'is_variation' => 1,
				'is_taxonomy'  => 1,
			);
		}

		update_post_meta( $product_id, '_product_attributes', $attributes );

	}

	/*Cancello i dati temporanei dalla tabella dedicata*/
	$temp->wcifd_delete_temporary_data( $hash );

}
add_action( 'wcifd_import_product_event', 'wcifd_import_single_product', 10, 8 );
