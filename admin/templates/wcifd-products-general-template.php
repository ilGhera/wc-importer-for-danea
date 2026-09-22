<?php
/**
 * Products template - General
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.4.0
 */

defined( 'ABSPATH' ) || exit;
?>

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
					<p class="description"><?php esc_html_e( 'The Danea price list to use for WooCommerce regular price.', 'wc-importer-for-danea' ); ?></p>
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
					<p class="description"><?php esc_html_e( 'The Danea price list to use for WooCommerce sale price.', 'wc-importer-for-danea' ); ?></p>
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
				<th scope="row"><?php esc_html_e( 'Import EAN barcode', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-import-ean" value="0">
					<input type="checkbox" name="wcifd-import-ean" value="1"<?php echo 1 === intval( $import_ean ) ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Import the barcode field from Danea as GTIN/EAN in WooCommerce (only valid numeric codes).', 'wc-importer-for-danea' ); ?></p>
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
            <tr>
                <th></th>
                <td><?php WCIFD_Admin::go_premium(); ?></td>
            </tr>
		</table>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>" disabled>
	</form>

</div>
