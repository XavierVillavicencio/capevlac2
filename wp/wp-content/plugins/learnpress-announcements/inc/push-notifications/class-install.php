<?php
namespace LP_Announcements\Push_Notifications;

defined( 'ABSPATH' ) || exit;

class Install {

	private static $instance = null;

	public function __construct() {
		// Register activation hook.
		register_activation_hook( LP_ADDON_ANNOUNCEMENTS_FILE, array( $this, 'install' ) );
	}

	public function install() {
		$this->create_table_notifications();
	}

	private function create_table_notifications() {
		global $wpdb;

		$wpdb->hide_errors();

		$table_name = $wpdb->prefix . 'learnpress_notifications';

		$charset_collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$charset_collate = $wpdb->get_charset_collate();
		}

		// Create table save device.
		$sql = "CREATE TABLE $table_name (
			notification_id bigint(20) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			type varchar(20) NOT NULL,
			title text NULL default NULL,
			content text NOT NULL,
			image varchar(255) NULL default NULL,
			status varchar(20) NOT NULL,
			source varchar(255) NULL default NULL,
			date_created datetime NOT NULL,
			date_reminder datetime NULL default NULL,
			PRIMARY KEY  (notification_id)
			) $charset_collate;";

			/*
			* Data save receiver: {comparison: "include", type: "all", value: "instructor"}, {comparison: "exclude", type: "role", value: "instructor"}, {comparison: "include", type: "user", value: "instructor2"}
			* Create table save data receiver with notification_id, comparison, type, value
			*/
			$sql .= "CREATE TABLE {$wpdb->prefix}learnpress_notifications_receivers (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				notification_id bigint(20) NOT NULL,
				comparison varchar(20) NOT NULL,
				type varchar(20) NOT NULL,
				value varchar(255) NOT NULL,
				PRIMARY KEY  (id)
				) $charset_collate;";


		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );
	}

	// instance.
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
Install::instance();
