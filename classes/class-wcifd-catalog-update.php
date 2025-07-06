<?php
/**
 * Update products catalog
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/classes
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Catalog_Update
 *
 * @since 1.7.0
 */
class WCIFD_Catalog_Update {

	/**
	 * File
	 *
	 * @var string $file
	 */
	public $file;

	/**
	 * Is CSV
	 *
	 * @var bool $is_csv
	 */
	public $is_csv;

	/**
	 * Regular price list
	 *
	 * @var string $regular_price_list
	 */
	public $regular_price_list;

	/**
	 * Sale price list
	 *
	 * @var string $sale_price_list
	 */
	public $sale_price_list;

	/**
	 * Size type
	 *
	 * @var string $size_type
	 */
	public $size_type;

	/**
	 * Weight type
	 *
	 * @var string $weight_type
	 */
	public $weight_type;

	/**
	 * Deleted products
	 *
	 * @var bool $deleted_products
	 */
	public $deleted_products;

	/**
	 * Replace products
	 *
	 * @var bool $replace_products
	 */
	public $replace_products;

	/**
	 * WC Role Based Price
	 *
	 * @var array $wc_rbp
	 */
	public $wc_rbp;

	/**
	 * WC Role Based Pricing
	 *
	 * @var array $wc_mrbp
	 */
	public $wc_mrbp;

	/**
	 * The constructor
	 *
	 * @param file $file   the file imported.
	 * @param bool $is_csv csv with true, XML instead.
	 *
	 * @return void
	 */
	public function __construct( $file, $is_csv = false ) {

		/* Vars */
		$this->file   = $file;
		$this->is_csv = $is_csv;

		/* Admin options */
		$this->regular_price_list = get_option( 'wcifd-regular-price-list' );
		$this->sale_price_list    = get_option( 'wcifd-sale-price-list' );
		$this->size_type          = get_option( 'wcifd-size-type' );
		$this->weight_type        = get_option( 'wcifd-weight-type' );
		$this->deleted_products   = get_option( 'wcifd-deleted-products' );
		$this->replace_products   = get_option( 'wcifd-replace-products' );

		/* WooCommerce Role Based Price */
		$this->wc_rbp = WCIFD_Functions::get_wc_rbp();

		/* WooCommerce Role Based Pricing */
		$this->wc_mrbp = WCIFD_Functions::get_wc_mrbp();

		/* Import products */
		$this->import_products();

		/* Delete products */
		$this->delete_products();
	}

	/**
	 * Get products
	 *
	 * @param bool $delete get products to be deleted.
	 *
	 * @return array
	 */
	public function get_products( $delete = false ) {

		$products = array();

		if ( $this->is_csv ) {

			if ( $delete ) {

				return;
			}

			if ( false !== ( $handle = fopen( $this->file, 'r' ) ) ) {

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
			$results = simplexml_load_file( $this->file );

			if ( $delete ) {

				$products = isset( $results->DeletedProducts ) ? $results->DeletedProducts : null;

			} else {

				/* Delete all products */
				if ( $this->replace_products && 'full' === strval( $results->attributes()->Mode[0] ) ) {

					$this->delete_all_products();
				}

				/* Check if the update is full or not */
				$products = $results->Products ? $results->Products : $results->UpdatedProducts;
			}
		}

		return $products;
	}

	/**
	 * Import products
	 *
	 * @return void
	 */
	public function import_products() {

		/* Get products */
		$products = $this->get_products();

		if ( $products && $products->children() ) {

			/* Set transient for progress bar */
			set_transient( 'wcifd-total-actions', count( $products->children() ), DAY_IN_SECONDS );

			foreach ( $products->children() as $product ) {

				/* Vat */
				$tax_attributes = null;
				if ( isset( $product->Vat ) ) {

					$tax_attributes = $product->Vat->attributes();
				}

				$data = array(
					'product'            => $product,
					'regular_price_list' => $this->regular_price_list,
					'sale_price_list'    => $this->sale_price_list,
					'size_type'          => $this->size_type,
					'weight_type'        => $this->weight_type,
					'tax_attributes'     => $tax_attributes,
					'deleted_products'   => $this->deleted_products,
					'wc_rbp'             => $this->wc_rbp,
					'wc_mrbp'            => $this->wc_mrbp,
					'is_csv'             => $this->is_csv,
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
		}
	}

	/**
	 * Delete products
	 *
	 * @return void
	 */
	public function delete_products() {

		/* Get products to be deleted */
		$products = $this->get_products( true );

		/* Delete products */
		if ( $products && $products->children() ) {

			/* Set transient for progress bar */
			set_transient( 'wcifd-total-delete-actions', count( $products->children() ), DAY_IN_SECONDS );

			foreach ( $products->children() as $del_product ) {

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

	/**
	 * Handle the single product delete event
	 *
	 * @param string $product_sku the sku of the single product to be deleted, encoded in json.
	 *
	 * @return void
	 */
	public static function delete_single_product( $product_sku ) {

		$sku = json_decode( $product_sku, true );

		if ( isset( $sku[0] ) ) {

			$product_id = WCIFD_Functions::search_product( $sku[0] );

			if ( $product_id ) {

				$product = wc_get_product( $product_id );

				/* Delete product */
				$deleted = $product->delete( true );

				if ( $deleted ) {

					/* Delete transients */
					wc_delete_product_transients( $product_id );

				} else {

					error_log( 'WCIFD ERROR | Eliminazione prodotto | ID: ' . $product_id );
				}
			}
		}
	}

	/**
	 * Delete all products in WooCommerce
	 *
	 * @return void
	 */
	public function delete_all_products() {

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		);

		$product_ids = get_posts( $args );

		if ( $product_ids ) {

			foreach ( $product_ids as $product_id ) {

				/* Get product */
				$product = wc_get_product( $product_id );

				if ( $product ) {

					/* Delete product */
					$deleted = $product->delete( true );

					if ( $deleted ) {

						/* Delete transients */
						wc_delete_product_transients( $product_id );

					} else {

						error_log( 'WCIFD ERROR | Eliminazione prodotto | ID: ' . $product_id );
					}
				}
			}
		}
	}
}

