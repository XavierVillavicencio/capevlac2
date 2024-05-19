<?php
/**
 * Template for displaying attachment of assignment.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/assignments/single-assignment/attachment.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Assignments/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

$current_assignment = LP_Global::course_item();
$list_attachments   = $current_assignment->get_attachments_assignment( $current_assignment );
?>

<?php if ( ! empty( $list_attachments ) ) : ?>
	<div class="learn_press_assignment_attachment">
		<h2><?php echo esc_html__( 'Attachment Files:', 'learnpress-assignments' ); ?></h2>

		<ul class="assignment-files assignment-documentations">
			<?php foreach ( $list_attachments as $att_id ) : ?>
				<li><?php echo wp_get_attachment_link( $att_id, $size = 'none' ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
