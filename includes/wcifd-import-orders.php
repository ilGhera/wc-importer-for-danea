<?php
/**
 * Importazione ordini da Danea Easyfatt
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.1
 */

/**
 * Importazione ordini
 *
 * @return void
 */
function wcifd_orders() {

	if ( isset( $_POST['orders-import'], $_POST['wcifd-orders-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-orders-nonce'] ) ), 'wcifd-orders-import' ) ) {

		/*Impostazioni admin*/
		$wcifd_orders_add_users = isset( $_POST['wcifd-orders-add-users'] ) ? sanitize_text_field( wp_unslash( $_POST['wcifd-orders-add-users'] ) ) : null;
		$wcifd_orders_status    = isset( $_POST['wcifd-orders-status'] ) ? strtolower( str_replace( ' ', '-', sanitize_text_field( wp_unslash( $_POST['wcifd-orders-status'] ) ) ) ) : null;
		update_option( 'wcifd-orders-add-users', $wcifd_orders_add_users );
		update_option( 'wcifd-orders-status', $wcifd_orders_status );

		$file   = isset( $_FILES['orders-list']['tmp_name'] ) ? sanitize_text_field( wp_unslash( $_FILES['orders-list']['tmp_name'] ) ) : null;
		$data   = simplexml_load_file( $file );
		$orders = $data->Documents;

		$o = 0; // Orders.
		$u = 0; // Users.
		$p = 0; // Products.
		foreach ( $orders->Document as $order ) {

			/*L'id ordine Danea, utile a cui verrà legato quello WooCommerce*/
			$order_number = WCIFD_Functions::decode_xml_value( $order->Number );

			if ( ! WCIFD_Functions::get_order_by_number( $order_number ) ) {

				$o++;

				/*Dettagli ordine*/
				$order_date     = WCIFD_Functions::decode_xml_value( $order->Date );
				$order_comment  = WCIFD_Functions::decode_xml_value( $order->InternalComment );
				$payment_method = WCIFD_Functions::decode_xml_value( $order->PaymentName );

				/*Dettagli cliente*/
				if ( $order->CustomerReference ) {
					$user_name = strtolower( str_replace( ' ', '-', $order->CustomerReference ) );
					$name      = explode( ' ', $order->CustomerReference );
				} else {
					$user_name = strtolower( str_replace( ' ', '-', $order->CustomerName ) );
					$name      = explode( ' ', $order->CustomerName );
				}

				/*Nomi dei campi fiscali*/
				$cf_name = WCIFD_Functions::get_italian_tax_fields_names( 'cf_name' );
				$pi_name = WCIFD_Functions::get_italian_tax_fields_names( 'pi_name' );

				/*Dettagli ordine*/
				$billing_company  = WCIFD_Functions::decode_xml_value( $order->CustomerName );
				$billing_address  = WCIFD_Functions::decode_xml_value( $order->CustomerAddress );
				$billing_city     = WCIFD_Functions::decode_xml_value( $order->CustomerCity );
				$billing_postcode = WCIFD_Functions::decode_xml_value( $order->CustomerPostcode );
				$billing_state    = WCIFD_Functions::decode_xml_value( $order->CustomerProvince );
				$billing_country  = WCIFD_Functions::get_country_code( WCIFD_Functions::decode_xml_value( $order->CustomerCountry ) );
				$billing_phone    = WCIFD_Functions::decode_xml_value( $order->CustomerTel );
				$billing_email    = WCIFD_Functions::decode_xml_value( $order->CustomerEmail );
				$fiscal_code      = WCIFD_Functions::decode_xml_value( $order->CustomerFiscalCode );
				$p_iva            = WCIFD_Functions::decode_xml_value( $order->CustomerVatCode );

				/*Dettagli spedizione*/
				$shipping_name     = WCIFD_Functions::decode_xml_value( $order->DeliveryName );
				$shipping_address  = WCIFD_Functions::decode_xml_value( $order->DeliveryAddress );
				$shipping_city     = WCIFD_Functions::decode_xml_value( $order->DeliveryCity );
				$shipping_postcode = WCIFD_Functions::decode_xml_value( $order->DeliveryPostcode );
				$shipping_state    = WCIFD_Functions::decode_xml_value( $order->DeliveryProvince );
				$shipping_country  = WCIFD_Functions::get_country_code( WCIFD_Functions::decode_xml_value( $order->DeliveryCountry ) );

				/*Creazione utente se necessario*/
				if ( ! email_exists( $order->CustomerEmail ) && ! WCIFD_Functions::get_user_id_by_tax_code( $order->CustomerVatCode ) && ! WCIFD_Functions::get_user_id_by_tax_code( $order->CustomerFiscalCode ) && 1 === intval( $wcifd_orders_add_users ) ) {

					$u++;
					$random_password = wp_generate_password( 12, false );
					$role            = ( get_option( 'wcifd-clients-role' ) ) ? get_option( 'wcifd-clients-role' ) : 'customer';

					$userdata = array(
						'role'         => $role,
						'user_login'   => $user_name,
						'first_name'   => $name[0],
						'last_name'    => $name[1],
						'display_name' => $order->CustomerName,
						'user_email'   => $order->CustomerEmail,
					);

					$user_id = wp_insert_user( $userdata );

					/*User meta*/
					if ( $order->CustomerReference ) {
						add_user_meta( $user_id, 'billing_company', $billing_company );
					}

					/* Dettagli ordine */
					add_user_meta( $user_id, 'billing_first_name', $name[0] );
					add_user_meta( $user_id, 'billing_last_name', $name[1] );
					add_user_meta( $user_id, 'billing_address_1', $billing_address );
					add_user_meta( $user_id, 'billing_city', $billing_city );
					add_user_meta( $user_id, 'billing_postcode', $billing_postcode );
					add_user_meta( $user_id, 'billing_state', $billing_state );
					add_user_meta( $user_id, 'billing_country', $billing_country );
					add_user_meta( $user_id, 'billing_phone', $billing_phone );
					add_user_meta( $user_id, 'billing_email', $billing_email );

					if ( $cf_name ) {
						add_user_meta( $user_id, $cf_name, $fiscal_code );
					}
					if ( $pi_name ) {
						add_user_meta( $user_id, $pi_name, $p_iva );
					}

					/*Dettagli spedizione*/
					add_user_meta( $user_id, 'shipping_first_name', $shipping_name );
					add_user_meta( $user_id, 'shipping_address_1', $shipping_address );
					add_user_meta( $user_id, 'shipping_city', $shipping_city );
					add_user_meta( $user_id, 'shipping_postcode', $shipping_postcode );
					add_user_meta( $user_id, 'shipping_state', $shipping_state );
					add_user_meta( $user_id, 'shipping_country', $shipping_country );

				} else {
					$user    = get_user_by( 'email', $billing_email );
					$user_id = $user->ID;
				}

				/*Dettagli ordine*/
				$billing_address = array(
					'first_name' => $name[0],
					'last_name'  => $name[1],
					'company'    => $billing_company,
					'email'      => $billing_email,
					'phone'      => $billing_phone,
					'address_1'  => $billing_address,
					'city'       => $billing_city,
					'state'      => $billing_state,
					'postcode'   => $billing_postcode,
					'country'    => $billing_country,
				);

				/*Dettagli spedizione*/
				$shipping_address = array(
					'first_name' => $shipping_name,
					'address_1'  => $shipping_address,
					'city'       => $shipping_city,
					'state'      => $shipping_state,
					'postcode'   => $shipping_postcode,
					'country'    => $shipping_country,
				);

				$args = array(
					'status'        => $wcifd_orders_status,
					'customer_id'   => $user_id,
					'customer_note' => $order_comment,
				);

				/*Creazione nuovo ordine WooCommerce*/
				$wc_order = wc_create_order( $args );

				/*Aggiunta ordine Danea*/
				add_post_meta( $wc_order->id, 'wcifd-order-number', $order_number );
				wp_update_post(
					array(
						'ID'        => $wc_order->id,
						'post_date' => $order_date,
					)
				);

				$wc_order->set_address( $billing_address, 'billing' );
				$wc_order->set_address( $shipping_address, 'shipping' );

				/*Impostazione metodo di pagamento*/
				$payment_gateway = WCIFD_Functions::get_wc_payment_gateway( $payment_method );
				update_post_meta( $wc_order->id, '_payment_method', $payment_gateway['id'] );
				update_post_meta( $wc_order->id, '_payment_method_title', $payment_gateway['title'] );

				/*Dettagli prodotti*/
				foreach ( $order->Rows->Row as $item ) {

					$sku         = WCIFD_Functions::decode_xml_value( $item->Code );
					$title       = WCIFD_Functions::decode_xml_value( $item->Description );
					$tax         = WCIFD_Functions::decode_xml_value( $item->VatCode );
					$price       = WCIFD_Functions::decode_xml_value( $item->Price );
					$total_sales = WCIFD_Functions::decode_xml_value( $item->Qty );

					/*Verifica presenza prodotto*/
					if ( WCIFD_Functions::search_product( $item->Code ) ) {

						$product_id = WCIFD_Functions::search_product( $sku );
						$wc_order->add_product( get_product( $product_id ), $total_sales );

					} else {

						/*Creazione nuovo prodotto WooCommerce*/
						$p++;

						/*Verifica classe di imposta*/
						$tax_status = 'none';
						$tax_class  = '';
						$perc       = WCIFD_Functions::decode_xml_value( $tax['Perc'] );
						$class      = WCIFD_Functions::decode_xml_value( $tax['Class'] );
						if ( 0 !== intval( $perc ) || 'Escluso' !== $class ) {
							$tax_status = 'taxable';
							$tax_class  = WCIFD_Functions::get_tax_rate_class( WCIFD_Functions::decode_xml_value( $tax ), strval( $perc ) );
						}

						$args = array(
							'post_author' => get_current_user_id(), // Al momento non viene recuperato l'ID del fornitore.
							'post_title'  => $title,
							'post_type'   => 'product',
							'post_status' => 'publish',
							'meta_input'  => array(
								'_sku'           => $sku,
								'_tax_status'    => $tax_status,
								'_tax_class'     => $tax_class,
								'_regular_price' => $price,
								'_price'         => $price,
							),

						);
						$product_id = wp_insert_post( $args );
						wp_set_object_terms( $product_id, 'Imported', 'product_cat', true );
						$wc_order->add_product( get_product( $product_id ), $total_sales );

					}
				}

				$wc_order->calculate_totals();

			}
		}

		$output  = '<div id="message" class="updated"><p>';
		$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';

		/* Translators: 1 numero di ordini, 2 numero di utenti, 3 numero di prodotti */
		$output .= sprintf( __( 'Imported %1$d orders, %2$d users and %3$d products.', 'wc-importer-for-danea' ), $o, $u, $p );
		$output .= '</p></div>';

		echo wp_kses_post( $output );

	}

}
