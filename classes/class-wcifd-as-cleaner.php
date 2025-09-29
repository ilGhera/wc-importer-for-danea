<?php
/**
 * Action Scheduler Cleaner
 *
 * @author ilGhera
 * @package wc-importer-for-danea-premium/admin
 *
 * @since 1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WCIFD_AS_Cleaner
 *
 * This class handles the custom cleanup of Action Scheduler actions
 * based on plugin settings, and schedules a custom cron job to perform this cleanup automatically.
 *
 * @since 1.7.0
 */
class WCIFD_AS_Cleaner {

	/**
	 * The options saved by the admin
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Define the custom cron hook for daily cleanup.
	 *
	 * @var string
	 */
	const CRON_HOOK = 'wcifd_as_daily_cleanup';

	/**
	 * The constructor
	 *
	 * Initializes the cleaner by loading options and setting up WordPress hooks.
	 *
	 * @return void
	 */
	public function __construct() {

		$this->options = get_option( 'wcifd-tools' );
		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks.
	 *
	 * Sets up Action Scheduler filters and registers custom cron events.
	 *
	 * @return void
	 */
	private function init_hooks() {

		/* Action Scheduler filters */
		add_filter( 'action_scheduler_retention_period', array( $this, 'set_custom_retention_period' ) );
		add_filter( 'action_scheduler_logger_retention_period', array( $this, 'set_custom_retention_period' ) );
		add_filter( 'action_scheduler_default_cleaner_statuses', array( $this, 'add_custom_cleaner_statuses' ) );
		add_filter( 'action_scheduler_cleanup_batch_size', array( $this, 'set_cleanup_batch_size' ) );

		/* Custom Cron Hooks for automatic cleanup */
		add_action( 'wp', array( $this, 'schedule_cleanup_cron' ) );

		/* Register the function to be executed when the custom cron hook fires. */
		add_action( self::CRON_HOOK, array( $this, 'run_custom_cleanup' ) );
	}

	/**
	 * Sets the custom retention period for Action Scheduler.
	 * Retrieves the number of days from the plugin settings to determine
	 * how long completed, canceled, or failed actions are kept before being deleted.
	 *
	 * @param int $period The default retention period (in seconds).
	 *
	 * @return int The new retention period (in seconds).
	 */
	public function set_custom_retention_period( $period ) {

		$days = isset( $this->options['as-cleaner-days'] ) ? absint( $this->options['as-cleaner-days'] ) : 30;

		if ( $days < 1 ) {
			$days = 1; // Ensure a minimum of 1 day retention.
		}

		return $days * DAY_IN_SECONDS;
	}

	/**
	 * Adds extra statuses (failed, canceled) for Action Scheduler cleanup.
	 * Plugin settings determine which additional statuses should be cleaned besides 'complete'.
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

		return $statuses;
	}

	/**
	 * Sets the cleanup batch size for Action Scheduler.
	 * This determines how many actions are processed in a single cleanup run.
	 * A larger batch size can speed up cleanup but might hit PHP execution limits.
	 *
	 * @param int $size The default batch size.
	 *
	 * @return int The new batch size.
	 */
	public function set_cleanup_batch_size( $size ) {

		return 500;
	}

	/**
	 * Schedules the custom daily cleanup cron job.
	 * This function checks if the cron event is already scheduled and
	 * schedules it if it's not found. It runs on the 'wp' hook
	 * to ensure WordPress is fully loaded.
	 *
	 * @return void
	 */
	public function schedule_cleanup_cron() {

		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {

			/* Schedule the event to run daily, starting from the current time. */
			wp_schedule_event( time(), 'daily', self::CRON_HOOK );
		}
	}

	/**
	 * Function executed by the custom cron job.
	 * This method directly calls Action Scheduler's core cleanup routine.
	 * All filters defined in this class (retention, statuses, batch size)
	 * will automatically be respected by the cleaner when it runs.
	 * It also includes a specific cleanup for orphaned or uncleaned logs.
	 *
	 * @return void
	 */
	public function run_custom_cleanup() {

		global $wpdb;

		error_log( '=== WCIFD | Starting Action Scheduler cleanup =======================' );

		/**
		 * Standard Action Scheduler cleanup for actions and their related logs.
		 * This uses the filters set by this class.
		 */
		$cleaner       = new ActionScheduler_QueueCleaner();
		$cleaned_count = $cleaner->clean();
		error_log( 'ActionScheduler_QueueCleaner completato. Azioni rimosse: ' . $cleaned_count );

		/**
		 * Attempt to clean older logs using Action Scheduler's dedicated logger cleanup.
		 * This function respects the 'action_scheduler_logger_retention_period' filter.
		 * It might be redundant if the main cleaner already covers all cases, but it's a good explicit call.
		 */
		if ( class_exists( 'ActionScheduler_Logger' ) && method_exists( 'ActionScheduler_Logger', 'delete_old_logs' ) ) {
			ActionScheduler_Logger::delete_old_logs();
			error_log( 'Tentativo di pulizia diretta dei log tramite ActionScheduler_Logger (pulizia standard dei log)' );
		}

		/**
		 * Fallback: Forced cleanup for "orphaned" or persistent old logs.
		 * This addresses logs that might not be removed by standard AS cleanup,
		 * typically due to missing or invalid action IDs.
		 */
		$retention_seconds = apply_filters( 'action_scheduler_logger_retention_period', DAY_IN_SECONDS );

		// Ensure a minimum of 1 day retention for safety in production environments.
		if ( $retention_seconds < DAY_IN_SECONDS ) {
			$retention_seconds = DAY_IN_SECONDS;
		}

		$cutoff_date = gmdate( 'Y-m-d H:i:s', time() - $retention_seconds );
		$table_name  = $wpdb->prefix . 'actionscheduler_logs';
		$batch_size  = $this->set_cleanup_batch_size( 0 );

		/**
		 * Execute a direct DELETE query for old logs based on log_date_gmt.
		 * This query specifically targets logs older than the retention period,
		 * providing a robust cleanup for any logs not covered by AS's internal logic.
		 */
		$deleted_orphaned_logs = $wpdb->query(
			$wpdb->prepare(
				'DELETE FROM ' . $wpdb->prefix . 'actionscheduler_logs WHERE `log_date_gmt` < %s LIMIT %d',
				$cutoff_date,
				$batch_size
			)
		);

		if ( false !== $deleted_orphaned_logs ) {
			error_log( 'Pulizia forzata dei log orfani/vecchi completata. Righe eliminate:' . $deleted_orphaned_logs );
		} else {
			error_log( 'Errore durante la pulizia forzata dei log orfani/vecchi: ' . $wpdb->last_error );
		}
		error_log( '=== WCIFD | Action Scheduler cleanup terminata =======================' );
	}

	/**
	 * Static method to be called upon plugin deactivation.
	 * This ensures that the scheduled custom cron job is properly
	 * unscheduled when the plugin is deactivated, preventing orphaned cron events.
	 *
	 * @return void
	 */
	public static function deactivate_plugin() {

		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
		}
	}
}

