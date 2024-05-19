<?php
/**
 * Template for displaying BuddyPress profile assignments page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/assignments/compatible/learnpress-assignments/profile/assignments.php.
 *
 * @author   ThimPress
 * @package  LearnPress/Assignments/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$profile = learn_press_get_profile();
$curd    = new LP_Assignment_CURD();
$query   = $curd->profile_query_assignments( $profile->get_user_data( 'id' ), array( 'status' => '' ) );
?>

<?php if ( $query->get_items() ) { ?>
	<table class="lp-list-table profile-list-assignments profile-list-table">
		<thead>
		<tr>
			<th class="column-assignment"><?php _e( 'Assignment', 'learnpress-assignments' ); ?></th>
			<th class="column-mark"><?php _e( 'Mark', 'learnpress-assignments' ); ?></th>
			<th class="column-status"><?php _e( 'Status', 'learnpress-assignments' ); ?></th>
			<th class="column-time-interval"><?php _e( 'Interval', 'learnpress-assignments' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $query->get_items() as $user_assignment ) { ?>
			<?php
			/**
			 * @var $user_assignment LP_User_Item_Assignment
			 */
			$assignment = learn_press_get_assignment( $user_assignment->get_id() );
			$courses    = learn_press_get_item_courses( array( $user_assignment->get_id() ) );

			$course      = learn_press_get_course( $courses[0]->ID );
			$course_data = $user->get_course_data( $course->get_id() );
			if ( ! $course_data ) {
				continue;
			}

			$user_item_id = $course_data->get_item( $assignment->get_id() )->get_user_item_id();
			$mark         = learn_press_get_user_item_meta( $user_item_id, '_lp_assignment_mark', true );
			$evaluated    = $user->has_item_status( array( 'evaluated' ), $assignment->get_id(), $course->get_id() );
			?>
			<tr>
				<td class="column-assignment">
					<a href="<?php echo $assignment->get_permalink(); ?>">
						<?php echo $assignment->get_title( 'display' ); ?>
					</a>
				</td>
				<td class="column-mark">
					<?php
					if ( $evaluated ) {
						echo $mark . '/' . $assignment->get_data( 'mark' );

						if ( ! $evaluated ) {
							$status = __( 'completed', 'learnpress-assignments' );
						} else {
							$status = $mark >= $assignment->get_data( 'passing_grade' ) ? __( 'passed', 'learnpress-assignments' ) : __( 'failed', 'learnpress-assignments' );
						}
						?>
						<span class="lp-label label-<?php echo esc_attr( $status ); ?>"><?php esc_html_e( $status ); ?></span>
						<?php
					} else {
						echo '-';
					}
					?>
				</td>
				<td class="column-status">
					<?php echo $evaluated ? __( 'Evaluated', 'learnpress-assignments' ) : __( 'Not evaluate', 'learnpress-assignments' ); ?>
				</td>
				<td class="column-time-interval">
					<?php echo( $user_assignment->get_time_interval( 'display' ) ); ?>
				</td>
			</tr>
			<?php continue; ?>
			<tr>
				<td colspan="4"></td>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
		<tr class="list-table-nav">
			<td colspan="2" class="nav-text"><?php echo $query->get_offset_text(); ?></td>
			<td colspan="2" class="nav-pages"><?php $query->get_nav_numbers( true ); ?></td>
		</tr>
		</tfoot>
	</table>

	<?php
} else {
	learn_press_display_message( __( 'No assignments!', 'learnpress-assignments' ) );
}

