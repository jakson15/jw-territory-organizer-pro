<?php
abstract class jtopget_Meta_Box {

	public static function add() {
		$screens = [ 'post', 'territories' ];
		foreach ( $screens as $screen ) {
			add_meta_box(
				'jtop_get_date',          // Unique ID
				'Received date',         // Box title
				[ self::class, 'html' ],   // Content callback, must be of type callable
				$screen,                 // Post type
				'normal',                // Display form
				'default'                // Display priority
			);
		}
	}

	public static function save( $post_id ) {
		if ( array_key_exists( 'get_data', $_POST ) ) {
			update_post_meta(
				$post_id,
				'jtop_get_date',
				$_POST['get_data']
			);
		}
	}

	public static function html( $post ) {
		$get_data = get_post_meta( $post->ID, 'jtop_get_date', true );
		?>
		<input type="date" name="get_data" id="get_data" value="<?php echo $get_data; ?>" class="widefat">
		<?php
	}
}

add_action( 'add_meta_boxes', [ 'jtopget_Meta_Box', 'add' ] );
add_action( 'save_post', [ 'jtopget_Meta_Box', 'save' ] );


abstract class jtopcovered_Meta_Box {

	public static function add() {
		$screens = [ 'post', 'territories' ];
		foreach ( $screens as $screen ) {
			add_meta_box(
				'jtop_covered_date',      // Unique ID
				'Covered date',          // Box title
				[ self::class, 'html' ],   // Content callback, must be of type callable
				$screen,                 // Post type
				'normal',                // Display form
				'default'                // Display priority
			);
		}
	}

	public static function save( $post_id ) {
		if ( array_key_exists( 'covered_data', $_POST ) ) {
			update_post_meta(
				$post_id,
				'jtop_covered_date',
				$_POST['covered_data']
			);
		}
	}

	public static function html( $post ) {
		$covered_data = get_post_meta( $post->ID, 'jtop_covered_date', true );
		?>
		<input type="date" name="covered_data" value="<?php echo $covered_data; ?>" class="widefat">
		<?php
	}
}

add_action( 'add_meta_boxes', [ 'jtopcovered_Meta_Box', 'add' ] );
add_action( 'save_post', [ 'jtopcovered_Meta_Box', 'save' ] );
