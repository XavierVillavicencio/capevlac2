<?php
/**
 * Template for displaying Assignment after sent.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/assignments/content-assignment/buttons/sent.php.
 *
 * @author  ThimPress
 * @package  Learnpress/Assignments/Templates
 * @version  3.0.1
 */

defined( 'ABSPATH' ) || exit();

$assigment_db = LP_Assigment_DB::getInstance();
$course       = learn_press_get_course();
if ( ! $course ) {
	return;
}

$current_assignment = LP_Global::course_item();
$user               = learn_press_get_current_user();
$course_data        = $user->get_course_data( $course->get_id() );
if ( ! $course_data ) {
	return;
}

$user_item = $course_data->get_item( $current_assignment->get_id() );
if ( ! $user_item ) {
	return;
}
$user_item_id = $user_item->get_user_item_id();

$last_answer = $assigment_db->get_extra_value( $user_item_id, $assigment_db::$answer_note_key );
if ( is_null( $last_answer ) ) {
	$last_answer = '';
}
$uploaded_files = learn_press_assignment_get_uploaded_files( $user_item_id );
?>

<div class="assignment-after-sent">
	<h3><?php esc_html_e( 'Your Answer:', 'learnpress-assignments' ); ?></h3>
	<blockquote><?php echo wpautop( $last_answer ); ?></blockquote>

	<?php if ( ! empty( $uploaded_files ) ) : ?>
		<h3><?php esc_html_e( 'Your Uploaded File(s):', 'learnpress-assignments' ); ?></h3>

		<div class="learn-press-assignment-uploaded">
			<ul class="assignment-files assignment-uploaded list-group list-group-flush">
				<?php foreach ( $uploaded_files as $file ) : ?>
					<li class="list-group-item"><a href="<?php echo esc_url( get_site_url() . $file->url ); ?>" target="_blank" rel="noopener"><?php echo $file->filename; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</div>
