<?php
class LP_Frontend_Editor_Settings extends LP_Abstract_Settings_Page {
	public function __construct() {
		$this->id   = 'frontend_editor';
		$this->text = esc_html__( 'Frontend Editor', 'learnpress-frontend-editor' );

		parent::__construct();
	}

	public function get_settings( $section = '', $tab = '' ) {
		$settings = array(
			array(
				'type'  => 'title',
				'title' => esc_html__( 'Frontend Editor', 'learnpress-frontend-editor' ),
			),
			array(
				'name'    => esc_html__( 'Page Slug', 'learnpress-frontend-editor' ),
				'desc'    => sprintf( 'e.g. %s/%s', home_url(), '<code>frontend-editor</code>' ),
				'id'      => 'frontend_editor_page_slug',
				'type'    => 'text',
				'default' => 'frontend-editor',
			),
			array(
				'type' => 'sectionend',
			),
		);

		return apply_filters( 'learnpress/frontend_editor/settings', $settings );
	}
}

return new LP_Frontend_Editor_Settings();
