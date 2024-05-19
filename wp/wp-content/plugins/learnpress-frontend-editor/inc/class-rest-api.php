<?php
/**
 * REST API class.
 *
 * @since  1.0.0
 * @author Nhamdv
 */

class LP_FE_Rest_API {
	protected static $_instance = null;

	const NAMESPACE = 'lp/fe/v1';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register router
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/instructor',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'instructor' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/dashboard',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'dashboard' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/dashboard-courses',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'dashboard_courses' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/dashboard-chart',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'dashboard_chart' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/courses',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_courses' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/courses/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_course' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/courses/save-courses',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_courses' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/course/add',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'add_course' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/courses/move-trash',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'move_trash_course' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/section/update',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'update_sections' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/section/get-items',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/section/add-item',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'add_item' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/section/get-questions',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_questions_list' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/section/add-questions',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'add_questions' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/lessons',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_lesson_list' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/lessons/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_lesson_by_id' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/lessons/update',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'update_lesson' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/lessons/get-settings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_lesson_settings' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/lessons/remove',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'remove_lesson' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/quiz',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_quiz_list' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/quiz/(?P<id>\d+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_quiz_by_id' ),
					'permission_callback' => function() {
						return $this->is_instructor();
					},
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'update_quiz' ),
					'permission_callback' => function() {
						return $this->is_instructor();
					},
				),
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/quiz/move-trash',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'move_trash_quiz' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/quiz/remove',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'remove_quiz' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		// Assignment.
		register_rest_route(
			self::NAMESPACE,
			'/assignment',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_assignments' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/assignment/(?P<id>\d+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_assignment' ),
					'permission_callback' => function() {
						return $this->is_instructor();
					},
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'update_assignment' ),
					'permission_callback' => function() {
						return $this->is_instructor();
					},
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/assignment/move-trash',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'move_trash_assignment' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/assignment/remove',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'remove_assignment' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/questions',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_questions_instructor' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/questions/(?P<id>\d+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_questions_by_id' ),
					'permission_callback' => function() {
						return $this->is_instructor();
					},
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'update_question' ),
					'permission_callback' => function() {
						return $this->is_instructor();
					},
				),
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/questions/move-trash',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'move_trash_question' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/questions/add-new',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'add_new_question' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/questions/remove',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'remove_question' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/settings',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => function() {
						return $this->is_instructor();
					},
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => function() {
						return $this->is_instructor();
					},
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/duplicate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'duplicate' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/update-post-status',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'update_post_status' ),
				'permission_callback' => function() {
					return $this->is_instructor();
				},
			)
		);
	}

	public function is_instructor() {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		$user = learn_press_get_user( $user_id );

		if ( ! $user ) {
			return false;
		}

		if ( $user->is_instructor() || $user->is_admin() ) {
			return true;
		}

		return false;
	}

	protected function get_course_by_item_id( $item_id ) {
		static $output;

		global $wpdb;

		if ( empty( $item_id ) ) {
			return false;
		}

		if ( ! isset( $output ) ) {
			$output = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT c.ID FROM {$wpdb->posts} c
					INNER JOIN {$wpdb->learnpress_sections} s ON c.ID = s.section_course_id
					INNER JOIN {$wpdb->learnpress_section_items} si ON si.section_id = s.section_id
					WHERE si.item_id = %d ORDER BY si.section_id DESC LIMIT 1
					",
					$item_id
				)
			);
		}

		if ( $output ) {
			return absint( $output );
		}

		return false;
	}

	public function update_sections( $request ) {
		$type      = $request->get_param( 'type' );
		$course_id = $request->get_param( 'course_id' );

		try {
			if ( empty( $course_id ) ) {
				throw new Exception( __( 'Course ID is required', 'learnpress-frontend-editor' ) );
			}

			$post = get_post( $course_id );

			// Support for co-instructor.
			$co_instructor_ids = get_post_meta( $course_id, '_lp_co_teacher', false );
			$co_instructor_ids = ! empty( $co_instructor_ids ) ? $co_instructor_ids : array();

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) && ! in_array( get_current_user_id(), $co_instructor_ids ) ) {
				throw new Exception( __( 'You are not allowed to update this assignment', 'learnpress-frontend-editor' ) );
			}

			global $wpdb;

			$section_curd = new LP_Section_CURD( absint( $course_id ) );

			$data = array();

			switch ( $type ) {
				case 'insert':
					$args = array(
						'section_course_id'   => absint( $course_id ),
						'section_name'        => sanitize_text_field( $request['title'] ),
						'section_description' => '',
					);
					$data = $section_curd->create( $args );
					break;
				case 'sort':
					$section_ids = $request->get_param( 'section_ids' );
					$section_ids = array_map( 'absint', $section_ids );
					$data        = $section_curd->update_sections_order( $section_ids );
					break;
				case 'remove':
					$section_id = $request->get_param( 'sectionId' );
					$section_id = absint( $section_id );
					$data       = $section_curd->delete( $section_id );
					break;
				case 'update':
					$section_id = $request->get_param( 'sectionId' );
					$data       = $wpdb->update(
						$wpdb->learnpress_sections,
						array(
							'section_name'        => sanitize_text_field( $request['title'] ),
							'section_description' => sanitize_text_field( $request['description'] ),
						),
						array( 'section_id' => absint( $section_id ) )
					);
					break;
				case 'add-section-item':
					$item_type  = $request->get_param( 'itemType' );
					$title      = $request->get_param( 'title' );
					$section_id = $request->get_param( 'section_id' );

					if ( empty( $title ) ) {
						throw new Exception( __( 'Title is required', 'learnpress-frontend-editor' ) );
					}

					$post_id = wp_insert_post(
						array(
							'post_title'  => $title,
							'post_status' => 'publish',
							'post_type'   => 'lp_' . $item_type,
						),
						true
					);

					if ( is_wp_error( $post_id ) ) {
						throw new Exception( $post_id->get_error_message() );
					}

					// Update default post meta.
					$default_meta = '';
					if ( $item_type === 'lesson' && class_exists( 'LP_Lesson' ) ) {
						$default_meta = LP_Lesson::get_default_meta();
					} elseif ( $item_type === 'quiz' && class_exists( 'LP_Quiz' ) ) {
						$default_meta = LP_Quiz::get_default_meta();
					} elseif ( $item_type === 'assignment' && class_exists( 'LP_Assignment' ) ) {
						$default_meta = LP_Assignment::get_default_meta();
					}

					if ( ! empty( $default_meta ) ) {
						foreach ( $default_meta as $key => $value ) {
							update_post_meta( $post_id, '_lp_' . $key, $value );
						}
					}

					$item_order = LP_Section_Items_DB::getInstance()->get_last_number_order( $section_id );

					$wpdb->insert(
						$wpdb->learnpress_section_items,
						array(
							'section_id' => absint( $section_id ),
							'item_id'    => absint( $post_id ),
							'item_order' => absint( $item_order ) + 1,
							'item_type'  => 'lp_' . $item_type,
						)
					);

					$data = array(
						'id'       => ! empty( $post_id ) ? $post_id : 0,
						'settings' => $this->get_setting_metabox( $post_id, 'lp_' . $item_type ),
					);

					if ( $item_type === 'quiz' ) {
						$data['question_types'] = $this->get_question_types();
					}

					break;
				case 'add-section-items':
					$item_ids   = ! empty( $request['itemIds'] ) ? array_map( 'absint', $request['itemIds'] ) : array();
					$section_id = ! empty( $request['section_id'] ) ? absint( $request['section_id'] ) : 0;
					$item_type  = $request['itemType'];

					$last_item_order_number = LP_Section_Items_DB::getInstance()->get_last_number_order( $section_id );

					if ( ! empty( $item_ids ) ) {
						foreach ( $item_ids as $item_id ) {
							$last_item_order_number++;

							$wpdb->insert(
								$wpdb->learnpress_section_items,
								array(
									'section_id' => $section_id,
									'item_id'    => $item_id,
									'item_order' => $last_item_order_number,
									'item_type'  => 'lp_' . $item_type,
								)
							);

							$data[] = $this->get_item_content( $item_id, 'lp_' . $item_type );
						}
					}

					break;
				case 'sort-section-items':
					$item_ids   = ! empty( $request['itemIds'] ) ? array_map( 'absint', $request['itemIds'] ) : array();
					$section_id = ! empty( $request['section_id'] ) ? absint( $request['section_id'] ) : 0;

					foreach ( $item_ids as $item_key => $item_id ) {
						$wpdb->update(
							$wpdb->learnpress_section_items,
							array( 'item_order' => $item_key + 1 ),
							array(
								'section_id' => $section_id,
								'item_id'    => $item_id,
							)
						);
					}

					break;
				case 'remove-section-item':
					$section_id = ! empty( $request['section_id'] ) ? absint( $request['section_id'] ) : 0;
					$item_id    = ! empty( $request['item_id'] ) ? absint( $request['item_id'] ) : 0;

					$wpdb->delete(
						$wpdb->learnpress_section_items,
						array(
							'section_id' => $section_id,
							'item_id'    => $item_id,
						)
					);

					learn_press_reset_auto_increment( 'learnpress_section_items' );

					break;
				default:
					throw new Exception( __( 'Invalid type', 'learnpress-frontend-editor' ) );
			}

			$course_post_type = LP_Course_Post_Type::instance();
			$course_post_type->save( $course_id );

			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => $data,
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}

	}

	/**
	 * Get instructor
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function instructor( $request ) {
		$instructor = wp_get_current_user();

		if ( ! $instructor instanceof WP_User ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Instructor not found', 'learnpress-frontend-editor' ),
				)
			);
		}

		$user = learn_press_get_user( $instructor->ID );

		$avatar = get_avatar_url( $instructor->ID );

		$profile_picture_src = $user->get_upload_profile_src();

		if ( $profile_picture_src ) {
			$user->set_data( 'profile_picture_src', $profile_picture_src );

			$avatar = $profile_picture_src;
		}

		return new WP_REST_Response(
			array(
				'success'    => true,
				'instructor' => array(
					'id'     => $instructor->ID,
					'name'   => $instructor->display_name,
					'email'  => $instructor->user_email,
					'avatar' => $avatar,
				),
			)
		);
	}

	public function update_post_status( $request ) {
		$id     = $request->get_param( 'id' );
		$status = $request->get_param( 'status' );

		try {
			$post = get_post( $id );

			if ( ! $post ) {
				throw new Exception( __( 'Post not found', 'learnpress-frontend-editor' ) );
			}

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				throw new Exception( __( 'You are not allowed to duplicate this item', 'learnpress-frontend-editor' ) );
			}

			$post->post_status = $status;

			wp_update_post( $post );

			$response = array(
				'success' => true,
				'message' => __( 'Post updated', 'learnpress-frontend-editor' ),
			);

			return new WP_REST_Response( $response );
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	/**
	 * Duplicate for course, lesson, quiz...
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function duplicate( $request ) {
		$id   = $request->get_param( 'id' );
		$type = $request->get_param( 'type' );

		try {
			$post = get_post( $id );

			if ( ! $post ) {
				throw new Exception( __( 'Item not found', 'learnpress-frontend-editor' ) );
			}

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				throw new Exception( __( 'You are not allowed to duplicate this item', 'learnpress-frontend-editor' ) );
			}

			$duplicate_args = apply_filters( 'learn-press/duplicate-post-args', array( 'post_status' => 'publish' ) );

			if ( ! function_exists( 'learn_press_duplicate_post' ) ) {
				require_once LP_PLUGIN_PATH . 'inc/admin/lp-admin-functions.php';
			}

			switch ( $type ) {
				case 'course':
					$curd        = new LP_Course_CURD();
					$new_item_id = $curd->duplicate(
						$id,
						array(
							'exclude_meta' => array(
								'order-pending',
								'order-processing',
								'order-completed',
								'order-cancelled',
								'order-failed',
								'count_enrolled_users',
								'_lp_sample_data',
								'_lp_retake_count',
							),
						)
					);
					break;
				case 'lesson':
					$curd        = new LP_Lesson_CURD();
					$new_item_id = $curd->duplicate( $id, $duplicate_args );
					break;
				case 'quiz':
					$curd        = new LP_Quiz_CURD();
					$new_item_id = $curd->duplicate( $id, $duplicate_args );
					break;
				case 'question':
					$curd        = new LP_Question_CURD();
					$new_item_id = $curd->duplicate( $id, $duplicate_args );
					break;
				default:
					break;
			}

			if ( is_wp_error( $new_item_id ) ) {
				throw new Exception( $new_item_id->get_error_message() );
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'id'      => $new_item_id,
					'message' => __( 'Item duplicated successfully', 'learnpress-frontend-editor' ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function dashboard( $request ) {
		$user_id = get_current_user_id();

		try {
			$args = array(
				'author'         => $user_id,
				'fields'         => 'ids',
				'post_type'      => LP_COURSE_CPT,
				'posts_per_page' => -1,
			);

			$total   = get_posts( $args );
			$pending = get_posts( array_merge( $args, array( 'post_status' => 'pending' ) ) );

			$lp_user_items_db = LP_User_Items_DB::getInstance();
			$filter           = new LP_User_Items_Filter();
			$filter->user_id  = $user_id;
			$count_status     = $lp_user_items_db->count_status_by_items( $filter );

			$statistic = LP_Profile::instance( $user_id )->get_statistic_info();

			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => array(
						'total_courses'      => count( $total ),
						'pending_courses'    => count( $pending ),
						'active_courses'     => $statistic['active_courses'] ?? 0,
						'total_users'        => $statistic['total_users'] ?? 0,
						'enrolled_courses'   => $statistic['enrolled_courses'] ?? 0,
						'finshed_courses'    => $statistic['completed_courses'] ?? 0,
						'passed_courses'     => $count_status->{'passed'} ?? 0,
						'failed_courses'     => $count_status->{'failed'} ?? 0,
						'inprogress_courses' => $count_status->{'in-progress'} ?? 0,
					),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function dashboard_courses( $request ) {
		try {
			$filter              = new LP_Course_Filter();
			$filter->limit       = $request['per_page'] ?? 10;
			$filter->page        = $request['page'] ? absint( $request['page'] + 1 ) : 1;
			$filter->post_author = get_current_user_id();

			$filter = LP_Course_DB::getInstance()->get_courses_order_by_popular( $filter );

			// Remove "Query get courses not attend"
			unset( $filter->union[1] );

			$total_rows  = 0;
			$pupular     = LP_Course_DB::getInstance()->get_courses( $filter, $total_rows );
			$total_pages = LP_Database::get_total_pages( $filter->limit, $total_rows );

			$output = array();

			if ( ! empty( $pupular ) ) {
				foreach ( $pupular as $course ) {
					$lp_course = learn_press_get_course( $course->ID );
					$output[]  = array(
						'ID'            => $course->ID,
						'post_title'    => $course->post_title,
						'count_student' => $course->total ?? 0,
					);
				}
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'courses' => $output,
					'pages'   => $total_pages,
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function dashboard_chart( $request ) {
		global $wpdb;

		$course_id = $request->get_param( 'course_id' );
		$year      = $request->get_param( 'year' );
		$month     = $request->get_param( 'month' );

		try {
			if ( empty( $course_id ) ) {
				throw new Exception( __( 'Course id is required', 'learnpress' ) );
			}

			$course_id = absint( $course_id );

			$labels = array(
				'enrolled' => esc_html__( 'Enrolled', 'learnpress-frontend-editor' ),
				'finished' => esc_html__( 'Finished', 'learnpress-frontend-editor' ),
			);

			if ( ! empty( $month ) ) {
				// Query count post all day on month.
				$query_enrolled_month = "SELECT COUNT(*) as count, DATE_FORMAT(start_time, '%%d') as day FROM {$wpdb->learnpress_user_items} WHERE item_type = 'lp_course' AND (status = %s OR status = %s OR status = %s ) AND item_id = %d AND YEAR(start_time) = %d AND MONTH(start_time) = %d GROUP BY day";
				$query_enrolled_month = $wpdb->prepare( $query_enrolled_month, LP_COURSE_ENROLLED, LP_COURSE_FINISHED, LP_COURSE_PURCHASED, $course_id, $year, $month );
				$enrolled_month       = $wpdb->get_results( $query_enrolled_month );

				$query_finished_month = "SELECT COUNT(*) as count, DATE_FORMAT(start_time, '%%d') as day FROM {$wpdb->learnpress_user_items} WHERE item_type = 'lp_course' AND status = %s AND item_id = %d AND YEAR(start_time) = %d AND MONTH(start_time) = %d GROUP BY day";
				$query_finished_month = $wpdb->prepare( $query_finished_month, LP_COURSE_FINISHED, $course_id, $year, $month );
				$finished_month       = $wpdb->get_results( $query_finished_month );

				$enrolled_day = wp_list_pluck( $enrolled_month, 'count', 'day' );
				$finished_day = wp_list_pluck( $finished_month, 'count', 'day' );

				$results = array();
				for ( $day = 0; $day < cal_days_in_month( 0, $month, $year ); $day++ ) {
					$d = $day + 1;
					$d = $d < 10 ? '0' . $d : $d;

					$results[] = array(
						'name'              => $d,
						$labels['enrolled'] => isset( $enrolled_day[ $d ] ) ? absint( $enrolled_day[ $d ] ) : 0,
						$labels['finished'] => isset( $finished_day[ $d ] ) ? absint( $finished_day[ $d ] ) : 0,
					);
				}
			} else {
				// quey all post on year.
				$query_enrolled_year = "SELECT COUNT(*) as count, DATE_FORMAT(start_time, '%%m') as month FROM {$wpdb->learnpress_user_items} WHERE item_type = 'lp_course' AND (status = %s OR status = %s OR status = %s ) AND item_id = %d AND YEAR(start_time) = %d GROUP BY month";
				$query_enrolled_year = $wpdb->prepare( $query_enrolled_year, LP_COURSE_ENROLLED, LP_COURSE_FINISHED, LP_COURSE_PURCHASED, $course_id, $year );
				$enrolled_year       = $wpdb->get_results( $query_enrolled_year );

				$query_finished_year = "SELECT COUNT(*) as count, DATE_FORMAT(start_time, '%%m') as month FROM {$wpdb->learnpress_user_items} WHERE item_type = 'lp_course' AND status = %s AND item_id = %d AND YEAR(start_time) = %d GROUP BY month";
				$query_finished_year = $wpdb->prepare( $query_finished_year, LP_COURSE_FINISHED, $course_id, $year );
				$finished_year       = $wpdb->get_results( $query_finished_year );

				$enrolled_month = wp_list_pluck( $enrolled_year, 'count', 'month' );
				$finished_month = wp_list_pluck( $finished_year, 'count', 'month' );

				$results = array();
				for ( $month_for = 0; $month_for < 12; $month_for++ ) {
					$m = $month_for + 1;
					$m = $m < 10 ? '0' . $m : $m;

					$results[] = array(
						'name'              => $m,
						$labels['enrolled'] => isset( $enrolled_month[ $m ] ) ? absint( $enrolled_month[ $m ] ) : 0,
						$labels['finished'] => isset( $finished_month[ $m ] ) ? absint( $finished_month[ $m ] ) : 0,
					);
				}
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => '',
					'labels'  => $labels,
					'data'    => $results,
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function get_settings( $request ) {
		$user = wp_get_current_user();

		if ( ! $user instanceof WP_User ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Instructor not found', 'learnpress-frontend-editor' ),
				)
			);
		}

		$lp_user    = learn_press_get_user( get_current_user_id() );
		$thumb_size = learn_press_get_avatar_thumb_size();

		$avatar = get_avatar_url( $user->ID );

		$profile_picture_src = $lp_user->get_upload_profile_src();

		if ( $profile_picture_src ) {
			$lp_user->set_data( 'profile_picture_src', $profile_picture_src );

			$avatar = $profile_picture_src;
		}

		return array(
			'data'                  => array(
				'first_name'   => $user->first_name ?? '',
				'last_name'    => $user->last_name ?? '',
				'email'        => $user->user_email ?? '',
				'display_name' => $user->display_name ?? '',
				'description'  => $user->description ?? '',
				'avatar'       => array(
					'width'  => $thumb_size['width'],
					'height' => $thumb_size['height'],
					'url'    => $avatar,
				),
			),
			'social'                => learn_press_get_user_extra_profile_info(),
			'register_field'        => LP_Settings::instance()->get( 'register_profile_fields' ),
			'register_field_values' => lp_get_user_custom_register_fields(),
		);
	}

	public function update_settings( $request ) {
		$user = wp_get_current_user();

		if ( ! $user instanceof WP_User ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Instructor not found', 'learnpress-frontend-editor' ),
				)
			);
		}

		$update_data = array(
			'ID'           => get_current_user_id(),
			'first_name'   => ! empty( $request['first_name'] ) ? sanitize_text_field( $request['first_name'] ) : '',
			'last_name'    => ! empty( $request['last_name'] ) ? sanitize_text_field( $request['last_name'] ) : '',
			'description'  => ! empty( $request['description'] ) ? sanitize_textarea_field( $request['description'] ) : '',
			'display_name' => ! empty( $request['display_name'] ) ? sanitize_text_field( $request['display_name'] ) : '',
			'user_email'   => ! empty( $request['email'] ) ? sanitize_email( $request['email'] ) : '',
		);

		$custom_register = array();

		if ( ! empty( $request['custom_fields'] ) ) {
			$fields = LP_Settings::instance()->get( 'register_profile_fields' );

			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					if ( $field['type'] === 'checkbox' ) {
						$custom_register[ $field['id'] ] = $request['custom_fields'][ $field['id'] ] ? 1 : 0;
					} elseif ( $field['type'] === 'textarea' ) {
						$custom_register[ $field['id'] ] = ! empty( $request['custom_fields'][ $field['id'] ] ) ? sanitize_textarea_field( $request['custom_fields'][ $field['id'] ] ) : '';
					} else {
						$custom_register[ $field['id'] ] = ! empty( $request['custom_fields'][ $field['id'] ] ) ? sanitize_text_field( $request['custom_fields'][ $field['id'] ] ) : '';
					}
				}
			}
		}

		$update = LP_Forms_Handler::update_user_data( $update_data, $custom_register );

		// Update social.
		$extra_data = get_user_meta( get_current_user_id(), '_lp_extra_info', true );
		$socials    = ! empty( $request['social'] ) ? array_map( 'sanitize_text_field', $request['social'] ) : array();

		if ( ! empty( $extra_data ) ) {
			$socials = array_merge( $extra_data, $socials );
		}

		update_user_meta( get_current_user_id(), '_lp_extra_info', $socials );

		// Update avatar.
		$profile_controller = new LP_REST_Profile_Controller();

		if ( ! empty( $request['avatar']['url'] ) && strpos( $request['avatar']['url'], 'data:image' ) !== false ) {
			$request_profile = new WP_REST_Request( 'POST' );
			$request_profile->set_body_params(
				array(
					'file' => $request['avatar']['url'],
				)
			);
			$update_avatar = $profile_controller->upload_avatar( $request_profile );
		}

		if ( empty( $request['avatar']['url'] ) ) {
			$delete_avatar = $profile_controller->remove_avatar( new WP_REST_Request( 'POST' ) );
		}

		// Update new Password.
		if ( ! empty( $request['newPassword'] ) ) {
			$new_password = trim( $request['newPassword'] );

			wp_set_password( $new_password, get_current_user_id() );
		}

		if ( is_wp_error( $update ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $update->get_error_message(),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'logout'  => empty( $request['newPassword'] ) ? false : true,
				'message' => __( 'Profile updated', 'learnpress-frontend-editor' ),
			)
		);
	}

	public function remove_question( $request ) {
		$id    = $request->get_param( 'id' );
		$trash = $request->get_param( 'trash' );

		try {
			$post = get_post( $id );

			if ( ! $post || $post->post_type !== LP_QUESTION_CPT ) {
				throw new Exception( __( 'Question not found', 'learnpress-frontend-editor' ) );
			}

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				throw new Exception( __( 'You are not allowed to delete this question', 'learnpress-frontend-editor' ) );
			}

			if ( empty( $trash ) ) {
				$delete = wp_delete_post( $id );

				if ( is_wp_error( $delete ) ) {
					throw new Exception( 'Cannot delete this question.' );
				}
			} else {
				$move_trash = wp_trash_post( $id );

				if ( is_wp_error( $move_trash ) ) {
					throw new Exception( esc_html__( 'Cannot move this question to trash', 'learnpress-frontend-editor' ) );
				}
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => empty( $trash ) ? esc_html__( 'Delete this question successfully', 'learnpress-frontend-editor' ) : esc_html__( 'This question has been moved to trash.', 'learnpress-frontend-editor' ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function add_new_question( $request ) {
		return new WP_REST_Response(
			array(
				'success' => true,
				'types'   => $this->get_question_types(),
			)
		);
	}

	public function move_trash_question( $request ) {
		$id = $request->get_param( 'question_id' );

		$post = get_post( $id );

		if ( ! $post || $post->post_type !== LP_QUESTION_CPT ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Question not found', 'learnpress-frontend-editor' ),
				)
			);
		}

		if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'You are not allowed to move this question', 'learnpress-frontend-editor' ),
				)
			);
		}

		$delete = wp_trash_post( $id );

		if ( ! $delete ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Cannot move to trash', 'learnpress-frontend-editor' ),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Quiz moved to trash', 'learnpress-frontend-editor' ),
			)
		);
	}

	public function update_question( $request ) {
		global $wpdb;

		$question_id = $request->get_param( 'id' );
		$insert      = $request['insert'] ?? false;

		try {
			$question = $request['question'];

			if ( $insert ) {
				$question_id = wp_insert_post(
					array(
						'post_type'    => LP_QUESTION_CPT,
						'post_title'   => sanitize_text_field( $question['title'] ),
						'post_content' => $question['description'] ?? '',
						'post_status'  => 'publish',
					),
					true
				);

				if ( is_wp_error( $question_id ) ) {
					throw new Exception( $question_id->get_error_message() );
				}
			}

			$post = get_post( $question_id );

			if ( ! $post || $post->post_type !== LP_QUESTION_CPT ) {
				throw new Exception( __( 'Question not found', 'learnpress-frontend-editor' ) );
			}

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				throw new Exception( __( 'You are not allowed to delete this question', 'learnpress-frontend-editor' ) );
			}

			wp_update_post(
				array(
					'ID'           => $question_id,
					'post_type'    => LP_QUESTION_CPT,
					'post_title'   => sanitize_text_field( $question['title'] ),
					'post_content' => $question['description'] ?? '',
					'post_status'  => ! empty( $request['post_status'] ) ? sanitize_text_field( $request['post_status'] ) : 'publish',
				)
			);

			update_post_meta( $question_id, '_lp_type', $question['type'] );
			update_post_meta( $question_id, '_lp_explanation', wp_kses_post( $question['explanation'] ) );
			update_post_meta( $question_id, '_lp_hint', wp_kses_post( $question['hint'] ) );
			update_post_meta( $question_id, '_lp_mark', floatval( $question['points'] ) );

			if ( isset( $question['answers'] ) ) {
				$question_db                = LP_Question::get_question( $question_id );
				$answers                    = $question_db->get_data( 'answer_options' );
				$question_answer_list_ids   = wp_list_pluck( $answers, 'question_answer_id' );
				$question_answer_update_ids = wp_list_pluck( $question['answers'], 'question_answer_id' );

				$question_answer_delete_ids = array_diff( $question_answer_list_ids, $question_answer_update_ids );

				if ( ! empty( $question_answer_delete_ids ) ) {
					foreach ( $question_answer_delete_ids as $question_answer_delete_id ) {
						$wpdb->delete(
							$wpdb->learnpress_question_answers,
							array( 'question_answer_id' => $question_answer_delete_id )
						);
					}
				}

				$new_answers = array();
				foreach ( $question['answers'] as $answer_key => $answer ) {
					$answer_id = absint( $answer['question_answer_id'] );

					if ( ! $answer_id ) {
						continue;
					}

					if ( ! in_array( $answer_id, $question_answer_list_ids ) ) {
						$wpdb->insert(
							$wpdb->learnpress_question_answers,
							array(
								'question_id' => $question_id,
								'title'       => wp_kses_post( $answer['title'] ),
								'value'       => $answer['value'],
								'order'       => $answer_key + 1,
								'is_true'     => $answer['is_correct'] ? 'yes' : 'no',
							)
						);
						$answer_id = $wpdb->insert_id;
					} else {
						$wpdb->update(
							$wpdb->learnpress_question_answers,
							array(
								'question_answer_id' => $answer_id,
								'title'              => wp_kses_post( $answer['title'] ),
								'value'              => $answer['value'],
								'is_true'            => $answer['is_correct'] ? 'yes' : 'no',
								'order'              => $answer_key + 1,
							),
							array( 'question_answer_id' => $answer_id )
						);
					}

					// Update for Fill in Blanks.
					if ( ! empty( $answer['blanks'] ) ) {
						$blanks     = $answer['blanks'];
						$new_blanks = array();

						if ( is_array( $blanks ) ) {
							foreach ( $blanks as $id => $blank ) {
								$question_db->_blanks[ $blank['id'] ] = $blank;
								$new_blanks[ $blank['id'] ]           = $blank;
							}
						}

						learn_press_update_question_answer_meta( $answer_id, '_blanks', $new_blanks );
					}

					$new_answers[] = array(
						'question_answer_id' => $answer_id,
						'title'              => wp_kses_post( $answer['title'] ),
						'value'              => $answer['value'],
						'is_true'            => $answer['is_correct'] ? 'yes' : 'no',
						'order'              => $answer_key + 1,
					);
				}

				$question_db->set_data( 'answer_options', $new_answers );
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => $insert ? __( 'Question created', 'learnpress-frontend-editor' ) : __( 'Question updated', 'learnpress-frontend-editor' ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function get_questions_instructor( $request ) {
		$user_id = get_current_user_id();

		$query_args = array(
			'post_type'      => LP_QUESTION_CPT,
			'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'trash' ),
			'posts_per_page' => 10,
		);

		$user = learn_press_get_user( $user_id );

		if ( $user->is_instructor() ) {
			$query_args['author'] = $user_id;
		}

		if ( ! empty( $request['search'] ) ) {
			$query_args['s']       = sanitize_text_field( $request['search'] );
			$query_args['orderby'] = 'relevance';
		}

		if ( ! empty( $request['paged'] ) ) {
			$query_args['paged'] = absint( $request['paged'] ) + 1;
		}

		$query       = new WP_Query();
		$result      = $query->query( $query_args );
		$total_posts = $query->found_posts;

		if ( $total_posts < 1 ) {
			unset( $query_args['paged'] );
			$count_query = new WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		$questions = array();

		$types = LP_Question::get_types();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post = get_post( get_the_ID() );

				$type = get_post_meta( $post->ID, '_lp_type', true );

				$questions[] = array(
					'id'            => get_the_ID(),
					'title'         => $post->post_title,
					'status'        => $post->post_status,
					'quiz'          => $this->get_assigned_question( get_the_ID() ),
					'type'          => $types[ $type ] ?? '',
					'author'        => get_user_by( 'ID', $post->post_author )->display_name,
					'date_modified' => lp_jwt_prepare_date_response( $post->post_modified_gmt ),
				);
			}
		}

		return new WP_REST_Response(
			array(
				'success'   => true,
				'questions' => $questions,
				'total'     => (int) $total_posts,
				'pages'     => (int) ceil( $total_posts / (int) $query->query_vars['posts_per_page'] ),
			)
		);
	}

	public function get_questions_by_id( $request ) {
		$question_id = absint( $request['id'] );

		try {
			$post = get_post( $question_id );

			if ( ! $post || $post->post_type !== LP_QUESTION_CPT ) {
				throw new Exception( __( 'Question not found', 'learnpress-frontend-editor' ) );
			}

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				throw new Exception( __( 'You are not allowed to delete this question', 'learnpress-frontend-editor' ) );
			}

			$question = LP_Question::get_question( $question_id );

			$answers = $question->get_data( 'answer_options' ) ? array_values( $question->get_data( 'answer_options' ) ) : array();
			$answers = apply_filters( 'learn-press/question-editor/question-answers-data', $answers, $question_id, 0 );

			$output = array(
				'id'          => $question_id,
				'title'       => $post->post_title ?? '',
				'description' => $post->post_content ?? '',
				'points'      => get_post_meta( $question_id, '_lp_mark', true ),
				'hint'        => get_post_meta( $question_id, '_lp_hint', true ),
				'explanation' => get_post_meta( $question_id, '_lp_explanation', true ),
				'type'        => $question->get_type(),
				'answers'     => $this->get_answers( $answers, $question->get_type() ),
			);

			return new WP_REST_Response(
				array(
					'success'  => true,
					'types'    => $this->get_question_types(),
					'question' => $output,
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function get_assigned_question( $id ) {
		$curd = new LP_Question_CURD();
		$quiz = $curd->get_quiz( $id );

		if ( $quiz ) {
			return array(
				'id'    => $quiz->ID,
				'title' => $quiz->post_title ?? '',
			);
		}

		return false;
	}

	public function move_trash_quiz( $request ) {
		$id = $request->get_param( 'quiz_id' );

		$post = get_post( $id );

		if ( ! $post || $post->post_type !== LP_QUIZ_CPT ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Quiz not found', 'learnpress-frontend-editor' ),
				)
			);
		}

		if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'You are not allowed to move this quiz', 'learnpress-frontend-editor' ),
				)
			);
		}

		$delete = wp_trash_post( $id );

		if ( ! $delete ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Cannot move to trash', 'learnpress-frontend-editor' ),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Quiz moved to trash', 'learnpress-frontend-editor' ),
			)
		);
	}

	public function move_trash_assignment( $request ) {
		$id = $request->get_param( 'assignment_id' );

		$post = get_post( $id );

		if ( ! $post || $post->post_type !== 'lp_assignment' ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Assignment not found', 'learnpress-frontend-editor' ),
				)
			);
		}

		if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'You are not allowed to move this quiz', 'learnpress-frontend-editor' ),
				)
			);
		}

		$delete = wp_trash_post( $id );

		if ( ! $delete ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Cannot move to trash', 'learnpress-frontend-editor' ),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Assignment moved to trash', 'learnpress-frontend-editor' ),
			)
		);
	}

	public function get_assignments( $request ) {
		if ( ! defined( 'LP_ASSIGNMENT_CPT' ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Assignment addon is not active', 'learnpress-frontend-editor' ),
				)
			);
		}

		$user_id = get_current_user_id();

		$query_args = array(
			'post_type'      => LP_ASSIGNMENT_CPT,
			'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'trash' ),
			'posts_per_page' => 10,
		);

		$user = learn_press_get_user( $user_id );

		if ( $user->is_instructor() ) {
			$query_args['author'] = $user_id;
		}

		if ( ! empty( $request['search'] ) ) {
			$query_args['s']       = sanitize_text_field( $request['search'] );
			$query_args['orderby'] = 'relevance';
		}

		if ( ! empty( $request['paged'] ) ) {
			$query_args['paged'] = absint( $request['paged'] ) + 1;
		}

		$query       = new WP_Query();
		$result      = $query->query( $query_args );
		$total_posts = $query->found_posts;

		if ( $total_posts < 1 ) {
			unset( $query_args['paged'] );
			$count_query = new WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		$curd = new LP_Assignment_CURD();

		$assignment = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post = get_post( get_the_ID() );

				$assignment[] = array(
					'id'            => get_the_ID(),
					'title'         => $post->post_title,
					'status'        => $post->post_status,
					'course'        => $this->get_assigned( get_the_ID() ),
					'students'      => count( $curd->get_students( get_the_ID() ) ),
					'author'        => get_user_by( 'ID', $post->post_author )->display_name,
					'date_modified' => lp_jwt_prepare_date_response( $post->post_modified_gmt ),
				);
			}
		}

		return new WP_REST_Response(
			array(
				'success'   => true,
				'assignment' => $assignment,
				'total'     => (int) $total_posts,
				'pages'     => (int) ceil( $total_posts / (int) $query->query_vars['posts_per_page'] ),
			)
		);
	}

	public function get_assignment( $request ) {
		$id = absint( $request['id'] );

		$output = $this->get_item_content( $id, 'lp_assignment' );

		return new WP_REST_Response(
			array(
				'success' => true,
				'assignment' => $output,
			)
		);
	}

	public function remove_assignment( $request ) {
		$id    = $request->get_param( 'id' );
		$trash = $request->get_param( 'trash' );

		try {
			$post = get_post( $id );

			if ( ! $post || $post->post_type !== 'lp_assignment' ) {
				throw new Exception( __( 'Assignment not found', 'learnpress-frontend-editor' ) );
			}

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				throw new Exception( __( 'You are not allowed to delete this assignment', 'learnpress-frontend-editor' ) );
			}

			if ( empty( $trash ) ) {
				$delete = wp_delete_post( $id );

				if ( is_wp_error( $delete ) ) {
					throw new Exception( 'Cannot delete this assignment.' );
				}
			} else {
				$move_trash = wp_trash_post( $id );

				if ( is_wp_error( $move_trash ) ) {
					throw new Exception( esc_html__( 'Cannot move this assignment to trash', 'learnpress-frontend-editor' ) );
				}
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => empty( $trash ) ? esc_html__( 'Delete this assignment successfully', 'learnpress-frontend-editor' ) : esc_html__( 'This assignment has been moved to trash.', 'learnpress-frontend-editor' ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function update_assignment( $request ) {
		$id = $request->get_param( 'id' );

		try {
			$data   = $request['assignment'];
			$insert = $request['insert'];

			if ( $insert ) {
				$id = wp_insert_post(
					array(
						'post_type'    => 'lp_assignment',
						'post_title'   => sanitize_text_field( $data['title'] ),
						'post_content' => $data['description'] ?? '',
						'post_status'  => 'publish',
					),
					true
				);

				if ( is_wp_error( $id ) ) {
					throw new Exception( $id->get_error_message() );
				}
			}

			$post = get_post( $id );

			if ( ! $post || $post->post_type !== 'lp_assignment' ) {
				throw new Exception( __( 'Assignment not found', 'learnpress-frontend-editor' ) );
			}

			$course_id = $this->get_course_by_item_id( $id );

			// Support for co-instructor.
			$co_instructor_ids = get_post_meta( $course_id, '_lp_co_teacher', false );
			$co_instructor_ids = ! empty( $co_instructor_ids ) ? $co_instructor_ids : array();

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) && ! in_array( get_current_user_id(), $co_instructor_ids ) ) {
				throw new Exception( __( 'You are not allowed to update this assignment', 'learnpress-frontend-editor' ) );
			}

			wp_update_post(
				array(
					'ID'           => $id,
					'post_type'    => 'lp_assignment',
					'post_title'   => sanitize_text_field( $data['title'] ),
					'post_content' => $data['description'] ?? '',
					'post_status'  => ! empty( $request['post_status'] ) ? sanitize_text_field( $request['post_status'] ) : 'publish',
				)
			);

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				\Elementor\Plugin::$instance->documents->get( $id )->set_is_built_with_elementor( ! empty( $data['is_elementor'] ) );
			}

			if ( ! empty( $data['settings'] ) ) {
				foreach ( $data['settings'] as $item_setting ) {
					foreach ( $item_setting as $setting_key => $setting_item ) {
						$this->update_setting( $id, $setting_item );
					}
				}
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => $insert ? __( 'Assignment created', 'learnpress-frontend-editor' ) : __( 'Assignment updated', 'learnpress-frontend-editor' ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function update_quiz( $request ) {
		$quiz_id = $request->get_param( 'id' );

		try {
			$data   = $request['quiz'];
			$insert = $request['insert'];

			if ( $insert ) {
				$quiz_id = wp_insert_post(
					array(
						'post_type'    => LP_QUIZ_CPT,
						'post_title'   => sanitize_text_field( $data['title'] ),
						'post_content' => $data['description'] ?? '',
						'post_status'  => 'publish',
					),
					true
				);

				if ( is_wp_error( $quiz_id ) ) {
					throw new Exception( $quiz_id->get_error_message() );
				}
			}

			$post = get_post( $quiz_id );

			if ( ! $post || $post->post_type !== LP_QUIZ_CPT ) {
				throw new Exception( __( 'Quiz not found', 'learnpress-frontend-editor' ) );
			}

			$course_id = $this->get_course_by_item_id( $quiz_id );

			// Support for co-instructor.
			$co_instructor_ids = get_post_meta( $course_id, '_lp_co_teacher', false );
			$co_instructor_ids = ! empty( $co_instructor_ids ) ? $co_instructor_ids : array();

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) && ! in_array( get_current_user_id(), $co_instructor_ids ) ) {
				throw new Exception( __( 'You are not allowed to update this quiz', 'learnpress-frontend-editor' ) );
			}

			wp_update_post(
				array(
					'ID'           => $quiz_id,
					'post_type'    => LP_QUIZ_CPT,
					'post_title'   => sanitize_text_field( $data['title'] ),
					'post_content' => $data['description'] ?? '',
					'post_status'  => ! empty( $request['post_status'] ) ? sanitize_text_field( $request['post_status'] ) : 'publish',
				)
			);

			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				\Elementor\Plugin::$instance->documents->get( $quiz_id )->set_is_built_with_elementor( ! empty( $data['is_elementor'] ) );
			}

			if ( ! empty( $data['settings'] ) ) {
				foreach ( $data['settings'] as $item_setting ) {
					$this->update_setting( $quiz_id, $item_setting );
				}
			}

			$this->update_quesions_in_quiz( $quiz_id, $data['questions'] );

			$course_post_type = LP_Course_Post_Type::instance();
			$course_post_type->save( $course_id );

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => $insert ? __( 'Quiz created', 'learnpress-frontend-editor' ) : __( 'Quiz updated', 'learnpress-frontend-editor' ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function get_quiz_by_id( $request ) {
		$quiz_id = absint( $request['id'] );

		$output = $this->get_item_content( $quiz_id, 'lp_quiz' );

		return new WP_REST_Response(
			array(
				'success' => true,
				'quiz'    => $output,
			)
		);
	}

	public function remove_quiz( $request ) {
		$id    = $request->get_param( 'id' );
		$trash = $request->get_param( 'trash' );

		try {
			$post = get_post( $id );

			if ( ! $post || $post->post_type !== LP_QUIZ_CPT ) {
				throw new Exception( __( 'Quiz not found', 'learnpress-frontend-editor' ) );
			}

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				throw new Exception( __( 'You are not allowed to delete this quiz', 'learnpress-frontend-editor' ) );
			}

			if ( empty( $trash ) ) {
				$delete = wp_delete_post( $id );

				if ( is_wp_error( $delete ) ) {
					throw new Exception( 'Cannot delete this quiz.' );
				}
			} else {
				$move_trash = wp_trash_post( $id );

				if ( is_wp_error( $move_trash ) ) {
					throw new Exception( esc_html__( 'Cannot move this quiz to trash', 'learnpress-frontend-editor' ) );
				}
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => empty( $trash ) ? esc_html__( 'Delete this quiz successfully', 'learnpress-frontend-editor' ) : esc_html__( 'This quiz has been moved to trash.', 'learnpress-frontend-editor' ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function get_quiz_list( $request ) {
		$user_id = get_current_user_id();

		$query_args = array(
			'post_type'      => LP_QUIZ_CPT,
			'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'trash' ),
			'posts_per_page' => 10,
		);

		$user = learn_press_get_user( $user_id );

		if ( $user->is_instructor() ) {
			$query_args['author'] = $user_id;
		}

		if ( ! empty( $request['search'] ) ) {
			$query_args['s']       = sanitize_text_field( $request['search'] );
			$query_args['orderby'] = 'relevance';
		}

		if ( ! empty( $request['paged'] ) ) {
			$query_args['paged'] = absint( $request['paged'] ) + 1;
		}

		$query       = new WP_Query();
		$result      = $query->query( $query_args );
		$total_posts = $query->found_posts;

		if ( $total_posts < 1 ) {
			unset( $query_args['paged'] );
			$count_query = new WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		$quiz = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post = get_post( get_the_ID() );

				$quiz[] = array(
					'id'            => get_the_ID(),
					'title'         => $post->post_title,
					'status'        => $post->post_status,
					'course'        => $this->get_assigned( get_the_ID() ),
					'author'        => get_user_by( 'ID', $post->post_author )->display_name,
					'duration'      => learn_press_get_post_translated_duration( get_the_ID(), esc_html__( 'Lifetime', 'learnpress' ) ),
					'date_modified' => lp_jwt_prepare_date_response( $post->post_modified_gmt ),
				);
			}
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'quiz'    => $quiz,
				'total'   => (int) $total_posts,
				'pages'   => (int) ceil( $total_posts / (int) $query->query_vars['posts_per_page'] ),
			)
		);
	}

	public function remove_lesson( $request ) {
		$id    = $request->get_param( 'id' );
		$trash = $request->get_param( 'trash' );

		try {
			$post = get_post( $id );

			if ( ! $post || $post->post_type !== LP_LESSON_CPT ) {
				throw new Exception( __( 'Lesson not found', 'learnpress-frontend-editor' ) );
			}

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
				throw new Exception( __( 'You are not allowed to delete this lesson', 'learnpress-frontend-editor' ) );
			}

			if ( empty( $trash ) ) {
				$delete = wp_delete_post( $id );

				if ( is_wp_error( $delete ) ) {
					throw new Exception( 'Cannot delete this lesson.' );
				}
			} else {
				$move_trash = wp_trash_post( $id );

				if ( is_wp_error( $move_trash ) ) {
					throw new Exception( esc_html__( 'Cannot move this lesson to trash', 'learnpress-frontend-editor' ) );
				}
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => empty( $trash ) ? esc_html__( 'Delete this lesson successfully', 'learnpress-frontend-editor' ) : esc_html__( 'This lesson has been moved to trash.', 'learnpress-frontend-editor' ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function get_lesson_list( $request ) {
		$user_id = get_current_user_id();

		$query_args = array(
			'post_type'      => LP_LESSON_CPT,
			'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'trash' ),
			'posts_per_page' => 10,
		);

		$user = learn_press_get_user( $user_id );

		if ( $user->is_instructor() ) {
			$query_args['author'] = $user_id;
		}

		if ( ! empty( $request['search'] ) ) {
			$query_args['s']       = sanitize_text_field( $request['search'] );
			$query_args['orderby'] = 'relevance';
		}

		if ( ! empty( $request['paged'] ) ) {
			$query_args['paged'] = absint( $request['paged'] ) + 1;
		}

		$query       = new WP_Query();
		$result      = $query->query( $query_args );
		$total_posts = $query->found_posts;

		if ( $total_posts < 1 ) {
			unset( $query_args['paged'] );
			$count_query = new WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		$lessons = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$post = get_post( get_the_ID() );

				$lessons[] = array(
					'id'            => get_the_ID(),
					'title'         => $post->post_title,
					'status'        => $post->post_status,
					'course'        => $this->get_assigned( get_the_ID() ),
					'author'        => get_user_by( 'ID', $post->post_author )->display_name,
					'preview'       => get_post_meta( get_the_ID(), '_lp_preview', true ),
					'date_modified' => lp_jwt_prepare_date_response( $post->post_modified_gmt ),
				);
			}
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'lessons' => $lessons,
				'total'   => (int) $total_posts,
				'pages'   => (int) ceil( $total_posts / (int) $query->query_vars['posts_per_page'] ),
			)
		);
	}

	public function get_lesson_by_id( $request ) {
		$lesson_id = absint( $request['id'] );

		try {
			if ( ! $lesson_id ) {
				throw new Exception( __( 'Lesson not found', 'learnpress-frontend-editor' ) );
			}

			$post = get_post( $lesson_id );

			if ( ! $post ) {
				throw new Exception( __( 'Lesson not found', 'learnpress-frontend-editor' ) );
			}

			$document = defined( 'ELEMENTOR_VERSION' ) ? \Elementor\Plugin::$instance->documents->get( absint( $lesson_id ) ) : false;

			$data = array(
				'id'           => $lesson_id,
				'name'         => $post->post_title,
				'content'      => $post->post_content,
				'status'       => $post->post_status,
				'is_elementor' => $document ? $document->is_built_with_elementor() : false,
			);

			return new WP_REST_Response(
				array(
					'success'  => true,
					'lesson'   => $data,
					'settings' => $this->get_setting_metabox( $lesson_id, LP_LESSON_CPT ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function update_lesson( $request ) {
		$lesson_id    = $request->get_param( 'id' );
		$title        = $request->get_param( 'title' );
		$description  = $request->get_param( 'description' );
		$settings     = $request->get_param( 'settings' );
		$is_elementor = $request->get_param( 'is_elementor' );
		$insert       = $request->get_param( 'insert' );
		$is_publish   = $request->get_param( 'isPublic' );

		try {
			if ( $insert ) {
				$lesson_id = wp_insert_post(
					array(
						'post_type'    => LP_LESSON_CPT,
						'post_title'   => sanitize_text_field( $title ?? '' ),
						'post_content' => $description ?? '',
						'post_status'  => 'publish',
					),
					true
				);

				if ( is_wp_error( $lesson_id ) ) {
					throw new Exception( $lesson_id->get_error_message() );
				}
			} else {
				$post = get_post( $lesson_id );

				if ( ! $post || $post->post_type !== LP_LESSON_CPT ) {
					throw new Exception( __( 'Lesson not found', 'learnpress-frontend-editor' ) );
				}

				$course_id = $this->get_course_by_item_id( $lesson_id );

				// Support for co-instructor.
				$co_instructor_ids = get_post_meta( $course_id, '_lp_co_teacher', false );
				$co_instructor_ids = ! empty( $co_instructor_ids ) ? $co_instructor_ids : array();

				if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) && ! in_array( get_current_user_id(), $co_instructor_ids ) ) {
					throw new Exception( __( 'You are not allowed to update this lesson', 'learnpress-frontend-editor' ) );
				}

				$update_arg = array(
					'ID'           => $lesson_id,
					'post_type'    => LP_LESSON_CPT,
					'post_title'   => sanitize_text_field( $title ?? '' ),
					'post_content' => $description ?? '',
				);

				if ( defined( 'ELEMENTOR_VERSION' ) ) {
					\Elementor\Plugin::$instance->documents->get( $lesson_id )->set_is_built_with_elementor( ! empty( $is_elementor ) );
				}

				if ( $is_publish ) {
					$update_arg['post_status'] = 'publish';
				}

				$update = wp_update_post( $update_arg );

				if ( is_wp_error( $update ) ) {
					throw new Exception( $update->get_error_message() );
				}
			}

			if ( ! empty( $settings ) ) {
				foreach ( $settings as $item_setting ) {
					$this->update_setting( $lesson_id, $item_setting );
				}
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => $insert ? esc_html__( 'Insert lesson successfully', 'learnpress-frontend-editor' ) : esc_html__( 'Update lesson successfully', 'learnpress-frontend-editor' ),
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function get_lesson_settings( $request ) {
		return $this->get_setting_metabox( 0, LP_LESSON_CPT );
	}

	public function get_assigned( $id ) {
		$courses = learn_press_get_item_courses( $id );

		if ( empty( $courses ) ) {
			return array();
		}

		return array(
			'id'    => $courses[0]->ID,
			'title' => $courses[0]->post_title ?? '',
		);
	}

	public function add_course( $request ) {
		$settings = $this->get_setting_course( 0 );

		return new WP_REST_Response(
			array(
				'success'  => true,
				'settings' => $this->get_setting_course( 0 ),
			)
		);
	}

	public function save_courses( $request ) {
		$general  = $request->get_param( 'general' );
		$sections = $request->get_param( 'section' );
		$insert   = $request->get_param( 'insert' );

		$course_id = absint( $general['id'] );

		try {
			global $wpdb;

			if ( ! $course_id && ! $insert ) {
				throw new Exception( __( 'Course not found', 'learnpress-frontend-editor' ) );
			}

			if ( $insert ) {
				$course_id = wp_insert_post(
					array(
						'post_type'    => LP_COURSE_CPT,
						'post_title'   => sanitize_text_field( $general['title'] ?? '' ),
						'post_content' => wp_unslash( $general['description'] ?? '' ),
						'post_status'  => ! empty( $general['post_status'] ) ? sanitize_text_field( $general['post_status'] ) : 'publish',
						'post_name'    => sanitize_text_field( $general['permalink'] ),
						'tax_input'    => array(
							'course_category' => ! empty( $general['categories'] ) ? array_map( 'absint', $general['categories'] ) : array(),
							'course_tag'      => ! empty( $general['tags'] ) ? array_map( 'absint', $general['tags'] ) : array(),
						),
					),
					true
				);

				if ( is_wp_error( $course_id ) ) {
					throw new Exception( $course_id->get_error_message() );
				}

				$post = get_post( $course_id );
			} else {
				$post = get_post( $course_id );

				if ( ! $post || $post->post_type !== LP_COURSE_CPT ) {
					throw new Exception( __( 'Course not found', 'learnpress-frontend-editor' ) );
				}

				// Support for co-instructor.
				$co_instructor_ids = get_post_meta( $post->ID, '_lp_co_teacher', false );
				$co_instructor_ids = ! empty( $co_instructor_ids ) ? $co_instructor_ids : array();

				if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) && ! in_array( get_current_user_id(), $co_instructor_ids ) ) {
					throw new Exception( __( 'You are not allowed to update this course', 'learnpress-frontend-editor' ) );
				}

				$update = wp_update_post(
					array(
						'ID'           => $course_id,
						'post_type'    => LP_COURSE_CPT,
						'post_title'   => sanitize_text_field( $general['title'] ?? '' ),
						'post_content' => wp_unslash( $general['description'] ?? '' ),
						'post_status'  => ! empty( $general['post_status'] ) ? sanitize_text_field( $general['post_status'] ) : 'publish',
						'post_name'    => sanitize_text_field( $general['permalink'] ),
						'tax_input'    => array(
							'course_category' => ! empty( $general['categories'] ) ? array_map( 'absint', $general['categories'] ) : array(),
							'course_tag'      => ! empty( $general['tags'] ) ? array_map( 'absint', $general['tags'] ) : array(),
						),
					)
				);

				if ( is_wp_error( $update ) ) {
					throw new Exception( $update->get_error_message() );
				}

				if ( defined( 'ELEMENTOR_VERSION' ) ) {
					\Elementor\Plugin::$instance->documents->get( $course_id )->set_is_built_with_elementor( ! empty( $general['is_elementor'] ) );
				}
			}

			$course = learn_press_get_course( $course_id );

			if ( ! $course ) {
				throw new Exception( __( 'Course not found', 'learnpress-frontend-editor' ) );
			}

			if ( ! empty( $general['featuredImage']['id'] ) ) {
				set_post_thumbnail( $course_id, absint( $general['featuredImage']['id'] ) );
			}

			$lp_user = learn_press_get_user( get_current_user_id() );

			// Save settings post_meta.
			if ( ! empty( $general['settings'] ) ) {
				foreach ( $general['settings'] as $setting_content ) {
					if ( ! empty( $setting_content['content'] ) ) {
						foreach ( $setting_content['content'] as $setting ) {
							if ( $lp_user->is_instructor() && in_array( $setting['id'], array( '_lp_course_author', '_lp_co_teacher' ) ) ) {
								continue;
							}

							$this->update_setting( $course_id, $setting );

							// Update post_author.
							if ( $setting['id'] === '_lp_course_author' && ! empty( $setting['value'] ) ) {
								$wpdb->update( $wpdb->posts, array( 'post_author' => absint( wp_unslash( $setting['value'] ) ) ), array( 'ID' => $course_id ) );
							}
						}
					}
				}
			}

			$course_post_type = LP_Course_Post_Type::instance();
			$course_post_type->save( $course_id, $post );

			do_action( 'save_post', $course_id, $post, true );
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => $insert ? __( 'Insert course successfully!', 'learnpress' ) : __( 'Update course successfully!', 'learnpress' ),
			)
		);
	}

	private function update_quesions_in_quiz( $quiz_id, $questions ) {
		global $wpdb;

		$quiz           = LP_Quiz::get_quiz( $quiz_id );
		$questions_list = $quiz->quiz_editor_get_questions();

		$question_ids        = wp_list_pluck( $questions_list, 'id' );
		$question_update_ids = wp_list_pluck( $questions, 'id' );
		$question_delete_ids = array_diff( $question_ids, $question_update_ids );

		if ( ! empty( $question_delete_ids ) ) {
			$quiz_curd = new LP_Quiz_CURD();

			foreach ( $question_delete_ids as $question_delete_id ) {
				$quiz_curd->remove_questions( $quiz_id, $question_delete_id );
			}
		}

		foreach ( $questions as $question_key => $question ) {
			$question_id = absint( $question['id'] );

			if ( ! $question_id ) {
				continue;
			}

			$question_post = get_post( $question_id );

			if ( ! $question_post ) {
				$question_id = wp_insert_post(
					array(
						'post_type'    => 'lp_question',
						'post_status'  => 'publish',
						'post_title'   => sanitize_text_field( $question['title'] ),
						'post_content' => $question['description'] ?? '',
						'post_author'  => get_current_user_id(),
					)
				);
			} else {
				wp_update_post(
					array(
						'ID'           => $question_id,
						'post_title'   => sanitize_text_field( $question['title'] ),
						'post_content' => $question['description'],
					)
				);
			}

			if ( ! in_array( $question_id, $question_ids ) ) {
				$wpdb->insert(
					$wpdb->prefix . 'learnpress_quiz_questions',
					array(
						'quiz_id'        => $quiz_id,
						'question_id'    => $question_id,
						'question_order' => $question_key + 1,
					),
					array( '%d', '%d', '%d' )
				);
			} else {
				$wpdb->update(
					$wpdb->prefix . 'learnpress_quiz_questions',
					array(
						'question_order' => $question_key + 1,
						'question_id'    => $question_id,
						'quiz_id'        => $quiz_id,
					),
					array( 'question_id' => $question_id )
				);
			}

			update_post_meta( $question_id, '_lp_type', $question['type'] );
			update_post_meta( $question_id, '_lp_explanation', wp_kses_post( $question['explanation'] ) );
			update_post_meta( $question_id, '_lp_hint', wp_kses_post( $question['hint'] ) );
			update_post_meta( $question_id, '_lp_mark', floatval( $question['points'] ) );

			if ( isset( $question['answers'] ) ) {
				$question_db                = LP_Question::get_question( $question_id );
				$answers                    = $question_db->get_data( 'answer_options' );
				$question_answer_list_ids   = wp_list_pluck( $answers, 'question_answer_id' );
				$question_answer_update_ids = wp_list_pluck( $question['answers'], 'question_answer_id' );

				$question_answer_delete_ids = array_diff( $question_answer_list_ids, $question_answer_update_ids );

				if ( ! empty( $question_answer_delete_ids ) ) {
					foreach ( $question_answer_delete_ids as $question_answer_delete_id ) {
						$wpdb->delete(
							$wpdb->learnpress_question_answers,
							array( 'question_answer_id' => $question_answer_delete_id )
						);
					}
				}

				$new_answers = array();
				foreach ( $question['answers'] as $answer_key => $answer ) {
					$answer_id = absint( $answer['question_answer_id'] );

					if ( ! $answer_id ) {
						continue;
					}

					if ( ! in_array( $answer_id, $question_answer_list_ids ) ) {
						$wpdb->insert(
							$wpdb->learnpress_question_answers,
							array(
								'question_id' => $question_id,
								'title'       => wp_kses_post( $answer['title'] ),
								'value'       => $answer['value'],
								'order'       => $answer_key + 1,
								'is_true'     => $answer['is_correct'] ? 'yes' : 'no',
							)
						);
						$answer_id = $wpdb->insert_id;
					} else {
						$wpdb->update(
							$wpdb->learnpress_question_answers,
							array(
								'question_answer_id' => $answer_id,
								'title'              => wp_kses_post( $answer['title'] ),
								'value'              => $answer['value'],
								'is_true'            => $answer['is_correct'] ? 'yes' : 'no',
								'order'              => $answer_key + 1,
							),
							array( 'question_answer_id' => $answer_id )
						);
					}

					// Update for Fill in Blanks.
					if ( ! empty( $answer['blanks'] ) ) {
						$blanks     = $answer['blanks'];
						$new_blanks = array();

						if ( is_array( $blanks ) ) {
							foreach ( $blanks as $id => $blank ) {
								$question_db->_blanks[ $blank['id'] ] = $blank;
								$new_blanks[ $blank['id'] ]           = $blank;
							}
						}

						learn_press_update_question_answer_meta( $answer_id, '_blanks', $new_blanks );
					}

					$new_answers[] = array(
						'question_answer_id' => $answer_id,
						'title'              => wp_kses_post( $answer['title'] ),
						'value'              => $answer['value'],
						'is_true'            => $answer['is_correct'] ? 'yes' : 'no',
						'order'              => $answer_key + 1,
					);
				}

				$question_db->set_data( 'answer_options', $new_answers );
			}
		}
	}

	public function update_setting( $id, $settings ) {
		switch ( $settings['type'] ) {
			case 'LP_Meta_Box_Text_Field':
				$value = sanitize_text_field( wp_unslash( $settings['value'] !== false ? $settings['value'] : $settings['default'] ) );

				if ( isset( $settings['extra']['type_input'] ) && $settings['extra']['type_input'] === 'number' && $value !== '' ) {
					$value = floatval( $value );

					if ( $settings['extra']['custom_attributes']['step'] === '1' ) {
						$value = (int) $value;
					}

					if ( isset( $settings['extra']['custom_attributes']['min'] ) ) {
						$min = floatval( $settings['extra']['custom_attributes']['min'] );

						if ( $value < $min ) {
							$value = $min;
						}

						if ( floatval( $settings['extra']['custom_attributes']['min'] ) >= 0 ) {
							$value = abs( $value );
						}
					}

					if ( isset( $settings['extra']['custom_attributes']['max'] ) ) {
						$max = floatval( $settings['extra']['custom_attributes']['max'] );

						if ( $value > $max ) {
							$value = $max;
						}
					}
				}

				update_post_meta( $id, $settings['id'], $value );
				break;
			case 'LP_Meta_Box_Textarea_Field':
				update_post_meta( $id, $settings['id'], wp_kses_post( wp_unslash( $settings['value'] !== false ? $settings['value'] : $settings['default'] ) ) );
				break;
			case 'LP_Meta_Box_Duration_Field':
				if ( $settings['value'] !== false && $settings['value'] !== '' ) {
					$value = sanitize_text_field( wp_unslash( $settings['value'] ) );

					$explode = explode( ' ', $value );
					$number  = (float) $explode[0] < 0 ? 0 : absint( $explode[0] );
					$unit    = $explode[1] ?? $settings['extra']['default_time'];

					$value = $number . ' ' . $unit;
				} else {
					$value = absint( wp_unslash( $settings['default'] ) ) . ' ' . $settings['extra']['default_time'];
				}

				update_post_meta( $id, $settings['id'], $value );
				break;
			case 'LP_Meta_Box_Extra_Field':
				$value = wp_unslash( $settings['value'] !== false && $settings['value'] !== '' ? $settings['value'] : $settings['default'] );
				$value = array_filter(
					$value,
					function( $item ) {
						return ! is_null( $item ) && $item !== '';
					}
				);

				update_post_meta( $id, $settings['id'], array_map( 'sanitize_text_field', array_values( $value ) ) );
				break;

			case 'LP_Meta_Box_File_Field':
				$value = wp_unslash( $settings['value'] !== false && $settings['value'] !== '' ? wp_unslash( array_map( 'absint', $settings['value'] ) ) : $settings['default'] );

				update_post_meta( $id, $settings['id'], $value );
				break;
			case 'LP_Meta_Box_Autocomplete_Field':
				$value = wp_unslash( $settings['value'] !== false && $settings['value'] !== '' ? wp_unslash( array_map( 'absint', $settings['value'] ) ) : $settings['default'] );
				$value = apply_filters( 'learn-press/admin/metabox/autocomplete/' . $settings['id'] . '/save', $value, wp_unslash( $settings['value'] ), $id );

				update_post_meta( $id, $settings['id'], $value );
				break;
			case 'LP_Meta_Box_Select_Field':
				$value = wp_unslash( $settings['value'] !== false && $settings['value'] !== '' ? wp_unslash( $settings['value'] ) : $settings['default'] );

				if ( ! empty( $settings['extra']['custom_save'] ) ) {
					do_action( 'learnpress/admin/metabox/select/save', $settings['id'], $value, $id );
				} else {
					if ( ! empty( $settings['extra']['multil_meta'] ) ) {
						$get_values = get_post_meta( $id, $settings['id'] ) ?? array();
						$new_values = $value;

						$array_get_values = ! empty( $get_values ) ? array_values( $get_values ) : array();
						$array_new_values = ! empty( $new_values ) ? array_values( $new_values ) : array();

						$del_val = array_diff( $array_get_values, $array_new_values );
						$new_val = array_diff( $array_new_values, $array_get_values );

						foreach ( $del_val as $level_id ) {
							delete_post_meta( $id, $settings['id'], $level_id );
						}

						foreach ( $new_val as $level_id ) {
							add_post_meta( $id, $settings['id'], $level_id, false );
						}
					} else {
						update_post_meta( $id, $settings['id'], $value );
					}
				}
				break;
			default:
				update_post_meta( $id, $settings['id'], wp_unslash( $settings['value'] !== false ? $settings['value'] : $settings['default'] ) ); // Cannot sanitize because "Nham bao the"
		}
	}

	public function move_trash_course( $request ) {
		$course_id = $request->get_param( 'course_id' );
		$is_draft  = $request->get_param( 'isDraft' );
		$is_delete = $request->get_param( 'isDelete' );
		$message   = '';

		try {
			$post = get_post( $course_id );

			if ( ! $post || $post->post_type !== LP_COURSE_CPT ) {
				throw new Exception( __( 'Course not found', 'learnpress-frontend-editor' ) );
			}

			// Support for co-instructor.
			$co_instructor_ids = get_post_meta( $post->ID, '_lp_co_teacher', false );
			$co_instructor_ids = ! empty( $co_instructor_ids ) ? $co_instructor_ids : array();

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) && ! in_array( get_current_user_id(), $co_instructor_ids ) ) {
				throw new Exception( __( 'You are not allowed to delete this course', 'learnpress-frontend-editor' ) );
			}

			if ( $is_delete ) {
				if ( ! current_user_can( 'manage_options' ) ) {
					throw new Exception( __( 'You are not allowed to delete this course', 'learnpress-frontend-editor' ) );
				}

				wp_delete_post( $course_id, true );

				$message = __( 'Course has been deleted', 'learnpress-frontend-editor' );
			} elseif ( $is_draft ) {
				$update = wp_update_post(
					array(
						'ID'          => $course_id,
						'post_type'   => LP_COURSE_CPT,
						'post_status' => 'draft',
					)
				);

				if ( ! $update ) {
					throw new Exception( __( 'Course cannot be moved to draft', 'learnpress-frontend-editor' ) );
				}

				$message = __( 'Course has been moved to draft', 'learnpress-frontend-editor' );
			} else {
				$delete = wp_trash_post( $course_id );

				if ( ! $delete ) {
					throw new Exception( __( 'Error when move course to trash', 'learnpress-frontend-editor' ) );
				}

				$message = __( 'Course moved to trash', 'learnpress-frontend-editor' );
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => $message,
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function add_item( $request ) {
		$type = $request->get_param( 'type' );

		if ( empty( $type ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Missing params', 'learnpress-frontend-editor' ),
				)
			);
		}

		$output = array(
			'settings' => $this->get_setting_metabox( 0, 'lp_' . $type ),
		);

		if ( $type === 'quiz' ) {
			$output['question_types'] = $this->get_question_types();
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'item'    => $output,
				'message' => __( 'Add items successfully', 'learnpress-frontend-editor' ),
			)
		);
	}

	public function add_questions( $request ) {
		$questions = $request->get_param( 'questions' );
		$quiz_id   = $request->get_param( 'quizId' );

		if ( empty( $questions ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Missing params', 'learnpress-frontend-editor' ),
				)
			);
		}

		$output = array();

		if ( ! empty( $questions ) ) {
			foreach ( $questions as $id ) {
				$question = LP_Question::get_question( $id );

				$answers = array();
				if ( is_array( $question->get_data( 'answer_options' ) ) ) {
					foreach ( $question->get_data( 'answer_options' ) as $answer ) {
						$answers[] = $answer;
					}
				}

				$post     = get_post( $id );
				$output[] = apply_filters(
					'learn-press/quiz-editor/question-data',
					array(
						'id'          => $id,
						'title'       => $post->post_title,
						'description' => $post->post_content,
						'points'      => get_post_meta( $id, '_lp_mark', true ),
						'hint'        => get_post_meta( $id, '_lp_hint', true ),
						'explanation' => get_post_meta( $id, '_lp_explanation', true ),
						'type'        => $question->get_type(),
						'answers'     => apply_filters( 'learn-press/quiz-editor/question-answers-data', $this->get_answers( $answers, $question->get_type() ), $id, absint( $quiz_id ) ),
					),
					$id,
					absint( $quiz_id )
				);
			}
		}

		return new WP_REST_Response(
			array(
				'success'   => true,
				'questions' => $output,
				'message'   => __( 'Add questions successfully', 'learnpress-frontend-editor' ),
			)
		);
	}

	public function get_questions_list( WP_REST_Request $request ) {
		$quiz_id = ! empty( $request['quizId'] ) ? absint( $request['quizId'] ) : 0;
		$search  = ! empty( $request['search'] ) ? sanitize_text_field( $request['search'] ) : '';
		$page    = ! empty( $request['page'] ) ? absint( $request['page'] ) : 1;
		$exclude = ! empty( $request['exclude'] ) ? $request['exclude'] : '';

		if ( $exclude ) {
			$exclude = json_decode( $exclude, true );
		}

		$ids_exclude = array();

		if ( is_array( $exclude ) ) {
			foreach ( $exclude as $item ) {
				$ids_exclude[] = $item['id'];
			}
		}

		if ( ! class_exists( 'LP_Modal_Search_Items' ) ) {
			require_once LP_PLUGIN_PATH . '/inc/admin/class-lp-modal-search-items.php';
		}

		$query = new LP_Modal_Search_Items(
			array(
				'type'       => 'lp_question',
				'context'    => 'quiz',
				'context_id' => $quiz_id,
				'term'       => $search,
				'limit'      => apply_filters( 'learn-press/quiz-editor/choose-items-limit', 10 ),
				'paged'      => $page,
				'exclude'    => $ids_exclude,
			)
		);

		$items = $query->get_items();

		$output = array();

		if ( ! empty( $items ) ) {
			foreach ( $items as $id ) {
				$post = get_post( $id );

				$output[] = array(
					'id'    => $post->ID,
					'title' => $post->post_title,
					'type'  => $post->post_type,
				);
			}
		}

		$pagination = $query->get_pagination( false );

		return new WP_REST_Response(
			array(
				'success' => true,
				'items'   => $output,
				'pages'   => $pagination['total'] ?? 0,
			)
		);
	}

	public function get_items( WP_REST_Request $request ) {
		$course_id = ! empty( $request['courseId'] ) ? absint( $request['courseId'] ) : '';
		$search    = ! empty( $request['search'] ) ? sanitize_text_field( $request['search'] ) : '';
		$type      = isset( $request['type'] ) ? sanitize_text_field( $request['type'] ) : '';
		$page      = ! empty( $request['page'] ) ? absint( $request['page'] ) : 1;
		$exclude   = ! empty( $request['exclude'] ) ? $request['exclude'] : '';

		if ( $exclude ) {
			$exclude = json_decode( $exclude, true );
		}

		$ids_exclude = array();

		if ( is_array( $exclude ) ) {
			foreach ( $exclude as $item ) {
				$ids_exclude[] = $item['id'];
			}
		}

		if ( ! class_exists( 'LP_Modal_Search_Items' ) ) {
			require_once LP_PLUGIN_PATH . '/inc/admin/class-lp-modal-search-items.php';
		}

		$query = new LP_Modal_Search_Items(
			array(
				'type'       => 'lp_' . $type,
				'context'    => 'course',
				'context_id' => $course_id,
				'term'       => $search,
				'limit'      => apply_filters( 'learn-press/course-editor/choose-items-limit', 10 ),
				'paged'      => $page,
				'exclude'    => $ids_exclude,
			)
		);

		$items = $query->get_items();

		$output = array();

		if ( ! empty( $items ) ) {
			foreach ( $items as $id ) {
				$post = get_post( $id );

				$output[] = array(
					'id'    => $post->ID,
					'title' => $post->post_title,
					'type'  => $post->post_type,
				);
			}
		}

		$pagination = $query->get_pagination( false );

		return new WP_REST_Response(
			array(
				'success' => true,
				'items'   => $output,
				'pages'   => $pagination['total'] ?? 0,
			)
		);
	}

	/**
	 * Get courses
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_courses( $request ) {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Instructor not found', 'learnpress-frontend-editor' ),
				)
			);
		}

		$courses    = array();
		$query_args = array(
			'post_type'      => LP_COURSE_CPT,
			'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'trash' ),
			'posts_per_page' => 8,
		);

		$user = learn_press_get_user( $user_id );

		if ( $user->is_instructor() ) {
			// Support for co-instructor plugin.
			if ( class_exists( 'LP_Co_Instructor_Preload' ) ) {
				$co_instructor_courses = LP_CO_Instructor_DB::getInstance()->get_post_of_instructor( $user_id );
			}

			if ( isset( $co_instructor_courses ) && count( $co_instructor_courses ) > 0 ) {
				$query_args['post__in'] = $co_instructor_courses;
			} else {
				$query_args['author'] = $user_id;
			}
		}

		if ( ! empty( $request['search'] ) ) {
			$query_args['s']       = sanitize_text_field( $request['search'] );
			$query_args['orderby'] = 'relevance';
		}

		if ( ! empty( $request['paged'] ) ) {
			$query_args['paged'] = absint( $request['paged'] ) + 1;
		}

		$query       = new WP_Query();
		$result      = $query->query( $query_args );
		$total_posts = $query->found_posts;

		if ( $total_posts < 1 ) {
			unset( $query_args['paged'] );
			$count_query = new WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$course = learn_press_get_course( get_the_ID() );
				$terms  = get_the_terms( get_the_ID(), 'course_category' );

				$categories = array();
				if ( $terms ) {
					$categories = wp_list_pluck( $terms, 'name' );
				}

				$post = get_post( get_the_ID() );

				$courses[] = array(
					'id'           => get_the_ID(),
					'title'        => $post->post_title,
					'image'        => get_the_post_thumbnail_url( get_the_ID(), 'full' ),
					'permalink'    => get_the_permalink( get_the_ID() ),
					'status'       => $post->post_status,
					'author'       => get_the_author(),
					'categories'   => $categories,
					'price'        => array(
						'raw'      => floatval( $course->get_price() ),
						'rendered' => $course->get_price_html(),
					),
					'origin_price' => array(
						'raw'      => floatval( $course->get_origin_price() ),
						'rendered' => $course->get_origin_price_html(),
					),
					'sale_price'   => array(
						'raw'      => floatval( $course->get_sale_price() ),
						'rendered' => learn_press_format_price( $course->get_sale_price(), true ),
					),
				);
			}
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'courses' => $courses,
				'total'   => (int) $total_posts,
				'pages'   => (int) ceil( $total_posts / (int) $query->query_vars['posts_per_page'] ),
			)
		);
	}

	/**
	 * Get course
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_course( $request ) {
		$course_id = absint( $request['id'] );

		try {
			if ( ! $course_id ) {
				throw new Exception( __( 'Course not found', 'learnpress-frontend-editor' ) );
			}

			$course = learn_press_get_course( $course_id );

			if ( ! $course ) {
				throw new Exception( esc_html__( 'Course not found', 'learnpress-frontend-editor' ) );
			}

			$post = get_post( $course_id );

			if ( ! $post || $post->post_type !== LP_COURSE_CPT ) {
				throw new Exception( __( 'Course not found', 'learnpress-frontend-editor' ) );
			}

			// Support for co-instructor.
			$co_instructor_ids = get_post_meta( $post->ID, '_lp_co_teacher', false );
			$co_instructor_ids = ! empty( $co_instructor_ids ) ? $co_instructor_ids : array();

			if ( absint( $post->post_author ) !== get_current_user_id() && ! current_user_can( 'manage_options' ) && ! in_array( get_current_user_id(), $co_instructor_ids ) ) {
				throw new Exception( __( 'You are not allowed to edit this course', 'learnpress-frontend-editor' ) );
			}

			$document = defined( 'ELEMENTOR_VERSION' ) ? \Elementor\Plugin::$instance->documents->get( absint( $course_id ) ) : false;

			$data = array(
				'id'           => $course_id,
				'name'         => $post->post_title,
				'slug'         => $post->post_name,
				'image'        => array(
					'id'  => get_post_thumbnail_id( $course_id ),
					'url' => get_the_post_thumbnail_url( $course_id, 'full' ),
				),
				'status'       => $post->post_status,
				'content'      => $post->post_content,
				'permalink'    => get_the_permalink( $course_id ),
				'custom_link'  => $this->get_sample_permalink( $course_id ),
				'categories'   => $this->get_course_taxonomy( $course_id, 'course_category' ),
				'tags'         => $this->get_course_taxonomy( $course_id, 'course_tag' ),
				'settings'     => $this->get_setting_course( $course_id ),
				'sections'     => $this->get_sections( $course ),
				'is_elementor' => $document ? $document->is_built_with_elementor() : false,
			);

			return new WP_REST_Response(
				array(
					'success' => true,
					'course'  => $data,
				)
			);
		} catch ( \Throwable $th ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $th->getMessage(),
				)
			);
		}
	}

	public function get_sample_permalink( $id, $new_title = null, $new_slug = null ) {
		$post = get_post( $id );
		if ( ! $post ) {
			return '';
		}
		if ( ! function_exists( 'get_sample_permalink' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		list($permalink, $post_name) = get_sample_permalink( $post->ID, $new_title, $new_slug );

		$view_link      = false;
		$preview_target = '';

		if ( current_user_can( 'read_post', $post->ID ) ) {
			if ( 'draft' === $post->post_status || empty( $post->post_name ) ) {
				$view_link      = get_preview_post_link( $post );
				$preview_target = " target='wp-preview-{$post->ID}'";
			} else {
				if ( 'publish' === $post->post_status || 'attachment' === $post->post_type ) {
					$view_link = get_permalink( $post );
				} else {
					// Allow non-published (private, future) to be viewed at a pretty permalink, in case $post->post_name is set.
					$view_link = str_replace( array( '%pagename%', '%postname%' ), $post->post_name, $permalink );
				}
			}
		}

		// Permalinks without a post/page name placeholder don't have anything to edit.
		if ( false === strpos( $permalink, '%postname%' ) && false === strpos( $permalink, '%pagename%' ) ) {
			return array(
				'url'          => esc_url( $view_link ),
				'display_link' => esc_html( urldecode( $permalink ) ),
				'edit_link'    => '',
			);
		} else {
			if ( mb_strlen( $post_name ) > 34 ) {
				$post_name_abridged = mb_substr( $post_name, 0, 16 ) . '&hellip;' . mb_substr( $post_name, -16 );
			} else {
				$post_name_abridged = $post_name;
			}

			return array(
				'url'          => esc_url_raw( $view_link ),
				'display_link' => str_replace( array( '%pagename%/', '%postname%' ), '', esc_html( urldecode( $permalink ) ) ),
				'edit_link'    => $post_name_abridged,
			);
		}
	}

	public function get_course_taxonomy( $id, $taxonomy ) {
		$terms  = get_the_terms( $id, $taxonomy );
		$output = array();

		if ( $terms ) {
			$output = wp_list_pluck( $terms, 'term_id' );
		}

		return $output;
	}

	public function get_sections( $course ) {
		$curriculum = $course->get_curriculum_raw();
		$user_id    = get_current_user_id();

		try {
			if ( empty( $user_id ) ) {
				throw new Exception( __( 'User not found', 'learnpress-frontend-editor' ) );
			}

			$user = learn_press_get_user( $user_id );

			if ( ! $user ) {
				throw new Exception( __( 'User not found', 'learnpress-frontend-editor' ) );
			}

			$output = array();

			if ( ! empty( $curriculum ) ) {
				foreach ( $curriculum as $section ) {
					if ( ! empty( $section ) ) {
						$data = array(
							'id'          => absint( $section['id'] ),
							'title'       => $section['title'] ?? '',
							'description' => $section['description'] ?? '',
						);

						if ( ! empty( $section['items'] ) ) {
							foreach ( $section['items'] as $key_item => $item ) {
								$data['content'][ $key_item ] = $this->get_item_content( $item['id'], $item['type'] );
							}
						} else {
							$data['content'] = array();
						}

						$output[] = $data;
					}
				}
			}

			return $output;
		} catch ( \Throwable $th ) {
			return false;
		}
	}

	public function get_item_content( $id, $type ) {
		$post = get_post( absint( $id ) );

		$document = defined( 'ELEMENTOR_VERSION' ) ? \Elementor\Plugin::$instance->documents->get( absint( $id ) ) : false;

		$output = array();

		if ( $post ) {
			$output = array(
				'id'           => absint( $id ),
				'type'         => str_replace( 'lp_', '', $type ) ?? '',
				'title'        => $post->post_title ?? '',
				'description'  => $post->post_content ?? '',
				'settings'     => $this->get_setting_metabox( absint( $id ), $type ),
				'is_elementor' => $document ? $document->is_built_with_elementor() : false,
			);

			if ( $type === LP_QUIZ_CPT ) {
				$output['types']     = $this->get_question_types();
				$output['questions'] = $this->get_questions( absint( $id ) );
			}
		}

		return $output;
	}

	public function get_setting_course( $id ) {
		if ( ! class_exists( 'LP_Meta_Box' ) ) {
			include_once LP_PLUGIN_PATH . 'inc/admin/views/meta-boxes/class-lp-meta-box.php';
		}

		if ( ! class_exists( 'LP_Meta_Box_Course' ) ) {
			include_once LP_PLUGIN_PATH . 'inc/admin/views/meta-boxes/course/settings.php';
		}

		$metabox = new LP_Meta_Box_Course();

		$output = array();

		$co_instructor_ids = get_post_meta( $id, '_lp_co_teacher', false );

		$co_instructor_ids = ! empty( $co_instructor_ids ) ? $co_instructor_ids : array();

		$metaboxes = $metabox->metabox( $id );

		$user = learn_press_get_user( get_current_user_id() );

		if ( in_array( get_current_user_id(), $co_instructor_ids ) || $user->is_instructor() ) {
			unset( $metaboxes['author'] );
		}

		foreach ( $metaboxes as $key => $tab ) {
			$data = array(
				'id'    => $key,
				'title' => $tab['label'],
			);

			if ( isset( $tab['content'] ) ) {
				foreach ( $tab['content'] as $meta_key => $object ) {
					if ( is_a( $object, 'LP_Meta_Box_Field' ) ) {
						$object->id        = $meta_key;
						$object->type      = get_class( $object );
						$object->value     = isset( $object->extra['value'] ) ? $object->extra['value'] : $object->meta_value( $id );
						$data['content'][] = $object;
					}
				}
			}

			$output[] = $data;
		}

		return $output;
	}

	public function get_setting_metabox( $id, $type ) {
		if ( ! class_exists( 'LP_Meta_Box' ) ) {
			include_once LP_PLUGIN_PATH . 'inc/admin/views/meta-boxes/class-lp-meta-box.php';
		}

		if ( $type === LP_QUIZ_CPT ) {
			if ( ! class_exists( 'LP_Meta_Box_Quiz' ) ) {
				include_once LP_PLUGIN_PATH . 'inc/admin/views/meta-boxes/quiz/settings.php';
			}

			$metabox = new LP_Meta_Box_Quiz();
		} elseif ( $type === LP_LESSON_CPT ) {
			if ( ! class_exists( 'LP_Meta_Box_Lesson' ) ) {
				include_once LP_PLUGIN_PATH . 'inc/admin/views/meta-boxes/lesson/settings.php';
			}

			$metabox = new LP_Meta_Box_Lesson();
		} elseif ( $type === LP_QUESTION_CPT ) {
			if ( ! class_exists( 'LP_Meta_Box_Question' ) ) {
				include_once LP_PLUGIN_PATH . 'inc/admin/views/meta-boxes/question/settings.php';
			}

			$metabox = new LP_Meta_Box_Question();
		} elseif ( class_exists( 'LP_Addon_Assignment_Preload' ) && $type === 'lp_assignment' ) {
			if ( ! class_exists( 'LP_Meta_Box_Assignment' ) ) {
				require_once LP_ADDON_ASSIGNMENT_INC_PATH . 'custom-post-types/metaboxes.php';
			}
			$metabox_attachments = new LP_Meta_Box_Assignment_Attachments();
			$metabox             = new LP_Meta_Box_Assignment();
		}

		$output = array();

		if ( isset( $metabox_attachments ) ) {
			foreach ( $metabox_attachments->metabox( $id ) as $meta_key => $object ) {
				if ( is_a( $object, 'LP_Meta_Box_Field' ) ) {
					$object->id              = $meta_key;
					$object->type            = get_class( $object );
					$object->value           = isset( $object->extra['value'] ) ? $object->extra['value'] : $object->meta_value( $id );
					$output['attachments'][] = $object;
				}
			}
		}

		if ( isset( $metabox ) ) {
			foreach ( $metabox->metabox( $id ) as $meta_key => $object ) {
				if ( is_a( $object, 'LP_Meta_Box_Field' ) ) {
					$object->id    = $meta_key;
					$object->type  = get_class( $object );
					$object->value = isset( $object->extra['value'] ) ? $object->extra['value'] : $object->meta_value( $id );

					if ( $type === 'lp_assignment' ) {
						$output['settings'][] = $object;
					} else {
						$output[] = $object;
					}
				}
			}
		}

		return $output;
	}

	public function get_question_types() {
		$types  = LP_Question::get_types();
		$output = array();

		if ( ! empty( $types ) ) {
			foreach ( $types as $key => $type ) {
				$output[] = array(
					'key'   => $key,
					'label' => $type,
				);
			}
		}

		return $output;
	}

	public function get_questions( $quiz_id ) {
		$quiz = LP_Quiz::get_quiz( $quiz_id );

		$questions = $quiz->quiz_editor_get_questions();

		$output = array();
		if ( ! empty( $questions ) ) {
			foreach ( $questions as $key => $question ) {
				$output[] = array(
					'id'          => $question['id'],
					'title'       => $question['title'] ?? '',
					'description' => $question['settings']['content'] ?? '',
					'points'      => $question['settings']['mark'] ? floatval( $question['settings']['mark'] ) : 0,
					'hint'        => $question['settings']['hint'] ?? '',
					'explanation' => $question['settings']['explanation'] ?? '',
					'type'        => $question['type']['key'] ?? '',
					'answers'     => $this->get_answers( $question['answers'], $question['type']['key'] ),
				);
			}
		}

		return $output;
	}

	public function get_answers( $answers, $type ) {
		$output = array();

		if ( ! empty( $answers ) ) {
			foreach ( $answers as $key => $answer ) {
				$output[ $key ] = array(
					'question_answer_id' => $answer['question_answer_id'] ? absint( $answer['question_answer_id'] ) : 0,
					'title'              => $answer['title'] ?? '',
					'value'              => $answer['value'] ?? '',
					'is_correct'         => $answer['is_true'] === 'yes' ? true : false,
				);

				if ( $type === 'fill_in_blanks' ) {
					$output[ $key ]['blanks'] = $answer['blanks'] ?? array();
				}
			}
		}

		return $output;
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}

LP_FE_Rest_API::instance();
