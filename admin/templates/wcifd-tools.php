<?php
/**
 * Tools template
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.4.0
 */

defined( 'ABSPATH' ) || exit;

$tools = array(
    'as-cleaner-days'   => 30,
    'as-clean-failed'   => false,
    'as-clean-canceled' => false,
);
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
            <tr>
                <th></th>
                <td><?php WCIFD_Admin::go_premium(); ?></td>
            </tr>
		</table>
		<input type="submit" class="button-primary" style="margin-top: 1.5rem;" value="<?php esc_html_e( 'Save Changes', 'wc-importer-for-danea' ); ?>" disabled>
	</form>
</div>
