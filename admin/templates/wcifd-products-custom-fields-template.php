<?php
/**
 * Products template - Custom fields
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="wcifd-products-fields" class="wcifd-products-sub">

	<form name="wcifd-products-fields" class="wcifd-form" method="post" action="">

		<h2 class="title"><?php esc_html_e( 'Import Danea Custom Fields', 'wc-importer-for-danea' ); ?></h2>

		<table class="form-table">

			<?php

			$custom_fields = get_option( 'wcifd-custom-fields' ) ? get_option( 'wcifd-custom-fields' ) : array();

			for ( $i = 1; $i < 5; $i++ ) {

				$import_field  = isset( $custom_fields[ $i ]['import'] ) ? $custom_fields[ $i ]['import'] : 0;
				$tag_append    = isset( $custom_fields[ $i ]['append'] ) ? $custom_fields[ $i ]['append'] : 0;
				$split_field   = isset( $custom_fields[ $i ]['split'] ) ? $custom_fields[ $i ]['split'] : 0;
				$display_field = isset( $custom_fields[ $i ]['display'] ) ? $custom_fields[ $i ]['display'] : 0;
				$field_name    = isset( $custom_fields[ $i ]['name'] ) ? $custom_fields[ $i ]['name'] : '';

				if ( isset( $_POST['wcifd-custom-fields-hidden'], $_POST['wcifd-products-fields-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-products-fields-nonce'] ) ), 'wcifd-products-fields' ) ) {

					if ( isset( $_POST[ 'import-custom-field-' . $i ] ) ) {

						$import_field                  = sanitize_text_field( wp_unslash( $_POST[ 'import-custom-field-' . $i ] ) );
						$custom_fields[ $i ]['import'] = $import_field;

					}

					if ( isset( $_POST[ 'custom-field-tag-append-' . $i ] ) ) {

						$tag_append                    = sanitize_text_field( wp_unslash( $_POST[ 'custom-field-tag-append-' . $i ] ) );
						$custom_fields[ $i ]['append'] = $tag_append;

					}

					if ( isset( $_POST[ 'split-custom-field-' . $i ] ) ) {

						$split_field                  = sanitize_text_field( wp_unslash( $_POST[ 'split-custom-field-' . $i ] ) );
						$custom_fields[ $i ]['split'] = $split_field;

					}

					if ( isset( $_POST[ 'display-custom-field-' . $i ] ) ) {

						$display_field                  = sanitize_text_field( wp_unslash( $_POST[ 'display-custom-field-' . $i ] ) );
						$custom_fields[ $i ]['display'] = $display_field;

					}

					if ( isset( $_POST[ 'custom-field-name-' . $i ] ) ) {

						$field_name                  = sanitize_text_field( wp_unslash( $_POST[ 'custom-field-name-' . $i ] ) );
						$custom_fields[ $i ]['name'] = $field_name;

					}

					update_option( 'wcifd-custom-fields', $custom_fields );

				}

				echo '<tr class="one-of wcifd-custom-field">';

					/* Translators: the custom field number */
					echo '<th scope="row">' . sprintf( esc_html__( 'Custom Field %d', 'wc-importer-for-danea' ), intval( $i ) ) . '</th>';
					echo '<td>';

						echo '<div class="field-import">';
							echo '<input type="hidden" name="import-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<select name="import-custom-field-' . esc_attr( $i ) . '" class="wcifd-select">';
								echo '<option value="">' . esc_html__( 'Don\'t import', 'wc-importer-for-danea' ) . '</option>';
								echo '<option value="attribute"' . ( 'attribute' === $import_field ? ' selected' : null ) . '>' . esc_html__( 'Attribute', 'wc-importer-for-danea' ) . '</option>';
								echo '<option value="tag"' . ( 'tag' === $import_field ? ' selected' : null ) . '>' . esc_html__( 'Tag', 'wc-importer-for-danea' ) . '</option>';
							echo '</select>';

							/* Translators: the custom field number */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Import Danea Custom Field %d', 'wc-importer-for-danea' ), intval( $i ) ) . '</p>';
						echo '</div>';

						echo '<div class="field-tag-append">';
							echo '<input type="hidden" name="custom-field-tag-append-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="custom-field-tag-append-' . esc_attr( $i ) . '" value="1"' . ( 1 === intval( $tag_append ) ? ' checked="checked"' : '' ) . '>';
							echo '<p class="description bottom">' . esc_html__( 'Add to other product tags present', 'wc-importer-for-danea' ) . '</p>';
						echo '</div>';

						echo '<div class="field-split">';
							echo '<input type="hidden" name="split-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="split-custom-field-' . esc_attr( $i ) . '" value="1"' . ( 1 === intval( $split_field ) ? ' checked="checked"' : '' ) . '>';
							echo '<p class="description bottom">' . esc_html__( 'Create multiple attributes/tags using comma as separator', 'wc-importer-for-danea' ) . '</p>';
						echo '</div>';

						echo '<div class="field-display">';
							echo '<input type="hidden" name="display-custom-field-' . esc_attr( $i ) . '" value="0">';
							echo '<input type="checkbox" name="display-custom-field-' . esc_attr( $i ) . '" value="1"' . ( 1 === intval( $display_field ) ? ' checked="checked"' : '' ) . '>';

							/* Translators: the custom field number */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Make Custom Field %d visible in front-end', 'wc-importer-for-danea' ), intval( $i ) ) . '</p>';
						echo '</div>';

						echo '<div class="field-name">';
							echo '<input type="text" class="custom-field-name" name="custom-field-name-' . esc_attr( $i ) . '" value="' . esc_attr( $field_name ) . '" placeholder="' . esc_html__( 'My custom field', 'wc-importer-for-danea' ) . '">';

							/* Translators: the custom field number */
							echo '<p class="description bottom">' . sprintf( esc_html__( 'Add a name to Custom Field %d', 'wc-importer-for-danea' ), intval( $i ) ) . '</p>';
						echo '</div>';

					echo '</td>';
				echo '</tr>';

			}
			?>

		</table>
		<?php wp_nonce_field( 'wcifd-products-fields', 'wcifd-products-fields-nonce' ); ?>
		<input type="hidden" name="wcifd-custom-fields-hidden" value="1">
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>">
	</form>

</div>
