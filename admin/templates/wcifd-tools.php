<?php
/**
 * Tools template
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;

$tools = get_option( 'wcifd-tools' );

if ( ! $tools ) {
	$tools = array(
		'as-cleaner-days'   => 30,
		'as-clean-failed'   => false,
		'as-clean-canceled' => false,
	);
}

if ( isset( $_POST['wcifd-tools-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcifd-tools-nonce'] ) ), 'wcifd-tools' ) ) {

	if ( isset( $_POST['as-cleaner-days'] ) ) {
		$tools['as-cleaner-days'] = sanitize_text_field( wp_unslash( $_POST['as-cleaner-days'] ) );
	}

	if ( isset( $_POST['as-clean-failed'] ) ) {
		$tools['as-clean-failed'] = sanitize_text_field( wp_unslash( $_POST['as-clean-failed'] ) );
	}

	if ( isset( $_POST['as-clean-canceled'] ) ) {
		$tools['as-clean-canceled'] = sanitize_text_field( wp_unslash( $_POST['as-clean-canceled'] ) );
	}

	update_option( 'wcifd-tools', $tools );
}
?>

<div id="wcifd-tools" class="wcifd-admin">
	<form name="wcifd-tools-settings" class="wcifd-form" method="post" action="">
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Action Scheduler cleaner', 'wc-importer-for-danea' ); ?></th>
				<td>
					<select class="wcifd-select" name="as-cleaner-days" id="as-cleaner-days">
						<?php
						$options_days = array( 7, 30, 60, 90 );
						foreach ( $options_days as $day_value ) {
							/* Translators: the number of days */
							echo '<option value="' . esc_attr( $day_value ) . '" ' . selected( $day_value, $tools['as-cleaner-days'], false ) . '>' . sprintf( esc_html__( '%d Days', 'wc-importer-for-danea' ), intval( $day_value ) ) . '</option>';
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'Action Scheduler automatically cleans up old completed, failed, or canceled tasks. Select the number of days to keep these task entries in your database.', 'wc-importer-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Additional Task Statuses to Clean', 'wc-importer-for-danea' ); ?></th>
				<td>
					<input type="hidden" name="as-clean-failed" value="0">
					<input type="checkbox" name="as-clean-failed" id="as-clean-failed" value="1" <?php checked( $tools['as-clean-failed'], true ); ?>>
					<p class="description bottom"><?php esc_html_e( 'Also clean failed tasks', 'wc-importer-for-danea' ); ?></p>
					<input type="hidden" name="as-clean-canceled" value="0">
					<input type="checkbox" name="as-clean-canceled" id="as-clean-canceled" value="1" <?php checked( $tools['as-clean-canceled'], true ); ?>>
					<p class="description bottom"><?php esc_html_e( 'Also clean canceled tasks', 'wc-importer-for-danea' ); ?></p>
					<p class="description">
						<?php esc_html_e( 'By default, Action Scheduler cleans up completed tasks. Select these options to also remove tasks that have failed or were canceled after the specified number of days.', 'wc-importer-for-danea' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( 'wcifd-tools', 'wcifd-tools-nonce' ); ?>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>">
	</form>
</div>
