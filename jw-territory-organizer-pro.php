<?php
/**
 * Plugin Name: JW Territory Organizer Pro
 * Description: This free simple plugin help you to organize territories in your congregation.
 * Version:     0.1
 *
 * @package     jw-territories-organizer-pro
 *
 * Author:      WordpressDesign
 * Author URI:  https://wordpressdesign.pl/
 * Text Domain: jw-territory-organizer-pro-pro
 * License:     GPL3
 *
 * JW Territory Organizer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 */

// Make sure we don't expose any info if called directly.
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a small plugin, not much I can do when called directly. Missed \"add_action\" function';
	exit;
}

define( 'JW_TERRITORY_ORGANIZER_PRO_VERSION', '0.1' );
define( 'JW_TERRITORY_ORGANIZER_PRO__MINIMUM_WP_VERSION', '4.0' );
define( 'JW_TERRITORY_ORGANIZER_PRO__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Composer.
require __DIR__ . '/vendor/autoload.php';

// CMB2.
require __DIR__ . '/wp-content/plugins/cmb2/init.php';

// CMB2 Ajax search.
require_once __DIR__ . '/wp-content/plugins/cmb2-field-ajax-search/cmb2-field-ajax-search.php';

// CMB2 Google maps.
require_once __DIR__ . '/wp-content/plugins/cmb_field_map/cmb-field-map.php';

// CMB2 Google maps Polylines.
require_once __DIR__ . '/wp-content/plugins/cmb2-roadway-segments/cmb2-roadway-segments.php';

// CMB2 Google maps Polylines.
require_once __DIR__ . '/wp-content/plugins/cmb_polylines_map/cmb-polylines-map.php';



require_once JW_TERRITORY_ORGANIZER_PRO__PLUGIN_DIR . 'class-jtop-init.php';
//require_once JW_TERRITORY_ORGANIZER_PRO__PLUGIN_DIR . 'class-jtop-export.php';
require_once JW_TERRITORY_ORGANIZER_PRO__PLUGIN_DIR . 'class-jtop-records-filters.php';
require_once JW_TERRITORY_ORGANIZER_PRO__PLUGIN_DIR . 'class-jtop-settings.php';

/**
 * Add Google Maps API key notification.
 */
function google_maps_api_key_notice() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Add Google Maps API key in JW Territory settings.', 'jw-territory-organizer-pro' ); ?></p>
	</div>
	<?php
}

/**
 * Add Google Maps API key to ACF.
 */
function jtop_google_maps_api_key_init() {
	$jtop_settings = get_option( 'jtop_settings' );
	if ( empty( $jtop_settings['jtop_google_maps_api'] ) ) {
		add_action( 'admin_notices', 'google_maps_api_key_notice' );
	} else {
		add_filter(
			'pw_google_api_key',
			function() {
				$jtop_settings = get_option( 'jtop_settings' );
				return $jtop_settings['jtop_google_maps_api'];
			}
		);
	}
}
add_action( 'plugins_loaded', 'jtop_google_maps_api_key_init' );
