<?php
/**
 * Import contacts from Danea as WordPress users
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Import_Users
 *  
 * @since 1.4.0
 */
class WCIFD_Import_Users {

	/**
	 * The type of users to import
	 *
	 * @var string 
	 */
	public $type;

    /**
     * The role to assign to imported users
     *
     * @var string 
     */
    public $role;

    /**
     * The functions class
     *
     * @var object 
     */
    public $functions;

    /**
     * The constructor.
     *
     * @return void
     */
    public function __construct( $type ) {

        /* Initialize the function class */
        $this->functions = new WCIFD_Functions();

        $this->type = $type;
        $this->import_users();
    }

    /**
     * Import users
     *
     * @return void
     */
    public function import_users() {
        
		$file = $this->import_file();

		if ( $file ) {

			$i      = 0;
			$n      = 0;
			$row    = 0;
			$head   = null;
			$handle = fopen( $file, 'r' );

			if ( $handle ) {

				while ( ( $user = fgetcsv( $handle, 1000, ',', '"', '\\' ) ) !== false ) {

					$row ++;

					if ( 1 === $row ) {

						$head = $user;

					} else {

                        /* Import single user */
                        $this->import_single_user( $user, $head, $i, $n );
                    }
				}

				fclose( $handle );
			}

			$output  = '<div id="message" class="updated"><p>';
			$output .= '<strong>ilGhera Danea Importer for WooCommerce</strong><br>';

			/* Translators: 1 the users imported, 2 the total rows */
			$output .= sprintf( __( 'Imported %1$d of %2$d contacts<br>', 'wc-importer-for-danea' ), $i, ( $row - 1 ) );

			/* Translators: 1 the users updated, 2 the total rows */
			$output .= sprintf( __( 'Updated %1$d of %2$d contacts', 'wc-importer-for-danea' ), $n, ( $row - 1 ) );

			$output .= '</p></div>';

			echo wp_kses_post( $output );
		}
    }

    /**
     * Import file
     *
     * @return string
     */
    public function import_file() {

        if ( isset( $_POST[ $this->type . '-import' ], $_POST[ 'wcifd-' . $this->type . '-nonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ 'wcifd-' . $this->type . '-nonce' ] ) ), 'wcifd-' . $this->type . '-import' ) ) {

            if ( isset( $_POST[ 'wcifd-users-' . $this->type ] ) ) {

                $role = sanitize_text_field( wp_unslash( $_POST[ 'wcifd-users-' . $this->type ] ) );
                $this->role = $role;

                update_option( 'wcifd-' . $this->type . '-role', $role );
            }

            return isset( $_FILES[ $this->type . '-list' ]['tmp_name'] ) ? sanitize_text_field( wp_unslash( $_FILES[ $this->type . '-list' ]['tmp_name'] ) ) : '';
        }
    }

    /**
     * Import single user
     *
     * @param object $user the Danea user.
     * @param int    $head the Danea user.
     *
     * @return void
     */
    public function import_single_user( $user, $head, $i, $n ) {

        $user  = array_combine( $head, $user );
        $email = $user['e-mail'];

        /* Email required */
        if ( ! $email ) {

            return;
        }

        /* Get the user data */
        $danea_user = $this->get_user_data( $user );

        $userdata = array(
            'role'         => $this->role,
            'user_login'   => $danea_user['user_name'],
            'user_pass'    => '',
            'first_name'   => $danea_user['name'][0],
            'last_name'    => $danea_user['last_name'],
            'display_name' => $user['Denominazione'],
            'user_email'   => $email,
            'description'  => $danea_user['description'],
        );

        /* Define the tax fields */
        $cf_name      = $this->functions->get_italian_tax_fields_names( 'cf_name' );
        $pi_name      = $this->functions->get_italian_tax_fields_names( 'pi_name' );
        $pec_name     = $this->functions->get_italian_tax_fields_names( 'pec_name' );
        $pa_code_name = $this->functions->get_italian_tax_fields_names( 'pa_code_name' );

        /* Check if the user exists */
        $user_id = username_exists( $danea_user['user_name'] );

        if ( ! $user_id ) {

            $get_user = get_user_by( 'email', $email );
            $user_id  = is_object( $get_user ) && isset( $get_user->ID ) ? $get_user->ID : null;
        }

        /* Add the new user */
        if ( ! $user_id && ! email_exists( $email ) ) {

            $i++;
            $user_id = wp_insert_user( $userdata );

        } else {

            /* Update the user */
            $n++;
            $userdata['ID'] = $user_id;

            /* Check if the user role must be changed */
            $user_info  = get_userdata( $user_id );
            $user_roles = is_object( $user_info ) ? $user_info->roles : null;

            if ( is_array( $user_roles ) && ! in_array( $this->role, $user_roles, true ) ) {

                unset( $userdata['role'] );
                $the_user = new WP_User( $user_id );
                $the_user->set_role( $this->role );
            }

            wp_update_user( $userdata );
        }

        /* User meta */
        if ( $user['Referente'] ) {

            update_user_meta( $user_id, 'billing_company', $user['Denominazione'] );
        }

        update_user_meta( $user_id, 'billing_first_name', $danea_user['name'][0] );
        update_user_meta( $user_id, 'billing_last_name', $danea_user['last_name'] );
        update_user_meta( $user_id, 'billing_address_1', $danea_user['address'] );
        update_user_meta( $user_id, 'billing_city', $danea_user['city'] );
        update_user_meta( $user_id, 'billing_postcode', $danea_user['cap'] );
        update_user_meta( $user_id, 'billing_state', $danea_user['state'] );
        update_user_meta( $user_id, 'billing_country', $danea_user['country'] );
        update_user_meta( $user_id, 'billing_phone', $danea_user['tel'] );
        update_user_meta( $user_id, 'billing_email', $email );

        if ( $cf_name ) {

            update_user_meta( $user_id, $cf_name, $danea_user['fiscal_code'] );
        }

        if ( $pi_name ) {

            update_user_meta( $user_id, $pi_name, $danea_user['p_iva'] );
        }

        if ( $pec_name ) {

            update_user_meta( $user_id, $pec_name, $danea_user['pec'] );
        }

        if ( $pa_code_name ) {

            update_user_meta( $user_id, $pa_code_name, $danea_user['pa_code'] );
        }
    }

    /**
     * Get the user data
     *
     * @param array $user the Danea user.
     *
     * @return array
     */
    public function get_user_data( $user ) {

        $output = array();

        if ( $user['Referente'] ) {

            $output['user_name'] = strtolower( str_replace( ' ', '-', $user['Referente'] ) );
            $output['name']      = explode( ' ', $user['Referente'] );

        } else {

            $output['user_name'] = strtolower( str_replace( ' ', '-', $user['Denominazione'] ) );
            $output['name']      = explode( ' ', $user['Denominazione'] );
        }

        $output['last_name']   = isset( $output['name'][1] ) ? $output['name'][1] : '';
        $output['address']     = isset( $user['Indirizzo'] ) ? $user['Indirizzo'] : '';
        $output['cap']         = isset( $user['Cap'] ) ? $user['Cap'] : '';
        $output['city']        = isset( $user['Città'] ) ? $user['Città'] : '';
        $output['state']       = isset( $user['Prov.'] ) ? $user['Prov.'] : '';
        $output['country']     = isset( $user['Nazione'] ) ? $this->functions->get_country_code( $user['Nazione'] ) : '';
        $output['tel']         = isset( $user['Tel.'] ) ? $user['Tel.'] : '';
        $output['fiscal_code'] = isset( $user['Codice fiscale'] ) ? $user['Codice fiscale'] : '';
        $output['p_iva']       = isset( $user['Partita Iva'] ) ? $user['Partita Iva'] : '';
        $output['pec']         = isset( $user['Pec'] ) ? $user['Pec'] : '';
        $output['pa_code']     = isset( $user['Cod. destinatario Fatt. elettr.'] ) ? $user['Cod. destinatario Fatt. elettr.'] : '';
        $output['description'] = isset( $user['Note'] ) ? $user['Note'] : '';

        return $output;
    }
}

