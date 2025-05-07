<?php
/**
 * Matching image to single product
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Matching image to single product
 *
 * @param string $hash the WooCommerce product code that identifies the match to be made.
 *
 * @return void
 */
class WCIFD_Single_Product_Image {

	/**
	 * Instance of WCIFD_Temporary_Data
	 *
	 * @var WCIFD_Temporary_Data
	 */
	public $temp;

	/**
	 * Product ID
	 *
	 * @var int
	 */
	public $product_id;

	/**
	 * Image name
	 *
	 * @var string
	 */
	public $image_name;

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		add_action( 'wcifd_product_image_event', array( $this, 'set_product_image' ), 10, 1 );
	}

	/**
	 * Set product image
	 *
	 * @param string $hash the hash code that identifies the match to be made.
	 *
	 * @return void
	 */
	public function set_product_image( $hash ) {

		/* Vars */
		$this->temp       = new WCIFD_Temporary_Data();
		$data             = $this->temp->wcifd_get_temporary_data( $hash, true );
		$this->product_id = isset( $data['product_id'] ) ? $data['product_id'] : '';
		$this->image_name = isset( $data['image_name'] ) ? $data['image_name'] : '';

		if ( $this->product_id && $this->image_name ) {

			/* Get the image ID */
			$attachment_id = $this->get_image_id_by_name( $this->image_name );

			if ( $attachment_id ) {

				/* Link image to the product */
				set_post_thumbnail( $this->product_id, $attachment_id );

				/* Assign the product as post_parent of the image */
				$updated = wp_update_post(
					array(
						'ID'          => $attachment_id,
						'post_parent' => $this->product_id,
					)
				);

				if ( 0 !== $updated && ! is_wp_error( $updated ) ) {

					$this->temp->wcifd_delete_temporary_data( $hash, true );
				}
			}
		}
	}

	/**
	 * Get image by name
	 *
	 * @return int
	 */
	public function get_image_id_by_name() {

		$attachment_id = null;

		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'name'           => $this->image_name,
			'fields'         => 'ids',
			'posts_per_page' => -1,
		);

		$image_ids = get_posts( $args );

		if ( is_array( $image_ids ) && isset( $image_ids[0] ) ) {

			return $image_ids[0];
		}
	}
}

new WCIFD_Single_Product_Image();

