<?php /*

**************************************************************************

Plugin Name:  Clean Archives Reloaded
Plugin URI:   http://www.viper007bond.com/wordpress-plugins/clean-archives-reloaded/
Description:  A slick, Javascript-enhanced post archive list generator for WordPress 2.5+.
Version:      3.2.0
Author:       Viper007Bond
Author URI:   http://www.viper007bond.com/

**************************************************************************

Copyright (C) 2008-2011 Viper007Bond

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

**************************************************************************/

// Seems some themes bundle this plugin and then users install it,
// resulting in double output. This should be avoided, so use a global.
if ( !isset($cleanarchivesreloaded) )
	$cleanarchivesreloaded = false;

class CleanArchivesReloaded {

	# Configuration has been moved to the new options page at Settings -> Clean Archives.
	# You can also set the configuration via the shortcode tag.

	var $version = '3.2.0';

	// Class initialization
	function CleanArchivesReloaded() {
		if ( !function_exists('add_shortcode') )
			return;

		// Load up the localization file if we're using WordPress in a different language
		// Place it in this plugin's "languages" folder and name it "car-[value in wp-config].mo"
		load_plugin_textdomain( 'clean-archives-reloaded', false, '/clean-archives-reloaded/languages' );

		// Make sure all the plugin options have defaults set
		if ( FALSE === get_option('car_usejs') )       add_option( 'car_usejs', 1 );
		if ( FALSE === get_option('car_monthorder') )  add_option( 'car_monthorder', 'new' );
		if ( FALSE === get_option('car_postorder') )   add_option( 'car_postorder', 'new' );
		if ( FALSE === get_option('car_dynamicload') ) add_option( 'car_dynamicload', null );

		// Register all the hooks
		add_action( 'admin_menu', array(&$this, 'AddAdminMenu') );
		add_shortcode( 'cleanarchivesreloaded' , array(&$this, 'PostList') );
		add_shortcode( 'cartotalposts' , array(&$this, 'PostCount') );
		add_filter( 'widget_text', 'do_shortcode', 11 ); // For the text widget

		// Hook into wp_head early so we can potentially look for posts
		add_action( 'wp_head', array(&$this, 'MaybeEnqueueCSSJavascript'), 1 );

		// For users with a persistent object caching plugin installed, we need to dump the cache in some cases
		add_action( 'save_post', array(&$this, 'DeleteCache') );
		add_action( 'edit_post', array(&$this, 'DeleteCache') );
		add_action( 'delete_post', array(&$this, 'DeleteCache') );
	}


	// Register the admin menu
	function AddAdminMenu() {
		add_options_page( __('Clean Archives Reloaded', 'clean-archives-reloaded'), __('Clean Archives', 'clean-archives-reloaded'), 'manage_options', 'clean-archives-reloaded', array(&$this, 'OptionsPage') );
	}


	// The options page for this plugin
	function OptionsPage() { ?>

<div class="wrap">
	<h2><?php _e('Clean Archives Reloaded', 'clean-archives-reloaded'); ?></h2>

	<form method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>

	<table class="form-table">
		<tr>
			<th scope="row" colspan="2" class="th-full">
				<label for="car_usejs">
					<input name="car_usejs" type="checkbox" id="car_usejs" value="1" <?php checked( '1', get_option('car_usejs') ); ?> />
					<?php _e('Use Javascript to make the months collapsible', 'clean-archives-reloaded'); ?>
				</label>
			</th>
		</tr>
		<tr>
			<th scope="row" colspan="2" class="th-full">
				<label for="car_dynamicload">
					<input name="car_dynamicload" type="checkbox" id="car_dynamicload" value="1" <?php checked( '1', get_option('car_dynamicload') ); ?> />
					<?php _e("Only load the Javascript files when displaying an archive (this will break the archive list if it's used in your sidebar)", 'clean-archives-reloaded'); ?>
				</label>
			</th>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Month Ordering', 'clean-archives-reloaded'); ?></th>
			<td>
				<p>
					<label><input name="car_monthorder" type="radio" value="new" <?php checked( 'new', get_option('car_monthorder') ); ?> /> <?php _e('Show newest months first', 'clean-archives-reloaded'); ?></label><br />
					<label><input name="car_monthorder" type="radio" value="old" <?php checked( 'old', get_option('car_monthorder') ); ?> /> <?php _e('Show oldest months first', 'clean-archives-reloaded'); ?></label>
				</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Post Ordering', 'clean-archives-reloaded'); ?></th>
			<td>
				<p><?php _e('Within individual months...', 'clean-archives-reloaded'); ?></p>
				<p>
					<label><input name="car_postorder" type="radio" value="new" <?php checked( 'new', get_option('car_postorder') ); ?> /> <?php _e('Show newest posts first', 'clean-archives-reloaded'); ?></label><br />
					<label><input name="car_postorder" type="radio" value="old" <?php checked( 'old', get_option('car_postorder') ); ?> /> <?php _e('Show oldest posts first', 'clean-archives-reloaded'); ?></label>
				</p>
			</td>
		</tr>
	</table>

	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="car_usejs,car_monthorder,car_postorder,car_dynamicload" />
	</p>

	</form>
</div>

<?php
	}


	// If the user wants to, then look forward at the posts or Page to be displayed and check for the shortcode
	// This allows for only loading jQuery and this plugin's CSS/JS when the archive will be displayed
	function MaybeEnqueueCSSJavascript() {
		global $wp_query;

		// If the user opted to only have the JS/CSS loaded when the archive is to be displayed, then check for it
		if ( 1 == get_option('car_dynamicload') ) {
			// Abort if no posts (obviously)
			if ( !is_array($wp_query->posts) || empty($wp_query->posts) ) return;

			// Loop through each post looking for the shortcode
			foreach ( $wp_query->posts as $post ) {
				if ( false !== stripos( $post->post_content, '[cleanarchivesreloaded') || false !== stripos( $post->post_content, '[cartotalposts') ) {
					wp_enqueue_script( 'jquery' );
					add_action( 'wp_head', array(&$this, 'OutputCSSJavascript') ); // Even though we're in wp_head right now, default of 10 is greater than 1
					return;
				}
			}
		}

		// Else load regardless
		else {
			wp_enqueue_script( 'jquery' );
			add_action( 'wp_head', array(&$this, 'OutputCSSJavascript') ); // Even though we're in wp_head right now, default of 10 is greater than 1
		}
	}


	// Output a little helper CSS and the Javascript for the plugin
	// Based on code from http://www.learningjquery.com/2007/03/accordion-madness
	function OutputCSSJavascript() {
		global $cleanarchivesreloaded;

		if ( !empty($cleanarchivesreloaded) )
			return;

		$cleanarchivesreloaded = true;

		?>

	<!-- Clean Archives Reloaded v<?php echo $this->version; ?> | http://www.viper007bond.com/wordpress-plugins/clean-archives-reloaded/ -->
	<style type="text/css">.car-collapse .car-yearmonth { cursor: s-resize; } </style>
	<script type="text/javascript">
		/* <![CDATA[ */
			jQuery(document).ready(function() {
				jQuery('.car-collapse').find('.car-monthlisting').hide();
				jQuery('.car-collapse').find('.car-monthlisting:first').show();
				jQuery('.car-collapse').find('.car-yearmonth').click(function() {
					jQuery(this).next('ul').slideToggle('fast');
				});
				jQuery('.car-collapse').find('.car-toggler').click(function() {
					if ( '<?php echo js_escape( __('Expand All', 'clean-archives-reloaded') ); ?>' == jQuery(this).text() ) {
						jQuery(this).parent('.car-container').find('.car-monthlisting').show();
						jQuery(this).text('<?php echo js_escape( __('Collapse All', 'clean-archives-reloaded') ); ?>');
					}
					else {
						jQuery(this).parent('.car-container').find('.car-monthlisting').hide();
						jQuery(this).text('<?php echo js_escape( __('Expand All', 'clean-archives-reloaded') ); ?>');
					}
					return false;
				});
			});
		/* ]]> */
	</script>

<?php
	}


	// Grab all posts and filter them into an array
	function GetPosts() {
		global $wpdb;

		// If we have a cached copy of the filtered posts array, use that instead
		if ( $posts = wp_cache_get( 'posts', 'clean-archives-reloaded' ) )
			return $posts;

		// A direct query is used instead of get_posts() for memory reasons
		$rawposts = $wpdb->get_results( "SELECT ID, post_date, post_date_gmt, comment_status, comment_count FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_password = ''" );

		// Loop through each post and sort it into a structured array
		foreach( $rawposts as $post ) {
			$posts[ mysql2date( 'Y.m', $post->post_date ) ][] = $post;
		}
		$rawposts = null; // More memory cleanup

		// Store the results into the WordPress cache
		wp_cache_set( 'posts', $posts, 'clean-archives-reloaded' );

		return $posts;
	}


	// Generates the HTML output based on $atts array from the shortcode
	function PostList( $atts = array() ) {
		global $wp_locale;

		// Set any missing $atts items to the defaults
		$atts = shortcode_atts(array(
			'usejs'        => get_option('car_usejs'),
			'monthorder'   => get_option('car_monthorder'),
			'postorder'    => get_option('car_postorder'),
			'postcount'    => '1',
			'commentcount' => '1',
		), $atts);

		// Get the big array of all posts
		$posts = $this->GetPosts();

		// Sort the months based on $atts
		( 'new' == $atts['monthorder'] ) ? krsort( $posts ) : ksort( $posts );

		// Sort the posts within each month based on $atts
		foreach( $posts as $key => $month ) {
			$sorter = array();
			foreach ( $month as $post )
				$sorter[] = $post->post_date_gmt;

			$sortorder = ( 'new' == $atts['postorder'] ) ? SORT_DESC : SORT_ASC;

			array_multisort( $sorter, $sortorder, $month );

			$posts[$key] = $month;
			unset($month);
		}


		// Generate the HTML
		$html = '<div class="car-container';
		if ( 1 == $atts['usejs'] ) $html .= ' car-collapse';
		$html .= '">'. "\n";

		if ( 1 == $atts['usejs'] ) $html .= '<a href="#" class="car-toggler">' . __('Expand All', 'clean-archives-reloaded') . "</a>\n\n";

		$html .= '<ul class="car-list">' . "\n";

		$firstmonth = TRUE;
		foreach( $posts as $yearmonth => $posts ) {
			list( $year, $month ) = explode( '.', $yearmonth );

			$firstpost = TRUE;
			foreach( $posts as $post ) {
				if ( TRUE == $firstpost ) {
					$html .= '	<li><span class="car-yearmonth">' . sprintf( __('%1$s %2$d'), $wp_locale->get_month($month), $year );
					if ( '0' != $atts['postcount'] ) $html .= ' <span title="' . __('Post Count', 'clean-archives-reloaded') . '">(' . count($posts) . ')</span>';
					$html .= "</span>\n		<ul class='car-monthlisting'>\n";
					$firstpost = FALSE;
				}

				$html .= '			<li>' .  mysql2date( 'd', $post->post_date ) . ': <a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a>';

				// Unless comments are closed and there are no comments, show the comment count
				if ( '0' != $atts['commentcount'] && ( 0 != $post->comment_count || 'closed' != $post->comment_status ) )
					$html .= ' <span title="' . __('Comment Count', 'clean-archives-reloaded') . '">(' . $post->comment_count . ')</span>';

				$html .= "</li>\n";
			}

			$html .= "		</ul>\n	</li>\n";
		}

		$html .= "</ul>\n</div>\n";

		return $html;
	}


	// Returns the total number of posts
	function PostCount() {
		$num_posts = wp_count_posts( 'post' );
		return number_format_i18n( $num_posts->publish );
	}


	// Deletes the cached filtered posts array for users using a persistent caching plugin
	function DeleteCache() {
		wp_cache_delete( 'posts', 'clean-archives-reloaded' );
	}
}

// Start this plugin once all other plugins are fully loaded
add_action( 'init', create_function( '', 'global $CleanArchivesReloaded; $CleanArchivesReloaded = new CleanArchivesReloaded();' ) );


// Some backwards compatibility wrapper functions
function car_total_posts() {
	global $CleanArchivesReloaded;
	return $CleanArchivesReloaded->PostCount();
}
function clean_archives_reloaded() {
	global $CleanArchivesReloaded;
	echo $CleanArchivesReloaded->PostList();
}
function car_regenerate() {
	return FALSE;
}

?>