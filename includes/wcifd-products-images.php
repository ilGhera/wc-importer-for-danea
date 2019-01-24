<?php
/**
 * Importazione immagine prodotto
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @version 1.1.0
 */
function wcifd_products_images() {

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	/*File immagine*/
	$file = $_FILES['file'];

	/*Elimino duplicato se presente*/
	$old_attach = get_page_by_title( sanitize_title( $file['name'] ), OBJECT, 'attachment' );
	if ( isset( $old_attach->ID ) ) {
		wp_delete_post( $old_attach->ID );
	}

	/*Caricamento immagine in WP Media*/
	$wp_image = wp_handle_upload( $file, array( 'test_form' => false ) );

	if ( ! $wp_image || isset( $wp_image['error'] ) ) {
		echo 'Errore durante il caricamento delle immagini.';
		exit;
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
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	/*Generazione e aggiornamento metadati*/
	$attach_data = wp_generate_attachment_metadata( $attach_id, $wp_image['file'] );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	echo 'OK';
}
