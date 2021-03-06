<?php

// Add "new window" class
add_filter('tiny_mce_before_init', 'add_lightbox_classes');

function add_lightbox_classes($initArray) {
	$initArray['theme_advanced_styles'] = "Nytt vindu=newwin";
	return $initArray;
}

function get_comments_bubble() {
	global $post;
	$comments = get_comments('post_id='.$post->ID);
	$postcommentnumber = 0;
	foreach($comments as $comment) {
		$postcommentnumber += ($comment->comment_type == "") ? 1 : 0;
	}
	$statement_meta = get_statement_meta($post->ID);
	
	$postcomments = '<span class="comment_bubble"><a href="' . $statement_meta->statement_url . '#comments" title="' . __('Les kommentarer til ', 'thematic') . the_title_attribute('echo=0') . '">'.$postcommentnumber . __('', 'thematic') . '</a></span>';
	// Do not display if no comments
	if ($postcommentnumber==0) {$postcomments = '';}
	return $postcomments;
}

// Fix permalink in post RSS feed
function nhop_the_permalink_rss($content) {
	global $post;
	$statement_meta = get_statement_meta($post->ID);
	return $statement_meta->statement_url;
}
add_filter('the_permalink_rss', 'nhop_the_permalink_rss');

// Fix permalink in comments RSS feed
function nhop_permalink_comments_rss($content) {
	global $comment;
	$statement_meta = get_statement_meta($comment->comment_post_ID);
	return $statement_meta->statement_url . '#comment-' . $comment->comment_ID;
}
add_filter('get_comment_link', 'nhop_permalink_comments_rss');

function my_postheader() {
    global $id, $post, $authordata;
	
	$nhop = get_theme_options();
	$show = get_query_var('show') ? get_query_var('show') : $_GET['show'];
	
    if (is_single() || is_page()) {
        $posttitle = get_the_title();
    } elseif (is_404()) {    
        $posttitle = __('Ikke funnet', 'thematic');
    } else {
		$statement_meta = get_statement_meta($post->ID);
        $posttitle = '<a href="';
		if ($post->post_type == "post") {
			$posttitle .= $statement_meta->statement_url;
		}
		else {
			$posttitle .= get_permalink();
		}
        $posttitle .= '" title="';
        $posttitle .= __('', 'thematic') . the_title_attribute('echo=0');
        $posttitle .= '" rel="bookmark">';
        $posttitle .= get_the_title();
        $posttitle .= "</a>\n";
    }
	$posttitle = apply_filters('thematic_postheader_posttitle',$posttitle); 
    
    // Display comments link
	$postcomments = '';
    if (comments_open()) {
        $postcomments = get_comments_bubble();
    } else {
        $postcomments = '';
    }
    $postcomments = apply_filters('thematic_postfooter_postcomments', $postcomments); 
	
	// ------------------
	
	$postmeta  = "";
	$postmeta .= $meta_author;
	$postmeta .= '<span class="entry-date"> ';
	$postmeta .= get_the_time(thematic_time_display());
	$postmeta .= '</span>';
    $postmeta = apply_filters('thematic_postheader_postmeta',$postmeta); 
	
	if ($post->post_type != "topic" && $post->post_type != 'page' && !is_404()) {
		$titlecomments = $postcomments;
	}
	
	// Decide header type
	if(is_search()){
		$htype = "h3";
	} else if ($post->post_type == 'page' || is_404()) {
        $htype = "h1";
    } else if (!is_single() && $post->post_type == "post") {
		$htype = "h3";
	} else if (!is_single()){
		$htype = "h2";
	} else if (is_single() && $show == $nhop['slug_statement']) {
		$htype = "h2";
	} else if (is_single() && $show == $nhop['slug_statements']) {
        $htype = "h1";
	} else {
        $htype = "h1";
    }
	
	// Render header
	$is_statements = (strpos($_SERVER['REQUEST_URI'], '/'.get_theme_option('slug_statements').'/') !== false);
    if(!is_single() && $post->post_type == "post" && !is_404()) {
		if (!is_author()) echo getAuthorMeta(true);
		if (get_theme_option('version') != 'minimal' && !is_category() && $post->post_type != 'topic' && !is_404() && !$is_statements) {
			echo '<div class="meta_category">'.get_theme_option('topic_name').': <a href="'.$statement_meta->topic_url.'">'.$statement_meta->topic_title.'</a></div>';
		}
		echo '<'.$htype.' class="entry-title">' . $titlecomments . $posttitle . '</'.$htype.'>';
	} else if(!is_single() && !is_search() && $post->post_type != 'page' && !is_404()) {
		echo getAuthorMeta(true);
        echo '<'.$htype.' class="entry-title">' . $titlecomments . $posttitle . '</'.$htype.'>';
	} else {
        echo '<'.$htype.' class="entry-title">' . $titlecomments . $posttitle . '</'.$htype.'>';
    }
}

add_filter ('thematic_postheader', 'my_postheader');

function my_postfooter() {
	global $post, $authordata;
    if (is_single() || is_page()) {
        $postfooter = '';
    } elseif (is_404()) {    
        $postfooter = '';
    } else {
		$statement_meta = get_statement_meta($post->ID);
		
		if ($post->post_type == "post") {
			$readfull  = '<span class="meta_readmore"><a href="'.$statement_meta->statement_url.'">'.str_replace("{author_name}", $authordata->display_name, get_theme_option('read_post')).'</a></span>';
		}
		else {
			$readfull  = '<span class="meta_readmore"><a href="'.get_permalink().'">'.str_replace("{topic}", get_the_title(), get_theme_option('read_topic')).'</a></span>';
		}
		
        $postfooter .= $readfull;
				
		if (comments_open()) {
			$comments = get_comments('post_id='.$post->ID);
			$postcommentnumber = 0;
			foreach($comments as $comment) {
				$postcommentnumber += ($comment->comment_type == "") ? 1 : 0;
			}
			if (current_user_can('edit_posts')) {
				$postfooter .= ' | <span class="edit">' . thematic_postheader_posteditlink() . '</span>';
			}
    	} 
    }
	echo $postfooter;
}
add_filter ('thematic_postfooter', 'my_postfooter');

function nhop_the_content($content) {
	global $post, $email_view;
	
	$questions_content = "";
	$date_content = "";
	if (is_single() || $email_view) {
		$answer_meta = get_post_custom($post->ID);
		
		if ($answer_meta["TDOMF Form #1 Custom Field #_2"] ||
			$answer_meta["TDOMF Form #1 Custom Field #_3"] ||
			$answer_meta["TDOMF Form #1 Custom Field #_4"]) {
			
			$questions = $answer_meta["topic_questions"];
			$parent_topic = $answer_meta["TDOMF Form #1 Custom Field #_1"][0];
			$question_answers = array();
			$question_answers[0] = $answer_meta["TDOMF Form #1 Custom Field #_2"];
			$question_answers[1] = $answer_meta["TDOMF Form #1 Custom Field #_3"];
			$question_answers[2] = $answer_meta["TDOMF Form #1 Custom Field #_4"];
			
			$topic_meta = get_post_custom($parent_topic);
			$questions = $topic_meta['topic_questions'];
			
			if ($question_answers[$i][0]) {
				$questions_content .= "<div class='question_answers'>";
				for ($i=0; $i<3; $i++) {
					if ($question_answers[$i][0]) {
						$questions_content .= "<h4>".$questions[$i]."</h4><div>".$question_answers[$i][0]."</div>";
					}
				}
				$questions_content .= "</div>";
			}
			
			$date_content .= "<p class='entry-date'>\n";
			$date_content .= get_the_date()." ".get_the_time();
			$date_content .="</p>\n";
		}
	}
    return $content.$questions_content.$date_content;

}
add_filter('the_content', 'nhop_the_content');

// Truncate content
function nhop_content($content) {
	global $email_view;
	if (is_single() || is_page() || $email_view)
		return $content;
	return nhop_trim_excerpt($content);
}
add_filter('the_content', 'nhop_content');


function nhop_the_excerpt($content) {
	global $post;
	if ($post->post_type == 'topic') {
		$sit = get_post_custom_values('topic_summary_sit', $post->ID);
		return nhop_trim_excerpt($content);
	}
	return $content;
}
add_filter('get_the_excerpt', 'nhop_the_excerpt');


?>