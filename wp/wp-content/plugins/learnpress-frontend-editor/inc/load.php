<?php
class LP_Addon_Frontend_Editor extends LP_Addon {

	public $version = LP_ADDON_FRONTEND_EDITOR_VER;

	public $require_version = LP_ADDON_FRONTEND_EDITOR_REQUIRE_VER;

	public $plugin_file = LP_ADDON_FRONTEND_EDITOR_FILE;

	public function __construct() {
		parent::__construct();

		add_action( 'admin_bar_menu', array( $this, 'add_admin_menu' ), 80 );
		add_filter( 'learn-press/admin/settings-tabs-array', array( $this, 'admin_settings' ) );
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_action( 'template_include', array( $this, 'template_includes' ), 1000 );
		add_filter( 'learnpress_metabox_settings_sanitize_option_learn_press_frontend_editor_page_slug', array( $this, 'sanitize_setting' ), 10, 3 );
	}

	public function _includes() {
		include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/functions.php';
		include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/class-rest-api.php';
	}

	public function enqueue_scripts() {
		$info = include LP_ADDON_FRONTEND_EDITOR_PATH . '/build/frontend-editor.asset.php';
		wp_enqueue_style( 'learnpress-frontend-editor', LP_ADDON_FRONTEND_EDITOR_URL . '/build/frontend-editor.css', array(), $info['version'] );
		wp_enqueue_script( 'learnpress-frontend-editor', LP_ADDON_FRONTEND_EDITOR_URL . '/build/frontend-editor.js', $info['dependencies'], $info['version'], true );

		wp_localize_script(
			'learnpress-frontend-editor',
			'learnpress_frontend_editor',
			apply_filters(
				'learnpress_frontend_editor_localize_script',
				array(
					'page_slug'             => learnpress_frontend_editor_get_slug(),
					'site_url'              => home_url( '/' ),
					'admin_url'             => admin_url(),
					'logout_url'            => wp_logout_url( home_url() ),
					'elementor_cpt_support' => defined( 'ELEMENTOR_VERSION' ) ? get_option( 'elementor_cpt_support', array() ) : array(),
					'course_item_types'     => learn_press_course_get_support_item_types(),
					'is_admin'              => current_user_can( 'manage_options' ),
					'is_review_course'      => LP_Settings::get_option( 'required_review', 'yes' ) === 'yes',
					'nonce'                 => wp_create_nonce( 'wp_rest' ),
					'logo_url'              => '', // Use for custom logo url.
					'logo_small_url'        => '', // Use for custom logo small url.
					'add_ons'               => array(), // If add-on support frontend use this filter show.
				)
			)
		);

		wp_set_script_translations( 'learnpress-frontend-editor', 'learnpress-frontend-editor', LP_ADDON_FRONTEND_EDITOR_PATH . '/languages' );

		learnpress_frontend_editor_tinymce_inline_scripts();

		wp_enqueue_editor(); // Support for tinymce.
		wp_enqueue_media(); // Support for tinymce media.
		wp_enqueue_script( 'media-audiovideo' );
		wp_enqueue_style( 'media-views' );
		wp_enqueue_script( 'mce-view' );

		do_action( 'learnpress/addons/frontend_editor/enqueue_scripts' );
	}

	public function add_admin_menu( $wp_admin_bar ) {
		if ( ! $this->can_view_frontend_editor() ) {
			return;
		}

		$title = esc_html__( 'LearnPress Frontend Editor', 'learnpress-frontend-editor' );
		$href  = learnpress_frontend_editor_get_url();

		if ( is_singular( LP_COURSE_CPT ) && get_the_ID() ) {
			$title = esc_html__( 'Edit with LearnPress Frontend Editor', 'learnpress-frontend-editor' );
			$href  = learnpress_frontend_editor_get_url( 'course/' . get_the_ID() . '/' );
		}

		$wp_admin_bar->add_node(
			array(
				'id'    => 'lp-frontend-editor',
				'title' => '
					<img style="width: 20px; height: 20px; padding: 0; line-height: 1.84615384; vertical-align: middle; margin: -6px 0 0 0;" src="data:image/png;base64,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">
					<span class="ab-label">' . $title . '</span>',
				'href'  => $href,
			)
		);
	}

	public function template_includes( $template ) {
		global $wp_query;

		if ( learnpress_is_page_frontend_editor() ) {
			if ( $this->can_view_frontend_editor() ) {

				$this->setup_the_scripts();

				wp_head();
				?>

				<div id="learnpress-frontend-editor-root"></div>

				<?php
				wp_footer();
				return;
			} else {
				wp_redirect( home_url() );
				exit();
			}
		}

		return $template;
	}

	public function setup_the_scripts() {
		add_filter( 'show_admin_bar', '__return_false' );

		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_footer' );

		// Handle `wp_head`
		add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
		add_action( 'wp_head', 'wp_print_styles', 8 );
		add_action( 'wp_head', 'wp_print_head_scripts', 9 );
		add_action( 'wp_head', 'wp_site_icon' );

		// Handle `wp_footer`
		add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );

		// Handle `wp_enqueue_scripts`
		remove_all_actions( 'wp_enqueue_scripts' );

		// Also remove all scripts hooked into after_wp_tiny_mce.
		remove_all_actions( 'after_wp_tiny_mce' );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999999 );

		do_action( 'learnpress/frontend-editor/init' );
	}

	public function can_view_frontend_editor() {
		return is_user_logged_in() && current_user_can( 'edit_lp_courses' );
	}

	public function add_rewrite_rules() {
		$root_slug = learnpress_frontend_editor_get_slug();

		if ( ! $root_slug ) {
			return;
		}

		add_rewrite_rule(
			'^' . $root_slug . '/?$',
			'index.php?frontend-editor=$matches[1]&post-type=$matches[2]',
			'top'
		);
		add_rewrite_rule(
			'^' . $root_slug . '/(course)/?(.*)?',
			'index.php?frontend-editor=$matches[1]&post-id=0',
			'top'
		);
		add_rewrite_rule(
			'^' . $root_slug . '/(lesson)/?(.*)?',
			'index.php?frontend-editor=$matches[1]&post-id=0',
			'top'
		);
		add_rewrite_rule(
			'^' . $root_slug . '/(quiz)/?(.*)?',
			'index.php?frontend-editor=$matches[1]&post-id=0',
			'top'
		);
		add_rewrite_rule(
			'^' . $root_slug . '/(questions)/?(.*)?',
			'index.php?frontend-editor=$matches[1]&post-id=0',
			'top'
		);
		add_rewrite_rule(
			'^' . $root_slug . '/(assignment)/?(.*)?',
			'index.php?frontend-editor=$matches[1]&post-id=0',
			'top'
		);
		add_rewrite_rule(
			'^' . $root_slug . '/(settings)/?(.*)?',
			'index.php?frontend-editor=$matches[1]&post-id=0',
			'top'
		);
		add_rewrite_tag( '%frontend-editor%', '([^&]+)' );
		add_rewrite_tag( '%post-type%', '([^&]+)' );
		add_rewrite_tag( '%post-id%', '([^&]+)' );
		add_rewrite_tag( '%item-id%', '([^&]+)' );
		add_rewrite_tag( '%sort%', '([^&]+)' );
		add_rewrite_tag( '%sortby%', '([^&]+)' );

		flush_rewrite_rules();
	}

	public function admin_settings( $tabs ) {
		$tabs['frontend_editor'] = include_once LP_ADDON_FRONTEND_EDITOR_PATH . '/inc/class-settings.php';

		return $tabs;
	}

	public function sanitize_setting( $value, $option, $raw_value ) {
		$value = sanitize_title( $value );

		return $value;
	}
}
