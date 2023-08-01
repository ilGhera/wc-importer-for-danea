<?php
/**
 * Importazione prodotti
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @since 1.3.0
 */

?>

<ul class="subsubsub wcifd">
	<li><a class="current" data-link="wcifd-products-general"><?php esc_html_e( 'General', 'wcifd' ); ?></a> | </li>
	<li><a data-link="wcifd-products-fields"><?php esc_html_e( 'Custom fields', 'wcifd' ); ?></a> | </li>
	<li><a data-link="wcifd-products-remote"><?php esc_html_e( 'Remote', 'wcifd' ); ?></a> | </li>
	<li><a data-link="wcifd-products-file"><?php esc_html_e( 'Import file', 'wcifd' ); ?></a></li>
</ul>

<div class="clear"></div>

<!--Form Prodotti - Generali-->
<div id="wcifd-products-general" class="wcifd-products-sub" style="display: block;">

	<form name="wcifd-products-settings" class="wcifd-form" method="post" action="">

		<h2 class="title"><?php esc_html_e( 'Generall settings', 'wcifd' ); ?></h2>

		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Prices imported with tax', 'wcifd' ); ?></th>
				<td>
					<select name="tax-included" class="wcifd-select">
						<option value="1"><?php esc_html_e( 'Yes, I will import prices inclusive of tax', 'wcifd' ); ?></option>
						<option value="0"><?php esc_html_e( 'No, I will import prices exclusive of tax', 'wcifd' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'In Danea you can choose if export prices with tax included or not. What are you going to import?', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Regular price', 'wcifd' ); ?></th>
				<td>
					<select name="regular-price-list" class="wcifd-select">
						<?php
						for ( $n = 1; $n <= 9; $n++ ) {
							echo '<option value="' . esc_attr( $n ) . '">' . esc_html__( 'Price list ', 'wcifd' ) . esc_html( $n ) . '</option>';
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'The Danea price list to use for Woocommerce regular price.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sale price', 'wcifd' ); ?></th>
				<td>
					<select name="sale-price-list" class="wcifd-select">
						<?php
						echo '<option>' . esc_html__( 'Select a price list', 'wcifd' ) . '</option>';
						for ( $n = 1; $n <= 9; $n++ ) {
							echo '<option value="' . esc_attr( $n ) . '">' . esc_html__( 'Price list ', 'wcifd' ) . esc_html( $n ) . '</option>';
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'The Danea price list to use for Woocommerce sale price.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Product size type', 'wcifd' ); ?></th>
				<td>
					<select name="wcifd-size-type" class="wcifd-select">
						<option value="gross-size"><?php esc_html_e( 'Gross size', 'wcifd' ); ?></option>
						<option value="net-size"><?php esc_html_e( 'Net size', 'wcifd' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Chose if import gross or net product size.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Product weight type', 'wcifd' ); ?></th>
				<td>
					<select name="wcifd-weight-type" class="wcifd-select">
						<option value="gross-weight"><?php esc_html_e( 'Gross weight', 'wcifd' ); ?></option>
						<option value="net-weight"><?php esc_html_e( 'Net weight', 'wcifd' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Chose if import gross or net product weight.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Short description', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="short-description" value="0">
					<select name="short-description" class="wcifd-select" disabled>
						<option value=""><?php esc_html_e( 'None', 'wcifd' ); ?></option>
						<option value="excerpt"><?php esc_html_e( 'Use part of the full description', 'wcifd' ); ?></option>
						<option value="notes"><?php esc_html_e( 'Use the content of the Note field', 'wcifd' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select the content to use for the short description of the product.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Notes as description', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="notes-as-description" value="0">
					<input type="checkbox" name="notes-as-description" value="1">
					<p class="description"><?php esc_html_e( 'Use the Notes field content if HTML description is empty.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product description', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="exclude-description" value="0">
					<input type="checkbox" name="exclude-description" value="1">
					<p class="description"><?php esc_html_e( 'Exclude descriptions from products updates.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product title', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="exclude-title" value="0">
					<input type="checkbox" name="exclude-title" value="1">
					<p class="description"><?php esc_html_e( 'Exclude title from products updates.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product URL', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="exclude-url" value="0">
					<input type="checkbox" name="exclude-url" value="1">
					<p class="description"><?php esc_html_e( 'Exclude URL from products updates.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Categories', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="deleting-categories" value="0">
					<input type="checkbox" name="deleting-categories" value="1">
					<p class="description"><?php esc_html_e( 'Avoid deleting categories during synchronizations.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Deleted products', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="deleted-products" value="0">
					<input type="checkbox" name="deleted-products" value="1">
					<p class="description"><?php esc_html_e( 'Avoid updating products in trash.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Replace products', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="replace-products" value="0">
					<input type="checkbox" name="replace-products" value="1">
					<p class="description"><?php esc_html_e( 'Replace all WC products with a full update coming from Danea Easyfatt ', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Variations prices', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="products-variations-prices" value="0">
					<input type="checkbox" name="products-variations-prices" value="1">
					<p class="description"><?php esc_html_e( 'Exclude variations prices from products updates.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Products not available', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="products-not-available" value="0">
					<input type="checkbox" name="products-not-available" value="1">
					<p class="description"><?php esc_html_e( 'Avoid creating new products if not available in stock.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier as author', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="hidden-use-suppliers" value="0">
					<input type="checkbox" name="wcifd-use-suppliers" value="1">
					<p class="description"><?php esc_html_e( 'Use the product supplier as post author.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Producer', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-producer" value="0">
					<input type="checkbox" name="wcifd-display-producer" value="1">
					<p class="description"><?php esc_html_e( 'Display the producer to the user', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-supplier" value="0">
					<input type="checkbox" name="wcifd-display-supplier" value="1">
					<p class="description"><?php esc_html_e( 'Display the supplier to the user.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier product code', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-sup-product-code" value="0">
					<input type="checkbox" name="wcifd-display-sup-product-code" value="1">
					<p class="description"><?php esc_html_e( 'Display the Supplier product code to the user.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Publish new products', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="publish-new-products" value="0">
					<input type="checkbox" name="publish-new-products" value="1">
					<p class="description"><?php esc_html_e( 'Publish new products directly.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><?php go_premium(); ?></td>
			</tr>
		</table>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wcifd' ); ?>" disabled>
	</form>

</div>

<!--Form Prodotti - Custom fields-->
<div id="wcifd-products-fields" class="wcifd-products-sub">

	<form name="wcifd-products-fields" class="wcifd-form" method="post" action="">

		<h2 class="title"><?php esc_html_e( 'Import Danea Custom Fields', 'wcifd' ); ?></h2>

		<table class="form-table">

			<?php

			$custom_fields = get_option( 'wcifd-custom-fields' ) ? get_option( 'wcifd-custom-fields' ) : array();

			for ( $i = 1; $i < 5; $i++ ) {

				echo '<tr class="one-of wcifd-custom-field">';

					/* Translators: Il numero del campo personalizzato */
					echo '<th scope="row">' . sprintf( esc_html__( 'Custom Field %d', 'wcifd' ), intval( $i ) ) . '</th>';
					echo '<td>';

						echo '<div class="field-import">';
							echo '<input type="hidden" name="import-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<select name="import-custom-field-' . esc_attr( $i ) . '" class="wcifd-select">';
								echo '<option value="">' . esc_html__( 'Don\'t import', 'wcifd' ) . '</option>';
								echo '<option value="attribute">' . esc_html__( 'Attribute', 'wcifd' ) . '</option>';
								echo '<option value="tag">' . esc_html__( 'Tag', 'wcifd' ) . '</option>';
							echo '</select>';

							/* Translators: Il numero del campo personalizzato */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Import Danea Custom Field %d', 'wcifd' ), intval( $i ) ) . '</p>';
						echo '</div>';

						echo '<div class="field-tag-append">';
							echo '<input type="hidden" name="custom-field-tag-append-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="custom-field-tag-append-' . esc_attr( $i ) . '" value="1">';
							echo '<p class="description bottom">' . esc_html__( 'Add to other product tags present', 'wcifd' ) . '</p>';
						echo '</div>';

						echo '<div class="field-split">';
							echo '<input type="hidden" name="split-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="split-custom-field-' . esc_attr( $i ) . '" value="1">';
							echo '<p class="description bottom">' . esc_html__( 'Create multiple attributes/tags using comma as separator', 'wcifd' ) . '</p>';
						echo '</div>';

						echo '<div class="field-display">';
							echo '<input type="hidden" name="display-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="display-custom-field-' . esc_attr( $i ) . '" value="1">';

							/* Translators: Il numero del campo personalizzato */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Make Custom Field %d visible in front-end', 'wcifd' ), intval( $i ) ) . '</p>';
						echo '</div>';

						echo '<div class="field-name">';
							echo '<input type="text" class="custom-field-name" name="custom-field-name-' . esc_attr( $i ) . '" value="" placeholder="' . esc_html__( 'My custom field', 'wcifd' ) . '">';

							/* Translators: Il numero del campo personalizzato */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Add a name to Custom Field %d', 'wcifd' ), intval( $i ) ) . '</p>';
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
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_attr_e( 'Save Changes', 'wcifd' ); ?>" disabled>
	</form>

</div>

<!--Form Prodotti - Remote-->
<div id="wcifd-products-remote" class="wcifd-products-sub">

	<form name="wcifd-receive-products" id="wcifd-receive-products" class="wcifd-form" method="post" action="">

		<h2 class="title"><?php esc_html_e( 'Receive products from Danea', 'wcifd' ); ?></h2>

		<p>
			<?php
			esc_html_e( 'Receive products directly from the XML sent by Danea via HTTP post.', 'wcifd' ) . '<br>';
			?>
		</p>

		<table class="form-table">

			<?php $receive_orders_url = __( 'Please insert your <strong>Premium Key</strong>', 'wcifd' ); ?>

			<tr>
				<th scope="row"><?php esc_html_e( 'URL', 'wcifd' ); ?></th>
				<td>
					<div class="wcifd-copy-url"><span class="wcifd-red"><?php echo esc_html( $receive_orders_url ); ?></span></div>
					<p class="description"><?php esc_html_e( 'Add this URL to the Settings tab of the Products update function in Danea.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Import images', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="hidden-receive-images" value="1">
					<input type="checkbox" class="wcifd-import-images" name="wcifd-import-images" value="1">
					<?php esc_html_e( 'Import products images from Danea.', 'wcifd' ); ?>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><?php go_premium(); ?></td>
			</tr>
		</table>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wcifd' ); ?>" disabled>
	</form>

</div>

<!--Form Prodotti - File upload-->
<div id="wcifd-products-file" class="wcifd-products-sub">

	<form name="wcifd-products-import" id="wcifd-products-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

		<h2 class="title"><?php esc_html_e( 'Import products from a file', 'wcifd' ); ?></h2>

		<table class="form-table">
			<tr>
				<th scoper="row"><?php esc_html_e( 'File type', 'wcifd' ); ?></th>
				<td>
					<select name="file-type" class="wcifd-select">
							<option value="xml" ><?php esc_html_e( 'xml', 'wcifd' ); ?></option>
							<option value="csv" ><?php esc_html_e( 'csv', 'wcifd' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select the file type to be imported', 'wcifd' ); ?></p>
				</td>
			</tr>
			<input type="hidden" name="products-import" value="1">
			<tr>
				<th scope="row"><?php esc_html_e( 'Add products', 'wcifd' ); ?></th>
				<td>
					<input type="file" name="products-list" disabled>
					<p class="description"><?php esc_html_e( 'Select your products list file', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><?php go_premium(); ?></td>
			</tr>
		</table>
		<input type="submit" class="button-primary" value="<?php esc_html_e( 'Import Products', 'wcifd' ); ?>" disabled>
	</form>

</div>
