<?php
/**
 * Importazione prodotti
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.3.1
 */

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
						<option value="1"><?php esc_html_e( 'Yes, I will import prices inclusive of tax', 'wc-importer-for-danea' ); ?></option>
						<option value="0"><?php esc_html_e( 'No, I will import prices exclusive of tax', 'wc-importer-for-danea' ); ?></option>
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
							echo '<option value="' . esc_attr( $n ) . '">' . esc_html__( 'Price list ', 'wc-importer-for-danea' ) . esc_html( $n ) . '</option>';
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
							echo '<option value="' . esc_attr( $n ) . '">' . esc_html__( 'Price list ', 'wc-importer-for-danea' ) . esc_html( $n ) . '</option>';
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
						<option value="gross-size"><?php esc_html_e( 'Gross size', 'wc-importer-for-danea' ); ?></option>
						<option value="net-size"><?php esc_html_e( 'Net size', 'wc-importer-for-danea' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Chose if import gross or net product size.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Product weight type', 'wc-importer-for-danea' ); ?></th>
				<td>
					<select name="wcifd-weight-type" class="wcifd-select">
						<option value="gross-weight"><?php esc_html_e( 'Gross weight', 'wc-importer-for-danea' ); ?></option>
						<option value="net-weight"><?php esc_html_e( 'Net weight', 'wc-importer-for-danea' ); ?></option>
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
						<option value="excerpt"><?php esc_html_e( 'Use part of the full description', 'wc-importer-for-danea' ); ?></option>
						<option value="notes"><?php esc_html_e( 'Use the content of the Note field', 'wc-importer-for-danea' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select the content to use for the short description of the product.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Notes as description', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="notes-as-description" value="0">
					<input type="checkbox" name="notes-as-description" value="1">
					<p class="description"><?php esc_html_e( 'Use the Notes field content if HTML description is empty.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product description', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="exclude-description" value="0">
					<input type="checkbox" name="exclude-description" value="1">
					<p class="description"><?php esc_html_e( 'Exclude descriptions from products updates.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product title', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="exclude-title" value="0">
					<input type="checkbox" name="exclude-title" value="1">
					<p class="description"><?php esc_html_e( 'Exclude title from products updates.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product URL', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="exclude-url" value="0">
					<input type="checkbox" name="exclude-url" value="1">
					<p class="description"><?php esc_html_e( 'Exclude URL from products updates.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Categories', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="deleting-categories" value="0">
					<input type="checkbox" name="deleting-categories" value="1">
					<p class="description"><?php esc_html_e( 'Avoid deleting categories during synchronizations.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Deleted products', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="deleted-products" value="0">
					<input type="checkbox" name="deleted-products" value="1">
					<p class="description"><?php esc_html_e( 'Avoid updating products in trash.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Replace products', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="replace-products" value="0">
					<input type="checkbox" name="replace-products" value="1">
					<p class="description"><?php esc_html_e( 'Replace all WC products with a full update coming from Danea Easyfatt ', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Variations prices', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="products-variations-prices" value="0">
					<input type="checkbox" name="products-variations-prices" value="1">
					<p class="description"><?php esc_html_e( 'Exclude variations prices from products updates.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Products not available', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="products-not-available" value="0">
					<input type="checkbox" name="products-not-available" value="1">
					<p class="description"><?php esc_html_e( 'Avoid creating new products if not available in stock.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier as author', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-use-suppliers" value="0">
					<input type="checkbox" name="wcifd-use-suppliers" value="1">
					<p class="description"><?php esc_html_e( 'Use the product supplier as post author.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Producer', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-producer" value="0">
					<input type="checkbox" name="wcifd-display-producer" value="1">
					<p class="description"><?php esc_html_e( 'Display the producer to the user', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-supplier" value="0">
					<input type="checkbox" name="wcifd-display-supplier" value="1">
					<p class="description"><?php esc_html_e( 'Display the supplier to the user.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier product code', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-sup-product-code" value="0">
					<input type="checkbox" name="wcifd-display-sup-product-code" value="1">
					<p class="description"><?php esc_html_e( 'Display the Supplier product code to the user.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Publish new products', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="publish-new-products" value="0">
					<input type="checkbox" name="publish-new-products" value="1">
					<p class="description"><?php esc_html_e( 'Publish new products directly.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><?php go_premium(); ?></td>
			</tr>
		</table>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>" disabled>
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

				echo '<tr class="one-of wcifd-custom-field">';

					/* Translators: Il numero del campo personalizzato */
					echo '<th scope="row">' . sprintf( esc_html__( 'Custom Field %d', 'wc-importer-for-danea' ), intval( $i ) ) . '</th>';
					echo '<td>';

						echo '<div class="field-import">';
							echo '<input type="hidden" name="import-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<select name="import-custom-field-' . esc_attr( $i ) . '" class="wcifd-select">';
								echo '<option value="">' . esc_html__( 'Don\'t import', 'wc-importer-for-danea' ) . '</option>';
								echo '<option value="attribute">' . esc_html__( 'Attribute', 'wc-importer-for-danea' ) . '</option>';
								echo '<option value="tag">' . esc_html__( 'Tag', 'wc-importer-for-danea' ) . '</option>';
							echo '</select>';

							/* Translators: Il numero del campo personalizzato */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Import Danea Custom Field %d', 'wc-importer-for-danea' ), intval( $i ) ) . '</p>';
						echo '</div>';

						echo '<div class="field-tag-append">';
							echo '<input type="hidden" name="custom-field-tag-append-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="custom-field-tag-append-' . esc_attr( $i ) . '" value="1">';
							echo '<p class="description bottom">' . esc_html__( 'Add to other product tags present', 'wc-importer-for-danea' ) . '</p>';
						echo '</div>';

						echo '<div class="field-split">';
							echo '<input type="hidden" name="split-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="split-custom-field-' . esc_attr( $i ) . '" value="1">';
							echo '<p class="description bottom">' . esc_html__( 'Create multiple attributes/tags using comma as separator', 'wc-importer-for-danea' ) . '</p>';
						echo '</div>';

						echo '<div class="field-display">';
							echo '<input type="hidden" name="display-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="display-custom-field-' . esc_attr( $i ) . '" value="1">';

							/* Translators: Il numero del campo personalizzato */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Make Custom Field %d visible in front-end', 'wc-importer-for-danea' ), intval( $i ) ) . '</p>';
						echo '</div>';

						echo '<div class="field-name">';
							echo '<input type="text" class="custom-field-name" name="custom-field-name-' . esc_attr( $i ) . '" value="" placeholder="' . esc_html__( 'My custom field', 'wc-importer-for-danea' ) . '">';

							/* Translators: Il numero del campo personalizzato */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Add a name to Custom Field %d', 'wc-importer-for-danea' ), intval( $i ) ) . '</p>';
						echo '</div>';

					echo '</td>';
				echo '</tr>';

			}
			?>
			<tr>
				<th></th>
				<td><?php go_premium(); ?></td>
			</tr>
		</table>
		<?php wp_nonce_field( 'wcifd-products-fields', 'wcifd-products-fields-nonce' ); ?>
		<input type="hidden" name="wcifd-custom-fields-hidden" value="1">
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_attr_e( 'Save Changes', 'wc-importer-for-danea' ); ?>" disabled>
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

			<?php $receive_orders_url = __( 'Please insert your <strong>Premium Key</strong>', 'wc-importer-for-danea' ); ?>

			<tr>
				<th scope="row"><?php esc_html_e( 'URL', 'wc-importer-for-danea' ); ?></th>
				<td>
					<div class="wcifd-copy-url"><span class="wcifd-red"><?php echo wp_kses_post( $receive_orders_url ); ?></span></div>
					<p class="description"><?php esc_html_e( 'Add this URL to the Settings tab of the Products update function in Danea.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Import images', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-receive-images" value="1">
					<input type="checkbox" class="wcifd-import-images" name="wcifd-import-images" value="1">
					<?php esc_html_e( 'Import products images from Danea.', 'wc-importer-for-danea' ); ?>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><?php go_premium(); ?></td>
			</tr>
		</table>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>" disabled>
	</form>

</div>

<!--Form Prodotti - File upload-->
<div id="wcifd-products-file" class="wcifd-products-sub">

	<form name="wcifd-products-import" id="wcifd-products-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

		<h2 class="title"><?php esc_html_e( 'Import products from a file', 'wc-importer-for-danea' ); ?></h2>

		<table class="form-table">
			<tr>
				<th scoper="row"><?php esc_html_e( 'File type', 'wc-importer-for-danea' ); ?></th>
				<td>
					<select name="file-type" class="wcifd-select">
							<option value="xml" ><?php esc_html_e( 'xml', 'wc-importer-for-danea' ); ?></option>
							<option value="csv" ><?php esc_html_e( 'csv', 'wc-importer-for-danea' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select the file type to be imported', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<input type="hidden" name="products-import" value="1">
			<tr>
				<th scope="row"><?php esc_html_e( 'Add products', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="file" name="products-list" disabled>
					<p class="description"><?php esc_html_e( 'Select your products list file', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><?php go_premium(); ?></td>
			</tr>
		</table>
		<input type="submit" class="button-primary" value="<?php esc_html_e( 'Import Products', 'wc-importer-for-danea' ); ?>" disabled>
	</form>

</div>
