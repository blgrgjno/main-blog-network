<?php
	global $post;
	$show = get_query_var('show') ? get_query_var('show') : $_GET['show'];
	$statement_id = get_query_var('statement_id') ? get_query_var('statement_id') : $_GET['statement_id'];
	$meta_values = get_post_custom($post->ID);
	$permalink = get_permalink($post->ID);
	$nhop = get_theme_options();
	
	function nhop_single_post_title($content) {
		global $show, $statement_id;
		
		$nhop = get_theme_options();
		
		$title_before = "";
		switch ($show) {
			case $nhop['slug_full']:
				$title_before = $nhop['tab_full'] . " | ";
				break;
			case $nhop['slug_examples']:
				$title_before = $nhop['tab_examples'] . " | ";
				break;
			case $nhop['slug_statements']:
				$title_before = $nhop['tab_statements'] . " | ";
				break;
			case $nhop['slug_statement']:
				$title_before = get_the_title($statement_id) . " | ";
				break;
			case $nhop['slug_write']:
				if ($nhop['enable_statements']) {
					$title_before = $nhop['send_answer'] . " | ";
				}
				break;
			default:
				$title_before = "";
				break;
		}
		
		return $title_before.$content;
	}
	add_filter('single_post_title','nhop_single_post_title');
	
	function nhop_page_header() {
		global $post, $show;
		
		$meta_values = get_post_custom($post->ID);
		$nhop = get_theme_options();
?>
		<div id="c_page_header" class="page_header_topic">
			<div class="page_header">
				<?php exit_logo(); ?>
				<div class="page_intro">
					<h1 class="entry-title"><?php the_title() ?></h1>
				</div>
				<?php
					if (has_post_thumbnail()) {
				?>
						<div class="page_ill">
				<?php
							the_post_thumbnail(null, array('title' => null));
				?>
						</div>
				<?php
					}
				?>
<?php
		if ($show != $nhop['slug_write']) {
			topic_select();
		}
		
		switch ($show) {
			case $nhop['slug_full']:
				$class_full = "active";
				break;
			case $nhop['slug_examples']:
				$class_examples = "active";
				break;
			case $nhop['slug_statement']: case $nhop['slug_statements']:
				$class_statements = "active";
				break;
			default:
				$class_summary = "active";
				break;
		}
?>

<?php
		if ($show != $nhop['slug_write'] || !$nhop['enable_statements']) {
			$permalink = get_permalink($post->ID);
?>
			<div class="topic-tabs-container">
				<ul class="topic-tabs">
				
					<?php if($meta_values['topic_summary_sit'][0]) { ?>
						<li class="<?php echo $class_summary; ?>"><a href="<?php echo $permalink ?>"><?php echo $nhop['tab_summary']; ?></a></li>
					<?php } ?>
					<?php if($meta_values['topic_main_sit'][0]) { ?>
						<li class="<?php echo $class_full; ?>"><a href="<?php echo $permalink; ?><?php echo $nhop['slug_full']; ?>/"><?php echo $nhop['tab_full']; ?></a></li>
					<?php } ?>
					<?php if($meta_values['topic_examples'][0]) { ?>
						<li class="<?php echo $class_examples; ?>"><a href="<?php echo $permalink; ?><?php echo $nhop['slug_examples']; ?>/"><?php echo $nhop['tab_examples']; ?></a></li>
					<?php } ?>
					<li class="<?php echo $class_statements; ?>"><a href="<?php echo $permalink; ?><?php echo $nhop['slug_statements']; ?>/"><?php echo $nhop['tab_statements']; ?></a></li>
				</ul>
			</div>
<?php
		}
?>
			</div>
		</div>
<?php
	}
	add_action('thematic_belowheader','nhop_page_header');

	function writeEntry() {
		global $post, $wp_query;
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();
		$nhop = get_theme_options();
		
		$show = get_query_var('show') ? get_query_var('show') : $_GET['show'];
		if ($show == $nhop['slug_write'] ) {
			return;
		}
		
		if ($post->post_type == "topic") {
			$topic_url = get_permalink($post->ID);
			$topic_title = get_the_title($post->ID);
		}
		else {
			$statement_meta = get_statement_meta($post->ID);
			$topic_url = $statement_meta->topic_url;
			$topic_title = $statement_meta->topic_title;
		}
?>
		<div class="nhop_entry aside main-aside" id="write-entry-aside">
			<div class="sidebox">
				<h3 class="widgettitle"><?php echo $nhop['write_entry_title']; ?></h3>
				<div class="entry-content">
					<p><?php echo str_replace("{topic}", mb_strtolower($topic_title, 'UTF-8'), $nhop['write_entry_text']); ?></p>
<?php
	if ($nhop['enable_statements']) {
?>
					<p class="buttons"><a href="<?php echo $topic_url.$nhop['slug_write']."/"; ?>" class="buttonRound"><span><?php echo $nhop['send_answer']; ?></span></a></p>
<?php
	}
?>
				</div><!-- /entry-content -->
			</div><!-- /sidebox -->
		</div><!-- /write-entry -->
<?php
	}
	add_action('thematic_abovemainasides', 'writeEntry');
	
    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();

	function topic_select() {
		$nhop = get_theme_options();
?>
		<div class="topic_select">
			<a href="/" id="topicSelectToggle" style="display:none;"><?php echo $nhop['topic_menu_label']; ?></a>
		</div>
<?php
	}

	function topic_select_footer() {
		$nhop = get_theme_options();
?>
		<div class="topic_select_menu" id="topicSelectMenu" style="display:none">
<?php
		// Render topic groups and topics
		if (class_exists('Walker_Front_Page')) {
			wp_nav_menu_no_ul(array(
				'menu' => 'temaer',
				'container' => 'div',
				'container_class' => 'front_menu',
				'break_point' => $nhop['header_menu_break_point'],
				'walker' => new Walker_Front_Page
			));
		}
?>
		</div>
<?php
	}
	if ($show != $nhop['slug_write']) {
		add_action('thematic_after', 'topic_select_footer');
	}

	function nhop_internalnav($id, $hidetop=false) {
		global $meta_values;
		$meta_values = get_post_custom($post->ID);
?>
		<div class="internalnav" id="<?php echo $id; ?>">
			<div class="internalnav_parts">
				<?php if ($meta_values['topic_main_sit_header'][0]) { ?>
					<a href="#situasjon"><?php echo $meta_values['topic_main_sit_header'][0]; ?></a> |
				<?php } ?>
				<?php if ($meta_values['topic_main_goal_header'][0]) { ?>
					<a href="#maal"><?php echo $meta_values['topic_main_goal_header'][0]; ?></a> |
				<?php } ?>
				<?php if ($meta_values['topic_main_means_header'][0]) { ?>
					<a href="#virkemidler"><?php echo $meta_values['topic_main_means_header'][0]; ?></a>
				<?php } ?>
			</div>
			<?php if (!$hidetop) { ?>
			<div class="internalnav_top">
				<a href="#">Til toppen</a>
			</div>
			<?php } ?>
		</div>
<?php
	}
	
?>
<div id="container">
	<div id="content">

		<?php
			// calling the widget area 'page-top'
			get_sidebar('page-top');
		?>
<?php
	if ($show == $nhop['slug_full']) {
		if ($meta_values['topic_summary_means'][0]) {
?>
			<?php if ($meta_values['topic_main_sit_header'][0]) { ?>
				<?php nhop_internalnav('situasjon', true); ?>
				
				<div class="<?php thematic_post_class() ?>">
					<h2 class="entry-title"><?php echo $meta_values['topic_main_sit_header'][0]; ?> <?php social_bookmarks(); ?></h2>
					<div class="entry-content">
						<?php echo apply_filters('the_content', $meta_values['topic_main_sit'][0]); ?>
					</div>
				</div>
			<?php } ?>
			
			<?php if ($meta_values['topic_main_goal_header'][0]) { ?>
				<?php nhop_internalnav('maal'); ?>
				
				<div class="<?php thematic_post_class() ?>">
					<h2 class="entry-title"><?php echo $meta_values['topic_main_goal_header'][0]; ?></h2>
					<div class="entry-content">
						<?php echo apply_filters('the_content', $meta_values['topic_main_goal'][0]); ?>
					</div>
				</div>
			<?php } ?>
			
			<?php if ($meta_values['topic_main_means_header'][0]) { ?>
				<?php nhop_internalnav('virkemidler'); ?>
				
				<div class="<?php thematic_post_class() ?>">
					<h2 class="entry-title"><?php echo $meta_values['topic_main_means_header'][0]; ?></h2>
					<div class="entry-content">
						<?php echo apply_filters('the_content', $meta_values['topic_main_means'][0]); ?>
					</div>
				</div>
			<?php } ?>
			
			<?php nhop_internalnav('bunn'); ?>
<?php
		}
		else {
?>
			<div class="entry-content">
				<?php echo $meta_values['topic_main_sit'][0]; ?>
			</div>
<?php
		}
	}
	else if ($show == $nhop['slug_examples']) {
?>
		<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class() ?>">
			<h2 class="entry-title"><?php echo $nhop['tab_examples']; ?> <?php social_bookmarks(); ?></h2>
			<div class="entry-content">
<?php
			echo apply_filters('the_content', $meta_values['topic_examples'][0]);
?>
			</div>
		</div>
<?php
	}
	else if ($show == $nhop['slug_statements']) {
?>
		<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class() ?>">
<?php
		
		$categories = get_the_category($post->ID);
		$getcat = 0;
		if (sizeof($categories) > 0) {
			$getcat = intval($categories[0]->term_id);
		}
		
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		$temp_query = $wp_query;
		
		$qs = 'meta_key='.get_theme_option('parent_topic_field_name').'&meta_value='.$post->ID.'&paged='.$paged;
		if ($_GET['sortering'] == 'kommentarer') {
			$qs .= '&orderby=comment_count';
		}
		query_posts($qs); 
		
		global $wp_query;
		echo "<h2 class='page-title'>";
		social_bookmarks();
		echo "Høringssvar <span class='number'>(".$wp_query->found_posts.")</span>";
		echo "</h2>";
		
		// Sorting
		$sort_base_url = get_permalink().$nhop['slug_statements']."/";
?>
		<div class="postSorter">
			<a href="<?php echo $sort_base_url; ?>" <?php if ($_GET['sortering'] != "kommentarer") echo "class='active'"; ?>>Siste</a> |
			<a href="<?php echo $sort_base_url; ?>?sortering=kommentarer" <?php if ($_GET['sortering'] == "kommentarer") echo "class='active'"; ?>>Mest kommentert</a>
		</div>
<?php
		// calling the widget area 'index-top'
		get_sidebar('index-top');

		// action hook for placing content above the index loop
		thematic_above_indexloop();

		// action hook creating the index loop
		thematic_indexloop();
		
		// action hook for placing content below the index loop
		thematic_below_indexloop();
		
		// calling the widget area 'index-bottom'
		get_sidebar('index-bottom');
		
		// create the navigation below the content
		thematic_navigation_below();
		
?>
		</div>
<?php
	}
	else if ($show == $nhop['slug_statement'] && $statement_id) {
?>
		<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class() ?>">
<?php
		$post = get_post($statement_id, OBJECT);
		setup_postdata($post);
?>
		<div class="socialBookmarks_container">
<?php
		social_bookmarks();
?>
		</div>
<?php
		echo getAuthorMeta();
		
		echo "<div id='answercontent'>";
		// action hook creating the single post
		thematic_singlepost();
		echo "</div>";
		
		// calling the widget area 'single-insert'
		get_sidebar('single-insert');

		// calling the comments template
		thematic_comments_template();
		
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();
		
?>
		</div>
<?php
	}
	else if ($show == $nhop['slug_write'] && $nhop['enable_statements']) {
?>
		<div id="post-<?php the_ID(); ?>" class="<?php thematic_post_class() ?>">
			<h2 class="page-title"><?php echo $nhop['send_answer']; ?></h2>
			<div class="entry-content">
<?php
			if(function_exists('tdomf_the_form')) {
				echo "<div class='statement_form'>";
				global $show;
				tdomf_the_form(1);
				echo "</div>";
			} else {
				exit("Forms is not enabled");
			}
?>
			</div>
		</div>
<?php
	}
	else {
		// Display summary
		if ($meta_values['topic_summary_means'][0]) {
?>
			<div class="<?php thematic_post_class() ?>">
				<h2 class="entry-title"><?php social_bookmarks(); ?> <?php echo $meta_values['topic_summary_sit_header'][0]; ?></h2>
				<div class="entry-content">
					<?php echo apply_filters('the_content', $meta_values['topic_summary_sit'][0]); ?>
				</div>
				<?php if ($meta_values['topic_main_sit'][0]) { ?>
					<p class="meta_readmore"><a href='<?php echo $permalink.$nhop['slug_full']; ?>/#situasjon'>Mer om <?php echo mb_strtolower($meta_values['topic_summary_sit_header'][0], 'UTF-8'); ?></a></p>
				<?php } ?>
			</div>
			
			<?php if ($meta_values['topic_summary_goal_header'][0]) { ?>
				<div class="<?php thematic_post_class() ?>">
					<h2 class="entry-title"><?php echo $meta_values['topic_summary_goal_header'][0]; ?></h2>
					<div class="entry-content">
						<?php echo apply_filters('the_content', $meta_values['topic_summary_goal'][0]); ?>
					</div>
					<?php if ($meta_values['topic_main_goal'][0]) { ?>
						<p class="meta_readmore"><a href='<?php echo $permalink.$nhop['slug_full']; ?>/#maal'>Mer om <?php echo mb_strtolower($meta_values['topic_summary_goal_header'][0], 'UTF-8'); ?></a></p>
					<?php } ?>
				</div>
			<?php } ?>
			
			<?php if ($meta_values['topic_summary_means_header'][0]) { ?>
				<div class="<?php thematic_post_class() ?>">
					<h2 class="entry-title"><?php echo $meta_values['topic_summary_means_header'][0]; ?></h2>
					<div class="entry-content">
						<?php echo apply_filters('the_content', $meta_values['topic_summary_means'][0]); ?>
					</div>
					<?php if ($meta_values['topic_main_means'][0]) { ?>
					<p class="meta_readmore"><a href='<?php echo $permalink.$nhop['slug_full']; ?>/#virkemidler'>Mer om <?php echo mb_strtolower($meta_values['topic_summary_means_header'][0], 'UTF-8'); ?></a></p>
					<?php } ?>
				</div>
			<?php } ?>
<?php
			if ($nhop['enable_questions']) {
				$hasquestions = $meta_values['topic_question_0'];
				if (sizeof($hasquestions) > 0) {
?>
				<div class="<?php thematic_post_class() ?>">
					<h2 class="entry-title"><?php echo $meta_values['topic_questions_header'][0]; ?></h2>
					<div class="entry-content">
						<div class="c_questions">
							<p class="intro"><?php echo $nhop['topic_questions_intro']; ?></p>
							<ul class="questions">
<?php
							for ($i=0; $i<5; $i++) {
								if ($meta_values['topic_question_'.$i][0] != "") {
?>
									<li><?php echo $meta_values['topic_question_'.$i][0]; ?></li>
<?php
								}
								else {
									break;
								}
							}
?>
							</ul>
						</div>
					</div>
				</div>
<?php
				}
			}
		}
		else if ($meta_values['topic_full_means'][0]) {
?>
			<?php echo $meta_values['topic_summary_sit'][0]; ?>
			<p class=""><a href='<?php echo $permalink.$nhop['slug_full']."/"; ?>'>Mer om <?php echo mb_strtolower(get_the_title(), 'UTF-8'); ?></a></p>
<?php
		}
?>
<?php
	}
	
	if ($show != $nhop['slug_write'] && $nhop['enable_statements']) {
?>
		<p><a href="<?php echo $permalink.$nhop['slug_write']."/"; ?>" class="buttonRound"><span><?php echo $nhop['send_answer']; ?></span></a></p>
<?php
	}
	
	wp_link_pages("\t\t\t\t\t<div class='page-link'>".__('Pages: ', 'thematic'), "</div>\n", 'number');
	edit_post_link(__('Edit', 'thematic'),'<span class="edit-link">','</span>') ?>

	<?php
	
	// calling the widget area 'page-bottom'
	get_sidebar('page-bottom');
	
	?>

	</div><!-- #content -->
</div><!-- #container -->

<?php 

    // action hook for placing content below #container
    thematic_belowcontainer();
?>
	<div class="topic-sidebar">
<?php
    // calling the standard sidebar 
    thematic_sidebar();
?>
	</div>
<?php
    // calling footer.php
    get_footer();

?>