<?php
/**
 * Importazione clienti
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.6.1
 */

global $wp_roles;
$roles     = $wp_roles->get_names();
$users_val = get_option( 'wcifd-clients-role' );

if ( isset( $_POST['wcifd-users'], $_POST['wcifd-clients-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-clients-nonce'] ) ), 'wcifd-clients-import' ) ) {

	$users_val = sanitize_text_field( wp_unslash( $_POST['wcifd-users'] ) );

}
?>

<!--Form Clienti-->
<form name="wcifd-clients-import" id="wcifd-clients-import" class="wcifd-form"  method="post" enctype="multipart/form-data" action="">

	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e( 'User role', 'wc-importer-for-danea' ); ?></th>
			<td>
			<select class="wcifd-users-clients wcifd-select" name="wcifd-users-clients">
				<?php
				if ( $users_val ) {
					echo '<option value=" ' . esc_attr( $users_val ) . ' " selected="selected"> ' . esc_html( $users_val ) . '</option>';
					foreach ( $roles as $key => $value ) {
						if ( $key !== $users_val ) {
							echo '<option value=" ' . esc_attr( $key ) . ' "> ' . esc_html( $key ) . '</option>';
						}
					}
				} else {
					echo '<option value="Subscriber" selected="selected">Subscriber</option>';
					foreach ( $roles as $key => $value ) {
						if ( 'Subscriber' !== $key ) {
							echo '<option value=" ' . esc_attr( $key ) . ' "> ' . esc_html( $key ) . '</option>';
						}
					}
				}
				?>
			</select>
			<p class="description"><?php esc_html_e( 'Select a WordPress user role for your clients.', 'wc-importer-for-danea' ); ?></p>
		</tr>

		<?php wp_nonce_field( 'wcifd-clients-import', 'wcifd-clients-nonce' ); ?>
		<input type="hidden" name="clients-import" value="1">

		<tr>
			<th scope="row"><?php esc_html_e( 'Add clients', 'wc-importer-for-danea' ); ?></th>
			<td>
				<input type="file" name="clients-list">
				<p class="description"><?php esc_html_e( 'Select your clients list file (.csv)', 'wc-importer-for-danea' ); ?></p>
			</td>
		</tr>

	</table>

	<input type="submit" class="button-primary" value="<?php esc_html_e( 'Import Clients', 'wc-importer-for-danea' ); ?>">

</form>

<?php wcifd_users( 'clients' ); ?>
