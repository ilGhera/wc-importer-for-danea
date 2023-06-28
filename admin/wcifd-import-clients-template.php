<?php
/**
 * Importazione clienti
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @since 1.6.0
 */

global $wp_roles;
$roles = $wp_roles->get_names();
$users_val = ( isset( $_POST['wcifd-users'] ) ) ? sanitize_text_field( $_POST['wcifd-users'] ) : get_option( 'wcifd-clients-role' );
?>

<!--Form Clienti-->
<form name="wcifd-clients-import" id="wcifd-clients-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'User role', 'wcifd' ); ?></th>
			<td>
			<select class="wcifd-users-clients wcifd-select" name="wcifd-users-clients">
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
			<p class="description"><?php _e( 'Select a Wordpress user role for your clients.', 'wcifd' ); ?></p>
		</tr>

		<?php wp_nonce_field( 'wcifd-clients-import', 'wcifd-clients-nonce' ); ?>
		<input type="hidden" name="clients-import" value="1">

		<tr>
			<th scope="row"><?php _e( 'Add clients', 'wcifd' ); ?></th>
			<td>
				<input type="file" name="clients-list">
				<p class="description"><?php _e( 'Select your clients list file (.csv)', 'wcifd' ); ?></p>
			</td>
		</tr>

	</table>

	<input type="submit" class="button-primary" value="<?php _e( 'Import Clients', 'wcifd' ); ?>">

</form>

<?php wcifd_users( 'clients' ); ?>
