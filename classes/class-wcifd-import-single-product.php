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
	 * The product hash
	 *
	 * @var string
	 */
	public $hash;

	/**
	 * The temporary data
	 *
	 * @var object
	 */
	public $temp;

	/**
	 * The product data imported
	 *
	 * @var array
	 */
	public $p_data;

	/**
	 * The product imported from Danea Easyfatt
	 *
	 * @var array
	 */
	public $d_product;

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
	 * Available colors
	 *
	 * @var array
	 */
	public $avail_colors = array();

	/**
	 * Available sizes
	 *
	 * @var array
	 */
	public $avail_sizes = array();

	/**
	 * Check the option about the update of the product variations
	 *
	 * @var bool
	 */
	public $exclude_variations_prices;

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		add_action( 'wcifd_import_product_event', array( $this, 'init' ), 10, 8 );
	}


	/**
	 * Init the product import
	 *
	 * @param string $hash the product hash.
	 *
	 * @return void
	 */
	public function init( $hash ) {

		$this->hash = $hash;
		$this->temp = new WCIFD_Temporary_Data();

		/* Get temporary data from the db */
		$this->p_data = $this->temp->wcifd_get_temporary_data( $hash );

		/* Get the product data */
		$this->d_product = isset( $this->p_data['product'] ) ? $this->p_data['product'] : '';

		/* Get the options */
		$this->notes_as_descriptions     = get_option( 'wcifd-notes-as-description' );
		$this->short_description_opt     = get_option( 'wcifd-short-description' );
		$this->tax_included              = get_option( 'wcifd-tax-included' );
		$this->products_not_available    = get_option( 'wcifd-products-not-available' );
		$this->exclude_variations_prices = get_option( 'wcifd-products-variations-prices' );

		/* Ends if the product does not exists */
		if ( ! $this->d_product ) {

			/* Delete temprary data from the db */
			$this->temp->wcifd_delete_temporary_data( $hash );

			return;

		} else {

			$this->single_product( $this->d_product );
		}
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
	 * Get type if product exists
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
	 * Get product status
	 *
	 * @param array $var_attributes the product attributes.
	 *
	 * @return string
	 */
	public function get_status( $var_attributes ) {

		$status_option = get_option( 'wcifd-publish-new-products' ) ? 'publish' : 'draft';

		return $var_attributes ? 'publish' : $status_option;
	}


	/**
	 * Get the author of the post
	 *
	 * @return int
	 */
	public function get_the_author() {

		/* Check the option post author as supplier */
		return ( 1 === intval( get_option( 'wcifd-use-suppliers' ) ) && isset( $this->d_product['SupplierCode'] ) ) ? $this->d_product['SupplierCode'] : get_option( 'wcifd-current-user' );
	}


	/**
	 * Get the product description
	 *
	 * @param string $title the product title.
	 *
	 * @return string
	 */
	public function get_description( $title ) {

		if ( $this->notes_as_descriptions && isset( $this->d_product['Notes'] ) && is_string( $this->d_product['Notes'] ) ) {

			$output = wp_filter_post_kses( $this->d_product['Notes'] );

		} elseif ( isset( $this->d_product['DescriptionHtml'] ) && is_string( $this->d_product['DescriptionHtml'] ) ) {

			$output = wp_filter_post_kses( $this->d_product['DescriptionHtml'] );

		} else {

			$output = $title;
		}

		return $output;
	}


	/**
	 * Get the short product description
	 *
	 * @return string
	 */
	public function get_short_description() {

		$output = null;

		if ( 'excerpt' === $this->short_description_opt ) {

			$output = WCIFD_Functions::get_short_description( $data['description'] );

		} elseif ( 'notes' === $this->short_description_opt && isset( $this->d_product['Notes'] ) && is_string( $this->d_product['Notes'] ) ) {

			$output = wp_filter_post_kses( $this->d_product['Notes'] );

		}

		return $output;
	}


	/**
	 * Get previously exported variable products details
	 *
	 * @return array
	 */
	public function get_data_from_notes() {

		$output                      = array();
		$output['parent_sku']        = null;
		$output['var_attributes']    = null;
		$output['variable_product']  = null;
		$output['parent_product_id'] = null;

		if ( ! $this->notes_as_descriptions && 'notes' !== $this->short_description_opt && isset( $this->d_product['Notes'] ) && is_string( $this->d_product['Notes'] ) ) {

			$notes = json_decode( $this->d_product['Notes'], true );

			if ( is_array( $notes ) ) {

				/* Parent SKU */
				if ( isset( $notes['parent_sku'] ) && '' !== $notes['parent_sku'] ) {

					$output['parent_sku'] = $notes['parent_sku'];

				} elseif ( isset( $notes['parent_id'] ) && '' !== $notes['parent_id'] ) {

					$output['parent_product_id'] = $notes['parent_id'];

				}

				if ( $output['parent_sku'] ) {
					$output['parent_product_id'] = WCIFD_Functions::search_product( $output['parent_sku'] );
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
	 * Get manage stock
	 *
	 * @return string
	 */
	public function get_manage_stock() {

		$manage_stock = get_option( 'woocommerce_manage_stock' ); // WC option.

		if ( 'yes' === $manage_stock ) {

			return ( isset( $this->d_product['ManageWarehouse'] ) && 'true' === $this->d_product['ManageWarehouse'] ) ? 'yes' : 'no';
		} else {

			return 'no';
		}
	}


	/**
	 * Get stock status
	 *
	 * @param array $data the product data.
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
	 * @return string
	 */
	public function get_weight() {

		if ( 'gross-weight' === $this->p_data['weight_type'] ) {

			return isset( $this->d_product['GrossWeight'] ) ? $this->d_product['GrossWeight'] : null;
		} else {

			return isset( $this->d_product['NetWeight'] ) ? $this->d_product['NetWeight'] : null;
		}
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
			$tax_class  = WCIFD_Functions::get_tax_rate_class( $data['tax'], strval( $perc ) );
		}

		return $status ? $tax_status : $tax_class;
	}


	/**
	 * The single product information
	 *
	 * @return array
	 */
	public function product_data() {

		$data                     = array();
		$data['sku']              = isset( $this->d_product['Code'] ) ? $this->d_product['Code'] : '';
		$data['sku']              = str_replace( '\\', '\\\\', $data['sku'] );
		$data['title']            = isset( $this->d_product['Description'] ) ? htmlentities( $this->d_product['Description'] ) : '';
		$data['category']         = isset( $this->d_product['Category'] ) ? $this->d_product['Category'] : '';
		$data['sub_category']     = isset( $this->d_product['Subcategory'] ) ? $this->d_product['Subcategory'] : '';
		$data['producer_name']    = isset( $this->d_product['ProducerName'] ) ? $this->d_product['ProducerName'] : '';
		$data['supplier_name']    = isset( $this->d_product['SupplierName'] ) ? $this->d_product['SupplierName'] : '';
		$data['sud_product_code'] = isset( $this->d_product['SupplierProductCode'] ) ? $this->d_product['SupplierProductCode'] : '';
		$data['get_tax']          = isset( $this->d_product['Vat'] ) ? $this->d_product['Vat'] : '';
		$data['tax']              = ( is_array( $data['get_tax'] ) && isset( $data['get_tax'] ) && isset( $data['get_tax'][0] ) ) ? $data['get_tax'][0] : $data['get_tax'];
		$data['stock']            = isset( $this->d_product['AvailableQty'] ) ? $this->d_product['AvailableQty'] : '';
		$data['um']               = isset( $this->d_product['Um'] ) ? $this->d_product['Um'] : '';
		$data['size_um']          = isset( $this->d_product['SizeUm'] ) ? $this->d_product['SizeUm'] : '';
		$data['weight_um']        = isset( $this->d_product['WeightUm'] ) ? $this->d_product['WeightUm'] : '';
		$data['image_file_name']  = isset( $this->d_product['ImageFileName'] ) ? sanitize_title( $this->d_product['ImageFileName'] ) : '';
		$data['regular_price']    = WCIFD_Functions::get_list_price( $this->d_product, $this->p_data['regular_price_list'], $this->tax_included );
		$data['sale_price']       = WCIFD_Functions::get_list_price( $this->d_product, $this->p_data['sale_price_list'], $this->tax_included );
		$data['on_sale']          = $data['sale_price'] ? 1 : 0;
		$data['size_type']        = isset( $this->p_data['size_type'] ) ? $this->p_data['size_type'] : '';
		$data['weight_type']      = isset( $this->p_data['weight_type'] ) ? $this->p_data['weight_type'] : '';
		$data['tax_attributes']   = isset( $this->p_data['tax_attributes'] ) ? $this->p_data['tax_attributes'] : '';
		$data['tax_status']       = $this->get_tax_info( $data, true );
		$data['tax_class']        = $this->get_tax_info( $data );
		$data['deleted_products'] = isset( $this->p_data['deleted_products'] ) ? $this->p_data['deleted_products'] : '';
		$data['wc_rbp']           = isset( $this->p_data['wc_rbp'] ) ? $this->p_data['wc_rbp'] : '';
		$data['length']           = WCIFD_Functions::get_product_size( $this->d_product, $data['size_type'], 'z' );
		$data['width']            = WCIFD_Functions::get_product_size( $this->d_product, $data['size_type'], 'x' );
		$data['height']           = WCIFD_Functions::get_product_size( $this->d_product, $data['size_type'], 'y' );
		$data['weight']           = $this->get_weight( $this->d_product );
		$data['author']           = $this->get_the_author( $this->d_product );

		/* Product description */
		$data['description'] = $this->get_description( $data['title'] );

		/* Product short description */
		$data['short_description'] = $this->get_short_description();

		/* Retrieve product details from Notes field */
		$data = array_merge( $data, $this->get_data_from_notes() );

		/* Product status */
		$data['status'] = $this->get_status( $data['var_attributes'] );

		/* Manage stock */
		$data['manage_stock'] = $this->get_manage_stock();

		/* Stock status */
		$data['stock_status'] = $this->get_stock_status( $data );

		/* Custom fields */
		for ( $i = 1; $i < 5; $i++ ) {

			$field_name   = 'CustomField' . $i;
			$custom_field = isset( $this->d_product[ $field_name ] ) ? $this->d_product[ $field_name ] : '';

			if ( $custom_field ) {
				$data[ $field_name ] = $custom_field;
			}
		}

		return $data;
	}


	/**
	 * Get the product size/color variants coming from Danea Easyfatt
	 *
	 * @param object $product the WC product.
	 *
	 * @return array
	 */
	public function get_danea_variants( $product ) {

		$output   = null;
		$variants = null;

		if ( isset( $this->d_product['Variants'] ) ) {

			$variants = $this->d_product['Variants'];

		} elseif ( isset( $this->d_product['Variant'] ) ) {

			foreach ( $this->d_product['Variant'] as $variant ) {

				$variants[] = $variant;
			}
		}

		$output = $variants;

		if ( isset( $variants['Variant'][0] ) && is_array( $variants['Variant'][0] ) ) {

			$output = $variants['Variant'];
		}

		if ( $output ) {

			$product->set_meta_data( 'wcifd-danea-size-color', 1 );
			return $output;
		}
	}


	/**
	 * Get details about a product previously exported from WooCommerce
	 *
	 * @param int   $product_id the WC product ID.
	 * @param array $data the product data.
	 *
	 * @return void
	 */
	public function get_prev_exported_product_details( $product_id, $data ) {

		if ( $data['variable_product'] ) {

			/* Update product type */
			$product = new WC_Product_Variable( $product_id );

			if ( $data['imported_attributes'] ) {

				$attributes = $product->get_attributes();

				foreach ( $data['imported_attributes'] as $key => $value ) {

					$attribute = new WC_Product_Attribute();
					$attribute->set_id( wc_attribute_taxonomy_id_by_name( 'pa_' . $key ) );
					$attribute->set_name( 'pa_' . $key );
					$attribute->set_options( array( $value ) );
					$attribute->set_visible( true );
					$attribute->set_variation( true );
					$attributes[] = $attribute;

					$product->set_attributes( $attributes );
					$product->save();
				}
			}
		} elseif ( $data['parent_product_id'] && $data['var_attributes'] ) {

			/* Update product type */
			$variation = new WC_Product_Variation( $product_id );

			$attributes = $variation->get_attributes();

			foreach ( $data['var_attributes'] as $attr ) {

				$attributes[] = $attr;
			}

			$variation->set_attributes( $attributes );
			$variation->save();
		}
	}


	/**
	 * Add data about WC Role Based Price
	 *
	 * @param array $data the product data.
	 *
	 * @return array
	 */
	public function add_wcrbp_data( $data ) {

		$output = array();

		if ( is_array( $data['wc_rbp'] ) && ! empty( $data['wc_rbp'] ) ) {

			foreach ( $data['wc_rbp'] as $role => $price_types ) {
				foreach ( $price_types as $key => $value ) {

					$wc_rbp_price = WCIFD_Functions::get_list_price( $this->d_product, $value, $this->tax_included );

					if ( $wc_rbp_price ) {

						$output['_enable_role_based_price']           = 1;
						$output['_role_based_price'][ $role ][ $key ] = $wc_rbp_price;
					}
				}
			}
		}

		return $output;
	}


	/**
	 * Update data about WC Role Based Price
	 *
	 * @param object $product the WC product.
	 * @param array  $data    the product data.
	 *
	 * @return void
	 */
	public function upate_wcrbp_data( $product, $data ) {

		if ( is_array( $data['wc_rbp'] ) && ! empty( $data['wc_rbp'] ) ) {

			$role_based_price = array();

			foreach ( $data['wc_rbp'] as $role => $price_types ) {

				foreach ( $price_types as $key => $value ) {

					$wc_rbp_price = WCIFD_Functions::get_list_price( $this->d_product, $value, $this->tax_included );

					if ( $wc_rbp_price ) {

						$product->update_meta_data( '_enable_role_based_price', 1 );

						/* Add the single role price to the array */
						$role_based_price[ $role ][ $key ] = $wc_rbp_price;
					}
				}

				/* Update role based price */
				$product->update_meta_data( '_role_based_price', $role_based_price );

				if ( $this->get_danea_variants( $product ) && function_exists( 'wc_rbp_delete_variation_data' ) ) {

					wc_rbp_delete_variation_data( $product->get_id(), $role );
				}
			}
		}
	}


	/**
	 * Crate a new WC product
	 *
	 * @param array $data the product data.
	 *
	 * @return int the product ID
	 */
	public function create_new_product( $data ) {

		/* No new product if not in stock */
		if ( $this->products_not_available && 1 > $stock ) {
			return;
		}

		/* Insert the new product */
		$product = new WC_Product_Simple();

		$props = array(
			'name'               => $data['title'],
			'type'               => $this->get_product_type( $data ),
			'parent_id'          => $data['parent_product_id'],
			'description'        => $data['description'],
			'short_description'  => $data['short_description'],
			'status'             => $data['status'],
			'sku'                => $data['sku'],
			'tax_status'         => $data['tax_status'],
			'tax_class'          => $data['tax_class'],
			'stock_quantity'     => $data['stock'],
			'manage_stock'       => $data['manage_stock'],
			'stock_status'       => $data['stock_status'],
			'catalog_visibility' => 'visible', // Temp.
			'regular_price'      => $data['regular_price'],
			'price'              => $data['regular_price'],
			'sale_price'         => $data['regular_price'],
			'width'              => $data['width'],
			'height'             => $data['height'],
			'length'             => $data['length'],
			'weight'             => $data['weight'],
		);

		if ( $data['sale_price'] ) {
			$props['sale_price'] = $data['sale_price'];
			$props['price']      = $data['sale_price'];
		} else {
			$props['sale_price'] = '';
		}

		$metadata = array(
			'author'   => $data['author'], // Temp.
			'wcifd-um' => $data['um'],
		);

		/* WooCommerce Role Based Price */
		$metadata = array_merge( $metadata, $this->add_wcrbp_data( $data ) );

		/* Set props */
		$product->set_props( $props );

		/* Set metadata */
		foreach ( $metadata as $key => $value ) {

			$product->update_meta_data( $key, $value );
		}

		$product->save();

		$product_id = $product->get_id();

		if ( is_wp_error( $product_id ) ) {

			error_log( 'WCIFD ERROR | Inserimento prodotto | Sku: ' . $sku . ' | ' . print_r( $product_id->get_error_message(), true ) );

			return;

		} else {

			return $product_id;
		}
	}


	/**
	 * Update product
	 *
	 * @param int   $product_id the WC product ID.
	 * @param array $data the product data.
	 *
	 * @return void
	 */
	public function update_product( $product_id, $data ) {

		/* Don't update product if in trash */
		$data['status'] = 1 === intval( $data['deleted_products'] ) ? 'trash' : '';

		$product = wc_get_product( $product_id );

		if ( $product->get_status() !== $data['status'] ) {

			/* Check if backorder are activated */
			if ( 'outofstock' === $data['stock_status'] ) {
				if ( in_array( $product->get_backorders(), array( 'yes', 'notify' ), true ) ) {
					$stock_status = 'onbackorder';
				}
			}

			$product->set_sku( $data['sku'] );
			$product->set_tax_status( $data['tax_status'] );
			$product->set_tax_class( $data['tax_class'] );
			$product->set_stock_quantity( $data['stock'] );
			$product->set_manage_stock( $data['manage_stock'] );
			$product->set_stock_status( $data['stock_status'] );
			$product->set_regular_price( $data['regular_price'] );
			$product->set_price( $data['regular_price'] );
			$product->set_width( $data['width'] );
			$product->set_height( $data['height'] );
			$product->set_length( $data['length'] );
			$product->set_weight( $data['weight'] );
			$product->update_meta_data( '_wcifd-um', $data['um'] );

			if ( $data['sale_price'] ) {
				$product->set_sale_price( $data['sale_price'] );
				$product->set_price( $data['sale_price'] );
			} else {
				$product->set_sale_price( '' );
			}

			/* Product name */
			if ( ! get_option( 'wcifd-exclude-title' ) ) {
				$product->set_name( $data['title'] );
			}

			/* Product URL */
			if ( ! get_option( 'wcifd-exclude-url' ) ) {
				$product->set_slug( sanitize_title_with_dashes( wp_strip_all_tags( $data['title'] ) ) );
			}

			/* Product description */
			if ( ! get_option( 'wcifd-exclude-description' ) ) {
				$product->set_description( $data['description'] );
				$product->set_short_description( $data['short_description'] );
			}

			if ( $this->get_danea_variants( $product ) ) {
				wc_delete_product_transients( $product_id );
				wp_cache_delete( 'alloptions', 'options' );
			}

			/* Update WC Role Based Price data */
			$this->upate_wcrbp_data( $product, $data );

			/* Save updates */
			$product->save();

			if ( is_wp_error( $product_id ) ) {

				error_log( 'WCIFD ERROR | Aggiornamento prodotto | Sku: ' . $data['sku'] . ' | ' . print_r( $product_id->get_error_message(), true ) );

				return;

			}
		} else {

			return;

		}

	}


	/**
	 * Handle Danea Easyfatt color/size single product variants
	 *
	 * @param array  $variant the variant data recived from Danea Easyfatt.
	 * @param int    $n the variant number.
	 * @param object $product the WC product.
	 * @param array  $data the product data.
	 *
	 * @return void
	 */
	public function single_variant( $variant, $n, $product, $data ) {

		$barcode      = isset( $variant['Barcode'] ) ? $variant['Barcode'] : '';
		$var_id       = WCIFD_Functions::search_product( $barcode );
		$in_stock     = isset( $variant['AvailableQty'] ) ? $variant['AvailableQty'] : '';
		$man_stock    = 'yes';
		$stock_status = ( $in_stock ) ? 'instock' : 'outofstock';

		if ( ! $var_id ) {

			/* Do not import variation if not available */
			if ( $this->products_not_available && 1 > $in_stock ) {
				/* continue; */
				return; // Temp.
			}
		}

		/* Attributes */
		$size  = isset( $variant['Size'] ) ? $variant['Size'] : '-';
		$color = isset( $variant['Color'] ) ? $variant['Color'] : '-';

		/* Add new size */
		if ( '-' !== $size && ! in_array( $size, $this->avail_sizes, true ) ) {

			$this->avail_sizes[] = $size;
		}

		/* Add new color */
		if ( '-' !== $color && ! in_array( $color, $this->avail_colors, true ) ) {

			$this->avail_colors[] = $color;
		}

		/* Variation metas */
		$meta_input = array();

		/* Add attribute to the variations metas */
		if ( $this->avail_colors ) {
			$meta_input['attribute_pa_color'] = sanitize_title( $color );
		}
		if ( $this->avail_sizes ) {
			$meta_input['attribute_pa_size'] = sanitize_title( $size );
		}

		/* Update variation */
		$variation = new WC_Product_Variation( $var_id );

		$variation->set_sku( $barcode );
		$variation->set_stock_quantity( $in_stock );
		$variation->set_stock_status( $stock_status );
		$variation->set_manage_stock( $man_stock );

		if ( ! $var_id || ( $var_id && ! $this->exclude_variations_prices ) ) {

			if ( $data['sale_price'] ) {

				$price = $data['sale_price'];
			} else {

				$price = $data['regular_price'];
			}

			$variation->set_regular_price( $price );
			$variation->set_sale_price( $price );
			$variation->set_price( $price );

			/* WooCommerce Role Based Price */
			$meta_input = array_merge( $meta_input, $this->add_wcrbp_data( $data ) );
		}

		/* Add weight and dimensions */
		if ( $data['weight'] ) {
			$variation->set_weight( $data['weight'] );
		}

		if ( $data['length'] ) {
			$variation->set_length( $data['length'] );
		}

		if ( $data['width'] ) {
			$variation->set_width( $data['width'] );
		}

		if ( $data['height'] ) {
			$variation->set_height( $data['height'] );
		}

		$variation->set_name( 'danea-product-' . $product->get_id() . '-variation-' . ( $n++ ) );
		$variation->set_parent_id( $product->get_id() );
		$variation->set_status( 'publish' );

		if ( is_array( $meta_input ) && ! empty( $meta_input ) ) {

			foreach ( $meta_input as $key => $value ) {

				$variation->update_meta_data( $key, $value );
			}
		}

		if ( $variation->get_id() ) {

			/* Check backorders option */
			if ( 'outofstock' === $stock_status && $variation->get_id() ) {

				$backorders = $variation->get_backorders();

				if ( 'yes' === $backorders || 'notify' === $backorders ) {
					$variation->set_stock_status( $stock_status );
				}
			}

			$attributes = $variation->get_attributes();

			if ( $color ) {
				$attributes['pa_color'] = sanitize_title( $color );
			}

			if ( $size ) {
				$attributes['pa_size'] = sanitize_title( $size );
			}

			$variation->set_attributes( $attributes );

			/* Save the variation */
			$variation->save();
		}

		/* error_log( 'WCIFD ERROR | Aggiornamento variazione prodotto | Sku: ' . $barcode . ' | ' . print_r( $var_id->get_error_message(), true ) ); */

	}


	/**
	 * Handle Danea Easyfatt color/size product variants
	 *
	 * @param id    $product_id the WC product ID.
	 * @param array $data the product data.
	 *
	 * @return void
	 */
	public function danea_variants( $product_id, $data ) {

		/* Update the parent product */
		$product = new WC_Product_Variable( $product_id );

		$v = 1;

		/* Define the array of the variations */
		$variants = $this->get_danea_variants( $product );

		if ( is_array( $variants ) && ! empty( $variants ) ) {

			foreach ( $variants as $variant ) {

				$this->single_variant( $variant, $v, $product, $data );
			}
		}

		/* Product attributes */
		$attributes = $product->get_attributes();

		if ( $this->avail_colors ) {

			$attribute = new WC_Product_Attribute();
			$attribute->set_id( wc_attribute_taxonomy_id_by_name( 'pa_color' ) );
			$attribute->set_name( 'pa_color' );
			$attribute->set_options( $this->avail_colors );
			$attribute->set_visible( true );
			$attribute->set_variation( true );
			$attributes[] = $attribute;
		}

		if ( $this->avail_sizes ) {

			$attribute = new WC_Product_Attribute();
			$attribute->set_id( wc_attribute_taxonomy_id_by_name( 'pa_size' ) );
			$attribute->set_name( 'pa_size' );
			$attribute->set_options( $this->avail_sizes );
			$attribute->set_visible( true );
			$attribute->set_variation( true );
			$attributes[] = $attribute;
		}

		$product->set_attributes( $attributes );
		$product->save();
	}


	/**
	 * Handle product categories
	 *
	 * @param int   $product_id the WC product ID.
	 * @param array $data the product data.
	 *
	 * @return void
	 */
	public function product_categories( $product_id, $data ) {

		/* Product categories */
		if ( $data['category'] ) {

			/* Category */
			$category_term = WCIFD_Functions::add_taxonomy_term( $product_id, $data['category'], 0 );

			if ( $data['sub_category'] && ! is_wp_error( $category_term ) && isset( $category_term['term_id'] ) ) {

				$more_terms = array();

				/* First subcategory */
				$more_terms[1] = WCIFD_Functions::add_taxonomy_term( $product_id, $data['sub_category'], $category_term['term_id'], true );

				/* Other subcategories */
				for ( $i = 2; $i < 10; $i++ ) {
					$sub_name = 'Subcategory' . $i;
					if ( isset( $product[ $sub_name ] ) ) {

						$more_terms[ $i ] = WCIFD_Functions::add_taxonomy_term( $product_id, $product[ $sub_name ], $more_terms[ $i - 1 ]['term_id'], true );

					}
				}
			}
		}
	}


	/**
	 * Handle product image
	 *
	 * @param int   $product_id the WC product ID.
	 * @param array $data the product data.
	 *
	 * @return void
	 */
	public function single_product_image( $product_id, $data ) {

		/* Save image information if present */
		if ( get_option( 'wcifd-import-images' ) ) {

			if ( $data['image_file_name'] ) {

				/* Add temporary data to the dedicated table for image/product association */
				$this->temp->wcifd_add_temporary_image( $this->hash, $product_id, $data['image_file_name'] );

			} else {

				/* Remove the product image */
				if ( has_post_thumbnail( $product_id ) ) {
					$attachment_id = get_post_thumbnail_id( $product_id );
					wp_delete_attachment( $attachment_id, true );
				}
			}
		}
	}


	/**
	 * Handle product attributes
	 *
	 * @param int   $product_id the WC product ID.
	 * @param array $data the product data.
	 *
	 * @return void
	 */
	public function product_attributes( $product_id, $data ) {

		/* Product */
		$product = wc_get_product( $product_id );

		/* Product attributes */
		$attributes = $product->get_attributes();

		/* More attributes */
		$more_attributes = array(
			'producer'         => $data['producer_name'],
			'supplier'         => $data['supplier_name'],
			'sup-product-code' => $data['sud_product_code'],
		);

		foreach ( $more_attributes as $key => $value ) {

			if ( $value ) {

				$is_visible = get_option( 'wcifd-display-' . $key ) ? get_option( 'wcifd-display-' . $key ) : '0';

				$attribute = new WC_Product_Attribute();
				$attribute->set_id( wc_attribute_taxonomy_id_by_name( 'pa_' . $key ) );
				$attribute->set_name( 'pa_' . $key );
				$attribute->set_options( array( $value ) );
				$attribute->set_visible( $is_visible );
				$attribute->set_variation( false );
				$attributes[] = $attribute;

			} else {

				if ( isset( $attributes[ 'pa_' . $key ] ) ) {

					unset( $attributes[ 'pa_' . $key ] );
				}
			}
		}

		$product->set_attributes( $attributes );
		$product->save();
	}


	/**
	 * Handle Danea Easyfatt custom fields
	 *
	 * @param int   $product_id the WC product ID.
	 * @param array $data the product data.
	 *
	 * @return void
	 */
	public function danea_custom_fields( $product_id, $data ) {

		/* Get the product */
		$product = wc_get_product( $product_id );

		/* Get the product attributes */
		$attributes = $product->get_attributes();

		/* Get the product tag ids */
		$tag_ids = $product->get_tag_ids();

		for ( $i = 1; $i < 5; $i++ ) {

			$field_name   = 'CustomField' . $i;
			$pa_name      = 'pa_' . strtolower( $field_name );
			$custom_field = isset( $data[ $field_name ] ) ? $data[ $field_name ] : '';
			$tag_id       = null;

			if ( $custom_field ) {

				$fields_options = get_option( 'wcifd-custom-fields' );
				$import         = isset( $fields_options[ $i ]['import'] ) ? $fields_options[ $i ]['import'] : false;
				$cf_name        = isset( $fields_options[ $i ]['name'] ) ? $fields_options[ $i ]['name'] : false;
				$split          = isset( $fields_options[ $i ]['split'] ) ? $fields_options[ $i ]['split'] : false;
				$is_visible     = isset( $fields_options[ $i ]['display'] ) ? $fields_options[ $i ]['display'] : '0';

				/* Append option for tags */
				if ( 1 === $i ) {
					$append  = isset( $fields_options[ $i ]['append'] ) ? $fields_options[ $i ]['append'] : false;
					$tag_ids = array();
				}

				/* Get the tag id if exists */
				$tag_id = term_exists( $custom_field, 'product_tag' );

				if ( 'attribute' === $import ) {

					/* Remove tag */
					if ( $tag_id && 0 !== $tag_id ) {

						$tag_ids = array_diff( $tag_ids, array( $tag_id ) );
						$product->set_tag_ids( $tag_ids );
					}

					/* Get product attribute options */
					$attribute_options = array();

					if ( $split ) {

						$values = array_map( 'trim', explode( ',', $custom_field ) );

						if ( is_array( $values ) ) {

							$attribute_options = $values;
						}
					} else {

						$attribute_options = array( $custom_field );
					}

					if ( ! empty( $attribute_options ) ) {

						/* Set attribute */
						$attribute = new WC_Product_Attribute();
						$attribute->set_id( wc_attribute_taxonomy_id_by_name( $pa_name ) );
						$attribute->set_name( $pa_name );
						$attribute->set_options( $attribute_options );
						$attribute->set_visible( true );
						$attribute->set_variation( true );
						$attributes[] = $attribute;
						$product->set_attributes( $attributes );
					}

					$product->save();

				} elseif ( 'tag' === $import ) {

					/* Remove attribute */
					if ( isset( $attributes[ $pa_name ] ) ) {
						unset( $attributes[ $pa_name ] );
						$product->set_attributes( $attributes );
					}

					/* Add tag */
					if ( $tag_id && 0 !== $tag_id ) {

						if ( ! in_array( $tag_id, $tag_ids, true ) ) {

							$tag_ids[] = $tag_id;
						}
					} else {

						$tag_id    = wp_insert_term( $custom_field, 'product_tag' );
						$tag_ids[] = $tag_id['term_id'];
					}

					$product->set_tag_ids( $tag_ids );
					$product->save();

				} else {

					/* Remove attribute */
					if ( isset( $attributes[ $pa_name ] ) ) {
						unset( $attributes[ $pa_name ] );
						$product->set_attributes( $attributes );
					}

					/* Remove tag */
					if ( $tag_id && 0 !== $tag_id ) {

						$tag_ids = array_diff( $tag_ids, array( $tag_id ) );
						$product->set_tag_ids( $tag_ids );
					}

					$product->save();
				}
			} else {

				/* Remove attribute */
				if ( isset( $attributes[ $pa_name ] ) ) {
					unset( $attributes[ $pa_name ] );
					$product->set_attributes( $attributes );
				}

				/* Remove tag */
				if ( $tag_id && 0 !== $tag_id ) {

					$tag_ids = array_diff( $tag_ids, array( $tag_id ) );
					$product->set_tag_ids( $tag_ids );
				}

				$product->save();
			}
		}
	}


	/**
	 *
	 * Create a new product or update it if exists
	 *
	 * @retur void
	 */
	public function single_product() {

		$data = $this->product_data();
		$id   = $this->get_product_id( $data );

		if ( ! $id ) {

			$id = $this->create_new_product( $data );

			/* Get previously exported product details */
			$this->get_prev_exported_product_details( $id, $data );

		} else {

			$this->update_product( $id, $data );
		}

		/* Handle product variants */
		$this->danea_variants( $id, $data );

		/* Handle product categories */
		$this->product_categories( $id, $data );

		/* Handle product image */
		$this->single_product_image( $id, $data );

		/* Handle product attributes */
		$this->product_attributes( $id, $data );

		/* Handle Danea Easyfatt custom fields */
		$this->danea_custom_fields( $id, $data );

		/* Deletes the temporary data from the database */
		$this->temp->wcifd_delete_temporary_data( $this->hash );
	}
}

new WCIFD_Import_Single_Product();

