<?php
/**
 * Importing product images
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Products_Images
 *
 * @since 1.7.0
 */
class WCIFD_Products_Images {

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->handle_image_upload();
	}

	/**
	 * Handle image upload
	 *
	 * @return void
	 */
	public function handle_image_upload() {

		if ( ! function_exists( 'wp_handle_upload' ) ) {

			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {

			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$file = isset( $_FILES['file'] ) ? $_FILES['file'] : null;

		if ( ! $file || ! is_array( $file ) || empty( $file['tmp_name'] ) ) {

			echo 'OK';
			return;
		}

		/* The original filename */
		$original_file_name = $file['name'];
		error_log( 'WCIFD | Ricevuta richiesta upload per: ' . $original_file_name );

		/* Delete duplicates */
		$this->delete_duplicates( $original_file_name );

		/* Get the upload directory */
		$upload_dir  = wp_upload_dir();
		$upload_path = $upload_dir['path'];

		/* Create a unique filename based on the original file name */
		$unique_file_name = wp_unique_filename( $upload_path, $original_file_name );

		/* The uploaded file data */
		$uploaded_file_data = array(
			'name'     => $unique_file_name,
			'type'     => $file['type'],
			'tmp_name' => $file['tmp_name'],
			'error'    => $file['error'],
			'size'     => $file['size'],
		);

		/* Upload the image in WP Media */
		$wp_image = wp_handle_upload( $uploaded_file_data, array( 'test_form' => false ) );

		if ( isset( $wp_image['error'] ) ) {

			error_log( 'WCIFD ERROR | Immagine: ' . $file['name'] . ' | ' . print_r( $wp_image['error'], true ) );
			echo 'OK';
			return;

		} elseif ( ! $wp_image ) {

			error_log( 'WCIFD ERROR | Immagine: ' . $file['name'] . ' |  Errore di ricezione' );
			echo 'OK';
			return;
		}

		/* Get the file type */
		$filetype = wp_check_filetype( basename( $wp_image['file'] ), null );

		/* The attachment data */
		$attachment = array(
			'guid'           => $wp_image['url'],
			'post_mime_type' => $filetype['type'],
			'post_title'     => sanitize_title( $original_file_name ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		/* Add attachment */
		$attach_id = wp_insert_attachment( $attachment, $wp_image['file'] );

		if ( is_wp_error( $attach_id ) ) {
			error_log( 'WCIFD ERROR | Creazione attachment fallita per: ' . $original_file_name . ' | ' . $attach_id->get_error_message() );
			echo 'OK';
			return;
		}

		error_log( 'WCIFD | Attachment creato - ID: ' . $attach_id . ' per file: ' . $original_file_name );

		/* Generate and update metadata */
		$attach_data = wp_generate_attachment_metadata( $attach_id, $wp_image['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		/* Add post meta to the attachment */
		update_post_meta( $attach_id, '_wcifd_original_filename', $original_file_name );
		error_log( 'WCIFD | Meta _wcifd_original_filename salvato per attachment ID ' . $attach_id . ': ' . $original_file_name );

		echo 'OK';
	}

	/**
	 * Delete duplicates by original filename stored in post meta.
	 *
	 * @param string $original_filename The original filename from the Danea system.
	 *
	 * @return void
	 */
	public function delete_duplicates( $original_filename ) {

		$image_ids_to_delete = array();

		$image_ids_by_meta = $this->get_image_ids_by_meta( $original_filename );
		$image_ids_by_name = $this->get_image_ids_by_name( $original_filename );

		error_log( '=== WCIFD | Eliminazione duplicati ========================' );
		error_log( 'Nome originale: ' . $original_filename );
		error_log( 'Slug sanitizzato: ' . sanitize_title( $original_filename ) );
		error_log( 'Trovati per meta: ' . print_r( $image_ids_by_meta, true ) );
		error_log( 'Trovati per nome: ' . print_r( $image_ids_by_name, true ) );

		if ( ! empty( $image_ids_by_meta ) ) {
			$image_ids_to_delete = $image_ids_by_meta;
			error_log( 'Duplicati da eliminare (trovati per meta): ' . implode( ', ', $image_ids_to_delete ) );
		} elseif ( ! empty( $image_ids_by_name ) ) {
			$image_ids_to_delete = $image_ids_by_name;
			error_log( 'Duplicati da eliminare (trovati per nome): ' . implode( ', ', $image_ids_to_delete ) );
		} else {
			error_log( 'Nessun duplicato trovato.' );
		}

		/* Deleting process */
		if ( ! empty( $image_ids_to_delete ) ) {
			foreach ( $image_ids_to_delete as $id ) {
				wp_delete_post( $id, true );
				error_log( 'Eliminato duplicato per ' . $original_filename . ' (ID: ' . $id . ')' );
			}
		} else {
			error_log( 'Nessuna eliminazione eseguita.' );
		}

		error_log( '===========================================================' );
	}

	/**
	 * Get images by original filename stored in post meta.
	 *
	 * @param string $original_filename The original filename from the Danea system.
	 *
	 * @return array An array of attachment IDs.
	 */
	public function get_image_ids_by_meta( $original_filename ) {

		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_query'     => array(
				array(
					'key'     => '_wcifd_original_filename',
					'value'   => $original_filename,
					'compare' => '=',
				),
			),
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		);

		return get_posts( $args );
	}

	/**
	 * Get images by original filename.
	 *
	 * @param string $original_filename The original filename from the Danea system.
	 *
	 * @return array An array of attachment IDs.
	 */
	public function get_image_ids_by_name( $original_filename ) {

		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'name'           => sanitize_title( $original_filename ),
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		);

		return get_posts( $args );
	}
}

