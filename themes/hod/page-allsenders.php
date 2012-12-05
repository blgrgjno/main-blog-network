<?php
/**
 * Template Name: Alle innsendere
 *
 * NHOP Listing av alle innsendere
 */
?>
<?php

	function nhop_page_header() {
?>
		<div id="c_page_header" class="page_header_page">
			<div class="page_header">
				<?php exit_logo(); ?>
				<div class="page_intro">
					<?php thematic_postheader(); ?>
					<div class="entry-content">
						<?php the_content(); ?>

					</div>
				</div>
<?php
					// Filtering
					
					$firstchar = get_query_var('firstchar') ? get_query_var('firstchar') : $_GET['firstchar'];
					
					$showPrivate = true;
					$showOrg = true;
					if ($_POST['filter']) {
						$showPrivate = $_POST['showPrivate'];
						$showOrg = $_POST['showOrg'];
					}
					$permalink = get_permalink();
?>
				<div class="entry-content">
					<form method="post" action="." class="allsenders-filters">
						<div class="charfilter">
							<p>
							<a href='<?php echo $permalink; ?>' <?php if (!isset($firstchar)) echo "class='active'"; ?> >Alle</a>
<?php
							for ($i=65; $i<=90; $i++) {
								$class = (strtoupper($firstchar) == chr($i)) ? "class='active'" : "";
								echo "<a href='".$permalink."forbokstav/".strtolower(chr($i))."/' ".$class.">".chr($i)."</a> ";
							}
?>
							<a href='<?php echo $permalink; ?>forbokstav/æ' <?php if ($firstchar == "æ") echo "class='active'"; ?> >Æ</a>
							<a href='<?php echo $permalink; ?>forbokstav/ø' <?php if ($firstchar == "ø") echo "class='active'"; ?> >Ø</a>
							<a href='<?php echo $permalink; ?>forbokstav/å' <?php if ($firstchar == "å") echo "class='active'"; ?> >Å</a>
							</p>
						</div>
						<div class="typefilter">
							<p>
							Vis:
							<input type="checkbox" name="showPrivate" id="showPrivate" <?php if ($showPrivate) echo "checked='checked'"; ?> />
							<label for="showPrivate">Privatpersoner</label>
							<input type="checkbox" name="showOrg" id="showOrg" <?php if ($showOrg) echo "checked='checked'"; ?> />
							<label for="showOrg">Virksomheter og organisasjoner</label>
							<input type="submit" name="filter" value="Filtrer" />
							</p>
						</div>
					</form>
				</div>
			</div>
		</div>
<?php
	}
	add_action('thematic_belowheader','nhop_page_header');
	
    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();
?>
	<div id="container" class="fullwidth">
		<div id="content">

            <?php
        
            // calling the widget area 'page-top'
            get_sidebar('page-top');

            ?>

			<?php wp_list_comments('callback=mytheme_comment'); ?>
            
			<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class() ?>">
            
				<div class="entry-content">
<?php
					// Author table
					
					$qs  = "SELECT DISTINCT wpp.post_author, wpu.display_name, COUNT(wpp.ID) AS count ";
					$qs .= "FROM $wpdb->posts as wpp, $wpdb->users as wpu WHERE ";
					$qs .= "post_type = 'post' AND " . get_private_posts_cap_sql( 'post' ) . " ";
					$qs .= "AND wpu.ID = wpp.post_author ";
					$qs .= "GROUP BY wpp.post_author ";
					
					$author_count = array();
					foreach ( (array) $wpdb->get_results($qs) as $row ) {
						$display_name = $row->display_name;
						
						// Messy, but necessary
						$qs  = "SELECT meta_value ";
						$qs .= "FROM $wpdb->usermeta WHERE ";
						$qs .= "meta_key = 'display_name' ";
						$qs .= "AND user_id = ".$row->post_author;
						$alternate = $wpdb->get_var($qs);
						if ($alternate) {
							$display_name = $alternate;
						}
						$author_count[$display_name] = array('id' => $row->post_author, 'count' => $row->count);
					}
?>
					<table class="fullWidthTable allSendersTable">
						<thead>
							<tr>
								<th class="firstCol">Innsender</th>
								<th class="lastCol">Beskrivelse</th>
							</tr>
						</thead>
						<tbody>
<?php
					// Filtering
					
					$showPrivate = true;
					$showOrg = true;
					if ($_POST['filter']) {
						$showPrivate = $_POST['showPrivate'];
						$showOrg = $_POST['showOrg'];
					}
					
					$firstchar = get_query_var('firstchar') ? get_query_var('firstchar') : $_GET['firstchar'];
					
					setlocale(LC_ALL, 'no_NO');
					uksort($author_count, "strnatcasecmp");
					
					foreach ( (array) $author_count as $display_name => $data ) {
						if ($data['count'] > 0) {
							$user_type = get_user_meta($data['id'], 'user_type', true);
							$user_info = get_userdata($data['id']);
							
							if (($showPrivate && $user_type != 'virksomhet') || ($showOrg && $user_type == 'virksomhet')) {
								if (!isset($firstchar) || stripos($display_name, $firstchar) === 0) {
?>
								<tr>
									<td class="firstCol"><a href="<?php echo get_author_posts_url($user_id, $user_info->user_nicename) ?>"><?php echo $user_info->display_name; ?></a></td>
									<td class="lastCol"><?php echo truncate($user_info->description, 100, ' (&hellip;)', false, false); ?></td>
								</tr>
<?php
								}
							}
						}
					}
?>
						</tbody>
					</table>
<?php
                    wp_link_pages("\t\t\t\t\t<div class='page-link'>".__('Pages: ', 'thematic'), "</div>\n", 'number');
                    
                    edit_post_link(__('Edit', 'thematic'),'<span class="edit-link">','</span>') ?>

				</div>
                
			</div><!-- .post -->

        <?php
        
        // calling the widget area 'page-bottom'
        get_sidebar('page-bottom');
        
        ?>

		</div><!-- #content -->
	</div><!-- #container -->

<?php 

    // action hook for placing content below #container
    thematic_belowcontainer();

    // calling footer.php
    get_footer();

?>