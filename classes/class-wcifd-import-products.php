<?php
/**
 * Import products from a XML/CSV
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/classes
 *
 * @since 1.6.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Import_Products
 *
 * @since 1.6.1
 */
class WCIFD_Import_Products {

	/**
	 * The file
	 *
	 * @var file
	 */
	public $file;

	/**
	 * Is CSV
	 *
	 * @var bool
	 */
	public $is_csv;

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->import_file();

	}

	/**
	 * Import file
	 *
	 * @return void
	 */
	private function import_file() {

		if ( isset( $_POST['products-import'], $_POST['wcifd-products-file-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-products-file-nonce'] ) ), 'wcifd-products-file' ) ) {

			$this->file = isset( $_FILES['products-list']['tmp_name'] ) ? sanitize_text_field( wp_unslash( $_FILES['products-list']['tmp_name'] ) ) : null;
			$file_type  = isset( $_POST['file-type'] ) ? sanitize_text_field( wp_unslash( $_POST['file-type'] ) ) : null;

			update_option( 'wcifd-file-type', $file_type );

			$this->is_csv = 'xml' === $file_type ? false : true;
			add_action( 'wp_loaded', array( $this, 'file_handler' ) );
		}
	}

	/**
	 * Invoke the catalog update
	 *
	 * @return void
	 */
	public function file_handler() {

		new WCIFD_Catalog_Update( $this->file, $this->is_csv );
	}
}

