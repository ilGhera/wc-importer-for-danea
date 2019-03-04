<?php
/**
 * Importazione prodotti
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @version 1.1.4
 */
?>

<!--Form prodotti-->
<form name="wcifd-products-settings" class="wcifd-form one-of" method="post" action="">
	
	<h2 class="title"><?php echo __( 'General settings', 'wcifd' ); ?></h2>
	
	<table class="form-table">
		<?php
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
		<tr>
			<th scope="row"><?php echo __( 'Prices imported with tax', 'wcifd' ); ?></th>
			<td>
				<select name="tax-included" class="wcifd">
					<option value="1" <?php echo( $tax_included == 1 ) ? ' selected="selected"' : ''; ?>><?php echo __( ' Yes, I will import prices inclusive of tax', 'wcifd' ); ?></option>
					<option value="0" <?php echo( $tax_included == 0 ) ? ' selected="selected"' : ''; ?>><?php echo __( 'No, I will import prices exclusive of tax', 'wcifd' ); ?></option>
				</select>
				<p class="description"><?php echo __( 'In Danea you can choose if export prices with tax included or not. What are you going to import?', 'wcifd' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Regular price', 'wcifd' ); ?></th>
			<td>
				<select name="regular-price-list" class="wcifd">
					<?php
					for ( $n = 1; $n <= 9; $n++ ) {
						echo '<option value="' . $n . '"' . ( $regular_price_list == $n ? 'selected="selected"' : '' ) . '>' . __( 'Price list ', 'wcifd' ) . $n . '</option>';
					}
					?>
				</select>
				<p class="description"><?php echo __( 'The Danea price list to use for Woocommerce regular price.', 'wcifd' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Sale price', 'wcifd' ); ?></th>
			<td>
				<select name="sale-price-list" class="wcifd">
					<?php
					echo '<option>' . __( 'Select a price list', 'wcifd' ) . '</option>';
					for ( $n = 1; $n <= 9; $n++ ) {
						echo '<option value="' . $n . '"' . ( $sale_price_list == $n ? 'selected="selected"' : '' ) . '>' . __( 'Price list ', 'wcifd' ) . $n . '</option>';
					}
					?>
				</select>
				<p class="description"><?php echo __( 'The Danea price list to use for Woocommerce sale price.', 'wcifd' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Product size type', 'wcifd' ); ?></th>
			<td>
				<select name="wcifd-size-type" class="wcifd">
					<option value="gross-size"<?php echo( $size_type == 'gross-size' ) ? ' selected="selected"' : ''; ?>><?php echo __( 'Gross size', 'wcifd' ); ?></option>
					<option value="net-size"<?php echo( $size_type == 'net-size' ) ? ' selected="selected"' : ''; ?>><?php echo __( 'Net size', 'wcifd' ); ?></option>
				</select>
				<p class="description"><?php echo __( 'Chose if import gross or net product size.', 'wcifd' ); ?></p>
			</td>
		</tr>
		<tr>
		<tr>
			<th scope="row"><?php echo __( 'Product weight type', 'wcifd' ); ?></th>
			<td>
				<select name="wcifd-weight-type" class="wcifd">
					<option value="gross-weight"<?php echo( $weight_type == 'gross-weight' ) ? 'selected="selected"' : ''; ?>><?php echo __( 'Gross weight', 'wcifd' ); ?></option>
					<option value="net-weight"<?php echo( $weight_type == 'net-weight' ) ? 'selected="selected"' : ''; ?>><?php echo __( 'Net weight', 'wcifd' ); ?></option>
				</select>
				<p class="description"><?php echo __( 'Chose if import gross or net product weight.', 'wcifd' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Short description', 'wcifd' ); ?></th>
			<td>
				<input type="hidden" name="short-description" value="0">
				<input type="checkbox" name="short-description" value="1"<?php echo $short_description == 1 ? ' checked="checked"' : ''; ?>>
				<?php echo __( 'Use the excerpt as short product description.', 'wcifd' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Exclude product description', 'wcifd' ); ?></th>
			<td>
				<input type="hidden" name="exclude-description" value="0">
				<input type="checkbox" name="exclude-description" value="1"<?php echo $exclude_description == 1 ? ' checked="checked"' : ''; ?>>
				<?php echo __( 'Exclude descriptions for products updates.', 'wcifd' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Categories', 'wcifd' ); ?></th>
			<td>
				<input type="hidden" name="deleting-categories" value="0">
				<input type="checkbox" name="deleting-categories" value="1"<?php echo $deleting_categories == 1 ? ' checked="checked"' : ''; ?>>
				<?php echo __( 'Avoid deleting categories during synchronizations.', 'wcifd' ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Deleted products', 'wcifd' ); ?></th>
			<td>
				<input type="hidden" name="deleted-products" value="0">
				<input type="checkbox" name="deleted-products" value="1"<?php echo $deleted_products == 1 ? ' checked="checked"' : ''; ?>>
				<?php echo __( 'Avoid updating products in trash.', 'wcifd' ); ?>
			</td>
		</tr>

		<tr>
			<th scope="row"><?php _e( 'Suppliers', 'wcifd' ); ?></th>
			<td>
				<fieldset>
					<label for="wcifd-use-suppliers">
						<input type="hidden" name="hidden-use-suppliers" value="1">
						<input type="checkbox" class="wcifd-use-suppliers" name="wcifd-use-suppliers" value="1" 
						<?php
						if ( get_option( 'wcifd-use-suppliers' ) == 1 ) {
							echo 'checked="checked"'; }
						?>
						>
						<?php echo __( 'Use the product supplier as post author.', 'wcifd' ); ?>
					</label>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Publish new products', 'wcifd' ); ?></th>
			<td>
				<input type="hidden" name="publish-new-products" value="0">
				<input type="checkbox" name="publish-new-products" value="1"<?php echo $publish_new_products == 1 ? ' checked="checked"' : ''; ?>>
				<?php echo __( 'Publish new products directly.', 'wcifd' ); ?>
			</td>
		</tr>
	</table>
	<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php _e( 'Save Changes', 'wcifd' ); ?>">
</form>

<form name="wcifd-receive-products" id="wcifd-receive-products" class="wcifd-form one-of" method="post" action="">
	
	<h2 class="title"><?php echo __( 'Receive products from Danea', 'wcifd' ); ?></h2>

	<p>
		<?php
		echo __( 'Receive products directly from the XML sent by Danea via HTTP post.', 'wcifd' ) . '<br>';
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
			<th scope="row"><?php echo __( 'URL', 'wcifd' ); ?></th>
			<td>
				<div class="wcifd-copy-url"><span<?php echo( ! $premium_key ? ' class="wcifd-red"' : '' ); ?>><?php echo $receive_orders_url; ?></span></div>
				<p class="description"><?php echo __( 'Add this URL to the <strong>Settings</strong> tab of the <strong>Products update</strong> function in Danea.', 'wcifd' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Import images', 'wcifd' ); ?></th>
			<td>
				<input type="hidden" name="hidden-receive-images" value="1">
				<input type="checkbox" class="wcifd-import-images" name="wcifd-import-images" value="1" <?php echo( $import_images == 1 ) ? 'checked="checked"' : ''; ?>>
				<?php echo __( 'Import products images from Danea.', 'wcifd' ); ?>
			</td>
		</tr>
	</table>
	<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php _e( 'Save Changes', 'wcifd' ); ?>">
</form>

<form name="wcifd-products-import" id="wcifd-products-import" class="wcifd-form one-of"  method="post" enctype="multipart/form-data" action="">

	<?php $file_type = ( isset( $_POST['file-type'] ) ) ? sanitize_text_field( $_POST['file-type'] ) : get_option( 'wcifd-file-type' ); ?>


	<h2 class="title"><?php echo __( 'Import products from a file', 'wcifd' ); ?></h2>

	<table class="form-table">
		<tr>
			<th scoper="row"><?php echo __( 'File type', 'wcifd' ); ?></th>
			<td>
				<select name="file-type" class="wcifd">
						<option value="xml" <?php echo( 'xml' === $file_type ) ? ' selected="selected"' : ''; ?>><?php echo __( 'xml', 'wcifd' ); ?></option>
						<option value="csv" <?php echo( 'csv' === $file_type ) ? ' selected="selected"' : ''; ?>><?php echo __( 'csv', 'wcifd' ); ?></option>
				</select>
				<p class="description"><?php echo __( 'Select the file type to be imported', 'wcifd' ); ?></p>
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

<?php wcifd_products(); ?>
