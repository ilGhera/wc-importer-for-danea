<?php
/**
 * Eliminazione di tutti i prodotti WooCommerce in caso di ricezione dell'intero catalogo da Danea Easyfatt
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @since 1.5.0
 */

function wcifd_delete_all_products() {

    global $wpdb;

    $queries = array(
        "DELETE relations.*, taxes.*, terms.*
        FROM wp_term_relationships AS relations
        INNER JOIN wp_term_taxonomy AS taxes
        ON relations.term_taxonomy_id=taxes.term_taxonomy_id
        INNER JOIN wp_terms AS terms
        ON taxes.term_id=terms.term_id
        WHERE object_id IN (SELECT ID FROM wp_posts WHERE post_type='product')",
        "DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type = 'product')",
        "DELETE FROM wp_posts WHERE post_type = 'product'",
    );

    foreach ( $queries as $key => $query ) {

        $result = $wpdb->query( $query );

        error_log( 'QUERY ' . $key . ': ' . $result );

    }

}

