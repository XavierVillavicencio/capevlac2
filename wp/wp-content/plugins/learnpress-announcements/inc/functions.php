<?php
/**
 * LearnPress Announcements Functions
 *
 * Define common functions for both front-end and back-end
 *
 * @author   ThimPress
 * @package  LearnPress/Announcements/Functions
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'learn_press_announcements_template' ) ) {
	/**
	 * Get template.
	 *
	 * @param $name
	 * @param null $args
	 */
	function learn_press_announcements_template( $name, $args = null ) {
		_deprecated_function( __FUNCTION__, '4.0.4', 'LP_Addon_Announcements_Preload::$addon->get_template' );
		learn_press_get_template( $name, $args, learn_press_template_path() . '/addons/announcements/', LP_ANNOUNCEMENTS_TEMPLATE );
	}
}

if ( ! function_exists( 'learn_press_announcements_admin_view' ) ) {

	/**
	 * Get admin view file.
	 *
	 * @param $view
	 * @param string $args
	 */
	function learn_press_announcements_admin_view( $view, $args = '' ) {
		learn_press_admin_view( $view, wp_parse_args( $args, array( 'plugin_file' => LP_ADDON_ANNOUNCEMENTS_FILE ) ) );
	}
}

if ( ! function_exists( 'lp_announcement_metabox_list' ) ) {
	function lp_announcement_metabox_list( $post_id = '' ) {
		$arr_attrs = array();
		$edit      = '#';
		$title     = '';
		$date      = '';

		if ( ! empty( $post_id ) ) {
			$arr_attrs[] = 'id="' . esc_attr( $post_id ) . '"';
			$arr_attrs[] = 'class="lp_announcement-item"';
			$arr_attrs[] = 'data-id="' . esc_attr( $post_id ) . '"';
			$edit        = esc_url( get_edit_post_link( $post_id ) );
			$title       = get_the_title( $post_id );

			$current_time = current_time( 'timestamp' );
			$post_time    = get_the_time( 'U', $post_id );

			if ( ( $current_time - $post_time ) < DAY_IN_SECONDS ) {
				$date = human_time_diff( $post_time, $current_time ) . esc_html__( ' ago', 'learnpress-announcement' );
			} else {
				$date = get_the_date( '', $post_id );
			}
		} else {
			$arr_attrs[] = 'class="lp_announcement-item lp-hidden"';
		}

		if ( $title == '' ) {
			$title = esc_html__( 'No Title', 'learnpress-announcements' );
		}
		?>

		<tr <?php echo implode( ' ', $arr_attrs ); ?>>

			<td class="section-item-icon">
				<a href="<?php echo $edit; ?>" target="_blank"><span class="learn-press-icon"></span></a>
			</td>

			<td class="section-item-input">
				<input type="text" class="lp-item-name" readonly="readonly" value="<?php echo esc_attr( $title ); ?>" title="<?php echo esc_attr( $title ); ?>">
			</td>

			<td class="section-item-date">
				<span class="lp-date"><?php echo $date; ?></span>
			</td>

			<td class="section-item-actions">
				<p class="lp-item-actions lp-button-actions">
					<a href="#" class="lp-item-action lp-send dashicons dashicons-email-alt" data-confirm="<?php esc_attr_e( 'Are you want to send mail for all user?', 'learnpress-announcements' ); ?>" title="<?php esc_attr_e( 'Send Mail', 'learnpress-announcements' ); ?>"></a>
					<a href="<?php echo $edit; ?>" class="lp-item-action lp-edit dashicons dashicons-edit" target="_blank" title="<?php _e( 'Edit Announcement', 'learnpress-announcements' ); ?>"></a>
					<a href="#" class="lp-item-action lp-remove dashicons dashicons-trash" data-confirm-remove="<?php esc_attr_e( 'Are you sure you want to remove this item?', 'learpress-announcements' ); ?>" title="<?php esc_attr_e( 'Remove Announcement', 'learnpress-announcements' ); ?>"></a>
					<span class="item-checkbox">
						<input type="checkbox">
					</span>
				</p>
			</td>
		</tr>
		<?php
	}
}
