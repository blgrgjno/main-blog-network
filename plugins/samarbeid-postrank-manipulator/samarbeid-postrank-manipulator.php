<?php

/*
Plugin Name: Samarbeid for Arbeid PostRank Manipulator
Plugin URI: http://www.bouvet.no
Description: Manipulate the entries shown.
Version: 0.2
Author: Bouvet ASA
Author URI: http://www.bouvet.no
*/




/**
 * Register admin menu
 */
add_action('admin_menu', 'bvt_sfa_feed_gui_admin_menu');
 
function bvt_sfa_feed_gui_admin_menu () {
	add_menu_page(
        'PostRank GUI',
        'PostRank GUI',
        'manage_categories',
        'bvt_sfa_feed_gui_admin',
        'bvt_sfa_feed_gui_admin_screen'
    );
}

function bvt_sfa_feed_gui_admin_screen() {

	$action = !empty($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : false;
	
	echo '<h2>PostRank feed admin</h2>';	
	$happening = '';
	
	if($action == 'edit' || $action=='gui-create')
	{
		$happening = bvt_sfa_feed_gui_edit($id);
	}  
	else if($action == 'create'){
	  $happening = bvt_sfa_feed_create();
	}
	else if($action == 'update')
		$happening = bvt_sfa_feed_gui_save($id);
	else
	{
		if($action == 'add')
			$happening = bvt_sfa_feed_gui_display_in_feed($id, 1);
		else if($action == 'remove')
			$happening = bvt_sfa_feed_gui_display_in_feed($id, 0);
		
		bvt_sfa_feed_gui_display_list();
	}
	if($happening != '')
	{
		?>
			<div class="error">
        <p><?php echo $happening?></p>
			</div>			
		<?php
		bvt_sfa_feed_gui_edit();
	}
}

function bvt_sfa_feed_gui_save($id=false)
{
	global $wpdb;
	if($id!=false && $_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if ( get_magic_quotes_gpc() ) {
			$_POST = array_map( 'stripslashes_deep', $_POST );
		} 

		$content = !empty($_POST['content']) ? ($_POST['content']) : '';
		$title = !empty($_POST['title']) ? ($_POST['title']) : '';
		$author = !empty($_POST['author']) ? ($_POST['author']) : '';
		$link = !empty($_POST['link']) ? ($_POST['link']) : '';
		
		$sql = $wpdb->prepare("
			UPDATE
				".$wpdb->prefix . "samarbeid_feed_data
			SET
				post_title = %s,
				post_link = %s,
				post_author = %s,
				post_content = %s
			WHERE
				post_id = %d",
			$title,
			$link,
			$author,
			$content,
			$id
		);
 	
		if($wpdb->query($sql)==0){		  
			echo "<p>&quot;$title &quot; was updated successfully.";
		}
		else{		
			echo "<p>An error occured ";
		}
		
		?>
		
		<br/>Go back to the <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=bvt_sfa_feed_gui_admin">feed admin page</a>.
		<?php
	}
}

function bvt_sfa_feed_create()
{
	global $wpdb;
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		if ( get_magic_quotes_gpc() ) {
			$_POST = array_map( 'stripslashes_deep', $_POST );
		} 

		$content = !empty($_POST['content']) ? ($_POST['content']) : '';
		$topic = !empty($_POST['topic']) ? ($_POST['topic']) : '';
		$title = !empty($_POST['title']) ? ($_POST['title']) : '';
		$author = !empty($_POST['author']) ? ($_POST['author']) : '';
		$link = !empty($_POST['author']) ? ($_POST['link']) : '';
		
		$str_error = "";
	
		if($topic == "-1"){
		  $str_error = $str_error."<li>Topic is mandatory </li>";		  
		}
		if($title == ""){
		  $str_error = $str_error."<li>Title is mandatory </li>";
		}
		
		if($str_error == ""){		
      $table_name = $wpdb->prefix . "samarbeid_feed_data";
      $stmt = $wpdb->prepare("
      INSERT INTO ".$table_name." (
          category_id,
          post_link,
          post_title,
          post_selected_date,
          post_publish_date,
          post_author,
          post_content,
          post_publish_status,
          post_manually_created          
      )
      VALUES (
          %d,
          %s,
          %s,
          %s,
          %s,
          %s,        
          %s,
          0,
          1
      )",
      $topic,
      $link,
      $title,    
      date("Y-m-d H:i:s", mktime()),
      date("Y-m-d H:i:s", mktime()),
      $author,
      $content 
      );
      
      
      $result = $wpdb->query($stmt);
      if($result != false){
        echo "<p>&quot;$title &quot; was created successfully.";
        echo '<br/>Go back to the <a href="'.$_SERVER['PHP_SELF'].'?page=bvt_sfa_feed_gui_admin">feed admin page</a> to enable it';
        echo $result;
      }
      else{        
        echo "An error occured.";        
      }      
    }
    else{           
      return "One or more errors occurred:<ol>".$str_error."</ol>";    
    }
  }
}


function bvt_sfa_feed_gui_edit($id=false)
{
	global $wpdb;
	$action='';
	
	$action_label = "";
	$action_title = "";
	
	// check if $id exists and get quote
	if($id!=false)
	{
    $action_title = "Updated feed element";
    $action_name = "update";    
		$data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "samarbeid_feed_data WHERE post_id='" . $wpdb->escape($id) . "' LIMIT 1");
		if ( empty($data) )
		{
			return "A feed element with that ID couldn't be found";
		}
		else
		{
			$data = $data[0];
		}
	}
	else{
	  $action_title = "Create new feed element";
    $action_name = "create";
    $data->post_title = get_input($_REQUEST['title']);
    $data->post_link = get_input($_REQUEST['link']);
    $data->post_author = get_input($_REQUEST['author']);
    $data->post_content = get_input($_REQUEST['content']);
	}
	
	$allowed_tags_head = "<abbr><acronym><del><q>";

    $allowed_tags_body = "<a><abbr><acronym><br><del><dl><li><ol><p><q>" . "<table><td><tbody><th><thead><tfoot><tr><ul>";
	
	?>
	<style type="text/css">
		form.admin {
			-moz-border-radius-bottomleft:5px;
			-moz-border-radius-bottomright:5px;
			-moz-border-radius-topleft:5px;
			-moz-border-radius-topright:5px;
			background:#FFFFFF none repeat scroll 0 0;
			border:1px solid #CCDDDD;
			margin:1em 0 2em;
			padding:1em 2em 0;
			width:55em;
		}
		
		div.row {
			padding-bottom:1em;
			overflow:hidden;
		}
		
		div.label {
			float:left;
			padding-left:1em;
			text-align:right;
			width:14em;
		}
		
		div.value {
			float:right;
			padding-right:1em;
			width:38em;
		}
		
		div.value textarea {
			height:30em;
			width:35em;
		}
		
		div.value input {
			width:38em;
		}
		
		div.title {
			border-bottom:1px solid #CCDDDD;
			clear:both;
			margin-bottom:0;
		}
		
		div.title h3 {
			margin-bottom:0;
		}
		
		div.submit {
			text-align:center;
		}
		
		#entry_preview {
			background: #fff;
			-moz-border-radius:5px 5px 5px 5px;
			padding:0 1em 0;
			border:1px solid #CCDDDD;
			width:55em;
			margin-bottom:1em;
		}
	</style>
	

	<div class="updated">
		<p>Some of the html-tags are stripped out on the front page. </p><p>Allowed tags for title: <?php echo htmlentities($allowed_tags_head)?></p><p>Allowed tags for content: <?php echo htmlentities($allowed_tags_body)?></p>
	</div>
	
	<form class="admin" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=bvt_sfa_feed_gui_admin">
		<input type="hidden" name="action" value="<?php echo $action_name?>"/>
		<input type="hidden" name="id" value="<?php if ( !empty($data) ) echo htmlentities($data->post_id); ?>"/>

		<div class="row">
			<div class="title"><h3><?php echo $action_title?></h3></div>
		</div>

		<div class="row">
			<div class="label">Title (Mandatory)</div>
			<div class="value"><input type="text" name="title" class="input" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->post_title); ?>"/></div>
		</div>
    <div class="row">
			<div class="label">Link</div>
			<div class="value"><input type="text" name="link" class="input" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->post_link); ?>"/></div>
		</div>
		
		<?php
		  if($id==false){
		?>
    <div class="row">
      <div class="label">Topic (Mandatory)</div>
      <div class="value">
      <?php 
			    wp_dropdown_categories(array(
			      "show_option_none" => 'Select topic',
			      "exclude" => 1,  /* 1 is "Uncategorized" */
			      "hide_empty" => false,
			      "hierarchical" => 0,
			      "name" => "topic",
			      "child_of" => get_cat_id('Tema'),
			      "selected" => $topic
			));      
		?>
		</div>
		</div>
    <?php }?>
		<div class="row">
			<div class="label">Author</div>
			<div class="value"><input type="text" name="author" class="input" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->post_author); ?>"/></div>
		</div>
		
		<div class="row">
			<div class="label">Content</div>
			<div class="value"><textarea name="content" id="form_content"><?php if ( !empty($data) ) echo htmlspecialchars($data->post_content); ?></textarea></div>
		</div>
		<div class="submit">
			<input type="submit" value="<?php echo $action_name?>"/>
		</div>
	</form>
	<h3>Content preview</h3><div id="entry_preview"><?php if ( !empty($data) ) echo $data->post_content; ?></div>
	
	<script type="text/javascript">
		/*<![CDATA[*/
		if(typeof jQuery!="undefined")
		{
			jQuery("#form_content").keyup(function(event){
				jQuery("#entry_preview").html(jQuery(this).val());
			});

		}
		/*]]>*/
	</script>
	<?php
}



// Display all items
function bvt_sfa_feed_gui_display_list() {

	global $wpdb;
	
	$plugin_dependency = "bvt_sfa_feed_admin_menu";
	
	if(!function_exists($plugin_dependency))
	{
		echo "This plugin is dependant on the PostRank feeds plugin";
		exit;
	}
	
	$entries_per_page = 50;
	if ( get_magic_quotes_gpc() ) {
		$_GET = array_map( 'stripslashes_deep', $_GET );
		$_POST = array_map( 'stripslashes_deep', $_POST );
	}

	$offset = (isset($_GET['offset']) && !empty($_GET['offset'])) ? $_GET['offset'] : '0';
	$offset = abs((int)$offset);
	
	$topic = (isset($_GET['topic']) && !empty($_GET['topic'])) ? $_GET['topic'] : '';

	$where = '';
	if(is_numeric($topic) && $topic!=-1)
		$where = " WHERE category_id = $topic ";
	$inputs = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "samarbeid_feed_data ".$where." ORDER BY post_publish_date DESC LIMIT $offset, $entries_per_page");	
	?>
	  <a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=bvt_sfa_feed_gui_admin&action=gui-create">New</a>
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<input type="hidden" name="page" value="bvt_sfa_feed_gui_admin"/>
		<label for="topic">Filter on topic:</label>
		<?php 
			wp_dropdown_categories(array(
			  "show_option_none" => ' ',
			  "exclude" => 1,  /* 1 is "Uncategorized" */
			  "hide_empty" => false,
			  "hierarchical" => 0,
			  "name" => "topic",
			  "child_of" => get_cat_id('Tema'),
			  "selected" => $topic
			));
		?>
		<input type="submit" value="ok"/>
		</form>
		<table class="widefat fixed" width="100%" cellpadding="3" cellspacing="3">
		        <thead>
			    <tr>
				<th><?php _e('Topic') ?></th>
				<th><?php _e('Subject') ?></th>
				<th><?php _e('Name') ?></th>
				<th><?php _e('Date') ?></th>
				<th><?php _e('Change status') ?></th>
				<th><?php _e('View/edit') ?></th>
				<th><?php _e('Source') ?></th>
			    </tr>
		        </thead>
		<?php
		
		$class = '';
		foreach ( $inputs as $input )
		{
			//$class = ($class == 'alternate') ? '' : 'alternate';
			$class = ($input->post_publish_status) ? 'active' : 'inactive';
			//a bit crude
			$source = "Google Reader";
			if (substr($input->post_link, 0, 4) == "md5:") {
			   $source = 'Webform';
			} 
			else if($input->post_manually_created == 1){
			    $source = 'Editorial';
			}			
			?>
			<tr class="<?php echo $class; ?>">
				<td><?php echo get_cat_name($input->category_id); ?></td>
				<td><?php echo $input->post_title; ?></td>
				<td><?php echo $input->post_author; ?></td>
				<td><?php echo $input->post_publish_date; ?></td>
				<?php if($input->post_publish_status == true) { ?>
				<td><a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=bvt_sfa_feed_gui_admin&amp;topic=<?php echo $topic ?>&amp;offset=<?php echo $offset?>&amp;action=remove&amp;id=<?php echo $input->post_id;?>"><?php echo 'Deactivate'; ?></a></td>
				<?php } else { ?>
				<td><a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=bvt_sfa_feed_gui_admin&amp;topic=<?php echo $topic ?>&amp;offset=<?php echo $offset?>&amp;action=add&amp;id=<?php echo $input->post_id;?>"><?php echo 'Activate'; ?></a></td>
				<?php } ?>
				<td><a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=bvt_sfa_feed_gui_admin&amp;action=edit&amp;id=<?php echo $input->post_id;?>"><?php echo 'View/edit'; ?></a></td>
				<td><?php echo $source?></td>
			</tr>
			<?php
		}
		?>
		</table>
    <p>
		<?php if($offset>$entries_per_page-1) { ?>
			<a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=bvt_sfa_feed_gui_admin&amp;topic=<?php echo $topic ?>&amp;offset=<?php echo $offset-$entries_per_page ?>">previous</a>&nbsp;&nbsp;
		<?php } ?>
		<a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=bvt_sfa_feed_gui_admin&amp;topic=<?php echo $topic ?>&amp;offset=<?php echo $offset+$entries_per_page ?>">next</a>
		</p>

		<?php
}


function bvt_sfa_feed_gui_display_in_feed($id,$display)
{
	global $wpdb;
	
	// check if $id is numeric
	if($id!=false && is_numeric($id))
	{
		if(!$wpdb->query("UPDATE ".$wpdb->prefix . "samarbeid_feed_data SET post_publish_status = $display WHERE post_id = $id"))
			return "Error occured ";
	}
	else
		return "ID is not correct";
}

function get_input($variable)
{
	return !empty($variable)||trim($variable)!='' ? trim($variable) : '';
}

function postrank_gui_resources() {    
    $src = WP_PLUGIN_URL . "/" .
          str_replace(basename(__FILE__), "", plugin_basename(__FILE__)) .
          "gui.css";    
    echo '<link rel="stylesheet" type="text/css" href="' . $src . '" />';
}
add_action('admin_head', 'postrank_gui_resources');

?>