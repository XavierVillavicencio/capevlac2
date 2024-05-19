<?php
/**
 * Class LP_Assignment_Evaluate.
 *
 * @package LearnPress/Assignments/Classes
 * @version 4.0.0
 * @author Nhamdv - Code is poetry
 */

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'LP_Assignment_Evaluate' ) ) {

	/**
	 * Class LP_Assignment_Evaluate
	 */
	class LP_Assignment_Evaluate {

		/**
		 * @var array
		 */
		protected static $_instance = array();

		/**
		 * @var null
		 */
		protected $assignment = null;

		/**
		 * @var null
		 */
		protected $user_item_id = null;

		/**
		 * @var null
		 */
		protected $evaluated = null;

		/**
		 * LP_Assignment_Evaluate constructor.
		 *
		 * @param $assignment LP_Assignment
		 * @param $user_item_id
		 * @param $evaluated
		 */
		public function __construct( $assignment, $user_item_id, $evaluated ) {
			if ( ! LP_Assignment::get_assignment( $assignment ) ) {
				return;
			}
			$this->assignment   = $assignment;
			$this->user_item_id = $user_item_id;
			$this->evaluated    = $evaluated;
		}

		/**
		 * Display.
		 */
		public function display() {
			$this->get_settings_v4();
		}

		/**
		 * @return mixed
		 * @editor tungnx
		 * @modify 4.0.1 - comment - not use
		 */
		/*public function get_settings() {
			$prefix = '_lp_evaluate_assignment_';

			$mark   = learn_press_get_user_item_meta( $this->user_item_id, '_lp_assignment_mark', true );
			$note   = learn_press_get_user_item_meta( $this->user_item_id, '_lp_assignment_instructor_note', true );
			$upload = learn_press_get_user_item_meta( $this->user_item_id, '_lp_assignment_evaluate_upload', true );

			$settings = apply_filters(
				'learn-press/assignment-evaluate-options',
				array(
					array(
						'title'            => __( 'Mark', 'learnpress-assignments' ),
						'id'               => $prefix . 'mark',
						'std'              => $mark ? $mark : 0,
						'type'             => 'number',
						'desc'             => __( 'Mark for user answer.', 'learnpress-assignments' ),
						'min'              => 0,
						'max'              => $this->assignment ? $this->assignment->get_data( 'mark' ) : '',
						'assignment-field' => 'yes',
						'disabled'         => $this->evaluated ? true : false,
					),
					array(
						'title'            => __( 'Instructor note', 'learnpress-assignments' ),
						'id'               => $prefix . 'instructor_note',
						'std'              => $note ? $note : '',
						'type'             => 'textarea',
						'placeholder'      => __( 'Note here...', 'learnpress-assignments' ),
						'desc'             => __( 'Note for send student.', 'learnpress-assignments' ),
						'assignment-field' => 'yes',
						'disabled'         => $this->evaluated ? true : false,
					),
					array(
						'title'            => __( 'Document', 'learnpress-assignments' ),
						'std'              => $upload ? $upload : '',
						'id'               => $prefix . 'document',
						'type'             => 'file_advanced',
						'desc'             => __( 'Upload files for the right answers, reference, etc', 'learnpress-assignments' ),
						'assignment-field' => 'yes',
						'disabled'         => $this->evaluated ? true : false,
					),
				)
			);

			return $settings;
		}*/

		public function get_settings_v4() {
			$prefix        = '_lp_evaluate_assignment_';
			$assignment_db = LP_Assigment_DB::getInstance();

			$mark = learn_press_get_user_item_meta( $this->user_item_id, '_lp_assignment_mark', true );

			$instructor_note = $assignment_db->get_extra_value( $this->user_item_id, $assignment_db::$instructor_note_key );
			if ( empty( $instructor_note ) ) { // get value old from column meta_value
				$instructor_note = learn_press_get_user_item_meta( $this->user_item_id, $assignment_db::$instructor_note_key, true );
			}

			$upload = learn_press_get_user_item_meta( $this->user_item_id, '_lp_assignment_evaluate_upload', true );
			?>

			<div class="lp-meta-box">
				<div class="lp-meta-box__inner">
				<?php
				lp_meta_box_text_input_field(
					array(
						'id'                => $prefix . 'mark',
						'label'             => esc_html__( 'Mark', 'learnpress-assignments' ),
						'description'       => esc_html__( 'Mark for user answer.', 'learnpress-assignments' ),
						'type'              => 'number',
						'type_input'        => 'number',
						'default'           => $mark ? $mark : 0,
						'custom_attributes' => array(
							'min'  => '0',
							'step' => '0.1',
							'max'  => $this->assignment ? $this->assignment->get_data( 'mark' ) : '',
						),
					)
				);

				lp_meta_box_textarea_field(
					array(
						'id'          => $prefix . 'instructor_note',
						'label'       => esc_html__( 'Instructor note', 'learnpress-assignments' ),
						'placeholder' => __( 'Note here...', 'learnpress-assignments' ),
						'description' => esc_html__( 'Note for send student.', 'learnpress-assignments' ),
						'default'     => $instructor_note ? $instructor_note : '',
					)
				);

				lp_meta_box_file_input_field(
					array(
						'id'          => $prefix . 'document',
						'label'       => esc_html__( 'Document', 'learnpress-assignments' ),
						'description' => esc_html__( 'Upload files for the right answers, reference, etc.', 'learnpress-assignments' ),
						'multil'      => true,
						'default'     => $upload ? $upload : '',
					)
				);
				?>
				</div>
			</div>
			<?php

		}

		/**
		 * @param $assignment
		 * @param $user_item_id
		 * @param $evaluated
		 *
		 * @return array|LP_Assignment_Evaluate
		 */
		public static function instance( $assignment, $user_item_id, $evaluated ) {
			if ( ! self::$_instance ) {
				self::$_instance = new self( $assignment, $user_item_id, $evaluated );
			}

			return self::$_instance;
		}
	}
}
