<?php
/**
 * Template for displaying assignments tab in user profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/assignments/profile/tabs/assignments.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Assignments/Templates
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit();

$profile = LP_Profile::instance();
$user    = $profile->get_user();

$filter_status = LP_Request::get_string( 'filter-status' );
$curd          = new LP_Assignment_CURD();
$query         = $curd->profile_query_assignments( $profile->get_user_data( 'id' ), array( 'status' => $filter_status ) );
$filters       = $curd->get_assignments_filters( $profile, $filter_status );
?>

<div class="learn-press-subtab-content">
	<h3 class="profile-heading"><?php esc_html_e( 'My Assignments', 'learnpress-assignments' ); ?></h3>

	<?php if ( $filters ) : ?>
		<ul class="lp-sub-menu learn-press-filters">
			<?php foreach ( $filters as $class => $link ) : ?>
				<li class="<?php echo esc_attr( $class ); ?>"><?php echo $link; ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ( $query->get_items() ) { ?>
		<table class="lp-list-table profile-list-assignments profile-list-table">
			<thead>
			<tr>
				<th class="column-course"><?php esc_html_e( 'Course', 'learnpress-assignments' ); ?></th>
				<th class="column-assignment"><?php esc_html_e( 'Assignment', 'learnpress-assignments' ); ?></th>
				<th class="column-padding-grade"><?php esc_html_e( 'Passing Grade', 'learnpress-assignments' ); ?></th>
				<th class="column-status"><?php esc_html_e( 'Status', 'learnpress-assignments' ); ?></th>
				<th class="column-mark"><?php esc_html_e( 'Mark', 'learnpress-assignments' ); ?></th>
				<th class="column-time-interval"><?php esc_html_e( 'Interval', 'learnpress-assignments' ); ?></th>
			</tr>
			</thead>

			<tbody>
			<?php foreach ( $query->get_items() as $user_assignment ) : ?>
				<?php
				$assignment = learn_press_get_assignment( $user_assignment->get_id() );
				$courses    = learn_press_get_item_courses( array( $user_assignment->get_id() ) );

				if ( ! $courses ) {
					continue;
				}

				$course      = learn_press_get_course( $courses[0]->ID );
				$course_data = $user->get_course_data( $course->get_id() );
				if ( ! $course_data ) {
					continue;
				}

				$user_item = $course_data->get_item( $assignment->get_id() );
				if ( ! $user_item ) {
					continue;
				}

				$user_item_id = $user_item->get_user_item_id();
				$mark         = learn_press_get_user_item_meta( $user_item_id, '_lp_assignment_mark', true );
				$evaluated    = $user->has_item_status( array( 'evaluated' ), $assignment->get_id(), $course->get_id() );
				?>

				<tr>
					<td class="column-course">
						<?php if ( $courses ) : ?>
							<a href="<?php echo esc_url( $course->get_permalink() ); ?>">
								<?php echo $course->get_title( 'display' ); ?>
							</a>
						<?php endif; ?>
					</td>

					<td class="column-assignment">
						<?php if ( $courses ) : ?>
							<a href="<?php echo esc_url( $course->get_item_link( $user_assignment->get_id() ) ); ?>">
								<?php echo $assignment->get_title( 'display' ); ?>
							</a>
						<?php endif; ?>
					</td>

					<td class="column-passing-grade">
						<?php echo $assignment->get_data( 'passing_grade' ); ?>
					</td>

					<td class="column-status">
						<?php echo $evaluated ? esc_html__( 'Evaluated', 'learnpress-assignments' ) : esc_html__( 'Not evaluate', 'learnpress-assignments' ); ?>
					</td>

					<td class="column-mark">
						<?php
						if ( $evaluated ) {
							echo $mark . '/' . $assignment->get_data( 'mark' );

							if ( ! $evaluated ) {
								$status = esc_html__( 'completed', 'learnpress-assignments' );
							} else {
								$status = $mark >= $assignment->get_data( 'passing_grade' ) ? esc_html__( 'passed', 'learnpress-assignments' ) : esc_html__( 'failed', 'learnpress-assignments' );
							}
							?>
							<span class="lp-label label-<?php echo esc_attr( $status ); ?>"><?php esc_html( $status ); ?></span>
							<?php
						} else {
							echo '-';
						}
						?>
					</td>
					<td class="column-time-interval">
						<?php echo esc_html( $user_assignment->get_time_interval( 'display' ) ); ?>
					</td>
				</tr>
				<?php continue; ?>
				<tr>
					<td colspan="4"></td>
				</tr>
			<?php endforeach; ?>
			</tbody>

			<tfoot>
			<tr class="list-table-nav">
				<td colspan="2" class="nav-text">
					<?php echo $query->get_offset_text(); ?>
				</td>
				<td colspan="4" class="nav-pages">
					<?php $query->get_nav_numbers( true ); ?>
				</td>
			</tr>
			</tfoot>
		</table>

	<?php } else { ?>
		<?php learn_press_display_message( __( 'No assignments!', 'learnpress-assignments' ) ); ?>
	<?php } ?>
</div>
