<?php
/**
 * WooCommerce role Based Price
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @since 1.3.0
 */

?>

<!--Role Based Price-->
<form name="wcifd-rbp-settings" class="wcifd-form" method="post" action="">

	<table class="form-table">
		<?php
		$wc_rbp_general = get_option( 'wc_rbp_general' );
		if ( function_exists( 'woocommerce_role_based_price' ) && $wc_rbp_general ) {
			$wc_rbp_allowed_roles = isset( $wc_rbp_general['wc_rbp_allowed_roles'] ) ? $wc_rbp_general['wc_rbp_allowed_roles'] : '';
			$wc_rbp_allowed_price = isset( $wc_rbp_general['wc_rbp_allowed_price'] ) ? $wc_rbp_general['wc_rbp_allowed_price'] : '';

			if ( $wc_rbp_allowed_roles ) {
				$p = 0;
				foreach ( $wc_rbp_allowed_roles as $rl ) {
					foreach ( $wc_rbp_allowed_price as $price_type ) {

						$p ++;
						$price_label = 'regular_price' === $price_type ? $wc_rbp_general['wc_rbp_regular_price_label'] : $wc_rbp_general['wc_rbp_selling_price_label'];
						$field_name  = $price_type . '_' . $rl;

						if ( count( $wc_rbp_allowed_price ) === 1 ) {

							echo '<tr class="one-of">';

						} else {

							echo 0 === $p % 2 ? '<tr class="one-of">' : '<tr>';

						}
						?>
							<th scope="row"><?php esc_html_e( $price_label, 'wcifd' ) . ' ' . esc_html_e( ucfirst( $rl ), 'woocommerce' ); ?></th>
							<td>
								<select name="<?php echo esc_attr( $field_name ); ?>" class="wcifd wcifd-select">
									<?php
									for ( $n = 1; $n <= 9; $n++ ) {
										echo '<option value="' . esc_attr( $n ) . '">' . esc_html__( 'Price list ', 'wcifd' ) . intval( $n ) . '</option>';
									}
									?>
								</select>
								<p class="description"><?php esc_html_e( 'The Danea price list to use', 'wcifd' ); ?></p>
							</td>
						</tr>
						<?php
					}
				}
			}
		}
		?>
		<tr>
			<th></th>
			<td><?php go_premium(); ?></td>
		</tr>
	</table>
	<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wcifd' ); ?>" disabled>
</form>
