<?php

function getAuthorMeta($simple = false) {
	global $author;
	if(isset($_GET['author_name'])) :
	$curauth = get_userdatabylogin($_GET['author_name']);
	else :
	$curauth = get_userdata(intval($author));
	endif;

	global $authordata;
    if (in_category('nyheter')) { return ""; }

	// Get user type
	$user_type = get_user_meta($authordata->ID, "user_type", true);
	
	if (!$simple) {
		$meta_author  = '<div class="author-meta-box">';
	}
	else {
		$meta_author  = '<div class="author-meta">';
	}
	
	if (strpos($authordata->display_name, "tdomf") === 0) {
		$meta_author .= sprintf('<span class="author_name author_%2$s"><span class="a_alt">%1$s</span></span>',
			tdomf_get_the_submitter(),
			$user_type
		);
	}
	else {
		$meta_author .= sprintf('<span class="author_name author_%2$s"><a href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( $post->post_author, $authordata->user_nicename ),
			esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
			get_the_author(),
			$user_type
		);
	}
	if (!$simple) {
		if ($authordata->description) {
			$meta_author .= '<div class="author_description">';
			$meta_author .= $authordata->user_description;
			$meta_author .= "</div>";
		}
		if ($authordata->user_url) {
			// Fix URLs without http://
			$website = trim($authordata->user_url);
			if (strpos($website, "http") === false) $website = "http://" . $website;
			
			$meta_author .= '<div class="author_url">';
			$meta_author .= sprintf('<a href="%1$s" title="%2$s" rel="nofollow" target="_blank" class="newwin">%1$s</a>',
				$website,
				get_the_author());
			$meta_author .= "</div>";
		}
		$meta_author .= get_social_bookmarks();
	}
	$meta_author .= "</div>";
	
	return $meta_author;
}

function get_statement_meta($post_id) {
	$topic_id = get_post_custom_values(get_theme_option('parent_topic_field_name'), $post_id);
	if ($topic_id = $topic_id[0]) {
		$topic_title = get_the_title($topic_id);
		$topic_url = get_permalink($topic_id);
		$statement_url = $topic_url.get_theme_option('slug_statement')."/".$post_id."/";
		
		return (object) array(
			'topic_id' => $topic_id,
			'topic_title' => $topic_title,
			'topic_url' => $topic_url,
			'statement_url' => $statement_url,
		);
	}
	return false;
}

function my_loginout($content) {
	$thechar = (strrpos($content , "?") !== false) ? "&amp;" : "?";
	return preg_replace('/">/', $thechar.'redirect_to='.esc_url($_SERVER['REQUEST_URI']).'">', $content, 1);
}
add_filter('loginout','my_loginout');
add_filter('register','my_loginout');

function check_post_thumbnail() {
	global $post;
	if ($post->post_type == "post") {
		$statement_meta = get_statement_meta($post->ID);
		$page_id = $statement_meta->topic_id;
	}
	else {
		$page_id = $post->ID;
	}
	if (has_post_thumbnail($page_id)) {
		global $wp_query;
		$show = get_query_var('show') ? get_query_var('show') : $_GET['show'];
		$extraclass = "";
		if ($show == get_theme_option('slug_write')) $extraclass = "thumbnail-spacer-write";
		
		echo "<div class='thumbnail-spacer ".$extraclass."'>&nbsp;</div>";
	}
}
add_action('thematic_abovemainasides', 'check_post_thumbnail');

/* Truncate / Excerpt by words */

function nhop_trim_excerpt($text, $excerpt_length=35) {
	$text = strip_shortcodes( $text );
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = strip_tags($text);
	$excerpt_more = apply_filters('excerpt_more', ' ' . '(...)');
	$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
	if ( count($words) > $excerpt_length ) {
		array_pop($words);
		$text = implode(' ', $words);
		$text = $text . $excerpt_more;
	} else {
		$text = implode(' ', $words);
	}
	$text = str_replace('[...]', '(&hellip;)', $text);
	return "<p>".$text."</p>";
}

function get_the_excerpt_here($post_id) {
  global $wpdb;
  $query = "SELECT post_excerpt FROM $wpdb->posts WHERE ID = $post_id LIMIT 1";
  $query = $wpdb->prepare( $query );
  $result = $wpdb->get_results($query, ARRAY_A);
  return $result[0]['post_excerpt'];
}

/* Truncate / Excerpt by characters */

/*
@param string $text String to truncate.
@param integer $length Length of returned string, including ellipsis.
@param string $ending Ending to be appended to the trimmed string.
@param boolean $exact If false, $text will not be cut mid-word
@param boolean $considerHtml If true, HTML tags would be handled correctly
@return string Trimmed string.
*/
/**
 * @copyright  2008, IgorN
 * @author     IgorN (progi2007@gmail.com)
 */
function truncate($text, $length = 150, $ending = '&hellip;', $considerHtml = false, $exact = false) {
	if ($considerHtml) {
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		
		foreach ($lines as $line_matchings) {
			if (!empty($line_matchings[1])) {
				if (preg_match('/^<(s*.+?/s*|s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(s.+?)?)>$/is', $line_matchings[1])) {
				} else if (preg_match('/^<s*/([^s]+?)s*>$/s', $line_matchings[1], $tag_matchings)) {
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
						unset($open_tags[$pos]);
					}
				} else if (preg_match('/^<s*([^s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				$truncate .= $line_matchings[1];
			}
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length > $length) {
				$left = $length - $total_length;
				$entities_length = 0;
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			if($total_length >= $length) {
				break;
			}
		}
	} else {
		if (strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = substr($text, 0, $length - strlen($ending));
		}
	}
	
	if (!$exact) {
		$spacepos = strrpos($truncate, ' ');
		if (isset($spacepos)) {
			$truncate = substr($truncate, 0, $spacepos);
		}
	}
	$truncate .= $ending;
	
	if($considerHtml) {
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}
	}
	
	return $truncate;   
}

?>