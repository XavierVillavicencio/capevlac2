<?php
/**
 * Template for displaying loop item in announcements tab of single course page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/announcements/loop/item.php.
 *
 * @author  ThimPress
 * @package LearnPress/Announcements/Templates
 * @version 3.0.1
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
if ( ! isset( $announcement ) ) {
	return;
}
$announcement_id = $announcement->ID;

$title   = ! empty( $announcement->post_title ) ? $announcement->post_title : __( 'No Title', 'learnpress-announcements' );
$content = ! empty( $announcement->post_content ) ? $announcement->post_content : __( 'No Content', 'learnpress-announcements' );
?>

<div id="lp-announcement-<?php echo esc_attr( $announcement_id ); ?>" class="lp-announcement-item">

	<h2 id="announcement_item_<?php echo $announcement_id; ?>" class="announcement_item title">
		<?php echo wp_kses_post( $announcement->post_title ); ?>
	</h2>

	<div class="lp-announcement-content">
		<div class="lp-announcement-wrap-content">
			<?php
			$user = get_current_user_id();
			if ( current_user_can( 'administrator' ) || $user === (int) $announcement->post_author ) {
				echo apply_filters( 'lp_announcements_edit_post', ' <p class="lp-edit-post"><a href="' . get_edit_post_link( $announcement_id ) . '">' . __( 'Edit', 'learnpress-announcement' ) . '</a></p>' );
			}

			echo wp_kses_post( $content );
			?>
		</div>

		<?php
		$comment_open = comments_open( $announcement_id );
		if ( $comment_open ) {
			LP_Addon_Announcements_Preload::$addon->get_template( 'loop/comments.php', array( 'announcement_id' => $announcement_id ) );
			//learn_press_announcements_template( 'loop/comments.php', array( 'announcement_id' => $announcement_id ) );
		} else {
			echo apply_filters( 'lp_announcement_hide_comments', __( '<p> Comments is closed </p>', 'learnpress-announcements' ) );
		}
		?>
	</div>

</div>
