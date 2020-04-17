<?php
/**
 * Ricevute tutte le immagini dal gestionale, abbina quelle non legate al rispettivo prodotto
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @since 1.3.0
 */
function wcifd_orphan_images() {

	$orphan_images = json_decode( get_option( 'wcifd-orphan-images' ), true );

	if ( is_array( $orphan_images ) && ! empty( $orphan_images ) ) {

		foreach ( $orphan_images as $key => $value ) {

			as_enqueue_async_action(
				'wcifd_product_image_event',
				array(
					$key,
					$value,
					true,
				),
				'wcifd-product-image'
			);

		}

	}

	/*Interrompo se tutti i prodotti sono stati trasferiti e le immagini gestite*/
	$next = as_next_scheduled_action(
		'wcifd_import_product_event',
		array(),
		'wcifd-import-product'
	);

	if ( ! $next ) {

		if ( is_array( $orphan_images ) && empty( $orphan_images ) ) {

			/*Schedulo un azione per interrompere il processo ricorrente*/
			as_enqueue_async_action(
				'wcifd_stop_orphan_images_event',
				array(),
				'wcifd-orphan-images'
			);

		}

	}

}
add_action( 'wcifd_orphan_images_event', 'wcifd_orphan_images' );


/**
 * Interrompe l'azione programmata di assegnazione delle immagini orfane
 *
 * @return void
 */
function wcifd_stop_orphan_images() {

	as_unschedule_action( 'wcifd_orphan_images_event' );

}
add_action( 'wcifd_stop_orphan_images_event', 'wcifd_stop_orphan_images' );
