<?php
/**
 * Importazione prodotti da file XML/CSV
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.1
 */

/**
 * Importazione prodotti
 *
 * @return void
 */
function wcifd_products() {

	if ( isset( $_POST['products-import'], $_POST['wcifd-products-file-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-products-file-nonce'] ) ), 'wcifd-products-file' ) ) {

		$file      = isset( $_FILES['products-list']['tmp_name'] ) ? sanitize_text_field( wp_unslash( $_FILES['products-list']['tmp_name'] ) ) : null;
		$file_type = isset( $_POST['file-type'] ) ? sanitize_text_field( wp_unslash( $_POST['file-type'] ) ) : null;
		update_option( 'wcifd-file-type', $file_type );

		if ( 'xml' === $file_type ) {

			wcifd_catalog_update( $file );

			$output  = '<div id="message" class="updated"><p>';
			$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';
			$output .= __( 'The import process has started and is running in the background', 'wc-importer-for-danea' );
			$output .= '</p></div>';

			echo wp_kses_post( $output );

		} else {

			/*Change execution time limit*/
			ini_set( 'max_execution_time', 0 );

			$tax_included     = get_option( 'wcifd-tax-included' );
			$use_suppliers    = get_option( 'wcifd-use-suppliers' );
			$deleted_products = get_option( 'wcifd-deleted-products' );

			$get_regular_price_list = get_option( 'wcifd-regular-price-list' );
			$get_sale_price_list    = get_option( 'wcifd-sale-price-list' );
			$size_type              = get_option( 'wcifd-size-type' );
			$weight_type            = get_option( 'wcifd-weight-type' );
			$rows                   = array_map( 'str_getcsv', file( $file ) );
			$header                 = array_shift( $rows );

			$products = array();
			foreach ( $rows as $row ) {
				$products[] = array_combine( $header, $row );
			}

			$p = 0;
			$u = 0;
			$v = 0;
			foreach ( $products as $product ) {
				$sku          = $product['Cod.'];
				$title        = $product['Descrizione'];
				$description  = isset( $product['Descriz. web (Sorgente HTML)'] ) ? $product['Descriz. web (Sorgente HTML)'] : '';
				$product_type = isset( $product['Tipologia'] ) ? $product['Tipologia'] : '';
				$category     = isset( $product['Categoria'] ) ? $product['Categoria'] : '';
				$sub_category = isset( $product['Sottocategoria'] ) ? $product['Sottocategoria'] : '';
				$tax          = isset( $product['Cod. Iva'] ) ? $product['Cod. Iva'] : '';

				if ( 0 === intval( $tax_included ) ) {
					$regular_price = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $get_regular_price_list ] ) );
					$sale_price    = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $get_sale_price_list ] ) );

				} else {
					$regular_price = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $get_regular_price_list . ' (ivato)' ] ) );
					$sale_price    = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $get_sale_price_list . ' (ivato)' ] ) );
				}

				$supplier_id       = isset( $product['Cod. fornitore'] ) ? $product['Cod. fornitore'] : '';
				$supplier          = isset( $product['Fornitore'] ) ? $product['Fornitore'] : '';
				$parent_sku        = null;
				$variable_product  = null;
				$parent_product_id = null;
				$var_attributes    = null;

				if ( $product['Note'] ) {
					$notes = json_decode( $product['Note'], true );

					/*Parent sku*/
					if ( isset( $notes['parent_sku'] ) && '' !== $notes['parent_sku'] ) {

						$parent_sku = $notes['parent_sku'];

					} elseif ( isset( $notes['parent_id'] ) && '' !== $notes['parent_id'] ) {

						$parent_product_id = $notes['parent_id'];

					}

					if ( $parent_sku ) {
						$parent_product_id = wcifd_search_product( $parent_sku );
					}

					$var_attributes = isset( $notes['var_attributes'] ) ? $notes['var_attributes'] : '';
					$var_type       = isset( $notes['product_type'] ) ? $notes['product_type'] : '';

					/*Prodotto variabile*/
					if ( 'variable' === $var_type && $notes['attributes'] ) {
						$variable_product    = true;
						$imported_attributes = $notes['attributes'];
					}
				}

				/*Post status - Le variazioni devono essere pubblicate*/
				$new_products_status = get_option( 'wcifd-publish-new-products' ) ? 'publish' : 'draft';
				$status              = ( $var_attributes ) ? 'publish' : $new_products_status;

				/*Verifico la presenza del prodotto*/
				$id   = wcifd_search_product( $sku );
				$type = ( wp_get_post_parent_id( $id ) || $parent_product_id ) ? 'product_variation' : 'product';

				/*Gestione magazzino*/
				$stock        = $product['Q.tà giacenza'];
				$total_sales  = $product['Tot. q.tà scaricata'];
				$manage_stock = ( 'Art. con magazzino' === $product_type || 'Art. con magazzino (taglie/colori)' === $product_type ) ? 'yes' : 'no';

				/*Dimensione prodotto*/
				$length = wcifd_get_product_size( $product, $size_type, 'z', true );
				$width  = wcifd_get_product_size( $product, $size_type, 'x', true );
				$height = wcifd_get_product_size( $product, $size_type, 'y', true );

				/*Peso del prodotto*/
				if ( 'gross-weight' === $weight_type ) {
					$weight = isset( $product['Peso lordo'] ) ? $product['Peso lordo'] : '';
				} else {
					$weight = isset( $product['Peso netto'] ) ? $product['Peso netto'] : '';
				}

				/*Autore del post come fornitore*/
				$author = ( 1 === intval( $use_suppliers ) && $supplier_id ) ? $supplier_id : get_option( 'wcifd-current-user' );

				/*Variazione taglia e colore di Danea*/
				if ( 'Art. con magazzino (taglie/colori)' === $product_type ) {
					update_post_meta( $id, 'wcifd-danea-size-color', 1 );
				}

				/*Verifica classe di tassazione*/
				$tax_status = 'none';
				$tax_class  = '';
				if ( $tax ) {
					$tax_status = 'taxable';
					$tax_class  = wcifd_get_tax_rate_class( $tax );
				}

				/*Inizio aggiornamento prodotto o creazione se non presente*/
				if ( ! $id ) {

					$p++;

					$args = array(
						'post_author'  => $author,
						'post_title'   => wp_strip_all_tags( $title ),
						'post_type'    => $type,
						'post_parent'  => $parent_product_id,
						'post_content' => $description,
						'post_status'  => $status,
						'meta_input'   => array(
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
						$args['meta_input']['_price']      = $sale_price;
					} else {
						$args['meta_input']['_sale_price'] = '';
					}

					/*WooCommerce Role Based Price*/
					$wc_rbp = get_wc_rbp();

					if ( is_array( $wc_rbp ) && ! empty( $wc_rbp ) ) {

						$args['meta_input']['_enable_role_based_price'] = 1;

						/*Per ruolo utente impostato*/
						foreach ( $wc_rbp as $role => $price_types ) {

							/*Tipo prezzi, scontati o meno*/
							foreach ( $price_types as $key => $value ) {
								$wc_rbp_price = wcifd_get_list_price( $product, $value, $tax_included );

								/*Prezzo ivato o meno*/
								if ( 0 === intval( $tax_included ) ) {
									$wc_rbp_price = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $value ] ) );
								} else {
									$wc_rbp_price = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $value . ' (ivato)' ] ) );
								}

								$args['meta_input']['_role_based_price'][ $role ][ $key ] = $wc_rbp_price;
							}
						}
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

						$v++;

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
					$status = 1 === intval( $deleted_products ) ? 'trash' : '';

					if ( get_post_status( $id ) !== $status ) {

						$u++;

						$args = array(
							'ID'          => $id,
							'post_status' => get_post_status( $id ),
							'post_author' => $author,
							'post_type'   => $type,
							'meta_input'  => array(
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
							$args['meta_input']['_price']      = $sale_price;
						} else {
							$args['meta_input']['_sale_price'] = '';
						}

						/*WooCommerce Role Based Price*/
						$wc_rbp = get_wc_rbp();

						if ( is_array( $wc_rbp ) && ! empty( $wc_rbp ) ) {

							$args['meta_input']['_enable_role_based_price'] = 1;

							/*Per ruolo utente impostato*/
							foreach ( $wc_rbp as $role => $price_types ) {

								/*Tipo prezzi, scontati o meno*/
								foreach ( $price_types as $key => $value ) {
									$wc_rbp_price = wcifd_get_list_price( $product, $value, $tax_included );

									/*Prezzo ivato o meno*/
									if ( 0 === intval( $tax_included ) ) {
										$wc_rbp_price = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $value ] ) );
									} else {
										$wc_rbp_price = str_replace( ',', '.', str_replace( array( ' ', '€' ), '', $product[ 'Listino ' . $value . ' (ivato)' ] ) );
									}

									$args['meta_input']['_role_based_price'][ $role ][ $key ] = $wc_rbp_price;
								}
							}
						}

						/*Nome prodotto*/
						if ( ! get_option( 'wcifd-exclude-title' ) ) {
							$args['post_title'] = $title;
						}

						/*URL prodotto*/
						if ( ! get_option( 'wcifd-exclude-url' ) ) {
							$args['post_name'] = sanitize_title_with_dashes( wp_strip_all_tags( $title ) );
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

					if ( 0 === intval( $cat_term ) || null === $cat_term ) {
						$cat_term = wp_insert_term( $category, 'product_cat' );
					}
					wp_set_object_terms( $product_id, intval( $cat_term['term_id'] ), 'product_cat', true );

					if ( $sub_category ) {

						$more_terms = array();

						$subs       = explode( ' » ', $sub_category );
						$subs_count = count( $subs );

						if ( is_array( $subs ) ) {

							for ( $i = 0; $i < $subs_count; $i++ ) {

								$parent_term      = 0 === $i ? $cat_term['term_id'] : $more_terms[ $i - 1 ]['term_id'];
								$more_terms[ $i ] = wcifd_add_taxonomy_term( $product_id, $subs[ $i ], $parent_term, true );

							}
						}
					}
				}
			}

			$p_imported = $p - $v;
			$output     = '<div id="message" class="updated"><p>';
			$output    .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';

			/* Translators: 1 numero prodotti, 2 numero variazioni, 3 numero aggiornamenti */
			$output .= sprintf( __( 'New products imported: %1$d<br>New variations imported: %2$d<br>Items updated: %3$d', 'wc-importer-for-danea' ), $p_imported, $v, $u );
			$output .= '</p></div>';

			echo wp_kses_post( $output );

		}
	}

}
