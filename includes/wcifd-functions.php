<?php
/*
WOOCOMMERCE IMPORTER FOR DANEA | FUNCTIONS
*/


//NO DIRECT ACCESS
if ( !defined( 'ABSPATH' ) ) exit;


//GET THE ITALIANS TAX FIELDS NAMES
function wcifd_get_italian_tax_fields_names($field) {

	$cf_name = null;
	$pi_name = null;

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


