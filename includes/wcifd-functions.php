<?php
/*
WOOCOMMERCE IMPORTER FOR DANEA - PREMIUM | FUNCTIONS
*/


//NO DIRECT ACCESS
if ( !defined( 'ABSPATH' ) ) exit;


//GENERATE RANDOM STRING
function wcifd_rand_md5($length) {
	$max = ceil($length / 32);
	$random = '';
	for ($i = 0; $i < $max; $i ++) {
	$random .= md5(microtime(true).mt_rand(10000,90000));
	}
	return substr($random, 0, $length);
}


//GET THE ITALIANS TAX FIELDS NAMES
function wcifd_get_italian_tax_fields_names($field) {

	//WooCommerce Aggiungere CF e P.IVA
	if(class_exists('WC_BrazilianCheckoutFields')) {
		$cf_name = 'billing_cpf';
		$pi_name = 'billing_cnpj';
	} 
	//WooCommerce P.IVA e Codice Fiscale per Italia
	elseif(class_exists('WooCommerce_Piva_Cf_Invoice_Ita')) {
		$cf_name = 'billing_cf';
		$pi_name = 'billing_piva';	
	} 
	//YITH WooCommerce Checkout Manager
	elseif(function_exists('ywccp_init')) {
		$cf_name = 'billing_Codice_Fiscale';
		$pi_name = 'billing_Partita_IVA';
	} 
	//WOO Codice Fiscale
	elseif(function_exists('woocf_on_checkout')) {
		$cf_name = 'billing_CF';
		$pi_name = 'billing_iva';	
	}
	
	if($field == 'cf_name') {
		return $cf_name;
	} else {
		return $pi_name;
	}

} 


//CHECK IF A PERSONAL TAX CODE EXISTS
function check_tax_code($tax_code) {
	global $wpdb;
	$query = "
		SELECT user_id FROM $wpdb->usermeta WHERE meta_value = '$tax_code'
	";
	$result = $wpdb->get_results($query, ARRAY_A);
	return $result[0]['user_id'];
}


//GET STATE CODE FROM COUNTRY NAME
function wcifd_get_state_code($state_name) {
	$countries = WC()->countries->countries;
	foreach ($countries as $key => $value) {
		if($value == $state_name) {
			return $key;
		} elseif($key == $state_name) {
			return $state_name;
		}
	}
}


//GET SINGLE VALUE FROM XML PARSED OBJECT
function wcifd_json_decode($field = '') {
	$decoded = json_decode(json_encode($field), true);
	$output = $decoded ? $decoded[0] : ''; 
	return $output;
}


//GET ORDER BY DANEA ORDER NUMBER
function get_order_by_number($number) {
	global $wpdb;
	$query = "
		SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'wcifd-order-number' AND meta_value = '$number'
	";
	$results = $wpdb->get_results($query, ARRAY_A);
	return $results[0];
}


//RETRIEVE THE PAYMENT GATEWAY FOR SINGLE ORDER
function wcifd_payment_gateway($method) {
	switch ($method) {
		case 'Paypal':
		case 'Carta di credito':
			return array('id' => 'paypal', 'title' => 'PayPal');
			break;
		case 'Contrassegno':
			return array('id' => 'cod', 'title' => 'Cash on Delivery');
			break;
		case 'Bonifico bancario':
			return array('id' => 'bacs', 'title' => 'Direct Bank Transfer');
			break;		
		default:
			return null;
			break;
	}
}


//IMPORT DANEA CONTACTS AS WORDPRESS USERS 
function wcifd_users($type) {

	if(isset($_POST[$type . '-import']) && wp_verify_nonce( $_POST['wcifd-' . $type . '-nonce'], 'wcifd-' . $type . '-import' )) {

		if(isset($_POST['wcifd-users-' . $type])) {
			$role = sanitize_text_field($_POST['wcifd-users-' . $type]);
			update_option('wcifd-' . $type . '-role', $role);
		}
	
		$file = isset($_FILES[$type . '-list']['tmp_name']) ? $_FILES[$type . '-list']['tmp_name'] : '';

		if($file) {
			$rows = array_map('str_getcsv', file($file));
			$header = array_shift($rows);
			$users = array();
			foreach ($rows as $row) {
				// var_dump($row);
			    $users[] = array_combine($header, $row);
			}
			
			$i = 0;
			$n = 0;
			foreach ($users as $user) {

				if($user['Referente']) {
					$user_name = strtolower(str_replace(' ', '-', $user['Referente']));	
					$name = explode(' ', $user['Referente']);			
				} else {
					$user_name = strtolower(str_replace(' ', '-', $user['Denominazione']));
					$name = explode(' ', $user['Denominazione']);
				}
				
				$address = $user['Indirizzo'];
				$cap = $user['Cap'];
				$city = $user['Città'];
				$state = $user['Prov.'];
				$country = wcifd_get_state_code($user['Nazione']);
				$tel = $user['Tel.'];
				$email = $user['e-mail'];
				$fiscal_code = $user['Codice fiscale'];
				$p_iva = $user['Partita Iva'];
				$description = $user['Note'];

				$userdata = array(
					'role' => $role,
					'user_login'   => $user_name,
					'user_pass'	   => null,
					'first_name'   => $name[0],
					'last_name'    => $name[1],
					'display_name' => $user['Denominazione'],
					'user_email'   => $email,
					'description'  => $description
				);

				/*Get the italian tax fields names*/
				$cf_name = wcifd_get_italian_tax_fields_names('cf_name');
				$pi_name = wcifd_get_italian_tax_fields_names('pi_name');

				/*Check if teh user exists*/
				$user_id = username_exists( $user_name );

				/*Add the new user*/
				if ( !$user_id and email_exists($email) == false ) {
					
					$i++;
					// $random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
					
					$user_id = wp_insert_user($userdata);

				} else {
					
					/*Update the user*/
					$n++;
					$userdata['ID'] = $user_id;

					/*Check if the user role must be changed*/
					$user_info = get_userdata($user_id);
					$user_roles = $user_info->roles;

					if(!in_array($role, $user_roles)) {
						unset($userdata['role']);
						$the_user = new WP_User($user_id);
						$the_user->set_role($role);
					}

					wp_update_user($userdata);

				}

				//USER META
				if($user['Referente']) {
					update_user_meta($user_id, 'billing_company', $user['Denominazione']);
				}
				update_user_meta($user_id, 'billing_first_name', $name[0]);
				update_user_meta($user_id, 'billing_last_name', $name[1]);					
				update_user_meta($user_id, 'billing_address_1', $address);
				update_user_meta($user_id, 'billing_city', $city);
				update_user_meta($user_id, 'billing_postcode', $cap);
				update_user_meta($user_id, 'billing_state', $state);
				update_user_meta($user_id, 'billing_country', $country);
				update_user_meta($user_id, 'billing_phone', $tel);
				update_user_meta($user_id, 'billing_email', $email);

				if($cf_name) {
					update_user_meta($user_id, $cf_name, $fiscal_code);
				}
				if($pi_name) {
					update_user_meta($user_id, $pi_name, $p_iva);					
				}

			}

			$output  = '<div id="message" class="updated"><p>';
			$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';
			$output .= sprintf( __( 'Imported %d of %d contacts<br>', 'wcifd' ), $i, count($users) );
			$output .= sprintf( __( 'Updated %d of %d contacts', 'wcifd' ), $n, count($users) );
		    $output .= '</p></div>';
		    echo $output;
		}
		
	}

}


//SEARCH PRODUCT BY SKU
function wcifd_search_product($sku) {
	global $wpdb;
	$query = "
		SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_sku' AND meta_value = '$sku'
	";

	$query2 = "
		SELECT * FROM $wpdb->posts WHERE post_type IN('product, product_variation') AND
	"; 
	$results = $wpdb->get_results($query, ARRAY_A);
	$post_id = $results ? $results[0]['post_id'] : '';

	if(!$post_id) {
		$product = wc_get_product($sku);
		$post_id = ($product) ? $post_id = $sku : null;
	}
	return $post_id;
}


//GET TAX RATE CLASS OR CREATE IT
function wcifd_get_tax_rate_class($name, $value='') {

	// If no value is passed, vat name is used in case it's an int
	if($value == '') {
		$value = (is_numeric($name)) ? $name : '';
	}

	global $wpdb;
	$query = "
		SELECT * FROM " . $wpdb->prefix . "woocommerce_tax_rates WHERE tax_rate_name = '$name'
	";

	$results = $wpdb->get_results($query, ARRAY_A);

	if($results) {

		$tax_rate_class = ($results[0]['tax_rate_class']) ? $results[0]['tax_rate_class'] : '';
	
	} else {
		// Create the new class only with a value
		if($value != '') {
			$tax_rate_class = $name != 22 ? $name : '';

			if($tax_rate_class) {
				$tax_classes = explode("\n", get_option('woocommerce_tax_classes'));
				$tax_classes[] = $tax_rate_class;
				update_option('woocommerce_tax_classes', implode("\n", $tax_classes));
			}

	 		$wpdb->insert(
	 			$wpdb->prefix . 'woocommerce_tax_rates',
	 			array(
	 				'tax_rate_country' => 'IT',
					'tax_rate'       => number_format($value, 4),
					'tax_rate_name'  => $name,
					'tax_rate_priority' => 1,
					'tax_rate_shipping' => 0,
					'tax_rate_class' => $tax_rate_class
					),
	 			array(
	 				'%s',
	 				'%s',
	 				'%s',
	 				'%d',
	 				'%d',
	 				'%s'
	 			)
			);			
		}
	}

 	return $tax_rate_class;

}


//IMPORT DANEA PRODUCTS IN WOOCOMMERCE
function wcifd_products() {

	if(isset($_POST['products-import']) && wp_verify_nonce( $_POST['wcifd-products-nonce'], 'wcifd-products-import' )) {
		
		//CHANGE EXECUTIUON TIME LIMIT
		ini_set('max_execution_time', 0);	

		$tax_included = get_option('wcifd-tax-included');
		$use_suppliers = get_option( 'wcifd-use-suppliers'); 

		$update_products = sanitize_text_field($_POST['update-products']);
		update_option('wcifd-update-products', $update_products);

		$get_regular_price_list = get_option('wcifd-regular-price-list');
		$get_sale_price_list = get_option('wcifd-sale-price-list');
		$size_type = get_option('wcifd-size-type');
		$weight_type = get_option('wcifd-weight-type');


		$file = $_FILES['products-list']['tmp_name'];
		$rows = array_map('str_getcsv', file($file));
		$header = array_shift($rows);
		$products = array();
		foreach ($rows as $row) {
			// var_dump($row);
		    $products[] = array_combine($header, $row);
		}

		$i = 0;
		$u = 0;
		foreach ($products as $product) {
			$sku = $product['Cod.'];
			$title = $product['Descrizione'];
			$description = $product['Descriz. web (Sorgente HTML)'];
			$product_type = $product['Tipologia'];
			$category = $product['Categoria'];
			$sub_category = $product['Sottocategoria'];
			$tax  = $product['Cod. Iva'];
			if($tax_included == 0) {
				$regular_price = str_replace(',', '.', str_replace(array(' ', '€'), '', $product['Listino ' . $get_regular_price_list]));				
				$sale_price   = str_replace(',', '.', str_replace(array(' ', '€'), '', $product['Listino ' . $get_sale_price_list]));				

			} else {
				$regular_price = str_replace(',', '.', str_replace(array(' ', '€'), '', $product['Listino ' . $get_regular_price_list . ' (ivato)']));
				$sale_price    = str_replace(',', '.', str_replace(array(' ', '€'), '', $product['Listino ' . $get_sale_price_list . ' (ivato)']));												
			}
			$supplier_id = $product['Cod. fornitore'];
			$supplier = $product['Fornitore'];


			// PARENT SKU AND VARIABLE PRODUCT
			// Useful for importing products from Danea in a new Wordpress/ Woocommerce installation.
			// Variable products whith all the attributes will be created.	
			
			$parent_sku 	   = null;
			$var_attributes    = null;
			$variable_product  = null; 
			$parent_product_id = null;

			if($product['Note']) {
				$notes = json_decode($product['Note'], true);
				// PARENT SKU
				$parent_sku = ($notes['parent_sku']) ? $notes['parent_sku'] : null;
				$parent_product_id = wcifd_search_product($parent_sku);
				$var_attributes = $notes['var_attributes'];

				//VARIABLE PRODUCT
				if($notes['product_type'] == 'variable' && $notes['attributes']) {
					$variable_product = true;
					$imported_attributes = $notes['attributes'];
				}

			}


			//POST STATUS - VARIATION MUST BE PUBLISHED
			$status = ($var_attributes) ? 'publish' : 'draft';


			//SEARCH FOR THE PRODUCT
			$id   = wcifd_search_product($sku);
			$type = (wp_get_post_parent_id($id) || $parent_sku) ? 'product_variation' : 'product';


			//MANAGE STOCK
			$stock = $product['Q.tà giacenza'];
			$total_sales = $product['Tot. q.tà scaricata'];
			$manage_stock = ($product_type == 'Art. con magazzino' || $product_type == 'Art. con magazzino (taglie/colori)') ? 'yes' : 'no';


			//PRODUCT MEASURES
			$length = wcifd_get_product_size($product, $size_type, 'z', true);
			$width  = wcifd_get_product_size($product, $size_type, 'x', true);
			$height = wcifd_get_product_size($product, $size_type, 'y', true);


			//WEIGHT
			if($weight_type == 'gross-weight') {
				$weight = $product['Peso lordo'];
			} else {
				$weight = $product['Peso netto'];
			}


			//AUTHOR
			$author = ($use_suppliers == 1 && $supplier_id) ? $supplier_id : get_option('wcifd-current-user');

			
			//DANEA VARIANTS SIZE & COLOR
			if($product_type == 'Art. con magazzino (taglie/colori)') {
				update_post_meta($id, 'wcifd-danea-size-color', 1);
			}


			//TAX RATE CHECK
			$tax_status = 'none';
			$tax_class = '';
			if($tax) {
				$tax_status = 'taxable';
				$tax_class = wcifd_get_tax_rate_class($tax);
			}


			//START CREATE OR UPDATE PRODUCT

			if(!$id) {

				$args = array(
					'post_author'      => $author,
					'post_title'       => wp_strip_all_tags($title),
					'post_type'        => $type,
					'post_parent'	   => $parent_product_id,
					'post_content'	   => $description,
					'post_status'	   => $status,
					'meta_input'       => array(
											'_sku'           => $sku,
											'_tax_status'    => $tax_status,
											'_tax_class'     => $tax_class,
											'_stock'         => $stock,
											'_manage_stock'  => $manage_stock,
											'_visibility'	 => 'visible',
											'_regular_price' => $regular_price,
											'_price'         => $regular_price,
											'_sell_price'	 => $regular_price,
											'_width'	     => $width,
											'_height'    	 => $height,
											'_length'		 => $length,
											'_weight'		 => $weight
				    )
				);

				if($sale_price) {
					$args['meta_input']['_sale_price'] = $sale_price;
					$args['meta_input']['_sell_price'] = $sale_price;
					$args['meta_input']['_price'] = $sale_price;
				} else {
					$args['meta_input']['_sale_price'] = '';				
				}

				/*Short description*/
				if(get_option('wcifd-short-description')) {
					$args['post_excerpt'] = wcifd_get_short_description($description);
				}

				//ADD A NEW PRODUCT
				$product_id = wp_insert_post($args);

				if($variable_product) {

					$i++;

					//UPDATE THE PARENT PRODUCT
					wp_set_object_terms($product_id, 'variable', 'product_type' );

					if($imported_attributes) {
						foreach ($imported_attributes as $key => $value) {

							$attr = array(
								$key => array(
									'name' 		   => $key, 
									'value'		   => '', 
									'is_visible'   => 1, 
									'is_variation' => 1, 
									'is_taxonomy'  => 1
								), 
							);

							if(get_post_meta($product_id, '_product_attributes', true)) {
								$metas = get_post_meta($product_id, '_product_attributes', true);
							} else {
								$metas = array();
							}

							$metas[$key] = $attr[$key];
							update_post_meta( $product_id, '_product_attributes', $metas);

							wp_set_object_terms( $product_id, $value, $key);
						}
					}

				} elseif($parent_sku && $var_attributes) {
					
					foreach ($var_attributes as $key => $value) {
						$attr = array(
							$key => array(
								'name' 		   => $value, 
								'value'		   => '',
								'is_visible'   => 1, 
								'is_variation' => 1, 
								'is_taxonomy'  => 1
								)
						);

						update_post_meta($product_id, 'attribute_' . $key, $value);

					    if(get_post_meta($product_id, '_product_attributes', true)) {
							$metas = get_post_meta($product_id, '_product_attributes', true);
						} else {
							$metas = array();
						}

						$metas[$key] = $attr[$key];
						update_post_meta( $product_id, '_product_attributes', $metas);
					}
					
				} else {
					$i++;
				}
				
			} elseif($update_products == 1 && wcifd_search_product($sku)) {

				// DO NOT UPDATE POSTS IN THE TRASH
				if(get_post_status($id) != 'trash') {
					$u++;
					$args = array(
						'ID'			   => $id,
						'post_status'	   => get_post_status($id),
						'post_author'      => $author,
						'post_title'       => wp_strip_all_tags($title),
						'post_name'        => sanitize_title_with_dashes(wp_strip_all_tags($title)),
						'post_type'        => $type,
						'meta_input'       => array(
												'_sku'           => $sku,
												'_tax_status'    => $tax_status,
												'_tax_class'     => $tax_class,
												'_stock'         => $stock,
												'_manage_stock'  => $manage_stock,
												'total_sales'    => $total_sales,
												'_visibility'	 => 'visible',
												'_regular_price' => $regular_price,
												'_price'         => $regular_price,
												'_sell_price'	 => $regular_price,
												'_width'	     => $width,
												'_height'    	 => $height,
												'_length'		 => $length,
												'_weight'		 => $weight
						)
					);

					if($sale_price) {
						$args['meta_input']['_sale_price'] = $sale_price;
						$args['meta_input']['_sell_price'] = $sale_price;
						$args['meta_input']['_price'] = $sale_price;
					} else {
						$args['meta_input']['_sale_price'] = '';				
					}

					/*Product description*/
					if(!get_option('wcifd-exclude-description')) {
						$args['post_content'] = $description;

						/*Short description*/
						if(get_option('wcifd-short-description')) {
							$args['post_excerpt'] = wcifd_get_short_description($description);
						}

					}

					//UPDATE PRODUCT
					$product_id = wp_update_post($args);
				}
			}

			//ADD PRODUCT CAT AND SUB-CAT
			if($category) {

				/*Category*/
				$cat_term = term_exists($category, 'product_cat');

				if($cat_term === 0 || $cat_term === null) {
					$cat_term = wp_insert_term($category, 'product_cat');
				}
				wp_set_object_terms($product_id, intval($cat_term['term_id']), 'product_cat', true);

				if($sub_category){
					
					/*Subcategory*/
					$subcat_term = term_exists($sub_category, 'product_cat', $cat_term['term_id']);

					if($subcat_term === 0 || $subcat_term === null) {
						$subcat_term = wp_insert_term($sub_category, 'product_cat', array('parent' => $cat_term['term_id']));
					}
					wp_set_object_terms($product_id, intval($subcat_term['term_id']), 'product_cat', true);			
				}			
			}
			
		}
		$output  = '<div id="message" class="updated"><p>';
		$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';
		$output .= sprintf( __( 'Products imported: %d<br>Products updated: %d', 'wcifd' ), $i, $u );
	    $output .= '</p></div>';
	    echo $output;

	}

}

function wcifd_delete_variations($parent_id) {
	$args = array('post_type' => 'product_variation', 'post_parent' => $parent_id);
	$vars = get_children($args, ARRAY_A);
	foreach ($vars as $var) {
		wp_delete_post($var['ID']);
	}

}


//REGISTER TAXONOMY FOR SIZE
function wcifd_register_taxonomy($name) {
    $paname = 'pa_' . $name;
    if(!get_taxonomy($paname)) {
	    $permalinks = get_option('woocommerce_permalinks');
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
	                                'new_item_name'     => sprintf( __( 'New %s', 'woocommerce' ), $name )
	                            ),
	                        'show_ui'           => false,
	                        'query_var'         => true,
	                        'rewrite'           => array(
	                            'slug'         => empty( $permalinks['attribute_base'] ) ? '' : trailingslashit( $permalinks['attribute_base'] ) . sanitize_title( $name ),
	                            'with_front'   => false,
	                            'hierarchical' => true
	                        ),
	                        'sort'              => false,
	                        'public'            => true,
	                        'show_in_nav_menus' => false,
	                        'capabilities'      => array(
	                            'manage_terms' => 'manage_product_terms',
	                            'edit_terms'   => 'edit_product_terms',
	                            'delete_terms' => 'delete_product_terms',
	                            'assign_terms' => 'assign_product_terms',
	                        )
	                    );
	    register_taxonomy($paname, array('product'), $taxonomy_data);    	
    } 
}


//UPDATE TRANSIENT
function wcifd_update_transient_wc_attributes() {
	global $wpdb;
	$query = "
		SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies
	";
	$results = $wpdb->get_results($query);

	$data = array();
	foreach ($results as $key => $value) {
		$data[$key] = $value;
	}

	update_option('_transient_wc_attribute_taxonomies', $data);
}


//ADD ATTRIBUTES
function wcifd_register_attributes() {
	$attributes = array('Size', 'Color');
	global $wpdb;

	foreach ($attributes as $attr) {

		wcifd_register_taxonomy($attr);
	 	add_action( 'woocommerce_after_register_taxonomy', 'wcifd_register_taxonomy' );	

		$query = "
			SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attr'
			" ;
		
		$results = $wpdb->get_results($query, ARRAY_A);

		$changes = false;
		if($results == null) {
				$changes = true;
				$wpdb->insert(
		 			$wpdb->prefix . 'woocommerce_attribute_taxonomies',
		 			array(
	 					'attribute_name'    => sanitize_title($attr),
	 					'attribute_label'   => $attr,
	 					'attribute_type'    => 'select',
	 					'attribute_orderby' => 'menu_order',
	 					'attribute_public'  => 0
	 				),
		 			array(
		 				'%s',
		 				'%s',
		 				'%s',
		 				'%s',
		 				'%d'
		 			)
	 			);
		}
	}

	if($changes) {
		wcifd_update_transient_wc_attributes();
	}

}
add_action('init', 'wcifd_register_attributes');


//GET THE LIST PRICE FROM THE DANEA PRODUCT UPDATE XML, BASED ON THE LIST NUMBER AND THE TAXES INCLUDED OR NOT
function wcifd_get_list_price($product, $number, $tax_included=false) {

	$gross_price = 'GrossPrice' . $number;
	$net_price	 = 'NetPrice' . $number;
	
	$output = $tax_included ? $product->$gross_price : $product->$net_price;

	return $output;

}


//GET THE MEASURES OF THE PRODUCT, BASED ON THE OPTION SET.
function wcifd_get_product_size($product, $type, $measure, $csv=false) {
	$x = null;
	$y = null;
	$z = null;
	if($type == 'gross-size') {
		$x = $csv == true ? $product['Dim. imballo X'] : $product->PackingSizeX;
		$y = $csv == true ? $product['Dim. imballo Y'] : $product->PackingSizeY;
		$z = $csv == true ? $product['Dim. imballo Z'] : $product->PackingSizeZ;
	} else {
		$x = $csv == true ? $product['Dim. netta X'] : $product->NetSizeX;
		$y = $csv == true ? $product['Dim. netta Y'] : $product->NetSizeY;
		$z = $csv == true ? $product['Dim. netta Z'] : $product->NetSizeZ;
	}
	
	switch ($measure) {
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


//SHORT PRODUCT DESCRIPTION
function wcifd_get_short_description($description) {
	$output = null;
	if(strlen($description) > 340) {
		$output = substr($description, 0, 340) . '...';
	} else {
		$output = $description;
	}

	return $output;
}


//DANEA CATALOG UPDATE
function wcifd_catalog_update($file) {

	//OPTIONS
	$get_regular_price_list = get_option('wcifd-regular-price-list');
	$get_sale_price_list = get_option('wcifd-sale-price-list');
	$size_type = get_option('wcifd-size-type');
	$weight_type = get_option('wcifd-weight-type');

	$results = simplexml_load_file($file);

	//ARE THEY ALL PRODUCTS OR JUST THE UPDATES?
	($results->Products) ? $products = $results->Products : $products = $results->UpdatedProducts;

	foreach ($products->children() as $product) {
		$sku = $product->Code;
		$title = $product->Description;
		$description = ($product->DescriptionHtml != '') ? $product->DescriptionHtml : $title;
		$category = $product->Category;
		$sub_category = $product->Subcategory;
		$tax  = $product->Vat;
		$stock = $product->AvailableQty;
		$size_um = $product->SizeUm;
		$weight_um = $product->WeightUm;
	
		// PARENT SKU AND VARIABLE PRODUCT
		// Useful for importing products from Danea in a new Wordpress/ Woocommerce installation.
		// Variable products whith all the attributes will be created.	

		$parent_sku 	   = null;
		$var_attributes    = null;
		$variable_product  = null; 
		$parent_product_id = null;

		if($product->Notes) {
			$notes = json_decode($product->Notes, true);
			if(is_array($notes)) {
				// PARENT SKU
				$parent_sku = array_key_exists('parent_sku', $notes) ? $notes['parent_sku'] : null;
				if($parent_sku) {
					$parent_product_id = wcifd_search_product($parent_sku);					
				}
				$var_attributes = array_key_exists('var_attributes', $notes) ? $notes['var_attributes'] : null;

				//VARIABLE PRODUCT
				if($notes['product_type'] == 'variable' && $notes['attributes']) {
					$variable_product = true;
					$imported_attributes = $notes['attributes'];
				}
			}
		}


		//POST STATUS - VARIATION MUST BE PUBLISHED
		$new_products_status = get_option('wcifd-publish-new-products') ? 'publish' : 'draft'; 
		$status = ($var_attributes) ? 'publish' : $new_products_status;


		//SEARCH FOR THE PRODUCT
		$id   = wcifd_search_product(wcifd_json_decode($sku));
		$type = (wp_get_post_parent_id($id) || $parent_sku) ? 'product_variation' : 'product';


		//MANAGE STOCK
		$manage_stock = ($product->ManageWarehouse == 'true') ? 'yes' : 'no';
		$stock_status = ($stock >= 1) ? 'instock' : 'outofstock';


		//PRODUCT MEASURES
		$length = wcifd_get_product_size($product, $size_type, 'z');
		$width  = wcifd_get_product_size($product, $size_type, 'x');
		$height = wcifd_get_product_size($product, $size_type, 'y');


		//WEIGHT
		if($weight_type == 'gross-weight') {
			$weight = $product->GrossWeight;
		} else {
			$weight = $product->NetWeight;
		}


		//AUTHOR
		$author = (get_option('wcifd-use-suppliers') == 1 && $product->SupplierCode) ? $product->SupplierCode : get_option('wcifd-current-user');


		//TAX
		$tax_included = get_option('wcifd-tax-included');


		//PRICES
		$regular_price = wcifd_get_list_price($product, $get_regular_price_list, $tax_included);
		$sale_price    = wcifd_get_list_price($product, $get_sale_price_list, $tax_included);


		//DANEA VARIANTS SIZE & COLOR
		$variants = $product->Variants;
		if($variants) {
			update_post_meta($id, 'wcifd-danea-size-color', 1);
		}


		//TAX RATE CHECK
		$tax_status = 'none';
		$tax_class = '';
		$perc = wcifd_json_decode($tax['Perc']);
		$class = wcifd_json_decode($tax['Class']);
		if($perc != 0 || $class != 'Escluso') {
			$tax_status = 'taxable';
			$tax_class = wcifd_get_tax_rate_class( wcifd_json_decode($tax), strval($perc) );
		}

		//START CREATE OR UPDATE PRODUCT
		if(!$id) {

			$args = array(
				'post_author'      => $author,
				'post_title'       => wp_strip_all_tags($title),
				'post_type'        => $type,
				'post_parent'	   => $parent_product_id,
				'post_content'	   => $description,
				'post_status'	   => $status,
				'meta_input'       => array(
										'_sku'                => wcifd_json_decode($sku),
										'_tax_status'         => $tax_status,
										'_tax_class'          => $tax_class,
										'_stock'              => wcifd_json_decode($stock),
										'_manage_stock'       => $manage_stock,
										'_stock_status'		  => $stock_status,
										'_visibility'	      => 'visible',
										'_regular_price'      => wcifd_json_decode($regular_price),
										'_price'           	  => wcifd_json_decode($regular_price),
										'_sell_price'		  => wcifd_json_decode($regular_price),
										'_width'			  => wcifd_json_decode($width),
										'_height'			  => wcifd_json_decode($height),
										'_length'			  => wcifd_json_decode($length),
										'_weight'			  => wcifd_json_decode($weight)

			    )
			);

			if($sale_price) {
				$args['meta_input']['_sale_price'] = wcifd_json_decode($sale_price);
				$args['meta_input']['_sell_price'] = wcifd_json_decode($sale_price);
				$args['meta_input']['_price'] = wcifd_json_decode($sale_price);
			} else {
				$args['meta_input']['_sale_price'] = '';				
			}

			/*Short description*/
			if(get_option('wcifd-short-description')) {
				$args['post_excerpt'] = wcifd_get_short_description($description);
			}



			//ADD A NEW PRODUCT
			$product_id = wp_insert_post($args);

			if($variable_product) {

				//UPDATE THE PARENT PRODUCT
				wp_set_object_terms($product_id, 'variable', 'product_type' );

				if($imported_attributes) {
					foreach ($imported_attributes as $key => $value) {

						$attr = array(
							$key => array(
								'name' 		   => $key, 
								'value'		   => '', 
								'is_visible'   => 1, 
								'is_variation' => 1, 
								'is_taxonomy'  => 1
							), 
						);

						if(get_post_meta($product_id, '_product_attributes', true)) {
							$metas = get_post_meta($product_id, '_product_attributes', true);
						} else {
							$metas = array();
						}

						$metas[$key] = $attr[$key];
						update_post_meta( $product_id, '_product_attributes', $metas);

						wp_set_object_terms( $product_id, $value, $key);
					}
				}

			} elseif($parent_sku && $var_attributes) {
				
				foreach ($var_attributes as $key => $value) {
					$attr = array(
						$key => array(
							'name' 		   => $value, 
							'value'		   => '',
							'is_visible'   => 1, 
							'is_variation' => 1, 
							'is_taxonomy'  => 1
							)
					);
					// $metas = get_post_meta(get_the_ID(), '_product_attributes', true);
					update_post_meta($product_id, 'attribute_' . $key, $value);
				    
				    if(get_post_meta($product_id, '_product_attributes', true)) {
						$metas = get_post_meta($product_id, '_product_attributes', true);
					} else {
						$metas = array();
					}

					$metas[$key] = $attr[$key];
					update_post_meta( $product_id, '_product_attributes', $metas);
				}
				
			}
			
		} else {

			// DO NOT UPDATE POSTS IN THE TRASH
			if(get_post_status($id) != 'trash') {
				$args = array(
					'ID'			   => $id,
					'post_status'	   => get_post_status($id),
					'post_author'      => $author,
					'post_title'       => wp_strip_all_tags($title),
					'post_name'        => sanitize_title_with_dashes(wp_strip_all_tags($title)),
					'post_type'        => $type,
					// 'post_content'	   => $description,
					'meta_input'       => array(
											'_sku'                => wcifd_json_decode($sku),
											'_tax_status'         => $tax_status,
											'_tax_class'          => $tax_class,
											'_stock'              => wcifd_json_decode($stock),
											'_manage_stock'       => $manage_stock,
											'_stock_status'		  => $stock_status,
											'_visibility'	      => 'visible',
											'_regular_price'      => wcifd_json_decode($regular_price),
											'_price'           	  => wcifd_json_decode($regular_price),
											'_sell_price'         => wcifd_json_decode($regular_price),
											'_width'			  => wcifd_json_decode($width),
											'_height'			  => wcifd_json_decode($height),
											'_length'			  => wcifd_json_decode($length),
											'_weight'			  => wcifd_json_decode($weight)
				   )

				);

				if($sale_price) {
					$args['meta_input']['_sale_price'] = wcifd_json_decode($sale_price);
					$args['meta_input']['_sell_price'] = wcifd_json_decode($sale_price);
					$args['meta_input']['_price'] = wcifd_json_decode($sale_price);
				} else {
					$args['meta_input']['_sale_price'] = '';				
				}

				/*Product description*/
				if(!get_option('wcifd-exclude-description')) {
					$args['post_content'] = $description;

					/*Short description*/
					if(get_option('wcifd-short-description')) {
						$args['post_excerpt'] = wcifd_get_short_description($description);
					}

				}

				if($variants) {
					$transient_product_meta_key = '_transient_wc_var_prices_' .  $id;
					update_option( $transient_product_meta_key, strtotime('-12 hours') );
					wp_cache_delete('alloptions', 'options'); 
				}

				//UPDATE PRODUCT
				$product_id = wp_update_post($args);

			}

		}


		//ADD PRODUCT CAT AND SUB-CAT
		if($category) {

			/*Category*/
			$cat_term = term_exists($category, 'product_cat', 0);

			if($cat_term === 0 || $cat_term === null) {
				$cat_term = wp_insert_term($category, 'product_cat');
			}
			wp_set_object_terms($product_id, intval($cat_term['term_id']), 'product_cat', true);

			if($sub_category){
				
				/*Subcategory*/
				$subcat_term = term_exists($sub_category, 'product_cat', $cat_term['term_id']);

				if($subcat_term === 0 || $subcat_term === null) {
					$subcat_term = wp_insert_term($sub_category, 'product_cat', array('parent' => $cat_term['term_id']));
				}
				wp_set_object_terms($product_id, intval($subcat_term['term_id']), 'product_cat', true);			
			}			
		}



		
		//PRODUCTS VARS
		if($variants) {

			//UPDATE THE PARENT PRODUCT
			wp_set_object_terms($product_id, 'variable', 'product_type' );

			$avail_colors = array();
			$avail_sizes = array();

			
			$v = 1; //VARIANT LOOP
			foreach($variants->children() as $variant) {
				
				$barcode  = wcifd_json_decode($variant->Barcode);
				$in_stock = wcifd_json_decode($variant->AvailableQty);

				$man_stock = 'yes';
				$stock_status = ($in_stock) ? 'instock' : 'outofstock';


				//ATTRIBUTES
				$size     = wcifd_json_decode($variant->Size);
				$color    = wcifd_json_decode($variant->Color);


				//ADD VALID ATTRIBUTE TERMS TO THE ARRAY
				if($size != '-' && !in_array($size, $avail_sizes)) {
					$avail_sizes[] = $size;
				}

				if($color != '-' && !in_array($color, $avail_colors)) {
					$avail_colors[] = $color;
				}


				//VARIATION METAS INPUT
				$meta_input = array(
					'_sku'               => $barcode,
					'_stock'             => $in_stock,
					'_stock_status'		 => $stock_status,
					'_manage_stock'      => $man_stock,
					'_regular_price' 	 => wcifd_json_decode($regular_price),
					'_price'         	 => wcifd_json_decode($regular_price),
					'_sell_price'        => wcifd_json_decode($regular_price)
			    );

			    if($sale_price) {
					$meta_input['_sale_price'] = wcifd_json_decode($sale_price);
					$meta_input['_sell_price'] = wcifd_json_decode($sale_price);
					$meta_input['_price'] = wcifd_json_decode($sale_price);
				} else {
					$meta_input['_sale_price'] = '';				
				}


				//ADD ATTRIBUTE TERM TO THE VARIATION METAS
				if($avail_colors) {
					$meta_input['attribute_pa_color'] = sanitize_title($color);
				}
				if($avail_sizes) {
					$meta_input['attribute_pa_size'] = sanitize_title($size);
				}


				//ADD WEIGHT AND MEASURES
				if($weight) {
					$meta_input['_weight'] = wcifd_json_decode($weight);
				}

				if($length) {
					$meta_input['_length'] = wcifd_json_decode($length);
				}

				if($width) {
					$meta_input['_width'] = wcifd_json_decode($width);
				}

				if($height) {
					$meta_input['_height'] = wcifd_json_decode($height);
				}




				if(!wcifd_search_product($barcode)) {
 					
 					//ADD NEW VARIATION
	 				$var_args = array(
						'post_author'      => $author,
						'post_name'        => 'danea-product-' . $product_id . '-variation-' . $v++,
						'post_type'        => 'product_variation',
						'post_parent'	   => $product_id,
						'post_content'	   => $description,
						'post_status'	   => 'publish',
						'meta_input'       => $meta_input

					);
					
					$var_id =  wp_insert_post($var_args);

				} else {

					//UPDATE VARIATION
					if(get_post_status(wcifd_search_product($barcode)) != 'trash') {

						$var_args = array(
							'ID'			   => wcifd_search_product($barcode),
							'post_author'      => $author,
							'post_name'        => 'danea-product-' . $product_id . '-variation-' . $v++,
							'post_type'        => 'product_variation',
							'post_parent'	   => $product_id,
							'post_content'	   => $description,
							'post_status'	   => 'publish',
							'meta_input'       => $meta_input

						);
						
						$var_id =  wp_update_post($var_args);

					}

				}

				//LINK VARIATIONS WITH THE AVAILABLE ATTRIBUTE TERMS
				if($avail_colors) {
					wp_set_object_terms( $var_id, $avail_colors, 'pa_color');
				}
				if($avail_sizes) {
					wp_set_object_terms( $var_id, $avail_sizes, 'pa_size');
				}


				//VARIATION ATTRIBUTES
				$attr = array();

				if($color) {
					$attr['pa_color'] = array(
						'name' 		   => sanitize_title($color), 
						'value'		   => '',
						'is_visible'   => 1, 
						'is_variation' => 1, 
						'is_taxonomy'  => 1
					); 
				}

				if($size) {
					$attr['pa_size'] = array(
						'name' 		   => sanitize_title($size), 
						'value' 	   => '',
						'is_visible'   => 1, 
						'is_variation' => 1, 
						'is_taxonomy'  => 1
					);
				}

				if($attr) {
				    update_post_meta($var_id, '_product_attributes', $attr);					
				}

			}	

			
			//AVAILABLE ATTRIBUTES FOR THIS PRODUCT
			$attributes = get_post_meta($product_id, '_product_attributes', true) ? get_post_meta($product_id, '_product_attributes', true) : array();

			if($avail_colors) {
				wp_set_object_terms( $product_id, $avail_colors, 'pa_color');		

				$attributes['pa_color'] = array(
					'name' 		   => 'pa_color', 
					'value'		   => '', 
					'position'     => 0,
					'is_visible'   => 1, 
					'is_variation' => 1, 
					'is_taxonomy'  => 1
				);
			}

			if($avail_sizes) {
				wp_set_object_terms( $product_id, $avail_sizes, 'pa_size');				

				$attributes['pa_size'] = array(
					'name' 		   => 'pa_size',
					'value' 	   => '',
					'position'	   => 1,
					'is_visible'   => 1, 
					'is_variation' => 1, 
					'is_taxonomy'  => 1
				);
			}

			update_post_meta( $product_id, '_product_attributes', $attributes);
		
		}

	}
}


//CHECK IF AN IMAGE ALREADY EXISTS
function wcifd_get_guid($guid) {
	global $wpdb;
	$query = "
		SELECT ID FROM $wpdb->posts WHERE guid = '" . $guid . "'
	";
	$results = $wpdb->get_results($query, ARRAY_A);
	return $results;
}


function wcifd_get_id_by_img($img_name) {
	global $wpdb;
	$query = "
		SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'danea_img_name' AND meta_value = '$img_name'
	";
	$results = $wpdb->get_results($query, ARRAY_A);
	return $results[0]['post_id'];
}


//RECEIVE PRODUCTS UPDATE AND IMAGES
function wcifd_products_update_request() {

	//CHANGE EXECUTION TIME LIMIT
	ini_set('max_execution_time', 0);

	//CHANGE MEMORY LIMIT
	// ini_set('memory_limit','960M');

	$premium_key = strtolower(get_option('wcifd-premium-key'));
	$url_code = strtolower(get_option('wcifd-url-code'));
	$import_images = get_option('wcifd-import-images');
	$key  = isset($_GET['key']) ? $_GET['key'] : '';
	$code = isset($_GET['code']) ? $_GET['code'] : '';
	$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
		
	if($key && $code) {

		if($key == $premium_key && $code == $url_code)  {

			$imagesURL = home_url() . '?key=' . $key . '&code=' . $code . '&mode=images';
			
			//Update products data
			if($mode == 'data') {

				if(move_uploaded_file($_FILES['file']['tmp_name'], 'wcifd-prodotti.xml')){	
					wcifd_catalog_update('wcifd-prodotti.xml');
					echo "OK\n";
					if($import_images == 1) {
						echo "ImageSendURL=$imagesURL";						
					}

				} else {
					echo 'Error';
				}	

			} elseif($mode == 'images' && $import_images == 1) {

				//Update products images
				wcifd_products_images();
			
			}

		} else {
			echo 'Error';			
		}

		exit;
	}

} 
add_action('init', 'wcifd_products_update_request');


//IMPORT DANEA ORDERS CREATING WOOCOMMERCE ORDERS
function wcifd_orders() {

	if(isset($_POST['orders-import']) && wp_verify_nonce( $_POST['wcifd-orders-nonce'], 'wcifd-orders-import' )) {

		//Get user options and update them into the database
		$wcifd_orders_add_users = sanitize_text_field($_POST['wcifd-orders-add-users']);
	    $wcifd_orders_status = strtolower(str_replace(' ', '-', sanitize_text_field($_POST['wcifd-orders-status'])));
		update_option('wcifd-orders-add-users', $wcifd_orders_add_users);
	    update_option('wcifd-orders-status', $wcifd_orders_status);
	
		$file = $_FILES['orders-list']['tmp_name'];
		$data = simplexml_load_file($file);
		$orders = $data->Documents;
 
 		$o = 0; //Orders
		$u = 0;	//Users
		$p = 0; //Products
		foreach ($orders->Document as $order) {

			//Danea order number, useful for not importing the content a second time
			$order_number = wcifd_json_decode($order->Number);

			if(!get_order_by_number($order_number)) {

				$o++;

				//ORDER DETAILS
				$order_date = wcifd_json_decode($order->Date);
				$order_comment = wcifd_json_decode($order->InternalComment);
				$payment_method = wcifd_json_decode($order->PaymentName);

				//CUSTOMERS DETAILS
				if($order->CustomerReference) {
					$user_name = strtolower(str_replace(' ', '-', $order->CustomerReference));	
					$name = explode(' ', $order->CustomerReference);			
				} else {
					$user_name = strtolower(str_replace(' ', '-', $order->CustomerName));
					$name = explode(' ', $order->CustomerName);
				}

				//P.IVA and Fiscal Code fields name
				$cf_name = wcifd_get_italian_tax_fields_names('cf_name');
				$pi_name = wcifd_get_italian_tax_fields_names('pi_name');

				//Billing details
				$billing_company   = wcifd_json_decode($order->CustomerName);
				$billing_address   = wcifd_json_decode($order->CustomerAddress);
				$billing_city 	   = wcifd_json_decode($order->CustomerCity);
				$billing_postcode  = wcifd_json_decode($order->CustomerPostcode);
				$billing_state     = wcifd_json_decode($order->CustomerProvince);
				$billing_country   = wcifd_get_state_code(wcifd_json_decode($order->CustomerCountry));
				$billing_phone 	   = wcifd_json_decode($order->CustomerTel);
				$billing_email 	   = wcifd_json_decode($order->CustomerEmail);
				$fiscal_code 	   = wcifd_json_decode($order->CustomerFiscalCode);
				$p_iva 		  	   = wcifd_json_decode($order->CustomerVatCode);

				//Shipping details
				$shipping_name 	  = wcifd_json_decode($order->DeliveryName);
				$shipping_address  = wcifd_json_decode($order->DeliveryAddress);
				$shipping_city     = wcifd_json_decode($order->DeliveryCity);
				$shipping_postcode = wcifd_json_decode($order->DeliveryPostcode);
				$shipping_state    = wcifd_json_decode($order->DeliveryProvince);
				$shipping_country  = wcifd_get_state_code(wcifd_json_decode($order->DeliveryCountry));


				//CREATE A NEW USER IF REQUIRED
				if(!email_exists($order->CustomerEmail) && !check_tax_code($order->CustomerVatCode) && !check_tax_code($order->CustomerFiscalCode) && $wcifd_orders_add_users == 1) {

					$u++;
					$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
					$role = (get_option('wcifd-clients-role')) ? get_option('wcifd-clients-role') : 'customer';
					
					
					$userdata = array(
						'role' => $role,
						'user_login'   => $user_name,
						'first_name'   => $name[0],
						'last_name'    => $name[1],
						'display_name' => $order->CustomerName,
						'user_email'   => $order->CustomerEmail
					);

					$user_id = wp_insert_user($userdata);
		

					//USER META
					if($order->CustomerReference) {
						add_user_meta($user_id, 'billing_company', $billing_company);
					}
					//Billing details
					add_user_meta($user_id, 'billing_first_name', $name[0]);
					add_user_meta($user_id, 'billing_last_name', $name[1]);					
					add_user_meta($user_id, 'billing_address_1', $billing_address);
					add_user_meta($user_id, 'billing_city', $billing_city);
					add_user_meta($user_id, 'billing_postcode', $billing_postcode);
					add_user_meta($user_id, 'billing_state', $billing_state);
					add_user_meta($user_id, 'billing_country', $billing_country);
					add_user_meta($user_id, 'billing_phone', $billing_phone);
					add_user_meta($user_id, 'billing_email', $billing_email);

					if($cf_name) {
						add_user_meta($user_id, $cf_name, $fiscal_code);					
					}
					if($pi_name) {
						add_user_meta($user_id, $pi_name, $p_iva);	
					}

					//Shipping details
					add_user_meta($user_id, 'shipping_first_name', $shipping_name);
					add_user_meta($user_id, 'shipping_address_1', $shipping_address);
					add_user_meta($user_id, 'shipping_city', $shipping_city);
					add_user_meta($user_id, 'shipping_postcode', $shipping_postcode);
					add_user_meta($user_id, 'shipping_state', $shipping_state);
					add_user_meta($user_id, 'shipping_country', $shipping_country);

				} else {
					$user = get_user_by('email', $billing_email);
					$user_id = $user->ID;
				}

				//ORDER DETAILS

				//Order billing informations
				$billing_address = array(
			        'first_name'    => $name[0],
			        'last_name'     => $name[1],
			        'company'       => $billing_company,
			        'email'         => $billing_email,
			        'phone'         => $billing_phone,
			        'address_1'     => $billing_address,
			        'city'          => $billing_city,
			        'state'         => $billing_state,
			        'postcode'      => $billing_postcode,
			        'country'       => $billing_country
			    );

				//Order shipping informations
			    $shipping_address = array(
			        'first_name'    => $shipping_name,
			        'address_1'     => $shipping_address,
			        'city'          => $shipping_city,
			        'state'         => $shipping_state,
			        'postcode'      => $shipping_postcode,
			        'country'       => $shipping_country
			    );
				
				$args = array(
					'status' 	    => $wcifd_orders_status,
					'customer_id'   => $user_id,
					'customer_note' => $order_comment
				);

				//CREATE NEW WC ORDER
				$wc_order = wc_create_order($args);

				//Add the order_number to the wc_order
				add_post_meta($wc_order->id, 'wcifd-order-number', $order_number);
				wp_update_post( array( 'ID' => $wc_order->id, 'post_date' => $order_date));

				$wc_order->set_address( $billing_address, 'billing' );
				$wc_order->set_address( $shipping_address, 'shipping' );

				//Set the payment gateway
				$payment_gateway = wcifd_payment_gateway($payment_method);
				update_post_meta( $wc_order->id, '_payment_method', $payment_gateway['id'] );
			    update_post_meta( $wc_order->id, '_payment_method_title', $payment_gateway['title'] );


				//PRODUCTS DETAILS
				foreach($order->Rows->Row as $item) {

					$sku = wcifd_json_decode($item->Code);
					$title = wcifd_json_decode($item->Description);
					$tax  = wcifd_json_decode($item->VatCode);
					$price = wcifd_json_decode($item->Price);
					// $supplier_id = $item->xxxxx
					// $supplier = $item->SalesAgent
					$total_sales = wcifd_json_decode($item->Qty);


					//Check if the single product exists
					if(wcifd_search_product($item->Code)) {

						$product_id = wcifd_search_product($sku);
						$wc_order->add_product(get_product($product_id), $total_sales);

					} else {

						//Create the new Woocommerce product
						$p++;

						//TAX RATE CHECK
						$tax_status = 'none';
						$tax_class = '';
						$perc = wcifd_json_decode($tax['Perc']);
						$class = wcifd_json_decode($tax['Class']);
						if($perc != 0 || $class != 'Escluso') {
							$tax_status = 'taxable';
							$tax_class = wcifd_get_tax_rate_class( wcifd_json_decode($tax), strval($perc) );
						}

					
						$args = array(
							'post_author'      => get_current_user_id(), //Al momento non viene recuperato l'ID del fornitore
							'post_title'       => $title,
							'post_type'        => 'product',
							'post_status' 	   => 'publish',
							// 'tax_input'		   => array(
							// 						'product_cat'    => $category
							// 					  ),
							'meta_input'       => array(
													'_sku'           => $sku,
													'_tax_status'    => $tax_status,
													'_tax_class'     => $tax_class,
													'_regular_price' => $price,
													'_price'         => $price,
												  )

						);
						$product_id = wp_insert_post($args);
						wp_set_object_terms($product_id, 'Imported', 'product_cat', true);
						$wc_order->add_product(get_product($product_id), $total_sales);
						
					}				
				}


				$wc_order->calculate_totals();

			}

		}

		$output  = '<div id="message" class="updated"><p>';
		$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';
		$output .= sprintf( __( 'Imported %d orders, %d users and %d products.', 'wcifd' ), $o, $u, $p);
	    $output .= '</p></div>';
	    echo $output;

	}

}


//CHECK FOR UPDATE MESSAGE
function wcifd_check_update() {
	return __('Check for updates', 'wcifd');	
}
add_filter('puc_manual_check_link-wc-importer-for-danea-premium', 'wcifd_check_update');


//UPDATE RESULT MESSAGES
function wcifd_update_message($message = '', $status = '') {
	
	if ( $status == 'no_update' ) {
		$message = __('<strong>Woocommerce Importer for Danea - Premium</strong> is up to date.', 'wcifd'); 
	} else if ( $status == 'update_available' ) {
		$message = __('A new version of <strong>Woocommerce Importer for Danea - Premium</strong> is available.', 'wcifd'); 
	} else {
		$message = __('There was an error trying to update. Please try again later.', 'wcifd');	
	}
	
	return $message;

}
add_filter('puc_manual_check_message-wc-importer-for-danea-premium', 'wcifd_update_message', 10, 2);
