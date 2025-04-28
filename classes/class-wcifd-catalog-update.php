<?php
/**
 * Update products catalog
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.2
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Catalog_Update
 *
 * @param file $file   the file imported.
 * @param bool $is_csv csv with true, XML instead.
 *
 * @return void
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

		/* Import products */
		$this->import_products();
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

			$handle = fopen( $this->file, 'r' );

			if ( false !== ( $handle ) ) {

				$headers = fgetcsv( $handle );
				$row     = fgetcsv( $handle );

				while ( false !== ( $row ) ) {

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

					wcifd_delete_all_products();
				}

				/* Check if the update is full or not */
				$products = $results->Products ? $results->Products : $results->UpdatedProducts;
				$products = $products->children();
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

		if ( is_array( $products ) && ! empty( $products ) ) {

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
					'regular_price_list' => $this->regular_price_list,
					'sale_price_list'    => $this->sale_price_list,
					'size_type'          => $this->size_type,
					'weight_type'        => $this->weight_type,
					'tax_attributes'     => $tax_attributes,
					'deleted_products'   => $this->deleted_products,
					'wc_rbp'             => $this->wc_rbp,
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
	 * @param object $results the data coming from the XML file.
	 *
	 * @return void
	 */
	public function delete_products( $results ) {

		/* Get products to be deleted */
		$products = $this->get_products( true );

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
}

