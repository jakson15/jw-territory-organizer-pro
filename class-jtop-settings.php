<?php
/**
 * Class create Territory Assigment records Settings page.
 *
 * @package jw-territory-organizer-pro
 */

/**
 * Create Export date admin page
 *
 * @package jw-territory-organizer-pro
 */

if ( ! class_exists( 'Jtop_Settings' ) ) {
	/**
	 * Class initiates the plugin.
	 */
	class Jtop_Settings {

		/**
		 * Construcor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'jtop_settings_page' ) );
			add_action( 'admin_init', array( $this, 'jtop_settings_init' ) );
		}
		/**
		 * Create Export date admin page
		 */
		public function jtop_settings_page() {
			add_submenu_page(
				'edit.php?post_type=territory_records',
				__( 'Settings' ),
				__( 'Settings' ),
				'manage_options',
				'jtop_settings_page',
				array( __CLASS__, 'jtop_settings_page_html' )
			);
		}

		/**
		 * Generate Territory-Assigment settings
		 */
		public function jtop_settings_init() {
			register_setting( 'jtopSettings', 'jtop_settings' );

			add_settings_section(
				'jtop_jtopSettings_section',
				__( 'General Settings', 'jw-territory-organizer-pro' ),
				array( $this, 'jtop_settings_section_callback' ),
				'jtopSettings'
			);

			add_settings_field(
				'jtop_google_maps_api',
				__( 'Google Maps API key', 'jw-territory-organizer-pro' ),
				array( $this, 'jtop_google_maps_api_render' ),
				'jtopSettings',
				'jtop_jtopSettings_section'
			);

		}

		/**
		 * Display Google Maps API settings
		 */
		public function jtop_settings_section_callback() {

			esc_html_e( 'Add your Google Maps API key to load maps in territories', 'jw-territory-organizer-pro' );

		}

		/**
		 * Display Google Maps API settings
		 */
		public function jtop_google_maps_api_render() {
			$options = get_option( 'jtop_settings' );
			?>
			<input type='text' name='jtop_settings[jtop_google_maps_api]' value='<?php echo esc_html( $options['jtop_google_maps_api'] ); ?>'>
			<?php
		}

		/**
		 * Display content of export page
		 */
		public static function jtop_settings_page_html() {
			?>
			<h1><?php esc_html_e( 'Settings JW Territory' ); ?></h1>
			<div class="container">
				<div class="container">
				<form action='options.php' method='post'>

					<?php
					settings_fields( 'jtopSettings' );
					do_settings_sections( 'jtopSettings' );
					submit_button();
					?>

				</form>
			</div>
			<?php
		}
	}
	new Jtop_Settings();
}
