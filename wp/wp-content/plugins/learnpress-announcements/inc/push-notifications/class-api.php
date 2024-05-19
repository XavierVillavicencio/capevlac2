<?php
namespace LP_Announcements\Push_Notifications;

class Api {

	const NAMESPACE = 'lp/notifications/v1';

	const MOBILE_NAMESPACE = 'learnpress/notifications/v1';

	protected static $instance = null;

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		$permission_callback = function() {
			return current_user_can( 'manage_options' );
		};

		$mobile_permission_callback = function() {
			$enabled = \LP_Settings::get_option( 'lp_push_notification_enable' );

			if ( $enabled !== 'yes' ) {
				return new WP_Error( 'lp_push_notification_disabled', __( 'Push notification is disabled', 'learnpress-mobile-app' ), array( 'status' => 403 ) );
			}

			return is_user_logged_in();
		};

		register_rest_route(
			self::NAMESPACE,
			'/notifications',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_notifications' ),
				'permission_callback' => $permission_callback,
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/notifications/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_notification' ),
				'permission_callback' => $permission_callback,
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/delete',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'delete_notification' ),
				'permission_callback' => $permission_callback,
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/save-notifications',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_notifications' ),
				'permission_callback' => $permission_callback,
				'args'                => array(
					'id' => array(
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
					'name' => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_title',
					),
					'title'    => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'content'     => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'media'    => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'type'    => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'source' => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'reminder' => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'receivers' => array(
						'type'              => 'array',
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/send-notification',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'send_notification' ),
				'permission_callback' => $permission_callback,
			)
		);

		register_rest_route(
			self::MOBILE_NAMESPACE,
			'/notifications',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_mobile_notifications' ),
				'permission_callback' => $mobile_permission_callback,
			)
		);
	}

	public function get_mobile_notifications( $request ) {
		$per_page = ! empty( $request->get_param( 'per_page' ) ) ? absint( $request->get_param( 'per_page' ) ) : 10;
		$page     = ! empty( $request->get_param( 'page' ) ) ? absint( $request->get_param( 'page' ) ) : 1;
		$status   = ! empty( $request->get_param( 'status' ) ) ? sanitize_text_field( $request->get_param( 'status' ) ) : 'completed';
		$s        = ! empty( $request->get_param( 'search' ) ) ? sanitize_text_field( $request->get_param( 'search' ) ) : '';
		$type	 = ! empty( $request->get_param( 'type' ) ) ? sanitize_text_field( $request->get_param( 'type' ) ) : '';

		try {
			$user = wp_get_current_user();

			if ( ! $user ) {
				throw new \Exception( __( 'User not found', 'learnpress-announcements' ) );
			}

			$notifications = Database::instance()->get_notifications(
				array(
					'per_page' => $per_page,
					'page'     => $page,
					'status'   => $status,
					's'        => $s,
					'type'     => $type,
					'user'     => $user,
				)
			);

			if ( ! empty( $notifications['notifications'] ) ) {
				// Convert date_created to current timezone use get_date_from_gmt.
				foreach ( $notifications['notifications'] as $key => $notification ) {
					$notifications['notifications'][ $key ]->date_created = get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $notification->date_created ) ), 'Y-m-d H:i:s' );
				}
			}

			return rest_ensure_response( array( 'success' => true, 'data' => $notifications ) );
		} catch (\Throwable $th) {
			return rest_ensure_response( array( 'success' => false, 'message' => $th->getMessage() ) );
		}
	}

	public function send_notification( $request ) {
		$notification_id = ! empty( $request['id'] ) ? absint( $request['id'] ) : 0;

		try {
			if ( empty( $notification_id ) ) {
				throw new \Exception( __( 'Notification id is required', 'learnpress-announcements' ) );
			}

			// Get all users.
			$user_ids = Database::instance()->get_user_ids_by_notification_id( $notification_id, false );

			if ( empty( $user_ids ) ) {
				throw new \Exception( __( 'No user found', 'learnpress-announcements' ) );
			}

			// Update date reminder.
			Database::instance()->update_notification( $notification_id, array( 'date_reminder' => null, 'status' => 'completed' ) );

			// send notification to mobile devices.
			$send = $this->push_notifications( $user_ids, $notification_id, $request );

			if ( is_wp_error( $send ) ) {
				throw new \Exception( $send->get_error_message() );
			}

			return rest_ensure_response( array( 'success' => true, 'message' => __( 'Send notification successfully', 'learnpress-announcements' ) ) );
		} catch (\Throwable $th) {
			return rest_ensure_response( array( 'success' => false, 'message' => $th->getMessage() ) );
		}
	}

	public function push_notifications( $user_ids, $notification_id, $request ) {
		if ( ! class_exists( '\LP_Mobile_Push_Notifications_Database' ) ) {
			return new \WP_Error( 'lp_push_notification_database_not_found', __( 'Database not found, Please activate LearnPress Mobile plugin', 'learnpress-announcements' ), array( 'status' => 403 ) );
		}

		$database = \LP_Mobile_Push_Notifications_Database::instance();

		if ( empty( $user_ids ) ) {
			$device_tokens = $database->get_all_device_tokens();
		} else {
			// Get device_tokens by user_ids
			$device_tokens = $database->get_device_tokens_by_user_ids( $user_ids );
		}

		if ( empty( $device_tokens ) ) {
			return new \WP_Error( 'lp_push_notification_no_device_token', __( 'No device token', 'learnpress-announcements' ), array( 'status' => 403 ) );
		}

		$enabled = \LP_Settings::get_option( 'lp_push_notification_enable' );

		if ( $enabled !== 'yes' ) {
			return new \WP_Error( 'lp_push_notification_disabled', __( 'Push notification is disabled', 'learnpress-announcements' ), array( 'status' => 403 ) );
		}

		$notification_db = Database::instance()->get_notification( $notification_id );

		$notification = array(
			'title' => $notification_db->title ?? '',
			'body'  => $notification_db->content,
		);

		$this->request_send_push_notifications( $database, $device_tokens, $notification, $request );

		return true;
	}

	private function request_send_push_notifications( $database, $device_tokens, $notification, $request ) {
		$platform = ! empty( $request->get_header( 'x-platform' ) ) ? sanitize_text_field( $request->get_header( 'x-platform' ) ) : '';
		$project_id   = function_exists( 'learnpress_mobile_push_notification_settings' ) ? learnpress_mobile_push_notification_settings( $platform )['project_id'] : '';
		$access_token = learnpress_push_notifications_get_access_token( $platform );

		$buffer = '';

		foreach ( $device_tokens as $device_token ) {
			$buffer .= '--subrequest_boundary';
			$buffer .= "\n";

			$buffer .= 'Content-Type: application/http';
			$buffer .= "\n";
			$buffer .= 'Content-Transfer-Encoding: binary';
			$buffer .= "\n";
			$buffer .= "\n";

			$buffer .= 'POST /v1/projects/' . $project_id . '/messages:send';
			$buffer .= "\n";
			$buffer .= 'Content-Type: application/json';
			$buffer .= "\n";
			$buffer .= 'accept: application/json';
			$buffer .= "\n";
			$buffer .= "\n";

			$buffer .= json_encode(
				array(
					'message' => array(
						'token'        => $device_token,
						'notification' => $notification,
						'apns'         => array(
							'payload' => array(
								'aps' => array(
									'content_available' => true,
									'mutable_content'  => true,
								),
							),
							'headers' => array(
								'apns-priority' => '5',
							),
						),
						'android' => array(
							'priority' => 'high',
						),
					),
				)
			);

			$buffer .= "\n";
		}

		$buffer .= '--subrequest_boundary--';

		$headers = array(
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type'  => 'multipart/mixed; boundary="subrequest_boundary"',
		);

		$request = wp_remote_post(
			'https://fcm.googleapis.com/batch',
			array(
				'headers' => $headers,
				'body'    => trim( $buffer ),
			)
		);
		// ! Not use delete device token because when device offline it will be deleted - Nhamdv
		// $request_body   = wp_remote_retrieve_body( $request );
		// $request_header = wp_remote_retrieve_headers( $request );

		// $response = $this->push_notifications_parse_response( $request_header['content-type'], $request_body );

		// $device_error = array();

		// if ( ! empty( $response ) ) {
		// 	foreach ( $response as $key => $item ) {
		// 		if ( isset( $item['error'] ) ) {
		// 			$device_error[] = $device_tokens[ $key ];
		// 		}
		// 	}
		// }

		// if ( ! empty( $device_error ) ) {
		// 	$database->delete_device_token_by_tokens( $device_error );
		// }
	}

	private function push_notifications_parse_response( $content_type, $body ) {
		$content_type = explode( ';', $content_type );
		$boundary     = false;

		foreach ( $content_type as $part ) {
			$part = explode( '=', $part, 2 );

			if ( isset( $part[0] ) && 'boundary' == trim( $part[0] ) ) {
				$boundary = $part[1];
			}
		}

		$body = (string) $body;
		if ( ! empty( $body ) ) {
			$body      = str_replace( "--$boundary--", "--$boundary", $body );
			$parts     = explode( "--$boundary", $body );
			$responses = array();

			foreach ( $parts as $i => $part ) {
				$part = trim( $part );

				if ( ! empty( $part ) ) {
					list( $raw_headers, $part) = explode( "\r\n\r\n", $part, 2 );

					$status = substr( $part, 0, strpos( $part, "\n" ) );
					$status = explode( ' ', $status );
					$status = $status[1];

					$response_body = explode( "\r\n\r\n", $part, 2 );
					$response_body = isset( $response_body[1] ) ? $response_body[1] : null;

					$responses[] = json_decode( $response_body, true );
				}
			}

			return $responses;
		}

		return null;
	}

	public function get_notifications( $request ) {
		$per_page = ! empty( $request->get_param( 'per_page' ) ) ? absint( $request->get_param( 'per_page' ) ) : 10;
		$page     = ! empty( $request->get_param( 'page' ) ) ? absint( $request->get_param( 'page' ) ) : 1;
		$status   = ! empty( $request->get_param( 'status' ) ) ? sanitize_text_field( $request->get_param( 'status' ) ) : '';
		$s        = ! empty( $request->get_param( 'search' ) ) ? sanitize_text_field( $request->get_param( 'search' ) ) : '';
		$type	 = ! empty( $request->get_param( 'type' ) ) ? sanitize_text_field( $request->get_param( 'type' ) ) : '';
		$user = ! empty( $request->get_param( 'user' ) ) ? sanitize_text_field( $request->get_param( 'user' ) ) : '';

		try {
			$notifications = Database::instance()->get_notifications(
				array(
					'per_page' => $per_page,
					'page'     => $page,
					'status'   => $status,
					's'        => $s,
					'type'     => $type,
					'user'     => $user,
				)
			);

			// Convert date_reminder to current timezone use get_date_from_gmt.
			foreach ( $notifications['notifications'] as $key => $notification ) {
				if ( $notifications['notifications'][ $key ]->date_reminder ) {
					$notifications['notifications'][ $key ]->date_reminder = get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $notification->date_reminder ) ), 'Y-m-d H:i:s' );
				}
			}

			return rest_ensure_response( array( 'success' => true, 'data' => $notifications ) );
		} catch (\Throwable $th) {
			return rest_ensure_response( array( 'success' => false, 'message' => $th->getMessage() ) );
		}

		return rest_ensure_response( array( 'success' => true, 'data' => $notifications ) );
	}

	public function get_notification( $request ) {
		$notification_id = ! empty( $request['id'] ) ? absint( $request['id'] ) : 0;

		try {
			$notification = Database::instance()->get_notification( $notification_id );

			// Convert date_reminder to current timezone use get_date_from_gmt.
			if ( ! empty( $notification->date_reminder) ) {
				$notification->date_reminder = get_date_from_gmt( gmdate( 'Y-m-d H:i:s', strtotime( $notification->date_reminder ) ), 'Y-m-d H:i:s' );
			}

			$receivers = Database::instance()->get_receivers( $notification_id );

			$notification->receivers = $receivers;

			return rest_ensure_response( array( 'success' => true, 'data' => $notification ) );
		} catch (\Throwable $th) {
			return rest_ensure_response( array( 'success' => false, 'message' => $th->getMessage() ) );
		}
	}

	public function save_notifications( $request ) {
		try {
			$notification_id = $request->get_param('id');
			$name = $request->get_param('name');
			$title = $request->get_param('title');
			$content = $request->get_param('content');
			$media = $request->get_param('media');
			$type = $request->get_param('type');
			$source = $request->get_param('source');
			$reminder = $request->get_param('reminder');
			$receivers = $request->get_param('receivers');
			$is_send = ! empty( $request->get_param( 'isSend' ) ) ? $request->get_param( 'isSend' ) : false;

			if ( empty( $name ) ) {
				throw new \Exception( __( 'Name is required', 'learnpress-announcements' ) );
			}

			if ( empty( $content ) ) {
				throw new \Exception( __( 'Content is required', 'learnpress-announcements' ) );
			}

			// Check media is image link.
			if ( ! empty( $media ) && ! filter_var( $media, FILTER_VALIDATE_URL ) ) {
				throw new \Exception( __( 'Media is invalid', 'learnpress-announcements' ) );
			}

			// Check is image type.
			if ( ! empty( $media ) && ! in_array( wp_check_filetype( $media )['ext'], array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) {
				throw new \Exception( __( 'Media is invalid. Only accept jpg, jpeg, png, gif', 'learnpress-announcements' ) );
			}

			$data = array(
				'name'        => $name,
				'title'       => $title,
				'content'     => $content,
				'image'       => $media,
				'type'        => $type,
				'source'      => $source,
				'date_reminder' => ! empty( $reminder ) ? get_gmt_from_date(gmdate( 'Y-m-d H:i:s', strtotime( $reminder ) ), 'Y-m-d H:i:s' ) : null,
			);

			// Save notification to database.
			if ( ! empty( $notification_id ) ) {
				$notification_id = Database::instance()->update_notification( $notification_id, $data );

				Database::instance()->delete_notification_receivers( $notification_id );
			} else {
				$notification_id = Database::instance()->create_notification( $data );
			}

			if ( ! $notification_id ) {
				throw new \Exception( __( 'Cannot save notification', 'learnpress-announcements' ) );
			}

			if ( ! empty( $receivers ) ) {
				foreach ( $receivers as $receiver ) {
					Database::instance()->create_notification_receivers( $notification_id, $receiver );
				}
			}

			// delete transients.
			delete_transient( 'learnpress_notification_' . $notification_id );

			// Send push notification to mobile devices.
			if ( $is_send ) {
				// Set param id to send notification.
				$request->set_param( 'id', $notification_id );

				$this->send_notification( $request );
			}

			return rest_ensure_response( array( 'success' => true, 'message' => __( 'Save notification successfully', 'learnpress-announcements' ) ) );

		} catch ( \Throwable $th ) {
			return rest_ensure_response( array( 'success' => false, 'message' => $th->getMessage() ) );
		}
	}

	public function delete_notification( $request ) {
		try {
			$notification_id = ! empty( $request['id'] ) ? absint( $request['id'] ) : 0;

			if ( empty( $notification_id ) ) {
				throw new \Exception( __( 'Notification id is required', 'learnpress-announcements' ) );
			}

			Database::instance()->delete_notification( $notification_id );

			// Delete notification receivers.
			Database::instance()->delete_notification_receivers( $notification_id );

			// delete transients.
			delete_transient( 'learnpress_notification_' . $notification_id );

			return rest_ensure_response( array( 'success' => true, 'message' => __( 'Delete notification successfully', 'learnpress-announcements' ) ) );

		} catch ( \Throwable $th ) {
			return rest_ensure_response( array( 'success' => false, 'message' => $th->getMessage() ) );
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
Api::instance();
