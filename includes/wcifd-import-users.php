<?php
/**
 * Importazione contatti Danea come utenti WordPress
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 * @since 1.2.0
 *
 * @param  string $type il ruolo da assegnare agli utenti importati
 */
function wcifd_users( $type ) {

	if ( isset( $_POST[ $type . '-import' ] ) && wp_verify_nonce( $_POST[ 'wcifd-' . $type . '-nonce' ], 'wcifd-' . $type . '-import' ) ) {

		if ( isset( $_POST[ 'wcifd-users-' . $type ] ) ) {
			$role = sanitize_text_field( $_POST[ 'wcifd-users-' . $type ] );
			update_option( 'wcifd-' . $type . '-role', $role );
		}

		$file = isset( $_FILES[ $type . '-list' ]['tmp_name'] ) ? $_FILES[ $type . '-list' ]['tmp_name'] : '';

		if ( $file ) {
			$rows = array_map( 'str_getcsv', file( $file ) );
			$header = array_shift( $rows );
			$users = array();
			foreach ( $rows as $row ) {
				$users[] = array_combine( $header, $row );
			}

			$i = 0;
			$n = 0;
			foreach ( $users as $user ) {

				if ( $user['Referente'] ) {
					$user_name = strtolower( str_replace( ' ', '-', $user['Referente'] ) );
					$name = explode( ' ', $user['Referente'] );
				} else {
					$user_name = strtolower( str_replace( ' ', '-', $user['Denominazione'] ) );
					$name = explode( ' ', $user['Denominazione'] );
				}

				$address     = $user['Indirizzo'];
				$cap         = $user['Cap'];
				$city        = $user['Città'];
				$state       = $user['Prov.'];
				$country     = wcifd_get_state_code( $user['Nazione'] );
				$tel         = $user['Tel.'];
				$email       = $user['e-mail'];
				$fiscal_code = $user['Codice fiscale'];
				$p_iva       = $user['Partita Iva'];
				$pec         = $user['Pec'];
				$pa_code     = $user['Cod. destinatario Fatt. elettr.'];
				$description = $user['Note'];

				$userdata = array(
					'role' => $role,
					'user_login'   => $user_name,
					'user_pass'    => null,
					'first_name'   => $name[0],
					'last_name'    => $name[1],
					'display_name' => $user['Denominazione'],
					'user_email'   => $email,
					'description'  => $description,
				);

				/*Definisco i campi fiscali*/
				$cf_name      = wcifd_get_italian_tax_fields_names( 'cf_name' );
				$pi_name      = wcifd_get_italian_tax_fields_names( 'pi_name' );
				$pec_name     = wcifd_get_italian_tax_fields_names( 'pec_name' );
				$pa_code_name = wcifd_get_italian_tax_fields_names( 'pa_code_name' );

				/*Verifico la presenza dell'utente*/
				$user_id = username_exists( $user_name );

				/*Add the new user*/
				if ( ! $user_id and email_exists( $email ) == false ) {

					$i++;
					$user_id = wp_insert_user( $userdata );

				} else {

					/*Update the user*/
					$n++;
					$userdata['ID'] = $user_id;

					/*Check if the user role must be changed*/
					$user_info = get_userdata( $user_id );
					$user_roles = $user_info->roles;

					if ( ! in_array( $role, $user_roles ) ) {
						unset( $userdata['role'] );
						$the_user = new WP_User( $user_id );
						$the_user->set_role( $role );
					}

					wp_update_user( $userdata );

				}

				/*User meta*/
				if ( $user['Referente'] ) {
					update_user_meta( $user_id, 'billing_company', $user['Denominazione'] );
				}
				update_user_meta( $user_id, 'billing_first_name', $name[0] );
				update_user_meta( $user_id, 'billing_last_name', $name[1] );
				update_user_meta( $user_id, 'billing_address_1', $address );
				update_user_meta( $user_id, 'billing_city', $city );
				update_user_meta( $user_id, 'billing_postcode', $cap );
				update_user_meta( $user_id, 'billing_state', $state );
				update_user_meta( $user_id, 'billing_country', $country );
				update_user_meta( $user_id, 'billing_phone', $tel );
				update_user_meta( $user_id, 'billing_email', $email );

				if ( $cf_name ) {
					update_user_meta( $user_id, $cf_name, $fiscal_code );
				}

				if ( $pi_name ) {
					update_user_meta( $user_id, $pi_name, $p_iva );
				}

				if ( $pec_name ) {
					update_user_meta( $user_id, $pec_name, $pec );
				}

				if ( $pa_code_name ) {
					update_user_meta( $user_id, $pa_code_name, $pa_code );
				}
			}

			$output  = '<div id="message" class="updated"><p>';
			$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';
			$output .= sprintf( __( 'Imported %1$d of %2$d contacts<br>', 'wcifd' ), $i, count( $users ) );
			$output .= sprintf( __( 'Updated %1$d of %2$d contacts', 'wcifd' ), $n, count( $users ) );
			$output .= '</p></div>';
			echo $output;
		}
	}

}
