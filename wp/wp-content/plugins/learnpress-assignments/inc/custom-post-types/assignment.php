<?php
/**
 * Class LP_Lesson_Post_Type
 *
 * @author  ThimPress
 * @package LearnPress/Assignments/Classes
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Assignment_Post_Type' ) ) {
	/**
	 * Class LP_Assignment_Post_Type.
	 */
	final class LP_Assignment_Post_Type extends LP_Abstract_Post_Type {

		/**
		 * @var null
		 */
		protected static $_instance = null;

		/**
		 * @var array
		 */
		public static $metaboxes = array();

		/**
		 * LP_Assignment_Post_Type constructor.
		 *
		 * @param        $post_type
		 * @param string    $args
		 */
		public function __construct( $post_type, $args = '' ) {

			// posts where paged
			add_filter( 'posts_where_paged', array( $this, 'posts_where_paged' ), 10 );

			// view page
			add_filter( 'views_edit-' . LP_ASSIGNMENT_CPT, array( $this, 'views_pages' ), 10 );

			// add course assessment types in LP3.
			add_filter( 'learn_press_course_assessment_metabox', array( $this, 'update_evaluate_options' ) );

			// Add Evaluate final assignments in LP4.
			add_filter( 'learnpress/course-evaluation/methods', array( $this, 'evaluate_methods' ) );

			// add assignment link in LP settings for course
			add_filter( 'learn-press/course-settings-fields/single', array( $this, 'add_setting_course_link' ) );

			// add assignment link in LP settings for profile
			add_filter( 'learn-press/profile-settings-fields/sub-tabs', array( $this, 'add_setting_profile_link_v4' ), 10, 2 );

			// add assignment publicity in LP settings for profile.
			add_filter( 'learn-press/profile-settings-fields/publicity', array( $this, 'add_setting_profile_publicity' ) );

			add_filter( 'post_row_actions', array( $this, 'hide_view_link' ) );
			add_filter( 'page_row_actions', array( $this, 'hide_view_link' ) );

			// Add metabox to LP4.
			add_action( 'add_meta_boxes', array( $this, 'assignment_meta_boxes' ) );

			parent::__construct( $post_type, $args );
		}

		/**
		 * Register assignment post type.
		 */
		public function register() {
			register_post_type(
				LP_ASSIGNMENT_CPT,
				apply_filters(
					'lp_assignment_post_type_args',
					array(
						'labels'             => array(
							'name'               => esc_html__( 'Assignments', 'learnpress-assignments' ),
							'menu_name'          => esc_html__( 'Assignments', 'learnpress-assignments' ),
							'singular_name'      => esc_html__( 'Assignment', 'learnpress-assignments' ),
							'add_new_item'       => esc_html__( 'Add New Assignment', 'learnpress-assignments' ),
							'edit_item'          => esc_html__( 'Edit Assignment', 'learnpress-assignments' ),
							'all_items'          => esc_html__( 'Assignments', 'learnpress-assignments' ),
							'view_item'          => esc_html__( 'View Assignment', 'learnpress-assignments' ),
							'add_new'            => esc_html__( 'New Assignment', 'learnpress-assignments' ),
							'update_item'        => esc_html__( 'Update Assignment', 'learnpress-assignments' ),
							'search_items'       => esc_html__( 'Search Assignments', 'learnpress-assignments' ),
							'not_found'          => sprintf( __( 'You have not got any assignments yet. Click <a href="%s">Add new</a> to start', 'learnpress-assignments' ), admin_url( 'post-new.php?post_type=lp_assignment' ) ),
							'not_found_in_trash' => esc_html__( 'No assignment found in Trash', 'learnpress-assignments' ),
						),
						'public'             => true,
						'publicly_queryable' => true,
						'show_ui'            => true,
						'has_archive'        => false,
						'capability_type'    => LP_LESSON_CPT,
						'map_meta_cap'       => true,
						'show_in_menu'       => 'learn_press',
						'show_in_admin_bar'  => true,
						'show_in_nav_menus'  => true,
						'supports'           => array( 'title', 'editor', 'revisions' ),
						'hierarchical'       => true,
						'rewrite'            => [
							'slug'         => 'assignments',
							'hierarchical' => true,
							'with_front'   => false,
						],
					)
				)
			);
		}

		/**
		 * Metabox for Assignment in LP4.
		 *
		 * @return void
		 */
		public function assignment_meta_boxes() {
			add_meta_box( 'assignment_assigned', esc_html__( 'Assigned', 'learnpress-assignments' ), array( __CLASS__, 'assignment_assigned' ), LP_ASSIGNMENT_CPT, 'side', 'high' );
		}

		/**
		 * Add assignment meta box settings.
		 */
		public function add_meta_boxes() {
			parent::add_meta_boxes();
		}

		/**
		 * @return mixed
		 * NOT_USE_IN_LP4
		 */
		public static function settings_meta_box() {

			$meta_box = array(
				'title'      => __( 'General Settings', 'learnpress-assignments' ),
				'post_types' => LP_ASSIGNMENT_CPT,
				'context'    => 'normal',
				'priority'   => 'high',
				'fields'     => array(
					array(
						'name'         => __( 'Duration', 'learnpress-assignments' ),
						'desc'         => __( 'Duration of the assignment. Set 0 to disable.', 'learnpress-assignments' ),
						'id'           => '_lp_duration',
						'type'         => 'duration',
						'default_time' => 'day',
						'min'          => 0,
						'std'          => 3,
					),
					array(
						'name'     => __( 'Mark', 'learnpress-assignments' ),
						'id'       => '_lp_mark',
						'type'     => 'number',
						'desc'     => __( 'Maximum mark can the students receive.', 'learnpress-assignments' ),
						'min'      => 0,
						'required' => true,
						'step'     => 0.1,
						'std'      => 10,
					),
					array(
						'name'     => __( 'Passing Grade', 'learnpress-assignments' ),
						'desc'     => __( 'Requires user reached this point to pass the assignment.', 'learnpress-assignments' ),
						'id'       => '_lp_passing_grade',
						'type'     => 'number',
						'min'      => 0,
						'required' => true,
						'step'     => 0.1,
						'std'      => 8,
					),
					array(
						'name' => __( 'Re-take', 'learnpress-assignments' ),
						'id'   => '_lp_retake_count',
						'type' => 'number',
						'desc' => __( 'How many times the user can re-take this assignment. Set to 0 to disable', 'learnpress-assignments' ),
						'min'  => 0,
						'std'  => 0,
					),
					array(
						'name' => __( 'Upload files', 'learnpress-assignments' ),
						'id'   => '_lp_upload_files',
						'type' => 'number',
						'desc' => __( 'Number files the user can upload with this assignment. Set to 0 to disable', 'learnpress-assignments' ),
						'min'  => 0,
						'std'  => 1,
					),
					array(
						'name' => __( 'File Extensions', 'learnpress-assignments' ),
						'id'   => '_lp_file_extension',
						'type' => 'text',
						'desc' => __( 'Which types of file will be allowed uploading?', 'learnpress-assignments' ),
						'std'  => 'jpg,txt,zip,pdf,doc,docx,ppt',
					),
					array(
						'name' => __( 'Size Limit', 'learnpress-assignments' ),
						'id'   => '_lp_upload_file_limit',
						'type' => 'number',
						'desc' => __( 'Set Maximum Attachment size for upload ( set less than 128 MB)', 'learnpress-assignments' ),
						'min'  => 0,
						'max'  => 128,
						'std'  => 2,
					),
				),
			);

			return apply_filters( 'learn_press_assignment_general_settings_meta_box', $meta_box );
		}

		/**
		 * @return mixed
		 * NOT_USE_IN_LP4
		 */
		public function attachments_meta_box() {

			$meta_box = array(
				'title'      => __( 'Documentations', 'learnpress-assignments' ),
				'post_types' => LP_ASSIGNMENT_CPT,
				'context'    => 'normal',
				'priority'   => 'high',
				'fields'     => array(
					array(
						'name' => __( 'Attachments', 'learnpress-assignments' ),
						'desc' => __( 'Attach the related documentations here!', 'learnpress-assignments' ),
						'id'   => '_lp_attachments',
						'type' => 'file_advanced',
					),
					array(
						'name' => __( 'Introduction', 'learnpress-assignments' ),
						'desc' => __( 'Introduction about assignment.', 'learnpress-assignments' ),
						'id'   => '_lp_introduction',
						'type' => 'textarea',
					),
				),
			);

			return apply_filters( 'learn_press_assignment_attachments_meta_box', $meta_box );
		}

		/**
		 * Add assignment link in LP settings for profile.
		 *
		 * @param $settings
		 * @param $profile
		 *
		 * @return mixed
		 */
		public function add_setting_profile_link( $settings, $profile ) {

			$lp_settings  = LearnPress::instance()->settings();
			$user         = wp_get_current_user();
			$username     = $user->user_login;
			$profile_slug = 'profile';
			$profile_id   = learn_press_get_page_id( 'profile' );

			if ( $profile_id ) {
				$profile_post = get_post( $profile_id );
				$profile_slug = $profile_post->post_name;
			}
			$profile_url = site_url() . '/' . $profile_slug . '/' . $username;

			foreach ( $settings as $index => $setting ) {
				if ( isset( $setting['id'] ) && $setting['id'] == 'profile_endpoints[profile-quizzes]' ) {
					array_splice(
						$settings,
						$index + 1,
						0,
						array(
							array(
								'title'       => esc_html__( 'Assignments', 'learnpress-assignments' ),
								'id'          => 'profile_endpoints[profile-assignments]',
								'type'        => 'text',
								'default'     => 'assignments',
								'placeholder' => 'assignments',
								'desc'        => sprintf( __( 'Example link is %s', 'learnpress-assignments' ), "<code>{$profile_url}/" . $lp_settings->get( 'profile_endpoints.assignments', 'assignments' ) . '</code>' ),
							),
						)
					);
					break;
				}
			}

			return $settings;
		}

		public function add_setting_profile_link_v4( $settings, $profile ) {
			$profile_id   = learn_press_get_page_id( 'profile' );
			$lp_settings  = LearnPress::instance()->settings();
			$user         = wp_get_current_user();
			$username     = $user->user_login;
			$profile_slug = 'profile';

			if ( $profile_id ) {
				$profile_post = get_post( learn_press_get_page_id( 'profile' ) );
				$profile_slug = $profile_post->post_name;
			}
			$profile_url = site_url() . '/' . $profile_slug . '/' . $username;

			foreach ( $settings as $index => $setting ) {
				if ( isset( $setting['id'] ) && $setting['id'] == 'profile_endpoints[quizzes]' ) {
					array_splice(
						$settings,
						$index + 1,
						0,
						array(
							array(
								'title'       => esc_html__( 'Assignments', 'learnpress-assignments' ),
								'id'          => 'profile_endpoints[assignments]',
								'type'        => 'text',
								'default'     => 'assignments',
								'placeholder' => 'assignments',
								'desc'        => sprintf( __( 'Example link is %s', 'learnpress-assignments' ), "<code>{$profile_url}/" . $lp_settings->get( 'profile_endpoints.assignments', 'assignments' ) . '</code>' ),
							),
						)
					);
					break;
				}
			}

			return $settings;
		}

		/**
		 * Add assignment publicity in LP settings for profile.
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function add_setting_profile_publicity( $settings ) {
			foreach ( $settings as $index => $setting ) {
				if ( isset( $setting['id'] ) && $setting['id'] == 'profile_publicity[quizzes]' ) {
					array_splice(
						$settings,
						$index + 1,
						0,
						array(
							array(
								'title'      => __( 'Assignments', 'learnpress-assignments' ),
								'id'         => 'profile_publicity[assignments]',
								'default'    => 'no',
								'type'       => 'yes-no',
								'desc'       => __( 'Public user profile assignments.', 'learnpress-assignments' ) . learn_press_quick_tip( __( 'Allow user to turn on/off sharing profile assignments option', 'learnpress-assignments' ), false ),
								'visibility' => array(
									'state'       => 'show',
									'conditional' => array(
										array(
											'field'   => 'profile_publicity[dashboard]',
											'compare' => '=',
											'value'   => 'yes',
										),
									),
								),
							),
						)
					);
					break;
				}
			}

			return $settings;
		}

		/**
		 * Add assignment link in LP settings for course.
		 *
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function add_setting_course_link( $settings ) {
			foreach ( $settings as $index => $setting ) {
				if ( isset( $setting['id'] ) && $setting['id'] == 'quiz_slug' ) {
					array_splice(
						$settings,
						$index + 1,
						0,
						array(
							array(
								'title'   => __( 'Assignment', 'learnpress-assignments' ),
								'type'    => 'text',
								'id'      => 'assignment_slug',
								'desc'    => __( sprintf( '%s/course/sample-course/<code>assignments</code>/sample-assignment/', home_url() ), 'learnpress-assignments' ),
								'default' => 'assignments',
							),
						)
					);
					break;
				}
			}

			return $settings;
		}

		/**
		 * Add course assessment types.
		 *
		 * @param $meta_box
		 *
		 * @return mixed
		 */
		public function update_evaluate_options( $meta_box ) {
			$post_id        = LP_Request::get_int( 'post' );
			$post_id        = $post_id ? $post_id : ( ! empty( $post ) ? $post->ID : 0 );
			$course_results = get_post_meta( $post_id, '_lp_course_result', true );
			if ( $course_results == 'evaluate_final_assignment' && ! get_post_meta( $post_id, '_lp_final_assignment', true ) ) {
				$meta_box['fields'][0]['desc'] .= __( '<br /><strong>Note! </strong>No final assignment in course, please add a final assignment', 'learnpress-assignments' );
			}
			$course_result_option_desc         = array(
				'evaluate_final_assignment' => __( 'Evaluate by results of final assignment in course. You have to add a assignment into end of course.', 'learnpress-assignments' ),
				'evaluate_assignments'      => __( '<p>Evaluate by number of assignments completed per number of total assignments.</p>', 'learnpress-assignments' )
											   . __( '<p>E.g: Course has 20 assignments and user completed 15 assignments then the result = 15/20 = 75%.</p>', 'learnpress-assignments' ),
			);
			$assignment_passing_condition_html = '';

			if ( learn_press_get_course( $post_id ) ) {
				$passing_grade = '';

				if ( $final_assignment = get_post_meta( $post_id, '_lp_final_assignment', true ) ) {
					$passing_grade = get_post_meta( absint( $final_assignment ), '_lp_passing_grade', true );
					$maxi_mark     = get_post_meta( absint( $final_assignment ), '_lp_mark', true );
					$passing_grade = ( (int) $maxi_mark > 0 ) ? ( $passing_grade / $maxi_mark ) * 100 : 0;
				}

				$assignment_passing_condition_html = '
					<div id="passing-condition-assignment-result">
					<input type="number" name="_lp_course_result_final_assignment_passing_condition" value="' . absint( $passing_grade ) . '" /> %
					<p>' . __( 'This is conditional "passing grade" of Final assignment will apply for result of this course. When you change it here, the "passing grade" also change with new value for the Final assignment.', 'learnpress-assignments' ) . '</p>
					</div>
				';
			}
			if ( isset( $meta_box['fields'][0]['options'] ) ) {
				$meta_options                              = $meta_box['fields'][0]['options'];
				$meta_options['evaluate_final_assignment'] = __( 'Evaluate via results of the final assignment', 'learnpress-assignments' )
															 . learn_press_quick_tip( $course_result_option_desc['evaluate_final_assignment'], false )
															 . $assignment_passing_condition_html;
				/*
				$meta_options['evaluate_assignments']      = __( 'Evaluate via assignments', 'learnpress-assignments' )
															 . learn_press_quick_tip( $course_result_option_desc['evaluate_assignments'], false );*/ // maybe improve on next versions
				$meta_box['fields'][0]['options'] = $meta_options;
			}
			if ( isset( $meta_box['fields'][1]['visibility'] ) ) {
				$visibility                          = $meta_box['fields'][1]['visibility'];
				$visibility['conditional'][]         = array(
					'field'   => '_lp_course_result',
					'compare' => '!=',
					'value'   => 'evaluate_final_assignment',
				);
				$meta_box['fields'][1]['visibility'] = $visibility;
			}

			return $meta_box;
		}

		/**
		 * Evaluate metabox settings in LP4.
		 *
		 * @param array $methods
		 * @return array
		 */
		public function evaluate_methods( $methods ) {
			$post_id          = LP_Request::get_int( 'post' );
			$post_id          = $post_id ? $post_id : ( ! empty( $post ) ? $post->ID : 0 );
			$final_assignment = get_post_meta( $post_id, '_lp_final_assignment', true );
			$html             = '';

			if ( $final_assignment && get_post_meta( $post_id, '_lp_course_result', true ) === 'evaluate_final_assignment' ) {
				$passing_grade = get_post_meta( $final_assignment, '_lp_passing_grade', true );
				$mark          = get_post_meta( $final_assignment, '_lp_mark', true );
				$url           = get_edit_post_link( $final_assignment );
				$html          = '
					<div class="lp-metabox-evaluate-assignment">
						<div class="lp-metabox-evaluate-assignment__message">
						' . esc_html__( 'Passing Grade: ', 'learnpress-assignments' ) . ( $passing_grade / $mark ) * 100 . '% - ' . esc_html__( 'Assignment: ', 'learnpress-assignments' ) . '<a href="' . esc_url( $url ) . '">' . get_the_title( $final_assignment ) . '</a>
						</div>
					</div>
				';
			}

			$methods['evaluate_final_assignment'] = esc_html__( 'Evaluate via results of the final assignment', 'learnpress-assignments' )
			. learn_press_quick_tip( esc_html__( 'Evaluate by results of final assignment in course. You have to add a assignment into end of course.', 'learnpress-assignments' ), false )
			. '<a href="#" class="lp-metabox-evaluate-radio" data-id="' . $post_id . '" data-loading="' . esc_attr__( 'Loading...', 'learnpress-assignments' ) . '">' . esc_html__( 'Get Passing Grade', 'learnpress-assignments' ) . '</a>'
			. $html;

			return $methods;
		}

		/**
		 * @param $join
		 *
		 * @return string
		 */
		public function posts_join_paged( $join ) {
			if ( ! $this->_is_archive() ) {
				return $join;
			}

			global $wpdb;

			if ( $this->_filter_course() || ( $this->_get_orderby() == 'course-name' ) || $this->_get_search() ) {
				$join .= " LEFT JOIN {$wpdb->prefix}learnpress_section_items si ON {$wpdb->posts}.ID = si.item_id";
				$join .= " LEFT JOIN {$wpdb->prefix}learnpress_sections s ON s.section_id = si.section_id";
				$join .= " LEFT JOIN {$wpdb->posts} c ON c.ID = s.section_course_id";
			}

			return $join;
		}

		/**
		 * @param $where
		 *
		 * @return mixed|null|string|string[]
		 */
		public function posts_where_paged( $where ) {
			if ( ! $this->_is_archive() ) {
				return $where;
			}

			global $wpdb;

			if ( $course_id = $this->_filter_course() ) {
				$where .= $wpdb->prepare( ' AND (c.ID = %d)', $course_id );
			}

			if ( isset( $_GET['s'] ) ) {
				$s     = $_GET['s'];
				$where = preg_replace(
					"/\.post_content\s+LIKE\s*(\'[^\']+\')\s*\)/",
					" .post_content LIKE '%$s%' ) OR (c.post_title LIKE '%$s%' )",
					$where
				);
			}

			if ( 'yes' === LP_Request::get( 'unassigned' ) ) {
				$where .= $wpdb->prepare(
					"
                    AND {$wpdb->posts}.ID NOT IN(
                        SELECT si.item_id
                        FROM {$wpdb->learnpress_section_items} si
                        INNER JOIN wp_posts p ON p.ID = si.item_id
                        WHERE p.post_type = %s
                    )
                ",
					LP_ASSIGNMENT_CPT
				);
			}

			return $where;
		}

		/**
		 * Add filters to lesson view.
		 *
		 * @since 3.0.0
		 *
		 * @param array $views
		 *
		 * @return mixed
		 */
		public function views_pages( array $views ) {
			$count_unassigned_assignment = LP_Course_DB::getInstance()->get_total_item_unassigned( LP_ASSIGNMENT_CPT );

			if ( $count_unassigned_assignment > 0 ) {
				$views['unassigned'] = sprintf(
					'<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
					admin_url( 'edit.php?post_type=' . LP_ASSIGNMENT_CPT . '&unassigned=yes' ),
					isset( $_GET['unassigned'] ) ? 'current' : '',
					__( 'Unassigned', 'learnpress-assignments' ),
					$count_unassigned_assignment
				);
			}

			return $views;
		}

		/**
		 * Add columns to admin manage assignment page.
		 *
		 * @param  array $columns
		 *
		 * @return array
		 */
		public function columns_head( $columns ) {
			$pos = array_search( 'title', array_keys( $columns ) );
			if ( false !== $pos && ! array_key_exists( 'lp_course', $columns ) ) {
				$columns = array_merge(
					array_slice( $columns, 0, $pos + 1 ),
					array(
						'author'        => __( 'Author', 'learnpress-assignments' ),
						'lp_course'     => __( 'Course', 'learnpress-assignments' ),
						'students'      => __( 'Students', 'learnpress-assignments' ),
						'mark'          => __( 'Mark', 'learnpress-assignments' ),
						'passing_grade' => __( 'Passing Grade', 'learnpress-assignments' ),
						'duration'      => __( 'Duration', 'learnpress-assignments' ),
						'actions'       => __( 'Actions', 'learnpress-assignments' ),
					),
					array_slice( $columns, $pos + 1 )
				);
			}
			unset( $columns['taxonomy-lesson-tag'] );
			$user = wp_get_current_user();
			if ( in_array( 'lp_teacher', $user->roles ) ) {
				unset( $columns['author'] );
			}

			return $columns;
		}


		/**
		 * @return bool
		 */
		private function _get_search() {
			return isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : false;
		}

		/**
		 * @return string
		 */
		private function _get_orderby() {
			return isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '';
		}

		/**
		 * @return bool
		 */
		private function _is_archive() {
			global $pagenow, $post_type;
			if ( ! is_admin() || ( $pagenow != 'edit.php' ) || ( LP_ASSIGNMENT_CPT != $post_type ) ) {
				return false;
			}

			return true;
		}

		/**
		 * @param $order_by_statement
		 *
		 * @return string
		 */
		public function posts_orderby( $order_by_statement ) {
			if ( ! $this->_is_archive() ) {
				return $order_by_statement;
			}

			global $wpdb;

			if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {
				switch ( $_GET['orderby'] ) {
					case 'course-name':
						$order_by_statement = "c.post_title {$_GET['order']}";
						break;
					default:
						$order_by_statement = "{$wpdb->posts}.post_title {$_GET['order']}";
				}
			}

			return $order_by_statement;
		}

		/**
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function sortable_columns( $columns ) {
			$columns['author']    = 'author';
			$columns['lp_course'] = 'course-name';

			return $columns;
		}

		public function hide_view_link( $actions ) {
			global $post;

			if ( LP_ASSIGNMENT_CPT == $post->post_type ) {
				unset( $actions['view'] );
			}

			return $actions;
		}


		/**
		 * Display content for custom column
		 *
		 * @param string $name
		 * @param int    $post_id
		 */
		public function columns_content( $name, $post_id = 0 ) {
			// assignment curd
			$curd = new LP_Assignment_CURD();

			switch ( $name ) {
				case 'lp_course':
					$courses = learn_press_get_item_courses( $post_id );
					if ( $courses ) {
						foreach ( $courses as $course ) {
							echo '<div><a href="' . esc_url( add_query_arg( array( 'filter_course' => $course->ID ) ) ) . '">' . get_the_title( $course->ID ) . '</a>';
							echo '<div class="row-actions">';
							printf( '<a href="%s">%s</a>', admin_url( sprintf( 'post.php?post=%d&action=edit', $course->ID ) ), __( 'Edit', 'learnpress-assignments' ) );
							echo '&nbsp;|&nbsp;';
							printf( '<a href="%s">%s</a>', get_the_permalink( $course->ID ), __( 'View', 'learnpress-assignments' ) );
							echo '</div></div>';
						}
					} else {
						esc_html_e( 'Not assigned yet', 'learnpress-assignments' );
					}
					break;
				case 'students':
					$count = count( $curd->get_students( $post_id ) );

					echo '<span class="lp-label-counter' . ( ! $count ? ' disabled' : '' ) . '">' . $count . '</span>';
					break;
				case 'mark':
					$maximum_mark = ( get_post_meta( $post_id, '_lp_mark', true ) ) ? get_post_meta( $post_id, '_lp_mark', true ) : 10;

					echo $maximum_mark;
					break;
				case 'passing_grade':
					$passing_grade = ( get_post_meta( $post_id, '_lp_passing_grade', true ) ) ? get_post_meta( $post_id, '_lp_passing_grade', true ) : 7;

					echo $passing_grade;
					break;
				case 'duration':
					echo learn_press_get_post_translated_duration( $post_id, false );
					break;
				case 'actions':
					printf( '<a href="%s" target="">%s</a>', learn_press_assignment_students_url( array( 'assignment_id' => $post_id ) ), __( 'View', 'learnpress-assignments' ) );
					break;
					break;
				default:
					break;
			}
		}

		/**
		 * Assignment assigned view.
		 *
		 * @since 3.0.0
		 */
		public static function assignment_assigned() {
			learn_press_admin_view( 'meta-boxes/course/assigned.php' );
		}

		/**
		 * @return bool|int
		 */
		private function _filter_course() {
			return ! empty( $_REQUEST['filter_course'] ) ? absint( $_REQUEST['filter_course'] ) : false;
		}

		/**
		 * @return LP_Assignment_Post_Type|null
		 */
		public static function instance() {
			if ( ! self::$_instance ) {
				self::$_instance = new self( LP_ASSIGNMENT_CPT, array() );
			}

			return self::$_instance;
		}
	}

	// LP_Assignmen_Post_Type
	$assignment_post_type = LP_Assignment_Post_Type::instance();
}
