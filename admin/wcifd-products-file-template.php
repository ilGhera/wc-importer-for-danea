<?php
/**
 * Products template - File
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.6.1
 */

defined( 'ABSPATH' ) || exit;
?>

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
