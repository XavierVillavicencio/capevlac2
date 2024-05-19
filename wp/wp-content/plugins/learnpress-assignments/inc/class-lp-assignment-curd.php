<?php
/**
 * Class LP_Assignment_CURD
 *
 * @author  ThimPress
 * @package LearnPress/Assignments/Classes/CURD
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Assignment_CURD' ) ) {

	/**
	 * Class LP_Assignment_CURD
	 */
	class LP_Assignment_CURD extends LP_Object_Data_CURD implements LP_Interface_CURD {

		/**
		 * Create assignment, with default meta.
		 *
		 * @param $args
		 *
		 * @return int|WP_Error
		 */
		public function create( &$args ) {
			$args = wp_parse_args(
				$args,
				array(
					'id'      => '',
					'status'  => 'publish',
					'title'   => __( 'New Assignment', 'learnpress-assignments' ),
					'content' => '',
					'author'  => learn_press_get_current_user_id(),
				)
			);

			$assignment_id = wp_insert_post(
				array(
					'ID'           => $args['id'],
					'post_type'    => LP_ASSIGNMENT_CPT,
					'post_status'  => $args['status'],
					'post_title'   => $args['title'],
					'post_content' => $args['content'],
					'post_author'  => $args['author'],
				)
			);

			if ( $assignment_id ) {
				// add default meta for new assignment
				$default_meta = LP_Assignment::get_default_meta();

				if ( is_array( $default_meta ) ) {
					foreach ( $default_meta as $key => $value ) {
						update_post_meta( $assignment_id, '_lp_' . $key, $value );
					}
				}
			}

			return $assignment_id;
		}

		/**
		 * @param object $assignment
		 */
		public function update( &$assignment ) {
			// TODO: Implement update() method.
		}

		/**
		 * Delete assignment.
		 *
		 * @since 3.0.0
		 *
		 * @param object $assignment_id
		 */
		public function delete( &$assignment_id ) {
			// course curd
			$curd = new LP_Course_CURD();

			// allow hook
			do_action( 'learn-press/before-delete-assignment', $assignment_id );

			// remove assignment from course items
			$curd->remove_item( $assignment_id );
		}

		/**
		 * Duplicate assignment.
		 *
		 * @since 3.0.0
		 *
		 * @param $assignment_id
		 * @param array         $args
		 *
		 * @return mixed|WP_Error
		 */
		public function duplicate( &$assignment_id, $args = array() ) {
			if ( ! $assignment_id ) {
				return new WP_Error( __( '<p>Op! ID not found</p>', 'learnpress-assignments' ) );
			}

			if ( get_post_type( $assignment_id ) != LP_ASSIGNMENT_CPT ) {
				return new WP_Error( __( '<p>Op! The assignment does not exist</p>', 'learnpress-assignments' ) );
			}

			// ensure that user can create assignment
			if ( ! current_user_can( 'edit_posts' ) ) {
				return new WP_Error( __( '<p>Sorry! You don\'t have permission to duplicate this assignment</p>', 'learnpress-assignments' ) );
			}

			// duplicate assignment
			$new_assignment_id = learn_press_duplicate_post( $assignment_id, $args );

			if ( ! $new_assignment_id || is_wp_error( $new_assignment_id ) ) {
				return new WP_Error( __( '<p>Sorry! Failed to duplicate assignment!</p>', 'learnpress-assignments' ) );
			}

			do_action( 'learn-press/item/after-duplicate', $assignment_id, $new_assignment_id, $args );
			return $new_assignment_id;
		}

		/**
		 * Load assignment data.
		 *
		 * @since 3.0.0
		 *
		 * @param object $assignment
		 *
		 * @return object
		 * @throws Exception
		 */
		public function load( &$assignment ) {
			// assignment id
			$id = $assignment->get_id();

			if ( ! $id || get_post_type( $id ) !== LP_ASSIGNMENT_CPT ) {
				throw new Exception( sprintf( __( 'Invalid assignment with ID "%d".', 'learnpress-assignments' ), $id ) );
			}
			$assignment->set_data_via_methods(
				array(
					'retake_count'   => get_post_meta( $assignment->get_id(), '_lp_retake_count', true ),
					'mark'           => get_post_meta( $assignment->get_id(), '_lp_mark', true ),
					'introduction'   => get_post_meta( $assignment->get_id(), '_lp_introduction', true ),
					'file_extension' => get_post_meta( $assignment->get_id(), '_lp_file_extension', true ),
					'files_amount'   => get_post_meta( $assignment->get_id(), '_lp_upload_files', true ),
					'passing_grade'  => get_post_meta( $assignment->get_id(), '_lp_passing_grade', true ),
				)
			);

			return $assignment;
		}

		/**
		 * @param $assignment
		 *
		 * @return array|null|object
		 */
		public function get_students( $assignment ) {

			global $wpdb;

			$assignment = LP_Assignment::get_assignment( $assignment );
			$query      = $wpdb->prepare(
				"
				SELECT DISTINCT student.* FROM {$wpdb->users} AS student
				INNER JOIN {$wpdb->prefix}learnpress_user_items AS user_item
				ON user_item.user_id = student.ID
				WHERE user_item.item_id = %d AND user_item.item_type = %s AND user_item.status IN (%s, %s)
			",
				$assignment->get_id(),
				LP_ASSIGNMENT_CPT,
				'completed',
				'evaluated'
			);

			$students = $wpdb->get_results( $query, ARRAY_A );

			return $students;
		}

		/**
		 * @param int    $user_id
		 * @param string $args
		 *
		 * @return LP_Query_List_Table
		 */
		public function profile_query_assignments( $user_id = 0, $args = '' ) {
			global $wpdb, $wp;

			$paged = 1;

			if ( ! empty( $wp->query_vars['view_id'] ) ) {
				$paged = absint( $wp->query_vars['view_id'] );
			}

			$paged = max( $paged, 1 );
			$args  = wp_parse_args(
				$args,
				array(
					'paged'  => $paged,
					'limit'  => 10,
					'status' => '',
				)
			);

			if ( ! $user_id ) {
				$user_id = get_current_user_id();
			}

			$cache_key = sprintf( 'assignments-%d-%s', $user_id, md5( build_query( $args ) ) );

			if ( false === ( $assignments = wp_cache_get( $cache_key, 'lp-user-assignments' ) ) ) {

				$user_curd = new LP_User_CURD();
				$orders    = $user_curd->get_orders( $user_id );
				$query     = array(
					'total' => 0,
					'pages' => 0,
					'items' => false,
				);

				$assignments = array(
					'total' => 0,
					'paged' => $args['paged'],
					'limit' => $args['limit'],
					'pages' => 0,
					'items' => array(),
				);

				try {
					if ( ! $orders ) {
						throw new Exception( '', 0 );
					}

					$course_ids   = array_keys( $orders );
					$query_args   = $course_ids;
					$query_args[] = $user_id;

					$select  = 'SELECT * ';
					$from    = "FROM {$wpdb->learnpress_user_items}";
					$join    = $wpdb->prepare(
						"INNER JOIN {$wpdb->posts} c ON c.ID = item_id AND c.post_type = %s",
						LP_ASSIGNMENT_CPT
					);
					$where   = $wpdb->prepare( 'WHERE 1 AND user_id = %d', $user_id );
					$orderby = 'ORDER BY item_id, user_item_id DESC';

					if ( ! empty( $args['status'] ) ) {
						switch ( $args['status'] ) {
							case 'evaluated':
							case 'passed':
							case 'failed':
								$where .= $wpdb->prepare( ' AND status IN( %s )', array( 'evaluated' ) );

								if ( $args['status'] !== 'completed' ) {
									if ( $args['status'] != 'evaluated' ) {
										if ( 'passed' === $args['status'] ) {
											$where .= $wpdb->prepare( '  AND graduation = %s', 'passed' );
										} else {
											$where .= $wpdb->prepare(
												'  AND ( graduation IS NULL OR graduation <> %s )',
												'passed'
											);
										}
									}
								}

								break;
							case 'completed':
								$where .= $wpdb->prepare( ' AND status IN( %s )', array( 'completed' ) );
						}
					}

					$limit  = $args['limit'];
					$offset = ( $args['paged'] - 1 ) * $limit;

					$query_parts = apply_filters(
						'learn-press/query/user-assignments',
						compact( 'select', 'from', 'join', 'where', 'orderby' ),
						$user_id,
						$args
					);

					list( $select, $from, $join, $where, $orderby ) = array_values( $query_parts );

					$sql = "
						{$select}
						{$from}
						{$join}
						{$where}
						{$orderby}
						LIMIT {$offset}, {$limit}
					";

					$items = $wpdb->get_results( $sql, ARRAY_A );

					if ( $items ) {
						$count      = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
						$course_ids = wp_list_pluck( $items, 'item_id' );
						// LP_Helper::cache_posts( $course_ids );

						$assignments['total'] = $count;
						$assignments['pages'] = ceil( $count / $args['limit'] );
						foreach ( $items as $item ) {
							$assignments['items'][] = new LP_User_Item_Assignment( $item );
						}
					}
				} catch ( Exception $ex ) {

				}
				wp_cache_set( $cache_key, $assignments, 'lp-user-assignments' );
			}

			$assignments['single'] = __( 'assignment', 'learnpress-assignments' );
			$assignments['plural'] = __( 'assignments', 'learnpress-assignments' );

			return new LP_Query_List_Table( $assignments );
		}

		/**
		 * @param $profile LP_Profile
		 * @param string             $current_filter
		 *
		 * @return mixed
		 */
		public function get_assignments_filters( $profile, $current_filter = '' ) {
			$url      = $profile->get_tab_link( 'assignments' );
			$defaults = array(
				'all'       => sprintf( '<a href="%s">%s</a>', esc_url( $url ), __( 'All', 'learnpress-assignments' ) ),
				'completed' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'completed', $url ) ), __( 'Submitted', 'learnpress-assignments' ) ),
				'evaluated' => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'evaluated', $url ) ), __( 'Evaluated', 'learnpress-assignments' ) ),
				'passed'    => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'passed', $url ) ), __( 'Passed', 'learnpress-assignments' ) ),
				'failed'    => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'filter-status', 'failed', $url ) ), __( 'Failed', 'learnpress-assignments' ) ),
			);

			if ( ! $current_filter ) {
				$keys           = array_keys( $defaults );
				$current_filter = reset( $keys );
			}

			foreach ( $defaults as $k => $v ) {
				if ( $k === $current_filter ) {
					$defaults[ $k ] = sprintf( '<span>%s</span>', strip_tags( $v ) );
				}
			}

			return apply_filters( 'learn-press/profile/assignments-filters', $defaults );
		}
	}

}
