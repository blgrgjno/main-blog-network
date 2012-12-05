<?php
	// Get the Reports
	include_once("ReportPost.class.php");
	
	global $wpdb;
	$wprp = new ReportPost($wpdb);
	
	// Handle Archive & DELETE
	if($_SERVER['REQUEST_METHOD']=='POST')
	{
	  	//echo $current_user->ID;
		
		if ( get_magic_quotes_gpc() ) {
			$_POST      = array_map( 'stripslashes_deep', $_POST );
			$_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );
		}
		
		$selected = $_POST['reportID'];
		
		if($selected && is_array($selected) && count($selected) > 0)
		{
			// If Archive
			if(isset($_POST['archiveit']))
			{
				global $current_user;
				get_currentuserinfo();
				
				$archive_c = $_POST['archive_c'];
				
				foreach($selected as $archive)
				{
					if(!$wprp->archive($archive, $current_user->ID, $archive_c))
					{
						echo "ERROR: ".$wprp->last_error;
						break; // EXIT LOOP
					}
				}
			}
			
			// DELETE
			if(isset($_POST['deleteit']))
			{
				foreach($selected as $archive)
				{
					if(!$wprp->delete($archive))
					{
						echo "ERROR: ".$wprp->last_error;
						break; // EXIT LOOP
					}
				}
			}
		}// IF selected
	}
	
	
	
	// Calculate Paggination
	$p = (int) isset($_GET['p']) && is_numeric($_GET['p'])? $_GET['p'] : 1;
	$limit= 20;
	
	$offset = ($limit * ($p - 1));
	
	// Search Based on Paggination
	$results = $wprp->findReports('ORDER BY id DESC',$limit, "WHERE status=1", $offset);
	
	// Calculate Pages
	$total_found = $wprp->totalRows;
	
	$pages = ceil($total_found / $limit);
?>
<div class="wrap"> 
	<h2><?php _e('New Reports', 'wp-reportpost'); ?></h2>
	
    <form action="" method="post">
    <div class="wprp-info">
    	<div class="wprp-buttons">
        	selected: <input type="button" value="Archive it" name="expandarchive" class="button-secondary delete" onclick="jQuery('#wprp-archive').slideToggle('slow');" /> or <input type="button" value="Delete it" name="delete-expand" class="button-secondary delete" onclick="jQuery('#delete-confirm').slideToggle('slow');" /> <small>(* will be removed permanently)</small>
        </div>
    	<span>Total Reports: <?php echo $total_found;?></span>
    </div>
    
    <div class="wprp-archive" id="wprp-archive" style="display:none">
        Moderator Comments:<br />
        <textarea name="archive_c" id="archive_c" style="width:60%;" rows="5"></textarea><br />
        <small>* Your User ID and IP will be logged!</small><br />
        <input type="submit" value="Archive it" name="archiveit" class="button-secondary delete" />
    </div>
    
    <div class="wprp-archive" id="delete-confirm" style="display:none">
    	<strong>Once deleted, It will be Permanently removed from database. This Report record canno't be found again</strong><br />
        Confirm Deleting?  <input type="submit" value="Confirm Delete" name="deleteit" class="button-secondary delete" /> 
    </div>
    <?php 
	if($total_found > 0):
	
	?>
	<table class="widefat post fixed" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" /></th>
                <th scope="col">Post Title</th>
                <th scope="col" style="width:80px;"># Reports</th>
			</tr>
		</thead>
        <tfoot>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" /></th>
                <th scope="col">Post Title</th>
                <th scope="col"># Reports</th>
			</tr>
		</tfoot>
		<tbody>
        <?php
		$alt = '';
		foreach($results as $report):
		$alt = ($alt == '') ? ' class = "alt"' : '';
		
		$permalink = get_bloginfo('wpurl')."/wp-admin/post.php?action=edit&post=".$report->postID;
		
		?>
			<tr <?php echo $alt;?>>
            	<th scope="row" class="check-column"><input type="checkbox" name="reportID[]" value="<?php echo $report->id;?>" /></th>
				<td><a href="<?php echo $permalink;?>" title="Edit The Post"><?php echo $report->post_title;?></a></td>
                <td align="center"><a href="<?php echo WP_PLUGIN_URL;?>/wp-reportpost/reports.php?id=<?php echo $report->id;?>&TB_iframe=true&type=reports" title="Report Details" class="thickbox" onclick="return false;"># View Details</a></td>
			</tr>
            <?php endforeach;?>
            
		</tbody>
	</table>
    <?php
	else:
		echo 'No Reports Found!';
	endif;
	?>
    </form>
    <?php
	
	if($pages > 1)
	{
	?>
    <div class="wprp-pages">
    	<ul>
        	<li class="pageinfo">Pages: </li>
            <?php 
			for($i=1; $i <= $pages; $i++): 
				if($i == $p)
				{?>
                <li class="current"><?php echo $i;?></li>
				<?php 
				continue;
				}
			?>
        	<li><a href="<?php echo get_bloginfo('wpurl')."/wp-admin/admin.php?".url_filter($_SERVER['QUERY_STRING'],'p')."&p=".$i;?>"><?php echo $i;?></a></li>
            <?php
			endfor;
			?>
        </ul>
    </div>
    <?php 
	}
	?>
</div>
<?php
function addHeaderCode() {
		echo "HEREME";
}
?>