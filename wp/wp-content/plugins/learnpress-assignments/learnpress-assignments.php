<?php
/**
 * Plugin Name: LearnPress - Assignments
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Assignments add-on for LearnPress.
 * Author: ThimPress
 * Version: 4.1.1
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms, assignment
 * Text Domain: learnpress-assignments
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.3.3
 *
 * @package learnpress-assigments
 */

defined( 'ABSPATH' ) || exit;

const LP_ADDON_ASSIGNMENT_FILE = __FILE__;
define( 'LP_ADDON_ASSIGNMENT_PATH', dirname( __FILE__ ) );
const LP_ADDON_ASSIGNMENT_INC_PATH = LP_ADDON_ASSIGNMENT_PATH . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR;

/**
 * Class LP_Addon_Assignment_Preload
 */
class LP_Addon_Assignment_Preload {
	/**
	 * @var array
	 */
	public static $addon_info = array();
	/**
	 * @var LP_Addon_Course_Review $addon
	 */
	public static $addon;

	/**
	 * Singleton.
	 *
	 * @return LP_Addon_Course_Review_Preload|mixed
	 */
	public static function instance() {
		static $instance;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * LP_Addon_Assignment_Preload constructor.
	 */
	protected function __construct() {
		$can_load = true;
		define( 'LP_ADDON_ASSIGNMENT_BASENAME', plugin_basename( __FILE__ ) );

		// Set version addon for LP check .
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			LP_ADDON_ASSIGNMENT_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_ASSIGNMENT_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_ASSIGNMENT_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

		// Check LP activated .
		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			$can_load = false;
		} else {
			// Check version LP
			if ( version_compare( LP_ADDON_ASSIGNMENT_REQUIRE_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
				$can_load = false;
			}
		}

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
			deactivate_plugins( LP_ADDON_ASSIGNMENT_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		add_filter( 'learn-press/email-actions', array( $this, 'email_notify_hook' ) );

		add_action( 'learn-press/ready', array( $this, 'load' ) );
	}

	/**
	 * Hook notify email assignment
	 *
	 * @param array $email_hooks
	 *
	 * @return array
	 */
	public function email_notify_hook( array $email_hooks ): array {
		$email_hooks['learn-press/assignment/instructor-evaluated'] = array(
			LP_Email_Assignment_Evaluated_Admin::class => LP_ADDON_ASSIGNMENT_PATH . 'inc/emails/evaluated/class-lp-email-evaluated-assignment-admin.php',
			LP_Email_Assignment_Evaluated_Instructor::class => LP_ADDON_ASSIGNMENT_PATH . 'inc/emails/evaluated/class-lp-email-evaluated-assignment-instructor.php',
			LP_Email_Assignment_Evaluated_User::class  => LP_ADDON_ASSIGNMENT_PATH . 'inc/emails/evaluated/class-lp-email-evaluated-assignment-user.php',
		);

		$email_hooks['learn-press/assignment/student-submitted'] = array(
			LP_Email_Assignment_Submitted_Admin::class => LP_ADDON_ASSIGNMENT_PATH . 'inc/emails/submitted/class-lp-email-submitted-assignment-admin.php',
			LP_Email_Assignment_Submitted_Instructor::class => LP_ADDON_ASSIGNMENT_PATH . 'inc/emails/submitted/class-lp-email-submitted-assignment-instructor.php',
			LP_Email_Assignment_Submitted_User::class  => LP_ADDON_ASSIGNMENT_PATH . 'inc/emails/submitted/class-lp-email-submitted-assignment-user.php',
		);

		return $email_hooks;
	}

	/**
	 * Load addon
	 */
	public function load() {
		self::$addon = LP_Addon::load( 'LP_Addon_Assignment', 'inc/load.php', __FILE__ );
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LearnPress version ' . LP_ADDON_ASSIGNMENT_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}
}

LP_Addon_Assignment_Preload::instance();
