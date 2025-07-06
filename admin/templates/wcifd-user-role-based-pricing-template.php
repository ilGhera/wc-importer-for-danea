<?php
/**
 * WooCommerce Role Based Pricing template
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="wcifd-rbp" class="wcifd-admin">
    <form name="wcifd-rbp-settings" class="wcifd-form" method="post" action="">

        <table class="form-table">
            <?php
            $wrbp_settings = get_option( 'wrbp_settings_array' );

            if ( class_exists( 'WOOCOMMERCE_ROLE_BASED_PRICING' ) && is_array( $wrbp_settings ) ) {
                $allowed_price = array( 'regular_price', 'sale_price' ); 

                if ( isset( $wrbp_settings['wrbp_func_enable'] ) && 'yes' !== $wrbp_settings['wrbp_func_enable'] ) {
                    return;
                }

                $roles_excluded = isset( $wrbp_settings['wrbp_exclude_role'] ) ? $wrbp_settings['wrbp_exclude_role'] : array();
                $roles_excluded = str_replace( ' ', '_', $roles_excluded );
                
                global $wp_roles;

                if ( isset( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {

                    $p = 0;
                    foreach ( $wp_roles->roles as $key => $value ) {

                        if ( ! in_array( $key, $roles_excluded ) ) {

                            foreach ( $allowed_price as $price_type ) {

                                $p ++;
                                $price_label = 'regular_price' === $price_type ? __( 'Regular price', 'wc-importer-for-danea' ) : __( 'Sale price', 'wc-importer-for-danea' );
                                $field_name  = $price_type . '_' . $key;

                                $price_list = get_option( 'wcifd_' . $field_name );

                                if ( isset( $_POST[ $field_name ], $_POST['wcifd-role-based-price-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-role-based-price-nonce'] ) ), 'wcifd-role-based-price' ) ) {

                                    $price_list = sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) );
                                    update_option( 'wcifd_' . $field_name, $price_list );

                                }

                                if ( count( $allowed_price ) === 1 ) {

                                    echo '<tr class="one-of">';

                                } else {

                                    echo 0 === $p % 2 ? '<tr class="one-of">' : '<tr>';

                                }
                                ?>
                                    <th scope="row"><?php echo esc_html__( $price_label, 'wc-importer-for-danea' ) . ' ' . ucfirst( esc_html__( $value['name'], 'woocommerce' ) ); ?></th>
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
            }
            ?>
        </table>
        <?php wp_nonce_field( 'wcifd-role-based-price', 'wcifd-role-based-price-nonce' ); ?>
        <input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>">
    </form>
</div>
