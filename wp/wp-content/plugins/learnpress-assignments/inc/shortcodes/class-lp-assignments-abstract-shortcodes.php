<?php
/**
 * Learnpress Assignments abstract shortcode.
 *
 * @version       3.1.0
 * @author        ThimPress
 * @package       Learnpress/Assignments/Shortcode
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LPASSIGNMENT_Shortcodes
 */
abstract class LPASSIGNMENT_Shortcodes {

	// shortcode name
	protected $shortcode = null;

	function __construct() {
		add_shortcode( $this->shortcode, array( $this, 'add_shortcode' ) );
	}

	function add_shortcode( $atts, $content = null ) {

	}

}
