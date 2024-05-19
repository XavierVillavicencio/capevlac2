<?php
/**
 * Template for displaying announcements tab in single course page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/announcements/announcements.php.
 *
 * @author  ThimPress
 * @package LearnPress/Announcements/Templates
 * @version 3.0.1
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
if ( ! isset( $query ) ) {
	return;
}
?>

<div id="lp-announcements" class="lp-announcements">
	<?php
	if ( $query->have_posts() ) {
		foreach ( $query->posts as $announcement ) {
			LP_Addon_Announcements_Preload::$addon->get_template( 'loop/item.php', array( 'announcement' => $announcement ) );
		}
	} else {
		LP_Addon_Announcements_Preload::$addon->get_template( 'loop/not-found.php' );
	}
	wp_reset_postdata();
	?>
</div>
