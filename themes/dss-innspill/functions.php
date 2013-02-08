<?php

// AJAX

add_action( 'wp_enqueue_scripts', 'dss_comment_vote_scripts', 10, 1 );
add_action( 'init', 'dss_comment_vote_init' );

function dss_comment_vote_init() {
	add_action( 'wp_ajax_nopriv_dss_comment_vote', 'dss_comment_vote_callback' );
	add_action( 'wp_ajax_dss_comment_vote', 'dss_comment_vote_callback' );
}

/**
 * Load ajax vote handling JavaScript and set nonce
 * 
 * @author Per Soderlind <per.soderlind@dss.dep.no>
 */
function dss_comment_vote_scripts() {
	$url = get_stylesheet_directory_uri();	

	// multisite fix, use home_url() if domain mapped to avoid cross-domain issues
	if ( home_url() != site_url() )
		$ajaxurl = home_url( '/wp-admin/admin-ajax.php' );
	else
		$ajaxurl = admin_url( 'admin-ajax.php' );
	$ajaxurl = apply_filters( 'safe_report_comments_ajax_url', $ajaxurl );

	wp_enqueue_script(  'dss_comment_coockie', $url . '/js/jquery-cookie/jquery.cookie.js', array('jquery'),'1.3.1');
	wp_enqueue_script(  'dss_comment_ajax', $url . '/js/dss_comment_vote.js', array( 'jquery','dss_comment_coockie' ), '1.2' );
	wp_localize_script( 'dss_comment_ajax', 'oDSSvote', array(
			'nonce' => wp_create_nonce( "dss_comment_vote_security" )
			,'ajaxurl' =>  $ajaxurl
		)
	);
}

/**
 * Ajax vote handling
 *
 * Triggered by the 'wp_ajax_dss_comment_vote' and 'wp_ajax_nopriv_dss_comment_vote' hooks in dss_comment_ajax_init()
 *
 * Called by js/dss_comment_ajax.js
 *
 * 
 * @author Per Soderlind <per.soderlind@dss.dep.no>
 */
function dss_comment_vote_callback() {
	header( "Content-type: application/json" );
	if ( check_ajax_referer( 'dss_comment_vote_security', 'security', false ) ) {

		$comment_id = $_POST['commentid'];
		$is_upvote = $_POST['is_upvote'];

		if ( true == $is_upvote ) {
			$upvote = get_comment_meta ( $comment_id, 'upvote', true );
			$newvote = ( !empty( $upvote ) ) ? $upvote + 1 : 1;
			$result = update_comment_meta( $comment_id, 'upvote', $newvote );

			$commentarr = get_comment($comment_id, ARRAY_A);
			$commentarr['comment_karma'] = $newvote; 
			wp_update_comment($commentarr);

		} else {
			$downvote = get_comment_meta ( $comment_id, 'downvote', true );
			$newvote = ( !empty( $downvote ) ) ? $downvote - 1 : -1;
			$result = update_comment_meta( $comment_id, 'downvote', $newvote );
		}

		if ( is_wp_error( $result ) ) {
			$error_string = $result->get_error_message();
			echo json_encode( array(
					'response' => 'failed'
					, 'message' => $error_string
				) );
		} else {
			echo json_encode( array(
					'response'=>'success'
					, 'message'=>'Lagt til!'
					, 'newvote' => $newvote
				) );
		}
	} else {
		echo json_encode( array(
				'response' => 'failed'
				, 'message' => 'invalid nonse'
			) );
		exit;
	}
	die();
}

// WIDGET

if (!class_exists('dss_vote_comments_widget')) {
	class dss_vote_comments_widget extends WP_Widget {
	
		var $localizationDomain = "dss";
	
		function __construct() {	
			parent::__construct(
				'dss_vote_comments_widget', // Base ID
				'Mest popul&aelig;re innspill', // Name
				array( 'description' => __( 'Viser de mest populære innspill', $this->localizationDomain ), ) // Args
			);	
		}
		function widget($args, $instance) {
			global $post;
			
			//if (!is_single() && !is_page()) return;
			
			extract($args, EXTR_SKIP);
			echo $before_widget;


			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			$max = empty($instance['max']) ? '5' : apply_filters('widget_max', $instance['max']);
			
			if ( !empty( $title ) ) { 
				echo $before_title . $title . $after_title; 
			}
			echo '<div class="dss-vote-comments-widget">';
			$options = get_option('dss_vote_comments_options');
				
			echo "<ul>";

			$args = array(
				'status' => 'approve',
			);

			$comments = get_comments($args);
			usort($comments, array(&$this,'comment_comparator'));
			$i = 0;
			$novote = 0;
			foreach($comments as $comment) :
				if ($i >= $max) break;
				if ($comment->comment_karma == 0) {
					$novote++;
				} else {
					printf("<li><a href=\"%s\" data-karma=\"%s\">%s</a> (%s)</li>"
						, get_comment_link($comment) 
						, $comment->comment_karma
						, (20 > strlen($comment->comment_content)) ? $comment->comment_content : mb_substr($comment->comment_content,0,17) . '...'
						, $comment->comment_karma
					);
				}
				$i++;
			endforeach;
			if ($novote == $i) {
				echo "<li>Venter på stemmer</li>";
			}
			echo "</ul>";
			echo '</div>';

			echo $after_widget;
		}

		function comment_comparator($a, $b) {
	        $compared = 0;
	        if($a->comment_karma != $b->comment_karma) {
	            $compared = $a->comment_karma < $b->comment_karma ? 1:-1;
	        }
	        return $compared;
		 }

		
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['max'] = strip_tags($new_instance['max']);
			
			return $instance;
		}
		
		function form($instance) {
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'max' => '5'  ));
			$title = strip_tags($instance['title']);
			$max = strip_tags($instance['max']);
			printf('<p><label for="%s">%s <input class="widefat" id="%s" name="%s" type="text" value="%s" /></label></p>'
				,$this->get_field_id('title')
				,__('Title:',$this->localizationDomain)
				,$this->get_field_id('title')
				,$this->get_field_name('title')
				,attribute_escape($title)
			);

			printf('<label for="%s">%s </label><input class="widefat" id="%s" name="%s" type="number" value="%d"/>'
				,$this->get_field_id('max')
				,__('Hvor mange:',$this->localizationDomain)
				,$this->get_field_id('max')
				,$this->get_field_name('max')
				,attribute_escape($max)
			);
		}
	} // end class
	add_action( 'widgets_init', create_function( '', 'register_widget( "dss_vote_comments_widget" );' ) );
} // end if widget class exists

//COMMENT TEMPLATE

/**
 * Override the default comment template, adding buttons for voting
 *
 * @author Per Soderlind <per.soderlind@dss.dep.no>
 */


if ( ! function_exists( 'dss_comment' ) ) :
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own dss_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * Note: This function is modified from the Twenty Eleven theme
	 *
	 * @since DSS Framework 1.0
	 * @author Ryan Hellyer <ryan@metronet.no>
	 */
	function dss_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;

		// delete_comment_meta($comment->comment_ID, 'upvote');
		// delete_comment_meta($comment->comment_ID, 'downvote');

		switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'dss' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'dss' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
		break;
	default :
?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
					$avatar_size = 68;
					if ( '0' != $comment->comment_parent )
						$avatar_size = 39;

					echo get_avatar( $comment, $avatar_size );

					/* translators: 1: comment author, 2: date and time */
					printf( __( '%1$s on %2$s <span class="says">said:</span>', 'dss' ),
						sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
						sprintf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							get_comment_time( 'c' ),
							/* translators: 1: date, 2: time */
							sprintf( __( '%1$s at %2$s', 'dss' ), get_comment_date(), get_comment_time() )
						)
					);
					?>

					<?php edit_comment_link( __( 'Edit', 'dss' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'dss' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php
				/**
				 * 
				 *
				 *
				 */
				$upvote = get_comment_meta ( $comment->comment_ID, 'upvote', true );
				$upvote = ( !empty( $upvote ) ) ? $upvote : 0;
				$downvote = get_comment_meta ( $comment->comment_ID, 'downvote', true );
				$downvote = ( !empty( $downvote ) ) ? $downvote : 0;

				printf( '<a class="comment-reply-link vote" data-commentid="%s" data-is_upvote="%s">Enig (%s)</a>', $comment->comment_ID, true, $upvote );
				printf( ' <a class="comment-reply-link vote" data-commentid="%s" data-is_upvote="%s">Uenig (%s)</a>', $comment->comment_ID, false, $downvote );
				?>
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'dss' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
		break;
		endswitch;
	}
endif; // ends check for dss_comment()
