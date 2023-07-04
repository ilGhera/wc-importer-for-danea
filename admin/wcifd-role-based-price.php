<?php
/**
 * WooCommerce role Based Price
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 * @since 1.6.0
 */
?>

<!--Role Based Price-->
<form name="wcifd-rbp-settings" class="wcifd-form" method="post" action="">
		
	<table class="form-table">
		<?php
		if( function_exists( 'woocommerce_role_based_price' ) && $wc_rbp_general = get_option( 'wc_rbp_general') ) {
			$wc_rbp_allowed_roles = isset( $wc_rbp_general['wc_rbp_allowed_roles'] ) ? $wc_rbp_general['wc_rbp_allowed_roles'] : '';
			$wc_rbp_allowed_price = isset( $wc_rbp_general['wc_rbp_allowed_price'] ) ? $wc_rbp_general['wc_rbp_allowed_price'] : '';
		
			if ( $wc_rbp_allowed_roles ) {
				$p = 0;
				foreach ( $wc_rbp_allowed_roles as $role ) {
					foreach ( $wc_rbp_allowed_price as $price_type) {

						$p ++;
						$price_label = $price_type === 'regular_price' ? $wc_rbp_general['wc_rbp_regular_price_label'] : $wc_rbp_general['wc_rbp_selling_price_label'];
						$field_name = $price_type . '_' . $role;

						$price_list = get_option( 'wcifd_' . $field_name );
						if ( isset( $_POST[ $field_name ] ) ) {
							$price_list = $_POST[ $field_name ];
							update_option( 'wcifd_' . $field_name, $price_list );
						}
						
						if ( count( $wc_rbp_allowed_price ) === 1 ) {
							
							echo '<tr class="one-of">';						
						
						} else {

							echo $p % 2 === 0 ? '<tr class="one-of">' : '<tr>';

						}
						?>
							<th scope="row"><?php echo __( $price_label, 'wcifd' ) . ' ' . ucfirst( __( $role, 'woocommerce' ) )  ?></th>
							<td>
								<select name="<?php echo $field_name; ?>" class="wcifd wcifd-select">
									<?php
									for ( $n = 1; $n <= 9; $n++ ) {
										echo '<option value="' . $n . '"' . ( $price_list == $n ? 'selected="selected"' : '' ) . '>' . __( 'Price list ', 'wcifd' ) . $n . '</option>';
									}
									?>
								</select>
								<p class="description"><?php echo __( 'The Danea price list to use', 'wcifd' ); ?></p>
							</td>
						</tr>
					<?php
					}
				}
			}
		}
		?>
	</table>
	<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php _e( 'Save Changes', 'wcifd' ); ?>">
</form>
