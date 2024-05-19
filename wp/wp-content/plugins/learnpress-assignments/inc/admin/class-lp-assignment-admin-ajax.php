<?php
/**
 * Class LP_Assignment_Admin_Ajax
 *
 * @author  ThimPress
 * @package LearnPress/Assignments/Classes
 * @version 3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Assignment_Admin_Ajax' ) ) {
	/**
	 * Class LP_Assignment_Admin_Ajax
	 */
	class LP_Assignment_Admin_Ajax {

		/**
		 * Add action ajax.
		 */
		public static function init() {

			if ( ! is_user_logged_in() ) {
				return;
			}

			$actions = array(
				'send_evaluated_mail',
				'delete_submission',
				'reset_result',
				'update_evalute_assignments',
			);

			foreach ( $actions as $action ) {
				add_action( 'wp_ajax_lp_assignment_' . $action, array( __CLASS__, $action ) );
			}
		}

		/**
		 * Resend evaluated mail.
		 */
		public static function send_evaluated_mail() {
			$user_id       = LP_Request::get_string( 'user_id' );
			$assignment_id = LP_Request::get_string( 'assignment_id' );

			if ( ! $user_id || ! $assignment_id ) {
				return;
			}

			$email = new LP_Email_Assignment_Evaluated_User();

			$result = $email->handle( [ $user_id, $assignment_id ] );

			if ( $result ) {
				learn_press_send_json(
					array(
						'status'  => 'success',
						'message' => __( 'Send mail to student successful!', 'learnpress-assignments' ),
					)
				);
			} else {
				learn_press_send_json(
					array(
						'status'  => 'fail',
						'message' => __( 'Send mail to student fail!', 'learnpress-assignments' ),
					)
				);
			}

			wp_die();
		}

		/**
		 * Delete user's assignment and user can send it again.
		 */
		public static function delete_submission() {
			$user_id       = LP_Request::get_string( 'user_id' );
			$assignment_id = LP_Request::get_string( 'assignment_id' );

			if ( ! $user_id || ! $assignment_id ) {
				return;
			}

			$user_curd = new LP_User_CURD();
			$result    = $user_curd->delete_user_item(
				array(
					'item_id' => $assignment_id,
					'user_id' => $user_id,
				)
			);

			if ( $result ) {
				learn_press_send_json(
					array(
						'status'  => 'success',
						'message' => __( 'Delete user\'s assignment successful!', 'learnpress-assignments' ),
					)
				);
			} else {
				learn_press_send_json(
					array(
						'status'  => 'fail',
						'message' => __( 'Delete user\'s assignment fail!', 'learnpress-assignments' ),
					)
				);
			}
			wp_die();
		}

		/**
		 * Clear the result has evaluated.
		 */
		public static function reset_result() {
			$user_item_id = LP_Request::get_string( 'user_item_id' );

			if ( ! $user_item_id ) {
				return;
			}

			learn_press_delete_user_item_meta( $user_item_id, 'grade' );
			learn_press_delete_user_item_meta( $user_item_id, '_lp_assignment_mark' );
			learn_press_delete_user_item_meta( $user_item_id, '_lp_assignment_instructor_note' );
			learn_press_delete_user_item_meta( $user_item_id, '_lp_assignment_evaluate_upload' );
			learn_press_update_user_item_meta( $user_item_id, '_lp_assignment_evaluate_author', 0 );

			$user_curd = new LP_User_CURD();
			$result    = $user_curd->update_user_item_status( $user_item_id, 'completed' );

			if ( $result ) {
				learn_press_send_json(
					array(
						'status'  => 'success',
						'message' => __( 'Clear the result has evaluated successful!', 'learnpress-assignments' ),
					)
				);
			} else {
				learn_press_send_json(
					array(
						'status'  => 'fail',
						'message' => __( 'Clear the result has evaluated fail!', 'learnpress-assignments' ),
					)
				);
			}

			wp_die();
		}

		/**
		 * Update evalute assignments.
		 */
		public static function update_evalute_assignments() {
			$course_id = LP_Request::get_string( 'course_id' );

			try {
				$output = array(
					'status'  => 'fail',
					'message' => '',
				);

				if ( ! $course_id ) {
					throw new Exception( esc_html__( 'No Course avaliable!', 'learnpress-assignment' ) );
				}

				$course = learn_press_get_course( $course_id );

				if ( ! $course ) {
					throw new Exception( esc_html__( 'No Course avaliable!', 'learnpress-assignment' ) );
				}

				$items = $course->get_item_ids();

				if ( $items ) {
					foreach ( $items as $item ) {
						if ( learn_press_get_post_type( $item ) === 'lp_assignment' ) {
							$final_assignment = $item;
						}
					}
				}

				ob_start();
				?>

				<div class="lp-metabox-evaluate-assignment">
					<?php
					if ( isset( $final_assignment ) ) {
						update_post_meta( $course_id, '_lp_final_assignment', $final_assignment );
						$passing_grade = get_post_meta( $final_assignment, '_lp_passing_grade', true );
						$mark          = get_post_meta( $final_assignment, '_lp_mark', true );
						$url           = get_edit_post_link( $final_assignment );

						$output['status'] = 'success';
						?>

						<div class="lp-metabox-evaluate-assignment__message">
							<?php
							printf(
								esc_html__( 'Passing Grade: %s', 'learpress-assignments' ),
								( $passing_grade / $mark ) * 100 . '%'
							);
							?>
							-
							<?php
							printf(
								esc_html__( 'Assignment: %s', 'learnpress-assignments' ),
								'<a href="' . $url . '">' . get_the_title( $final_assignment ) . '</a>'
							);
							?>
						</div>

					<?php } else { ?>
						<div
							class="lp-metabox-evaluate-assignment__message lp-metabox-evaluate-assignment__message--error">
							<?php
							esc_html_e(
								'No Assignment item in Course!',
								'learnpress-assignments'
							);
							?>
								</div>
					<?php } ?>
				</div>

				<?php
				$output['message'] = ob_get_clean();
			} catch ( Exception $e ) {
				$output['message'] = '<div class="lp-metabox-evaluate-assignment__message lp-metabox-evaluate-assignment__message--error"">' . $e->getMessage() . '</div>';
			}

			wp_send_json( $output );
		}

	}
}

add_action( 'admin_init', array( 'LP_Assignment_Admin_Ajax', 'init' ) );
