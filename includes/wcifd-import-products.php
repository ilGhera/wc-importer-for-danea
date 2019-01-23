<?php
/**
 * Importazione prodotti da file csv
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @version 1.1.0
 */
function wcifd_products() {

	if ( isset( $_POST['products-import'] ) && wp_verify_nonce( $_POST['wcifd-products-nonce'], 'wcifd-products-import' ) ) {

		/*Change execution time limit*/
		ini_set( 'max_execution_time', 0 );

		$tax_included = get_option( 'wcifd-tax-included' );
		$use_suppliers = get_option( 'wcifd-use-suppliers' );

		$update_products = sanitize_text_field( $_POST['update-products'] );
		update_option( 'wcifd-update-products', $update_products );

		$get_regular_price_list = get_option( 'wcifd-regular-price-list' );
		$get_sale_price_list = get_option( 'wcifd-sale-price-list' );
		$size_type = get_option( 'wcifd-size-type' );
		$weight_type = get_option( 'wcifd-weight-type' );

		$file = $_FILES['products-list']['tmp_name'];
		$rows = array_map( 'str_getcsv', file( $file ) );
		$header = array_shift( $rows );
		$products = array();
		foreach ( $rows as $row ) {
			$products[] = array_combine( $header, $row );
		}

		$i = 0;
		$u = 0;
		foreach ( $products as $product ) {
			$sku = $product['Cod.'];
			$title = $product['Descrizione'];
			$description = $product['Descriz. web (Sorgente HTML)'];
			$product_type = $product['Tipologia'];
			$category = $product['Categoria'];
			$sub_category = $product['Sottocategoria'];
			$tax  = $product['Cod. Iva'];
			if ( $tax_included == 0 ) {
				$regular_price = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $get_regular_price_list ] ) );
				$sale_price   = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $get_sale_price_list ] ) );

			} else {
				$regular_price = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $get_regular_price_list . ' (ivato)' ] ) );
				$sale_price    = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $get_sale_price_list . ' (ivato)' ] ) );
			}
			$supplier_id = $product['Cod. fornitore'];
			$supplier = $product['Fornitore'];
			$parent_sku        = null;
			$var_attributes    = null;
			$variable_product  = null;
			$parent_product_id = null;

			if ( $product['Note'] ) {
				$notes = json_decode( $product['Note'], true );

				/*Parent sku*/
				$parent_sku = ( $notes['parent_sku'] ) ? $notes['parent_sku'] : null;
				$parent_product_id = wcifd_search_product( $parent_sku );
				$var_attributes = $notes['var_attributes'];

				/*Prodotto variabile*/
				if ( $notes['product_type'] == 'variable' && $notes['attributes'] ) {
					$variable_product = true;
					$imported_attributes = $notes['attributes'];
				}
			}

			/*Post status - Le variazioni devono essere pubblicate*/
			$status = ( $var_attributes ) ? 'publish' : 'draft';

			/*Verifico la presenza del prodotto*/
			$id   = wcifd_search_product( $sku );
			$type = ( wp_get_post_parent_id( $id ) || $parent_sku ) ? 'product_variation' : 'product';

			/*Gestione magazzino*/
			$stock = $product['Q.tà giacenza'];
			$total_sales = $product['Tot. q.tà scaricata'];
			$manage_stock = ( $product_type == 'Art. con magazzino' || $product_type == 'Art. con magazzino (taglie/colori)' ) ? 'yes' : 'no';

			/*Dimensione prodotto*/
			$length = wcifd_get_product_size( $product, $size_type, 'z', true );
			$width  = wcifd_get_product_size( $product, $size_type, 'x', true );
			$height = wcifd_get_product_size( $product, $size_type, 'y', true );

			/*Peso del prodotto*/
			if ( $weight_type == 'gross-weight' ) {
				$weight = $product['Peso lordo'];
			} else {
				$weight = $product['Peso netto'];
			}

			/*Autore del post come fornitore*/
			$author = ( $use_suppliers == 1 && $supplier_id ) ? $supplier_id : get_option( 'wcifd-current-user' );

			/*Variazione taglia e colore di Danea*/
			if ( $product_type == 'Art. con magazzino (taglie/colori)' ) {
				update_post_meta( $id, 'wcifd-danea-size-color', 1 );
			}

			/*Verifica classe di tassazione*/
			$tax_status = 'none';
			$tax_class = '';
			if ( $tax ) {
				$tax_status = 'taxable';
				$tax_class = wcifd_get_tax_rate_class( $tax );
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
						'_sku'           => $sku,
						'_tax_status'    => $tax_status,
						'_tax_class'     => $tax_class,
						'_stock'         => $stock,
						'_manage_stock'  => $manage_stock,
						'_visibility'    => 'visible',
						'_regular_price' => $regular_price,
						'_price'         => $regular_price,
						'_sell_price'    => $regular_price,
						'_width'         => $width,
						'_height'        => $height,
						'_length'        => $length,
						'_weight'        => $weight,
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

					$i++;

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
				} else {

					$i++;
				}
			} elseif ( $update_products == 1 && wcifd_search_product( $sku ) ) {

				/*Non aggiornare il prodotto se nel cestino*/
				if ( get_post_status( $id ) != 'trash' ) {
					$u++;
					$args = array(
						'ID'               => $id,
						'post_status'      => get_post_status( $id ),
						'post_author'      => $author,
						'post_title'       => wp_strip_all_tags( $title ),
						'post_name'        => sanitize_title_with_dashes( wp_strip_all_tags( $title ) ),
						'post_type'        => $type,
						'meta_input'       => array(
							'_sku'           => $sku,
							'_tax_status'    => $tax_status,
							'_tax_class'     => $tax_class,
							'_stock'         => $stock,
							'_manage_stock'  => $manage_stock,
							'total_sales'    => $total_sales,
							'_visibility'    => 'visible',
							'_regular_price' => $regular_price,
							'_price'         => $regular_price,
							'_sell_price'    => $regular_price,
							'_width'         => $width,
							'_height'        => $height,
							'_length'        => $length,
							'_weight'        => $weight,
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

					/*Aggiornamento prodotto*/
					$product_id = wp_update_post( $args );
				}
			}

			/*Categorie prodotto*/
			if ( $category ) {

				/*Categoria*/
				$cat_term = term_exists( $category, 'product_cat' );

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
		}
		$output  = '<div id="message" class="updated"><p>';
		$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';
		$output .= sprintf( __( 'Products imported: %1$d<br>Products updated: %2$d', 'wcifd' ), $i, $u );
		$output .= '</p></div>';
		echo $output;

	}

}
