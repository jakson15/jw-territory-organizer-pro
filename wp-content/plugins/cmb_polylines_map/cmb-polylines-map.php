<?php
/*
Plugin Name: CMB2 Field Type: Google Maps
Plugin URI: https://github.com/mustardBees/cmb_field_map
GitHub Plugin URI: https://github.com/mustardBees/cmb_field_map
Description: Google Maps field type for CMB2.
Version: 2.2.0
Author: Phil Wylie
Author URI: https://www.philwylie.co.uk/
License: GPLv2+
*/

/**
 * Class jtop_CMB2_Field_Google_Maps.
 */
class JTOP_CMB2_Field_Google_Maps {

	/**
	 * Current version number.
	 */
	const VERSION = '2.2.0';

	/**
	 * Initialize the plugin by hooking into CMB2.
	 */
	public function __construct() {
		add_filter( 'cmb2_render_jtop_map', array( $this, 'render_jtop_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_jtop_map', array( $this, 'sanitize_jtop_map' ), 10, 4 );
		//add_filter( 'jtop_google_api_key', array( $this, 'google_api_key_constant' ) );
		add_action('admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ));
	}

	/**
	 * Render field.
	 */
	public function render_jtop_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {

		// Get the Google API key from the field's parameters.
		$api_key = $field->args( 'api_key' );

		// Allow a custom hook to specify the key.
		$api_key = apply_filters( 'pw_google_api_key', $api_key );



		echo '<input type="text" class="large-text jtop-map-search" id="' . $field->args( 'id' ) . '" />';

		echo '<div id="map"></div>';

		$this->setup_admin_scripts( $api_key );

		echo '<pre>';
		print_r( $field_escaped_value );
		echo '</pre>';

		$field_type_object->_desc( true, true );

		echo $field_type_object->input( array(
			'type'       => 'hidden',
			'name'       => $field->args('_name') . '[latitude]',
			'value'      => isset( $field_escaped_value['latitude'] ) ? $field_escaped_value['latitude'] : '',
			'class'      => 'jtop-map-latitude',
			'desc'       => '',
		) );
		echo $field_type_object->input( array(
			'type'       => 'hidden',
			'name'       => $field->args('_name') . '[longitude]',
			'value'      => isset( $field_escaped_value['longitude'] ) ? $field_escaped_value['longitude'] : '',
			'class'      => 'jtop-map-longitude',
			'desc'       => '',
		) );
	}

	/**
	 * Optionally save the latitude/longitude values into two custom fields.
	 */
	public function sanitize_jtop_map( $override_value, $value, $object_id, $field_args ) {
		if ( isset( $field_args['split_values'] ) && $field_args['split_values'] ) {
			if ( ! empty( $value['latitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_latitude', $value['latitude'] );
			}

			if ( ! empty( $value['longitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_longitude', $value['longitude'] );
			}
		}

		return $value;
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function setup_admin_scripts($api_key) {
		//wp_register_script( 'jtop-google-maps-api', "https://maps.googleapis.com/maps/api/js?key={$api_key}&libraries=places", null, null );
		wp_enqueue_script( 'jtop-google-maps', plugins_url( 'js/script.js', __FILE__ ), 'jtop-google-maps-api', self::VERSION );
		wp_register_script( 'jtop-google-maps-api-drow', "https://maps.googleapis.com/maps/api/js?key={$api_key}&libraries=drawing&callback=jtopMap", null, null );
		wp_enqueue_style( 'jtop-google-maps', plugins_url( 'css/style.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Default filter to return a Google API key constant if defined.
	 */
	public function google_api_key_constant( $google_api_key = null ) {

		// Allow the field's 'api_key' parameter or a custom hook to take precedence.
		if ( ! empty( $google_api_key ) ) {
			return $google_api_key;
		}

		if ( defined( 'jtop_GOOGLE_API_KEY' ) ) {
			$google_api_key = jtop_GOOGLE_API_KEY;
		}

		return $google_api_key;
	}
}
new JTOP_CMB2_Field_Google_Maps();
