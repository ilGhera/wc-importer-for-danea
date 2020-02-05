<?php
/**
 * Importazione prodotti
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @since 1.2.0
 */

$tax_included = get_option( 'wcifd-tax-included' );
if ( isset( $_POST['tax-included'] ) ) {
	$tax_included = sanitize_text_field( $_POST['tax-included'] );
	update_option( 'wcifd-tax-included', $tax_included );
}

$use_suppliers = get_option( 'wcifd-use-suppliers' );
if ( isset( $_POST['hidden-use-suppliers'] ) ) {
	$use_suppliers = ( isset( $_POST['wcifd-use-suppliers'] ) ) ? $_POST['wcifd-use-suppliers'] : 0;
	update_option( 'wcifd-use-suppliers', $use_suppliers );
	update_option( 'wcifd-current-user', get_current_user_id() );
}

$display_producer = get_option( 'wcifd-display-producer' );
if ( isset( $_POST['hidden-display-producer'] ) ) {
	$display_producer = ( isset( $_POST['wcifd-display-producer'] ) ) ? $_POST['wcifd-display-producer'] : 0;
	update_option( 'wcifd-display-producer', $display_producer );
}

$regular_price_list = get_option( 'wcifd-regular-price-list' );
if ( isset( $_POST['regular-price-list'] ) ) {
	$regular_price_list = $_POST['regular-price-list'];
	update_option( 'wcifd-regular-price-list', $regular_price_list );
}

$sale_price_list = get_option( 'wcifd-sale-price-list' );
if ( isset( $_POST['sale-price-list'] ) ) {
	$sale_price_list = $_POST['sale-price-list'];
	update_option( 'wcifd-sale-price-list', $sale_price_list );
}

$size_type = get_option( 'wcifd-size-type' );
if ( isset( $_POST['wcifd-size-type'] ) ) {
	$size_type = $_POST['wcifd-size-type'];
	update_option( 'wcifd-size-type', $size_type );
}

$weight_type = get_option( 'wcifd-weight-type' );
if ( isset( $_POST['wcifd-weight-type'] ) ) {
	$weight_type = $_POST['wcifd-weight-type'];
	update_option( 'wcifd-weight-type', $weight_type );
}

$short_description = get_option( 'wcifd-short-description' );
if ( isset( $_POST['short-description'] ) ) {
	$short_description = $_POST['short-description'] ? $_POST['short-description'] : 0;
	update_option( 'wcifd-short-description', $short_description );
}

$exclude_description = get_option( 'wcifd-exclude-description' );
if ( isset( $_POST['exclude-description'] ) ) {
	$exclude_description = $_POST['exclude-description'] ? $_POST['exclude-description'] : 0;
	update_option( 'wcifd-exclude-description', $exclude_description );
}

$exclude_title = get_option( 'wcifd-exclude-title' );
if ( isset( $_POST['exclude-title'] ) ) {
	$exclude_title = $_POST['exclude-title'] ? $_POST['exclude-title'] : 0;
	update_option( 'wcifd-exclude-title', $exclude_title );
}

$deleting_categories = get_option( 'wcifd-deleting-categories' );
if ( isset( $_POST['deleting-categories'] ) ) {
	$deleting_categories = $_POST['deleting-categories'] ? $_POST['deleting-categories'] : 0;
	update_option( 'wcifd-deleting-categories', $deleting_categories );
}

$deleted_products = get_option( 'wcifd-deleted-products' );
if ( isset( $_POST['deleted-products'] ) ) {
	$deleted_products = $_POST['deleted-products'] ? $_POST['deleted-products'] : 0;
	update_option( 'wcifd-deleted-products', $deleted_products );
}

$publish_new_products = get_option( 'wcifd-publish-new-products' );
if ( isset( $_POST['publish-new-products'] ) ) {
	$publish_new_products = $_POST['publish-new-products'] ? $_POST['publish-new-products'] : 0;
	update_option( 'wcifd-publish-new-products', $publish_new_products );
}

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
					<select name="tax-included" class="wcifd">
						<option value="1" <?php echo( $tax_included == 1 ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( ' Yes, I will import prices inclusive of tax', 'wcifd' ); ?></option>
						<option value="0" <?php echo( $tax_included == 0 ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'No, I will import prices exclusive of tax', 'wcifd' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'In Danea you can choose if export prices with tax included or not. What are you going to import?', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Regular price', 'wcifd' ); ?></th>
				<td>
					<select name="regular-price-list" class="wcifd">
						<?php
						for ( $n = 1; $n <= 9; $n++ ) {
							echo '<option value="' . $n . '"' . ( $regular_price_list == $n ? 'selected="selected"' : '' ) . '>' . __( 'Price list ', 'wcifd' ) . $n . '</option>';
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'The Danea price list to use for Woocommerce regular price.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Sale price', 'wcifd' ); ?></th>
				<td>
					<select name="sale-price-list" class="wcifd">
						<?php
						echo '<option>' . __( 'Select a price list', 'wcifd' ) . '</option>';
						for ( $n = 1; $n <= 9; $n++ ) {
							echo '<option value="' . $n . '"' . ( $sale_price_list == $n ? 'selected="selected"' : '' ) . '>' . __( 'Price list ', 'wcifd' ) . $n . '</option>';
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'The Danea price list to use for Woocommerce sale price.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Product size type', 'wcifd' ); ?></th>
				<td>
					<select name="wcifd-size-type" class="wcifd">
						<option value="gross-size"<?php echo( $size_type == 'gross-size' ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Gross size', 'wcifd' ); ?></option>
						<option value="net-size"<?php echo( $size_type == 'net-size' ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'Net size', 'wcifd' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Chose if import gross or net product size.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Product weight type', 'wcifd' ); ?></th>
				<td>
					<select name="wcifd-weight-type" class="wcifd">
						<option value="gross-weight"<?php echo( $weight_type == 'gross-weight' ) ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Gross weight', 'wcifd' ); ?></option>
						<option value="net-weight"<?php echo( $weight_type == 'net-weight' ) ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Net weight', 'wcifd' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Chose if import gross or net product weight.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Short description', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="short-description" value="0">
					<input type="checkbox" name="short-description" value="1"<?php echo $short_description == 1 ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Use the excerpt as short product description.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product description', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="exclude-description" value="0">
					<input type="checkbox" name="exclude-description" value="1"<?php echo $exclude_description == 1 ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Exclude descriptions for products updates.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Exclude product title', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="exclude-title" value="0">
					<input type="checkbox" name="exclude-title" value="1"<?php echo $exclude_title == 1 ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Exclude title for products updates.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Categories', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="deleting-categories" value="0">
					<input type="checkbox" name="deleting-categories" value="1"<?php echo $deleting_categories == 1 ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Avoid deleting categories during synchronizations.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Deleted products', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="deleted-products" value="0">
					<input type="checkbox" name="deleted-products" value="1"<?php echo $deleted_products == 1 ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Avoid updating products in trash.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Supplier', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="hidden-use-suppliers" value="0">
					<input type="checkbox" name="wcifd-use-suppliers" value="1"<?php echo $use_suppliers == 1 ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Use the product supplier as post author.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Producer', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="hidden-display-producer" value="0">
					<input type="checkbox" name="wcifd-display-producer" value="1"<?php echo $display_producer == 1 ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Display the producer to the user', 'wcifd' ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e( 'Publish new products', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="publish-new-products" value="0">
					<input type="checkbox" name="publish-new-products" value="1"<?php echo $publish_new_products == 1 ? ' checked="checked"' : ''; ?>>
					<p class="description"><?php esc_html_e( 'Publish new products directly.', 'wcifd' ); ?></p>
				</td>
			</tr>
		</table>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php _e( 'Save Changes', 'wcifd' ); ?>">
	</form>

</div>

<!--Form Prodotti - Custom fields-->
<div id="wcifd-products-fields" class="wcifd-products-sub">

	<form name="wcifd-products-fields" class="wcifd-form" method="post" action="">

		<h2 class="title"><?php esc_html_e( 'Import Danea Custom Fields', 'wcifd' ); ?></h2>
				
		<table class="form-table">

			<?php

			$custom_fields = get_option( 'wcifd-custom-fields' ) ? get_option( 'wcifd-custom-fields' ) : array();

			for ( $i = 1; $i < 5; $i++) { 
		
				$import_field  = isset( $custom_fields[ $i ]['import'] ) ? $custom_fields[ $i ]['import'] : 0; 
				$display_field = isset( $custom_fields[ $i ]['display'] ) ? $custom_fields[ $i ]['display'] : 0;
				$field_name    = isset( $custom_fields[ $i ]['name'] ) ? $custom_fields[ $i ]['name'] : '';

				if ( isset( $_POST['wcifd-custom-fields-hidden'] ) ) {
					
					if ( isset( $_POST[ 'import-custom-field-' . $i ] ) ) {

						$import_field = $_POST[ 'import-custom-field-' . $i ] ? $_POST[ 'import-custom-field-' . $i ] : 0;
						$custom_fields[ $i ]['import'] = $import_field;

					}

					if ( isset( $_POST[ 'display-custom-field-' . $i ] ) ) {

						$display_field = $_POST[ 'display-custom-field-' . $i ] ? $_POST[ 'display-custom-field-' . $i ] : 0;
						$custom_fields[ $i ]['display'] = $display_field;

					}

					if ( isset( $_POST[ 'custom-field-name-' . $i ] ) ) {

						$field_name = $_POST[ 'custom-field-name-' . $i ];
						$custom_fields[ $i ]['name'] = $field_name;

					}

					update_option( 'wcifd-custom-fields', $custom_fields );

				}

				echo '<tr class="one-of wcifd-custom-field">';
					echo '<th scope="row">' . sprintf( esc_html__( 'Custom Field %d', 'wcifd' ), $i ) . '</th>';
					echo '<td>';

						echo '<div class="field-import">';
							echo '<input type="hidden" name="import-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="import-custom-field-' . esc_attr( $i ) . '" value="1"' . ( $import_field == 1 ? ' checked="checked"' : '' ) . '>';
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Import Danea Custom Field %d', 'wcifd' ), $i ) . '</p>';
						echo '</div>';

						echo '<div class="field-display">';
							echo '<input type="hidden" name="display-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="display-custom-field-' . esc_attr( $i ) . '" value="1"' . ( $display_field == 1 ? ' checked="checked"' : '' ) . '>';
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Make Custom Field %d visible in front-end', 'wcifd' ), $i ) . '</p>';
						echo '</div>';

						echo '<div class="field-name">';
							echo '<input type="text" class="custom-field-name" name="custom-field-name-' . esc_attr( $i ) . '" value="' . esc_attr( $field_name ) . '" placeholder="' . esc_html__( 'My custom field', 'wcifd' ) . '">';
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Add a name to Custom Field %d', 'wcifd' ), $i ) . '</p>';
						echo '</div>';

					echo '</td>';
				echo '</tr>';

			}
			?>

		</table>
		<input type="hidden" name="wcifd-custom-fields-hidden" value="1">
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php _e( 'Save Changes', 'wcifd' ); ?>">
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
			<?php
			$premium_key = strtolower( get_option( 'wcifd-premium-key' ) );
			$url_code = get_option( 'wcifd-url-code' );
			if ( ! $url_code ) {
				$url_code = wcifd_rand_md5( 6 );
				add_option( 'wcifd-url-code', $url_code );
			}

			$receive_orders_url = __( 'Please insert your <strong>Premium Key</strong>', 'wcifd' );
			if ( $premium_key ) {
				$receive_orders_url = home_url() . '?key=' . $premium_key . '&code=' . $url_code . '&mode=data';
			}

			$import_images = get_option( 'wcifd-import-images' );
			if ( isset( $_POST['hidden-receive-images'] ) ) {
				$import_images = ( isset( $_POST['wcifd-import-images'] ) ) ? $_POST['wcifd-import-images'] : 0;
				update_option( 'wcifd-import-images', $import_images );
			}
			?>
				
			<tr>
				<th scope="row"><?php esc_html_e( 'URL', 'wcifd' ); ?></th>
				<td>
					<div class="wcifd-copy-url"><span<?php echo( ! $premium_key ? ' class="wcifd-red"' : '' ); ?>><?php echo $receive_orders_url; ?></span></div>
					<p class="description"><?php esc_html_e( 'Add this URL to the Settings tab of the Products update function in Danea.', 'wcifd' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Import images', 'wcifd' ); ?></th>
				<td>
					<input type="hidden" name="hidden-receive-images" value="1">
					<input type="checkbox" class="wcifd-import-images" name="wcifd-import-images" value="1" <?php echo( $import_images == 1 ) ? 'checked="checked"' : ''; ?>>
					<?php esc_html_e( 'Import products images from Danea.', 'wcifd' ); ?>
				</td>
			</tr>
		</table>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php _e( 'Save Changes', 'wcifd' ); ?>">
	</form>

</div>

<!--Form Prodotti - File upload-->
<div id="wcifd-products-file" class="wcifd-products-sub">

	<form name="wcifd-products-import" id="wcifd-products-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

		<?php $file_type = ( isset( $_POST['file-type'] ) ) ? sanitize_text_field( $_POST['file-type'] ) : get_option( 'wcifd-file-type' ); ?>


		<h2 class="title"><?php esc_html_e( 'Import products from a file', 'wcifd' ); ?></h2>

		<table class="form-table">
			<tr>
				<th scoper="row"><?php esc_html_e( 'File type', 'wcifd' ); ?></th>
				<td>
					<select name="file-type" class="wcifd">
							<option value="xml" <?php echo( 'xml' === $file_type ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'xml', 'wcifd' ); ?></option>
							<option value="csv" <?php echo( 'csv' === $file_type ) ? ' selected="selected"' : ''; ?>><?php esc_html_e( 'csv', 'wcifd' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'Select the file type to be imported', 'wcifd' ); ?></p>
				</td>
			</tr>
			<?php wp_nonce_field( 'wcifd-products-import', 'wcifd-products-nonce' ); ?>
			<input type="hidden" name="products-import" value="1">
			<tr>
				<th scope="row"><?php _e( 'Add products', 'wcifd' ); ?></th>
				<td>
					<input type="file" name="products-list">
					<p class="description"><?php _e( 'Select your products list file', 'wcifd' ); ?></p>
				</td>
			</tr>
		</table>
		<input type="submit" class="button-primary" value="<?php _e( 'Import Products', 'wcifd' ); ?>">
	</form>

</div>

<?php wcifd_products(); ?>
