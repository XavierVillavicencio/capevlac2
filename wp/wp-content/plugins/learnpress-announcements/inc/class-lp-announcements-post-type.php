<?php
/**
 * Learnpress Announcement Custom post type class.
 *
 * @author   ThimPress
 * @package  LearnPress/Announcements/Classes
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'LP_Announcement_Post_Type' ) ) {
	/**
	 * Class LP_Announcement_Post_Type.
	 */
	final class LP_Announcement_Post_Type extends LP_Abstract_Post_Type {
		protected $_post_type = LP_ANNOUNCEMENTS_CPT;
		/**
		 * @var null
		 */
		protected static $_instance = null;

		/**
		 * LP_Announcement_Post_Type constructor.
		 *
		 * @param $post_type
		 */
		public function __construct( $post_type ) {
			parent::__construct( $post_type );

			add_action( 'comment_form', array( $this, 'announcement_comment_form' ) );
		}

		/**
		 * Admin scripts.
		 *
		 * @return $this|void
		 */
		public function admin_scripts() {
			$screen = get_current_screen();
			if ( ! empty( $screen ) && $screen->id === 'edit-' . LP_ANNOUNCEMENTS_CPT ) {
				wp_enqueue_style( 'lp-announcements-editor-css', LP_ADDON_ANNOUNCEMENTS_URI . 'assets/css/admin.announcements.css', array(), LP_ADDON_ANNOUNCEMENTS_VER );
				wp_enqueue_script( 'lp-announcements-editor-js', LP_ADDON_ANNOUNCEMENTS_URI . 'assets/js/admin.announcements.js', array( 'jquery' ), LP_ADDON_ANNOUNCEMENTS_VER, true );
			}
		}

		/**
		 * Register announcement post type.
		 *
		 * @return array|bool
		 */
		public function register() {
			$labels = array(
				'name'                  => __( 'Announcements', 'learnpress-announcements' ),
				'add_new_item'          => __( 'Add Item', 'learnpress-announcements' ),
				'edit_item'             => __( 'Edit Item', 'learnpress-announcements' ),
				'new_item'              => __( 'New Item', 'learnpress-announcements' ),
				'view_item'             => __( 'View Item', 'learnpress-announcements' ),
				'search_items'          => __( 'Search Items', 'learnpress-announcements' ),
				'not_found'             => __( 'No items found', 'learnpress-announcements' ),
				'not_found_in_trash'    => __( 'No items found in Trash', 'learnpress-announcements' ),
				'all_items'             => __( 'Announcements', 'learnpress-announcements' ),
				'archives'              => __( 'Item Archives', 'learnpress-announcements' ),
				'insert_into_item'      => __( 'Insert item', 'learnpress-announcements' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'learnpress-announcements' ),
			);

			$args = array(
				'label'               => __( 'Announcements', 'learnpress-announcements' ),
				'description'         => __( 'Announcement', 'learnpress-announcements' ),
				'labels'              => $labels,
				'public'              => false,
				'exclude_from_search' => false,
				'show_ui'             => true,
				'show_in_menu'        => 'learn_press',
				'hierarchical'        => false,
				'capability_type'     => 'post',
				'capabilities'        => array(
					'create_posts' => false, // disable create new post button
				),
				'map_meta_cap'        => true,
				'supports'            => array( 'title', 'editor', 'comments' ),
			);

			return $args;
		}

		/**
		 * Add columns to admin manage announcement page.
		 *
		 * @param  array $columns
		 *
		 * @return array
		 */
		public function columns_head( $columns ) {

			$keys   = array_keys( $columns );
			$values = array_values( $columns );
			$pos    = array_search( 'title', $keys );

			if ( $pos !== false ) {
				array_splice( $keys, $pos + 1, 0, array( 'course', 'author' ) );
				array_splice(
					$values,
					$pos + 1,
					0,
					array(
						__( 'Course', 'learnpress-announcements' ),
						__( 'Author', 'learnpress-announcements' ),
					)
				);
				$columns = array_combine( $keys, $values );
			} else {
				$columns['course'] = __( 'Course', 'learnpress-announcements' );
				$columns['author'] = __( 'Author', 'learnpress-announcements' );
			}

			return $columns;

		}

		/**
		 * Displaying the content of extra columns.
		 *
		 * @param $column
		 * @param $post_id
		 */
		public function columns_content( $column, $post_id = 0 ) {

			switch ( $column ) {
				case 'course':
					$courses_id = get_post_meta( $post_id, '_lp_course_announcement' );
					$assigned   = false;

					foreach ( $courses_id as $course_id ) {
						$course = get_post( $course_id );
						if ( ! empty( $course ) ) {
							echo '<div class="lp-announcement-column-course"><a href="' . esc_url( add_query_arg( array( 'filter_course' => $course->ID ) ) ) . '">' . get_the_title( $course->ID ) . '</a>';
							echo '<div class="row-actions">';
							printf( '<a href="%s">%s</a>', admin_url( sprintf( 'post.php?post=%d&action=edit', $course->ID ) ), __( 'Edit', 'learnpress-announcements' ) );
							echo '&nbsp;|&nbsp;';
							printf( '<a href="%s">%s</a>', get_the_permalink( $course->ID ), __( 'View', 'learnpress-announcements' ) );
							echo '&nbsp;|&nbsp;';

							$course_id = learn_press_get_request( 'filter_course' );
							if ( $course_id ) {
								printf( '<a href="%s">%s</a>', remove_query_arg( 'filter_course' ), __( 'Remove Filter', 'learnpress-announcements' ) );
							} else {
								printf( '<a href="%s">%s</a>', add_query_arg( 'filter_course', $course->ID ), __( 'Filter', 'learnpress-announcements' ) );
							}
							echo '</div></div>';

							$assigned = true;
						}
					}

					if ( ! $assigned ) {
						_e( 'Not assigned yet', 'learnpress-announcements' );
					}

					break;
			}
		}

		/**
		 * Sortable columns.
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function sortable_columns( $columns ) {
			$columns['author'] = 'author';

			return $columns;
		}

		/**
		 * @return string
		 */
		private function _get_orderby() {
			return isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '';
		}

		/**
		 * @return bool
		 */
		private function _get_search() {
			return isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : false;
		}

		/**
		 * @return bool
		 */
		private function _is_archive() {
			global $pagenow, $post_type;
			if ( ! is_admin() || ( $pagenow != 'edit.php' ) || ( LP_ANNOUNCEMENTS_CPT != $post_type ) ) {
				return false;
			}

			return true;
		}

		/**
		 * @return bool|int
		 */
		private function _filter_course() {
			return ! empty( $_REQUEST['filter_course'] ) ? absint( $_REQUEST['filter_course'] ) : false;
		}

		/**
		 * @param string $fields
		 *
		 * @return mixed|string
		 */
		public function posts_fields( $fields ) {
			if ( ! $this->_is_archive() ) {
				return $fields;
			}

			$fields = ' DISTINCT ' . $fields;

			return $fields;
		}

		/**
		 * @param $join
		 *
		 * @return mixed|string
		 */
		public function posts_join_paged( $join ) {
			if ( ! $this->_is_archive() ) {
				return $join;
			}
			global $wpdb;
			if ( $this->_filter_course() || ( $this->_get_orderby() == 'course-name' ) || ( $this->_get_search() ) ) {
				$join .= " LEFT JOIN {$wpdb->prefix}postmeta mt ON (mt.post_id = {$wpdb->posts}.ID)";
			}

			return $join;
		}

		/**
		 * @param $where
		 *
		 * @return mixed|null|string|string[]
		 */
		public function posts_where_paged( $where ) {

			if ( ! $this->_is_archive() ) {
				return $where;
			}
			global $wpdb;
			if ( $course_id = $this->_filter_course() ) {
				$where .= $wpdb->prepare( " AND mt.meta_key = '_lp_course_announcement' AND mt.meta_value = %d", $course_id );
			}
			if ( isset( $_GET['s'] ) ) {
				$s     = $_GET['s'];
				$where = preg_replace(
					"/\.post_content\s+LIKE\s*(\'[^\']+\')\s*\)/",
					" .post_content LIKE '%$s%' ) OR ({$wpdb->posts}.post_title LIKE '%$s%' )",
					$where
				);
			}

			return $where;
		}

		/**
		 * Comment form.
		 */
		public function announcement_comment_form( $post_id ) {
			global $post;
			$current_post = get_post();

			if ( LP_ANNOUNCEMENTS_CPT === $post->post_type ) {
				wp_reset_postdata();
				$parent_post = get_post();

				if ( $parent_post->post_type === LP_COURSE_CPT ) { ?>
					<input type="hidden" name="lp_comment_announcement_from_course_url"
						   value="<?php the_permalink(); ?>"/>
					<input type="hidden" name="lp_comment_announcement_from_course" value="yes"/>
					<input type="hidden" name="lp_comment_from_announcement"
						   id="lp_comment_from_announcement_<?php echo $current_post->ID; ?>"
						   value="<?php echo $current_post->ID; ?>"/>
					<?php
				}

				$post = $current_post;
				setup_postdata( $post );
			}
		}

		/**
		 * Instance.
		 *
		 * @return LP_Announcement_Post_Type|null
		 */
		public static function instance() {
			if ( ! self::$_instance ) {
				self::$_instance = new self( LP_ANNOUNCEMENTS_CPT );
			}

			return self::$_instance;
		}
	}
}

$announcement_post_type = LP_Announcement_Post_Type::instance();
