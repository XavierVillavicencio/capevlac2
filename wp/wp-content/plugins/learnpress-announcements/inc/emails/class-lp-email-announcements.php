<?php

/**
 * Class LP_Email_Announcement
 *
 * @author   ThimPress
 * @package  LearnPress/Announcements/Classes
 * @version  4.0.1
 * @editor tungnx
 * @modify 4.0.3
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Email_Announcements' ) ) {
	/**
	 * Class LP_Email_Announcements.
	 */
	class LP_Email_Announcements extends LP_Email {
		/**
		 * Course ID
		 *
		 * @var LP_Course
		 */
		protected $course;

		/**
		 * User ID
		 *
		 * @var LP_User
		 */
		protected $user;

		/**
		 * Announcement
		 *
		 * @var WP_Post
		 */
		public $announcement;

		/**
		 * LP_Email_Announcement constructor.
		 */
		public function __construct() {
			$this->id             = 'announcements';
			$this->title          = esc_html__( 'Announcement', 'learnpress-announcements' );
			$this->description    = esc_html__( 'Announcement email.', 'learnpress-announcements' );
			$this->template_base  = LP_ANNOUNCEMENTS_TEMPLATE;
			$this->template_html  = 'emails/email-text-settings.php';
			$this->template_plain = 'emails/email-plain-settings.php';

			$this->default_subject = __( '[{{site_title}}] You have a new announcement ({{announcement_name}})', 'learnpress-announcements' );
			$this->default_heading = __( 'New Announcement', 'learnpress-announcements' );

			parent::__construct();

			$variable_on_email_support = apply_filters(
				'lp/assignment/email/submitted',
				[
					'{{announcement_id}}',
					'{{announcement_name}}',
					'{{announcement_excerpt}}',
					'{{announcement_content}}',
					'{{course_id}}',
					'{{course_name}}',
					'{{course_url}}',
					'{{user_id}}',
					'{{user_name}}',
					'{{user_email}}',
					'{{user_profile_url}}',
				]
			);

			$this->support_variables = array_merge( $this->support_variables, $variable_on_email_support );
		}

		/**
		 * Handle send email
		 *
		 * @param array $params
		 *
		 * @return bool
		 * @throws Exception
		 */
		public function handle( array $params ): bool {
			if ( ! $this->check_params( $params ) ) {
				return false;
			}

			$lp_course_db = LP_Course_DB::getInstance();

			/* Get users enrolled the course */
			$user_ids = $lp_course_db->get_user_ids_enrolled( $this->course->get_id() );
			if ( empty( $user_ids ) ) {
				return false;
			}

			$user_ids = array_keys( $user_ids );

			foreach ( $user_ids as $user_id ) {
				$this->user = learn_press_get_user( $user_id );
				if ( ! $this->user ) {
					continue;
				}
				$this->set_data_content();
				$this->set_receive( $this->user->get_email() );
				$this->send_email();
			}

			return true;
		}

		/**
		 * Check params
		 *
		 * @param array $params
		 *
		 * @return bool
		 */
		public function check_params( array $params ): bool {
			if ( ! $this->enable ) {
				return false;
			}

			if ( count( $params ) < 2 ) {
				return false;
			}

			$course_id       = $params[0] ?? 0;
			$announcement_id = $params[1] ?? 0;

			$this->course = learn_press_get_course( $course_id );
			if ( ! $this->course ) {
				return false;
			}

			$this->announcement = get_post( $announcement_id );
			if ( empty( $this->announcement ) ) {
				return false;
			}

			return true;
		}

		public function set_data_content() {
			$variables = apply_filters(
				'lp/email/announcements/variables-mapper',
				[
					'{{announcement_id}}'      => $this->announcement->ID,
					'{{announcement_name}}'    => $this->announcement->post_title,
					'{{announcement_excerpt}}' => $this->announcement->post_excerpt,
					'{{announcement_content}}' => $this->announcement->post_content,
					'{{course_id}}'            => $this->course->get_id(),
					'{{course_name}}'          => $this->course->get_title(),
					'{{course_url}}'           => $this->course->get_permalink(),
					'{{user_id}}'              => $this->user->get_id(),
					'{{user_name}}'            => $this->user->get_display_name(),
					'{{user_email}}'           => $this->user->get_email(),
					'{{user_profile_url}}'     => learn_press_user_profile_link( $this->user->get_id() ),
				]
			);

			$variables_common = $this->get_common_variables( $this->email_format );
			$this->variables  = array_merge( $variables, $variables_common );
		}
	}
}

return new LP_Email_Announcements();
