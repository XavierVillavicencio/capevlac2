<?php
/**
 * Admin View: Assignment student page.
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<?php $assignment_id = LP_Request::get_int( 'assignment_id' ); ?>

<?php
if ( ! learn_press_assignment_verify_url( $assignment_id ) ) {
	?>
	<div id="error-page">
		<p><?php _e( 'Sorry, you are not allowed to access this page.', 'learnpress-assignments' ); ?></p>
	</div>
	<?php
	return;
}

if ( ! $assignment = learn_press_get_assignment( $assignment_id ) ) {
	_e( 'Invalid assignment', 'learnpress-assignments' );

	return;
}
?>

<?php $list_table = new LP_Student_Assignment_List_Table( $assignment_id ); ?>


<?php
$courses = learn_press_get_item_courses( $assignment_id );

if ( empty( $courses ) ) {
	echo '<div style="margin-top: 60px;
    padding: 14px 30px;
    border: 2px solid red;
    border-radius: 10px;
    font-size: 16px;
    display: inline-block;">';
	esc_html_e( 'Error: Please assign Assignment in Course.', 'learnpress-assignments' );
	echo '</div>';
	return;
}

$lp_course = learn_press_get_course( $courses[0]->ID );
?>

<div class="wrap" id="learn-press-assignment">
	<h2><?php esc_html_e( 'Assignment Students', 'learnpress-assignments' ); ?></h2>

	<h3>
		<?php esc_html_e( 'Assignment', 'learnpress-assignments' ); ?>
		<a href="<?php echo esc_url( $assignment->get_edit_link() ); ?>" target="_blank"><?php echo $assignment->get_title(); ?></a>

		<?php
		if ( $lp_course ) :
			echo esc_html__( 'of course', 'learnpress-assignments' );
			?>
			<a href="<?php echo esc_url( $lp_course->get_edit_link() ); ?>" target="_blank"><?php echo $lp_course->get_title(); ?></a>

		<?php endif; ?>
	</h3>

	<form method="post">
		<?php $list_table->display(); ?>
	</form>
</div>
