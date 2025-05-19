<?php
/**
 * Import orders from Danea Easyfatt
 *
 * @author  ilGhera
 * @package wc-importer-for-danea-premium/includes
 *
 * @since 1.6.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class wcifd_orders
 *
 * @return void
 */
class WCIFD_Import_Orders {


	/**
	 * Add new users
	 *
	 * @var boolean
	 */
	public $add_new_users;

	/**
	 * Orders status
	 *
	 * @var string
	 */
	public $orders_status;

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->import_orders();
	}

	/**
	 * Import orders
	 *
	 * @return void
	 */
	public function import_orders() {

		$file = $this->import_file();

		if ( $file ) {

			$data   = simplexml_load_file( $file );
			$orders = $data->Documents;

			$o = 0; // Orders.
			$u = 0; // Users.
			$p = 0; // Products.

			foreach ( $orders->Document as $order ) {

				/* Import single order */
				$this->import_single_order( $order, $o, $u, $p );
			}

			$output  = '<div id="message" class="updated"><p>';
			$output .= '<strong>Woocommerce Importer for Danea - Premium</strong><br>';

			/* Translators: 1 orders number, 2 users number, 3 products number */
			$output .= sprintf( __( 'Imported %1$d orders, %2$d users and %3$d products.', 'wc-importer-for-danea' ), $o, $u, $p );
			$output .= '</p></div>';

			echo wp_kses_post( $output );
		}
	}

	/**
	 * Import file
	 *
	 * @return object
	 */
	public function import_file() {

		if ( isset( $_POST['orders-import'], $_POST['wcifd-orders-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-orders-nonce'] ) ), 'wcifd-orders-import' ) ) {

			/* Update import options */
			$this->update_options();

			return isset( $_FILES['orders-list']['tmp_name'] ) ? sanitize_text_field( wp_unslash( $_FILES['orders-list']['tmp_name'] ) ) : null;
		}
	}

	/**
	 * Import single order
	 *
	 * @param object $order the Danea order.
	 * @param int    $o     the orders counter.
	 * @param int    $u     the users counter.
	 * @param int    $p     the products counter.
	 *
	 * @return void
	 */
	public function import_single_order( $order, $o, $u, $p ) {

		/* The Danea order ID */
		$order_number = WCIFD_Functions::decode_xml_value( $order->Number );

		/* Check if the order already exists */
		if ( ! WCIFD_Functions::get_order_by_number( $order_number ) ) {

			/* Increase the orders counter */
			$o++;

			/* Get order data */
			$order_data = $this->get_order_data( $order );

			/* Create new user if necessary */
			if ( ! email_exists( $order->CustomerEmail ) && ! WCIFD_Functions::get_user_id_by_tax_code( $order->CustomerVatCode ) && ! WCIFD_Functions::get_user_id_by_tax_code( $order->CustomerFiscalCode ) && 1 === intval( $this->add_new_users ) ) {

				/* Increase the users counter */
				$u++;

				/* Add user */
				$user_id = $this->add_user( $order_data );

			} else {

				$user    = get_user_by( 'email', $order_data['billing_email'] );
				$user_id = $user->ID;
			}

			$args = array(
				'status'        => $this->orders_status,
				'customer_id'   => get_current_user_id(),
				'customer_note' => $order_data['order_comment'],
			);

			/* Create a new WC order */
			$wc_order = wc_create_order( $args );

			/* Add Danea order number */
			$wc_order->add_meta_data( 'wcifd-order-number', $order_number );

			$wc_order->set_date_created( $order_data['order_date'] );
			$wc_order->set_address( $this->get_billing_address( $order_data ), 'billing' );
			$wc_order->set_address( $this->get_shipping_address( $order_data ), 'shipping' );

			/* Set payment method */
			$payment_gateway = WCIFD_Functions::get_wc_payment_gateway( $order_data['payment_method'] );

			if ( $payment_gateway ) {

				$wc_order->set_payment_method( $payment_gateway['id'] );
				$wc_order->set_payment_method_title( $payment_gateway['title'] );
			}

			/* Products details */
			foreach ( $order->Rows->Row as $item ) {

				/* Add order item */
				$this->add_order_item( $wc_order, $item, $p );
			}

			$wc_order->calculate_totals();
			$wc_order->save();
		}
	}

	/**
	 * Update import options
	 *
	 * @return void
	 */
	public function update_options() {

		if ( isset( $_POST['wcifd-orders-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-orders-nonce'] ) ), 'wcifd-orders-import' ) ) {

			$this->add_new_users = isset( $_POST['wcifd-orders-add-users'] ) ? sanitize_text_field( wp_unslash( $_POST['wcifd-orders-add-users'] ) ) : null;
			$this->orders_status = isset( $_POST['wcifd-orders-status'] ) ? strtolower( str_replace( ' ', '-', sanitize_text_field( wp_unslash( $_POST['wcifd-orders-status'] ) ) ) ) : null;

			/* Save options */
			update_option( 'wcifd-orders-add-users', $this->add_new_users );
			update_option( 'wcifd-orders-status', $this->orders_status );
		}
	}

	/**
	 * Get order data
	 *
	 * @param object $order the Danea order.
	 *
	 * @return array
	 */
	public function get_order_data( $order ) {

		$order_data = array();

		/* Order details */
		$order_data['order_date']     = WCIFD_Functions::decode_xml_value( $order->Date );
		$order_data['order_comment']  = WCIFD_Functions::decode_xml_value( $order->InternalComment );
		$order_data['payment_method'] = WCIFD_Functions::decode_xml_value( $order->PaymentName );

		/* Client details */
		if ( $order->CustomerReference ) {
			$order_data['user_name'] = strtolower( str_replace( ' ', '-', $order->CustomerReference ) );
			$order_data['name']      = explode( ' ', $order->CustomerReference );
		} else {
			$order_data['user_name'] = strtolower( str_replace( ' ', '-', $order->CustomerName ) );
			$order_data['name']      = explode( ' ', $order->CustomerName );
		}

		/* Fiscal fields names */
		$order_data['cf_name'] = WCIFD_Functions::get_italian_tax_fields_names( 'cf_name' );
		$order_data['pi_name'] = WCIFD_Functions::get_italian_tax_fields_names( 'pi_name' );

		/* Order details */
		$order_data['billing_company']  = WCIFD_Functions::decode_xml_value( $order->CustomerName );
		$order_data['billing_address']  = WCIFD_Functions::decode_xml_value( $order->CustomerAddress );
		$order_data['billing_city']     = WCIFD_Functions::decode_xml_value( $order->CustomerCity );
		$order_data['billing_postcode'] = WCIFD_Functions::decode_xml_value( $order->CustomerPostcode );
		$order_data['billing_state']    = WCIFD_Functions::decode_xml_value( $order->CustomerProvince );
		$order_data['billing_country']  = WCIFD_Functions::get_country_code( WCIFD_Functions::decode_xml_value( $order->CustomerCountry ) );
		$order_data['billing_phone']    = WCIFD_Functions::decode_xml_value( $order->CustomerTel );
		$order_data['billing_email']    = WCIFD_Functions::decode_xml_value( $order->CustomerEmail );
		$order_data['fiscal_code']      = WCIFD_Functions::decode_xml_value( $order->CustomerFiscalCode );
		$order_data['p_iva']            = WCIFD_Functions::decode_xml_value( $order->CustomerVatCode );

		/* Shipping details */
		$order_data['shipping_name']     = WCIFD_Functions::decode_xml_value( $order->DeliveryName );
		$order_data['shipping_address']  = WCIFD_Functions::decode_xml_value( $order->DeliveryAddress );
		$order_data['shipping_city']     = WCIFD_Functions::decode_xml_value( $order->DeliveryCity );
		$order_data['shipping_postcode'] = WCIFD_Functions::decode_xml_value( $order->DeliveryPostcode );
		$order_data['shipping_state']    = WCIFD_Functions::decode_xml_value( $order->DeliveryProvince );
		$order_data['shipping_country']  = WCIFD_Functions::get_country_code( WCIFD_Functions::decode_xml_value( $order->DeliveryCountry ) );

		return $order_data;
	}

	/**
	 * Add order item
	 *
	 * @param object $wc_order the WC order.
	 * @param object $item     the Danea order item.
	 * @param int    $p        the products counter.
	 *
	 * @return void
	 */
	public function add_order_item( $wc_order, $item, $p ) {

		/* Get item data */
		$item_data = $this->get_item_data( $item );
		$wc_item   = null;

		if ( $item_data['sku'] ) {

			/* Check if the product already exists */
			$product_id = WCIFD_Functions::search_product( $item_data['sku'] );

			if ( ! $product_id ) {

				/* Increase the products counter */
				$p++;

				/* Create new WC product */
				$product_id = $this->create_new_product( $item_data );

				/* Add the product category Imported */
				wp_set_object_terms( $product_id, 'Imported', 'product_cat', true );
			}

			if ( $product_id ) {

				/* Add new WC order item */
				$wc_item = $this->add_order_item_product( $item_data, $product_id );
			}
		} else {

			/* Check if is a discount */
			if ( 0 > $item_data['price'] ) {

				/* Add new WC order item */
				$wc_item = $this->add_order_item_fee( $item_data );
			}
		}

		if ( $wc_item ) {

			/* Add item to the WC order */
			$wc_order->add_item( $wc_item );
		}
	}

	/**
	 * Add order item product
	 *
	 * @param array $item_data  the Danea order item data.
	 * @param int   $product_id the WC product ID.
	 *
	 * @return object
	 */
	public function add_order_item_product( $item_data, $product_id ) {

		/* Get product */
		$product = wc_get_product( $product_id );

		$wc_item = new WC_Order_Item_Product();
		$wc_item->set_product_id( $product_id );
		$wc_item->set_quantity( $item_data['total_sales'] );
		$wc_item->set_name( $product->get_name() );
		$wc_item->set_subtotal( $product->get_price() * $item_data['total_sales'] );
		$wc_item->set_total( $product->get_price() * $item_data['total_sales'] );
		$wc_item->set_tax_class( $product->get_tax_class() );
		$wc_item->save();

		return $wc_item;
	}

	/**
	 * Add order item fee
	 *
	 * @param array $item_data the Danea order item data.
	 *
	 * @return object
	 */
	public function add_order_item_fee( $item_data ) {

		/* Get tax details */
		$tax_details = $this->get_tax_details( $item_data );

		$wc_item = new WC_Order_Item_Fee();
		$wc_item->set_name( $item_data['title'] );
		$wc_item->set_amount( $item_data['price'] );
		$wc_item->set_total( $item_data['price'] * $item_data['total_sales'] );
		$wc_item->set_tax_class( $tax_details['class'] );
		$wc_item->save();

		return $wc_item;
	}

	/**
	 * Get tax details
	 *
	 * @param array $item_data the Danea order item data.
	 *
	 * @return array
	 */
	public function get_tax_details( $item_data ) {

		$tax_details = array(
			'status' => 'none',
			'class'  => null,
		);

		$perc  = isset( $item_data['tax']['Perc'] ) ? WCIFD_Functions::decode_xml_value( $item_data['tax']['Perc'] ) : null;
		$class = isset( $item_data['tax']['Class'] ) ? WCIFD_Functions::decode_xml_value( $item_data['tax']['Class'] ) : null;

		if ( 0 !== intval( $perc ) ) {
			$tax_details['status'] = 'taxable';
			$tax_details['class']  = WCIFD_Functions::get_tax_rate_class( WCIFD_Functions::decode_xml_value( $item_data['tax'] ), strval( $perc ) );
		}

		return $tax_details;
	}

	/**
	 * Get item data
	 *
	 * @param object $item the Danea order item.
	 *
	 * @return array
	 */
	public function get_item_data( $item ) {

		$item_data = array();

		$item_data['sku']         = WCIFD_Functions::decode_xml_value( $item->Code );
		$item_data['title']       = WCIFD_Functions::decode_xml_value( $item->Description );
		$item_data['tax']         = WCIFD_Functions::decode_xml_value( $item->VatCode );
		$item_data['price']       = WCIFD_Functions::decode_xml_value( $item->Price );
		$item_data['total_sales'] = WCIFD_Functions::decode_xml_value( $item->Qty );

		return $item_data;
	}

	/**
	 * Create new product
	 *
	 * @param array $item_data the item data.
	 *
	 * @return int the product ID
	 */
	public function create_new_product( $item_data ) {

		/* Get tax details */
		$tax_details = $this->get_tax_details( $item_data );

		/* Insert the new product */
		$product = new WC_Product_Simple();

		$props = array(
			'author'        => get_current_user_id(),
			'name'          => $item_data['title'],
			'type'          => 'product',
			'status'        => 'publish',
			'sku'           => $item_data['sku'],
			'tax_status'    => $tax_details['status'],
			'tax_class'     => $tax_details['class'],
			'regular_price' => $item_data['price'],
			'price'         => $item_data['price'],
		);

		/* Set props */
		$product->set_props( $props );

		return $product->save();
	}

	/**
	 * Get billing address
	 *
	 * @param array $order_data the Danea order data.
	 *
	 * @return array
	 */
	public function get_billing_address( $order_data ) {

		$billing_address = array(
			'first_name' => $order_data['name'][0],
			'last_name'  => $order_data['name'][1],
			'company'    => $order_data['billing_company'],
			'email'      => $order_data['billing_email'],
			'phone'      => $order_data['billing_phone'],
			'address_1'  => $order_data['billing_address'],
			'city'       => $order_data['billing_city'],
			'state'      => $order_data['billing_state'],
			'postcode'   => $order_data['billing_postcode'],
			'country'    => $order_data['billing_country'],
		);

		return $billing_address;
	}

	/**
	 * Get shipping address
	 *
	 * @param array $order_data the Danea order data.
	 *
	 * @return array
	 */
	public function get_shipping_address( $order_data ) {

		/* Shipping details */
		$shipping_address = array(
			'first_name' => $order_data['shipping_name'],
			'address_1'  => $order_data['shipping_address'],
			'city'       => $order_data['shipping_city'],
			'state'      => $order_data['shipping_state'],
			'postcode'   => $order_data['shipping_postcode'],
			'country'    => $order_data['shipping_country'],
		);

		return $shipping_address;
	}

	/**
	 * Add user
	 *
	 * @param array $order_data the order data.
	 *
	 * @return int the user ID
	 */
	public function add_user( $order_data ) {

		$random_password = wp_generate_password( 12, false );
		$role            = ( get_option( 'wcifd-clients-role' ) ) ? get_option( 'wcifd-clients-role' ) : 'customer';

		$userdata = array(
			'role'         => $role,
			'user_login'   => $order_data['user_name'],
			'first_name'   => $order_data['name'][0],
			'last_name'    => $order_data['name'][1],
			'display_name' => $order_data['name'],
			'user_email'   => $order_data['billing_email'],
			'user_pass'    => $random_password,
		);

		$user_id = wp_insert_user( $userdata );

		/*User meta*/
		if ( $order_data['billing_company'] ) {
			add_user_meta( $user_id, 'billing_company', $order_data['billing_company'] );
		}

		/* Order details */
		add_user_meta( $user_id, 'billing_first_name', $order_data['name'][0] );
		add_user_meta( $user_id, 'billing_last_name', $order_data['name'][1] );
		add_user_meta( $user_id, 'billing_address_1', $order_data['billing_address'] );
		add_user_meta( $user_id, 'billing_city', $order_data['billing_city'] );
		add_user_meta( $user_id, 'billing_postcode', $order_data['billing_postcode'] );
		add_user_meta( $user_id, 'billing_state', $order_data['billing_state'] );
		add_user_meta( $user_id, 'billing_country', $order_data['billing_country'] );
		add_user_meta( $user_id, 'billing_phone', $order_data['billing_phone'] );
		add_user_meta( $user_id, 'billing_email', $order_data['billing_email'] );

		if ( $order_data['cf_name'] ) {
			add_user_meta( $user_id, $order_data['cf_name'], $order_data['fiscal_code'] );
		}
		if ( $order_data['pi_name'] ) {
			add_user_meta( $user_id, $order_data['pi_name'], $order_data['p_iva'] );
		}

		/* Shipping details */
		add_user_meta( $user_id, 'shipping_first_name', $order_data['shipping_name'] );
		add_user_meta( $user_id, 'shipping_address_1', $order_data['shipping_address'] );
		add_user_meta( $user_id, 'shipping_city', $order_data['shipping_city'] );
		add_user_meta( $user_id, 'shipping_postcode', $order_data['shipping_postcode'] );
		add_user_meta( $user_id, 'shipping_state', $order_data['shipping_state'] );
		add_user_meta( $user_id, 'shipping_country', $order_data['shipping_country'] );

		return $user_id;
	}
}

