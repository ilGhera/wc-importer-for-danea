<?php
/**
 * Importazione ordini
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @version 1.1.0
 */
?>

<!--Product Form-->
<form name="wcifd-orders-import" id="wcifd-orders-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">
	<table class="form-table">

		<?php $wcifd_orders_add_users = ( isset( $_POST['wcifd-orders-add-users'] ) ) ? sanitize_text_field( $_POST['wcifd-orders-add-users'] ) : get_option( 'wcifd-orders-add-users' ); ?>
		<tr>
			<th scope="row"><?php _e( 'New customers', 'wcifd' ); ?></th>
			<td>
				<select name="wcifd-orders-add-users">
					<option name="" value="0"<?php echo( $wcifd_orders_add_users == 0 ) ? ' selected="selected"' : ''; ?>><?php _e( 'Don\'t create users', 'wcifd' ); ?></option>
					<option name="" value="1"<?php echo( $wcifd_orders_add_users == 1 ) ? ' selected="selected"' : ''; ?>><?php _e( 'Create users', 'wcifd' ); ?></option>
				</select>
				<p class="description"><?php _e( 'Add new customers as Wordpress users', 'wcifd' ); ?></p>
			</td>
		</tr>

		<?php
		if ( isset( $_POST['wcifd-orders-status'] ) ) {
			$wcifd_orders_status = strtolower( str_replace( ' ', '-', sanitize_text_field( $_POST['wcifd-orders-status'] ) ) );
		} else {
			$wcifd_orders_status = get_option( 'wcifd-orders-status' );
		}
		?>

		<tr>
			<th scope="row"><?php echo __( 'Orders status', 'wcifd' ); ?></th>
			<td>
				<select name="wcifd-orders-status">
					<?php
					$statuses = wc_get_order_statuses();
					foreach ( $statuses as $status ) {
						echo '<option name="' . $status . '" value="' . $status . '"';
						echo ( $wcifd_orders_status == strtolower( str_replace( ' ', '-', $status ) ) ) ? ' selected="selected">' : '>';
						echo __( $status, 'wcifd' ) . '</option>';
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
				<input type="file" name="orders-list">
				<p class="description"><?php _e( 'Select your orders list file (.xml)', 'wcifd' ); ?></p>
			</td>
		</tr>
	</table>
	<input type="submit" class="button-primary" value="<?php _e( 'Import Orders', 'wcifd' ); ?>">
</form>

<?php wcifd_orders(); ?>
