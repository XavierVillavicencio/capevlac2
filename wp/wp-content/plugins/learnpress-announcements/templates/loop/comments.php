<?php
/**
 * Template for displaying comment for announcement in announcements tab of single course page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/addons/announcements/loop/comments.php.
 *
 * @author  ThimPress
 * @package LearnPress/Announcements/Templates
 * @version 3.0.1
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
if ( ! isset( $announcement_id ) ) {
	return;
}

global $post;

$post = get_post( $announcement_id );
setup_postdata( $post );
if ( comments_open() || get_comments_number() ) {
	comments_template();
}
wp_reset_postdata();
