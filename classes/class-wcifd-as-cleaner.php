<?php
/**
 * Action Scheduler Cleaner
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_Admin
 *
 * @since 1.6.0
 */
class WCIFD_AS_Cleaner {

	/**
	 * The options saved by the admin
	 *
	 * @var array
	 */
	private $options;

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->options = get_option( 'wcifd-tools' );
		$this->init_hooks();
        error_log('WCIFD_AS_Cleaner - CONSTRUCTOR: Initial options loaded: ' . print_r($this->options, true)); // Log qui
	}

	/**
	 * Initialize WordPress hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {

		/* ActionScheduler filters */
		add_filter( 'action_scheduler_retention_period', array( $this, 'set_custom_retention_period' ) );
		add_filter( 'action_scheduler_default_cleaner_statuses', array( $this, 'add_custom_cleaner_statuses' ) );

	}

	/**
	 * Sets the custom retention period for Action Scheduler.
	 * Retrieves the number of days from the plugin settings.
	 *
	 * @param int $period The default retention period (in seconds).
	 *
	 * @return int The new retention period (in seconds).
	 */
	public function set_custom_retention_period( $period ) {

		$days = isset( $this->options['as-cleaner-days'] ) ? absint( $this->options['as-cleaner-days'] ) : 30;

		if ( $days < 1 ) {
			$days = 1;
		}

        error_log('WCIFD_AS_Cleaner - FILTER: action_scheduler_retention_period set to: ' . $days . ' days (' . ($days * DAY_IN_SECONDS) . ' seconds).'); // Log qui
		return $days * DAY_IN_SECONDS;
	}

	/**
	 * Adds extra statuses (failed, canceled) for Action Scheduler cleanup.
	 * Settings determine which statuses to add.
	 *
	 * @param array $statuses Array of default statuses to clean.
	 *
	 * @return array Array of updated statuses.
	 */
	public function add_custom_cleaner_statuses( $statuses ) {

		$clean_failed   = isset( $this->options['as-clean-failed'] ) ? (bool) $this->options['as-clean-failed'] : true;
		$clean_canceled = isset( $this->options['as-clean-canceled'] ) ? (bool) $this->options['as-clean-canceled'] : true;

		if ( $clean_failed && ! in_array( 'failed', $statuses, true ) ) {
			$statuses[] = 'failed';
		}
		if ( $clean_canceled && ! in_array( 'canceled', $statuses, true ) ) {
			$statuses[] = 'canceled';
		}

        error_log('WCIFD_AS_Cleaner - FILTER: action_scheduler_default_cleaner_statuses set to: ' . implode(', ', $statuses)); // Log qui
		return $statuses;
	}
}

new WCIFD_AS_Cleaner();

