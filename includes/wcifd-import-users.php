<?php
/**
 * Importazione contatti Danea come utenti WordPress
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.1
 */

/**
 * Importazione utenti
 *
 * @param  string $type il ruolo da assegnare agli utenti importati.
 *
 * @return void
 */
function wcifd_users( $type ) {

	if ( isset( $_POST[ $type . '-import' ], $_POST[ 'wcifd-' . $type . '-nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'wcifd-' . $type . '-nonce' ] ) ), 'wcifd-' . $type . '-import' ) ) {

		if ( isset( $_POST[ 'wcifd-users-' . $type ] ) ) {
			$role = sanitize_text_field( wp_unslash( $_POST[ 'wcifd-users-' . $type ] ) );
			update_option( 'wcifd-' . $type . '-role', $role );
		}

		$file = isset( $_FILES[ $type . '-list' ]['tmp_name'] ) ? sanitize_text_field( wp_unslash( $_FILES[ $type . '-list' ]['tmp_name'] ) ) : '';

		if ( $file ) {

			$i      = 0;
			$n      = 0;
			$row    = 0;
			$head   = null;
			$handle = fopen( $file, 'r' );

			if ( $handle ) {

				while ( ( $user = fgetcsv( $handle, 1000, ',' ) ) !== false ) {

					$row ++;

					if ( 1 === $row ) {

						$head = $user;

					} else {

						$user  = array_combine( $head, $user );
						$email = $user['e-mail'];

						/*Email obbligatoria*/
						if ( ! $email ) {
							continue;
						}

						if ( $user['Referente'] ) {
							$user_name = strtolower( str_replace( ' ', '-', $user['Referente'] ) );
							$name      = explode( ' ', $user['Referente'] );
						} else {
							$user_name = strtolower( str_replace( ' ', '-', $user['Denominazione'] ) );
							$name      = explode( ' ', $user['Denominazione'] );
						}

						$last_name   = isset( $name[1] ) ? $name[1] : null;
						$address     = $user['Indirizzo'];
						$cap         = $user['Cap'];
						$city        = $user['Città'];
						$state       = $user['Prov.'];
						$country     = wcifd_get_state_code( $user['Nazione'] );
						$tel         = $user['Tel.'];
						$fiscal_code = $user['Codice fiscale'];
						$p_iva       = $user['Partita Iva'];
						$pec         = $user['Pec'];
						$pa_code     = $user['Cod. destinatario Fatt. elettr.'];
						$description = $user['Note'];

						$userdata = array(
							'role'         => $role,
							'user_login'   => $user_name,
							'user_pass'    => null,
							'first_name'   => $name[0],
							'last_name'    => $last_name,
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

						if ( ! $user_id ) {
							$get_user = get_user_by( 'email', $email );
							$user_id  = is_object( $get_user ) && isset( $get_user->ID ) ? $get_user->ID : null;
						}

						/*Add the new user*/
						if ( ! $user_id && ! email_exists( $email ) ) {

							$i++;
							$user_id = wp_insert_user( $userdata );

						} else {

							/*Update the user*/
							$n++;
							$userdata['ID'] = $user_id;

							/*Check if the user role must be changed*/
							$user_info  = get_userdata( $user_id );
							$user_roles = is_object( $user_info ) ? $user_info->roles : null;

							if ( is_array( $user_roles ) && ! in_array( $role, $user_roles, true ) ) {
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
						update_user_meta( $user_id, 'billing_last_name', $last_name );
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
				}

				fclose( $handle );
			}

			$output  = '<div id="message" class="updated"><p>';
			$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';

			/* Translators: 1 the users imported, 2 the total rows */
			$output .= sprintf( __( 'Imported %1$d of %2$d contacts<br>', 'wc-importer-for-danea' ), $i, ( $row - 1 ) );

			/* Translators: 1 the users updated, 2 the total rows */
			$output .= sprintf( __( 'Updated %1$d of %2$d contacts', 'wc-importer-for-danea' ), $n, ( $row - 1 ) );

			$output .= '</p></div>';

			echo wp_kses_post( $output );
		}
	}

}

