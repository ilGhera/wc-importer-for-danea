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
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->temp = new WCIFD_Temporary_Data();

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
		$data = $this->temp->wcifd_get_temporary_data( $hash, true );
		$product_id = isset( $data['product_id'] ) ? $data['product_id'] : '';
		$image_name = isset( $data['image_name'] ) ? $data['image_name'] : '';

		if ( $product_id && $image_name ) {

			/* Get the image ID */
			$attachment_id = $this->get_image_id( $image_name );

			if ( $attachment_id ) {

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

					$this->temp->wcifd_delete_temporary_data( $hash, true );
				}
			}
		}
	}

    public function get_image_id( $image_name ) {

        $image_id = $this->get_image_id_by_original_name( $image_name );

        if ( ! $image_id ) {

            $image_id = $this->get_image_id_by_name( $image_name );
        }

        return $image_id;
    }

     /**
     * Retrieves an attachment ID by its stored original filename.
     *
     * @param string $image_name The image name.
     *
     * @return int|false The attachment ID if found, false otherwise.
     */
    public function get_attachment_id_by_original_filename( $image_name ) {

        $args = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'meta_query'     => array(
                array(
                    'key'     => '_wcifd_original_filename',
                    'value'   => $image_name,
                    'compare' => '=',
                ),
            ),
            'fields'         => 'ids',
            'posts_per_page' => 1, // Ci aspettiamo solo uno per nome originale, dato il delete_duplicates
            'no_found_rows'  => true,
        );

        $image_ids = get_posts( $args );

        return ( ! empty( $image_ids ) ) ? $image_ids[0] : false;
    }

	/**
	 * Get image by name
	 *
     * @param string $image_name The image name.
     *
	 * @return int
	 */
	public function get_image_id_by_name( $image_name ) {

        $sanitized_slug = sanitize_title( pathinfo( $image_name, PATHINFO_FILENAME ) ); // Assicurati che lo slug sia generato allo stesso modo di WP

		$attachment_id = null;

		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'name'           => $sanitized_slug,
			'fields'         => 'ids',
            'posts_per_page' => 1,
            'no_found_rows'  => true,
		);

        $image_ids_old_slug = get_posts( $args_old_slug );

		if ( ! empty( $image_ids_old_slug ) && isset( $image_ids_old_slug[0] ) ) {
			// Opzionale: Se trovi un vecchio allegato, potresti volerlo "migrare"
			// aggiungendo il _wcifd_original_filename meta, così la prossima volta sarà trovato subito.
			// Tuttavia, fai attenzione a non rallentare l'importazione.
			update_post_meta( $image_ids_old_slug[0], '_wcifd_original_filename', $image_name );

			return $image_ids_old_slug[0];
        }
    }
}

new WCIFD_Single_Product_Image();

