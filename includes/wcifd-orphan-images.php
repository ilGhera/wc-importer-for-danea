<?php
/**
 * Match the images with the respective products
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

/**
 * Image matching
 *
 * @return void
 */
function wcifd_orphan_images() {

	$temp = new WCIFD_Temporary_Data();

	$orphan_images = $temp->wcifd_get_temporary_images_data();

	if ( is_array( $orphan_images ) && ! empty( $orphan_images ) ) {

		foreach ( $orphan_images as $image ) {

			as_enqueue_async_action(
				'wcifd_product_image_event',
				array(
					$image['hash'],
				),
				'wcifd-product-image'
			);

		}
	}

	/* Abort if all products have been transferred and images managed */
	$next = as_next_scheduled_action(
		'wcifd_import_product_event',
		array(),
		'wcifd-import-product'
	);

	if ( ! $next ) {

		if ( is_array( $orphan_images ) && empty( $orphan_images ) ) {

			/* Schedule an action to stop the recurring process */
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
 * Stops the scheduled action of assigning orphaned images
 *
 * @return void
 */
function wcifd_stop_orphan_images() {

	as_unschedule_action( 'wcifd_orphan_images_event' );

}
add_action( 'wcifd_stop_orphan_images_event', 'wcifd_stop_orphan_images' );

