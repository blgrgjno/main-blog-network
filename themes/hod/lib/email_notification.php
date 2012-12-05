<?php

function send_notifications($postID) {
	global $email_view, $post;
	
	if ($parent_id = wp_is_post_revision($postID)) {
		$postID = $parent_id;
	}
	
	$post = get_post($postID, OBJECT);
	
	// Basic author data
	$authorEmail = get_the_author_meta('user_email', $post->post_author);
	$authorName = get_the_author_meta('first_name', $post->post_author).' '. get_the_author_meta('last_name', $post->post_author);
	$authorType = get_the_author_meta('user_type');
	
	// Topic meta data
	$statement_meta = get_statement_meta($post->ID);
	
	// Answer data
	$content = $post->post_content;
	$email_view = true;
	$content = apply_filters('the_content', $content);
	$email_view = false;
	$content = str_replace(']]>', ']]&gt;', $content);
	
	// Load email template
	$template_name = ($authorType == "virskomhet") ? 'email_notification_virksomhet.html' : 'email_notification_privatperson.html';
	$filename = locate_template(array($template_name), false);
	$handle = fopen($filename, "r");
	$message = fread($handle, filesize($filename));
	fclose($handle);
	
	// Fill template
	$search  = array(
		"{post_title}",
		"{post_content}",
		"{post_uri}",
		"{post_date}",
		"{topic_title}",
		"{topic_uri}",
		"{author_name}",
		"{author_email}",
		"{author_address}",
		"{author_login}",
		"{author_type}",
		"{saksbehandler}",
	);
	$replace = array(
		$post->post_title,
		$content,
		$statement_meta->statement_url,
		$post->post_date,
		$statement_meta->topic_title,
		$statement_meta->topic_url,
		$authorName,
		$authorEmail,
		get_the_author_meta('adresse_post', $post->post_author),
		get_the_author_meta('user_login', $post->post_author),
		($authorType == "virskomhet") ? 'Virksomhet' : 'Privatperson',
		get_the_author_meta('saksbehandler', $post->post_author),
	);
	$message = str_replace($search, $replace, $message);
	
	// recipient
	$to = $authorEmail;
	
	// subject
	$subject = 'Ditt høringssvar, '.get_the_title().', er mottatt';

	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

	// Additional headers
	$headers .= 'From: Fremtidens helsetjeneste <'.get_theme_option('contact_email').'>' . "\r\n";
	$headers .= 'Bcc: ' . get_theme_option('bcc') . "\r\n";
	
	// Mail it
	mail($to, $subject, $message, $headers);
	
	// Reset the global $the_post as this query will have stomped on it
	wp_reset_postdata();
}

add_action('publish_post', 'send_notifications');

?>