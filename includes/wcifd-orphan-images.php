<?php
/**
 * Ricevute tutte le immagini dal gestionale, abbina quelle non legate al rispettivo prodotto
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @since 1.1.6
 */
function wcifd_orphan_images() {

	$orphanImages = json_decode( get_option('wcifd-orphan-images'), true );
		
	if ( is_array( $orphanImages ) && !empty( $orphanImages ) ) {

		foreach ($orphanImages as $key => $value) {

			wp_schedule_single_event(
				time() + 1,
				'wcifd_product_image_event',
				array(
					$key,
					$value,
					true,
				)
			);

		}

	}

	/*Interrompo se tutti i prodotti sono stati trasferiti e le immagini gestite*/
	if ( ! wp_next_scheduled( 'wcifd_import_product_event' ) ) {
	
		if ( is_array( $orphanImages ) && empty( $orphanImages ) ) {
	
			$timestamp = wp_next_scheduled( 'wcifd_orphan_images_event' );
			wp_unschedule_event( $timestamp, 'wcifd_orphan_images_event' );
	
		}
	
	}

}
add_action( 'wcifd_orphan_images_event', 'wcifd_orphan_images' );
