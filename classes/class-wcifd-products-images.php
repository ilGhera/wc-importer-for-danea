<?php
/**
 * Importing product images
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Products_Images
 *
 * @return void
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

		$file = isset( $_FILES['file'] ) ? $_FILES['file'] : null;

		/* Delete duplicates */
		$this->delete_duplicates( $file );

		/* Load image in WP Media */
		$wp_image = wp_handle_upload( $file, array( 'test_form' => false ) );

		if ( isset( $wp_image['error'] ) ) {

			error_log( 'WCIFD ERROR | Immagine: ' . $file['name'] . ' | ' . print_r( $wp_image['error'], true ) );

			echo 'OK';

			return;

		} elseif ( ! $wp_image ) {

			error_log( 'WCIFD ERROR | Immagine: ' . $file['name'] . ' |  Errore di ricezione' );

			echo 'OK';

			return;
		}

		/* Image URL */
		$image_url = $wp_image['url'];

		$filetype = wp_check_filetype( basename( $image_url ), null );

		/* Upload directory */
		$wp_upload_dir = wp_upload_dir();

		$attachment = array(
			'guid'           => $wp_image['url'],
			'post_mime_type' => $filetype['type'],
			'post_title'     => sanitize_title( $file['name'] ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		/* Add attachment */
		$attach_id = wp_insert_attachment( $attachment, $wp_image['file'] );

		/* Requide by wp_generate_attachment_metadata() */
		require_once ABSPATH . 'wp-admin/includes/image.php';

		/* Generate and update metadata */
		$attach_data = wp_generate_attachment_metadata( $attach_id, $wp_image['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		echo 'OK';
	}

	/**
	 * Delete duplicates
	 *
	 * @param array $file the file imported.
	 *
	 * @return void
	 */
	public function delete_duplicates( $file ) {

		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'name'           => sanitize_title( $file['name'] ),
			'fields'         => 'ids',
			'posts_per_page' => -1,
		);

		$image_ids = get_posts( $args );

		if ( is_array( $image_ids ) && ! empty( $image_ids ) ) {

			foreach ( $image_ids as $id ) {

				wp_delete_post( $id, true );
			}
		}

		wp_reset_postdata();
	}
}

