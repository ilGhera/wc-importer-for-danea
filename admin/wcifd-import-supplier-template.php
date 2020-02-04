<?php
/**
 * Importazione fornitori
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @since 1.1.0
 */

global $wp_roles;
$roles = $wp_roles->get_names();
$users_val = ( isset( $_POST['wcifd-users'] ) ) ? sanitize_text_field( $_POST['wcifd-users'] ) : get_option( 'wcifd-suppliers-role' );
?>

<!--Form Fornitori-->
<form name="wcifd-suppliers-import" id="wcifd-suppliers-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'User role', 'wcifd' ); ?></th>
			<td>
			<select class="wcifd-users-suppliers" name="wcifd-users-suppliers">
				<?php
				if ( $users_val ) {
					echo '<option value=" ' . $users_val . ' " selected="selected"> ' . $users_val . '</option>';
					foreach ( $roles as $key => $value ) {
						if ( $key != $users_val ) {
							echo '<option value=" ' . $key . ' "> ' . $key . '</option>';
						}
					}
				} else {
					echo '<option value="Subscriber" selected="selected">Subscriber</option>';
					foreach ( $roles as $key => $value ) {
						if ( $key != 'Subscriber' ) {
							echo '<option value=" ' . $key . ' "> ' . $key . '</option>';
						}
					}
				}
				?>
			</select>
			<p class="description"><?php _e( 'Select a Wordpress user role for your suppliers.', 'wcifd' ); ?></p>
		</tr>

		<?php wp_nonce_field( 'wcifd-suppliers-import', 'wcifd-suppliers-nonce' ); ?>
		<input type="hidden" name="suppliers-import" value="1">

		<tr>
			<th scope="row"><?php _e( 'Add suppliers', 'wcifd' ); ?></th>
			<td>
				<input type="file" name="suppliers-list">
				<p class="description"><?php _e( 'Select your suppliers list file (.csv)', 'wcifd' ); ?></p>
			</td>
		</tr>

	</table>

	<input type="submit" class="button-primary" value="<?php _e( 'Import Suppliers', 'wcifd' ); ?>">

</form>

<?php wcifd_users( 'suppliers' ); ?>
