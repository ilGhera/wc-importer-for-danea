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
	$output = json_decode(json_encode($field), true);
	return $output[0];
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

	if($_POST[$type . '-import'] && wp_verify_nonce( $_POST['wcifd-' . $type . '-nonce'], 'wcifd-' . $type . '-import' )) {

		if(isset($_POST['wcifd-users-' . $type])) {
			$role = sanitize_text_field($_POST['wcifd-users-' . $type]);
			update_option('wcifd-' . $type . '-role', $role);
		}
	
		$file = $_FILES[$type . '-list']['tmp_name'];
		$rows = array_map('str_getcsv', file($file));
		$header = array_shift($rows);
		$users = array();
		foreach ($rows as $row) {
			// var_dump($row);
		    $users[] = array_combine($header, $row);
		}
		
		$i = 0;
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

			$user_id = username_exists( $user_name );
			if ( !$user_id and email_exists($email) == false ) {
				$i++;
				$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
				$userdata = array(
					'role' => $role,
					'user_login'   => $user_name,
					'first_name'   => $name[0],
					'last_name'    => $name[1],
					'display_name' => $user['Denominazione'],
					'user_email'   => $email,
					'description'  => $description
				);

				$user_id = wp_insert_user($userdata);

				$cf_name = wcifd_get_italian_tax_fields_names('cf_name');
				$pi_name = wcifd_get_italian_tax_fields_names('pi_name');

				//USER META
				if($user['Referente']) {
					add_user_meta($user_id, 'billing_company', $user['Denominazione']);
				}
				add_user_meta($user_id, 'billing_first_name', $name[0]);
				add_user_meta($user_id, 'billing_last_name', $name[1]);					
				add_user_meta($user_id, 'billing_address_1', $address);
				add_user_meta($user_id, 'billing_city', $city);
				add_user_meta($user_id, 'billing_postcode', $cap);
				add_user_meta($user_id, 'billing_state', $state);
				add_user_meta($user_id, 'billing_country', $country);
				add_user_meta($user_id, 'billing_phone', $tel);
				add_user_meta($user_id, 'billing_email', $email);

				if($cf_name) {
					add_user_meta($user_id, $cf_name, $fiscal_code);
				}
				if($pi_name) {
					add_user_meta($user_id, $pi_name, $p_iva);					
				}
			} 
		}

		$output  = '<div id="message" class="updated"><p>';
		$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';
		$output .= sprintf( __( 'Imported %d of %d contacts', 'wcifd' ), $i, count($users) );
	    $output .= '</p></div>';
	    echo $output;
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
	$post_id = $results[0]['post_id'];

	if(!$post_id) {
		$product = wc_get_product($sku);
		$post_id = ($product) ? $post_id = $sku : null;
	}
	return $post_id;
}


//GET TAX RATE CLASS OR CREATE IT
function wcifd_get_tax_rate_class($value) {
	global $wpdb;
	$query = "
		SELECT * FROM " . $wpdb->prefix . "woocommerce_tax_rates
	";
	$results = $wpdb->get_results($query, ARRAY_A);
	foreach ($results as $rate) {
	 	if(round($value) == round($rate['tax_rate'])) {
	 		$tax_rate_class = ($rate['tax_rate_class']) ? $rate['tax_rate_class'] : '';
	 	} else {
	 		$tax_rate_class = ($value < 22) ? 'reduced-rate' : '';
	 		$wpdb->insert(
	 			$wpdb->prefix . 'woocommerce_tax_rates',
	 			array(
 					'tax_rate'       => number_format($value, 4),
 					'tax_rate_name'  => 'IVA',
 					'tax_rate_priority' => 1,
 					'tax_rate_shipping' => 0,
 					'tax_rate_class' => $tax_rate_class
 				),
	 			array(
	 				'%s',
	 				'%s',
	 				'%d',
	 				'%d',
	 				'%s'
	 			)
 			);
	 	}
	 	return $tax_rate_class;
	} 
}


//IMPORT DANEA PRODUCTS IN WOOCOMMERCE
function wcifd_products() {

	if($_POST['products-import'] && wp_verify_nonce( $_POST['wcifd-products-nonce'], 'wcifd-products-import' )) {
	

		$tax_included = get_option('wcifd-tax-included');
		$use_suppliers = get_option( 'wcifd-use-suppliers'); 

		$update_products = sanitize_text_field($_POST['update-products']);
		update_option('wcifd-update-products', $update_products);


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
			$title = sanitize_title($product['Descrizione']);
			$description = $product['Descriz. web (Sorgente HTML)'];
			$product_type = $product['Tipologia'];
			$category = strtolower($product['Categoria']);
			$sub_category = strtolower($product['Sottocategoria']);
			$tax  = $product['Cod. Iva'];
			if($tax_included == 0) {
				$price = str_replace(',', '.', str_replace(array(' ', '€'), '', $product['Listino 1']));				
			} else {
				$price = str_replace(',', '.', str_replace(array(' ', '€'), '', $product['Listino 1 (ivato)']));								
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


			//AUTHOR
			$author = ($use_suppliers == 1 && $supplier_id) ? $supplier_id : get_option('wcifd-current-user');

			
			//DANEA VARIANTS SIZE & COLOR
			if($product_type == 'Art. con magazzino (taglie/colori)') {
				add_post_meta($id, 'wcifd-danea-size-color', 1);
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
											'_regular_price' => $price,
											'_price'         => $price
										  )
				);

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
						'post_content'	   => $description,
						'meta_input'       => array(
												'_sku'           => $sku,
												'_tax_status'    => $tax_status,
												'_tax_class'     => $tax_class,
												'_regular_price' => $price,
												'_price'         => $price,
												'_stock'         => $stock,
												'_manage_stock'  => $manage_stock,
												'total_sales'    => $total_sales,
												'_visibility'	 => 'visible'
											  )

					);

					//UPDATE PRODUCT
					$product_id = wp_update_post($args);
				}
			}

		//ADD PRODUCT CAT AND SUB-CAT
		wp_set_object_terms($product_id, $category, 'product_cat', true);
		$cat_id = term_exists($category, 'product_cat');
		$subcat_id = term_exists($sub_category, 'product_cat', $cat_id);

		if($sub_category){
			if(!$subcat_id) {
				wp_insert_term($sub_category, 'product_cat', array('parent' => $cat_id));
			}

			wp_set_object_terms($product_id, $sub_category, 'product_cat', true);			
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
	$attributes = array('size', 'color');
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
		 				// 'attribute_id'		=> 2,
	 					'attribute_name'    => $attr,
	 					'attribute_label'    => $attr,
	 					'attribute_type'    => 'select',
	 					'attribute_orderby' => 'menu_order',
	 					'attribute_public'  => 0
	 				),
		 			array(
		 				// '%d',
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


//DANEA CATALOG UPDATE
function wcifd_catalog_update($file) {

	$results = simplexml_load_file($file);

	//ARE THEY ALL PRODUCTS OR JUST THE UPDATES?
	($results->Products) ? $products = $results->Products : $products = $results->UpdatedProducts;

	foreach ($products->children() as $product) {
		$sku = $product->Code;
		$title = $product->Description;
		$description = ($product->DescriptionHtml) ? $product->DescriptionHtml : $title;
		$category = strtolower($product->Category);
		$sub_category = strtolower($product->Subcategory);
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
			if($notes) {
				// PARENT SKU
				$parent_sku = ($notes['parent_sku']) ? $notes['parent_sku'] : null;
				if($parent_sku) {
					$parent_product_id = wcifd_search_product($parent_sku);					
				}
				$var_attributes = $notes['var_attributes'];

				//VARIABLE PRODUCT
				if($notes['product_type'] == 'variable' && $notes['attributes']) {
					$variable_product = true;
					$imported_attributes = $notes['attributes'];
				}
			}
		}


		//POST STATUS - VARIATION MUST BE PUBLISHED
		$status = ($var_attributes) ? 'publish' : 'draft';


		//SEARCH FOR THE PRODUCT
		$id   = wcifd_search_product(wcifd_json_decode($sku));
		$type = (wp_get_post_parent_id($id) || $notes['parent_sku']) ? 'product_variation' : 'product';


		//MANAGE STOCK
		$manage_stock = ($product->ManageWarehouse == 'true') ? 'yes' : 'no';


		//AUTHOR
		$author = (get_option('wcifd-use-suppliers') == 1 && $product->SupplierCode) ? $product->SupplierCode : get_option('wcifd-current-user');


		//TAX
		$tax_included = get_option('wcifd-tax-included');
		$price = ($tax_included == 1) ? $product->GrossPrice1 : $product->NetPrice1;


		//DANEA VARIANTS SIZE & COLOR
		$variants = $product->Variants;
		if($variants) {
			add_post_meta($id, 'wcifd-danea-size-color', 1);
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
										'_sku'                => wcifd_json_decode($sku),
										'_tax_status'         => $tax_status,
										'_tax_class'          => $tax_class,
										'_stock'              => wcifd_json_decode($stock),
										'_manage_stock'       => $manage_stock,
										'_visibility'	      => 'visible',
										'_regular_price'      => wcifd_json_decode($price),
										'_price'           	  => wcifd_json_decode($price),

									  )
			);


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
					'post_content'	   => $description,
					'meta_input'       => array(
											'_sku'                => wcifd_json_decode($sku),
											'_tax_status'         => $tax_status,
											'_tax_class'          => $tax_class,
											'_stock'              => wcifd_json_decode($stock),
											'_manage_stock'       => $manage_stock,
											'_visibility'	      => 'visible',
											'_regular_price'      => wcifd_json_decode($price),
											'_price'           	  => wcifd_json_decode($price),
										  )

				);

				//UPDATE PRODUCT
				$product_id = wp_update_post($args);

			}

		}

		//ADD PRODUCT CAT AND SUB-CAT
		wp_set_object_terms($product_id, $category, 'product_cat', true);
		$cat_id = term_exists($category, 'product_cat');
		$subcat_id = term_exists($sub_category, 'product_cat', $cat_id);

		if($sub_category){
			if(!$subcat_id) {
				wp_insert_term($sub_category, 'product_cat', array('parent' => $cat_id));
			}

			wp_set_object_terms($product_id, $sub_category, 'product_cat', true);			
		}

		
		//PRODUCTS VARS
		if($variants) {

			//UPDATE THE PARENT PRODUCT
			wp_set_object_terms($product_id, 'variable', 'product_type' );

			$avail_colors = array();
			$avail_sizes = array();

			$attributes = array(
				'pa_color' => array(
					'name' 		   => 'pa_color', 
					'value'		   => '', 
					'position'     => 0,
					'is_visible'   => 1, 
					'is_variation' => 1, 
					'is_taxonomy'  => 1
					), 
				'pa_size' => array(
					'name' 		   => 'pa_size',
					'value' 	   => '',
					'position'	   => 1,
					'is_visible'   => 1, 
					'is_variation' => 1, 
					'is_taxonomy'  => 1
					)
				);

			update_post_meta( $product_id, '_product_attributes', $attributes);

			
			$v = 1; //VARIANT LOOP
			foreach($variants->children() as $variant) {
				$size     = $variant->Size;
				$color    = $variant->Color;

				$avail_sizes[]  = wcifd_json_decode($size);
				$avail_colors[] = wcifd_json_decode($color);

				wp_set_object_terms( $var_id, $avail_colors, 'pa_color');
				wp_set_object_terms( $var_id, $avail_sizes, 'pa_size');


				$barcode  = wcifd_json_decode($variant->Barcode);
				$in_stock = wcifd_json_decode($variant->AvailableQty);

				$man_stock = 'yes';
				$stock_status = ($in_stock) ? 'instock' : 'outofstock';

				if(!wcifd_search_product($barcode)) {
 
	 				$var_args = array(
						'post_author'      => $supplier_id,
						'post_name'        => 'danea-product-' . $product_id . '-variation-' . $v++,
						'post_type'        => 'product_variation',
						'post_parent'	   => $product_id,
						'post_content'	   => $description,
						'post_status'	   => 'publish',
						'meta_input'       => array(
												'_sku'               => $barcode,
												'_stock'             => $in_stock,
												'_stock_status'		 => $stock_status,
												'_manage_stock'      => $man_stock,
												'attribute_pa_color' => sanitize_title($color),
												'attribute_pa_size'  => sanitize_title($size),
												'_regular_price'  => wcifd_json_decode($price),
												'_price'          => wcifd_json_decode($price)
											  )

					);
					
					$var_id =  wp_insert_post($var_args);

				} else {

					if(get_post_status(wcifd_search_product($barcode)) != 'trash') {

						$var_args = array(
						'ID'			   => wcifd_search_product($barcode),
						'post_author'      => $author,
						'post_name'        => 'danea-product-' . $product_id . '-variation-' . $v++,
						'post_type'        => 'product_variation',
						'post_parent'	   => $product_id,
						'post_content'	   => $description,
						'post_status'	   => 'publish',
						'meta_input'       => array(
												'_sku'               => $barcode,
												'_stock'             => $in_stock,
												'_stock_status'		 => $stock_status,
												'_manage_stock'      => $man_stock,
												'attribute_pa_color' => sanitize_title($color),
												'attribute_pa_size'  => sanitize_title($size),
												'_regular_price'  => wcifd_json_decode($price),
												'_price'          => wcifd_json_decode($price)
											  )

						);
						
						$var_id =  wp_update_post($var_args);

					}

				}

				$attr = array(
						'pa_color' => array(
							'name' 		   => sanitize_title($color), 
							'value'		   => '',
							'is_visible'   => 1, 
							'is_variation' => 1, 
							'is_taxonomy'  => 1
							), 
						'pa_size' => array(
							'name' 		   => sanitize_title($size), 
							'value' 	   => '',
							'is_visible'   => 1, 
							'is_variation' => 1, 
							'is_taxonomy'  => 1
							)
					);

			    update_post_meta( $var_id, '_product_attributes', $attr);

			}	

			wp_set_object_terms( $product_id, $avail_colors, 'pa_color');
			wp_set_object_terms( $product_id, $avail_sizes, 'pa_size');
		
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
	$premium_key = strtolower(get_option('wcifd-premium-key'));
	$url_code = strtolower(get_option('wcifd-url-code'));
	$import_images = get_option('wcifd-import-images');
	$key  = $_GET['key'];
	$code = $_GET['code'];
	$mode = $_GET['mode'];
		
	if(isset($key) && isset($code)) {

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

	if($_POST['orders-import'] && wp_verify_nonce( $_POST['wcifd-orders-nonce'], 'wcifd-orders-import' )) {

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
						if($tax) {
							$tax_status = 'taxable';
							$tax_class = wcifd_get_tax_rate_class($tax);
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
