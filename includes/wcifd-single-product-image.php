<?php
/**
 * Abbinamento immagine a singolo prodotto
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @since 1.2.1
 */
function wcifd_single_product_image( $product_id, $image_file_name, $orphan = false ) {

	$attachment = get_page_by_title( $image_file_name, OBJECT, 'attachment' );

	if ( $product_id && isset( $attachment->ID ) ) {

		/*Lego l'immagine al prodotto*/
		set_post_thumbnail( $product_id, $attachment->ID );

		/*Assegno il prodotto come post_parent dell'immagine*/
		$updated = wp_update_post(
			array(
				'ID'          => $attachment->ID,
				'post_parent' => $product_id,
			)
		);
		
	}

	if ( $product_id && $orphan ) {
		
		$orphanImages = json_decode( get_option('wcifd-orphan-images'), true );
		
		if ( isset( $orphanImages[ $product_id ] ) ) {
			
			unset( $orphanImages[ $product_id ] );
		
		}

		update_option( 'wcifd-orphan-images', json_encode( $orphanImages, JSON_FORCE_OBJECT ) );

	}

}
add_action( 'wcifd_product_image_event', 'wcifd_single_product_image', 10, 3 );
