<?php
/**
 * Matching image to single product
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Matching image to single product
 *
 * @since 1.7.0
 */
class WCIFD_Single_Product_Image {

	/**
	 * Instance of WCIFD_Temporary_Data
	 *
	 * @var WCIFD_Temporary_Data
	 */
	public $temp;

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->temp = new WCIFD_Temporary_Data();

		add_action( 'wcifd_product_image_event', array( $this, 'set_product_image' ), 10, 1 );
	}

	/**
	 * Set product image
	 *
	 * @param string $hash the hash code that identifies the match to be made.
	 *
	 * @return void
	 */
	public function set_product_image( $hash ) {

		/* Vars */
		$data       = $this->temp->wcifd_get_temporary_data( $hash, true );
		$product_id = isset( $data['product_id'] ) ? (int) $data['product_id'] : 0;
		$image_name = isset( $data['image_name'] ) ? $data['image_name'] : '';

		error_log( '=== WCIFD | Associazione immagine prodotto ================' );
		error_log( 'Hash: ' . $hash );
		error_log( 'ID prodotto: ' . $product_id );
		error_log( 'Nome immagine: ' . $image_name );

		if ( 0 < $product_id && $image_name ) {

			/* Get the image ID */
			$attachment_id = $this->get_image_id( $image_name );

			if ( $attachment_id ) {

				/* Link image to the product */
				set_post_thumbnail( $product_id, $attachment_id );

				/* Assign the product as post_parent of the image */
				$updated = wp_update_post(
					array(
						'ID'          => $attachment_id,
						'post_parent' => $product_id,
					)
				);

				if ( 0 !== $updated && ! is_wp_error( $updated ) ) {

					error_log( 'Immagine assegnata al prodotto' );

					$this->temp->wcifd_delete_temporary_data( $hash, true );
				}
			}
		}

		error_log( '===========================================================' );
	}

	/**
	 * Retrieves an attachment ID by its stored original filename, with fallback to post_name.
	 *
	 * @param string $image_name The original image filename from Danea.
	 *
	 * @return int|false The attachment ID if found, false otherwise.
	 */
	public function get_image_id( $image_name ) {

		error_log( 'WCIFD | Nome immagine cercata: ' . $image_name );

		/* Attempt to retrieve using the new custom post meta */
		$image_id = $this->get_image_id_by_meta( $image_name );
		error_log( 'ID immagine da postmeta: ' . $image_id );

		if ( ! $image_id ) {

			/* If not found with the meta, try the old method (post_name/slug) */
			$image_id = $this->get_image_id_by_name( $image_name );
			error_log( 'ID immagine da nome: ' . $image_id );
		}

		if ( ! $image_id ) {

			/* Fallback: search by filename in guid or post_title */
			$image_id = $this->get_image_id_by_fallback( $image_name );
			error_log( 'ID immagine da fallback: ' . $image_id );
		}

		return $image_id;
	}

	/**
	 * Retrieves an attachment ID by its stored original filename.
	 *
	 * @param string $image_name The image name.
	 *
	 * @return int|false The attachment ID if found, false otherwise.
	 */
	public function get_image_id_by_meta( $image_name ) {

		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_query'     => array(
				array(
					'key'     => '_wcifd_original_filename',
					'value'   => $image_name,
					'compare' => '=',
				),
			),
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		);

		$image_ids = get_posts( $args );

		return ( ! empty( $image_ids ) ) ? $image_ids[0] : false;
	}

	/**
	 * Get image by name
	 *
	 * @param string $image_name The image name.
	 *
	 * @return int|false The attachment ID if found, false otherwise.
	 */
	public function get_image_id_by_name( $image_name ) {

        $output        = false;
		$attachment_id = null;

		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'name'           => sanitize_title( $image_name ),
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		);

		$image_ids_old_slug = get_posts( $args );

		if ( ! empty( $image_ids_old_slug ) && isset( $image_ids_old_slug[0] ) ) {

			/* Add original filename to the DB */
			update_post_meta( $image_ids_old_slug[0], '_wcifd_original_filename', $image_name );

			$output = $image_ids_old_slug[0];
		}

        return $output;
	}

	/**
	 * Fallback search for attachment by filename in guid or post_title.
	 *
	 * @param string $image_name The original image filename.
	 *
	 * @return int|false The attachment ID if found, false otherwise.
	 */
	public function get_image_id_by_fallback( $image_name ) {

		global $wpdb;

		// Rimuovi l'estensione per una ricerca più flessibile
		$clean_name = preg_replace( '/\\.[^.\\s]{3,4}$/', '', $image_name );
		$clean_name = sanitize_title( $clean_name );

		// Cerca nel post_title (senza estensione) e nel guid (URL che contiene il nome file)
		$query = $wpdb->prepare(
			"SELECT ID FROM $wpdb->posts 
			WHERE post_type = 'attachment' 
			AND post_status = 'inherit'
			AND ( post_title LIKE %s OR guid LIKE %s )
			LIMIT 1",
			'%' . $wpdb->esc_like( $clean_name ) . '%',
			'%' . $wpdb->esc_like( $image_name ) . '%'
		);

		$attachment_id = $wpdb->get_var( $query );

		if ( $attachment_id ) {
			// Salva il meta per futuri riferimenti
			update_post_meta( $attachment_id, '_wcifd_original_filename', $image_name );
			error_log( 'WCIFD | Immagine trovata con fallback: ' . $attachment_id );
		}

		return $attachment_id ? (int) $attachment_id : false;
	}
}

