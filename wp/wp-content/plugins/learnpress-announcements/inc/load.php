<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Announcements/Classes
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Announcements' ) ) {
	/**
	 * Class LP_Addon_Announcements.
	 */
	class LP_Addon_Announcements extends LP_Addon {

		/**
		 * @var string
		 */
		public $version = LP_ADDON_ANNOUNCEMENTS_VER;

		/**
		 * @var string
		 */
		public $require_version = LP_ADDON_ANNOUNCEMENTS_REQUIRE_VER;

		/**
		 * Path file addon.
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_ANNOUNCEMENTS_FILE;

		/**
		 * @var null
		 */
		protected static $_instance = null;

		/**
		 * LP_Addon_Announcements constructor.
		 */
		public function __construct() {
			parent::__construct();

			// Email settings
			add_action( 'plugins_loaded', [ $this, 'emails_setting' ] );
			// Email group
			add_filter( 'learn-press/email-section-classes', [ $this, 'add_email_group' ] );
		}

		/**
		 * Define Learnpress Announcement constants.
		 *
		 * @since 3.0.0
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_ANNOUNCEMENTS_URI', plugins_url( '/', LP_ADDON_ANNOUNCEMENTS_FILE ) );
			define( 'LP_ANNOUNCEMENTS_INC', LP_ANNOUNCEMENTS_PATH . '/inc/' );
			define( 'LP_ANNOUNCEMENTS_TEMPLATE', LP_ANNOUNCEMENTS_PATH . '/templates/' );
			define( 'LP_ANNOUNCEMENTS_CPT', 'lp_announcements' );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 3.0.0
		 */
		protected function _includes() {
			include_once LP_ANNOUNCEMENTS_INC . 'functions.php';
			include_once LP_ANNOUNCEMENTS_INC . 'class-lp-announcements-post-type.php';
			include_once LP_ANNOUNCEMENTS_INC . 'push-notifications/class-init.php';
		}

		/**
		 * Init hooks.
		 */
		protected function _init_hooks() {
			// Metaboxes in LP4.
			add_filter(
				'lp_course_data_settings_tabs',
				function ( $tabs ) {
					$tabs['course_announcements'] = array(
						'label'    => esc_html__( 'Announcements', 'learnpress' ),
						'target'   => 'announcements_course_data',
						'icon'     => 'dashicons-megaphone',
						'priority' => 60,
					);

					return $tabs;
				}
			);

			add_action( 'lp_course_data_setting_tab_content', array( $this, '_add_course_meta_content' ) );
			add_action( 'learnpress_save_lp_course_metabox', array( $this, '_save_meta_box' ), 10 );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Ajax
			add_action( 'wp_ajax_lp_announcements_lists_course', array( __CLASS__, 'lp_announcements_lists_course' ) );
			add_action( 'wp_ajax_lp_create_announcement', [ $this, 'lp_create_announcement' ] );
			add_action( 'wp_ajax_lp_remove_announcement', array( __CLASS__, 'ajax_remove_announcement' ) );
			add_action( 'wp_ajax_send_mail_announcements', [ $this, 'send_mail_announcements' ] );

			/* Render Frontend */
			add_filter( 'learn-press/course-tabs', array( $this, 'add_single_course_announcements_tab' ), 5 );
			add_filter( 'comment_post_redirect', array( $this, 'announcement_comment_post_redirect' ), 100, 2 );
			add_action( 'learn-press/frontend-editor/enqueue', array( $this, 'frontend_editor_enqueue' ) );
		}

		public function frontend_editor_enqueue() {
			wp_enqueue_style(
				'lp-announcements-editor-css',
				LP_ADDON_ANNOUNCEMENTS_URI . 'assets/css/admin.announcements.css',
				array(),
				LP_ADDON_ANNOUNCEMENTS_VER
			);
			wp_enqueue_script(
				'lp-announcements-editor-js',
				LP_ADDON_ANNOUNCEMENTS_URI . 'assets/js/admin.announcements.js',
				array( 'frontend-course-editor' ),
				LP_ADDON_ANNOUNCEMENTS_VER,
				true
			);
		}

		/**
		 * Enqueue scripts.
		 */
		public function enqueue_scripts() {
			global $post;

			$user      = learn_press_get_current_user();
			$user_data = get_userdata( $user->get_id() );
			$admin     = false;

			if ( $user_data && in_array( 'administrator', $user_data->roles ) ) {
				$admin = true;
			}

			if ( function_exists( 'learn_press_is_course' ) && learn_press_is_course() ) {
				if ( $admin || $user->has_course_status( $post->ID, array( 'enrolled', 'finished' ) ) ) {
					wp_enqueue_style(
						'jquery-ui-accordion',
						$this->get_plugin_url( 'assets/css/jquery-ui-accordion.css' ),
						array(),
						LP_ADDON_ANNOUNCEMENTS_VER
					);
					wp_enqueue_style(
						'lp-announcements-site-css',
						$this->get_plugin_url( 'assets/css/announcements.css' ),
						array(),
						LP_ADDON_ANNOUNCEMENTS_VER
					);
					wp_enqueue_script(
						'lp-announcements-site-js',
						$this->get_plugin_url( 'assets/js/announcements.js' ),
						array(
							'jquery',
							'jquery-ui-accordion',
						),
						LP_ADDON_ANNOUNCEMENTS_VER,
						true
					);
				}
			}
		}

		public function _add_course_meta_content() {
			global $post, $thepostid;

			if ( ! wp_script_is( 'lp_announcements', 'enqueued' ) ) {
				wp_enqueue_style(
					'lp_announcements',
					LP_ADDON_ANNOUNCEMENTS_URI . 'assets/css/admin.announcements.css',
					array(),
					LP_ADDON_ANNOUNCEMENTS_VER
				);
				wp_enqueue_script(
					'lp_announcements',
					LP_ADDON_ANNOUNCEMENTS_URI . 'assets/js/admin.announcements.js',
					array( 'jquery' ),
					LP_ADDON_ANNOUNCEMENTS_VER,
					true
				);
			}

			include_once LP_ANNOUNCEMENTS_INC . 'admin/views/metabox-content.php';
		}

		public function _save_meta_box( $post_id ) {
			if ( isset( $_POST['_lp_learnpress_announcements_send_mail'] ) && $_POST['_lp_learnpress_announcements_send_mail'] === 'on' ) {
				update_post_meta( $post_id, '_lp_learnpress_announcements_send_mail', 'on' );
			} else {
				update_post_meta( $post_id, '_lp_learnpress_announcements_send_mail', 'off' );
			}

			/* Save Display Comment Meta */
			if ( isset( $_POST['_lp_learnpress_announcements_display_discussion'] ) && $_POST['_lp_learnpress_announcements_display_discussion'] === 'on' ) {
				update_post_meta( $post_id, '_lp_learnpress_announcements_display_discussion', 'on' );
			} else {
				update_post_meta( $post_id, '_lp_learnpress_announcements_display_discussion', 'off' );
			}

			if ( isset( $_POST['_lp_announcements_display_comments'] ) ) {
				update_post_meta( $post_id, '_lp_learnpress_announcements_display_discussion', 'yes' );
			} else {
				update_post_meta( $post_id, '_lp_learnpress_announcements_display_discussion', 'no' );
			}
		}

		/**
		 * Add Announcements tab in admin course.
		 * Do not use in LP4
		 *
		 * @param $tabs
		 *
		 * @return array
		 */
		public function add_course_tab( $tabs ) {
			$forum = array( 'course_announcements' => new RW_Meta_Box( self::course_announcements_meta_box() ) );

			return array_merge( $tabs, $forum );
		}

		/**
		 * Course Announcement meta box.
		 * Do not use in LP4
		 *
		 * @return mixed
		 */
		public function course_announcements_meta_box() {
			$meta_box = array(
				'title'      => __( 'Announcements', 'learnpress-announcements' ),
				'post_types' => LP_COURSE_CPT,
				'context'    => 'normal',
				'icon'       => 'dashicons-megaphone',
				'priority'   => 'high',
				'pages'      => array( LP_COURSE_CPT ),
				'fields'     => array(
					array(
						'name' => __( 'Announcements', 'learnpress-announcements' ),
						'id'   => '_lp_announcements_list_announcements',
						'desc' => __(
							'Click the button "Send Mail" to send the new announcement for all students who were enrolled this course',
							'learnpress-announcements'
						),
						'type' => 'list_announcements',
						'std'  => '',
					),
					array(
						'name' => __( 'Display Comments', 'learnpress-announcements' ),
						'id'   => '_lp_announcements_display_comments',
						'desc' => __(
							'Allow the users who is enrolled comment for the all announcements',
							'learnpress-announcements'
						),
						'type' => 'checkbox',
						'std'  => 'true',
					),
				),
			);

			return apply_filters( 'learn-press/course-announcement/settings-meta-box-args', $meta_box );
		}

		/**
		 * Lists course.
		 */
		public static function lp_announcements_lists_course() {
			$user_id = get_current_user_id();
			if ( ( isset( $_POST['action'] ) && $_POST['action'] === 'lp_announcements_lists_course' ) && ( isset( $_POST['post_id'] ) && ! empty( $_POST['post_id'] ) ) ) {

				$post = get_post( $_POST['post_id'] );
				$user = $post->post_author;

				if ( empty( $user ) ) {
					wp_die();
				}
				$lp_args = array(
					'post_type'      => LP_COURSE_CPT,
					'post_status '   => 'publish',
					'posts_per_page' => '-1',
					'author'         => $user_id,
				);

				if ( user_can( $user_id, 'administrator' ) ) {
					$lp_args['author'] = get_post_field( 'post_author' );
				}

				if ( isset( $_POST['post__not_in'] ) && ! empty( $_POST['post__not_in'] ) ) {
					$lp_args['post__not_in'] = explode( ',', $_POST['post__not_in'] );
				}

				$query = new WP_Query( $lp_args );

				ob_start();

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						global $post;
						setup_postdata( $post );
						require LP_ANNOUNCEMENTS_INC . 'admin/views/popup-loop-item.php';
					}
					wp_reset_postdata();
				} else {
					require LP_ANNOUNCEMENTS_INC . 'admin/views/popup-not-found.php';
				}

				$result = ob_get_contents();
				ob_clean();
				echo $result;
			}
			wp_die();
		}


		/**
		 * Ajax create announcement.
		 */
		public function lp_create_announcement() {
			$response          = new stdClass();
			$response->status  = 'error';
			$response->message = 'error';
			$response->data    = new stdClass();

			try {
				if ( empty( $_POST['nonce'] ) || ! check_ajax_referer( 'lp-create-announcement', 'nonce', false ) ) {
					throw new Exception( 'Request invalid' );
				}

				if ( empty( $_POST['post_id'] ) || empty( $_POST['posts_id'] ) ) {
					throw new Exception( 'Invalid params!' );
				}

				if ( empty( $_POST['title'] ) ) {
					throw new Exception( 'Title not empty' );
				}

				$course_id      = absint( $_POST['post_id'] );
				$course_ids_str = LP_Request::get_param( 'posts_id' );
				$title          = LP_Request::get_param( 'title', '', 'html' );
				$content        = LP_Request::get_param( 'content', '', 'html' );
				$send_mail      = isset( $_POST['send_mail'] ) && 'true' === $_POST['send_mail'];

				$args = array(
					'post_status'  => 'publish',
					'post_type'    => LP_ANNOUNCEMENTS_CPT,
					'post_title'   => $title,
					'post_content' => $content,
				);

				$course = get_post( $course_id );
				if ( ! empty( $course ) ) {
					$args['post_author'] = $course->post_author;
				}

				if ( isset( $_POST['display_comment'] ) ) {
					if ( $_POST['display_comment'] === 'true' ) {
						$args['comment_status'] = 'open';
					} else {
						$args['comment_status'] = 'close';
					}
				}

				$new_announcement_id = wp_insert_post( $args );

				// Set multiple metadata for current announcement
				$course_ids = explode( ',', $course_ids_str );
				foreach ( $course_ids as $course_id ) {
					add_post_meta( $new_announcement_id, '_lp_course_announcement', $course_id );

					if ( $send_mail ) {
						do_action( 'learnpress/email/announcement/set-announcement-for-course', $course_id, $new_announcement_id );
					}
				}

				$current_time = strtotime( current_time( 'mysql', 1 ) );
				$post_time    = get_the_time( 'U', $new_announcement_id );

				if ( ( $current_time - $post_time ) < DAY_IN_SECONDS ) {
					$date = human_time_diff( $post_time, $current_time ) . __( ' ago', 'learnpress-announcement' );
				} else {
					$date = get_the_date( '', $new_announcement_id );
				}

				$response->status      = 'success';
				$response->message     = 'Create announcement success';
				$response->data->id    = $new_announcement_id;
				$response->data->title = get_the_title( $new_announcement_id );
				$response->data->date  = $date;
			} catch ( Throwable $e ) {
				$response->message = $e->getMessage();
			}

			wp_send_json( $response );
		}

		/**
		 * Ajax remove announcement.
		 */
		public static function ajax_remove_announcement() {
			if ( isset( $_POST['course_id'] ) && ! empty( $_POST['course_id'] )
				 && isset( $_POST['post_id'] ) && ! empty( $_POST['post_id'] )
			) {
				$course_id = $_POST['course_id'];
				$post_id   = $_POST['post_id'];

				delete_post_meta( $post_id, '_lp_course_announcement', $course_id );
			}

			wp_die();
		}

		/**
		 * Ajax send mail.
		 *
		 * @editor tungnx
		 * @modify 4.0.3
		 */
		public function send_mail_announcements() {
			$response          = new stdClass();
			$lp_course_db      = LP_Course_DB::getInstance();
			$response->status  = 'error';
			$response->message = 'error';

			try {
				if ( empty( $_POST['announcement_id'] ) || empty( $_POST['course_id'] ) ) {
					throw  new Exception( __( 'Params invalid!', '' ) );
				}

				$announcement_id = LP_Helper::sanitize_params_submitted( $_POST['announcement_id'] );
				$course_id       = absint( $_POST['course_id'] );

				/* Get users enrolled the course */
				$user_ids = $lp_course_db->get_user_ids_enrolled( $course_id );

				if ( ! empty( $user_ids ) ) {
					$user_ids = array_keys( $user_ids );
				} else {
					throw new Exception( 'No user enrolled course' );
				}

				$status_announcement = get_post_status( $announcement_id );
				$course              = learn_press_get_course( $course_id );

				if ( ! $course || 'trash' === $course->get_post_status() ) {
					throw new Exception( 'Course deleted in trash!' );
				}

				if ( empty( $status_announcement ) || $status_announcement == 'trash' ) {
					throw new Exception( 'Announcement deleted in trash!' );
				}

				$params = [ $course_id, $announcement_id ];

				$email  = new LP_Email_Announcements();
				$result = $email->handle( $params );

				//$emails_string_send_success = implode( ',', $email_arr_send_success );

				$response->status  = 'success';
				$response->message = 'Send mail success';
			} catch ( Throwable $e ) {
				$response->message = $e->getMessage();
			}

			wp_send_json( $response );
		}

		/**
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function add_single_course_announcements_tab( $tabs ) {
			$user = learn_press_get_current_user();
			if ( ! $user instanceof LP_User ) {
				return $tabs;
			}

			$course_id = get_the_ID();
			$user_data = get_userdata( $user->get_id() );

			if ( empty( $user_data->roles ) ) {
				return $tabs;
			}

			/* Check permission of user is admin or enrolled */
			$roles = $user_data->roles[0];
			if ( $user->has_enrolled_course( $course_id ) || $roles === 'lp_teacher' || $roles === 'administrator' ) {
				$tabs['announcements'] = array(
					'title'    => __( 'Announcements', 'learnpress-announcements' ),
					'priority' => 30,
					'callback' => array( $this, 'single_course_announcements_tab_content' ),
				);
			}

			return $tabs;
		}


		/**
		 * Announcements content in single course page.
		 */
		public function single_course_announcements_tab_content() {

			$args  = array(
				'post_type'      => LP_ANNOUNCEMENTS_CPT,
				'type'           => 'publish',
				'posts_per_page' => '-1',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => '_lp_course_announcement',
						'value'   => learn_press_get_course_id(),
						'compare' => '=',
					),
				),
			);
			$query = new WP_Query( $args );

			LP_Addon_Announcements_Preload::$addon->get_template( 'announcements.php', array( 'query' => $query ) );
		}

		/**
		 * @param $location
		 * @param $comment
		 *
		 * @return string
		 */
		public function announcement_comment_post_redirect( $location, $comment ) {
			if ( isset( $_REQUEST['lp_comment_announcement_from_course'] ) && ! empty( $_REQUEST['lp_comment_announcement_from_course'] ) ) {
				if ( isset( $_REQUEST['lp_comment_announcement_from_course_url'] ) && ! empty( $_REQUEST['lp_comment_announcement_from_course_url'] ) ) {
					return $_REQUEST['lp_comment_announcement_from_course_url'] . '?tab=announcements#comment-' . $comment->comment_ID;
				}
			}

			return $location;
		}

		/**
		 * Add email settings
		 */
		public function emails_setting() {
			if ( ! class_exists( 'LP_Emails' ) ) {
				return;
			}

			if ( ! class_exists( 'LP_Settings_Emails_Group' ) ) {
				include_once LP_PLUGIN_PATH . 'inc/admin/settings/email-groups/class-lp-settings-emails-group.php';
			}

			$emails = LP_Emails::instance()->emails;

			$emails['LP_Email_Announcements'] = include_once 'emails/class-lp-email-announcements.php';

			LP_Emails::instance()->emails = $emails;
		}

		/**
		 * Add email group
		 *
		 * @param array $groups
		 *
		 * @return array
		 */
		public function add_email_group( array $groups ): array {
			$groups[] = include 'admin/settings/email-groups/class-lp-settings-announcements-emails.php';

			return $groups;
		}

		/**
		 * Instance.
		 *
		 * @return LP_Addon_Announcements|null
		 */
		public static function instance() {
			if ( ! self::$_instance ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

//add_action( 'plugins_loaded', array( 'LP_Addon_Announcements', 'instance' ) );
