<?php
/**
 * Template for displaying assignment item content in single course.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/assignments/single-course/content-item-lp_assignment.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Assignments/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$quiz       = LP_Global::course_item_quiz();
$assignment = LP_Global::course_item();
?>

<div id="content-item-assignment" class="content-item-summary">

	<?php
	/**
	 * @see learn_press_content_item_summary_title()
	 * @see learn_press_content_item_summary_content()
	 */
	do_action( 'learn-press/before-content-item-summary/' . $assignment->get_item_type() );
	?>

	<?php
	/**
	 * @see learn_press_content_item_summary_question()
	 */
	do_action( 'learn-press/content-item-summary/' . $assignment->get_item_type() );
	?>

	<?php
	/**
	 * @see learn_press_content_item_summary_question_numbers()
	 */
	do_action( 'learn-press/after-content-item-summary/' . $assignment->get_item_type() );
	?>

</div>
