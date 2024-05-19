<?php
namespace LP_Announcements\Push_Notifications;

class Init {

	private static $instance = null;

	public function __construct() {
		$this->includes();

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function includes() {
		require_once LP_ANNOUNCEMENTS_INC . 'push-notifications/class-enqueue.php';
		require_once LP_ANNOUNCEMENTS_INC . 'push-notifications/class-database.php';
		require_once LP_ANNOUNCEMENTS_INC . 'push-notifications/class-api.php';
		require_once LP_ANNOUNCEMENTS_INC . 'push-notifications/class-cronjob.php';
	}

	public function add_admin_menu() {
		add_submenu_page(
			'learn_press',
			esc_html__( 'Notifications', 'learnpress-announcements' ),
			esc_html__( 'Notifications', 'learnpress-announcements' ),
			'manage_options',
			'learnpress-push-notifications',
			function() {
				echo '<div id="learnpress-push-notifications-root"></div>';
			},
			6
		);
	}

	// instance.
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
Init::instance();
