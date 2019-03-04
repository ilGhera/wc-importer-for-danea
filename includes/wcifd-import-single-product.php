<?php
/**
 * Importazione singolo prodotto
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @version 1.1.4
 *
 * @param  string $product_json       il singolo prodotto dal file xml codificato in json
 * @param  string $regular_price_list il listino prezzi selezionato dall'admin
 * @param  string $sale_price_list    il listino prezzi in offerta selezionato dall'admin
 * @param  string $size_type          misure lorde o nette, come da impostazioni dell'admin
 * @param  string $weight_type        peso lordo o netto, come da impostazioni dell'admin
 * @param  string $tax_attributes     gli attributi del campo Vat codificati in json
 * @param  string $deleted_products   determina se i prodotti nel cestino debbano essere aggiornati o meno
 */
function wcifd_import_single_product( $product_json, $regular_price_list, $sale_price_list, $size_type, $weight_type, $tax_attributes, $deleted_products ) {

	$product           = json_decode( $product_json );
	$sku               = isset( $product->Code ) ? $product->Code : '';
	$title             = isset( $product->Description ) ? $product->Description : '';
	$description       = ( is_string( $product->DescriptionHtml ) ) ? $product->DescriptionHtml : $title;
	$category          = isset( $product->Category ) ? $product->Category : '';
	$sub_category      = isset( $product->Subcategory ) ? $product->Subcategory : '';
	$tax               = isset( $product->Vat ) ? $product->Vat : '';
	$tax_attributes    = json_decode( $tax_attributes, true );
	$stock             = isset( $product->AvailableQty ) ? $product->AvailableQty : '';
	$size_um           = isset( $product->SizeUm ) ? $product->SizeUm : '';
	$weight_um         = isset( $product->WeightUm ) ? $product->WeightUm : '';
	$image_file_name   = isset( $product->ImageFileName ) ? sanitize_title( $product->ImageFileName ) : '';
	$parent_sku        = null;
	$var_attributes    = null;
	$variable_product  = null;
	$parent_product_id = null;

	if ( is_string( $product->Notes ) ) {

		$notes = json_decode( $product->Notes, true );

		if ( is_array( $notes ) ) {

			/*Parent sku*/
			if ( isset( $notes['parent_sku'] ) && $notes['parent_sku'] !== '' ) {
			
				$parent_sku = $notes['parent_sku'];	
			
			} elseif (isset( $notes['parent_id']) && $notes['parent_id'] !== '' ) {

				$parent_sku = $notes['parent_id'];	
			
			}

			if ( $parent_sku ) {
				$parent_product_id = wcifd_search_product( $parent_sku );
			}

			$var_attributes = isset( $notes['var_attributes'] ) ? $notes['var_attributes'] : null;

			/*Prodotto variabile*/
			if ( isset( $notes['product_type'] ) && $notes['product_type'] == 'variable' ) {
				if ( isset( $notes['attributes'] ) ) {
					$variable_product = true;
					$imported_attributes = $notes['attributes'];
				}
			}
		}
	}

	/*Post status - Le variazioni devono essere pubblicate*/
	$new_products_status = get_option( 'wcifd-publish-new-products' ) ? 'publish' : 'draft';
	$status = ( $var_attributes ) ? 'publish' : $new_products_status;

	/*Verifico la presenza del prodotto*/
	$id   = wcifd_search_product( $sku );
	$type = ( wp_get_post_parent_id( $id ) || $parent_sku ) ? 'product_variation' : 'product';

	/*Gestione magazzino*/
	$manage_stock = ( isset( $product->ManageWarehouse ) && $product->ManageWarehouse == 'true' ) ? 'yes' : 'no';
	$stock_status = ( $stock >= 1 ) ? 'instock' : 'outofstock';

	/*Dimensione prodotto*/
	$length = wcifd_get_product_size( $product, $size_type, 'z' );
	$width  = wcifd_get_product_size( $product, $size_type, 'x' );
	$height = wcifd_get_product_size( $product, $size_type, 'y' );

	/*Peso del prodotto*/
	if ( $weight_type == 'gross-weight' ) {
		$weight = isset( $product->GrossWeight ) ? $product->GrossWeight : '';
	} else {
		$weight = isset( $product->NetWeight ) ? $product->NetWeight : '';
	}

	/*Autore del post come fornitore*/
	$author = ( get_option( 'wcifd-use-suppliers' ) == 1 && $product->SupplierCode ) ? $product->SupplierCode : get_option( 'wcifd-current-user' );

	/*Imposte incluse*/
	$tax_included = get_option( 'wcifd-tax-included' );

	/*Prezzo di listino e prezzo scontato*/
	$regular_price = wcifd_get_list_price( $product, $regular_price_list, $tax_included );
	$sale_price    = wcifd_get_list_price( $product, $sale_price_list, $tax_included );

	/*Variazione taglia e colore di Danea*/
	$variants = isset( $product->Variants ) ? $product->Variants : '';
	
	if ( $variants ) {
		update_post_meta( $id, 'wcifd-danea-size-color', 1 );
	}

	/*Verifica classe di tassazione*/
	$tax_status = 'none';
	$tax_class = '';
	$perc = isset( $tax_attributes['@attributes']['Perc'] ) ? $tax_attributes['@attributes']['Perc'] : '';
	$class = isset( $tax_attributes['@attributes']['Class'] ) ? $tax_attributes['@attributes']['Class'] : '';

	if ( '0' !== $perc || ( 'Escluso' !== $class && 'NonSoggetto' !== $class ) ) {
		$tax_status = 'taxable';
		$tax_class = wcifd_get_tax_rate_class( $tax, strval( $perc ) );
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
				'_sku'                => $sku,
				'_tax_status'         => $tax_status,
				'_tax_class'          => $tax_class,
				'_stock'              => $stock,
				'_manage_stock'       => $manage_stock,
				'_stock_status'       => $stock_status,
				'_visibility'         => 'visible',
				'_regular_price'      => $regular_price,
				'_price'              => $regular_price,
				'_sell_price'         => $regular_price,
				'_width'              => $width,
				'_height'             => $height,
				'_length'             => $length,
				'_weight'             => $weight,

			),
		);

		if ( $sale_price ) {
			$args['meta_input']['_sale_price'] = $sale_price;
			$args['meta_input']['_sell_price'] = $sale_price;
			$args['meta_input']['_price'] = $sale_price;
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
		$status = $deleted_products === '1' ? 'trash' : '';

		if ( get_post_status( $id ) !== $status ) {

			/*Verifico se i backorders sono attivati*/
			if ( 'outofstock' === $stock_status ) {
				$backorders = get_post_meta( $id, '_backorders', true );
				if ( 'yes' === $backorders || 'notify' === $backorders ) {
					$stock_status = 'onbackorder';
				}
			}

			$args = array(
				'ID'               => $id,
				'post_status'      => get_post_status( $id ),
				'post_author'      => $author,
				'post_title'       => wp_strip_all_tags( $title ),
				'post_name'        => sanitize_title_with_dashes( wp_strip_all_tags( $title ) ),
				'post_type'        => $type,
				'meta_input'       => array(
					'_sku'                => $sku,
					'_tax_status'         => $tax_status,
					'_tax_class'          => $tax_class,
					'_stock'              => $stock,
					'_manage_stock'       => $manage_stock,
					'_stock_status'       => $stock_status,
					'_visibility'         => 'visible',
					'_regular_price'      => $regular_price,
					'_price'              => $regular_price,
					'_sell_price'         => $regular_price,
					'_width'              => $width,
					'_height'             => $height,
					'_length'             => $length,
					'_weight'             => $weight,
				),

			);

			if ( $sale_price ) {
				$args['meta_input']['_sale_price'] = $sale_price;
				$args['meta_input']['_sell_price'] = $sale_price;
				$args['meta_input']['_price'] = $sale_price;
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

		} else {

			return;

		}
	}

	/*Categorie prodotto*/
	if ( $category ) {

		/*Categoria*/
		$category_term = wcifd_add_taxonomy_term( $product_id, $category, 0 );

		if ( $sub_category && isset( $category_term['term_id'] ) ) {

			$more_terms = array();

			/*Prima sottocategoria*/
			$more_terms[1] = wcifd_add_taxonomy_term( $product_id, $sub_category, $category_term['term_id'], true );

			/*Sottocategorie successive*/
			for ( $i = 2; $i < 10; $i++ ) {
				$sub_name = 'Subcategory' . $i;
				if ( isset( $product->$sub_name ) ) {

					$more_terms[ $i ] = wcifd_add_taxonomy_term( $product_id, $product->$sub_name, $more_terms[ $i - 1 ]['term_id'], true );

				}
			}
		}
	}

	/*Se presente lego l'immagine al prodotto*/
	if ( $image_file_name ) {
		wp_schedule_single_event(
			time() + 10,
			'wcifd_product_image_event',
			array(
				$product_id,
				$image_file_name,
			)
		);
	}

	/*Variabili di prodotto*/
	if ( $variants ) {

		/*Aggiornamento prodotto padre*/
		wp_set_object_terms( $product_id, 'variable', 'product_type' );

		$avail_colors = array();
		$avail_sizes = array();

		$v = 1; //Variant loop
		$variants_array = is_array( $variants->Variant ) ? $variants->Variant : $variants; 

		foreach ( $variants_array as $variant ) {

			$barcode  = isset( $variant->Barcode) ? $variant->Barcode : '';
			$var_id = wcifd_search_product( $barcode );
			$in_stock = isset( $variant->AvailableQty) ? $variant->AvailableQty : '';

			$man_stock = 'yes';
			$stock_status = ( $in_stock ) ? 'instock' : 'outofstock';

			/*Verifico se i backorders sono attivati*/
			if ( 'outofstock' === $stock_status ) {
				$backorders = get_post_meta( $var_id, '_backorders', true );
				if ( 'yes' === $backorders || 'notify' === $backorders ) {
					$stock_status = 'onbackorder';
				}
			}

			/*Attributi*/
			$size     = isset( $variant->Size) ? $variant->Size : '-';
			$color    = isset( $variant->Color) ? $variant->Color : '-';

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
				'_regular_price'     => $regular_price,
				'_price'             => $regular_price,
				'_sell_price'        => $regular_price,
			);

			if ( $sale_price ) {
				$meta_input['_sale_price'] = $sale_price;
				$meta_input['_sell_price'] = $sale_price;
				$meta_input['_price'] = $sale_price;
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
				if ( get_post_status( $var_id ) != 'trash' ) {

					$var_args = array(
						'ID'               => $var_id,
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
add_action( 'wcifd_import_product_event', 'wcifd_import_single_product', 10, 7 );
