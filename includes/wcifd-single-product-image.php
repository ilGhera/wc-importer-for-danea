<?php
/**
 * Abbinamento immagine a singolo prodotto
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @version 1.1.0
 */
function wcifd_single_product_image( $product_id, $image_file_name ) {

	$attachment = get_page_by_title( $image_file_name, OBJECT, 'attachment' );

	if ( isset( $attachment->ID ) ) {

		/*Lego l'immagine al prodotto*/
		set_post_thumbnail( $product_id, $attachment->ID );

		/*Assegno il prodotto come post_parent dell'immagine*/
		wp_update_post(
			array(
				'ID'          => $attachment->ID,
				'post_parent' => $product_id,
			)
		);
	}

}
add_action( 'wcifd_product_image_event', 'wcifd_single_product_image', 10, 2 );
