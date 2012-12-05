<?php

/**
 * Reorder posts
 * Adds drag and drop editor for reordering WordPress posts
 * 
 * Based on work by Scott Basgaard and Ronald Huereca
 * 
 * To use this class, simply instantiate it using an argument to set the post type as follows:
 * new Reorder( array( 'post_type' => 'post', 'order'=> 'ASC', 'orderby' => 'menu_order' ) );
 * 
 * @copyright Copyright (c), Metronet
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Reorder {

	/**
	 * Set private variables
	 * 
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 */
	private $post_type;
	private $direction;
	private $orderby;
	private $heading;
	private $initial;
	private $final;

	/**
	 * Class constructor
	 * 
	 * Sets definitions
	 * Adds methods to appropriate hooks
	 * 
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 * return void
	 */
	public function __construct( $args = array() ) {

		// Parse arguments
		$defaults = array(
			'post_type' => 'page',                       // Setting the post type to be re-ordered
			'order'     => 'ASC',                        // Setting the order of the posts
			'orderby'   => 'menu_order',                 // Setting the method of ordering
			'heading'   => __( 'Organiser', 'reorder' ), // Default text for heading
			'initial'   => __( '', 'reorder' ),          // Initial text displayed before sorting code
			'final'     => __( '', 'reorder' )           // Initial text displayed before sorting code
		);
		extract( wp_parse_args( $args, $defaults ) );

		// Set variables
		$this->post_type = $post_type;
		$this->order     = $order;
		$this->orderby   = $orderby;
		$this->heading   = $heading;
		$this->initial   = $initial;
		$this->final     = $final;

		// Add actions
		add_action( 'wp_ajax_post_sort',   array( $this, 'save_post_order'  ) );
		add_action( 'admin_print_styles',  array( $this, 'print_styles'     ) );
		add_action( 'admin_print_scripts', array( $this, 'print_scripts'    ) );
		add_action( 'admin_menu',          array( $this, 'enable_post_sort' ), 10, 'page' );
		add_action( 'admin_print_styles',  array( $this, 'create_nonce'     ) );
	}

	/**
	 * Creating the nonce value used within sort.js
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 */
	public function create_nonce() {
		echo "<script>sortnonce = '" .  wp_create_nonce( 'sortnonce' ) . "';</script>";
	}

	/**
	 * Saving the post oder for later use
	 *
	 * @author Ryan Hellyer <ryan@metronet.no> and Ronald Huereca <ronald@metronet.no>
	 * @since Reorder 1.0
	 */
	public function save_post_order() {
		global $wpdb;

		// Verify nonce value, for security purposes
		wp_verify_nonce( json_encode( array( $_POST['nonce'] ) ), 'sortnonce' );

		// Split post output
		$order = explode( ',', $_POST['order'] );

		// Loop through blocks and stash in DB accordingly
		$counter = 0;
		foreach ( $order as $post_id ) {
			$wpdb->update(
				$wpdb->posts,
				array( 'menu_order' => $counter ),
				array( 'ID'         => $post_id )
			);
			$counter++;
		}

		die( 1 );
	}

	/**
	 * Print styles to admin page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 */
	public function print_styles() {
		global $pagenow;

		$pages = array( 'edit.php' );

		if ( in_array( $pagenow, $pages ) )
			wp_enqueue_style( 'reorderpages_style', get_template_directory_uri() . '/admin.css' );

	}

	/**
	 * Print scripts to admin page
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 */
	public function print_scripts() {
		global $pagenow;

		$pages = array( 'edit.php' );
		if ( in_array( $pagenow, $pages ) ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'levert_posts', get_template_directory_uri() . '/scripts/sort.js' );
		}
	}

	/**
	 * Add submenu
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 */
	public function enable_post_sort() {
		add_submenu_page(
			'edit.php?post_type=' . $this->post_type,
			__( 'Organiser', 'reorder' ),
			__( 'Organiser', 'reorder' ),
			'edit_posts', basename( __FILE__ ),
			array( $this, 'sort_posts' )
		);
	}

	/**
	 * HTML ourput
	 *
	 * @author Ryan Hellyer <ryan@metronet.no>
	 * @since Reorder 1.0
	 */
	public function sort_posts() {
		$posts = new WP_Query( 'post_type=' . $this->post_type . '&posts_per_page=-1&orderby=' . $this->orderby . '&order=' . $this->order );
		?>
		<div class="wrap">
		<h2><?php echo $this->heading; ?> <img src="<?php echo admin_url( 'images/loading.gif' ); ?>" id="loading-animation" /></h2>
		<?php echo $this->initial; ?>
		<ul id="post-list">
		<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
			<li id="<?php the_id(); ?>"><?php the_title(); ?></li>			
		<?php endwhile; ?>
		<?php echo $this->final; ?>
		</div><?php
	}

}
