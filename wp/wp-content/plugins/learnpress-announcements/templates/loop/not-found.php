<?php
/**
 * Template for displaying item not found in announcements tab of single course page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/announcements/loop/not-found.php.
 *
 * @author  ThimPress
 * @package LearnPress/Announcements/Templates
 * @version 3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>

<div class="lp-announcement-not-found">

	<?php echo apply_filters( 'learnpress-announcements-not-found', __( 'Announcements is empty', 'learnpress-announcements' ) ); ?>

</div>
