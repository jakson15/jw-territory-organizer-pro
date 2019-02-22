<?php
/**
 * Class create Territory Assigment records on export page.
 *
 * @package jw-territory-organizer-pro
 */

/**
 * Create Export date admin page
 *
 * @package jw-territory-organizer-pro
 */

	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;

if ( ! class_exists( 'Jtop_Export' ) ) {
	/**
	 * Class initiates the plugin.
	 */
	class Jtop_Export {

		/**
		 * Construcor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'jtop_export_page' ) );
			add_action( 'admin_init', array( $this, 'export_excel' ) );
		}
		/**
		 * Create Export date admin page
		 */
		public function jtop_export_page() {
			add_submenu_page(
				'edit.php?post_type=territory_records',
				__( 'Export data' ),
				__( 'Export data' ),
				'import',
				'jtop_export_data',
				array( __CLASS__, 'jtop_export_page_html' )
			);
		}

		/**
		 * Generate Territory-Assigment-records.xls
		 */
		public function export_excel() {
			// phpcs:ignore
			if ( isset( $_POST['export_excel'] ) ) {

					ob_clean();
					// Create new Spreadsheet object.
					$spreadsheet = new Spreadsheet();

					// Set document properties.
					$spreadsheet->getProperties()->setCreator( 'Maarten Balliauw' )
						->setLastModifiedBy( 'Maarten Balliauw' )
						->setTitle( 'Territory Assigment Record' )
						->setSubject( 'Territory Assigment Record' )
						->setDescription( 'Test document for Office 2007 XLSX, generated using PHP classes.' );

						// Add data to Territory Assigment Record
						// WP_Query arguments.
						$args = array(
							'post_type'      => array( 'territory_records' ),
							'post_status'    => array( 'publish' ),
							'order'          => 'ASC',
							'orderby'        => 'id',
							// phpcs:ignore
							'posts_per_page' => '300',
						);

						// The Query.
						$records = new WP_Query( $args );

						$column_letter_width       = 'A';
						$column_letter_merge       = 'A';
						$column_letter_merge_in    = 'B';
						$column_letter             = 'A';
						$column_letter_in          = 'A';
						$column_letter_in_date_out = 'A';
						$column_letter_in_back_in  = 'B';
						$row_number_merge          = 1;
						$territory_title;
						$name_of_publishers;

						$spreadsheet->setActiveSheetIndex( 0 );

						// The Loop.
				if ( $records->have_posts() ) {
					while ( $records->have_posts() ) {
						$records->the_post();

						$row_number_in      = 2;
						$row_number_in_date = 3;

						$territory_number = get_field( 'territory_number' );

						$spreadsheet->setActiveSheetIndex( 0 )->getColumnDimension( $column_letter_width )->setWidth( 10 );

						$spreadsheet->setActiveSheetIndex( 0 )->mergeCells( $column_letter_merge . $row_number_merge . ':' . ( ++$column_letter_merge ) . $row_number_merge );
						$spreadsheet->setActiveSheetIndex( 0 )->setCellValue( $column_letter . '1', 'Terr. No.' . $territory_number );

						// check if the repeater field has rows of data.
						if ( have_rows( 'covered_history_records' ) ) :

							// loop through the rows of data.
							while ( have_rows( 'covered_history_records' ) ) :
								the_row();

								$name_of_publisher    = get_sub_field( 'name_of_publisher' );
								$date_checked_out     = get_sub_field( 'date_checked_out' );
								$date_checked_back_in = get_sub_field( 'date_checked_back_in' );

								$spreadsheet->setActiveSheetIndex( 0 )->mergeCells( $column_letter_in . $row_number_in . ':' . $column_letter_merge_in . $row_number_in );
								$spreadsheet->setActiveSheetIndex( 0 )->setCellValue( $column_letter_in . $row_number_in, $name_of_publisher );
								$spreadsheet->setActiveSheetIndex( 0 )->setCellValue( $column_letter_in_date_out . $row_number_in_date, $date_checked_out );
								$spreadsheet->setActiveSheetIndex( 0 )->setCellValue( $column_letter_in_back_in . $row_number_in_date, $date_checked_back_in );

								$row_number_in_date = $row_number_in_date + 2;
								$row_number_in      = $row_number_in + 2;
							endwhile;

						endif;
						$column_letter_merge_in++;
						$column_letter_merge_in++;
						$column_letter_in++;
						$column_letter_in++;
						$column_letter++;
						$column_letter++;
						$column_letter_merge++;
						$column_letter_in_date_out++;
						$column_letter_in_date_out++;
						$column_letter_in_back_in++;
						$column_letter_in_back_in++;
						$column_letter_width++;

					}
				}
						// Restore original Post Data.
						wp_reset_postdata();

					// Miscellaneous glyphs, UTF-8.
					$spreadsheet->setActiveSheetIndex( 0 );

					// Set active sheet index to the first sheet, so Excel opens this as the first sheet.
					$spreadsheet->setActiveSheetIndex( 0 );

					// Redirect output to a client’s web browser (Xlsx).
					header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
					header( 'Content-Disposition: attachment;filename="Territory Assigment Record.xlsx"' );
					header( 'Cache-Control: max-age=0' );
					// If you're serving to IE 9, then the following may be needed.
					header( 'Cache-Control: max-age=1' );

					// If you're serving to IE over SSL, then the following may be needed.
					header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past.
					header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified.
					header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1.
					header( 'Pragma: public' ); // HTTP/1.0.

					$writer = IOFactory::createWriter( $spreadsheet, 'Xlsx' );
					$writer->save( 'php://output' );
					exit;

			}

		}

		/**
		 * Display content of export page
		 */
		public static function jtop_export_page_html() {
			?>
			<h1><?php esc_html_e( 'Export data from JW Territory' ); ?></h1>
			<div class="container">
				<div class="container">
				<h3><?php esc_html_e( 'Generate Territory Assigment Record to EXEL file' ); ?></h3>
				<form method="post" action="">
					<input type="submit" name="export_excel"  value="<?php esc_html_e( 'Generate file' ); ?>" class="button-primary"/>
				</form>
			</div>
			<?php
		}
	}
	new Jtop_Export();
}
