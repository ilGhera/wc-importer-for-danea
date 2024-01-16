<?php
/**
 * Funzioni generali
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.4
 */

/*No accesso diretto*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Generazione stringa random
 *
 * @param int $length lunghezza della stringa.
 *
 * @return string
 */
function wcifd_rand_md5( $length ) {

	$max    = ceil( $length / 32 );
	$random = '';
	for ( $i = 0; $i < $max; $i ++ ) {
		$random .= md5( microtime( true ) . mt_rand( 10000, 90000 ) );
	}

	return substr( $random, 0, $length );
}


/**
 * Definisce i nomi dei campi fiscali in uso, in particolare i post_meta da recuperare dal db
 *
 * @param  string $field il campo da definire.
 * @return string        il post_meta
 */
function wcifd_get_italian_tax_fields_names( $field ) {

	$cf_name      = null;
	$pi_name      = null;
	$pec_name     = null;
	$pa_code_name = null;

	/*Campi generati dal plugin*/
	if ( get_option( 'wcexd_company_invoice' ) || get_option( 'wcexd_private_invoice' ) ) {

		$cf_name      = 'billing_wcexd_cf';
		$pi_name      = 'billing_wcexd_piva';
		$pec_name     = 'billing_wcexd_pec';
		$pa_code_name = 'billing_wcexd_pa_code';

	} else {

		/*Plugin supportati*/

		/*WooCommerce Aggiungere CF e P.IVA*/
		if ( class_exists( 'WC_BrazilianCheckoutFields' ) ) {
			$cf_name = 'billing_cpf';
			$pi_name = 'billing_cnpj';

			/*WooCommerce P.IVA e Codice Fiscale per Italia*/
		} elseif ( class_exists( 'WooCommerce_Piva_Cf_Invoice_Ita' ) || class_exists( 'WC_Piva_Cf_Invoice_Ita' ) ) {
			$cf_name      = 'billing_cf';
			$pi_name      = 'billing_piva';
			$pec_name     = 'billing_pec';
			$pa_code_name = 'billing_pa_code';

			/*YITH WooCommerce Checkout Manager*/
		} elseif ( function_exists( 'ywccp_init' ) ) {
			$cf_name = 'billing_Codice_Fiscale';
			$pi_name = 'billing_Partita_IVA';

			/*WOO Codice Fiscale*/
		} elseif ( function_exists( 'woocf_on_checkout' ) ) {
			$cf_name = 'billing_CF';
			$pi_name = 'billing_iva';
		}
	}

	switch ( $field ) {
		case 'cf_name':
			return $cf_name;
		case 'pi_name':
			return $pi_name;
		case 'pec_name':
			return $pec_name;
		case 'pa_code_name':
			return $pa_code_name;
	}

}


/**
 * Verifica la presenza di un codice di tassazione, utilizzato per la ricerca degli utenti
 *
 * @param string $tax_code il codice.
 *
 * @return int l'id dell'utente legato al codice
 */
function check_tax_code( $tax_code ) {

	global $wpdb;
	$query  = "
		SELECT user_id FROM $wpdb->usermeta WHERE meta_value = '$tax_code'
	";
	$result = $wpdb->get_results( $query, ARRAY_A );

	return $result[0]['user_id'];

}


/**
 * Restituisce il codice di statto a due lettere, partendo dal nome completo
 *
 * @param string $state_name lo stato.
 *
 * @return string il codice dello stato
 */
function wcifd_get_state_code( $state_name ) {

	$countries = WC()->countries->countries;

	foreach ( $countries as $key => $value ) {

		if ( $value === $state_name ) {

			return $key;

		} elseif ( $key === $state_name ) {

			return $state_name;

		}
	}

}


/**
 * Restituisce il valore di un dato campo del file xml
 *
 * @param  string $field il campo da recuperare.
 * @return string        il valore
 */
function wcifd_json_decode( $field = '' ) {
	$decoded = json_decode( json_encode( $field ), true );
	$output  = $decoded ? $decoded[0] : '';
	return $output;
}


/**
 * Recupera l'ordine WooCommerce generato dall'importazione precedente di un ordine di Danea Easyfatt
 *
 * @param  int $number l'id dell'ordine di Danea Easyfatt.
 * @return int         l'id dell'ordine WooCommerce
 */
function get_order_by_number( $number ) {
	global $wpdb;
	$query   = "
		SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'wcifd-order-number' AND meta_value = '$number'
	";
	$results = $wpdb->get_results( $query, ARRAY_A );
	return $results[0];
}


/**
 * Restituisce il metodo di pagamento utilizzato negli ordini importati da Danea Easyfatt
 *
 * @param  string $method il metodo proveniente da Danea.
 * @return string         il metodo WC equivalente
 */
function wcifd_payment_gateway( $method ) {
	switch ( $method ) {
		case 'Paypal':
		case 'Carta di credito':
			return array(
				'id'    => 'paypal',
				'title' => 'PayPal',
			);
		case 'Contrassegno':
			return array(
				'id'    => 'cod',
				'title' => 'Cash on Delivery',
			);
		case 'Bonifico bancario':
			return array(
				'id'    => 'bacs',
				'title' => 'Direct Bank Transfer',
			);
		default:
			return null;
	}
}


/**
 * Elimina dalla tabella wp_postmeta gli SKU legati a prodotti inestistenti
 *
 * @param int    $product_id l'ID del prodotto WC.
 * @param string $sku        lo SKU del prodotto WC.
 *
 * @return void
 */
function wcifd_delete_orphan_sku( $product_id, $sku ) {

	global $wpdb;

    $wpdb->delete(
        $wpdb->postmeta,
        array(
            'post_id' => $product_id,
            'meta_key' => '_sku',
            'meta_value' => $sku,
        )
    );

}


/**
 * Verifica la presenza di un prodotto attraverso lo sku
 *
 * @param  string $sku               lo sku del prodotto.
 * @param  int    $parent_product_id l'id del prodotto padre se presente.
 *
 * @return int l'id del prodotto corrispondente se trovato
 */
function wcifd_search_product( $sku, $parent_product_id = null ) {

	global $wpdb;

    $result = $wpdb->get_var(
        $wpdb->prepare(
            "
            SELECT post_id
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_sku'
            AND meta_value = %s
            LIMIT 1
            ",
            $sku
        )
    );

	$post_id = isset( $result ) && wc_get_product( $result ) ? $result : null;

    if ( isset( $result ) ) {

        if ( wc_get_product( $result ) ) {

            $post_id = $result;

        } else {

            /* Delete the orphan sku */
            wcifd_delete_orphan_sku( $result, $sku );

            $post_id = null;
        }

    } else {

        $post_id = null;

    }

	if ( ! $post_id && $parent_product_id && is_numeric( $sku ) ) {

		$product = wc_get_product( $sku );

		if ( $product && ! is_wp_error( $product ) ) {

			$post_id = intval( $parent_product_id ) === $product->get_parent_id() ? $product->get_id() : null;

		}
	}

	return $post_id;
}


/**
 * Aggiunge un termine di tassonimia al prodotto dato
 *
 * @param  int     $product_id l'id del prodotto.
 * @param  string  $category   la categoria proveniente da Danea da aggiungere al prodotto.
 * @param  integer $parent_id  id del termine di tassonomia padre se presente.
 * @param  boolean $append     determina se il termine di tassonomia debba aggiungersi a quelli presenti o sostituirli.
 * @return array              il termine di tassonomia
 */
function wcifd_add_taxonomy_term( $product_id, $category, $parent_id = 0, $append = false ) {

	$append = '1' === get_option( 'wcifd-deleting-categories' ) ? true : $append;

	$term = term_exists( $category, 'product_cat', $parent_id );

	if ( 0 === $term || null === $term ) {
		$term = wp_insert_term( $category, 'product_cat', array( 'parent' => $parent_id ) );
	}

	if ( ! is_wp_error( $term ) ) {
		$output = wp_set_object_terms( $product_id, intval( $term['term_id'] ), 'product_cat', $append );
	}

	return $term;
}


/**
 * Aggiunge una nuova tax class WooCommerce
 *
 * @param  string $tax_name il nome dell'aliquota.
 * @return void
 */
function wcifd_add_tax_rate_class( $tax_name ) {

	global $wpdb;

	$response = $wpdb->insert(
		$wpdb->prefix . 'wc_tax_rate_classes',
		array(
			'name' => $tax_name,
			'slug' => sanitize_title_with_dashes( $tax_name ),
		),
		array(
			'%s',
			'%s',
		)
	);

}


/**
 * Recupera un'imposta in WooCommerce
 *
 * @param  string $name  il nome dell'imposta.
 * @param  string $value il valore.
 * @return string        la classe di imposta
 */
function wcifd_get_tax_rate_class( $name, $value = '' ) {

	/*Se non viene passato un valore, utilizza il nome dell'imposta se numerico*/
	if ( '' === $value ) {
		$value = ( is_numeric( $name ) ) ? $name : '';
	}

	global $wpdb;
	$query = '
		SELECT * FROM ' . $wpdb->prefix . "woocommerce_tax_rates WHERE tax_rate_name = '$name'
	";

	$results = $wpdb->get_results( $query, ARRAY_A );

	if ( $results ) {

		$tax_rate_class = ( $results[0]['tax_rate_class'] ) ? $results[0]['tax_rate_class'] : '';

	} else {

		/*Crea una nuova classe di tassazione solo con il valore numerico*/
		if ( '' !== $value ) {
			$tax_rate_class = 22 !== $name ? $name : '';

			if ( $tax_rate_class ) {
				$tax_classes   = explode( "\n", get_option( 'woocommerce_tax_classes' ) );
				$tax_classes[] = $tax_rate_class;
				update_option( 'woocommerce_tax_classes', implode( "\n", $tax_classes ) );
			}

			/* Nuova classe di tassazione */
			if ( $tax_rate_class ) {
				wcifd_add_tax_rate_class( $tax_rate_class );
			}

			$response = $wpdb->insert(
				$wpdb->prefix . 'woocommerce_tax_rates',
				array(
					'tax_rate_country'  => 'IT',
					'tax_rate'          => number_format( $value, 4 ),
					'tax_rate_name'     => $name,
					'tax_rate_priority' => 1,
					'tax_rate_shipping' => 0,
					'tax_rate_class'    => $tax_rate_class,
				),
				array(
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
					'%s',
				)
			);

		}
	}

	return $tax_rate_class;

}


/**
 * Elimina le variazioni di un dato prodotto
 *
 * @param  int $parent_id l'id del prodotto padre.
 */
function wcifd_delete_variations( $parent_id ) {
	$args = array(
		'post_type'   => 'product_variation',
		'post_parent' => $parent_id,
	);
	$vars = get_children( $args, ARRAY_A );
	if ( $vars ) {
		foreach ( $vars as $var ) {
			wp_delete_post( $var['ID'] );

			/*Aggiornamento meta lookup table*/
			new WCIFD_Product_Meta_Lookup( array( 'product_id' => $var['ID'] ), 'delete' );

		}
	}
}


/**
 * Registrazione tassonomie
 *
 * @param  string $name il nome della tassonomia da creare.
 *
 * @return void
 */
function wcifd_register_taxonomy( $name ) {

	$paname = 'pa_' . $name;

	if ( ! get_taxonomy( $paname ) ) {

		$permalinks    = get_option( 'woocommerce_permalinks' );
		$taxonomy_data = array(
			'hierarchical'          => true,
			'update_count_callback' => '_update_post_term_count',
			'labels'                => array(
				'name'              => $name,
				'singular_name'     => $name,
				/* Translators: the taxonomy name */
				'search_items'      => sprintf( __( 'Search %s', 'woocommerce' ), $name ),
				/* Translators: the taxonomy name */
				'all_items'         => sprintf( __( 'All %s', 'woocommerce' ), $name ),
				/* Translators: the taxonomy name */
				'parent_item'       => sprintf( __( 'Parent %s', 'woocommerce' ), $name ),
				/* Translators: the taxonomy name */
				'parent_item_colon' => sprintf( __( 'Parent %s:', 'woocommerce' ), $name ),
				/* Translators: the taxonomy name */
				'edit_item'         => sprintf( __( 'Edit %s', 'woocommerce' ), $name ),
				/* Translators: the taxonomy name */
				'update_item'       => sprintf( __( 'Update %s', 'woocommerce' ), $name ),
				/* Translators: the taxonomy name */
				'add_new_item'      => sprintf( __( 'Add New %s', 'woocommerce' ), $name ),
				/* Translators: the taxonomy name */
				'new_item_name'     => sprintf( __( 'New %s', 'woocommerce' ), $name ),
			),
			'show_ui'               => false,
			'query_var'             => true,
			'rewrite'               => array(
				'slug'         => empty( $permalinks['attribute_base'] ) ? '' : trailingslashit( $permalinks['attribute_base'] ) . sanitize_title( $name ),
				'with_front'   => false,
				'hierarchical' => true,
			),
			'sort'                  => false,
			'public'                => true,
			'show_in_nav_menus'     => false,
			'capabilities'          => array(
				'manage_terms' => 'manage_product_terms',
				'edit_terms'   => 'edit_product_terms',
				'delete_terms' => 'delete_product_terms',
				'assign_terms' => 'assign_product_terms',
			),
		);

		register_taxonomy( $paname, array( 'product' ), $taxonomy_data );

	}

}


/**
 * Update transient
 */
function wcifd_update_transient_wc_attributes() {
	global $wpdb;
	$query   = '
		SELECT * FROM ' . $wpdb->prefix . 'woocommerce_attribute_taxonomies
	';
	$results = $wpdb->get_results( $query );

	$data = array();
	foreach ( $results as $key => $value ) {
		$data[ $key ] = $value;
	}

	update_option( '_transient_wc_attribute_taxonomies', $data );
}


/**
 * Registrazione degli attributi
 */
function wcifd_register_attributes() {

	$attributes = array(
		'size'             => __( 'Size', 'wc-importer-for-danea' ),
		'color'            => __( 'Color', 'wc-importer-for-danea' ),
		'producer'         => __( 'Producer', 'wc-importer-for-danea' ),
		'supplier'         => __( 'Supplier', 'wc-importer-for-danea' ),
		'sup-product-code' => __( 'Supplier product code', 'wc-importer-for-danea' ),
	);

	$additional_attributes = array();

	if ( isset( $_POST['wcifd-custom-fields-hidden'], $_POST['wcifd-products-fields-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-products-fields-nonce'] ) ), 'wcifd-products-fields' ) ) {

		/*Recupero i campi liberi di Danea abilitati dall'admin*/
		$custom_fields = get_option( 'wcifd-custom-fields' );

		if ( $custom_fields && is_array( $custom_fields ) ) {

			$count_custom_fields = count( $custom_fields );

			for ( $i = 1; $i <= $count_custom_fields; $i++ ) {

				if ( isset( $custom_fields[ $i ]['import'] ) && 'attribute' === $custom_fields[ $i ]['import'] ) {

					$custom_field_name = isset( $_POST[ 'custom-field-name-' . $i ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'custom-field-name-' . $i ] ) ) : null;

					/* Translators: the custom field number */
					$name = $custom_field_name ? $custom_field_name : sprintf( __( 'Custom Field %d', 'wc-importer-for-danea' ), $i );

					$additional_attributes[ 'customfield' . $i ] = $name;

				}
			}
		}
	}

	global $wpdb;

	/* Contiene il nome degli attributi modificati */
	$changes = array();

	/* Unisco gli attributi */
	$all_attributes = array_merge( $attributes, $additional_attributes );

	foreach ( $all_attributes as $key => $value ) {

		/* Registrazione tassonomia */
		wcifd_register_taxonomy( $key );

		$query = '
			SELECT * FROM ' . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$key'
			";

		$results = $wpdb->get_results( $query, ARRAY_A );

		/* Inserimento record se non presente */
		if ( ! $results ) {

			$changes[] = $key;

			$insert = $wpdb->insert(
				$wpdb->prefix . 'woocommerce_attribute_taxonomies',
				array(
					'attribute_name'    => sanitize_title( $key ),
					'attribute_label'   => $value,
					'attribute_type'    => 'select',
					'attribute_orderby' => 'menu_order',
					'attribute_public'  => 0,
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
				)
			);

			/* Aggiornamento record in caso di nome modificato */
		} elseif ( isset( $results[0]['attribute_label'] ) && $results[0]['attribute_label'] !== $value ) {

			$changes[] = $key;

			$update = $wpdb->update(
				$wpdb->prefix . 'woocommerce_attribute_taxonomies',
				array(
					'attribute_label' => $value,
				),
				array(
					'attribute_name' => sanitize_title( $key ),
				),
				array(
					'%s',
				),
				array(
					'%s',
				)
			);

		}
	}

	if ( ! empty( $changes ) ) {
		wcifd_update_transient_wc_attributes();
	}

}
add_action( 'init', 'wcifd_register_attributes' );


/**
 * Restituisce il prezzo di listino dal file xml in base alle impostazioni dell'admin
 *
 * @param  array   $product      il singolo prodotto.
 * @param  int     $number       il listino impostato.
 * @param  boolean $tax_included prezzi ivati o meno.
 * @return stringa               il prezzo
 */
function wcifd_get_list_price( $product, $number, $tax_included = false ) {

	$gross_price = 'GrossPrice' . $number;
	$net_price   = 'NetPrice' . $number;

	if ( $tax_included ) {
		$output = isset( $product[ $gross_price ] ) ? $product[ $gross_price ] : '';
	} else {
		$output = isset( $product[ $net_price ] ) ? $product[ $net_price ] : '';
	}

	return $output;

}


/**
 * Restituisce prezzi e label per ruolo utente definite con WC Role Based Price
 *
 * @return array
 */
function get_wc_rbp() {

	$output         = null;
	$wc_rbp_general = get_option( 'wc_rbp_general' );

	if ( function_exists( 'woocommerce_role_based_price' ) && $wc_rbp_general ) {
		$wc_rbp_allowed_roles = isset( $wc_rbp_general['wc_rbp_allowed_roles'] ) ? $wc_rbp_general['wc_rbp_allowed_roles'] : '';
		$wc_rbp_allowed_price = isset( $wc_rbp_general['wc_rbp_allowed_price'] ) ? $wc_rbp_general['wc_rbp_allowed_price'] : '';

		if ( $wc_rbp_allowed_roles ) {
			$output = array();
			foreach ( $wc_rbp_allowed_roles as $role ) {
				foreach ( $wc_rbp_allowed_price as $price_type ) {
					$field_name = $price_type . '_' . $role;
					$price_list = get_option( 'wcifd_' . $field_name );

					$output[ $role ][ $price_type ] = $price_list;

				}
			}
		}
	}

	return $output;
}


/**
 * Recupera le dimensioni del prodotto, sulla base delle impostazioni
 *
 * @param  array   $product il prodotto.
 * @param  string  $type    misure nette o meno.
 * @param  string  $measure la dimensione da restituire.
 * @param  boolean $csv     csv o oggetto.
 * @return string           il dato
 */
function wcifd_get_product_size( $product, $type, $measure, $csv = false ) {
	$x = null;
	$y = null;
	$z = null;
	if ( 'gross-size' === $type ) {
		if ( $csv ) {
			$x = isset( $product['Dim. imballo X'] ) ? $product['Dim. imballo X'] : '';
			$y = isset( $product['Dim. imballo Y'] ) ? $product['Dim. imballo Y'] : '';
			$z = isset( $product['Dim. imballo Z'] ) ? $product['Dim. imballo Z'] : '';
		} else {
			$x = isset( $product['PackingSizeX'] ) ? $product['PackingSizeX'] : '';
			$y = isset( $product['PackingSizeY'] ) ? $product['PackingSizeY'] : '';
			$z = isset( $product['PackingSizeZ'] ) ? $product['PackingSizeZ'] : '';
		}
	} else {
		if ( $csv ) {
			$x = isset( $product['Dim. netta X'] ) ? $product['Dim. netta X'] : '';
			$y = isset( $product['Dim. netta Y'] ) ? $product['Dim. netta Y'] : '';
			$z = isset( $product['Dim. netta Z'] ) ? $product['Dim. netta Z'] : '';
		} else {
			$x = isset( $product['NetSizeX'] ) ? $product['NetSizeX'] : '';
			$y = isset( $product['NetSizeY'] ) ? $product['NetSizeY'] : '';
			$z = isset( $product['NetSizeZ'] ) ? $product['NetSizeZ'] : '';
		}
	}

	switch ( $measure ) {
		case 'x':
			$output = $x;
			break;

		case 'y':
			$output = $y;
			break;

		case 'z':
			$output = $z;
			break;
	}

	return $output;
}


/**
 * Genera la descrizione breve del prodotto
 *
 * @param  string $description la descrizione completa del prodotto.
 * @return string
 */
function wcifd_get_short_description( $description ) {
	$output = null;

	$description = wp_strip_all_tags( $description );

	if ( strlen( $description ) > 340 ) {
		$output = substr( $description, 0, 340 ) . '...';
	} else {
		$output = $description;
	}

	return $output;
}


/**
 * Check if an image already exists
 *
 * @param  $guid $guid the guid.
 * @return array the post ids
 */
function wcifd_get_guid( $guid ) {
	global $wpdb;
	$query   = "
		SELECT ID FROM $wpdb->posts WHERE guid = '" . $guid . "'
	";
	$results = $wpdb->get_results( $query, ARRAY_A );

	return $results;
}


/**
 * Return the image id from his name
 *
 * @param  string $img_name the image name.
 * @return id the image id
 */
function wcifd_get_id_by_img( $img_name ) {
	global $wpdb;
	$query   = "
		SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'danea_img_name' AND meta_value = '$img_name'
	";
	$results = $wpdb->get_results( $query, ARRAY_A );

	return $results[0]['post_id'];
}


/**
 * Ricezione chiamata http post proveniente da Danea Easyfatt
 */
function wcifd_products_update_request() {

	$premium_key   = strtolower( get_option( 'wcifd-premium-key' ) );
	$url_code      = strtolower( get_option( 'wcifd-url-code' ) );
	$import_images = get_option( 'wcifd-import-images' );
	$key           = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
	$code          = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
	$mode          = isset( $_GET['mode'] ) ? sanitize_text_field( wp_unslash( $_GET['mode'] ) ) : '';

	if ( $key && $code ) {

		if ( $key === $premium_key && $code === $url_code ) {

			$images_send_url        = sprintf( '%1$s?key=%2$s&code=%3$s&mode=images', home_url(), $key, $code );
			$images_send_finish_url = sprintf( '%s-send-finish', $images_send_url );

			/*Importazione prodotti*/
			if ( 'data' === $mode ) {

				if ( isset( $_FILES['file']['tmp_name'] ) && move_uploaded_file( sanitize_text_field( wp_unslash( $_FILES['file']['tmp_name'] ) ), 'wcifd-prodotti.xml' ) ) {

					wcifd_catalog_update( 'wcifd-prodotti.xml' );

					echo "OK\n";

					if ( 1 === intval( $import_images ) ) {

						printf( "ImageSendURL=%s\n", esc_url_raw( $images_send_url ) );
						printf( "ImageSendFinishURL=%s\n", esc_url_raw( $images_send_finish_url ) );

					}
				} else {

					esc_html_e( 'WCIFD ERROR | An error accourred while receiving data from Danea Easyfatt', 'wc-importer-for-danea' );

				}
			} elseif ( 'images' === $mode && 1 === intval( $import_images ) ) {

				/*Aggiornamento immagini*/
				wcifd_products_images();

			} elseif ( 'images-send-finish' === $mode && 1 === intval( $import_images ) ) {

				echo "OK\n";

				/*Gestione delle immaigni orfane a ricezione immagini completata*/
				$next = as_next_scheduled_action(
					'wcifd_orphan_images_event',
					array(),
					'wcifd-orphan-images'
				);

				if ( ! $next ) {

					as_schedule_recurring_action(
						time(),
						60,
						'wcifd_orphan_images_event',
						array(),
						'wcifd-orphan-images'
					);

				}
			}
		} else {

			echo wp_kses_post( __( 'WCIFD ERROR | It seems like the URL entered in Danea Easyfatt is not correct', 'wc-importer-for-danea' ) );

		}

		exit;

	}

}
add_action( 'init', 'wcifd_products_update_request' );


/**
 * Personalizzazione messaggio di controllo aggiornamenti
 *
 * @return string
 */
function wcifd_check_update() {

	return __( 'Check for updates', 'wc-importer-for-danea' );

}
add_filter( 'puc_manual_check_link-wc-importer-for-danea-premium', 'wcifd_check_update' );


/**
 * Personalizzazione messaggi di aggiornamento
 *
 * @param  string $message il testo messaggio.
 * @param  string $status  lo status della risposta.
 * @return string
 */
function wcifd_update_message( $message = '', $status = '' ) {

	if ( 'no_update' === $status ) {

		$message = __( '<strong>Woocommerce Importer for Danea - Premium</strong> is up to date.', 'wc-importer-for-danea' );

	} elseif ( 'update_available' === $status ) {

		$message = __( 'A new version of <strong>Woocommerce Importer for Danea - Premium</strong> is available.', 'wc-importer-for-danea' );

	} else {

		$message = __( 'There was an error trying to update. Please try again later.', 'wc-importer-for-danea' );

	}

	return $message;

}
add_filter( 'puc_manual_check_message-wc-importer-for-danea-premium', 'wcifd_update_message', 10, 2 );

