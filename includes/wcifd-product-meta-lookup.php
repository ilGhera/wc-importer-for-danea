<?php
/**
 * Aggiorna la tabella wp_wc_product_meta_lookup con i dati del prodotto
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @version 1.1.7 
 */
class wcifdProductMetaLookup {

	/**
	 * @param  array  $args   i dati del prodotto
	 * @param  string $mode   add, update o delete
	 */
	public function __construct( $args, $mode = 'add' ) {

		// $this->data = $data;
		// $this->mode = $mode;

		$this->data = $this->setup_data( $args );

		switch ( $mode ) {
			case 'add':
				$this->add( $this->data );
				break;
			case 'update':
				$this->update( $this->data );
				break;
			case 'delete':
				$this->delete( $this->data );
				break;
			
		}

	}


	/**
	 * Preparo i dati del prodotto
	 * @param  array $data i dati del prodotto
	 */
	private function setup_data( $data ) {

		$this->product_id 	= isset( $data['product_id'] ) ? $data['product_id'] : '';
		$this->sku 			= isset( $data['sku'] ) ? $data['sku'] : '';
		$this->virtual 		= 0;
		$this->downloadable 	= 0;
		$this->min_price 		= isset( $data['min_price'] ) ? $data['min_price'] : '';
		$this->max_price 		= isset( $data['max_price'] ) ? $data['max_price'] : '';
		$this->onsale 		= isset( $data['onsale'] ) ? $data['onsale'] : '';
		$this->stock_quantity = isset( $data['stock_quantity'] ) ? $data['stock_quantity'] : '';
		$this->stock_status 	= isset( $data['stock_status'] ) ? $data['stock_status'] : '';
		$this->rating_count 	= 0;
		$this->average_rating = 0.00;
		$this->total_sales 	= 0;

	}


	/**
	 * Inserisce record nella tabella
	 */
	private function add() {

		global $wpdb;

		$output = $wpdb->insert(

			$wpdb->prefix . 'wc_product_meta_lookup',
			array(
				'product_id' 	 => $this->product_id,
				'sku' 			 => $this->sku,
				'virtual' 		 => $this->virtual,
				'downloadable' 	 => $this->downloadable,
				'min_price' 	 => $this->min_price,
				'max_price' 	 => $this->max_price,
				'onsale' 		 => $this->onsale,
				'stock_quantity' => $this->stock_quantity,
				'stock_status'   => $this->stock_status,
				'rating_count' 	 => $this->rating_count,
				'average_rating' => $this->average_rating,
				'total_sales'	 => $this->total_sales,
			),
			array(
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
			)

		);

	}


	/**
	 * Aggiorna record nella tabella
	 */
	private function update() {

		global $wpdb;

		$output = $wpdb->update(

			$wpdb->prefix . 'wc_product_meta_lookup',
			array(
				'product_id' 	 => $this->product_id,
				'sku' 			 => $this->sku,
				'min_price' 	 => $this->min_price,
				'max_price' 	 => $this->max_price,
				'onsale' 		 => $this->onsale,
				'stock_quantity' => $this->stock_quantity,
				'stock_status'   => $this->stock_status,
			),
			array(
				'product_id' => $this->product_id,
			),
			array(
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
			)

		);

	}


	/**
	 * Elimina record dalla tabella
	 */
	private function delete() {

		global $wpdb;

		$output = $wpdb->delete(

			$wpdb->prefix . 'wc_product_meta_lookup',
			array(
				'product_id' => $this->product_id,
			),
			array(
				'%d',
			)
		);

	}

}
