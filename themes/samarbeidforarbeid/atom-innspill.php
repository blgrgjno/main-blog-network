<?php
/**
 * @package sfa
 * @subpackage sfa-theme
 */
/*
Template Name: atom-gi-innspill
*/

$arr_topic = array("Frafall i videregående opplæring", "Sykefravær", "Bærekraftig økonomi", "Næringsutvikling");

global $wpdb;
header('Content-Type: ' . feed_content_type('atom') . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '" ?' . '>';

$topic = '';

if(isset($_GET['topic']))
{
	$topic = $_GET['topic'];
	
	switch($topic)
	{
		case 'frafall':
			$topic = $arr_topic[0];
			break;
		case 'fravaer':
			$topic = $arr_topic[1];
			break;
		case 'okonomi':
			$topic = $arr_topic[2];
			break;
		case 'naeringsutvikling':
			$topic = $arr_topic[3];
			break;
		default :
			$topic = $arr_topic[3]; // Added to prevent MySQL injection
			break;
		
	}
	$where = " WHERE topic='$topic' ";
}
/*
else
	$wpdb->show_errors();
*/

$sql = "SELECT *, INET_NTOA(Ip) AS Ip FROM ".$wpdb->prefix."input_article ".$where." ORDER BY date desc LIMIT 0,20";
$wpdb->prepare( $sql ); // Data sanitisation
$feedbacks = $wpdb->get_results($sql);

$feed_updated;

if(!empty($feedbacks[0]))
	$feed_updated = mysql2date('Y-m-d\TH:i:s\Z', $feedbacks[0]->date, false);
?>

<feed
	xmlns="http://www.w3.org/2005/Atom"
	xml:lang="<?php echo get_option('rss_language'); ?>"
	xmlns:thr="http://purl.org/syndication/thread/1.0"
	<?php do_action('atom_ns'); do_action('atom_comments_ns'); ?>
>
	<title type="text">Innspill om artikler og blogginnlegg <?php echo ($topic!='' ? ' om emnet '.strtolower($topic) : '') ?></title>
	<subtitle type="text"></subtitle>
	<updated><?php echo $feed_updated ?></updated>
	<?php the_generator( 'atom' ); ?>
	<link rel="alternate" type="<?php bloginfo_rss('html_type'); ?>" href="<?php bloginfo_rss('home'); ?>" />
	<id><?php bloginfo_rss('atom_url'); ?>custom</id>

<?php
if ( !empty($feedbacks) )
{
	foreach ( $feedbacks as $feedback )
	{
?>
	<entry>
		<id>tag:samarbeidforarbeid.no,<?php echo mysql2date('Y-m-d', $feedback->date, false) ?>:<?php echo sha1($feedback->id.$feedback->alias)?></id>
		<title type="html"><![CDATA[<?php echo $feedback->subject;?>]]></title>
		<author>
			<name type="html"><![CDATA[<?php echo $feedback->alias ?>]]></name>
		</author>
		<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', $feedback->date, false); ?></updated>
		<published><?php echo mysql2date('Y-m-d\TH:i:s\Z', $feedback->date, false); ?></published>
		<content type="html"><![CDATA[Emne: <?php echo $feedback->topic ?><br/><a href="<?php echo $feedback->url ?>" target="_blank"><?php echo $feedback->url ?></a>]]></content>
	</entry>

<?php 
	}
}
else
{
?>

	<entry>
		<id>tag:samarbeidforarbeid.no</id>
		<title>empty</title>
		<author>
			<name></name>
		</author>
	</entry>

<?php
}
?>
</feed>
