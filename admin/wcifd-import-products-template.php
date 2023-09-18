<?php
/**
 * Importazione prodotti
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.6.1
 */

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

<ul class="subsubsub wcifd">
	<li><a class="current" data-link="wcifd-products-general"><?php esc_html_e( 'General', 'wc-importer-for-danea' ); ?></a> | </li>
	<li><a data-link="wcifd-products-fields"><?php esc_html_e( 'Custom fields', 'wc-importer-for-danea' ); ?></a> | </li>
	<li><a data-link="wcifd-products-remote"><?php esc_html_e( 'Remote', 'wc-importer-for-danea' ); ?></a> | </li>
	<li><a data-link="wcifd-products-file"><?php esc_html_e( 'Import file', 'wc-importer-for-danea' ); ?></a></li>
</ul>

<div class="clear"></div>

<!--Form Prodotti - Generali-->
<div id="wcifd-products-general" class="wcifd-products-sub" style="display: block;">

	<form name="wcifd-products-settings" class="wcifd-form" method="post" action="">

		<h2 class="title"><?php esc_html_e( 'Generall settings', 'wc-importer-for-danea' ); ?></h2>

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Prices imported with tax', 'wc-importer-for-danea' ); ?></th>
				<td>
					<select name="tax-included" class="wcifd-select">
						<option value="1" <?php echo( 1 === intval( $tax_included ) ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Yes, I will import prices inclusive of tax', 'wc-importer-for-danea' ); ?></option>
						<option value="0" <?php echo( 0 === intval( $tax_included ) ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'No, I will import prices exclusive of tax', 'wc-importer-for-danea' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'In Danea you can choose if export prices with tax included or not. What are you going to import?', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Regular price', 'wc-importer-for-danea' ); ?></th>
				<td>
					<select name="regular-price-list" class="wcifd-select">
						<?php
						for ( $n = 1; $n <= 9; $n++ ) {
							echo '<option value="' . esc_attr( $n ) . '"' . ( intval( $regular_price_list ) === $n ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Price list ', 'wc-importer-for-danea' ) . intval( $n ) . '</option>';
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'The Danea price list to use for Woocommerce regular price.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sale price', 'wc-importer-for-danea' ); ?></th>
				<td>
					<select name="sale-price-list" class="wcifd-select">
						<?php
						echo '<option>' . esc_html__( 'Select a price list', 'wc-importer-for-danea' ) . '</option>';
						for ( $n = 1; $n <= 9; $n++ ) {
							echo '<option value="' . esc_attr( $n ) . '"' . ( intval( $sale_price_list ) === $n ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Price list ', 'wc-importer-for-danea' ) . intval( $n ) . '</option>';
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'The Danea price list to use for Woocommerce sale price.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Product size type', 'wc-importer-for-danea' ); ?></th>
				<td>
					<select name="wcifd-size-type" class="wcifd-select">
						<option value="gross-size"<?php echo( 'gross-size' === $size_type ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Gross size', 'wc-importer-for-danea' ); ?></option>
						<option value="net-size"<?php echo( 'net-size' === $size_type ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Net size', 'wc-importer-for-danea' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Chose if import gross or net product size.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Product weight type', 'wc-importer-for-danea' ); ?></th>
				<td>
					<select name="wcifd-weight-type" class="wcifd-select">
						<option value="gross-weight"<?php echo( 'gross-weight' === $weight_type ) ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Gross weight', 'wc-importer-for-danea' ); ?></option>
						<option value="net-weight"<?php echo( 'net-weight' === $weight_type ) ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Net weight', 'wc-importer-for-danea' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Chose if import gross or net product weight.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Short description', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="short-description" value="0">
					<select name="short-description" class="wcifd-select">
						<option value=""><?php esc_html_e( 'None', 'wc-importer-for-danea' ); ?></option>
						<option value="excerpt"<?php echo ( 'excerpt' === $short_description ) ? ' selected' : null; ?>><?php esc_html_e( 'Use part of the full description', 'wc-importer-for-danea' ); ?></option>
						<option value="notes"<?php echo ( 'notes' === $short_description ) ? ' selected' : null; ?>><?php esc_html_e( 'Use the content of the Note field', 'wc-importer-for-danea' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select the content to use for the short description of the product.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Notes as description', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="notes-as-description" value="0">
					<input type="checkbox" name="notes-as-description" value="1"<?php echo 1 === intval( $notes_as_description ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Use the Notes field content if HTML description is empty.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product description', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="exclude-description" value="0">
					<input type="checkbox" name="exclude-description" value="1"<?php echo 1 === intval( $exclude_description ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Exclude descriptions from products updates.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product title', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="exclude-title" value="0">
					<input type="checkbox" name="exclude-title" value="1"<?php echo 1 === intval( $exclude_title ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Exclude title from products updates.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product URL', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="exclude-url" value="0">
					<input type="checkbox" name="exclude-url" value="1"<?php echo 1 === intval( $exclude_url ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Exclude URL from products updates.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Categories', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="deleting-categories" value="0">
					<input type="checkbox" name="deleting-categories" value="1"<?php echo 1 === intval( $deleting_categories ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Avoid deleting categories during synchronizations.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Deleted products', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="deleted-products" value="0">
					<input type="checkbox" name="deleted-products" value="1"<?php echo 1 === intval( $deleted_products ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Avoid updating products in trash.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Replace products', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="replace-products" value="0">
					<input type="checkbox" name="replace-products" value="1"<?php echo 1 === intval( $replace_products ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Replace all WC products with a full update coming from Danea Easyfatt ', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Variations prices', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="products-variations-prices" value="0">
					<input type="checkbox" name="products-variations-prices" value="1"<?php echo 1 === intval( $products_variations_prices ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Exclude variations prices from products updates.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Products not available', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="products-not-available" value="0">
					<input type="checkbox" name="products-not-available" value="1"<?php echo 1 === intval( $products_not_available ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Avoid creating new products if not available in stock.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier as author', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-use-suppliers" value="0">
					<input type="checkbox" name="wcifd-use-suppliers" value="1"<?php echo 1 === intval( $use_suppliers ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Use the product supplier as post author.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Producer', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-producer" value="0">
					<input type="checkbox" name="wcifd-display-producer" value="1"<?php echo 1 === intval( $display_producer ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Display the producer to the user.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-supplier" value="0">
					<input type="checkbox" name="wcifd-display-supplier" value="1"<?php echo 1 === intval( $display_supplier ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Display the supplier to the user.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier product code', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-sup-product-code" value="0">
					<input type="checkbox" name="wcifd-display-sup-product-code" value="1"<?php echo 1 === intval( $display_sup_product_code ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Display the Supplier product code to the user.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Publish new products', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="publish-new-products" value="0">
					<input type="checkbox" name="publish-new-products" value="1"<?php echo 1 === intval( $publish_new_products ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Publish new products directly.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'wcifd-products-general', 'wcifd-products-general-nonce' ); ?>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>">
	</form>

</div>

<!--Form Prodotti - Custom fields-->
<div id="wcifd-products-fields" class="wcifd-products-sub">

	<form name="wcifd-products-fields" class="wcifd-form" method="post" action="">

		<h2 class="title"><?php esc_html_e( 'Import Danea Custom Fields', 'wc-importer-for-danea' ); ?></h2>

		<table class="form-table">

			<?php

			$custom_fields = get_option( 'wcifd-custom-fields' ) ? get_option( 'wcifd-custom-fields' ) : array();

			for ( $i = 1; $i < 5; $i++ ) {

				$import_field  = isset( $custom_fields[ $i ]['import'] ) ? $custom_fields[ $i ]['import'] : 0;
				$tag_append    = isset( $custom_fields[ $i ]['append'] ) ? $custom_fields[ $i ]['append'] : 0;
				$split_field   = isset( $custom_fields[ $i ]['split'] ) ? $custom_fields[ $i ]['split'] : 0;
				$display_field = isset( $custom_fields[ $i ]['display'] ) ? $custom_fields[ $i ]['display'] : 0;
				$field_name    = isset( $custom_fields[ $i ]['name'] ) ? $custom_fields[ $i ]['name'] : '';

				if ( isset( $_POST['wcifd-custom-fields-hidden'], $_POST['wcifd-products-fields-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-products-fields-nonce'] ) ), 'wcifd-products-fields' ) ) {

					if ( isset( $_POST[ 'import-custom-field-' . $i ] ) ) {

						$import_field                  = sanitize_text_field( wp_unslash( $_POST[ 'import-custom-field-' . $i ] ) );
						$custom_fields[ $i ]['import'] = $import_field;

					}

					if ( isset( $_POST[ 'custom-field-tag-append-' . $i ] ) ) {

						$tag_append                    = sanitize_text_field( wp_unslash( $_POST[ 'custom-field-tag-append-' . $i ] ) );
						$custom_fields[ $i ]['append'] = $tag_append;

					}

					if ( isset( $_POST[ 'split-custom-field-' . $i ] ) ) {

						$split_field                  = sanitize_text_field( wp_unslash( $_POST[ 'split-custom-field-' . $i ] ) );
						$custom_fields[ $i ]['split'] = $split_field;

					}

					if ( isset( $_POST[ 'display-custom-field-' . $i ] ) ) {

						$display_field                  = sanitize_text_field( wp_unslash( $_POST[ 'display-custom-field-' . $i ] ) );
						$custom_fields[ $i ]['display'] = $display_field;

					}

					if ( isset( $_POST[ 'custom-field-name-' . $i ] ) ) {

						$field_name                  = sanitize_text_field( wp_unslash( $_POST[ 'custom-field-name-' . $i ] ) );
						$custom_fields[ $i ]['name'] = $field_name;

					}

					update_option( 'wcifd-custom-fields', $custom_fields );

				}

				echo '<tr class="one-of wcifd-custom-field">';

					/* Translators: il numero di campo personalizzato */
					echo '<th scope="row">' . sprintf( esc_html__( 'Custom Field %d', 'wc-importer-for-danea' ), intval( $i ) ) . '</th>';
					echo '<td>';

						echo '<div class="field-import">';
							echo '<input type="hidden" name="import-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<select name="import-custom-field-' . esc_attr( $i ) . '" class="wcifd-select">';
								echo '<option value="">' . esc_html__( 'Don\'t import', 'wc-importer-for-danea' ) . '</option>';
								echo '<option value="attribute"' . ( 'attribute' === $import_field ? ' selected' : null ) . '>' . esc_html__( 'Attribute', 'wc-importer-for-danea' ) . '</option>';
								echo '<option value="tag"' . ( 'tag' === $import_field ? ' selected' : null ) . '>' . esc_html__( 'Tag', 'wc-importer-for-danea' ) . '</option>';
							echo '</select>';

							/* Translators: il numero di campo personalizzato */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Import Danea Custom Field %d', 'wc-importer-for-danea' ), intval( $i ) ) . '</p>';
						echo '</div>';

						echo '<div class="field-tag-append">';
							echo '<input type="hidden" name="custom-field-tag-append-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="custom-field-tag-append-' . esc_attr( $i ) . '" value="1"' . ( 1 === intval( $tag_append ) ? ' checked="checked"' : '' ) . '>';
							echo '<p class="description bottom">' . esc_html__( 'Add to other product tags present', 'wc-importer-for-danea' ) . '</p>';
						echo '</div>';

						echo '<div class="field-split">';
							echo '<input type="hidden" name="split-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="split-custom-field-' . esc_attr( $i ) . '" value="1"' . ( 1 === intval( $split_field ) ? ' checked="checked"' : '' ) . '>';
							echo '<p class="description bottom">' . esc_html__( 'Create multiple attributes/tags using comma as separator', 'wc-importer-for-danea' ) . '</p>';
						echo '</div>';

						echo '<div class="field-display">';
							echo '<input type="hidden" name="display-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="display-custom-field-' . esc_attr( $i ) . '" value="1"' . ( 1 === intval( $display_field ) ? ' checked="checked"' : '' ) . '>';

							/* Translators: il numero di campo personalizzato */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Make Custom Field %d visible in front-end', 'wc-importer-for-danea' ), intval( $i ) ) . '</p>';
						echo '</div>';

						echo '<div class="field-name">';
							echo '<input type="text" class="custom-field-name" name="custom-field-name-' . esc_attr( $i ) . '" value="' . esc_attr( $field_name ) . '" placeholder="' . esc_html__( 'My custom field', 'wc-importer-for-danea' ) . '">';

							/* Translators: il numero di campo personalizzato */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Add a name to Custom Field %d', 'wc-importer-for-danea' ), intval( $i ) ) . '</p>';
						echo '</div>';

					echo '</td>';
				echo '</tr>';

			}
			?>

		</table>
		<?php wp_nonce_field( 'wcifd-products-fields', 'wcifd-products-fields-nonce' ); ?>
		<input type="hidden" name="wcifd-custom-fields-hidden" value="1">
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>">
	</form>

</div>

<!--Form Prodotti - Remote-->
<div id="wcifd-products-remote" class="wcifd-products-sub">

	<form name="wcifd-receive-products" id="wcifd-receive-products" class="wcifd-form" method="post" action="">

		<h2 class="title"><?php esc_html_e( 'Receive products from Danea', 'wc-importer-for-danea' ); ?></h2>

		<p>
			<?php
			esc_html_e( 'Receive products directly from the XML sent by Danea via HTTP post.', 'wc-importer-for-danea' ) . '<br>';
			?>
		</p>

		<table class="form-table">
			<?php
			$premium_key = strtolower( get_option( 'wcifd-premium-key' ) );
			$url_code    = get_option( 'wcifd-url-code' );
			if ( ! $url_code ) {
				$url_code = wcifd_rand_md5( 6 );
				add_option( 'wcifd-url-code', $url_code );
			}

			$receive_orders_url = __( 'Please insert your <strong>Premium Key</strong>', 'wc-importer-for-danea' );
			if ( $premium_key ) {
				$receive_orders_url = home_url() . '?key=' . $premium_key . '&code=' . $url_code . '&mode=data';
			}

			$import_images = get_option( 'wcifd-import-images' );

			if ( isset( $_POST['hidden-receive-images'], $_POST['wcifd-products-remote-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-products-remote-nonce'] ) ), 'wcifd-products-remote' ) ) {
				$import_images = ( isset( $_POST['wcifd-import-images'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wcifd-import-images'] ) ) : 0;
				update_option( 'wcifd-import-images', $import_images );
			}
			?>

			<tr>
				<th scope="row"><?php esc_html_e( 'URL', 'wc-importer-for-danea' ); ?></th>
				<td>
					<div class="wcifd-copy-url"><span<?php echo( ! $premium_key ? ' class="wcifd-red"' : '' ); ?>><?php echo wp_kses_post( $receive_orders_url ); ?></span></div>
					<p class="description"><?php esc_html_e( 'Add this URL to the Settings tab of the Products update function in Danea.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Import images', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-receive-images" value="1">
					<input type="checkbox" class="wcifd-import-images" name="wcifd-import-images" value="1" <?php echo( 1 === intval( $import_images ) ? 'checked="checked"' : '' ); ?>>
					<?php esc_html_e( 'Import products images from Danea.', 'wc-importer-for-danea' ); ?>
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'wcifd-products-remote', 'wcifd-products-remote-nonce' ); ?>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>">
	</form>

</div>

<!--Form Prodotti - File upload-->
<div id="wcifd-products-file" class="wcifd-products-sub">

	<form name="wcifd-products-import" id="wcifd-products-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

		<?php
			$file_type = get_option( 'wcifd-file-type' );

		if ( isset( $_POST['file-type'], $_POST['wcifd-products-file-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-products-file-nonce'] ) ), 'wcifd-products-file' ) ) {

			$file_type = sanitize_text_field( wp_unslash( $_POST['file-type'] ) );

		}
		?>


		<h2 class="title"><?php esc_html_e( 'Import products from a file', 'wc-importer-for-danea' ); ?></h2>

		<table class="form-table">
			<tr>
				<th scoper="row"><?php esc_html_e( 'File type', 'wc-importer-for-danea' ); ?></th>
				<td>
					<select name="file-type" class="wcifd-select">
							<option value="xml" <?php echo( 'xml' === $file_type ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'XML', 'wc-importer-for-danea' ); ?></option>
							<option value="csv" <?php echo( 'csv' === $file_type ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'CSV', 'wc-importer-for-danea' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select the file type to be imported', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<input type="hidden" name="products-import" value="1">
			<tr>
				<th scope="row"><?php esc_html_e( 'Add products', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="file" name="products-list">
					<p class="description"><?php esc_html_e( 'Select your products list file', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'wcifd-products-file', 'wcifd-products-file-nonce' ); ?>
		<input type="submit" class="button-primary" value="<?php esc_html_e( 'Import Products', 'wc-importer-for-danea' ); ?>">
	</form>

</div>

<?php wcifd_products(); ?>
