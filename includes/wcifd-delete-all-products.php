<?php
/**
 * Eliminazione di tutti i prodotti WooCommerce in caso di ricezione dell'intero catalogo da Danea Easyfatt
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.0
 */

/**
 * Eliminazione prodotti
 *
 * @return void
 */
function wcifd_delete_all_products() {

	global $wpdb;

	$queries = array(
		"DELETE relations.*, taxes.*, terms.*
        FROM $wpdb->term_relationships AS relations
        INNER JOIN $wpdb->term_taxonomy AS taxes
        ON relations.term_taxonomy_id=taxes.term_taxonomy_id
        INNER JOIN $wpdb->terms AS terms
        ON taxes.term_id=terms.term_id
        WHERE object_id IN (SELECT ID FROM $wpdb->posts WHERE post_type='product')",
		"DELETE FROM $wpdb->postmeta WHERE post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type = 'product')",
		"DELETE FROM $wpdb->posts WHERE post_type = 'product'",
	);

	foreach ( $queries as $key => $query ) {

		$result = $wpdb->query( $query );

	}

}

