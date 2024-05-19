<?php
/**
 * Plugin load class.
 *
 * @author   ThimPress
 * @package  LearnPress/Assignments/Classes
 * @version  3.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Addon_Assignment' ) ) {
	/**
	 * Class LP_Addon_Assignment
	 */
	class LP_Addon_Assignment extends LP_Addon {

		/**
		 * Addon version
		 *
		 * @var string
		 */
		public $version = LP_ADDON_ASSIGNMENT_VER;

		/**
		 * Require LP version
		 *
		 * @var string
		 */
		public $require_version = LP_ADDON_ASSIGNMENT_REQUIRE_VER;

		/**
		 * Path file addon
		 *
		 * @var string
		 */
		public $plugin_file = LP_ADDON_ASSIGNMENT_FILE;

		/**
		 * LP_Addon_Assignment constructor.
		 */
		public function __construct() {
			parent::__construct();

			include_once 'lp-assignment-database.php';

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'assignment_enqueue_scripts' ), 1011 );
			add_action( 'init', array( $this, 'register_ajax' ) );
			add_action( 'learn-press/course-item-slugs/for-rewrite-rules', array( $this, 'add_item_slug_for_rewrite_rules' ), 10, 1 );
			add_filter( 'learn-press/course-support-items', array( $this, 'put_type_here' ), 10, 2 );
			add_filter( 'learn-press/new-section-item-data', array( $this, 'new_assignment_item' ), 10, 4 );
			add_filter( 'learn-press/course-item-object-class', array( $this, 'assignment_object_class' ), 10, 4 );
			add_filter( 'learn-press/modal-search-items/exclude', array( $this, 'exclude_items' ), 10, 4 );
			add_action(
				'admin_init',
				function () {
					global $title;
					if ( isset( $_GET['page'] ) && ( 'assignment-evaluate' === $_GET['page'] ||
					'assignment-student' === $_GET['page'] ) ) {
						$title = __( 'Evaluate Assignment', 'learnpress-assignment' );
					}
				}
			);

			// update assignment item in single course template
			add_filter( 'learn_press_locate_template', array( $this, 'update_assignment_template' ), 10, 2 );

			add_action(
				'learn-press/course-section-item/before-lp_assignment-meta',
				array( $this, 'learnpress_assignment_show_duration' ),
				10
			);
			add_action(
				'learn-press/course-section-item/before-lp_assignment-meta',
				array( $this, 'learnpress_assignment_meta_final' ),
				15
			);

			// Hook calculate result
			add_filter(
				'learn-press/evaluate_passed_conditions',
				array( $this, 'learnpress_assignment_evaluate' ),
				10,
				3
			);
			add_filter( 'learn-press/get-course-item', array( $this, 'learnpress_assignment_get_item' ), 10, 3 );
			add_filter(
				'learn-press/default-user-item-status',
				array( $this, 'learnpress_assignment_default_user_item_status' ),
				10,
				2
			);
			add_filter(
				'learn-press/user-item-object',
				array( $this, 'learnpress_assignment_user_item_object' ),
				10,
				2
			);
			add_filter(
				'learn-press/course-item-type',
				array( $this, 'learnpress_assignment_course_item_type' ),
				10,
				1
			);
			add_filter(
				'learn-press/block-course-item-types',
				array( $this, 'learnpress_assignment_block_course_item_type' ),
				10,
				1
			);

			// count assignment in admin archive course
			// add_filter( 'learn-press/course-count-items', array( $this, 'count_course_assignment' ), 10, 2 );
			// add support final item
			add_filter( 'learn-press/post-types-support-assessment-by-final-item', array( $this, 'add_final_type' ) );

			// register page
			add_action( 'admin_menu', array( $this, 'register_pages' ) );

			// get grade
			add_filter( 'learn-press/user-item-grade', array( $this, 'learnpress_assignment_get_grade' ), 10, 4 );

			// add email group
			add_filter( 'learn-press/email-section-classes', array( $this, 'add_email_group' ) );

			// handle evaluate form actions
			add_action( 'admin_init', array( $this, 'evaluate_actions' ) );

			// count more evaluated assignments
			add_filter(
				'learn-press/course-item/completed',
				array( $this, 'learnpress_assignment_count_evaluated_item' ),
				10,
				3
			);

			// add passed or failed class when display item:
			add_filter(
				'learn-press/course-item-status-class',
				array( $this, 'learnpress_assignment_add_class_css' ),
				10,
				3
			);

			// add filter user access admin view assignment
			add_filter( 'learn-press/filter-user-access-types', array( $this, 'add_filter_access' ) );

			// add user profile page tabs
			add_filter( 'learn-press/profile-tabs', array( $this, 'add_profile_tabs' ) );

			// add std value assignment meta box fields - NOT USE IN LP4.
			add_filter( 'rwmb_field_meta', array( $this, 'assignment_field_meta' ), 10, 2 );

			// add profile setting publicity fields
			add_filter( 'learn-press/get-publicity-setting', array( $this, 'add_publicity_setting' ) );

			// check profile setting publicity fields
			add_filter( 'learn-press/check-publicity-setting', array( $this, 'check_publicity_setting' ), 10, 2 );

			// add user profile page setting publicity fields - NOT USE IN LP4.
			add_action( 'learn-press/end-profile-publicity-fields', array( $this, 'add_profile_publicity_fields' ) );

			// add user profile page setting publicity in LP4.
			add_action(
				'learn-press/profile-privacy-settings',
				function ( $privacy ) {
					$privacy[] = array(
						'name'        => esc_html__( 'Assignments', 'learnpress-assignments' ),
						'id'          => 'assignments',
						'default'     => 'no',
						'type'        => 'yes-no',
						'description' => esc_html__( 'Public your profile Assignment.', 'learnpress-assignments' ),
					);

					return $privacy;
				}
			);

			// save evaluate admin notice
			// add_action( 'learn-press/save-evaluate-form', array( $this, 'evaluate_admin_notice' ) );

			// LP assignment email setting
			// $this->emails_setting();
			// add_action( 'learn-press/register-emails', array( $this, 'emails_setting' ), 10, 2 );
			add_action( 'plugins_loaded', array( $this, 'emails_setting' ), 10 );
			// add_filter( 'learn-press/email-actions', [ $this, 'email_notify_hook' ] );

			add_filter( 'wp_default_editor', array( $this, 'evaluate_default_editor' ) );

			add_filter(
				'learn-press/content-drip/item-status',
				array( $this, 'update_status_item_content_drip' ),
				10,
				4
			);

			if ( is_plugin_active( 'learnpress-co-instructor/learnpress-co-instructor.php' ) ) {
				add_filter(
					'learn-press/co-instructor/case-post-type-can-edit',
					array( $this, 'add_post_type_to_check_can_edit_post' )
				);
			}

			//add_action( 'lp/background/course/save', array( $this, 'save_post_background' ), 10, 2 );
			add_filter( 'learn-press/course-passing-condition', array( $this, 'course_passing_condition' ), 10, 3 );
			add_action( 'lp/background/course/save', array( $this, 'course_save_background' ), 10, 2 );

			// Support for FE.
			add_filter(
				'learnpress_frontend_editor_localize_script',
				function( $add_ons ) {
					$add_ons['add_ons']['assignment'] = array();

					return $add_ons;
				}
			);

			// Add hook set item assignment is completed
			add_filter( 'lp/content-drip/drip_type_prerequisite/item-is-complete', array( $this, 'item_is_complete' ), 10, 2 );
			add_filter( 'lp/content-drip/drip_type_sequentially/item-is-complete', array( $this, 'item_is_complete' ), 10, 2 );
		}

		/**
		 * Add rewrite rules for single assignment.
		 * To compatible with LP v4.2.2.2 and higher.
		 *
		 * @param $item_slugs array
		 * @since 4.1.0
		 * @return array
		 */
		public function add_item_slug_for_rewrite_rules( array $item_slugs = array() ) {
			$item_slugs[ LP_ASSIGNMENT_CPT ] = urldecode( sanitize_title_with_dashes( LP_Settings::get_option( 'assignment_slug', 'assignments' ) ) );

			return $item_slugs;
		}

		/**
		 * Active visual in wp editor evaluate page.
		 *
		 * @param $editor
		 *
		 * @return string
		 */
		public function evaluate_default_editor( $editor ) {

			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'assignment-evaluate' ) {
				return 'tinymce';
			}

			return $editor;
		}

		public function learnpress_assignment_block_course_item_type( $types ) {
			$types[] = LP_ASSIGNMENT_CPT;

			return $types;
		}

		/**
		 * @param $types
		 *
		 * @return array
		 */
		public function add_filter_access( $types ) {
			$types[] = LP_ASSIGNMENT_CPT;

			return $types;
		}

		public function learnpress_assignment_add_class_css( $item_status, $item_grade, $item_type ) {
			$item_class = '';
			if ( $item_type == LP_ASSIGNMENT_CPT && $item_status == 'evaluated' ) {
				$item_class = $item_grade;
			}

			return $item_class;
		}

		/**
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function add_publicity_setting( $settings ) {
			$settings['assignments'] = LearnPress::instance()->settings()->get( 'profile_publicity.assignments' );

			return $settings;
		}

		/**
		 * @param $publicities
		 * @param $profile LP_Profile
		 *
		 * @return mixed
		 */
		public function check_publicity_setting( $publicities, $profile ) {
			$publicities['view-tab-assignment'] = $profile->get_publicity( 'assignments' ) == 'yes';

			return $publicities;
		}

		/**
		 * @param $profile LP_Profile
		 */
		public function add_profile_publicity_fields( $profile ) {
			if ( LearnPress::instance()->settings()->get( 'profile_publicity.assignments' ) === 'yes' ) { ?>
				<li class="form-field">
					<label for="my-assignments"><?php _e( 'My assignments', 'learnpress-assignments' ); ?></label>
					<div class="form-field-input">
						<input name="publicity[assignments]" value="yes" type="checkbox" id="my-assignments" <?php checked( $profile->get_publicity( 'assignments' ), 'yes' ); ?> />
						<p class="description"><?php esc_html_e( 'Public your profile assignments', 'learnpress-assignments' ); ?></p>
					</div>
				</li>
				<?php
			}
		}

		/**
		 * Add std value assignment meta box fields.
		 *
		 * @param $meta
		 * @param $field
		 *
		 * @return mixed
		 */
		public function assignment_field_meta( $meta, $field ) {
			if ( ! empty( $field['assignment-field'] ) ) {
				$meta = $field['std'];

			}

			return $meta;
		}

		/**
		 * Add user profile tabs.
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		public function add_profile_tabs( $tabs ) {
			$settings            = LearnPress::instance()->settings();
			$tabs['assignments'] = array(
				'title'    => __( 'Assignments', 'learnpress-assignments' ),
				'slug'     => $settings->get( 'profile_endpoints.assignments', 'assignments' ),
				'callback' => array( $this, 'tab_assignments' ),
				'icon'     => '<i class="fas fa-file-alt"></i>',
				'priority' => 25,
			);

			return $tabs;
		}

		public function tab_assignments() {
			learn_press_assignment_get_template( 'profile/tabs/assignments.php' );
		}

		/*public function save_post_background( $lp_course, $data ) {
			$course_id = $lp_course->get_id();

			$course_result = get_post_meta( $course_id, '_lp_course_result', true );

			if ( $course_result && $course_result === 'evaluate_final_assignment' ) {
				$items = $lp_course->get_item_ids();

				if ( $items ) {
					foreach ( $items as $item ) {
						if ( learn_press_get_post_type( $item ) === 'lp_assignment' ) {
							$final_assignment = $item;
						}
					}
				}

				if ( isset( $final_assignment ) ) {
					update_post_meta( $course_id, '_lp_final_assignment', $final_assignment );
					$passing_grade = (float) get_post_meta( $final_assignment, '_lp_passing_grade', true );
					update_post_meta( $course_id, '_lp_passing_condition', $passing_grade * 10 );
				}
			}
		}*/

		public function learnpress_assignment_count_evaluated_item( $completed, $item, $item_status ) {
			if ( $item && $item->get_data( 'item_type' ) == LP_ASSIGNMENT_CPT && $item_status == 'evaluated' ) {
				$completed ++;
			}

			return $completed;
		}

		/**
		 * Handle evaluate form actions.
		 */
		public function evaluate_actions() {
			$page          = LP_Request::get_param( 'page' );
			$assignment_id = LP_Request::get_param( 'assignment_id', 0, 'int' );
			$user_id       = LP_Request::get_param( 'user_id', 0, 'int' );

			if ( ! ( 'assignment-evaluate' === $page ) || ! $assignment_id || ! $user_id || 'post' !== strtolower( $_SERVER['REQUEST_METHOD'] ) ) {
				return;
			}

			$action       = LP_Request::get( 'action' );
			$user_item_id = LP_Request::get( 'user_item_id' );
			$assignment   = LP_Assignment::get_assignment( $assignment_id );

			if ( ! $action || ! $user_item_id ) {
				return;
			}

			$mark         = LP_Request::get( '_lp_evaluate_assignment_mark', 0 );
			$assigment_db = LP_Assigment_DB::getInstance();

			if ( $action != 're-evaluate' ) {
				learn_press_update_user_item_meta( $user_item_id, '_lp_assignment_mark', $mark );
				$assigment_db->update_extra_value( $user_item_id, LP_Assigment_DB::$instructor_note_key, LP_Request::get( '_lp_evaluate_assignment_instructor_note' ) );

				$document = isset( $_POST['_lp_evaluate_assignment_document'] ) ? wp_unslash( array_filter( explode( ',', $_POST['_lp_evaluate_assignment_document'] ) ) ) : array();

				learn_press_update_user_item_meta(
					$user_item_id,
					'_lp_assignment_evaluate_upload',
					$document
				);
				learn_press_update_user_item_meta(
					$user_item_id,
					'_lp_assignment_evaluate_author',
					learn_press_get_current_user()->get_id()
				);
			}

			$course = learn_press_get_item_courses( $assignment_id );
			//$lp_course = learn_press_get_course( $course[0]->ID );
			//$user      = learn_press_get_user( $user_id );
			//$course_data = $user->get_course_data( $lp_course->get_id() );
			$user_curd = new LP_User_CURD();

			switch ( $action ) {
				case 'evaluate':
					learn_press_update_user_item_field(
						array(
							'graduation' => ( $mark >= $assignment->get_data( 'passing_grade' ) ? 'passed' : 'failed' ),
							'user_id'    => $user_id,
						),
						array( 'user_item_id' => $user_item_id )
					);

					$user_curd->update_user_item_status( $user_item_id, 'evaluated' );

					//$course_data->calculate_course_results();

					do_action( 'learn-press/assignment/instructor-evaluated', $user_id, $assignment_id );

					break;
				case 're-evaluate':
					$user_curd->update_user_item_status( $user_item_id, 'completed' );

					do_action( 'learn-press/instructor-re-evaluated-assignment', $assignment_id, $user_id );
					break;
				default:
					break;
			}

			do_action( 'learn-press/save-evaluate-form', $action );
		}

		/**
		 * Add evaluate admin notice.
		 *
		 * @param $action
		 */
		public function evaluate_admin_notice( $action ) {
			if ( $action != 're-evaluate' ) {
				?>
				<div class="lpa-fe-evaluate-updated">
					<p>
						<?php
						switch ( $action ) {
							case 'evaluate':
								echo esc_html__( 'Evaluated successfully.', 'leanpress-assignments' );
								break;
							case 'save':
								echo esc_html__( 'Evaluation updated.', 'leanpress-assignments' );
								break;
							default:
								break;
						}
						?>
					</p>
				</div>
				<?php
			}
		}

		/**
		 * Add email setting group.
		 *
		 * @param $groups
		 *
		 * @return array
		 */
		public function add_email_group( $groups ) {
			$groups[] = include LP_ADDON_ASSIGNMENT_INC_PATH . 'admin/settings/email-groups/class-lp-settings-submitted-assignment-emails.php';
			$groups[] = include LP_ADDON_ASSIGNMENT_INC_PATH . 'admin/settings/email-groups/class-lp-settings-evaluated-assignment-emails.php';

			return $groups;
		}

		/**
		 * Add email setting.
		 */
		public function emails_setting() {
			if ( ! class_exists( 'LP_Emails' ) ) {
				return;
			}

			if ( ! class_exists( 'LP_Settings_Emails_Group' ) ) {
				include_once LP_PLUGIN_PATH . 'inc/admin/settings/email-groups/class-lp-settings-emails-group.php';
			}

			include_once 'emails/class-lp-email-assignment-type.php';

			$emails = LP_Emails::instance()->emails;

			$emails['LP_Email_Assignment_Submitted_Admin']      = include_once 'emails/submitted/class-lp-email-submitted-assignment-admin.php';
			$emails['LP_Email_Assignment_Submitted_Instructor'] = include_once 'emails/submitted/class-lp-email-submitted-assignment-instructor.php';
			$emails['LP_Email_Assignment_Submitted_User']       = include_once 'emails/submitted/class-lp-email-submitted-assignment-user.php';
			$emails['LP_Email_Assignment_Evaluated_User']       = include_once 'emails/evaluated/class-lp-email-evaluated-assignment-user.php';
			$emails['LP_Email_Assignment_Evaluated_Admin']      = include_once 'emails/evaluated/class-lp-email-evaluated-assignment-admin.php';
			$emails['LP_Email_Assignment_Evaluated_Instructor'] = include_once 'emails/evaluated/class-lp-email-evaluated-assignment-instructor.php';

			LP_Emails::instance()->emails = $emails;
		}

		public function learnpress_assignment_get_grade( $grade, $item_id, $user_id, $course_id ) {
			if ( LP_ASSIGNMENT_CPT == get_post_type( $item_id ) ) {
				$result = learn_press_assignment_get_result( $item_id, $user_id, $course_id );
				$grade  = isset( $result['grade'] ) ? $result['grade'] : false;
			}

			return $grade;
		}

		/*public function learnpress_assignment_add_rewrite_rules() {
			$course_type  = LP_COURSE_CPT;
			$post_types   = get_post_types( '', 'objects' );
			$slug         = preg_replace( '!^/!', '', $post_types[ $course_type ]->rewrite['slug'] );
			$has_category = false;

			if ( preg_match( '!(%?course_category%?)!', $slug ) ) {
				$slug         = preg_replace( '!(%?course_category%?)!', '(.+?)/([^/]+)', $slug );
				$has_category = true;
			}

			if ( $has_category ) {
				add_rewrite_rule(
					'^' . $slug . '(?:/' . $post_types[ LP_ASSIGNMENT_CPT ]->rewrite['slug'] . '/([^/]+))/?$',
					'index.php?' . $course_type . '=$matches[2]&course_category=$matches[1]&course-item=$matches[3]&item-type=' . LP_ASSIGNMENT_CPT,
					'top'
				);
			} else {
				add_rewrite_rule(
					'^' . $slug . '/([^/]+)(?:/' . $post_types[ LP_ASSIGNMENT_CPT ]->rewrite['slug'] . '/([^/]+))/?$',
					'index.php?' . $course_type . '=$matches[1]&course-item=$matches[2]&item-type=' . LP_ASSIGNMENT_CPT,
					'top'
				);
			}
		}*/

		/**
		 * Count number assignment in admin archive course page.
		 *
		 * @param $content
		 * @param $course_items
		 *
		 * @return string
		 */
		public function count_course_assignment( $content, $course_items ) {
			if ( $course_items ) {
				$number_assignment = 0;
				foreach ( $course_items as $item_id ) {
					if ( get_post_type( $item_id ) == LP_ASSIGNMENT_CPT ) {
						$number_assignment ++;
					}
				}

				return $number_assignment ? sprintf(
					', ' . _n(
						'%d assignment',
						'%d assignments',
						$number_assignment,
						'learnpress-assignments'
					),
					$number_assignment
				) : ', ' . __(
					'0 assignment',
					'learnpress-assignments'
				);
			}

			return $content;
		}

		/**
		 * @param $types
		 *
		 * @return array
		 */
		public function add_final_type( $types ) {
			$types[] = LP_ASSIGNMENT_CPT;

			return $types;
		}

		/**
		 * Register assignment pages.
		 */
		public function register_pages() {
			add_submenu_page(
				'',
				esc_html__( 'Assignment Student', 'learnpress-assignments' ),
				esc_html__( 'Assignment Student', 'learnpress-assignments' ),
				'edit_published_lp_courses',
				'assignment-student',
				array( $this, 'student_page' )
			);

			add_submenu_page(
				'',
				esc_html__( 'Assignment Evaluate', 'learnpress-assignments' ),
				esc_html__( 'Assignment Evaluate', 'learnpress-assignments' ),
				'edit_published_lp_courses',
				'assignment-evaluate',
				array( $this, 'evaluate_page' )
			);
		}

		/**
		 * Assignment students page.
		 */
		public function student_page() {
			$assignment_id = ! empty( $_REQUEST['assignment_id'] ) ? $_REQUEST['assignment_id'] : 0;

			global $post;
			$post = get_post( $assignment_id );
			setup_postdata( $post );

			require_once LP_ADDON_ASSIGNMENTS_INC . 'admin/class-student-list-table.php';
			learn_press_assignment_admin_view( 'students.php' );

			wp_reset_postdata();
		}

		/**
		 * Assignment evaluate page.
		 */
		public function evaluate_page() {
			$assignment_id = ! empty( $_REQUEST['assignment_id'] ) ? $_REQUEST['assignment_id'] : 0;

			global $post;

			wp_enqueue_media();
			$post = get_post( $assignment_id );
			setup_postdata( $post );

			require_once LP_ADDON_ASSIGNMENTS_INC . 'admin/class-lp-assignment-evaluate.php';
			learn_press_assignment_admin_view( 'evaluate.php' );

			wp_reset_postdata();
		}

		public function learnpress_assignment_course_item_type( $item_types ) {
			$item_types[] = 'lp_assignment';

			return $item_types;
		}

		public function learnpress_assignment_user_item_object( $item, $data ) {
			if ( isset( $data['item_id'] ) && LP_ASSIGNMENT_CPT == get_post_type( $data['item_id'] ) ) {
				$item = new LP_User_Item_Assignment( $data );
			}

			return $item;
		}

		public function learnpress_assignment_default_user_item_status( $status, $item_id ) {
			if ( get_post_type( $item_id ) === LP_ASSIGNMENT_CPT ) {
				$status = 'viewed';
			}

			return $status;
		}

		/**
		 * @param $item
		 * @param $item_type
		 * @param $item_id
		 *
		 * @return bool|LP_Assignment
		 */
		public function learnpress_assignment_get_item( $item, $item_type, $item_id ) {
			if ( LP_ASSIGNMENT_CPT === $item_type ) {
				$item = LP_Assignment::get_assignment( $item_id );
			}

			return $item;
		}

		/**
		 * Assignment evaluate
		 *
		 * @param array               $results
		 * @param string              $evaluate_type
		 * @param LP_User_Item_Course $user_course
		 *
		 * @return array
		 */
		public function learnpress_assignment_evaluate( $results, $evaluate_type, $user_course ) {
			if ( 'evaluate_final_assignment' !== $evaluate_type ) {
				return $results;
			}

			try {
				$results['evaluate_type'] = 'evaluate_final_assignment';
				$course                   = $user_course->get_course();
				if ( ! $course ) {
					return $results;
				}

				$final_assignment = get_post_meta( $course->get_id(), '_lp_final_assignment', true );
				if ( ! $final_assignment ) {
					return $results;
				}

				$asit_rs           = learn_press_assignment_get_result( $final_assignment, $user_course->get_user_id(), $course->get_id() );
				$results['result'] = $asit_rs['result'] ?? 0;

				if ( $asit_rs['grade'] == 'passed' ) {
					$results['pass']   = 1;
					$results['status'] = LP_COURSE_FINISHED;
				}
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}

			return $results;
		}

		/**
		 * Define constants.
		 */
		protected function _define_constants() {
			define( 'LP_ADDON_ASSIGNMENTS_PATH', dirname( LP_ADDON_ASSIGNMENT_FILE ) );
			define( 'LP_ADDON_ASSIGNMENTS_INC', LP_ADDON_ASSIGNMENTS_PATH . '/inc/' );
			define( 'LP_ADDON_ASSIGNMENTS_TEMPLATE', LP_ADDON_ASSIGNMENTS_PATH . '/templates/' );
			define( 'LP_INVALID_ASSIGNMENT_OR_COURSE', 250 );
			define( 'LP_ASSIGNMENT_HAS_STARTED_OR_COMPLETED', 260 );
			define( 'LP_ASSIGNMENT_CPT', 'lp_assignment' );
		}

		/**
		 *
		 */
		public function assignment_enqueue_scripts() {
			if ( function_exists( 'learn_press_is_course' ) && learn_press_is_course() ) {
				$min = '.min';
				$ver = LP_ADDON_ASSIGNMENT_VER;
				if ( LP_Debug::is_debug() ) {
					$min = '';
					$ver = uniqid();
				}

				wp_enqueue_style(
					'learn-press-assignment',
					LP_Addon_Assignment_Preload::$addon->get_plugin_url( "assets/css/assignment{$min}.css" ),
					array(),
					$ver
				);

				if ( learnpress_is_assignment_page() ) {
					wp_enqueue_script(
						'learn-press-assignment',
						LP_Addon_Assignment_Preload::$addon->get_plugin_url( "assets/dist/js/frontend/assignment{$min}.js" ),
						array(
							'jquery',
							'plupload-all',
							'lp-plugins-all',
						),
						$ver,
						true
					);
					$scripts = learn_press_assets();
					$scripts->add_script_data( 'learn-press-assignment', learn_press_assignment_single_args() );
				}
			}
		}

		/**
		 * @param $item
		 */
		public function learnpress_assignment_show_duration( $item ) {
			$duration = get_post_meta( $item->get_id(), '_lp_duration', true );

			$format = array(
				'week'   => __( 'week', 'learnpress-assignments' ),
				'day'    => __( 'day', 'learnpress-assignments' ),
				'hour'   => __( 'hour', 'learnpress-assignments' ),
				'minute' => __( 'min', 'learnpress-assignments' ),
				'second' => __( 'sec', 'learnpress-assignments' ),
			);

			$format_plural = array(
				'week'   => __( 'weeks', 'learnpress-assignments' ),
				'day'    => __( 'days', 'learnpress-assignments' ),
				'hour'   => __( 'hours', 'learnpress-assignments' ),
				'minute' => __( 'mins', 'learnpress-assignments' ),
				'second' => __( 'seconds', 'learnpress-assignments' ),
			);

			$text_dura = explode( ' ', $duration );

			if ( ! empty( end( $text_dura ) ) ) {
				$last_text = 'minute';

				if ( count( $text_dura ) > 1 ) {
					$last_text = end( $text_dura );
				}

				$replace_text = absint( $duration ) > 1 ? $format_plural[ $last_text ] : $format[ $last_text ];
				$duration     = isset( $format[ $last_text ] ) ? str_replace(
					$last_text,
					$replace_text,
					$duration
				) : $duration;
			}

			if ( absint( $duration ) == 0 ) {
				$duration = esc_html__( 'Unlimited Time', 'learnpress-assignments' );
			}

			echo '<span class="item-meta duration">' . $duration . '</span>';
		}

		public function learnpress_assignment_meta_final( $item ) {
			$course = $item->get_course();

			if ( $course && get_post_meta( $course->get_id(), '_lp_final_assignment', true ) == $item->get_id() ) {
				echo '<span class="item-meta final-assignment">' . esc_html__( 'Final', 'learnpress-assignments' ) . '</span>';
			}
		}

		/**
		 * Update single course assignment template files.
		 *
		 * @param $located
		 * @param $template_name
		 *
		 * @return mixed|string
		 */
		public function update_assignment_template( $located, $template_name ) {
			if ( $template_name == 'single-course/section/item-assignment.php' ) {
				$located = learn_press_assignment_get_template_part( 'item', 'assignment' );
				$located = learn_press_assignment_locate_template( 'single-course/section/item-assignment.php' );
			} elseif ( $template_name == 'single-course/content-item-lp_assignment.php' ) {
				$located = learn_press_assignment_locate_template( 'single-course/content-item-lp_assignment.php' );
			}

			return $located;
		}

		/**
		 * @param        $exclude
		 * @param        $type
		 * @param string  $context
		 * @param null    $context_id
		 *
		 * @return array
		 */
		public function exclude_items( $exclude, $type, $context = '', $context_id = null ) {
			if ( $type != 'lp_assignment' ) {
				return $exclude;
			}

			global $wpdb;

			$used_items = array();
			$query      = $wpdb->prepare(
				"
						SELECT item_id
						FROM {$wpdb->prefix}learnpress_section_items si
						INNER JOIN {$wpdb->prefix}learnpress_sections s ON s.section_id = si.section_id
						INNER JOIN {$wpdb->posts} p ON p.ID = s.section_course_id
						WHERE %d
						AND p.post_type = %s
					",
				1,
				LP_COURSE_CPT
			);

			$used_items = $wpdb->get_col( $query );

			if ( $used_items && $exclude ) {
				$exclude = array_merge( $exclude, $used_items );
			} elseif ( $used_items ) {
				$exclude = $used_items;
			}

			return is_array( $exclude ) ? array_unique( $exclude ) : array();
		}

		/**
		 * @param $status
		 * @param $type
		 * @param $item_type
		 * @param $item_id
		 *
		 * @return string
		 */
		public function assignment_object_class( $status, $type, $item_type, $item_id ) {
			$status['assignment'] = 'LP_Assignment';

			return $status;
		}

		/**
		 * @param $item
		 * @param $args
		 *
		 * @return int|WP_Error
		 */
		public function new_assignment_item( $item_id, $item, $args, $course_id ) {
			if ( $item['type'] == LP_ASSIGNMENT_CPT ) {
				$assigment_curd = new LP_Assignment_CURD();
				$item_id        = $assigment_curd->create( $args );
			}

			return $item_id;
		}

		/**
		 * @param $types
		 * @param $key
		 *
		 * @return array
		 */
		public function put_type_here( $types, $key ) {
			if ( $key ) {
				$types[] = 'lp_assignment';
			} else {
				$types['lp_assignment'] = esc_html__( 'Assignment', 'learnpress-assignments' );
			}

			return $types;
		}

		/**
		 * Register ajax
		 *
		 * @see start_assignment
		 * @see process_assignment
		 */
		public function register_ajax() {
			$actions = array(
				'start-assignment'    => 'start_assignment',
				'retake-assignment'   => 'retake_assignment',
				'controls-assignment' => 'process_assignment',
				'lpae-evaluate'       => 'lpae_evaluate',
			);

			foreach ( $actions as $action => $function ) {
				LP_Request::register_ajax( $action, array( __CLASS__, $function ) );
				LP_Request::register( "lp-{$action}", array( __CLASS__, $function ) );
			}
		}

		/**
		 * Retake assignment
		 *
		 * @editor tungnx
		 * @modify 4.0.1
		 * @version 4.0.1
		 */
		public static function retake_assignment() {
			$assignment_id = LP_Request::get_int( 'assignment-id' );

			try {
				if ( empty( $assignment_id ) ) {
					throw new Exception( esc_html__( 'No Assignment ID param.', 'learnpress-assignment' ) );
				}

				$request = new WP_REST_Request( 'POST' );
				$request->set_body_params(
					array(
						'id' => $assignment_id,
					)
				);

				$controller = new LP_Jwt_Assignment_V1_Controller();

				$response = $controller->retake_assignment( $request );

				if ( $response->is_error() ) {
					throw new Exception( $response->as_error()->get_error_message() );
				}

				$server = rest_get_server();
				$json   = $server->response_to_data( $response, false );

				if ( $json['data']['status'] == 400 ) {
					throw new Exception( $json['message'] );
				} else {
					wp_redirect( learn_press_get_current_url() );
					exit();
				}
			} catch ( \Throwable $th ) {
				learn_press_add_message( $th->getMessage(), 'error' );
			}
		}

		/**
		 * Evaluate result
		 *
		 * @editor tungnx
		 * @modify 4.0.1
		 * @version 4.0.1
		 */
		public static function lpae_evaluate() {
			$page          = LP_Request::get_param( 'evaluate-page' );
			$assignment_id = LP_Request::get_param( 'assignment_id', 0, 'int' );
			$user_id       = LP_Request::get_param( 'user_id', 0, 'int' );
			$evaluate_page = get_page_link( get_option( 'assignment_evaluate_page_id' ) );
			if ( ! ( $evaluate_page === $page ) || ! $assignment_id || ! $user_id || 'post' !== strtolower( $_SERVER['REQUEST_METHOD'] ) ) {
				return;
			}

			$action       = LP_Request::get_param( 'action' );
			$user_item_id = LP_Request::get_param( 'user_item_id', 0, 'int' );
			$assignment   = LP_Assignment::get_assignment( $assignment_id );

			if ( ! $action || ! $user_item_id ) {
				return;
			}

			$mark = LP_Request::get_param( '_lp_evaluate_assignment_mark', 0 );

			if ( $action != 're-evaluate' ) {
				learn_press_update_user_item_meta( $user_item_id, '_lp_assignment_mark', $mark );

				learn_press_update_user_item_meta(
					$user_item_id,
					'_lp_assignment_instructor_note',
					LP_Request::get( '_lp_evaluate_assignment_instructor_note' )
				);

				$document = isset( $_POST['_lp_evaluate_assignment_document'] ) ? wp_unslash( array_filter( explode( ',', $_POST['_lp_evaluate_assignment_document'] ) ) ) : array();

				learn_press_update_user_item_meta(
					$user_item_id,
					'_lp_assignment_evaluate_upload',
					$document
				);

				learn_press_update_user_item_meta(
					$user_item_id,
					'_lp_assignment_evaluate_author',
					learn_press_get_current_user()->get_id()
				);
			}

			$course = learn_press_get_item_courses( $assignment_id );
			//$lp_course = learn_press_get_course( $course[0]->ID );
			//$user      = learn_press_get_user( $user_id );
			//$course_data = $user->get_course_data( $lp_course->get_id() );

			$user_curd = new LP_User_CURD();

			switch ( $action ) {
				case 'evaluate':
					learn_press_update_user_item_field( array( 'graduation' => ( $mark >= $assignment->get_data( 'passing_grade' ) ? 'passed' : 'failed' ) ), array( 'user_item_id' => $user_item_id ) );

					$user_curd->update_user_item_status( $user_item_id, 'evaluated' );
					//$course_data->calculate_course_results();
					do_action( 'learn-press/instructor-evaluated-assignment', $assignment_id, $user_id );
					break;
				case 're-evaluate':
					$user_curd->update_user_item_status( $user_item_id, 'completed' );
					do_action( 'learn-press/instructor-re-evaluated-assignment', $assignment_id, $user_id );
					break;
				default:
					break;
			}

			do_action( 'learn-press/save-evaluate-form', $action );
		}

		/**
		 * Send or save answer of user
		 *
		 * @editor tungnx
		 * @modify 4.0.1
		 * @version 4.0.1
		 */
		public static function process_assignment() {
			$pressed_button = LP_Request::get_post( 'controls-button' );
			$user_note      = LP_Helper::sanitize_params_submitted( $_POST['assignment-editor-frontend'] ?? '', 'html' );
			$assignment_id  = $_POST['assignment-id'] ?? 0;
			$user           = learn_press_get_current_user();

			try {

				if ( empty( $assignment_id ) ) {
					throw new Exception( esc_html__( 'No Assignment ID param.', 'learnpress-assignment' ) );
				}

				$uploadedfiles = $_FILES['_lp_upload_file'] ?? '';

				$request = new WP_REST_Request( 'POST' );
				$request->set_header( 'Content-Type', 'multipart/form-data' );

				if ( ! empty( $uploadedfiles ) ) {
					$request->set_file_params( array( 'file' => $uploadedfiles ) );
				}

				$request->set_body_params(
					array(
						'action' => strtolower( $pressed_button ),
						'id'     => $assignment_id,
						'note'   => $user_note,
					)
				);

				$controller = new LP_Jwt_Assignment_V1_Controller();

				$response = $controller->submit_assignment( $request );

				if ( $response->is_error() ) {
					throw new Exception( $response->as_error()->get_error_message() );
				}

				$server = rest_get_server();
				$json   = $server->response_to_data( $response, false );

				if ( $json['data']['status'] == 400 ) {
					if ( ! empty( $json['items'] ) ) {
						$messages = wp_list_pluck( $json['items'], 'message' );
					}

					$error = $json['message'] ?? esc_html__( 'Response has Error !', 'learnpress-assignment' );

					if ( ! empty( $messages ) ) {
						$error = $error . ' (' . implode( ', ', $messages ) . ' ) ';
					}

					throw new Exception( $error );
				} else {
					learn_press_add_message( $json['message'] );
					wp_redirect( learn_press_get_current_url() );
					exit();
				}
			} catch ( \Throwable $th ) {
				learn_press_add_message( $th->getMessage(), 'error' );
			}

		}

		public static function learnpress_assignment_upload_dir( $dir ) {
			$assignment_id = LP_Request::get_int( 'assignment-id' );
			$user          = learn_press_get_current_user();
			$more_path     = '/assignments';

			if ( isset( $assignment_id ) && $assignment_id ) {
				$more_path .= '/' . $assignment_id;
			}

			if ( isset( $user ) && $user->get_id() ) {
				$more_path .= '/' . $user->get_id();
			}

			$dir['path']   = $dir['basedir'] . $more_path;
			$dir['url']    = $dir['baseurl'] . $more_path;
			$dir['subdir'] = $more_path;

			return $dir;
		}

		/**
		 * Start assignment
		 *
		 * @editor tungnx
		 * @throws Exception Error start assigment.
		 * @since 4.0.0
		 */
		public static function start_assignment() {
			$assignment_id = LP_Request::get_int( 'assignment-id' );

			try {
				if ( empty( $assignment_id ) ) {
					throw new Exception( esc_html__( 'No Assignment ID param.', 'learnpress-assignment' ) );
				}

				$request = new WP_REST_Request( 'POST' );
				$request->set_body_params(
					array(
						'id' => $assignment_id,
					)
				);

				$controller = new LP_Jwt_Assignment_V1_Controller();

				$response = $controller->start_assignment( $request );

				if ( $response->is_error() ) {
					throw new Exception( $response->as_error()->get_error_message() );
				}

				$server = rest_get_server();
				$json   = $server->response_to_data( $response, false );

				if ( $json['data']['status'] == 400 ) {
					throw new Exception( $json['message'] );
				} else {
					wp_redirect( learn_press_get_current_url() );
					exit();
				}
			} catch ( \Throwable $th ) {
				learn_press_add_message( $th->getMessage(), 'error' );
			}
		}

		/**
		 * Include files.
		 */
		protected function _includes() {
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . '/functions.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'custom-post-types' . DIRECTORY_SEPARATOR . 'metaboxes.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'custom-post-types' . DIRECTORY_SEPARATOR . 'assignment.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'class-lp-assignment-curd.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'class-lp-assignment.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'lp-assignment-template-functions.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'lp-assignment-template-hooks.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'admin/class-lp-assignment-admin-ajax.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'user-item/class-lp-user-item-assignment.php';

			// compatible with lp addons
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'admin/compatible/class-lp-assignment-co-instructor.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'admin/compatible/class-lp-assignment-buddypress.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'admin/compatible/class-lp-assignment-content-drip.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'admin/compatible/class-lp-assignment-gradebook.php';

			// Rest API
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'rest-api/class-lp-rest-assignment-v1-controller.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'rest-api/class-lp-evaluate-v1-controller.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'rest-api/class-rest-api.php';

			if ( ! is_admin() ) {
				$this->frontend_includes();
			}
		}

		public function frontend_includes() {
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'shortcodes/class-lp-assignments-abstract-shortcodes.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'shortcodes/class-lp-assignments-shortcode-students-answer.php';
			require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'shortcodes/class-lp-assignments-shortcode-evaluate-form.php';
		}

		/**
		 * Admin asset and localize script.
		 */
		public function admin_assets() {
			// TODO: add css and js
			$min = '.min';
			$ver = LP_ADDON_ASSIGNMENT_VER;
			if ( LP_Debug::is_debug() ) {
				$min = '';
				$ver = uniqid();
			}

			wp_enqueue_style(
				'learn-press-assignment',
				LP_Addon_Assignment_Preload::$addon->get_plugin_url( "assets/css/admin-assignment{$min}.css" ),
				array(),
				$ver
			);

			wp_register_script(
				'learn-press-assignment',
				LP_Addon_Assignment_Preload::$addon->get_plugin_url( "assets/dist/js/admin/admin-assignment{$min}.js" ),
				array( 'jquery' ),
				$ver,
				true
			);

			$screen = get_current_screen();

			if ( $screen && 'lp_assignment' === $screen->id || LP_Request::get_param( 'page' ) == 'assignment-student' ) {
				wp_enqueue_script( 'learn-press-assignment' );
			}

			wp_localize_script(
				'learn-press-assignment',
				'lpAssignment',
				array(
					'resend_evaluated_mail' => esc_html__(
						'Re-send email for student to notify assignment has been evaluated?',
						'learnpress-assignments'
					),
					'delete_submission'     => esc_html__(
						'Allow delete user\'s assignment and user can send it again?',
						'learnpress-assignments'
					),
					'reset_result'          => esc_html__(
						'Allow clear the result has evaluated?',
						'learnpress-assignments'
					),
					'invalid_mark'          => __( 'Mark value must greater than the Assignment Passing Grade value.', 'learnpress-assignments' ),
					'invalid_mark_grade'    => __( 'Assignment Passing Grade value must less than the Mark value.', 'learnpress-assignments' ),
				)
			);
		}

		public function update_status_item_content_drip( $status, $item_id, $course_id, $user ) {
			if ( get_post_type( $item_id ) == 'lp_assignment' ) {
				$status = $user->get_item_grade( $item_id, $course_id ) == 'passed' ? 'completed' : 'not_completed';
			}

			return $status;
		}

		public function add_post_type_to_check_can_edit_post( $post_type ) {
			return 'lp_assignment';
		}

		/**
		 * Set final assignment if choose evaluation is ''
		 *
		 * @param LP_Course $course
		 * @param array $data
		 *
		 * @since 4.0.9
		 * @version 1.0.0
		 * @return void
		 */
		public function course_save_background( $course, $data ) {
			$evaluation_type = get_post_meta( $course->get_id(), '_lp_course_result', true );
			if ( 'evaluate_final_assignment' !== $evaluation_type ) {
				return;
			}

			$sections_items = array_reverse( $course->get_full_sections_and_items_course() );
			foreach ( $sections_items as $k => $section_items ) {
				foreach ( array_reverse( $section_items->items ) as $item ) {
					if ( $item->type == LP_ASSIGNMENT_CPT ) {
						update_post_meta( $course->get_id(), '_lp_final_assignment', $item->id );
						//$course_passing_condition = $this->course_passing_condition( '', false, $course->get_id() );
						//update_post_meta( $course->get_id(), '_lp_passing_condition', $course_passing_condition );
						break 2;
					}
				}
			}
		}

		/**
		 * Set course passing condition.
		 *
		 * @param string $value
		 * @param bool $format
		 * @param int $course_id
		 *
		 * @since 4.0.9
		 * @version 1.0.0
		 * @return string
		 */
		public function course_passing_condition( $value, $format, $course_id ) {
			$course = learn_press_get_course( $course_id );
			if ( ! $course ) {
				return $value;
			}

			$evaluation_type = get_post_meta( $course_id, '_lp_course_result', true );
			if ( 'evaluate_final_assignment' == $evaluation_type ) {
				$assignment_id = get_post_meta( $course_id, '_lp_final_assignment', true );
				$assignment    = new LP_Assignment( $assignment_id );
				$value         = $assignment->get_passing_grade() * 100 / $assignment->get_mark();
				$value         = $value % 2 == 0 ? $value : number_format( $value, 2 );
			}

			return $value;
		}

		/**
		 * If user item has evaluated, item is complete.
		 *
		 * @param bool $item_not_completed
		 * @param LP_User_Item|null $user_item
		 *
		 * @since v4.1.1
		 * @version 1.0.0
		 * @return bool
		 */
		public function item_is_complete( $item_not_completed, $user_item ) {
			try {
				if ( ! $user_item instanceof LP_User_Item_Assignment ) {
					return $item_not_completed;
				}

				if ( $user_item->get_status() === 'evaluated' ) {
					$item_not_completed = false;
				}
			} catch ( Throwable $e ) {
				error_log( $e->getMessage() );
			}

			return $item_not_completed;
		}
	}
}
