<?php
/**
 * Abbinamento immagine a singolo prodotto
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @since 1.3.1
 *
 * @param string $hash il codice del prodotto WooCommerce che identifica l'abbinamento da eseguire.
 * @return void
 */
function wcifd_single_product_image( $hash ) {

	$temp       = new WCIFD_Temporary_Data();
	$data       = $temp->wcifd_get_temporary_data( $hash, true );
	$product_id = isset( $data['product_id'] ) ? $data['product_id'] : '';
	$image_name = isset( $data['image_name'] ) ? $data['image_name'] : '';

	if ( $product_id && $image_name ) {

		$attachment = get_page_by_title( $image_name, OBJECT, 'attachment' );

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

			if ( 0 !== $updated && ! is_wp_error( $updated ) ) {

				$temp->wcifd_delete_temporary_data( $hash, true );

			}

		}

	}

}
add_action( 'wcifd_product_image_event', 'wcifd_single_product_image', 10, 3 );
