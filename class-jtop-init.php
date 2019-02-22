<?php
/**
 * Initiate the plugin.
 *
 * @package jw-territory-organizer-pro
 */

if ( ! class_exists( 'Jtop_Init' ) ) {
	/**
	 * Class initiates the plugin.
	 */
	class Jtop_Init {

		/**
		 * Construcor.
		 */
		public function __construct() {
			register_activation_hook( __FILE__, 'jtop_activation' );
			register_deactivation_hook( __FILE__, 'jtop_deactivation' );
			$this->jtop_activation();
		}
		/**
		 * Post Type: Territory Assignment Records.
		 */
		public function jtop_territory_post_type() {

			$labels = array(
				'name'               => __( 'Territory Assignment Records', 'jw-territories-organizer-pro' ),
				'singular_name'      => __( 'Territory Assignment Record', 'jw-territories-organizer-pro' ),
				'menu_name'          => __( 'JW Territory', 'jw-territories-organizer-pro' ),
				'all_items'          => __( 'Territory Records', 'jw-territories-organizer-pro' ),
				'add_new'            => __( 'Add New Record', 'jw-territories-organizer-pro' ),
				'featured_image'     => __( 'Territory Map', 'jw-territories-organizer-pro' ),
				'set_featured_image' => __( 'Set territory map image', 'jw-territories-organizer-pro' ),
				'add_new_item'       => __( 'Add new Territory', 'jw-territories-organizer-pro' ),
			);

			$args = array(
				'label'                 => __( 'Territory Assignment Records', 'twentynineteen' ),
				'labels'                => $labels,
				'description'           => '',
				'public'                => true,
				'publicly_queryable'    => true,
				'show_ui'               => true,
				'delete_with_user'      => false,
				'show_in_rest'          => true,
				'rest_base'             => '',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
				'has_archive'           => true,
				'show_in_menu'          => true,
				'show_in_nav_menus'     => true,
				'exclude_from_search'   => false,
				'capability_type'       => 'post',
				'map_meta_cap'          => true,
				'hierarchical'          => false,
				'rewrite'               => array(
					'slug'       => 'territory_records',
					'with_front' => true,
				),
				'query_var'             => true,
				'menu_icon'             => 'dashicons-location-alt',
				'supports'              => array( 'comments', 'title', 'revisions' ),
			);

			register_post_type( 'territory_records', $args );
		}

		/**
		 * Taxonomy: Territories.
		 */
		public function jtop_territories_taxonomy() {

			$labels = array(
				'name'          => __( 'Territories', 'jw-territories-organizer-pro' ),
				'singular_name' => __( 'Territory', 'jw-territories-organizer-pro' ),
				'add_new_item'  => __( 'Add New Territory', 'jw-territories-organizer-pro' ),
			);

			$args = array(
				'label'                 => __( 'Territories', 'jw-territories-organizer-pro' ),
				'labels'                => $labels,
				'public'                => true,
				'publicly_queryable'    => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'show_in_nav_menus'     => true,
				'query_var'             => true,
				'rewrite'               => array(
					'slug'       => 'territories',
					'with_front' => true,
				),
				'show_admin_column'     => false,
				'show_in_rest'          => true,
				'rest_base'             => 'territories',
				'rest_controller_class' => 'WP_REST_Terms_Controller',
				'show_in_quick_edit'    => false,
			);
			register_taxonomy( 'territories', array( 'territory_records' ), $args );
		}

		/**
		 * Add User roles.
		 */
		public function jtop_users_role() {
			add_role(
				'service_overeseer',
				__(

					'Service Overseer'
				),
				array(

					'read'              => true, // Allows a user to read.
					'create_posts'      => true, // Allows user to create new posts.
					'edit_posts'        => true, // Allows user to edit their own posts.
					'edit_others_posts' => true, // Allows user to edit others posts too.
					'publish_posts'     => true, // Allows the user to publish posts.
					'manage_categories' => true, // Allows user to manage post categories.
				)
			);
		}

		/**
		 * Functionm add Territory informations metabox.
		 */
		public function jtop_add_territory_informations() {

			$prefix = '_jtop_';

			$cmb = new_cmb2_box(
				array(
					'id'           => $prefix . 'territory-informations',
					'title'        => __( 'Territory Informations', 'jw-territories-organizer-pro' ),
					'object_types' => array( 'territory_records' ),
					'context'      => 'normal',
					'priority'     => 'default',
				)
			);

			$cmb->add_field(
				array(
					'name'       => __( 'Territory number', 'jw-territories-organizer-pro' ),
					'id'         => $prefix . 'territory_number',
					'type'       => 'text_small',
					'attributes' => array(
						'required'  => 'required',
						'maxlength' => '3',
					),
				)
			);

			$cmb->add_field(
				array(
					'name'       => __( 'Territory name', 'jw-territories-organizer-pro' ),
					'id'         => $prefix . 'territory_name',
					'type'       => 'term_ajax_search',
					'attributes' => array(
						'required' => 'required',
					),
					'query_args' => array(
						'taxonomy'   => array( 'territories' ),
						'hide_empty' => false,
					),
				)
			);

			$cmb->add_field(
				array(
					'name' => __( 'Territory map borders', 'jw-territories-organizer-pro' ),
					'desc' => 'Draw boundaries of your terrintory',
					'id'   => $prefix . 'territory_map_borders',
					'type' => 'snapmap',
				)
			);

		}

		/**
		 * Remove some admin bars for user roles.
		 */
		public function jtop_add_covered_history_records() {

			$prefix = '_jtop_';

			$cmb = new_cmb2_box(
				array(
					'id'           => $prefix . 'covered-history-records',
					'title'        => __( 'Covered History Records', 'jw-territories-organizer-pro' ),
					'object_types' => array( 'territory_records' ),
					'context'      => 'normal',
					'priority'     => 'default',
				)
			);

			$group_field_id = $cmb->add_field(
				array(
					'id'          => $prefix . 'history_records',
					'type'        => 'group',
					'description' => __( 'Generates reusable form entries', 'cmb2' ),
					'repeatable'  => true,
					'options'     => array(
						'group_title'    => __( 'Record {#}', 'jw-territories-organizer-pro' ),
						'add_button'     => __( 'Add Record', 'jw-territories-organizer-pro' ),
						'remove_button'  => __( 'Remove record', 'jw-territories-organizer-pro' ),
						'sortable'       => true,
						'remove_confirm' => esc_html__( 'Are you sure you want to remove?', 'jw-territories-organizer-pro' ),
					),
				)
			);

			$cmb->add_group_field(
				$group_field_id,
				array(
					'name'       => __( 'Name of publisher', 'jw-territories-organizer-pro' ),
					'id'         => 'publisher_name',
					'type'       => 'text',
					'attributes' => array(
						'required' => 'required',
					),
				)
			);

			$cmb->add_group_field(
				$group_field_id,
				array(
					'name'       => __( 'Date checked out:', 'jw-territories-organizer-pro' ),
					'id'         => 'date_checked_out',
					'type'       => 'text_date',
					'attributes' => array(
						'required' => 'required',
					),
					// 'timezone_meta_key' => 'wiki_test_timezone',
					// 'date_format' => 'l jS \of F Y',
				)
			);

			$cmb->add_group_field(
				$group_field_id,
				array(
					'name' => __( 'Date checked back in:', 'jw-territories-organizer-pro' ),
					'id'   => 'date_checked_back_in',
					'type' => 'text_date',
					// 'timezone_meta_key' => 'wiki_test_timezone',
					// 'date_format' => 'l jS \of F Y',
				)
			);

			$cmb->add_group_field(
				$group_field_id,
				array(
					'name'             => __( 'Covered', 'jw-territories-organizer-pro' ),
					'id'               => 'covered',
					'type'             => 'select',
					'show_option_none' => false,
					'default'          => 'first',
					'options'          => array(
						'first'  => __( 'During work', 'jw-territories-organizer-pro' ),
						'second' => __( 'Covered', 'jw-territories-organizer-pro' ),
						'third'  => __( 'Not Covered', 'jw-territories-organizer-pro' ),
					),
					'attributes'       => array(
						'required' => 'required',
					),
				)
			);

		}

		/**
		 * Remove some wordpress metaboxes.
		 */
		public function jtop_remove_meta_boxes() {
				remove_meta_box( 'tagsdiv-territories', 'territory_records', 'side' );
				remove_meta_box( 'commentstatusdiv', 'territory_records', 'normal' );
		}

		/**
		 * Remove some admin bars for user roles.
		 */
		public function jtop_remove_menu_pages() {
			global $user_ID;

			if ( current_user_can( 'service_overeseer' ) ) {

				remove_menu_page( 'index.php' );
				remove_menu_page( 'edit.php' );
				remove_menu_page( 'edit-comments.php' );
				remove_menu_page( 'users.php' );
				remove_menu_page( 'tools.php' );
			}
		}

		/**
		 * Activation function.
		 */
		public function jtop_activation() {

			// Trigger our function that registers the custom post type.
			add_action( 'init', array( $this, 'jtop_territory_post_type' ) );

			// Add jtop_territories_taxonomy.
			add_action( 'init', array( $this, 'jtop_territories_taxonomy' ) );

			// Add user role.
			$this->jtop_users_role();

			// Remove pages by user role.
			add_action( 'admin_init', array( $this, 'jtop_remove_menu_pages' ) );

			// Add Territory informations metaboxs.
			add_action( 'cmb2_init', array( $this, 'jtop_add_territory_informations' ) );

			// Add Territory covered history metaboxes.
			add_action( 'cmb2_init', array( $this, 'jtop_add_covered_history_records' ) );

			add_action( 'admin_menu', array( $this, 'jtop_remove_meta_boxes' ) );

		}

		/**
		 * Deactivation function.
		 */
		public function jtop_deactivation() {
			// Unregister the post type, so the rules are no longer in memory.
			unregister_post_type( 'territory_records' );
			// Clear the permalinks to remove our post type's rules from the database.
			flush_rewrite_rules();
		}
	}
	new Jtop_Init();
}

