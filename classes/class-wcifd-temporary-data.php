<?php
/**
 * WCIFD Temporary Data
 *
 * Gestisce i dati dei prodotti provenienti da Danea Easyfatt.
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/classes
 *
 * @since 1.6.0
 */

/**
 * Class WCIFD_Temporary_Data
 */
class WCIFD_Temporary_Data {

	/**
	 * The constructor
	 *
	 * @param boolean $init true per eseguire hooks iniziali.
	 */
	public function __construct( $init = false ) {

		if ( $init ) {

			$this->wcifd_db_tables();

		}

	}

	/**
	 * Crea le tabelle previste dal plugin se non presenti
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
	 * Recupera i dati temporanei del prodotto nella tabella dedicata
	 *
	 * @param  string $hash  il codice identificativo del prodotto.
	 * @param  bool   $image se true recupera i dati dalla tabella delle immagini.
	 * @return array i dati del prodotto
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

		if ( $image && isset( $results[0] ) ) {

			return $results[0];

		} elseif ( isset( $results[0]['data'] ) ) {

			return json_decode( $results[0]['data'], true );

		}

	}


	/**
	 * Restituisce tutti gli abbinamenti prodotto/ immagine della tabella dedicata
	 *
	 * @return array
	 */
	public function wcifd_get_temporary_images_data() {

		global $wpdb;

		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wcifd_temporary_images", ARRAY_A );

		return $results;

	}


	/**
	 * Aggiunge i dati del prodotto nella tabella dedicata in attesa che venga creato
	 *
	 * @param  string $hash il codice identificativo del prodotto.
	 * @param  string $data tutti i dati del prodotto.
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
	 * Aggiunge id prodotto e nome dell'immagine nella tabella dedicata in attesa che vengano abbinati
	 *
	 * @param  string $hash il codice identificativo del prodotto.
	 * @param  int    $product_id l'id del prodotto WooCommerce.
	 * @param  string $image_name il nome dell'immagine proveniente da Danea Easyfatt.
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
	 * Cancella i dati temporanei dalla tabella dedicata
	 *
	 * @param  string $hash  il codice identificativo del prodotto.
	 * @param  bool   $image se true cancella dati dalla tabella delle immagini.
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
new WCIFD_Temporary_Data( true );
