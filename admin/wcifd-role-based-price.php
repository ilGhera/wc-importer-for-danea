<?php
/**
 * WooCommerce role Based Price
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.6.1
 */

?>

<!--Role Based Price-->
<form name="wcifd-rbp-settings" class="wcifd-form" method="post" action="">

	<table class="form-table">
		<?php
		$wc_rbp_general = get_option( 'wc_rbp_general' );

		if ( function_exists( 'woocommerce_role_based_price' ) && is_array( $wc_rbp_general ) ) {
			$wc_rbp_allowed_roles = isset( $wc_rbp_general['wc_rbp_allowed_roles'] ) ? $wc_rbp_general['wc_rbp_allowed_roles'] : '';
			$wc_rbp_allowed_price = isset( $wc_rbp_general['wc_rbp_allowed_price'] ) ? $wc_rbp_general['wc_rbp_allowed_price'] : '';

			if ( $wc_rbp_allowed_roles ) {
				$p = 0;
				foreach ( $wc_rbp_allowed_roles as $urole ) {
					foreach ( $wc_rbp_allowed_price as $price_type ) {

						$p ++;
						$price_label = 'regular_price' === $price_type ? $wc_rbp_general['wc_rbp_regular_price_label'] : $wc_rbp_general['wc_rbp_selling_price_label'];
						$field_name  = $price_type . '_' . $urole;

						$price_list = get_option( 'wcifd_' . $field_name );

						if ( isset( $_POST[ $field_name ], $_POST['wcifd-role-based-price-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-role-based-price-nonce'] ) ), 'wcifd-role-based-price' ) ) {

							$price_list = sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) );
							update_option( 'wcifd_' . $field_name, $price_list );

						}

						if ( count( $wc_rbp_allowed_price ) === 1 ) {

							echo '<tr class="one-of">';

						} else {

							echo 0 === $p % 2 ? '<tr class="one-of">' : '<tr>';

						}
						?>
							<th scope="row"><?php echo esc_html__( $price_label, 'wc-importer-for-danea' ) . ' ' . ucfirst( esc_html__( $urole, 'woocommerce' ) ); ?></th>
							<td>
								<select name="<?php echo esc_html( $field_name ); ?>" class="wcifd wcifd-select">
									<?php
									for ( $n = 1; $n <= 9; $n++ ) {
										echo '<option value="' . esc_attr( $n ) . '"' . ( intval( $price_list ) === $n ? 'selected="selected"' : '' ) . '>' . esc_html__( 'Price list ', 'wc-importer-for-danea' ) . esc_html( $n ) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php esc_html_e( 'The Danea price list to use', 'wc-importer-for-danea' ); ?></p>
							</td>
						</tr>
						<?php
					}
				}
			}
		}
		?>
	</table>
	<?php wp_nonce_field( 'wcifd-role-based-price', 'wcifd-role-based-price-nonce' ); ?>
	<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>">
</form>
