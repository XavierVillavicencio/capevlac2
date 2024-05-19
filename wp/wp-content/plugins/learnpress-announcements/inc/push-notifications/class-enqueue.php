<?php
namespace LP_Announcements\Push_Notifications;

class Enqueue {

	protected static $instance = null;

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();

		if ( strpos( $screen->id, 'learnpress-push-notifications' ) !== false ) {
			wp_enqueue_media();

			$file_info = include LP_ANNOUNCEMENTS_PATH . '/build/push-notifications.asset.php';
			wp_enqueue_script( 'learnpress-push-notifications', LP_ADDON_ANNOUNCEMENTS_URI . 'build/push-notifications.js', $file_info['dependencies'], $file_info['version'], true );
			wp_enqueue_style( 'learnpress-push-notifications', LP_ADDON_ANNOUNCEMENTS_URI . 'build/push-notifications.css', array(), $file_info['version'] );

			// add localize.
			wp_localize_script(
				'learnpress-push-notifications',
				'learnpressPushNotificationsSettings',
				apply_filters( 'learnpress_push_notifications_settings',
					array(
						'nonce'   => wp_create_nonce( 'learnpress-push-notifications' ),
						'isMutilPlatform' => false,
					)
				)
			);
		}
	}

	// instance.
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
Enqueue::instance();
