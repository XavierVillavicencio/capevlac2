<?php
/**
 * Template for displaying Assignment after sent.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/assignment/content-assignment/buttons/ratake.php.
 *
 * @author  ThimPress
 * @package  Learnpress/Assignments/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

$course             = learn_press_get_course();
$user               = learn_press_get_current_user();
$current_assignment = LP_Global::course_item();
$assignment_data    = $user->get_item_data( $current_assignment->get_id(), $course->get_id() );
$can_retake_time    = learn_press_get_retake_time( $assignment_data, $current_assignment );
?>

<?php do_action( 'learn-press/before-assignment-retake-button' ); ?>

<form name="retake-assignment" class="retake-assignment" method="post" enctype="multipart/form-data">

	<?php do_action( 'learn-press/begin-assignment-retake-button' ); ?>

	<button type="submit" data-counter="<?php echo $can_retake_time; ?>" class="button"><?php esc_html_e( 'Retake', 'learnpress-assignments' ); ?></button>

	<?php do_action( 'learn-press/end-assignment-retake-button' ); ?>

	<?php learnpress_assignment_action( 'retake', $current_assignment->get_id(), $course->get_id(), true ); ?>
	<input type="hidden" name="noajax" value="yes">

</form>

<?php do_action( 'learn-press/after-assignment-retake-button' ); ?>
