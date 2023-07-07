<?php
/**
 * Importazione immagine prodotto
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

/**
 * Importazione immagine
 *
 * @return void
 */
function wcifd_products_images() {

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	$file = isset( $_FILES['file'] ) ? $_FILES['file'] : null;

	/*Elimino duplicato se presente*/
	$args = array(
		'post_type'   => 'attachment',
		'post_status' => 'inherit',
		'name'        => sanitize_title( $file['name'] ),
	);

	$images = new WP_Query( $args );

	if ( $images->have_posts() ) {

		foreach ( $images->posts as $old_attach ) {

			wp_delete_post( $old_attach->ID );

		}
	}

	wp_reset_postdata();

	/*Caricamento immagine in WP Media*/
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

	/*Indirizzo immagine caricata*/
	$image_url = $wp_image['url'];

	$filetype = wp_check_filetype( basename( $image_url ), null );

	/*Upload directory*/
	$wp_upload_dir = wp_upload_dir();

	$attachment = array(
		'guid'           => $wp_image['url'],
		'post_mime_type' => $filetype['type'],
		'post_title'     => sanitize_title( $file['name'] ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	/*Inserimento attachment*/
	$attach_id = wp_insert_attachment( $attachment, $wp_image['file'] );

	/*Richiesto da wp_generate_attachment_metadata()*/
	require_once ABSPATH . 'wp-admin/includes/image.php';

	/*Generazione e aggiornamento metadati*/
	$attach_data = wp_generate_attachment_metadata( $attach_id, $wp_image['file'] );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	echo 'OK';
}
