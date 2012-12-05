<?php
/*

Plugin Name: WP 2 PDF
Plugin URI: http://pixopoint.com/products/wp2pdf/
Description: Convert your site into a PDF
Author: Ryan Hellyer
Version: 1.0
Author URI: http://metronet.no/

Copyright (c) 2012 Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/




/**
 * Define constants
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@pixopoint.com>
 */
define( 'WP2PDF_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'WP2PDF_URL', plugins_url( '', __FILE__ ) ); // Plugin folder URL
if ( ! defined( 'WP2PDF_PAPER_SIZE' ) ) {
	define( 'WP2PDF_PAPER_SIZE', 'a4' ); // The PDF paper size
}
if ( ! defined( 'WP2PDF_DIRECTION' ) ) {
	define( 'WP2PDF_DIRECTION', 'portrait' ); // Choose between portait and landscape
}
define( 'WP2PDF_QUERYVAR', 'wp2pdfb' ); // Query var used to access the PDF file




new WP_2_PDF();

/**
 * Convert WordPress posts and comments to PDF
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class WP_2_PDF {

	/**
	 * Class constructor
	 * Adds all the methods to appropriate hooks or shortcodes
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function __construct() {

		// Bail out now if not in admin panel
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		if ( isset( $_GET[WP2PDF_QUERYVAR] ) ) {
			add_action( 'admin_init', array( $this, 'create_pdf' ) );
		}
	}

	/**
	 * Add the admin menu item
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	function admin_menu() {

		add_management_page(
			__( 'Export PDF', 'wp2pdf' ), // Page title
			__( 'Export PDF', 'wp2pdf' ), // Menu title
			'manage_options',             // Capability
			'wp2pdf',                     // Menu slug
			array( $this, 'admin_page' )  // The page content
		);
	}

	/**
	 * The admin page contents
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @global string $page_content Crude hack to dump meta redirect into admin page
	 */
	public function admin_page() {
		global $page_content;

		?>
		<style type="text/css">
		#icon-wp2pdf-icon {
			background: url(<?php echo plugins_url( 'admin-icon.png' , __FILE__ ); ?>) no-repeat;
		}
		#page-title {
			line-height: 52px;
		}
		</style>
		<div class="wrap">
			<h2 id="page-title"><?php screen_icon( 'wp2pdf-icon' ); ?><?php _e( 'Export to PDF', 'wp2pdf' ); ?></h2><?php

			if ( ! isset( $_GET[WP2PDF_QUERYVAR] ) ) : ?>
			<p>
				<?php _e( 'The export process may take a long time if you have many blog posts. This page will continously refresh itself until the process is completed. If you close the browser window, you will need to being the export process again.', 'wp2pdf' ); ?>
			</p><?php
			endif;

			if ( isset( $page_content ) ) {
				echo $page_content;
			}

			if ( ! isset( $_GET[WP2PDF_QUERYVAR] ) ) :
			$url = admin_url( 'tools.php?page=wp2pdf&' . WP2PDF_QUERYVAR . '=0' );
			?>
			<p>
				<a href="<?php echo wp_nonce_url( $url, 'wp2pdf' ); ?>" class="button-primary"><?php _e( 'Export PDF', 'wp2pdf' ); ?></a>
			</p>
			<p>
				<a href="<?php echo wp_nonce_url( $url . '&images=show', 'wp2pdf' ); ?>" class="button-primary"><?php _e( 'Export PDF with images', 'wp2pdf' ); ?></a>
			</p><?php
			endif; ?>
		</div>
		<?php

	}

	/**
	 * Create PDF
	 * Creating the whole PDF in one hit is too resource
	 * intensive, and so they are created post by post
	 * then merged once they're all created.
	 * After creation, the temporary files are deleted
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @return string
	 * @global array    $post          Internal WordPress global, used here for processing posts
	 * @global integer  $blog_id       Internal WordPress global, used here to set the file name
	 * @global string   $page_content  Crude hack to allow for creating a meta tag early on, then dumping it out into the middle of the admin page later on
	 * 
	 */
	function create_pdf() {
		global $post, $blog_id, $page_content;

		// Stop the server from timing out
		set_time_limit( 600 );
		ini_set( 'max_execution_time', 600 );

		// Bail out now if user doesn't have permission to manage options
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Nonce check
		check_admin_referer( 'wp2pdf' );

		// Grab IDs of all posts and pages and place into an array ($the_posts)
		$count = 0;
		$args = array( 'numberposts' => -1 );
		$myposts = get_posts( $args );
		foreach( $myposts as $post ) {
			setup_postdata( $post );
			$the_posts[$count] = $post->ID;
			$count++;
		}

		$args = array( 'numberposts' => -1, 'post_type' => 'page' );
		$myposts = get_posts( $args );
		$the_posts[$count] = '';
		foreach( $myposts as $post ) {
			setup_postdata( $post );
			$the_posts[$count] = $post->ID;
			$count++;
		}

		if ( isset( $_GET[WP2PDF_QUERYVAR] ) ) {
			$iteration = $_GET[WP2PDF_QUERYVAR];
		} else {
			$iteration = 1;
		}

		// Create temporary PDF if haven't finished them yet
		if ( $iteration != $count ) {

			// Create the temporary PDF file
			$post_id = $the_posts[$iteration];
			$this->create_temporary_pdf( $post_id );

			$iteration++;

			// Redirect to new page
			$url = admin_url( 'tools.php?page=wp2pdf&' . WP2PDF_QUERYVAR . '=' . $iteration . '&count=' . $count );
			if ( isset( $_GET['show'] ) ) {
				$url .= '&show=images';
			}
			$url = wp_nonce_url( $url, 'wp2pdf' );

			// Create the page content (echo'd out onto admin page later on)
			$page_content = '
			<br /><br /><br />';

			if ( $iteration == $count ) {
				$page_content .= '
				<h1>' . __( 'PDF construction complete.', 'wp2pdf' ) . '</h1>
				<meta http-equiv="refresh" content="0;URL=\'';
				$page_content .= $url;
				$page_content .= '\'">';
			} else {
				$page_content .= '
				<h1>' . __( 'PDF Under construction, please wait. Do not close this browser window.', 'wp2pdf' ) . '</h1>
				<h2>' . __( 'Processing ', 'wp2pdf' ) . $iteration . __( '/', 'wp2pdf' ) . $count . '</h2>
				<meta http-equiv="refresh" content="0;URL=\'';
				$page_content .= $url;
				$page_content .= '\'">';
			}

		} else {
			// Grab current uploads URL
			$upload_dir = wp_upload_dir();
			$upload_dir = $upload_dir['basedir'];

			// Initiate PDF merger
			require( 'pdfmerger/PDFMerger.php' );
			$pdf = new PDFMerger;

			// Merge the temporary PDFs together
			$number = 0;
			while( $number < $count ) {
				$pdf->addPDF( $upload_dir . '/' . WP2PDF_QUERYVAR . '-file-' . $the_posts[$number] . '.pdf', 'all');
				$number++;
			}

			// Finally, merge the PDF together
			$pdf_merged = $pdf->merge( 'string', 'export.pdf' );

			// Deleting all the temporary PDF files
			$delete = 0;
			while( $delete < $count ) {
				unlink( $upload_dir . '/' . WP2PDF_QUERYVAR. '-file-' . $the_posts[$delete] . '.pdf' );
				$delete++;
			}

			// Output final PDF file
			header( 'Content-type: application/pdf' );
			header( 'Content-Disposition: attachment; filename="export-site-' . $blog_id . '.pdf"' );
			echo $pdf_merged;
			exit;
		}

	}

	/**
	 * Create the HTML ready for PDF'ing
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @param integer $post_to_be_processed
	 */
	public function create_temporary_pdf( $post_id ) {

		// Create HTML
		ob_start();
		require( 'template.php' );
		$html = ob_get_contents();
		ob_end_clean();

		// Stripping images from HTML
		if ( ! isset( $_GET['show'] ) ) {
			$html = str_replace( '<!DOCTYPE html>', 'THISISTHEDOCTYPE', $html );
			$html = strip_tags( $html, '<title><!DOCTYPE html><br><hr><p><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><div><body><html><meta><link><style>' );
			$html = str_replace( 'THISISTHEDOCTYPE', '<!DOCTYPE html>', $html );
		}

		// Render temporary individual PDF files
		require_once( WP2PDF_DIR . 'dompdf/dompdf_config.inc.php' ); // Load DOMPDF library
		$pdf_file = $this->render_pdf( $html );

		// Grab current uploads URL
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		file_put_contents( $upload_dir . '/' . WP2PDF_QUERYVAR . '-file-' . $post_id . '.pdf', $pdf_file );
	}

	/**
	 * Renders the PDF
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	public function render_pdf( $html ) {

		$dompdf = new DOMPDF();
		$dompdf->load_html( $html );
		$dompdf->set_paper( WP2PDF_PAPER_SIZE, WP2PDF_DIRECTION );
		$dompdf->render();

		// $dompdf->stream( 'dompdf_out.pdf', array( 'Attachment' => false ) );exit;
		$pdf = $dompdf->output();
		return $pdf;
	}

}

