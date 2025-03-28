<?php
/**
 * Single product import 
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Import_Single_Product
 *
 * @since 1.6.4
 */
class WCIFD_Import_Single_Product {

    /**
     * The product data imported
     *
     * @var array 
     */
    public $p_data;

    /**
     * The product imported
     *
     * @var array 
     */
    public $product;

    /**
     * Notes as description option 
     *
     * @var bool 
     */
    public $notes_as_descriptions;

    /**
     * Short description option 
     *
     * @var string 
     */
    public $short_description_opt;

    /**
     * Tax included 
     *
     * @var bool 
     */
    public $tax_included;

    /**
     * Products not available 
     *
     * @var bool 
     */
    public $products_not_available;

	/**
	 * The constructor
	 *
     * @param  string $hash il codice identificativo del prodotto.
     *
	 * @return void
	 */
	public function __construct( $hash ) {

        $temp                   = new WCIFD_Temporary_Data();
        $p_data                 = $temp->wcifd_get_temporary_data( $hash );
        $product                = isset( $p_data['product'] ) ? $p_data['product'] : '';
        $notes_as_descriptions  = get_option( 'wcifd-notes-as-description' );
        $short_description_opt  = get_option( 'wcifd-short-description' );
        $tax_included           = get_option( 'wcifd-tax-included' );
        $products_not_available = get_option( 'wcifd-products-not-available' );

        /* Ends if the product does not exists */
        if ( ! $product ) {

            /* Delete temprary data from the db */
            $temp->wcifd_delete_temporary_data( $hash );

            return;
        }
    }


    /**
     * Get the product description
     *
     * @param array  $product the product.
     * @param string $title the product title.
     *
     * @return string
     */
    public function get_description( $product, $title ) {

        if ( isset( $product['DescriptionHtml'] ) && is_string( $product['DescriptionHtml'] ) ) {

            $output = wp_filter_post_kses( $product['DescriptionHtml'] );

        } elseif ( $this->notes_as_descriptions && isset( $product['Notes'] ) && is_string( $product['Notes'] ) ) {

            $output = wp_filter_post_kses( $product['Notes'] );

        } else {

            $output = $title;

        }

        return $output;
    }


    /**
     * Get the short product description
     *
     * @param array $product the product.
     *
     * @return string
     */
    public function get_short_description( $product ) {

        if ( 'excerpt' === $this->short_description_opt ) {

            $output = WCIFD_Functions::get_short_description( $description );

        } elseif ( 'notes' === $this->short_description_opt && isset( $product['Notes'] ) && is_string( $product['Notes'] ) ) {

            $output = wp_filter_post_kses( $product['Notes'] );

        }

        return $output;
    }


    /**
     * Get previously exported variable products details 
     *
     * @param array $product the product received from Danea Easyfatt
     *
     * @return array
     */
    public function get_data_from_notes( $product ) {

        $output = array();
        $output['parent_sku'] = null;
        $output['var_attributes'] = null;
        $output['variable_product'] = null;
        $output['parent_product_id'] = null;

        if ( ! $this->notes_as_descriptions && 'notes' !== $this->short_description_opt && isset( $product['Notes'] ) && is_string( $product['Notes'] ) ) {

            $notes  = json_decode( $product['Notes'], true );

            if ( is_array( $notes ) ) {

                /* Parent SKU */
                if ( isset( $notes['parent_sku'] ) && '' !== $notes['parent_sku'] ) {

                    $output['parent_sku'] = $notes['parent_sku'];

                } elseif ( isset( $notes['parent_id'] ) && '' !== $notes['parent_id'] ) {

                    $output['parent_product_id'] = $notes['parent_id'];

                }

                if ( $output['parent_sku'] ) {
                    $output['parent_product_id'] = WCIFD_Functions::search_product( $output['parent_sku']);
                }

                $output['var_attributes'] = isset( $notes['var_attributes'] ) ? $notes['var_attributes'] : null;

                /* Variable product */
                if ( isset( $notes['product_type'] ) && 'variable' === $notes['product_type'] ) {
                    if ( isset( $notes['attributes'] ) ) {
                        $output['variable_product']    = true;
                        $output['imported_attributes'] = $notes['attributes'];
                    }
                }
            }
        }

        return $output;
    }


    /**
     * Get product status
     *
     * @param array $var_attributes
     * @return string
     */
    public function get_status( $var_attributes ) {

        $status_option = get_option( 'wcifd-publish-new-products' ) ? 'publish' : 'draft';

        return $var_attributes ? 'publish' : $status_option;
    }


    /**
     * Get manage stock
     *
     * @return string
     */
    public function get_manage_stock( $product ) {

        $manage_stock = get_option( 'woocommerce_manage_stock' ); // WC option.

        if ( 'yes' === $manage_stock ) {

            return ( isset( $product['ManageWarehouse'] ) && 'true' === $product['ManageWarehouse'] ) ? 'yes' : 'no';
        } else {

            return 'no';
        }
    }


    /**
     * Get stock status
     *
     * @return string
     */
    public function get_stock_status( $data ) {

        $output = 'instock';

        if ( 'yes' === $data['manage_stock'] && 1 > $data['stock'] ) {
            $output = 'outofstock';
        }

        return $output;
    }


    /**
     * Get the product weight
     *
     * @param array $product the product received from Danea Easyfatt
     *
     * @return string
     */
    public function get_weight( $product ) {

        if ( 'gross-weight' === $this->p_data['weight_type'] ) {

            return isset( $product['GrossWeight'] ) ? $product['GrossWeight'] : null;
        } else {

            return isset( $product['NetWeight'] ) ? $product['NetWeight'] : null;
        }
    }


    /**
     * Get the author of the post
     *
     * @param array $product the product received from Danea Easyfatt
     *
     * @return int 
     */
    public function get_the_author() {

        /* Check the option post author as supplier */
        return ( 1 === intval( get_option( 'wcifd-use-suppliers' ) ) && isset( $product['SupplierCode'] ) ) ? $product['SupplierCode'] : get_option( 'wcifd-current-user' );
    }


    /**
     * Get the product tax details
     *
     * @param array $data the product data.
     * @param bool  $status return tax status with true.
     *
     * @return array
     */
    public function get_tax_info( $data, $status = false ) {

        $tax_status = 'none';
        $tax_class  = '';
        $perc       = isset( $data['tax_attributes']['@attributes']['Perc'] ) ? $data['tax_attributes']['@attributes']['Perc'] : null;
        $class      = isset( $data['tax_attributes']['@attributes']['Class'] ) ? $data['tax_attributes']['@attributes']['Class'] : null;

        if ( '0' !== $perc || ( 'Escluso' !== $class && 'NonSoggetto' !== $class ) ) {
            $tax_status = 'taxable';
            $tax_class  = WCIFD_Functions::get_tax_rate_class( $tax, strval( $perc ) );
        }

        return $status ? $tax_status : $tax_class;
    }


    /**
     * The single product information
     *
     * @param array $product the product received from Danea Easyfatt
     *
     * @return array
     */
    public function product_data( $product ) {

        $data = array();
        $data['sku'] = isset( $product['Code'] ) ? $product['Code'] : '';
        $data['sku'] = str_replace( '\\', '\\\\', $sku );
        $data['title'] = isset( $product['Description'] ) ? htmlentities( $product['Description'] ) :'';
        $data['category'] = isset( $product['Category'] ) ? $product['Category'] :'';
        $data['sub_category'] = isset( $product['Subcategory'] ) ? $product['Subcategory'] : '';
        $data['producer_name'] = isset( $product['ProducerName'] ) ? $product['ProducerName'] :'';
        $data['supplier_name'] = isset( $product['SupplierName'] ) ? $product['SupplierName'] :'';
        $data['sup_product_code'] = isset( $product['SupplierProductCode'] ) ? $product['SupplierProductCode'] :'';
        $data['get_tax'] = isset( $product['Vat'] ) ? $product['Vat'] :'';
        $data['tax'] = ( is_array( $get_tax ) && isset( $get_tax[0] ) ) ? $get_tax[0] : $get_tax;
        $data['stock'] = isset( $product['AvailableQty'] ) ? $product['AvailableQty'] :'';
        $data['um'] = isset( $product['Um'] ) ? $product['Um'] :'';
        $data['size_um'] = isset( $product['SizeUm'] ) ? $product['SizeUm'] :'';
        $data['weight_um'] = isset( $product['WeightUm'] ) ? $product['WeightUm'] : '';
        $data['image_file_name'] = isset( $product['ImageFileName'] ) ? sanitize_title( $product['ImageFileName'] ) : '';
        $data['regular_price'] = WCIFD_Functions::get_list_price( $product, $this->p_data['regular_price_list'], $this->tax_included );
        $data['sale_price']    = WCIFD_Functions::get_list_price( $product, $this->p_data['sale_price_list'], $this->tax_included );
        $data['on_sale']       = $data['sale_price'] ? 1 : 0;
        $data['size_type'] = isset( $this->p_data['size_type'] ) ? $this->p_data['size_type'] : '';
        $data['weight_type'] = isset( $this->p_data['weight_type'] ) ? $this->p_data['weight_type'] :'';
        $data['tax_attributes'] = isset( $this->p_data['tax_attributes'] ) ? $this->p_data['tax_attributes'] :'';
        $data['tax_status'] = $this->get_tax_info( $data, true ); 
        $data['tax_class'] = $this->get_tax_info( $data ); 
        $data['deleted_products'] = isset( $this->p_data['deleted_products'] ) ? $this->p_data['deleted_products'] :'';
        $data['wc_rbp'] = isset( $this->p_data['wc_rbp'] ) ? $this->p_data['wc_rbp'] : '';
        $data['length'] = WCIFD_Functions::get_product_size( $product, $data['size_type'], 'z' );
        $data['width']  = WCIFD_Functions::get_product_size( $product, $data['size_type'], 'x' );
        $data['height'] = WCIFD_Functions::get_product_size( $product, $data['size_type'], 'y' );
        $data['weight'] = $this->get_weight( $product );
        $data['author'] = $this->get_the_author( $product );

        /* Product description */
        $data['description'] = $this->get_description( $product, $data['title'] );

        /* Product short description */
        $data['short_description'] = $this->get_short_description( $product );

        /* Retrieve product details from Notes field */
        $data = array_merge( $data, $this->get_data_from_notes( $product ) );

        /* Product status */
        $data['status'] = $this->get_status( $data['var_attributes'] ); 

        /* Manage stock */
        $data['manage_stock'] = $this->get_manage_stock( $product );

        /* Stock status */
        $data['stock_status'] = $this->get_stock_status( $data );

        return $data;
    }


    /**
     * Get the ID if the product exists
     *
     * @param array $data the product data.
     *
     * @return int
     */
    public function get_product_id( $data ) {

        return WCIFD_Functions::search_product( $data['sku'], $data['parent_product_id'] );
    }


    /**
     * Get the type if the product exists
     *
     * @param array $data the product data.
     *
     * @return int
     */
    public function get_product_type( $data ) {

        $id = $this->get_product_id( $data );

        return ( wp_get_post_parent_id( $id ) || $data['parent_product_id'] ) ? 'product_variation' : 'product';
    }


    /**
     * Get the product size/color variants coming from Danea Easyfatt
     *
     * @param array $product the product data.
     *
     * @return array
     */
    public function get_danea_variants( $product ) {

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

        return $variants;
    }

    /**
     * Crate a new WC product
     *
     * @param array $product the product data received from Danea Easyfatt.
     * @param array $data the product data.
     *
     * @return void
     */
    public function create_new_product( $product, $data ) {

        /* No new product if not in stock */
		if ( $this->products_not_available && 1 > $stock ) {
			return;
		}

		$props = array(
			'name' => $data['title'],
			'type' => $this->get_product_type( $data ),
			'parent_id' => $data['parent_product_id'],
			'description' => $data['description'],
			'short_description' => $data['short_description'],
			'status' => $data['status'],
            'sku' => $data['sku'],
            'tax_status' => $data['tax_status'],
            'tax_class' => $data['tax_class'],
            'stock_quantity'         => $data['stock'],
            'manage_stock' => $data['manage_stock'],
            'stock_status' => $data['stock_status'],
            'catalog_visibility'    => 'visible', // Temp.
            'regular_price' => $data['regular_price'],
            'price'         => $data['regular_price'],
            'sale_price'    => $data['regular_price'],
            'width'         => $data['width'],
            'height'        => $data['height'],
            'length'        => $data['length'],
            'weight'        => $data['weight'],
		);

		if ( $data['sale_price'] ) {
			$props['sale_price'] = $data['sale_price'];
			$props['price']      = $data['sale_price'];
		} else {
			$props['sale_price'] = '';
		}

        $metadata = array(
            'author' => $data['author'], // Temp.
            'wcifd-um' => $data['um'],
        );

		/* WooCommerce Role Based Price */
        if ( is_array( $data['wc_rbp'] ) && ! empty( $data['wc_rbp'] ) ) {

			foreach ( $data['wc_rbp'] as $role => $price_types ) {
				foreach ( $price_types as $key => $value ) {

					$wc_rbp_price = WCIFD_Functions::get_list_price( $product, $value, $this->tax_included );

					if ( $wc_rbp_price ) {

						$metadata['_enable_role_based_price'] = 1;
						$metadata['_role_based_price'][ $role ][ $key ] = $wc_rbp_price;

					}
				}
			}
		}

		/* Insert the new product */
        $new_product = wc_get_product();
        $new_product->set_props( $props );
        $new_product->set_metadata( $metadata );
        $new_product->save();

        $product_id = $new_product->get_id();

		if ( is_wp_error( $product_id ) ) {

			error_log( 'WCIFD ERROR | Inserimento prodotto | Sku: ' . $sku . ' | ' . print_r( $product_id->get_error_message(), true ) );

			return;

		} else {

			if ( $data['variable_product'] ) {

				/* Update product type */
				wp_set_object_terms( $product_id, 'variable', 'product_type' );

				if ( $data['imported_attributes'] ) {

                    $attributes = $new_product->get_attributes();

					foreach ( $data['imported_attributes'] as $key => $value ) {

                        $attribute = new WC_Product_Attribute();
                        $attribute->set_id( wc_attribute_taxonomy_id_by_name( 'pa_' . $key ) );
                        $attribute->set_name( 'pa_' . $key );
                        $attribute->set_options( array( $value ) );
                        $attribute->set_visible( $is_taxonomy  );
                        $attribute->set_variation( false );
                        $attributes[] = $attribute;


                        
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

		}
    }















	/*Inizio aggiornamento prodotto o creazione se non presente*/
	if ( ! $id ) {

        $this->create_new_product();

	} else {

		/*Non aggiornare il prodotto se nel cestino*/
		$data['status'] = 1 === intval( $deleted_products ) ? 'trash' : '';

        $wc_product = wc_get_product( $id );

		if ( $wc_product->get_status() !== $data['status'] ) {

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
            $wc_product->set_manage_stock( $data['manage_stock'] );
            $wc_product->set_stock_status( $stock_status );
            /* $wc_product->set_catalog_visibility( 'visible' ); */
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

						$wc_rbp_price = WCIFD_Functions::get_list_price( $product, $value, $tax_included );

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
		$category_term = WCIFD_Functions::add_taxonomy_term( $product_id, $category, 0 );

		if ( $sub_category && ! is_wp_error( $category_term ) && isset( $category_term['term_id'] ) ) {

			$more_terms = array();

			/*Prima sottocategoria*/
			$more_terms[1] = WCIFD_Functions::add_taxonomy_term( $product_id, $sub_category, $category_term['term_id'], true );

			/*Sottocategorie successive*/
			for ( $i = 2; $i < 10; $i++ ) {
				$sub_name = 'Subcategory' . $i;
				if ( isset( $product[ $sub_name ] ) ) {

					$more_terms[ $i ] = WCIFD_Functions::add_taxonomy_term( $product_id, $product[ $sub_name ], $more_terms[ $i - 1 ]['term_id'], true );

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
	/* $attributes = get_post_meta( $product_id, '_product_attributes', true ) ? get_post_meta( $product_id, '_product_attributes', true ) : array(); */
    $attributes = $wc_product->get_attributes();

	/*Attributi aggiuntivi*/
	$more_attributes = array(
		'producer'         => $producer_name,
		'supplier'         => $supplier_name,
		'sup-product-code' => $sup_product_code,
	);

    /* error_log( 'NEW ATTRIBUTES 1: ' . print_r( $wc_product->get_attributes(), true ) ); */

    /* error_log( 'ATTRIBUTES 1: ' . print_r( $attributes, true ) ); */
    /* error_log( 'MORE ATTRIBUTES: ' . print_r( $attributes, true ) ); */
	foreach ( $more_attributes as $key => $value ) {

		if ( $value ) {

			$is_visible = get_option( 'wcifd-display-' . $key ) ? get_option( 'wcifd-display-' . $key ) : '0';

			/* wp_set_object_terms( $product_id, array( $value ), 'pa_' . $key ); */

			/* $attributes[ 'pa_' . $key ] = array( */
			/* 	'name'         => 'pa_' . $key, */
			/* 	'value'        => '', */
			/* 	'is_visible'   => $is_visible, */
			/* 	'is_variation' => '0', */
			/* 	'is_taxonomy'  => '1', */
			/* ); */

            $attribute = new WC_Product_Attribute();
            $attribute->set_id( wc_attribute_taxonomy_id_by_name( 'pa_' . $key ) );
            $attribute->set_name( 'pa_' . $key );
            $attribute->set_options( array( 2591 ) );
            $attribute->set_visible( $is_visible  );
            $attribute->set_variation( false );
            $attributes[] = $attribute;

		} else {

            if ( isset( $attributes[ 'pa_' . $key ] ) ) {

                wp_delete_object_term_relationships( $product_id, 'pa_' . $key );
                unset( $attributes[ 'pa_' . $key ] );

                error_log( 'KEY: ' . 'pa_' . $key );

                /* $test = wp_remove_object_terms( $product_id, array( $value ), 'pa_' . $key ); */
                /* $test = $wc_product->delete_attribute( 'pa_' . $key ); */

                /* error_log( 'TEST 1: ' . print_r( $test, true ) ); */

            }

            /* error_log( 'ATTRIBUTES 2: ' . print_r( $attributes, true ) ); */
		}

        $wc_product->set_attributes( $attributes );
        error_log( 'NEW ATTRIBUTES 2: ' . print_r( $wc_product->get_attributes(), true ) );
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
			$var_id       = WCIFD_Functions::search_product( $barcode );
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

							$wc_rbp_price = WCIFD_Functions::get_list_price( $product, $value, $tax_included );

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
				if ( $this->products_not_available && 1 > $in_stock ) {
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
