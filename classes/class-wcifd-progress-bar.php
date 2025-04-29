<?php
/**
 * Progress bar products import
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/classes
 *
 * @since 1.6.1
 */

/**
 * Class WCIFD_Progress_Bar
 */
class WCIFD_Progress_Bar {

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_notices', array( $this, 'catalog_update_admin_notice' ) );
		add_action( 'wp_ajax_get-total-actions', array( $this, 'get_total_actions' ) );
		add_action( 'wp_ajax_get-scheduled-actions', array( $this, 'get_scheduled_actions' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( 'woocommerce_page_wc-importer-for-danea' === $screen->id ) {

			wp_enqueue_script( 'wcifd-progress-bar', WCIFD_URI . 'js/wcifd-progress-bar.js', array( 'jquery' ), WCIFD_VERSION, true );

			$options = array(
				'completedMessage'       => __( 'Products import was completed!', 'wc-importer-for-danea' ),
				'deleteMessage'          => __( 'Products delete is running.', 'wc-importer-for-danea' ),
				'completedDeleteMessage' => __( 'Products delete was completed!', 'wc-importer-for-danea' ),
			);

			wp_localize_script( 'wcifd-progress-bar', 'options', $options );
		}
	}

	/**
	 * Get the total number of actions scheduled
	 *
	 * @return void
	 */
	public function get_total_actions() {

		$import = intval( get_transient( 'wcifd-total-actions' ) );
		$delete = intval( get_transient( 'wcifd-total-delete-actions' ) );
		$nonce  = wp_create_nonce( 'wcifd-get-actions' );

		echo wp_json_encode(
			array(
				'import' => $import,
				'delete' => $delete,
				'nonce'  => $nonce,
			)
		);

		exit;
	}

	/**
	 * Get the actions pending
	 *
	 * @return void
	 */
	public function get_scheduled_actions() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( wp_verify_nonce( $nonce, 'wcifd-get-actions' ) ) {

			$del   = isset( $_POST['delete'] ) ? sanitize_text_field( wp_unslash( $_POST['delete'] ) ) : 0;
			$hook  = $del ? 'wcifd_delete_product_event' : 'wcifd_import_product_event';
			$group = $del ? 'wcifd-delete-product' : 'wcifd-import-product';

			$actions = as_get_scheduled_actions(
				array(
					'hook'     => $hook,
					'group'    => $group,
					'status'   => ActionScheduler_Store::STATUS_PENDING,
					'per_page' => -1,
				),
				'ids'
			);

			if ( 0 === count( $actions ) ) {

				$action = $del ? 'wcifd-total-delete-actions' : 'wcifd-total-actions';
				delete_transient( $action );
			}

			echo intval( count( $actions ) );
		}

		exit;
	}

	/**
	 * The progress bar as admin notice
	 *
	 * @return void
	 */
	public function catalog_update_admin_notice() {

		$screen = get_current_screen();

		if ( 'woocommerce_page_wc-importer-for-danea' === $screen->id ) {

			$output              = '<div class="update-nag notice notice-warning ilghera-notice-warning catalog-update is-dismissible">';
				$output         .= '<div class="ilghera-notice__content">';
					$output     .= '<div class="ilghera-notice__message">';
					$output     .= '<b>' . esc_html__( 'WC Importer for Danea', 'wc-importer-for-danea' ) . '</b> - ';
					$output     .= '<span class="wcifd-progress-bar-text">' . esc_html__( 'Products import is running.', 'wc-importer-for-danea' ) . '</span>';
					$output     .= '</div>';
					$output     .= '<div id="wcifd-progress-bar">';
						$output .= '<div id="wcifd-progress"></div>';
						$output .= '<span class="precentage">0%</span>';
					$output     .= '</div>';
				$output         .= '</div>';
			$output             .= '</div>';

			echo wp_kses_post( $output );
		}
	}
}
new WCIFD_Progress_Bar();

