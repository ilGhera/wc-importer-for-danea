<?php
/**
 * WCIFD Temporary Data
 *
 * Handles the products data received from Danea Easyfatt.
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/classes
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Temporary_Data
 *
 * @since 1.7.0
 */
class WCIFD_Temporary_Data {

	/**
	 * The constructor
	 *
	 * @param boolean $init execute hooks with true.
	 *
	 * @retur void
	 */
	public function __construct( $init = false ) {

		if ( $init ) {

			$this->wcifd_db_tables();
		}
	}

	/**
	 * Create the expected tables if not present
	 *
	 * @return void
	 */
	public function wcifd_db_tables() {

		global $wpdb;

		$temporary_data   = $wpdb->prefix . 'wcifd_temporary_data';
		$temporary_images = $wpdb->prefix . 'wcifd_temporary_images';

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $temporary_data ) ) !== $temporary_data ) {

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $temporary_data (
				id 			bigint(20) NOT NULL AUTO_INCREMENT,
				hash        varchar(255) NOT NULL,
				data 		longtext NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			dbDelta( $sql );

		}

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $temporary_images ) ) !== $temporary_images ) {

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $temporary_images (
				id 			bigint(20) NOT NULL AUTO_INCREMENT,
				hash        varchar(255) NOT NULL,
				product_id 	bigint(20) NOT NULL,
				image_name 	text NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			dbDelta( $sql );
		}

	}

	/**
	 * Get the temporary product data from the DB table
	 *
	 * @param string $hash  the single product hash.
	 * @param bool   $image get data about the image with true.
	 *
	 * @return array the product data
	 */
	public function wcifd_get_temporary_data( $hash, $image = false ) {

		global $wpdb;

		$table = $image ? 'wcifd_temporary_images' : 'wcifd_temporary_data';
		$table = "{$wpdb->prefix}$table";

		$results = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT * FROM %1$s WHERE `hash` = \'%2$s\'',
				$table,
				$hash
			),
			ARRAY_A
		);

		if ( isset( $results[0] ) ) {

			if ( $image ) {

				return $results[0];

			} elseif ( isset( $results[0]['data'] ) ) {

				return json_decode( $results[0]['data'], true );
			}
		}
	}

	/**
	 * Returns all the product/image combinations of the dedicated table
	 *
	 * @return array
	 */
	public function wcifd_get_temporary_images_data() {

		global $wpdb;

		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wcifd_temporary_images", ARRAY_A );

		return $results;

	}

	/**
	 * Adds the product data in the dedicated table waiting for it to be created
	 *
	 * @param string $hash the single product hash.
	 * @param string $data the product data.
	 *
	 * @return void
	 */
	public function wcifd_add_temporary_data( $hash, $data ) {

		global $wpdb;

		$results = $this->wcifd_get_temporary_data( $hash );

		if ( null === $results ) {

			$wpdb->insert(
				$wpdb->prefix . 'wcifd_temporary_data',
				array(
					'hash' => $hash,
					'data' => $data,
				),
				array(
					'%s',
					'%s',
				)
			);
		}
	}

	/**
	 * Adds product id and image name in the dedicated table waiting for them to be matched
	 *
	 * @param string $hash        the single product hash.
	 * @param int    $product_id  the WooCommerce product ID.
	 * @param  string $image_name the name of the image coming from Danea Easyfatt.
	 *
	 * @return void
	 */
	public function wcifd_add_temporary_image( $hash, $product_id, $image_name ) {

		global $wpdb;

		$results = $this->wcifd_get_temporary_data( $hash, true );

		if ( null === $results ) {

			$wpdb->insert(
				$wpdb->prefix . 'wcifd_temporary_images',
				array(
					'hash'       => $hash,
					'product_id' => $product_id,
					'image_name' => $image_name,
				),
				array(
					'%s',
					'%d',
					'%s',
				)
			);
		}
	}

	/**
	 * Delete the temporary data from the DB table.
	 *
	 * @param string $hash  the single product hash.
	 * @param bool   $image delete data from the image table with true.
	 *
	 * @return void
	 */
	public function wcifd_delete_temporary_data( $hash, $image = false ) {

		global $wpdb;

		$table = $image ? 'wcifd_temporary_images' : 'wcifd_temporary_data';

		$wpdb->delete(
			$wpdb->prefix . $table,
			array(
				'hash' => $hash,
			),
			array(
				'%s',
			)
		);
	}
}

