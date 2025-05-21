<?php
/**
 * Products template
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.6.1
 */

defined( 'ABSPATH' ) || exit;

$tax_included               = get_option( 'wcifd-tax-included' );
$use_suppliers              = get_option( 'wcifd-use-suppliers' );
$display_producer           = get_option( 'wcifd-display-producer' );
$display_supplier           = get_option( 'wcifd-display-supplier' );
$display_sup_product_code   = get_option( 'wcifd-display-sup-product-code' );
$regular_price_list         = get_option( 'wcifd-regular-price-list' );
$sale_price_list            = get_option( 'wcifd-sale-price-list' );
$size_type                  = get_option( 'wcifd-size-type' );
$weight_type                = get_option( 'wcifd-weight-type' );
$notes_as_description       = get_option( 'wcifd-notes-as-description' );
$short_description          = get_option( 'wcifd-short-description' );
$exclude_description        = get_option( 'wcifd-exclude-description' );
$exclude_title              = get_option( 'wcifd-exclude-title' );
$exclude_url                = get_option( 'wcifd-exclude-url' );
$deleting_categories        = get_option( 'wcifd-deleting-categories' );
$deleted_products           = get_option( 'wcifd-deleted-products' );
$replace_products           = get_option( 'wcifd-replace-products' );
$products_variations_prices = get_option( 'wcifd-products-variations-prices' );
$products_not_available     = get_option( 'wcifd-products-not-available' );
$publish_new_products       = get_option( 'wcifd-publish-new-products' );

if ( isset( $_POST['wcifd-products-general-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-products-general-nonce'] ) ), 'wcifd-products-general' ) ) {

	if ( isset( $_POST['tax-included'] ) ) {
		$tax_included = sanitize_text_field( wp_unslash( $_POST['tax-included'] ) );
		update_option( 'wcifd-tax-included', $tax_included );
	}

	if ( isset( $_POST['hidden-use-suppliers'] ) ) {
		$use_suppliers = ( isset( $_POST['wcifd-use-suppliers'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wcifd-use-suppliers'] ) ) : 0;
		update_option( 'wcifd-use-suppliers', $use_suppliers );
		update_option( 'wcifd-current-user', get_current_user_id() );
	}

	if ( isset( $_POST['hidden-display-producer'] ) ) {
		$display_producer = ( isset( $_POST['wcifd-display-producer'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wcifd-display-producer'] ) ) : 0;
		update_option( 'wcifd-display-producer', $display_producer );
	}

	if ( isset( $_POST['hidden-display-supplier'] ) ) {
		$display_supplier = ( isset( $_POST['wcifd-display-supplier'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wcifd-display-supplier'] ) ) : 0;
		update_option( 'wcifd-display-supplier', $display_supplier );
	}

	if ( isset( $_POST['hidden-display-sup-product-code'] ) ) {
		$display_sup_product_code = ( isset( $_POST['wcifd-display-sup-product-code'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wcifd-display-sup-product-code'] ) ) : 0;
		update_option( 'wcifd-display-sup-product-code', $display_sup_product_code );
	}

	if ( isset( $_POST['regular-price-list'] ) ) {
		$regular_price_list = sanitize_text_field( wp_unslash( $_POST['regular-price-list'] ) );
		update_option( 'wcifd-regular-price-list', $regular_price_list );
	}

	if ( isset( $_POST['sale-price-list'] ) ) {
		$sale_price_list = sanitize_text_field( wp_unslash( $_POST['sale-price-list'] ) );
		update_option( 'wcifd-sale-price-list', $sale_price_list );
	}

	if ( isset( $_POST['wcifd-size-type'] ) ) {
		$size_type = sanitize_text_field( wp_unslash( $_POST['wcifd-size-type'] ) );
		update_option( 'wcifd-size-type', $size_type );
	}

	if ( isset( $_POST['wcifd-weight-type'] ) ) {
		$weight_type = sanitize_text_field( wp_unslash( $_POST['wcifd-weight-type'] ) );
		update_option( 'wcifd-weight-type', $weight_type );
	}

	if ( isset( $_POST['notes-as-description'] ) ) {
		$notes_as_description = sanitize_text_field( wp_unslash( $_POST['notes-as-description'] ) );
		update_option( 'wcifd-notes-as-description', $notes_as_description );
	}

	if ( isset( $_POST['short-description'] ) ) {
		$short_description = sanitize_text_field( wp_unslash( $_POST['short-description'] ) );
		update_option( 'wcifd-short-description', $short_description );
	}

	if ( isset( $_POST['exclude-description'] ) ) {
		$exclude_description = sanitize_text_field( wp_unslash( $_POST['exclude-description'] ) );
		update_option( 'wcifd-exclude-description', $exclude_description );
	}

	if ( isset( $_POST['exclude-title'] ) ) {
		$exclude_title = sanitize_text_field( wp_unslash( $_POST['exclude-title'] ) );
		update_option( 'wcifd-exclude-title', $exclude_title );
	}

	if ( isset( $_POST['exclude-url'] ) ) {
		$exclude_url = sanitize_text_field( wp_unslash( $_POST['exclude-url'] ) );
		update_option( 'wcifd-exclude-url', $exclude_url );
	}

	if ( isset( $_POST['deleting-categories'] ) ) {
		$deleting_categories = sanitize_text_field( wp_unslash( $_POST['deleting-categories'] ) );
		update_option( 'wcifd-deleting-categories', $deleting_categories );
	}

	if ( isset( $_POST['deleted-products'] ) ) {
		$deleted_products = sanitize_text_field( wp_unslash( $_POST['deleted-products'] ) );
		update_option( 'wcifd-deleted-products', $deleted_products );
	}

	if ( isset( $_POST['replace-products'] ) ) {
		$replace_products = sanitize_text_field( wp_unslash( $_POST['replace-products'] ) );
		update_option( 'wcifd-replace-products', $replace_products );
	}

	if ( isset( $_POST['products-variations-prices'] ) ) {
		$products_variations_prices = sanitize_text_field( wp_unslash( $_POST['products-variations-prices'] ) );
		update_option( 'wcifd-products-variations-prices', $products_variations_prices );
	}

	if ( isset( $_POST['products-not-available'] ) ) {
		$products_not_available = sanitize_text_field( wp_unslash( $_POST['products-not-available'] ) );
		update_option( 'wcifd-products-not-available', $products_not_available );
	}

	if ( isset( $_POST['publish-new-products'] ) ) {
		$publish_new_products = sanitize_text_field( wp_unslash( $_POST['publish-new-products'] ) );
		update_option( 'wcifd-publish-new-products', $publish_new_products );
	}
}
?>

<div id="wcifd-products" class="wcifd-admin">

	<ul class="subsubsub wcifd">
		<li><a class="current" data-link="wcifd-products-general"><?php esc_html_e( 'General', 'wc-importer-for-danea' ); ?></a> | </li>
		<li><a data-link="wcifd-products-fields"><?php esc_html_e( 'Custom fields', 'wc-importer-for-danea' ); ?></a> | </li>
		<li><a data-link="wcifd-products-remote"><?php esc_html_e( 'Remote', 'wc-importer-for-danea' ); ?></a> | </li>
		<li><a data-link="wcifd-products-file"><?php esc_html_e( 'Import file', 'wc-importer-for-danea' ); ?></a></li>
	</ul>

	<div class="clear"></div>

	<?php
	require 'wcifd-products-general-template.php';
	require 'wcifd-products-custom-fields-template.php';
	require 'wcifd-products-remote-template.php';
	require 'wcifd-products-file-template.php';
	?>

</div>

