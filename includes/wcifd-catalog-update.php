<?php
/**
 * Aggiornaemnto del catalogo prodotti
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @version 1.1.0
 *
 * @param  file $file l'xml proveniente da Danea Easyfatt
 */
function wcifd_catalog_update( $file ) {

	/*Opzioni admin*/
	$get_regular_price_list = get_option( 'wcifd-regular-price-list' );
	$get_sale_price_list = get_option( 'wcifd-sale-price-list' );
	$size_type = get_option( 'wcifd-size-type' );
	$weight_type = get_option( 'wcifd-weight-type' );

	$results = simplexml_load_file( $file );

	/*Verifica che si tratti di un aggiornamento o dell'intero catalogo prodotti*/
	$products = $results->Products ? $results->Products : $results->UpdatedProducts;

	foreach ( $products->children() as $product ) {
		$sku = $product->Code;
		$title = $product->Description;
		$description = ( $product->DescriptionHtml != '' ) ? $product->DescriptionHtml : $title;
		$category = $product->Category;
		$sub_category = $product->Subcategory;
		$tax  = $product->Vat;
		$stock = $product->AvailableQty;
		$size_um = $product->SizeUm;
		$weight_um = $product->WeightUm;
		$parent_sku        = null;
		$var_attributes    = null;
		$variable_product  = null;
		$parent_product_id = null;

		if ( $product->Notes ) {
			$notes = json_decode( $product->Notes, true );
			if ( is_array( $notes ) ) {

				/*Parent sku*/
				$parent_sku = array_key_exists( 'parent_sku', $notes ) ? $notes['parent_sku'] : null;
				if ( $parent_sku ) {
					$parent_product_id = wcifd_search_product( $parent_sku );
				}
				$var_attributes = array_key_exists( 'var_attributes', $notes ) ? $notes['var_attributes'] : null;

				/*Prodotto variabile*/
				if ( $notes['product_type'] == 'variable' && $notes['attributes'] ) {
					$variable_product = true;
					$imported_attributes = $notes['attributes'];
				}
			}
		}

		/*Post status - Le variazioni devono essere pubblicate*/
		$new_products_status = get_option( 'wcifd-publish-new-products' ) ? 'publish' : 'draft';
		$status = ( $var_attributes ) ? 'publish' : $new_products_status;

		/*Verifico la presenza del prodotto*/
		$id   = wcifd_search_product( wcifd_json_decode( $sku ) );
		$type = ( wp_get_post_parent_id( $id ) || $parent_sku ) ? 'product_variation' : 'product';

		/*Gestione magazzino*/
		$manage_stock = ( $product->ManageWarehouse == 'true' ) ? 'yes' : 'no';
		$stock_status = ( $stock >= 1 ) ? 'instock' : 'outofstock';

		/*Dimensione prodotto*/
		$length = wcifd_get_product_size( $product, $size_type, 'z' );
		$width  = wcifd_get_product_size( $product, $size_type, 'x' );
		$height = wcifd_get_product_size( $product, $size_type, 'y' );

		/*Peso del prodotto*/
		if ( $weight_type == 'gross-weight' ) {
			$weight = $product->GrossWeight;
		} else {
			$weight = $product->NetWeight;
		}

		/*Autore del post come fornitore*/
		$author = ( get_option( 'wcifd-use-suppliers' ) == 1 && $product->SupplierCode ) ? $product->SupplierCode : get_option( 'wcifd-current-user' );

		/*Imposte incluse*/
		$tax_included = get_option( 'wcifd-tax-included' );

		/*Prezzo di listino e prezzo scontato*/
		$regular_price = wcifd_get_list_price( $product, $get_regular_price_list, $tax_included );
		$sale_price    = wcifd_get_list_price( $product, $get_sale_price_list, $tax_included );

		/*Variazione taglia e colore di Danea*/
		$variants = $product->Variants;
		if ( $variants ) {
			update_post_meta( $id, 'wcifd-danea-size-color', 1 );
		}

		/*Verifica classe di tassazione*/
		$tax_status = 'none';
		$tax_class = '';
		$perc = wcifd_json_decode( $tax['Perc'] );
		$class = wcifd_json_decode( $tax['Class'] );
		if ( $perc != 0 || $class != 'Escluso' ) {
			$tax_status = 'taxable';
			$tax_class = wcifd_get_tax_rate_class( wcifd_json_decode( $tax ), strval( $perc ) );
		}

		/*Inizio aggiornamento prodotto o creazione se non presente*/
		if ( ! $id ) {

			$args = array(
				'post_author'      => $author,
				'post_title'       => wp_strip_all_tags( $title ),
				'post_type'        => $type,
				'post_parent'      => $parent_product_id,
				'post_content'     => $description,
				'post_status'      => $status,
				'meta_input'       => array(
					'_sku'                => wcifd_json_decode( $sku ),
					'_tax_status'         => $tax_status,
					'_tax_class'          => $tax_class,
					'_stock'              => wcifd_json_decode( $stock ),
					'_manage_stock'       => $manage_stock,
					'_stock_status'       => $stock_status,
					'_visibility'         => 'visible',
					'_regular_price'      => wcifd_json_decode( $regular_price ),
					'_price'              => wcifd_json_decode( $regular_price ),
					'_sell_price'         => wcifd_json_decode( $regular_price ),
					'_width'              => wcifd_json_decode( $width ),
					'_height'             => wcifd_json_decode( $height ),
					'_length'             => wcifd_json_decode( $length ),
					'_weight'             => wcifd_json_decode( $weight ),

				),
			);

			if ( $sale_price ) {
				$args['meta_input']['_sale_price'] = wcifd_json_decode( $sale_price );
				$args['meta_input']['_sell_price'] = wcifd_json_decode( $sale_price );
				$args['meta_input']['_price'] = wcifd_json_decode( $sale_price );
			} else {
				$args['meta_input']['_sale_price'] = '';
			}

			/*Descrizione breve*/
			if ( get_option( 'wcifd-short-description' ) ) {
				$args['post_excerpt'] = wcifd_get_short_description( $description );
			}

			/*Inserimento nuovo prodotto*/
			$product_id = wp_insert_post( $args );

			if ( $variable_product ) {

				/*Aggiornamento prodotto padre*/
				wp_set_object_terms( $product_id, 'variable', 'product_type' );

				if ( $imported_attributes ) {
					foreach ( $imported_attributes as $key => $value ) {

						$attr = array(
							$key => array(
								'name'         => $key,
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

						wp_set_object_terms( $product_id, $value, $key );
					}
				}

			} elseif ( $parent_sku && $var_attributes ) {

				foreach ( $var_attributes as $key => $value ) {
					$attr = array(
						$key => array(
							'name'         => $value,
							'value'        => '',
							'is_visible'   => 1,
							'is_variation' => 1,
							'is_taxonomy'  => 1,
						),
					);
					update_post_meta( $product_id, 'attribute_' . $key, $value );

					if ( get_post_meta( $product_id, '_product_attributes', true ) ) {
						$metas = get_post_meta( $product_id, '_product_attributes', true );
					} else {
						$metas = array();
					}

					$metas[ $key ] = $attr[ $key ];
					update_post_meta( $product_id, '_product_attributes', $metas );
				}
			}
			
		} else {

			/*Non aggiornare il prodotto se nel cestino*/
			if ( get_post_status( $id ) != 'trash' ) {
				$args = array(
					'ID'               => $id,
					'post_status'      => get_post_status( $id ),
					'post_author'      => $author,
					'post_title'       => wp_strip_all_tags( $title ),
					'post_name'        => sanitize_title_with_dashes( wp_strip_all_tags( $title ) ),
					'post_type'        => $type,
					'meta_input'       => array(
						'_sku'                => wcifd_json_decode( $sku ),
						'_tax_status'         => $tax_status,
						'_tax_class'          => $tax_class,
						'_stock'              => wcifd_json_decode( $stock ),
						'_manage_stock'       => $manage_stock,
						'_stock_status'       => $stock_status,
						'_visibility'         => 'visible',
						'_regular_price'      => wcifd_json_decode( $regular_price ),
						'_price'              => wcifd_json_decode( $regular_price ),
						'_sell_price'         => wcifd_json_decode( $regular_price ),
						'_width'              => wcifd_json_decode( $width ),
						'_height'             => wcifd_json_decode( $height ),
						'_length'             => wcifd_json_decode( $length ),
						'_weight'             => wcifd_json_decode( $weight ),
					),

				);

				if ( $sale_price ) {
					$args['meta_input']['_sale_price'] = wcifd_json_decode( $sale_price );
					$args['meta_input']['_sell_price'] = wcifd_json_decode( $sale_price );
					$args['meta_input']['_price'] = wcifd_json_decode( $sale_price );
				} else {
					$args['meta_input']['_sale_price'] = '';
				}

				/*Descrizione prodotto*/
				if ( ! get_option( 'wcifd-exclude-description' ) ) {
					$args['post_content'] = $description;

					/*Descrizione breve*/
					if ( get_option( 'wcifd-short-description' ) ) {
						$args['post_excerpt'] = wcifd_get_short_description( $description );
					}
				}

				if ( $variants ) {
					$transient_product_meta_key = '_transient_wc_var_prices_' . $id;
					update_option( $transient_product_meta_key, strtotime( '-12 hours' ) );
					wp_cache_delete( 'alloptions', 'options' );
				}

				/*Aggiornamento prodotto*/
				$product_id = wp_update_post( $args );

			}
		}

		/*Categorie prodotto*/
		if ( $category ) {

			/*Categoria*/
			$cat_term = term_exists( $category, 'product_cat', 0 );

			if ( $cat_term === 0 || $cat_term === null ) {
				$cat_term = wp_insert_term( $category, 'product_cat' );
			}
			wp_set_object_terms( $product_id, intval( $cat_term['term_id'] ), 'product_cat', true );

			if ( $sub_category ) {

				/*Sottocategoria*/
				$subcat_term = term_exists( $sub_category, 'product_cat', $cat_term['term_id'] );

				if ( $subcat_term === 0 || $subcat_term === null ) {
					$subcat_term = wp_insert_term( $sub_category, 'product_cat', array( 'parent' => $cat_term['term_id'] ) );
				}

				if ( ! is_wp_error( $subcat_term ) ) {
					wp_set_object_terms( $product_id, intval( $subcat_term['term_id'] ), 'product_cat', true );
				}
			}
		}

		/*Variabili di prodotto*/
		if ( $variants ) {

			/*Aggiornamento prodotto padre*/
			wp_set_object_terms( $product_id, 'variable', 'product_type' );

			$avail_colors = array();
			$avail_sizes = array();

			$v = 1; //Variant loop
			foreach ( $variants->children() as $variant ) {

				$barcode  = wcifd_json_decode( $variant->Barcode );
				$in_stock = wcifd_json_decode( $variant->AvailableQty );

				$man_stock = 'yes';
				$stock_status = ( $in_stock ) ? 'instock' : 'outofstock';

				/*Attributi*/
				$size     = wcifd_json_decode( $variant->Size );
				$color    = wcifd_json_decode( $variant->Color );

				/*Aggiunta nuova taglia*/
				if ( $size != '-' && ! in_array( $size, $avail_sizes ) ) {
					$avail_sizes[] = $size;
				}

				/*Aggiunta nuovo coloro*/
				if ( $color != '-' && ! in_array( $color, $avail_colors ) ) {
					$avail_colors[] = $color;
				}

				/*Post_meta della variazione di prodotto*/
				$meta_input = array(
					'_sku'               => $barcode,
					'_stock'             => $in_stock,
					'_stock_status'      => $stock_status,
					'_manage_stock'      => $man_stock,
					'_regular_price'     => wcifd_json_decode( $regular_price ),
					'_price'             => wcifd_json_decode( $regular_price ),
					'_sell_price'        => wcifd_json_decode( $regular_price ),
				);

				if ( $sale_price ) {
					$meta_input['_sale_price'] = wcifd_json_decode( $sale_price );
					$meta_input['_sell_price'] = wcifd_json_decode( $sale_price );
					$meta_input['_price'] = wcifd_json_decode( $sale_price );
				} else {
					$meta_input['_sale_price'] = '';
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
					$meta_input['_weight'] = wcifd_json_decode( $weight );
				}

				if ( $length ) {
					$meta_input['_length'] = wcifd_json_decode( $length );
				}

				if ( $width ) {
					$meta_input['_width'] = wcifd_json_decode( $width );
				}

				if ( $height ) {
					$meta_input['_height'] = wcifd_json_decode( $height );
				}

				if ( ! wcifd_search_product( $barcode ) ) {

					/*Aggiunta nuova variazione*/
					$var_args = array(
						'post_author'      => $author,
						'post_name'        => 'danea-product-' . $product_id . '-variation-' . $v++,
						'post_type'        => 'product_variation',
						'post_parent'      => $product_id,
						'post_content'     => $description,
						'post_status'      => 'publish',
						'meta_input'       => $meta_input,

					);

					$var_id = wp_insert_post( $var_args );

				} else {

					/*Aggiornamento variazione*/
					if ( get_post_status( wcifd_search_product( $barcode ) ) != 'trash' ) {

						$var_args = array(
							'ID'               => wcifd_search_product( $barcode ),
							'post_author'      => $author,
							'post_name'        => 'danea-product-' . $product_id . '-variation-' . $v++,
							'post_type'        => 'product_variation',
							'post_parent'      => $product_id,
							'post_content'     => $description,
							'post_status'      => 'publish',
							'meta_input'       => $meta_input,

						);

						$var_id = wp_update_post( $var_args );

					}
				}

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
	}
}
