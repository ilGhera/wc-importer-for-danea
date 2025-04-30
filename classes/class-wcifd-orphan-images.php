<?php
/**
 * Match the images with the respective products
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Image matching
 *
 * @return void
 */
class WCIFD_Orphan_Images {

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		add_action( 'wcifd_orphan_images_event', array( $this, 'start_orphan_images' ) );
		add_action( 'wcifd_stop_orphan_images_event', array( $this, 'stop_orphan_images' ) );
	}

	/**
	 * Start the orphan images process
	 *
	 * @return void
	 */
	public function start_orphan_images() {

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

		/* Check if next action exists */
		$this->check_next_action( $orphan_images );
	}

	/**
	 * Check if the orphan images process can be stopped
	 *
	 * @param array $orphan_images the orphan images.
	 *
	 * @return void
	 */
	public function check_next_action( $orphan_images ) {

		/* Get next scheduled action */
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

	/**
	 * Stop the orphan images process
	 *
	 * @return void
	 */
	public function stop_orphan_images() {

		as_unschedule_action( 'wcifd_orphan_images_event' );
	}
}

new WCIFD_Orphan_Images();

