<?php
/**
 * Products template - Remote
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.6.1
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="wcifd-products-remote" class="wcifd-products-sub">

	<form name="wcifd-receive-products" id="wcifd-receive-products" class="wcifd-form" method="post" action="">

		<h2 class="title"><?php esc_html_e( 'Receive products from Danea', 'wc-importer-for-danea' ); ?></h2>

		<p>
			<?php
			esc_html_e( 'Receive products directly from the XML sent by Danea via HTTP post.', 'wc-importer-for-danea' ) . '<br>';
			?>
		</p>

		<table class="form-table">
			<?php
			$premium_key = strtolower( get_option( 'wcifd-premium-key' ) );
			$url_code    = get_option( 'wcifd-url-code' );
			if ( ! $url_code ) {
				$url_code = WCIFD_Functions::rand_md5( 6 );
				add_option( 'wcifd-url-code', $url_code );
			}

			$receive_orders_url = __( 'Please insert your <strong>Premium Key</strong>', 'wc-importer-for-danea' );
			if ( $premium_key ) {
				$receive_orders_url = home_url() . '?key=' . $premium_key . '&code=' . $url_code . '&mode=data';
			}

			$import_images = get_option( 'wcifd-import-images' );

			if ( isset( $_POST['hidden-receive-images'], $_POST['wcifd-products-remote-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-products-remote-nonce'] ) ), 'wcifd-products-remote' ) ) {
				$import_images = ( isset( $_POST['wcifd-import-images'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wcifd-import-images'] ) ) : 0;
				update_option( 'wcifd-import-images', $import_images );
			}
			?>

			<tr>
				<th scope="row"><?php esc_html_e( 'URL', 'wc-importer-for-danea' ); ?></th>
				<td>
					<div class="wcifd-copy-url"><span<?php echo( ! $premium_key ? ' class="wcifd-red"' : '' ); ?>><?php echo wp_kses_post( $receive_orders_url ); ?></span></div>
					<p class="description"><?php esc_html_e( 'Add this URL to the Settings tab of the Products update function in Danea.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Import images', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="hidden-receive-images" value="1">
					<input type="checkbox" class="wcifd-import-images" name="wcifd-import-images" value="1" <?php echo( 1 === intval( $import_images ) ? 'checked="checked"' : '' ); ?>>
					<?php esc_html_e( 'Import products images from Danea.', 'wc-importer-for-danea' ); ?>
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'wcifd-products-remote', 'wcifd-products-remote-nonce' ); ?>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>">
	</form>

</div>
