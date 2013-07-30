<?php

/**
 * Primary comment rating plugin class
 *
 **/
class Comment_Ratings {

	/**
	 * Initiates filters and actions for the plugin.
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts'           , array( $this, 'external_css' ) );
		add_action( 'wp_ajax_upvote_comment'       , array( $this, 'ajax_vote' ) );
		add_action( 'wp_ajax_nopriv_upvote_comment', array( $this, 'ajax_vote' ) );
		add_action( 'wp_enqueue_scripts'           , array( $this, 'external_scripts' ) );
		add_action( 'wp_head'                      , array( $this, 'inline_scripts' ) );
		add_action( 'init'                         , array( $this, 'ajax_vote' ) );
		add_filter( 'comment_reply_link'           , array( $this, 'vote_buttons_filter' ) );
		add_action( 'plugins_loaded'               , array( $this, 'load_textdomain' ) );
	}
	
	/*
	 * Make the plugin translatable
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'cr', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
	}
	
	/*
	 * Adds CSS to front end of site
	 */
	public function external_css() {
		wp_enqueue_style( 'comment_ratings', CR_URL . '/style.css', false, '', 'screen' );
	}
	
	/*
	 * Filter to add buttons automagically to the comments area
	 */
	public function vote_buttons_filter( $buttons ) {
		$buttons = $buttons . $this->vote_buttons();
		return $buttons;
	}
	
	/**
	 * Load external scripts
	 */
	public function external_scripts() {
		wp_enqueue_script( 'jquery' );
	}

	public function get_user_cookie() {
		if ( isset( $_COOKIE['comment_ratings'] ) ) {
			$hash = $_COOKIE['comment_ratings'];
		} else {
			$hash = '';
		}
		
		return $hash;
	}
	
	/**
	 * AJAX Handler for the actual upvote button
	 *
	 * @access public
	 */
	public function ajax_vote() {
		
		if ( ! isset( $_REQUEST['cookie'] ) || ! isset( $_REQUEST['comment_id'] ) || ! isset( $_REQUEST['post_id'] ) )
			return;
		
		$comment_id = absint( $_REQUEST['comment_id'] );
		$post_id    = absint( $_REQUEST['post_id'] );
		$cookie     = esc_html( $_REQUEST['cookie'] );
		
		// Check if user has voted before (don't want them changing their vote afterwards)
		if ( '' == get_comment_meta( $comment_id, 'votes_from_' . $cookie, true ) ) {
			
			// Store up-vote votes
			$up_vote = get_comment_meta( $comment_id, 'up_vote', true );
			if ( 'upvote_comment' == $_REQUEST['action'] ) {
				$up_vote++;
				update_comment_meta( $comment_id, 'votes_from_' . $cookie, 'up' );
				update_comment_meta( $comment_id, 'up_vote', $up_vote );
			}
			
			// Store down-vote votes
			$down_vote = get_comment_meta( $comment_id, 'down_vote', true );
			if ( 'downvote_comment' == $_REQUEST['action'] ) {
				$down_vote++;
				update_comment_meta( $comment_id, 'votes_from_' . $cookie, 'down' );
				update_comment_meta( $comment_id, 'down_vote', $down_vote );
			}
			
			$karma = $up_vote / ( $up_vote + $down_vote );
			$status['status_code'] = wp_update_comment( array( 'comment_ID' => $comment_id, 'comment_karma' => $karma ) );
			$status['karma']       = $karma;
			
		} else {
			$status['status_code'] = '-1';
			$status['reason']      = apply_filters( 'fmz_error_message_user_limit', __( 'This user has reached their limit for this comment' ), $comment_id );
		}

		die( json_encode( $status ) );
	}

	/**
	 * Returns Upvote button for comments
	 *
	 * @return string URL for upvote function
	 */
	public function display_upvote_button() {
		
		// Set class for link (based on whether user has voted previously or not)
		if ( 'up' == get_comment_meta( get_comment_ID(), 'votes_from_' . $this->get_user_cookie(), true )  &&  '' != $this->get_user_cookie()  ) {
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
	 * Handles the actual front-end interaction.
	 * Presumes that we actually have
	 */
	public function inline_scripts() {
		?>
		<script>
			function randomString(len) {
				charSet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				var randomString = '';
				for (var i = 0; i < len; i++) {
					var randomPoz = Math.floor(Math.random() * charSet.length);
					randomString += charSet.substring(randomPoz,randomPoz+1);
				}
				return randomString;
			}
			function readCookie(name) {
				var nameEQ = name + "=";
				var ca = document.cookie.split(';');
				for(var i=0;i < ca.length;i++) {
					var c = ca[i];
					while (c.charAt(0)==' ') c = c.substring(1,c.length);
					if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
				}
				return null;
			}
			function setCookie(c_name,value,exdays) {
				var exdate=new Date();
				exdate.setDate(exdate.getDate() + exdays);
				var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
				document.cookie=c_name + "=" + c_value;
			}
			
			( function( window, $ ) {
				var document = window.document;
				var comment_karma = function() {
					var self = this;
					$(document).ready(function($){
						$( '.karma-already-voted' ).on( 'click', function(e){
							e.preventDefault();
						});
						$( '.karma-can-vote .comment-downvote' ).on( 'click', function(e){
							$(this).addClass('already-downvoted');
						});
						$( '.karma-can-vote .comment-upvote' ).on( 'click', function(e){
							$(this).addClass('already-upvoted');
						});
						$( '.karma-can-vote a' ).on( 'click', function(e){
							
							// Set the cookie
							var the_cookie = readCookie("comment_ratings");
							var new_cookie = $('#comments_rating_cookie').val();
							if(the_cookie == null) {
								setCookie('comment_ratings',new_cookie,365);
							}
							
							// Send to AJAX processor
							var $this = $( this ), parent = $this.parents( 'ol' );
							var href_value = $(this).attr("href");
							e.preventDefault();
							var data = {};
							$.post( href_value, data, function() {}, 'json' );
						});
					});
				};

				window.comment_karma = new comment_karma();

			} )( window, jQuery );
		</script>
		<?php
	}
	
	/**
	 * Returns Downvote button for comments
	 *
	 * @return string URL for upvote function
	 */
	public function display_vote_button( $direction ) {
		
		// Set class for link (based on whether user has voted previously or not)
		if ( $direction == get_comment_meta( get_comment_ID(), 'votes_from_' . $this->get_user_cookie(), true ) &&  '' != $this->get_user_cookie() ) {
			$voted = 'already-' . $direction . 'voted';
		} else {
			$voted = 'comment-' . $direction . 'vote';
		}
		
		// Set class for link (based on whether user has voted previously or not)
		if ( 'down' == $direction ) {
			$text = __( 'Disagree', 'cr' );
		} else {
			$text = __( 'Agree', 'cr' );
		}
		
		// Get URL
		$url = add_query_arg(
			array(
				'action'     => $direction . 'vote_comment',
				'comment_id' => get_comment_ID(),
				'post_id'    => get_the_ID(),
				'cookie'     => CR_COOKIE
			),
			admin_url( 'admin-ajax.php' )
		);
		
		$string = '<a class="' . $direction . '-vote ' . $voted . '" href="' . esc_url( $url ) . '">' . $text;
		if ( '' != get_comment_meta( get_comment_ID(), $direction . '_vote', true ) ) {
			$string .= ' (' . get_comment_meta( get_comment_ID(), $direction . '_vote', true ) . ')';
		}
		$string .= '</a>';
		
		return $string;
	}
	
	/*
	 * Vote buttons
	 */
	public function vote_buttons() {
		$buttons = '';
		
		// Set classes dependent on whether user can vote or not
		$class = 'karma-vote';
		if ( '' == get_comment_meta( get_comment_ID(), 'votes_from_' . $this->get_user_cookie(), true ) || '' == $this->get_user_cookie() ) {
			$class .= ' karma-can-vote';
		} else {
			$class .= ' karma-already-voted';
		}
		// Set classes dependent on whether user can vote or not
		$class = 'karma-vote';
		if ( '' == get_comment_meta( get_comment_ID(), 'votes_from_' . $this->get_user_cookie(), true ) || '' == $this->get_user_cookie() ) {
			$class .= ' karma-can-vote';
		} else {
			$class .= ' karma-already-voted';
		}
		
		// Set the cookie value if not already present (note, cookie is not actually created until they actually click on something)
		if ( ! defined( 'CR_COOKIE' ) ) {
			if ( ! isset( $_COOKIE['comment_ratings'] ) ) {
				define( 'CR_COOKIE', md5( rand( 1, 99999999999 ) ) );
			} else {
				define( 'CR_COOKIE', $_COOKIE['comment_ratings'] );
			}
			$buttons = '<input type="hidden" value="' . CR_COOKIE . '" id="comments_rating_cookie" />';
		}
		
		$buttons .= ' <span class="' . $class . '">' . $this-> display_vote_button( 'up' ) . $this-> display_vote_button( 'down' ) . '</span>';
		
		return $buttons;
	}
}

$comment_ratings = new Comment_Ratings;
