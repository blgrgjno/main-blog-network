<?php
/**
  * Plugin Name: Comment Karma
  * Plugin URI: http://freshmuse.com/
  * Description: Allows users to vote on comments, sorting them via AJAX by vote
  * Version: 0.7
  * Author: Ryan Hellyer / Metronet / Justin Sainton
  * Author URI: http://www.metronet.no/
  *
  * This code was built based on code provided by Justin Sainton
  **/

/**
 *
 * Hello, I am Comment Karma.  Pleased to make your acquaintance.
 *
 * This is what I do.
 *
 * FRONT-END FUNCTIONALITY
 *
 * - Creates a template tag to be placed in comments-custom.php that creates a simple "upvote"
 * button that adds 1 numerical point to the the comment_karma meta field for that comment.
 *
 * - The button is wrapped in an IP address verifying code that only allows the user to vote X amount of times
 * (Defaults to 20)
 *
 * - Comments auto sort themselves via AJAX and order themselves based on comment_karma meta value,
 * in descending order
 *
 **/
class FMZ_Comment_Karma {

	private static $instance;

	/**
	 * Get active object instance
	 *
	 * @static
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance )
			self::$instance = FMZ_Comment_Karma::init();

		return self::$instance;
	}


	/**
	 * Empty constructor
	 */
	public function __construct() {}

	/**
	 * Initiates filters and actions for the plugin.
	 *
	 * @static
	 */
	public static function init() {

		add_action( 'wp_ajax_upvote_comment'       , array( __CLASS__, 'ajax_vote' ) );
		add_action( 'wp_ajax_nopriv_upvote_comment', array( __CLASS__, 'ajax_vote' ) );
		add_action( 'wp_enqueue_scripts'           , array( __CLASS__, 'enqueue_jquery' ) );
		add_action( 'wp_head'                      , array( __CLASS__, 'js' ) );
		add_action( 'wp_head'                      , array( __CLASS__, 'css' ) );
//		add_action( 'init'                         , array( __CLASS__, 'ajax_upvote' ) );
	}
	
	public function css() {
		echo '
		<style>
			.karma-vote {
			}
			.karma-vote a {
				background: lime url(http://blogg.regjeringen.no/wp-content/plugins/dss-karma/images/sprite.png);
				width: 13px;
				height: 13px;
				display: inline-block;
				text-indent: -999em;
			}
			.already-upvoted {
				background-position: 0 -26px;
			}
			.comment-upvote {
				background-position: 0 0;
			}
			.already-downvoted {
				background-position: 0 -39px;
			}
			.comment-downvote {
				background-position: 0 -13px;
			}
		</style>';
	}

	/**
	 * Our only front-end dependency - 99.99% certain it will always be included, but we just don't know for sure now do we?
	 * 
	 * @static
	 */
	public static function enqueue_jquery() {
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * AJAX Handler for the actual upvote button
	 *
	 * @access public
	 * @static
	 */
	public static function ajax_vote() {

		$comment_id = absint( $_REQUEST['comment_id'] );
		$post_id    = absint( $_REQUEST['post_id'] );

		if ( self::get_vote_from_machine( $comment_id ) != true ) {

			// Store up-vote votes
			$up_vote = get_comment_meta( $comment_id, 'up_vote', true );
			if ( 'upvote_comment' == $_REQUEST['action'] ) {
				$up_vote++;
			}
			update_comment_meta( $comment_id, 'up_vote', $up_vote );

			// Store down-vote votes
			$down_vote = get_comment_meta( $comment_id, 'down_vote', true );
			if ( 'downvote_comment' == $_REQUEST['action'] ) {
				$down_vote++;
			}
			$down_vote++;
			update_comment_meta( $comment_id, 'down_vote', $down_vote );

			// Log vote talley
			update_comment_meta( $comment_id, 'votes_from_' . self::get_user_hash(), true );

			$karma = $up_vote / ( $up_vote + $down_vote );
			$status['status_code'] = wp_update_comment( array( 'comment_ID' => $comment_id, 'comment_karma' => $karma ) );
			$status['karma']       = $karma;
// Temporary logging code
$meta = get_comment_meta( $comment_id, 'votes_from_' . self::get_user_hash(), true );
$path = '/var/www/dss/wp-content/plugins/logs.txt';
$file = file_get_contents( $path );
file_put_contents( $path, $file . "Time: " . time() . "\nComment ID: " . $comment_id . "\nMeta: " . $meta . " \nHash: " . self::get_user_hash() . "\n\n" );
/*
*/
		} else {
			$status['status_code'] = '-1';
			$status['reason']      = apply_filters( 'fmz_error_message_user_limit', __( 'This user has reached their limit for this comment' ), $comment_id );
		}

		die( json_encode( $status ) );
	}

	/**
	 * Returns URL for Upvote functionality
	 *
	 * @static
	 * @return string URL for upvote function
	 */
	public static function get_upvote_url() {
		return apply_filters(
			'fmz_get_upvote_url',
			add_query_arg(
				array(
					'action'     => 'upvote_comment',
					'comment_id' => get_comment_ID(),
					'post_id'    => get_the_ID()
				),
				admin_url( 'admin-ajax.php' )
			)
		);
	}

	/**
	 * Returns URL for Downvote functionality
	 *
	 * @static
	 * @return string URL for upvote function
	 */
	public static function get_downvote_url() {
		return apply_filters(
			'fmz_get_downvote_url',
			add_query_arg(
				array(
					'action'     => 'downvote_comment',
					'comment_id' => get_comment_ID(),
					'post_id'    => get_the_ID()
				),
				admin_url( 'admin-ajax.php' )
			)
		);
	}

	/**
	 * Returns Upvote button for comments
	 *
	 * @static
	 * @return string URL for upvote function
	 */
	public static function display_upvote_button() {
		
		// Set class for link (based on whether user has voted previously or not)
		if ( true == self::get_vote_from_machine( get_comment_ID() ) ) {
			$voted = 'already-upvoted';
			$text = __( 'You already voted' );
		} else {
			$voted = 'comment-upvote';
			$text = __( 'Vote for this comment' );
		}
		
		$string = '<a class="up-vote ' . $voted . '" href="' . esc_url( self::get_upvote_url() ) . '">' . $text . '</a>';
		if ( '' != get_comment_meta( get_comment_ID(), 'up_vote', true ) ) {
			$string .= ' (' . get_comment_meta( get_comment_ID(), 'up_vote', true ) . ')';
		}
		
		return $string;
	}

	/**
	 * Returns Downvote button for comments
	 *
	 * @static
	 * @return string URL for upvote function
	 */
	public static function display_downvote_button() {

		// Set class for link (based on whether user has voted previously or not)
		if ( true == self::get_vote_from_machine( get_comment_ID() ) ) {
			$voted = 'already-downvoted';
			$text = __( 'You already voted' );
		} else {
			$voted = 'comment-downvote';
			$text = __( 'Vote against this comment' );
		}

		$string = '<a class="down-vote ' . $voted . '" href="' . esc_url( self::get_downvote_url() ) . '">' . $text . '</a>';
		if ( '' != get_comment_meta( get_comment_ID(), 'down_vote', true ) ) {
			$string .= ' (' . get_comment_meta( get_comment_ID(), 'down_vote', true ) . ')';
		}
		
		return $string;
	}

	/**
	 * Logs a vote for a specific comment from a specific commenter
	 * Used for preventing same user repeatedly voting for a comment
	 *
	 * @param int    $comment_id Comment ID
	 * @static
	 * @return bool Whether or not user has voted
	 */
	public static function get_vote_from_machine( $comment_id ) {
		if ( true == get_comment_meta( $comment_id, 'votes_from_' . self::get_user_hash(), true ) ) {
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Get the hash for a particular user
	 *
	 * @return str Hash based on known user data
	 */
	public function get_user_hash() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
		$http_charset = isset( $_SERVER['HTTP_ACCEPT_CHARSET'] ) ? $_SERVER['HTTP_ACCEPT_CHARSET'] : '';
		$http_encoding = isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
		$http_lang = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
		$http_ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$hash = md5( $ip . $http_charset . $http_encoding . $http_lang . $http_ua );
		return $hash;
	}

	/**
	 * Handles the actual front-end interaction.
	 * Presumes that we actually have
	 *
	 * @static
	 * @return void
	 */
	public static function js() {
		?>
		<style type="text/css">
			a.comment-upvote img {
				width: 25px;
				height: 25px;
			}
		</style>
		<script>
			( function( window, $ ) {
				var document = window.document;
				var comment_karma = function() {
					var self = this;
					$(document).ready(function($){

						$( '.karma-vote a' ).on( 'click', function(e){
							var $this = $( this ), parent = $this.parents( 'ol' );
							var href_value = $(this).attr("href");

							e.preventDefault();
							var data = {
											action     : 'upvote_comment',
										};

							$.post( href_value, data, function( response ) {
							}, 'json' );

						});

						$( 'a.comment-downvote' ).on( 'click', function(e){
							var $this = $( this ), parent = $this.parents( 'ol' );
							var href_value = $(this).attr("href");

							e.preventDefault();
							var data = {
											action     : 'downvote_comment',
										};

							$.post( href_value, data, function( response ) {
							}, 'json' );

						});

					});
				};

				window.comment_karma = new comment_karma();

			} )( window, jQuery );
		</script>
		<?php
	}
}

FMZ_Comment_Karma::get_instance();

/*
 * Vote buttons
 */
function fmz_vote_buttons() {
	echo '<div class="karma-vote">';
	echo FMZ_Comment_Karma::display_upvote_button();
	echo FMZ_Comment_Karma::display_downvote_button();
	echo '</div>';
}
