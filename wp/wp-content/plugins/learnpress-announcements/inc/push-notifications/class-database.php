<?php
namespace LP_Announcements\Push_Notifications;

class Database {

	protected static $instance = null;

	public function get_notifications( $args ) {
		global $wpdb;

		$defaults = array(
			'per_page' => 10,
			'page'     => 1,
			'status'   => '',
			's'        => '',
			'type'     => '',
			'user'     => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$per_page = absint( $args['per_page'] );
		$page     = absint( $args['page'] );
		$status   = sanitize_text_field( $args['status'] );
		$s        = sanitize_text_field( $args['s'] );
		$type     = sanitize_text_field( $args['type'] );
		$user = sanitize_text_field( $args['user'] );

		$offset = ( $page - 1 ) * $per_page;

		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}learnpress_notifications as notifications";

		$where = array();

		if ( $status ) {
			$where[] = $wpdb->prepare( "notifications.status = %s", $status );
		}

		if ( $s ) {
			$where[] = $wpdb->prepare( "notifications.title LIKE %s", '%' . $s . '%' );
		}

		if ( $type ) {
			$where[] = $wpdb->prepare( "notifications.type = %s", $type );
		}

		if ( ! empty( $user ) ) {
			// get user role by user name.
			$user = $user instanceof \WP_User ? $user : get_user_by( 'login', $user );

			// get notification ids by user.
			$notification_ids = $this->get_notifications_by_user( $user );

			$where[] = $wpdb->prepare( "notifications.notification_id IN (" . implode( ',', $notification_ids ) . ")" );
		}

		if ( $where ) {
			$sql .= ' WHERE ' . implode( ' AND ', $where );
		}

		$sql .= " GROUP BY notifications.notification_id";

		$sql .= " ORDER BY notifications.notification_id DESC";

		$sql .= " LIMIT {$offset}, {$per_page}";

		$results = $wpdb->get_results( $sql );

		$total = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

		return array(
			'notifications' => $results,
			'total'         => absint( $total ),
		);
	}

	public function get_user_ids_by_notification_id( $notification_id, $force = false ) {
		// set transient.
		$transient_key = 'learnpress_notification_' . $notification_id;

		$user_ids = get_transient( $transient_key );

		if ( false === $user_ids || $force ) {
			$user_ids = $this->get_user_by_notification_id( $notification_id );

			// 7 days.
			set_transient( $transient_key, $user_ids, 7 * DAY_IN_SECONDS );
		}

		return $user_ids;
	}

	private function get_user_by_notification_id( $notification_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}learnpress_notifications_receivers WHERE notification_id = %d", $notification_id );

		$receivers = $wpdb->get_results( $sql, ARRAY_A );

		$user_ids = array();

		// Filter by comparison include.
		foreach ( $receivers as $receiver ) {
			if ( 'include' === $receiver['comparison'] ) {
				// Filter by type.
				switch ( $receiver['type'] ) {
					case 'all':
						$user_ids = get_users(
							array(
								'fields' => 'ID',
							)
						);
						break;
					case 'role':
						$user_ids = array_merge(
							$user_ids,
							get_users(
								array(
									'fields'  => 'ID',
									'role'    => $receiver['value'],
								)
							)
						);
						break;
					case 'user':
						$user = get_user_by( 'login', $receiver['value'] );

						if ( $user ) {
							$user_ids[] = $user->ID;
						}
						break;
				}
			}
		}

		// Filter by comparison exclude.
		foreach ( $receivers as $receiver ) {
			if ( 'exclude' === $receiver['comparison'] ) {
				// Filter by type.
				switch ( $receiver['type'] ) {
					case 'all':
						$user_ids = array();
						break;
					case 'role':
						$user_ids = array_diff(
							$user_ids,
							get_users(
								array(
									'fields'  => 'ID',
									'role'    => $receiver['value'],
								)
							)
						);
						break;
					case 'user':
						$user = get_user_by( 'login', $receiver['value'] );

						if ( $user ) {
							$user_ids = array_diff( $user_ids, array( $user->ID ) );
						}
						break;
				}
			}
		}

		// remove all duplicate user id.
		$user_ids = array_unique( $user_ids );

		return array_values( $user_ids );
	}

	public function get_notifications_by_user( $user ) {
		// get notification ids include by user.
		$include_user_notification_ids = $this->get_include_notifications_by_user( $user );

		// get notification ids exclude by user.
		$exclude_user_notification_ids = $this->get_exclude_notifications_by_user( $user );

		return array_diff( $include_user_notification_ids, $exclude_user_notification_ids );
	}

	private function get_include_notifications_by_user( $user ) {
		global $wpdb;

		// select notification_id from table learnpress_notifications_receivers not loop.
		$sql = $wpdb->prepare( "SELECT DISTINCT notification_id FROM {$wpdb->prefix}learnpress_notifications_receivers" );

		// where.
		$where = array();

		// all comparison include and type all.
		$where[] = $wpdb->prepare( "(comparison = %s AND type = %s)", 'include', 'all' );

		// all comparison include and type role and value $user_role.
		$where[] = $wpdb->prepare( "(comparison = %s AND type = %s AND value = %s)", 'include', 'role', $user->roles[0] );

		// all comparison include and type user and value user login name.
		$where[] = $wpdb->prepare( "(comparison = %s AND type = %s AND value = %s)", 'include', 'user', $user->user_login );

		$sql .= ' WHERE ' . implode( ' OR ', $where );

		$notification_ids = $wpdb->get_col( $sql );

		return $notification_ids;
	}

	private function get_exclude_notifications_by_user( $user ) {
		global $wpdb;

		// select notification_id from table learnpress_notifications_receivers not loop.
		$sql = $wpdb->prepare( "SELECT DISTINCT notification_id FROM {$wpdb->prefix}learnpress_notifications_receivers" );

		// where.
		$where = array();

		// all comparison exclude and type all.
		$where[] = $wpdb->prepare( "(comparison = %s AND type = %s)", 'exclude', 'all' );

		// all comparison exclude and type role and value $user_role.
		$where[] = $wpdb->prepare( "(comparison = %s AND type = %s AND value = %s)", 'exclude', 'role', $user->roles[0] );

		// all comparison exclude and type user and value user login name.
		$where[] = $wpdb->prepare( "(comparison = %s AND type = %s AND value = %s)", 'exclude', 'user', $user->user_login );

		$sql .= ' WHERE ' . implode( ' OR ', $where );

		$notification_ids = $wpdb->get_col( $sql );

		return $notification_ids;
	}

	public function get_notification( $notification_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}learnpress_notifications WHERE notification_id = %d", $notification_id );

		$notification = $wpdb->get_row( $sql );

		return $notification;
	}

	public function get_receivers( $notification_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}learnpress_notifications_receivers WHERE notification_id = %d", $notification_id );

		$receiver = $wpdb->get_results( $sql, ARRAY_A );

		return $receiver;
	}

	/**
	 * Create notification to database learnpress_notifications.
	 */
	public function create_notification( $args ) {
		global $wpdb;

		$defaults = array(
			'name'        => '',
			'title'       => '',
			'content'     => '',
			'image'       => '',
			'type'        => '',
			'status'      => 'pending',
			'source'      => '',
			'date_created' => current_time( 'mysql', true ),
			'date_reminder' => null,
		);

		$args = wp_parse_args( $args, $defaults );

		$wpdb->insert(
			$wpdb->prefix . 'learnpress_notifications',
			array(
				'name'        => $args['name'],
				'type'        => $args['type'],
				'title'       => $args['title'],
				'content'     => $args['content'],
				'image'       => $args['image'],
				'status'      => $args['status'],
				'source'      => $args['source'],
				'date_created' => $args['date_created'],
				'date_reminder' => $args['date_reminder'],
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);

		return $wpdb->insert_id;
	}

	/**
	 * Update notification to database learnpress_notifications.
	 */
	public function update_notification( $notification_id, $args ) {
		global $wpdb;

		$defaults = $this->get_notification( $notification_id );

		$args = wp_parse_args( $args, (array) $defaults );

		$wpdb->update(
			$wpdb->prefix . 'learnpress_notifications',
			array(
				'name'        => $args['name'],
				'type'        => $args['type'],
				'title'       => $args['title'],
				'content'     => $args['content'],
				'image'       => $args['image'],
				'source'      => $args['source'],
				'status'      => $args['status'],
				'date_reminder' => $args['date_reminder'],
			),
			array(
				'notification_id' => $notification_id,
			)
		);

		return $notification_id;
	}

	public function delete_notification( $notification_id ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'learnpress_notifications',
			array(
				'notification_id' => $notification_id,
			),
			array(
				'%d',
			)
		);
	}

	/**
	 * Create notification receivers to database learnpress_notifications_receivers.
	 */
	public function create_notification_receivers( $notification_id, $receivers ) {
		global $wpdb;

		$wpdb->hide_errors();

		$wpdb->insert(
			$wpdb->prefix . 'learnpress_notifications_receivers',
			array(
				'notification_id' => $notification_id,
				'comparison'      => $receivers['comparison'],
				'type'            => $receivers['type'],
				'value'           => $receivers['value'],
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
			)
		);

		return $wpdb->insert_id;
	}

	public function delete_notification_receivers( $notification_id ) {
		global $wpdb;

		$wpdb->delete(
			$wpdb->prefix . 'learnpress_notifications_receivers',
			array(
				'notification_id' => $notification_id,
			),
			array(
				'%d',
			)
		);
	}

	public function get_notification_date_reminder() {
		global $wpdb;

		$now = current_time( 'mysql', true );

		$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}learnpress_notifications WHERE status = 'pending' AND date_reminder <= %s", $now );

		$notifications = $wpdb->get_results( $sql, ARRAY_A );

		return $notifications;
	}

	// instance.
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
