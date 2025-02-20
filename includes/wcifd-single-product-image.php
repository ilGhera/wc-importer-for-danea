<?php
/**
 * Matching image to single product
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

/**
 * Matching image to single product
 *
 * @param string $hash the WooCommerce product code that identifies the match to be made.
 *
 * @return void
 */
function wcifd_single_product_image( $hash ) {

	$temp       = new WCIFD_Temporary_Data();
	$data       = $temp->wcifd_get_temporary_data( $hash, true );
	$product_id = isset( $data['product_id'] ) ? $data['product_id'] : '';
	$image_name = isset( $data['image_name'] ) ? $data['image_name'] : '';

	if ( $product_id && $image_name ) {

		/* Start - Retrieve the image through the name saved in the DB */
		$attachment_id = null;

		$args = array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'name'        => $image_name,
		);

		$images = new WP_Query( $args );

		if ( isset( $images->posts[0]->ID ) ) {

			$attachment_id = $images->posts[0]->ID;

		}

		wp_reset_postdata();
		/* End */

		if ( $product_id && $attachment_id ) {

			/* Link image to the product */
			set_post_thumbnail( $product_id, $attachment_id );

			/* Assign the product as post_parent of the image */
			$updated = wp_update_post(
				array(
					'ID'          => $attachment_id,
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

