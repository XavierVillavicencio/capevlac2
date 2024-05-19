<?php
/**
 * LearnPress Assignment Functions
 *
 * Define common functions for both front-end and back-end
 *
 * @author   ThimPress
 * @package  LearnPress/Assignments/Functions
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'learn_press_assignment_students_url' ) ) {
	/**
	 * @param array  $args
	 * @param string $field
	 *
	 * @return string
	 */
	function learn_press_assignment_students_url( $args = array(), $field = 'assignment-nonce' ) {
		$args = wp_parse_args( $args, array( 'assignment_id' => get_the_ID() ) );

		return wp_nonce_url(
			add_query_arg( $args, 'admin.php?page=assignment-student' ),
			'learn-press-assignment-' . $args['assignment_id'],
			$field
		);
	}
}

if ( ! function_exists( 'learn_press_assignment_verify_url' ) ) {
	/**
	 * @param int    $assignment_id
	 * @param string $nonce
	 *
	 * @return bool|false|int
	 */
	function learn_press_assignment_verify_url( $assignment_id = 0, $nonce = 'assignment-nonce' ) {
		if ( ! $assignment_id ) {
			$assignment_id = get_the_ID();
		}

		return ! empty( $_REQUEST[ $nonce ] ) ? ( wp_verify_nonce( $_REQUEST[ $nonce ], 'learn-press-assignment-' . $assignment_id ) && learn_press_allow_access_admin_page( $assignment_id ) ) : false;
	}
}

if ( ! function_exists( 'learn_press_restrict_access_admin_page' ) ) {
	/**
	 * @param $assignment_id
	 *
	 * @return bool
	 */
	function learn_press_allow_access_admin_page( $assignment_id ) {
		if ( ! $assignment_id ) {
			return false;
		}

		$assignment   = get_post( $assignment_id );
		$current_user = learn_press_get_current_user();

		if ( current_user_can( 'administrator' ) || ( $current_user->is_instructor() && $current_user->get_id() == $assignment->post_author ) ) {
			return true;
		}

		return apply_filters( 'learn-press/assignments/allow-access', false, $assignment_id );
	}
}

if ( ! function_exists( 'learn_press_assignment_evaluate_url' ) ) {
	/**
	 * @param array  $args
	 * @param string $field
	 *
	 * @return string
	 */
	function learn_press_assignment_evaluate_url( $args = array(), $field = 'assignment-nonce' ) {
		$args = wp_parse_args(
			$args,
			array(
				'assignment_id' => get_the_ID(),
				'user_id'       => 0,
			)
		);

		return wp_nonce_url(
			add_query_arg( $args, 'admin.php?page=assignment-evaluate' ),
			'learn-press-assignment-' . $args['assignment_id'],
			$field
		);
	}
}

if ( ! function_exists( 'learn_press_get_assignment' ) ) {
	/**
	 * @param $assignment
	 *
	 * @return bool|LP_Assignment
	 */
	function learn_press_get_assignment( $assignment ) {
		return LP_Assignment::get_assignment( $assignment );
	}
}

if ( ! function_exists( 'learn_press_assignment_admin_view' ) ) {
	/**
	 * Admin view.
	 *
	 * @param $name
	 * @param string $args
	 */
	function learn_press_assignment_admin_view( $name, $args = '' ) {
		if ( ! preg_match( '~.php$~', $name ) ) {
			$name .= '.php';
		}

		if ( is_array( $args ) ) {
			extract( $args );
		}

		include LP_ADDON_ASSIGNMENT_INC_PATH . "admin/views/{$name}";
	}
}

function learn_press_assignment_item_prefixes( $custom_prefixes, $course_id ) {
	$custom_prefixes[ LP_ASSIGNMENT_CPT ] = sanitize_title_with_dashes( LP_Settings::get_option( 'assignment_slug', 'assignments' ) );

	return $custom_prefixes;
}

add_filter( 'learn-press/course/custom-item-prefixes', 'learn_press_assignment_item_prefixes', 10, 2 );

function learn_press_assignment_item_slugs( $slugs ) {
	$slugs[ LP_ASSIGNMENT_CPT ] = 'assignment';

	return $slugs;
}

add_filter( 'learn-press/course/custom-item-slugs', 'learn_press_assignment_item_slugs' );

// return apply_filters( 'learn-press/course-support-items', $keys ? array_keys( $types ) : $types, $keys );

if ( ! function_exists( 'learn_press_course_assignment_class' ) ) {
	/**
	 * The class of lesson in course curriculum
	 *
	 * @param int          $assignment_id
	 * @param array|string $class
	 */
	function learn_press_course_assignment_class( $assignment_id = null, $class = null ) {
		if ( is_string( $class ) && $class ) {
			$class = preg_split( '!\s+!', $class );
		} else {
			$class = array();
		}

		$classes = array(
			'course-assignment course-item course-item-' . $assignment_id,
		);

		if ( LearnPress::instance()->user->has_completed_item( $assignment_id ) ) {
			$classes[] = 'item-completed';
		}

		if ( $assignment_id && LearnPress::instance()->course->is_current - item( $assignment_id ) ) {
			$classes[] = 'item-current';
		}

		if ( learn_press_is_course() ) {
			$course = LearnPress::instance()->course;
			if ( $course->is_free() ) {
				$classes[] = 'free-item';
			}
		}

		$classes = array_unique( array_merge( $classes, $class ) );

		echo 'class="' . implode( ' ', $classes ) . '"';
	}
}

if ( ! function_exists( 'learn_press_assignment_get_template' ) ) {
	/**
	 * @param $template_name
	 * @param array         $args
	 * @param string        $template_path
	 * @param string        $default_path
	 */
	function learn_press_assignment_get_template(
		$template_name,
		$args = array(),
		$template_path = '',
		$default_path = ''
	) {
		learn_press_get_template(
			$template_name,
			$args,
			learn_press_template_path() . '/addons/assignments/',
			LP_ADDON_ASSIGNMENT_PATH . '/templates/'
		);
	}
}

if ( ! function_exists( 'learn_press_assignment_locate_template' ) ) {
	/**
	 * @param $template_name
	 * @param string        $template_path
	 * @param string        $default_path
	 *
	 * @return mixed
	 */
	function learn_press_assignment_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = learn_press_template_path();
		}

		if ( ! $default_path ) {
			$default_path = LP_ADDON_ASSIGNMENT_PATH . '/templates/';
		}

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Get default template
		if ( ! $template ) {
			$template = trailingslashit( $default_path ) . $template_name;
		}

		// Return what we found
		return apply_filters( 'learn-press/assignment/locate-template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'learn_press_assignment_get_template_part' ) ) {
	function learn_press_assignment_get_template_part( $slug, $name = '' ) {
		$template = '';

		// Look in yourtheme/slug-name.php and yourtheme/learnpress/slug-name.php
		if ( $name ) {
			$template = locate_template(
				array(
					"{$slug}-{$name}.php",
					learn_press_assignment_template_path() . "/{$slug}-{$name}.php",
				)
			);
		}

		// Get default slug-name.php
		if ( ! $template && $name && file_exists( LP_ADDON_ASSIGNMENT_PATH . "/templates/{$slug}-{$name}.php" ) ) {
			$template = LP_ADDON_ASSIGNMENT_PATH . "/templates/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/learnpress/slug.php
		if ( ! $template ) {
			$template = locate_template(
				array(
					"{$slug}.php",
					learn_press_assignment_template_path() . "/{$slug}.php",
				)
			);
		}

		// Allow 3rd party plugin filter template file from their plugin
		if ( $template ) {
			$template = apply_filters( 'learn_press_assignment_get_template_part', $template, $slug, $name );
		}

		return $template;
	}
}

if ( ! function_exists( 'learn_press_assignment_template_path' ) ) {

	function learn_press_assignment_template_path() {
		return 'learnpress/addons/assignments';
	}
}

/**
 * @depecated 4.0.6
 */
/*if ( ! function_exists( '_evaluate_course_by_final_assignment' ) ) {
	function _evaluate_course_by_final_assignment( $user_course ) {
		$data = wp_cache_get(
			'user-course-' . $user_course->get_user_id() . '-' . $user_course->get_id(),
			'lp-user-course-results/evaluate-by-final-assignment'
		);

		if ( false === $data ) {
			$course           = $user_course->get_course();
			$final_assignment = get_post_meta( $course->get_id(), '_lp_final_assignment', true );

			if ( $final_assignment ) {
				$result = learn_press_assignment_get_result( $final_assignment, $user_course->get_user_id(), $course->get_id() );
			} else {
				$result = false;
			}

			$percent = $result ? $result['result'] : 0;
			$data    = array(
				'result' => $percent,
				'grade'  => $result['grade'] ? $result['grade'] : '',
				'status' => $user_course->get_status(),
				'pass'   => 0,
			);

			if ( $result['grade'] == 'passed' ) {
				$data['pass']   = 1;
				$data['status'] = LP_COURSE_FINISHED;
			}

			wp_cache_set(
				'user-course-' . $user_course->get_user_id() . '-' . $user_course->get_id(),
				$data,
				'lp-user-course-results/evaluate-by-final-assignment'
			);
		}

		return $data;
	}
}*/

if ( ! function_exists( 'learn_press_user_can_view_assignment' ) ) {
	function learn_press_user_can_view_assignment() {
		return true;
	}
}

if ( ! function_exists( 'learn_press_get_assignment_by_id' ) ) {
	function learn_press_get_assignment_by_id( $id = null ) {
		$assignment = new WP_Query(
			apply_filters(
				'learn_press_get_assignment_by_idquery',
				array(
					'post_type'  => 'lp_assignment',
					'post_staus' => 'publish',
					'ID'         => $id,
				)
			)
		);
		wp_reset_query();

		return $assignment;
	}
}

if ( ! function_exists( 'learn_press_single_assignment_part' ) ) {
	function learn_press_single_assignment_part( $template_name ) {
		$default_path = LP_ADDON_ASSIGNMENT_PATH . '/templates/';
		$template     = locate_template(
			array(
				trailingslashit( learn_press_assignment_template_path() ) . $template_name,
				$template_name,
			)
		);

		if ( ! $template ) {
			$template = trailingslashit( $default_path ) . $template_name;
		}

		if ( ! file_exists( $template ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template ), '2.1' );

			return;
		}

		include $template;
	}
}

if ( ! function_exists( 'learn_press_assignment_get_uploaded_files' ) ) {
	function learn_press_assignment_get_uploaded_files( $current_useritem_id ) {
		$uploaded_file = array();
		try {
			$assignment_db      = LP_Assigment_DB::getInstance();
			$uploaded_file_meta = $assignment_db->get_extra_value(
				$current_useritem_id,
				$assignment_db::$answer_upload_key
			);

			if ( empty( $uploaded_file_meta ) ) {
				return $uploaded_file;
			}

			$uploaded_file_meta = (array) json_decode( $uploaded_file_meta );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				$uploaded_file = $uploaded_file_meta;
			}
		} catch ( Throwable $e ) {
			error_log( $e->getMessage() );
		}

		return $uploaded_file;
	}
}

if ( ! function_exists( 'learn_press_assignment_single_args' ) ) {
	function learn_press_assignment_single_args() {
		$args               = array();
		$course             = learn_press_get_course();
		$current_assignment = LP_Global::course_item();

		if ( $course && $current_assignment ) {
			$user            = learn_press_get_current_user();
			$assignment_data = $user->get_item_data( $current_assignment->get_id(), $course->get_id() );
			$uploaded        = '';

			if ( $assignment_data ) {
				$remaining_time = learn_press_assignment_get_time_remaining( $assignment_data );
				$uploaded       = metadata_exists( 'learnpress_user_item', $assignment_data->get_user_item_id(), LP_Assigment_DB::$answer_note_key );
			} else {
				$remaining_time = false;
			}

			$args = array(
				'id'                => $current_assignment->get_id(),
				'totalTime'         => $current_assignment->get_duration()->get(),
				'remainingTime'     => $remaining_time ? $remaining_time->get() : $current_assignment->get_duration()->get(),
				'status'            => $user->get_item_status(
					$current_assignment->get_id(),
					$course->get_id()
				),
				'singular_day_text' => __( ' day left', 'learnpress-assignments' ),
				'plural_day_text'   => __( ' days left', 'learnpress-assignments' ),
				'uploaded'          => $uploaded,
				'root'              => esc_url_raw( rest_url() ),
				'nonce'             => wp_create_nonce( 'wp_rest' ),
			);
		}

		return $args;
	}
}

if ( ! function_exists( 'learn_press_assignment_get_time_remaining' ) ) {
	function learn_press_assignment_get_time_remaining( $assignment_data ) {
		if ( ! $assignment_data ) {
			return 0;
		}
		$assignment          = LP_Assignment::get_item( $assignment_data->get_id() );
		$assignment_duration = $assignment->get_duration();
		$diff                = false;

		if ( $assignment_duration && $assignment_duration->get_seconds() >= 0 ) {

			$filter            = new LP_User_Items_Filter();
			$filter->parent_id = $assignment_data->get_parent_id();
			$filter->item_id   = $assignment_data->get_id();
			$filter->user_id   = get_current_user_id();
			$user_assingment   = LP_User_Items_DB::getInstance()->get_user_course_item( $filter );

			if ( ! $user_assingment ) {
				return 0;
			}

			$diff = strtotime( current_time( 'mysql', 1 ) ) - strtotime( $user_assingment->start_time );

			$diff = $assignment_duration->diff( $diff )->get_seconds();

			if ( $diff <= 0 ) {
				$diff = 0;
			}
		}

		$remaining = $diff !== false ? new LP_Duration( $diff ) : false;

		return $remaining;
	}
}

if ( ! function_exists( 'learn_press_assignment_start' ) ) {
	/**
	 * @param LP_User $user
	 * @param $assignment_id
	 * @param $course_id
	 * @param $action
	 * @param $wp_error
	 *
	 * @return int|mixed|string|void|WP_Error
	 */
	function learn_press_assignment_start( $user, $assignment_id, $course_id, $action = 'start', $wp_error = false ) {
		try {
			$item_id      = learn_press_get_request( 'lp-preview' );
			$user_item_id = 0;

			if ( $item_id ) {
				learn_press_add_message(
					__( 'You cannot start a assignment in preview mode.', 'learnpress-assignments' ),
					'error'
				);
				wp_redirect( learn_press_get_preview_url( $item_id ) );
				exit();
			}

			// Validate course and quiz
			$course = learn_press_get_course( $course_id );
			if ( false === ( $course->has_item( $assignment_id ) ) ) {
				throw new Exception(
					__( 'Course does not exist or does not contain the assignment', 'learnpress-assignments' ),
					'lp_assignment_start_error'
				);
			}

			// If user has already finished the course
			if ( $user->has_finished_course( $course_id ) ) {
				throw new Exception(
					__( 'User has already finished the course of this assignment', 'learnpress-assignments' ),
					'lp_assignment_start_error'
				);
			}

			if ( 'start' == $action ) {
				// Check if user has already started or completed quiz
				if ( $user->has_item_status( array( 'started', 'completed' ), $assignment_id, $course_id ) ) {
					throw new Exception(
						__( 'User has started or completed assignment', 'learnpress-assignments' ),
						'lp_assignment_start_error'
					);
				}
			}

			if ( $course->is_required_enroll() && $user instanceof LP_User_Guest ) {
				throw new Exception(
					__( 'You have to login for starting assignment.', 'learnpress-assignments' ),
					'lp_assignment_start_error'
				);
			}

			$course_data = $user->get_course_data( $course->get_id() );
			if ( ! $course_data ) {
				throw new Exception(
					__( 'Course data is not found', 'learnpress-assignments' ),
					'lp_assignment_start_error'
				);
			}

			$user_item = $course_data->get_item( $assignment_id );
			if ( $user_item ) {
				$user_item_id = $user_item->get_user_item_id();
			}

			$return = learn_press_update_assignment_item( $assignment_id, $course_id, $user, 'started', $user_item_id );
			if ( ! $return ) {
				do_action( 'learn-press/user/start-assignment-failed', $assignment_id, $course_id, $user->get_id() );
				throw new Exception( __( 'Start assignment failed!', 'learnpress-assignments' ), 99 );
			}

			if ( 'retake' == $action ) {
				$current_redo_time = learn_press_get_user_item_meta( $user_item_id, '_lp_assignment_retaken', true );
				learn_press_delete_user_item_meta( $user_item_id, '_lp_assignment_mark' );
				learn_press_delete_user_item_meta( $user_item_id, '_lp_assignment_instructor_note' );
				learn_press_delete_user_item_meta( $user_item_id, '_lp_assignment_evaluate_upload' );
				learn_press_delete_user_item_meta( $user_item_id, '_lp_assignment_evaluate_author' );
				$current_redo_time = ( $current_redo_time ) ? $current_redo_time : 0;
				learn_press_update_user_item_meta( $user_item_id, '_lp_assignment_retaken', $current_redo_time + 1 );

				$return = $user_item_id;
			}
		} catch ( Exception $ex ) {
			$return = $wp_error ? new WP_Error( $ex->getCode(), $ex->getMessage() ) : false;
		}

		return $return;
	}
}

if ( ! function_exists( 'learn_press_update_assignment_item' ) ) {
	/**
	 * @param $assignment_id
	 * @param $course_id
	 * @param LP_User $user
	 * @param $status
	 * @param $user_itemid
	 *
	 * @return false|int|mixed|string
	 */
	function learn_press_update_assignment_item( $assignment_id, $course_id, $user, $status, $user_itemid = '' ) {
		global $wpdb;

		$course_data = $user->get_course_data( $course_id );
		if ( ! $course_data ) {
			return false;
		}

		$user_id = $user->get_id();

		$item_data = array(
			'user_id'      => $user_id,
			'item_id'      => $assignment_id,
			'user_item_id' => $user_itemid,
			'end_time'     => '0000-00-00 00:00:00',
			'item_type'    => LP_ASSIGNMENT_CPT,
			'status'       => $status,
			'ref_id'       => $course_id,
			'ref_type'     => LP_COURSE_CPT,
			'parent_id'    => $course_data->get_user_item_id(),
		);

		if ( $status == 'started' ) {
			$start_time              = current_time( 'mysql', 1 );
			$item_data['start_time'] = $start_time;
		} elseif ( $status == 'completed' ) {
			$end_time              = current_time( 'mysql', 1 );
			$item_data['end_time'] = $end_time;
		}

		$query = $wpdb->prepare(
			"
            SELECT ui.*
            FROM {$wpdb->learnpress_user_items} ui
            WHERE item_type = %s
                AND user_id = %d
                AND item_id = %d
            ORDER BY user_item_id DESC
            LIMIT 0, 1
        ",
			LP_ASSIGNMENT_CPT,
			$user->get_id(),
			$assignment_id
		);

		$item = $wpdb->get_row( $query, ARRAY_A );
		if ( $item ) {
			/*** TEST CACHE */
			// $this->_read_course_items( $result, $force );
		} else {
			$item = LP_User_Item::get_empty_item();
		}

		// Table fields
		$table_fields = array(
			'user_id'    => '%d',
			'item_id'    => '%d',
			'ref_id'     => '%d',
			'start_time' => '%s',
			'end_time'   => '%s',
			'item_type'  => '%s',
			'status'     => '%s',
			'ref_type'   => '%s',
			'parent_id'  => '%d',
		);

		// Data and format
		$data        = array();
		$data_format = array();

		// Update it later...
		$new_status = false;
		if ( array_key_exists( 'status', $item_data ) && $item_data['status'] != $item['status'] ) {
			$new_status = $item_data['status'];
			// unset( $item_data['status'] );
		}

		/*if ( ! empty( $item_data['end_time'] ) && empty( $item_data['end_time_gmt'] ) ) {
			$start_time = new LP_Datetime( $item_data['end_time'] );

			$item_data['end_time_gmt'] = $start_time->toSql( false );
		}*/

		// Build data and data format
		foreach ( $item_data as $field => $value ) {
			if ( ! empty( $table_fields[ $field ] ) ) {
				$data[ $field ]        = $value;
				$data_format[ $field ] = $table_fields[ $field ];
			}
		}

		$data['user_id'] = $user_id;
		$data['item_id'] = $assignment_id;

		$data['item_type'] = LP_ASSIGNMENT_CPT;

		foreach ( $data as $k => $v ) {
			$data_format[ $k ] = $table_fields[ $k ];
		}

		$data_format = array_values( $data_format );
		if ( ! $item || ! $user_itemid ) {
			$wpdb->insert(
				$wpdb->learnpress_user_items,
				$data,
				$data_format
			);
			$user_itemid = $wpdb->insert_id;
			$item        = learn_press_get_user_item( array( 'user_item_id' => $user_itemid ) );
		} else {
			$wpdb->update(
				$wpdb->learnpress_user_items,
				$data,
				array( 'user_item_id' => $user_itemid ),
				$data_format,
				array( '%d' )
			);
		}
		if ( $user_itemid ) {
			if ( is_object( $item ) ) {
				$item = (array) $item;
			}
		}

		return $user_itemid;
	}
}

if ( ! function_exists( 'learnpress_assignment_action' ) ) {
	function learnpress_assignment_action( $action, $assignment_id, $course_id, $ajax = false ) {
		?>
		<input type="hidden" name="assignment-id" value="<?php echo $assignment_id; ?>">
		<input type="hidden" name="course-id" value="<?php echo $course_id; ?>">

		<?php if ( $ajax ) : ?>
			<input type="hidden" name="lp-ajax" value="<?php echo $action; ?>-assignment">
		<?php else : ?>
			<input type="hidden" name="lp-<?php echo $action; ?>-assignment" value="<?php echo $assignment_id; ?>">
		<?php endif; ?>

		<input type="hidden" name="<?php echo $action; ?>-assignment-nonce" value="<?php echo wp_create_nonce( sprintf( 'learn-press/assignment/%s/%s-%s-%s', $action, get_current_user_id(), $course_id, $assignment_id ) ); ?>">
		<?php
	}
}

if ( ! function_exists( 'learn_press_assignment_get_result' ) ) {
	function learn_press_assignment_get_result( $item_id, $user_id, $course_id ) {
		$assignment = new LP_Assignment( $item_id );
		$user       = learn_press_get_user( $user_id );
		$status     = $user->get_item_status( $item_id, $course_id );
		$result     = array(
			'mark'         => $assignment->get_mark(),
			'user_mark'    => 0,
			'status'       => $status,
			'grade'        => '',
			'result'       => 0,
			'retake_count' => 0,
		);

		if ( ! $item_id ) {
			return $result;
		}

		$course_data = $user->get_course_data( $course_id );
		if ( $course_data ) {
			if ( $course_data->get_item( $item_id ) ) {
				$user_item_id           = $course_data->get_item( $item_id )->get_user_item_id();
				$result['user_mark']    = ( learn_press_get_user_item_meta( $user_item_id, '_lp_assignment_mark', true ) ) ? learn_press_get_user_item_meta( $user_item_id, '_lp_assignment_mark', true ) : 0;
				$result['retake_count'] = ( learn_press_get_user_item_meta( $user_item_id, '_lp_assignment_retaken', true ) ) ? learn_press_get_user_item_meta( $user_item_id, '_lp_assignment_retaken', true ) : 0;
				$percent                = $result['mark'] ? ( $result['user_mark'] / $result['mark'] ) * 100 : 0;
				$passing_condition      = $result['mark'] ? ( $assignment->get_data( 'passing_grade' ) / $result['mark'] ) * 100 : 0;
				$result['result']       = $percent;
				$result['grade']        = $status === 'evaluated' ? ( $percent >= $passing_condition ? 'passed' : 'failed' ) : '';

				learn_press_update_user_item_field( array( 'graduation' => $result['grade'] ), array( 'user_item_id' => $user_item_id ) );
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'learn_press_assignment_remove_old_files' ) ) {
	function learn_press_assignment_remove_old_files( $useritem_id, $metakey = '_lp_assignment_answer_upload' ) {
		$uploaded_files = learn_press_assignment_get_uploaded_files( $useritem_id, $metakey, true );
		if ( count( $uploaded_files ) ) {
			foreach ( $uploaded_files as $file ) {
				if ( is_file( ABSPATH . $file->file ) ) {
					unlink( ABSPATH . $file->file );
				}
			}
		}
	}
}

if ( ! function_exists( 'learn_press_get_retake_time' ) ) {
	function learn_press_get_retake_time( $assignment_data, $current_assignment ) {
		$user_itemid  = $assignment_data->get_user_item_id();
		$retake_count = $current_assignment->get_data( 'retake_count' );
		$redo_time    = learn_press_get_user_item_meta( $user_itemid, '_lp_assignment_retaken', true );
		$redo_time    = ( $redo_time ) ? $redo_time : 0;

		return ( $redo_time < $retake_count ) ? $retake_count - $redo_time : 0;
	}
}

if ( ! function_exists( 'learn_press_assignment_filesize_format' ) ) {
	function learn_press_assignment_filesize_format( $size ) {
		$sizes = array( 'B', 'KB', 'MB', 'GB' );
		$count = 0;
		if ( $size < 1024 ) {
			return $size . ' ' . $sizes[ $count ];
		} else {
			while ( $size > 1024 ) {
				$size = round( $size / 1024, 2 );
				$count ++;
			}

			return $size . ' ' . $sizes[ $count ];
		}
	}
}


// add_action( 'wp_ajax_delete_assignment_upload_file', 'delete_assignment_upload_file' );
// function delete_assignment_upload_file() {
// $assignment_db  = LP_Assigment_DB::getInstance();
// $file_order     = (string) filter_input( INPUT_POST, 'attOrder' );
// $file_path      = (string) filter_input( INPUT_POST, 'attName' );
// $useritem_id    = (int) filter_input( INPUT_POST, 'useritemId' );
// $uploaded_files = learn_press_assignment_get_uploaded_files( $useritem_id );
// unset( $uploaded_files[ $file_order ] );
// check_ajax_referer( "delete_assignment_upload_file_{$file_order}" );

// if ( is_file( ABSPATH . $file_path ) && @unlink( ABSPATH . $file_path ) ) {
// learn_press_update_user_item_meta( $useritem_id, '_lp_assignment_answer_upload', $uploaded_files );
// $assignment_db->update_extra_value( $useritem_id, $assignment_db::$answer_upload_key, json_encode( $uploaded_files ) );
// wp_send_json_success();
// } elseif ( is_file( ABSPATH . $file_path ) ) {
// wp_send_json_error(
// __(
// 'Error: remove file failed, maybe there is issue with the permission!',
// 'learnpress-assignments'
// )
// );
// } else {
// wp_send_json_error( __( 'Error: The file is not existed!', 'learnpress-assignments' ) );
// }
// die();
// }

add_filter( 'e-course-item-types', 'assignment_fe_item_types', 10, 1 );
function assignment_fe_item_types( $types ) {
	foreach ( $types as $key => $type ) {
		if ( $type['type'] === LP_ASSIGNMENT_CPT ) {
			$types[ $key ]['icon'] = 'dashicons dashicons-pressthis';
		}
	}

	return $types;
}

// Check if is page in Assignment item.
function learnpress_is_assignment_page() {
	$item = LP_Global::course_item();

	return $item instanceof LP_Assignment ? $item : false;
}
