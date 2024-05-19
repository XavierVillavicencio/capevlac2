<?php
namespace LP_Announcements\Push_Notifications;


// Cronjob class
class Cronjob {

	protected static $instance = null;

	// Constructor
	public function __construct() {
		// Cronjob
		add_action( 'init', array( $this, 'cronjob' ) );

		// Add cron schedule
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );

		// Clear cronjob after deactivation
		register_deactivation_hook( LP_ADDON_ANNOUNCEMENTS_FILE, array( $this, 'clear_cronjob' ) );

		// Cronjob for sending notifications
		add_action( 'lp_announcements_cronjob_get_notifications_date_reminder', array( $this, 'get_notifications_date_reminder' ) );
	}

	public function clear_cronjob() {
		// Clear cronjob
		wp_clear_scheduled_hook( 'lp_announcements_cronjob_get_notifications_date_reminder' );
	}

	public function cronjob() {
		// Cronjob for sending notifications
		if ( ! wp_next_scheduled( 'lp_announcements_cronjob_get_notifications_date_reminder' ) ) {
			wp_schedule_event( time(), 'LPPushNotification', 'lp_announcements_cronjob_get_notifications_date_reminder' );
		}
	}

	public function add_cron_schedule( $schedules ) {
		// Once 30 minutes
		$schedules['LPPushNotification'] = array(
			'interval' => 1800,
			'display'  => __( 'Once 30 minutes', 'learnpress-announcements' ),
		);

		return $schedules;
	}

	public function get_notifications_date_reminder() {
		$database = Database::instance();

		// Get all notifications with status 'publish' and type 'date_reminder <= current date' and status 'pending'
		$results = Database::instance()->get_notification_date_reminder();

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				// Send notification
				$request = new \WP_REST_Request();
				$request->set_method( 'POST' );
				$request->set_body_params(
					array(
						'id' => $result['notification_id'],
					)
				);

				// Send notification
				Api::instance()->send_notification( $request );
			}
		}
	}

	// Instance
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
	}
}

Cronjob::instance();
