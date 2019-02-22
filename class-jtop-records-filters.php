<?php
/**
 * Class create view on custom post type .
 *
 * @package jw-territory-organizer-pro
 */

if ( ! class_exists( 'Jtop_Records_Filters' ) ) {
	/**
	 * Class initiates the plugin.
	 */
	class Jtop_Records_Filters {

		/**
		 * Construcor.
		 */
		public function __construct() {
			add_filter( 'manage_edit-territory_records_columns', array( $this, 'jtop_edit_territories_columns' ) );
			add_action( 'manage_territory_records_posts_custom_column', array( $this, 'jtop_manage_territories_columns' ), 10, 2 );
			add_action( 'edit_form_after_title', array( $this, 'set_post_title' ) );
			add_action( 'admin_head', array( $this, 'jtop_custom_column_size' ) );

		}
		/**
		 * Edit custom post type admin column.
		 *
		 * @param string $columns Description.
		 */
		public function jtop_edit_territories_columns( $columns ) {

			$columns = array(
				'cb'                    => '&lt;input type="checkbox" />',
				'title'                 => __( 'Territory name' ),
				'jtop_duration'         => __( 'Duration' ),
				'jtop_covered_date'     => __( 'Last Covered Date' ),
				'publishers'            => __( 'Last Publisher' ),
				'jtop_territory_status' => __( 'Status' ),
			);

			return $columns;
		}

		/**
		 * Adding content to custom post type admin colunm.
		 *
		 * @param string $column Description.
		 * @param int    $post_id Get post id.
		 */
		public function jtop_manage_territories_columns( $column, $post_id ) {
			global $post;
			$prefix = '_jtop_';

			if ( empty( get_the_title() ) ) {
				$territory_name   = get_post_meta( get_the_ID(), $prefix . 'territory_name', false );
				$territory_name   = get_term( $territory_name['0'], 'territories' );
				$territory_number = get_post_meta( get_the_ID(), $prefix . 'territory_number', true );
				$post_title       = $territory_number . '. ' . $territory_name->name;
				// Update posts title.
				$my_post = array(
					'ID'         => get_the_ID(),
					'post_title' => $post_title,
				);
				// Update the post into the database.
				wp_update_post( $my_post );
			}

			/* Get the post meta. */
			$history_records = get_post_meta( get_the_ID(), $prefix . 'covered-history-records', true );
			foreach ( (array) $history_records as $key => $entry ) {

				echo $entry;
				print_r($entry);

				if ( isset( $entry[ $prefix . 'test_title_2' ] ) )

						$title = esc_html( $entry[ $prefix . 'test_title_2' ] );
			} //* end foreach;

			//$history_records    = get_field( 'covered_history_records' ); // get all the rows.
			//$last_record_number = count( $history_records );

			//$last_record_arr = $history_records[ $last_record_number - 1 ]; // get the last row.

			switch ( $column ) {

				/* If displaying the 'duration' column. */
				case 'jtop_duration':
					$date_checked_out     = $last_record_arr['date_checked_out']; // get the sub field value date_checked_out.
					$date_checked_back_in = $last_record_arr['date_checked_back_in']; // get the sub field value date_checked_back_in.

					$date_checked_out_format     = strtotime( $date_checked_out ); // cerating datetime object for date_checked_out.
					$date_checked_back_in_format = strtotime( $date_checked_back_in ); // cerating datetime object for date_checked_back_in.
					$now                         = strtotime( 'now' ); // cerating datetime object for time is now.

					/* If no duration is found, output a default message. */
					if ( empty( $date_checked_back_in ) ) {
						$time_duration = $now - $date_checked_out_format;
						echo esc_html( round( $time_duration / ( 60 * 60 * 24 ) ) . ' days' );
						break;

						/* If there is a duration, append 'days' to the text string. */
					} else {
						esc_html_e( 'None' );
						break;
					}

					/* If displaying the 'publisher' column. */
				case 'publishers':
					$rows_number = get_field( 'covered_history_records' );
					$rows_number = count( $rows_number );
					// check if the repeater field has rows of data.
					if ( have_rows( 'covered_history_records' ) ) :

							// loop through the rows of data.
						while ( have_rows( 'covered_history_records' ) ) :
							the_row();

							// display a sub field value.
							$publishers_name = get_sub_field_object( 'name_of_publisher' );

						endwhile;
					else :
						esc_html_e( 'No Publisher' );
					endif;
					echo esc_html( $publishers_name['value'] );
					break;

					/* If displaying the 'jtop_covered_date' column. */
				case 'jtop_covered_date':
					$dates = array();

					// check if the repeater field has rows of data.
					if ( have_rows( 'covered_history_records' ) ) :

							// loop through the rows of data.
						while ( have_rows( 'covered_history_records' ) ) :
							the_row();

							// display a sub field value.
							$date_checked_back_in = get_sub_field_object( 'date_checked_back_in' );

							if ( ! empty( $date_checked_back_in['value'] ) && 'Covered' === get_sub_field( 'covered' ) ) :

								$dates[] = $date_checked_back_in['value'];

							endif;

						endwhile;
						$dates_rows_number = count( $dates );
						if ( 0 !== $dates_rows_number ) :
							$dates_rows_number--;
						endif;
						if ( isset( $dates[ $dates_rows_number ] ) ) :
							echo esc_html( $dates[ $dates_rows_number ] );
						endif;
					else :
						esc_html_e_( 'No Date' );
					endif;

					break;

				case 'jtop_territory_status':
					if ( empty( $date_checked_back_in ) && 'During work' !== $last_record_arr['covered'] ) {
						echo '<p style="background-color: green; color:white; text-align:center;">' . esc_html( 'Free' ) . '</p>';
					} else {
						echo '<p style="background-color: red; color:white; text-align:center;">' . esc_html( 'Assigned' ) . '</p>';
					};

					break;

			}
		}

		/**
		 * Function change placeholder in add new territory-number set_post_title
		 *
		 * @param WP_Post $post Get post object.
		 */
		public function set_post_title( $post ) {
			global $current_user;

			if ( 'territory_records' !== get_post_type() || 'auto-draft' !== $post->post_status ) {
				return;
			}

			$title = 'It will be territory number and name (after publish)';
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("#title").val("<?php echo esc_html( $title ); ?>");
				$("#title").prop("readonly", true); // Don't allow author/editor to adjust the title.
			});
			</script>
			<?php
		}

		/**
		 * Function change custom_type admin colum size and add color depend of assigment.
		 */
		public function jtop_custom_column_size() {
			echo '<style type="text/css">';
			echo '.column-jtop_duration{ width:10% }';
			echo '.column-jtop_territory_status{ width:10% }';
			echo 'tbody tr td .column-jtop_territory_status{ background-color:green }';
			echo '</style>';
		}
	}
	new Jtop_Records_Filters();
}
