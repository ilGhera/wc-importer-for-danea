<?php
/**
 * Importazione ordini
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @since 1.3.0
 */
?>

<!--Product Form-->
<form name="wcifd-orders-import" id="wcifd-orders-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'New customers', 'wcifd' ); ?></th>
			<td>
				<select name="wcifd-orders-add-users" class="wcifd-select">
					<option name="" value="0"><?php _e( 'Don\'t create users', 'wcifd' ); ?></option>
					<option name="" value="1"><?php _e( 'Create users', 'wcifd' ); ?></option>
				</select>
				<p class="description"><?php _e( 'Add new customers as Wordpress users', 'wcifd' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo __( 'Orders status', 'wcifd' ); ?></th>
			<td>
				<select name="wcifd-orders-status" class="wcifd-select">
					<?php
					$statuses = wc_get_order_statuses();
					foreach ( $statuses as $status ) {
						echo '<option name="' . $status . '" value="' . $status . '">' . __( $status, 'wcifd' ) . '</option>';
					}
					?>
				</select>
				<p class="description"><?php echo __( 'Select the status that you want to assign to the imported orders.', 'wcifd' ); ?></p>
			</td>
		</tr>

		<?php wp_nonce_field( 'wcifd-orders-import', 'wcifd-orders-nonce' ); ?>

		<input type="hidden" name="orders-import" value="1">
		<tr>
			<th scope="row"><?php _e( 'Add orders', 'wcifd' ); ?></th>
			<td>
				<input type="file" name="orders-list" disabled>
				<p class="description"><?php _e( 'Select your orders list file (.xml)', 'wcifd' ); ?></p>
			</td>
		</tr>
		<tr>
			<th></th>
			<td><?php go_premium(); ?></td>
		</tr>
	</table>
	<input type="submit" class="button-primary" value="<?php _e( 'Import Orders', 'wcifd' ); ?>" disabled>
</form>
