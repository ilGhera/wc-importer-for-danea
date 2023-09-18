<?php
/**
 * Importazione ordini
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.3.1
 */

?>

<!--Product Form-->
<form name="wcifd-orders-import" id="wcifd-orders-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e( 'New customers', 'wc-importer-for-danea' ); ?></th>
			<td>
				<select name="wcifd-orders-add-users" class="wcifd-select">
					<option name="" value="0"><?php esc_html_e( 'Don\'t create users', 'wc-importer-for-danea' ); ?></option>
					<option name="" value="1"><?php esc_html_e( 'Create users', 'wc-importer-for-danea' ); ?></option>
				</select>
				<p class="description"><?php esc_html_e( 'Add new customers as WordPress users', 'wc-importer-for-danea' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Orders status', 'wc-importer-for-danea' ); ?></th>
			<td>
				<select name="wcifd-orders-status" class="wcifd-select">
					<?php
					$statuses = wc_get_order_statuses();
					foreach ( $statuses as $stat ) {
						echo '<option name="' . esc_attr( $stat ) . '" value="' . esc_attr( $stat ) . '">' . esc_html__( $stat, 'wc-importer-for-danea' ) . '</option>';
					}
					?>
				</select>
				<p class="description"><?php esc_html_e( 'Select the status that you want to assign to the imported orders.', 'wc-importer-for-danea' ); ?></p>
			</td>
		</tr>

		<?php wp_nonce_field( 'wcifd-orders-import', 'wcifd-orders-nonce' ); ?>

		<input type="hidden" name="orders-import" value="1">
		<tr>
			<th scope="row"><?php esc_html_e( 'Add orders', 'wc-importer-for-danea' ); ?></th>
			<td>
				<input type="file" name="orders-list" disabled>
				<p class="description"><?php esc_html_e( 'Select your orders list file (.xml)', 'wc-importer-for-danea' ); ?></p>
			</td>
		</tr>
		<tr>
			<th></th>
			<td><?php go_premium(); ?></td>
		</tr>
	</table>
	<input type="submit" class="button-primary" value="<?php esc_html_e( 'Import Orders', 'wc-importer-for-danea' ); ?>" disabled>
</form>
