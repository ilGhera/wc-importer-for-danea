<?php
/**
 * Funzioni
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @version 1.1.0
 */

/*No accesso diretto*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*Requires*/
require( WCIFD_INCLUDES . 'wcifd-import-users.php' );
require( WCIFD_INCLUDES . 'wcifd-import-products.php' );
require( WCIFD_INCLUDES . 'wcifd-catalog-update.php' );
require( WCIFD_INCLUDES . 'wcifd-import-single-product.php' );
require( WCIFD_INCLUDES . 'wcifd-delete-single-product.php' );
require( WCIFD_INCLUDES . 'wcifd-import-orders.php' );


/**
 * Generazione stringa random
 * @param  int $length lunghezza della stringa
 * @return string
 */
function wcifd_rand_md5( $length ) {
	$max = ceil( $length / 32 );
	$random = '';
	for ( $i = 0; $i < $max; $i ++ ) {
		$random .= md5( microtime( true ) . mt_rand( 10000, 90000 ) );
	}
	return substr( $random, 0, $length );
}


/**
 * Definisce i nomi dei campi fiscali in uso, in particolare i post_meta da recuperare dal db
 * @param  string $field il campo da definire
 * @return string        il post_meta
 */
function wcifd_get_italian_tax_fields_names( $field ) {

	/*WooCommerce Aggiungere CF e P.IVA*/
	if ( class_exists( 'WC_BrazilianCheckoutFields' ) ) {
		$cf_name = 'billing_cpf';
		$pi_name = 'billing_cnpj';
	}
	/*WooCommerce P.IVA e Codice Fiscale per Italia*/
	elseif ( class_exists( 'WooCommerce_Piva_Cf_Invoice_Ita' ) ) {
		$cf_name = 'billing_cf';
		$pi_name = 'billing_piva';
	}
	//YITH WooCommerce Checkout Manager
	elseif ( function_exists( 'ywccp_init' ) ) {
		$cf_name = 'billing_Codice_Fiscale';
		$pi_name = 'billing_Partita_IVA';
	}
	//WOO Codice Fiscale
	elseif ( function_exists( 'woocf_on_checkout' ) ) {
		$cf_name = 'billing_CF';
		$pi_name = 'billing_iva';
	}

	if ( $field == 'cf_name' ) {
		return $cf_name;
	} else {
		return $pi_name;
	}

}


/**
 * Verifica la presenza di un codice di tassazione, utilizzato per la ricerca degli ute4nti
 * @param  string $tax_code il codice
 * @return int              l'id dell'utente legato al codice
 */
function check_tax_code( $tax_code ) {
	global $wpdb;
	$query = "
		SELECT user_id FROM $wpdb->usermeta WHERE meta_value = '$tax_code'
	";
	$result = $wpdb->get_results( $query, ARRAY_A );
	return $result[0]['user_id'];
}


/**
 * Restituisce il codice di statto a due lettere, partendo dal nome completo
 * @param  string $state_name lo stato
 * @return string             il codice dello stato
 */
function wcifd_get_state_code( $state_name ) {
	$countries = WC()->countries->countries;
	foreach ( $countries as $key => $value ) {
		if ( $value == $state_name ) {
			return $key;
		} elseif ( $key == $state_name ) {
			return $state_name;
		}
	}
}


/**
 * Restituisce il valore di un dato campo del file xml
 * @param  string $field il campo da recuperare
 * @return string        il valore
 */
function wcifd_json_decode( $field = '' ) {
	$decoded = json_decode( json_encode( $field ), true );
	$output = $decoded ? $decoded[0] : '';
	return $output;
}


/**
 * Recupera l'ordine WooCommerce generato dall'importazione precedente di un ordine di Danea Easyfatt
 * @param  int $number l'id dell'ordine di Danea Easyfatt
 * @return int         l'id dell'ordine WooCommerce
 */
function get_order_by_number( $number ) {
	global $wpdb;
	$query = "
		SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'wcifd-order-number' AND meta_value = '$number'
	";
	$results = $wpdb->get_results( $query, ARRAY_A );
	return $results[0];
}


/**
 * Restituisce il metodo di pagamento utilizzato negli ordini importati da Danea Easyfatt
 * @param  string $method il metodo proveniente da Danea
 * @return string         il metodo WC equivalente
 */
function wcifd_payment_gateway( $method ) {
	switch ( $method ) {
		case 'Paypal':
		case 'Carta di credito':
			return array(
				'id' => 'paypal',
				'title' => 'PayPal',
			);
			break;
		case 'Contrassegno':
			return array(
				'id' => 'cod',
				'title' => 'Cash on Delivery',
			);
			break;
		case 'Bonifico bancario':
			return array(
				'id' => 'bacs',
				'title' => 'Direct Bank Transfer',
			);
			break;
		default:
			return null;
			break;
	}
}


/**
 * Verifica la presenza di un prodotto attraverso lo sku
 * @param  string $sku lo sku del prodotto
 * @return int         l'id del prodotto corrispondente se trovato
 */
function wcifd_search_product( $sku ) {
	global $wpdb;
	$query = "
		SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_sku' AND meta_value = '$sku'
	";

	$query2 = "
		SELECT * FROM $wpdb->posts WHERE post_type IN('product, product_variation') AND
	";
	$results = $wpdb->get_results( $query, ARRAY_A );
	$post_id = $results ? $results[0]['post_id'] : '';

	if ( ! $post_id ) {
		$product = wc_get_product( $sku );
		$post_id = ( $product ) ? $post_id = $sku : null;
	}

	return $post_id;
}


/**
 * Aggiunge un termine di tassonimia al prodotto dato
 * @param  int  $product_id l'id del prodotto
 * @param  string  $category   la categoria proveniente da Danea da aggiungere al prodotto
 * @param  integer $parent_id  id del termine di tassonomia padre se presente
 * @param  boolean $append     determina se il termine di tassonomia debba aggiungersi a quelli presenti o sostituirli
 * @return array              il termine di tassonomia
 */
function wcifd_add_taxonomy_term( $product_id, $category, $parent_id = 0, $append = false ) {

	$append = '1' === get_option('wcifd-deleting-categories') ? true : $append;

	$term = term_exists( $category, 'product_cat', $parent_id );

	if ( $term === 0 || $term === null ) {		
		$term = wp_insert_term( $category, 'product_cat', array( 'parent' => $parent_id ) );
	}

	if ( ! is_wp_error( $term ) ) {
		$output = wp_set_object_terms( $product_id, intval( $term['term_id'] ), 'product_cat', $append );
	}

	return $term;
}


/**
 * Recupera un'imposta in WooCommerce
 * @param  string $name  il nome dell'imposta
 * @param  string $value il valore
 * @return string        la classe di imposta
 */
function wcifd_get_tax_rate_class( $name, $value = '' ) {

	/*Se non viene passato un valore, utilizza il nome dell'imposta se numerico*/
	if ( $value == '' ) {
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

		/*Crea una nuova classe di tasszione solo con il valore numerico*/
		if ( $value != '' ) {
			$tax_rate_class = $name != 22 ? $name : '';

			if ( $tax_rate_class ) {
				$tax_classes = explode( "\n", get_option( 'woocommerce_tax_classes' ) );
				$tax_classes[] = $tax_rate_class;
				update_option( 'woocommerce_tax_classes', implode( "\n", $tax_classes ) );
			}

			$wpdb->insert(
				$wpdb->prefix . 'woocommerce_tax_rates',
				array(
					'tax_rate_country' => 'IT',
					'tax_rate'       => number_format( $value, 4 ),
					'tax_rate_name'  => $name,
					'tax_rate_priority' => 1,
					'tax_rate_shipping' => 0,
					'tax_rate_class' => $tax_rate_class,
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
 * @param  int $parent_id l'id del prodotto padre
 */
function wcifd_delete_variations( $parent_id ) {
	$args = array(
		'post_type' => 'product_variation',
		'post_parent' => $parent_id,
	);
	$vars = get_children( $args, ARRAY_A );
	if( $vars ) {
		foreach ( $vars as $var ) {
			wp_delete_post( $var['ID'] );
		}		
	}
}


/**
 * Registrazione tassonomie
 * @param  string $name il nome della tassonomia da creare
 */
function wcifd_register_taxonomy( $name ) {
	$paname = 'pa_' . $name;
	if ( ! get_taxonomy( $paname ) ) {
		$permalinks = get_option( 'woocommerce_permalinks' );
		$taxonomy_data = array(
			'hierarchical'          => true,
			'update_count_callback' => '_update_post_term_count',
			'labels'                => array(
				'name'              => $name,
				'singular_name'     => $name,
				'search_items'      => sprintf( __( 'Search %s', 'woocommerce' ), $name ),
				'all_items'         => sprintf( __( 'All %s', 'woocommerce' ), $name ),
				'parent_item'       => sprintf( __( 'Parent %s', 'woocommerce' ), $name ),
				'parent_item_colon' => sprintf( __( 'Parent %s:', 'woocommerce' ), $name ),
				'edit_item'         => sprintf( __( 'Edit %s', 'woocommerce' ), $name ),
				'update_item'       => sprintf( __( 'Update %s', 'woocommerce' ), $name ),
				'add_new_item'      => sprintf( __( 'Add New %s', 'woocommerce' ), $name ),
				'new_item_name'     => sprintf( __( 'New %s', 'woocommerce' ), $name ),
			),
			'show_ui'           => false,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'         => empty( $permalinks['attribute_base'] ) ? '' : trailingslashit( $permalinks['attribute_base'] ) . sanitize_title( $name ),
				'with_front'   => false,
				'hierarchical' => true,
			),
			'sort'              => false,
			'public'            => true,
			'show_in_nav_menus' => false,
			'capabilities'      => array(
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
	$query = '
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
 * Registrazione degli attributi "Size" end "Color"
 */
function wcifd_register_attributes() {
	$attributes = array( 'Size', 'Color' );
	global $wpdb;

	foreach ( $attributes as $attr ) {

		wcifd_register_taxonomy( $attr );
		add_action( 'woocommerce_after_register_taxonomy', 'wcifd_register_taxonomy' );

		$query = '
			SELECT * FROM ' . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attr'
			";

		$results = $wpdb->get_results( $query, ARRAY_A );

		$changes = false;
		if ( $results == null ) {
				$changes = true;
				$wpdb->insert(
					$wpdb->prefix . 'woocommerce_attribute_taxonomies',
					array(
						'attribute_name'    => sanitize_title( $attr ),
						'attribute_label'   => $attr,
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
		}
	}

	if ( $changes ) {
		wcifd_update_transient_wc_attributes();
	}

}
add_action( 'init', 'wcifd_register_attributes' );


/**
 * Restituisce il prezzo di listino dal file xml in base alle impostazioni dell'admin
 * @param  object  $product      il singolo prodotto
 * @param  int     $number       il listino impostato
 * @param  boolean $tax_included prezzi ivati o meno
 * @return stringa               il prezzo
 */
function wcifd_get_list_price( $product, $number, $tax_included = false ) {

	$gross_price = 'GrossPrice' . $number;
	$net_price   = 'NetPrice' . $number;

	if ( $tax_included ) {
		$output = isset( $product->$gross_price ) ? $product->$gross_price : '';
	} else {
		$output = isset( $product->$net_price ) ? $product->$net_price : '';
	}

	return $output;

}


/**
 * Recupera le dimensioni del prodotto, sulla base delle impostazioni
 * @param  mixed   $product il prodotto
 * @param  string  $type    misure nette o meno
 * @param  string  $measure la dimensione da restituire
 * @param  boolean $csv     csv o oggetto
 * @return string           il dato
 */
function wcifd_get_product_size( $product, $type, $measure, $csv = false ) {
	$x = null;
	$y = null;
	$z = null;
	if ( $type == 'gross-size' ) {
		if ( $csv ) {
			$x = isset( $product['Dim. imballo X'] ) ? $product['Dim. imballo X'] : '';
			$y = isset( $product['Dim. imballo Y'] ) ? $product['Dim. imballo Y'] : '';
			$z = isset( $product['Dim. imballo Z'] ) ? $product['Dim. imballo Z'] : '';
		} else {
			$x = isset( $product->PackingSizeX ) ? $product->PackingSizeX : '';
			$y = isset( $product->PackingSizeY ) ? $product->PackingSizeY : '';
			$z = isset( $product->PackingSizeZ ) ? $product->PackingSizeZ : '';
		}
	} else {
		if ( $csv ) {
			$x = isset( $product['Dim. netta X'] ) ? $product['Dim. netta X'] : '';
			$y = isset( $product['Dim. netta Y'] ) ? $product['Dim. netta Y'] : '';
			$z = isset( $product['Dim. netta Z'] ) ? $product['Dim. netta Z'] : '';
		} else {
			$x = isset( $product->NetSizeX ) ? $product->NetSizeX : '';
			$y = isset( $product->NetSizeY ) ? $product->NetSizeY : '';
			$z = isset( $product->NetSizeZ ) ? $product->NetSizeZ : '';
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
 * @param  string $description la descrizione completa del prodotto
 * @return string
 */
function wcifd_get_short_description( $description ) {
	$output = null;
	if ( strlen( $description ) > 340 ) {
		$output = substr( $description, 0, 340 ) . '...';
	} else {
		$output = $description;
	}

	return $output;
}


//CHECK IF AN IMAGE ALREADY EXISTS
function wcifd_get_guid( $guid ) {
	global $wpdb;
	$query = "
		SELECT ID FROM $wpdb->posts WHERE guid = '" . $guid . "'
	";
	$results = $wpdb->get_results( $query, ARRAY_A );
	return $results;
}


function wcifd_get_id_by_img( $img_name ) {
	global $wpdb;
	$query = "
		SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'danea_img_name' AND meta_value = '$img_name'
	";
	$results = $wpdb->get_results( $query, ARRAY_A );
	return $results[0]['post_id'];
}


/**
 * Ricezione chiamata http post proveniente da Danea Easyfatt
 */
function wcifd_products_update_request() {

	/*Change execution time limit*/
	ini_set( 'max_execution_time', 0 );

	$premium_key = strtolower( get_option( 'wcifd-premium-key' ) );
	$url_code = strtolower( get_option( 'wcifd-url-code' ) );
	$import_images = get_option( 'wcifd-import-images' );
	$key  = isset( $_GET['key'] ) ? $_GET['key'] : '';
	$code = isset( $_GET['code'] ) ? $_GET['code'] : '';
	$mode = isset( $_GET['mode'] ) ? $_GET['mode'] : '';

	if ( $key && $code ) {

		if ( $key == $premium_key && $code == $url_code ) {

			$imagesURL = home_url() . '?key=' . $key . '&code=' . $code . '&mode=images';

			/*Importazione prodotti*/
			if ( $mode == 'data' ) {

				if ( move_uploaded_file( $_FILES['file']['tmp_name'], 'wcifd-prodotti.xml' ) ) {
					wcifd_catalog_update( 'wcifd-prodotti.xml' );
					echo "OK\n";
					if ( $import_images == 1 ) {
						echo "ImageSendURL=$imagesURL";
					}
				} else {
					echo 'Error';
				}
			} elseif ( $mode == 'images' && $import_images == 1 ) {

				/*Aggiornamento immagini*/
				wcifd_products_images();

			}
		} else {
			echo 'Error';
		}

		exit;
	}

}
add_action( 'init', 'wcifd_products_update_request' );


/**
 * Personalizzazione messaggio di controllo aggiornamenti
 * @return string
 */
function wcifd_check_update() {
	return __( 'Check for updates', 'wcifd' );
}
add_filter( 'puc_manual_check_link-wc-importer-for-danea-premium', 'wcifd_check_update' );


/**
 * Personalizzazione messaggi di aggiornamento
 * @param  string $message il testo messaggio
 * @param  string $status  lo status della risposta
 * @return string
 */
function wcifd_update_message( $message = '', $status = '' ) {

	if ( $status == 'no_update' ) {
		$message = __( '<strong>Woocommerce Importer for Danea - Premium</strong> is up to date.', 'wcifd' );
	} else if ( $status == 'update_available' ) {
		$message = __( 'A new version of <strong>Woocommerce Importer for Danea - Premium</strong> is available.', 'wcifd' );
	} else {
		$message = __( 'There was an error trying to update. Please try again later.', 'wcifd' );
	}

	return $message;

}
add_filter( 'puc_manual_check_message-wc-importer-for-danea-premium', 'wcifd_update_message', 10, 2 );
