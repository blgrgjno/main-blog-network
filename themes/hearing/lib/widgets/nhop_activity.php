<?php

add_action('admin_head','nhop_selected_adminhead');

function nhop_selected_adminhead(){
	global $pagenow;
	if($pagenow == 'widgets.php'){
?>
		<style type="text/css">
			.selected-tabs .ui-tabs-panel {
				background-color:#f8f8f8;
				border:1px solid #ccc;
				border-width:1px;
				margin-bottom:20px;
				padding:10px 0;
				-moz-border-radius:5px;
				-webkit-border-radius:5px;
				border-radius:5px;
			}
			.selected-nav-tabs {
				height:27px;
				overflow:visible;
				padding-left:10px;
			}
			.selected-nav-tabs .ui-state-default a {
				background-color:#e0e0e0;
				border-bottom:1px solid #ccc;
			}
			.selected-nav-tabs .ui-tabs-selected a {
				font-weight:bold;
				background-color:#f8f8f8;
				border-bottom:1px solid #f8f8f8;
			}
			.selected-nav-tabs li {
				float:left;
				margin:0;
			}
			.selected-nav-tabs a {
				color:#333;
			}
			.widget .ui-tabs-panel p,
			.widget .ui-tabs-panel ul {
				margin:0 10px 1em;
			}
			.widget .ui-tabs-panel h3 {
				font-size:12px;
				font-weight:bold;
				border-top:1px solid #ccc;
				padding:1em 10px 0;
			}
		</style>
<?php
	}
}

class NHOP_activity extends WP_Widget {

	function NHOP_activity() {
		$widget_ops = array('classname' => 'nhop_activity', 'description' => __('Siste/mest diskuterte'));
		$this->WP_Widget('nhop_activity', __('NHOP: Aktivitet'), $widget_ops);
		$this->alt_option_name = 'nhop_activity';
		
		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function form($instance) {
		$random = rand();
?>
	<div id="selectedtabs<?php echo $random; ?>" class="selected-tabs">
		<ul class="selected-nav-tabs">
			<li><a href="#selectedfront<?php echo $random; ?>" class="nav-tab">Forside</a></li>
			<li><a href="#selectedtopic<?php echo $random; ?>" class="nav-tab">Emnesider</a></li>
		</ul>
<?php
		// Front Page - Automatic +/ Selected posts
		
		$front_latest_title = isset($instance['front_latest_title']) ? esc_attr($instance['front_latest_title']) : '';
		
		if ( !isset($instance['front_number_latest_org']))
			$front_number_latest_org = 3;
		else
			$front_number_latest_org = (int) $instance['front_number_latest_org'];
		
		if ( !isset($instance['front_number_latest_priv']))
			$front_number_latest_priv = 3;
		else
			$front_number_latest_priv = (int) $instance['front_number_latest_priv'];
		
		$selected_title = isset($instance['selected_title']) ? esc_attr($instance['selected_title']) : '';
		$selected_description = isset($instance['selected_description']) ? esc_attr($instance['selected_description']) : '';
		$posts = isset($instance['posts']) ? esc_attr($instance['posts']) : '';
		if ($posts) {
			$posts = explode(",", $posts);
		}
		else {
			$posts = array();
		}
		$link_title = isset($instance['link_title']) ? esc_attr($instance['link_title']) : '';
		$link_url = esc_url( $instance['link_url'] );
?>
		<div id="selectedfront<?php echo $random; ?>">
		
			<h3>Siste innlegg</h3>
			
			<p><label for="<?php echo $this->get_field_id('front_latest_title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('front_latest_title'); ?>" name="<?php echo $this->get_field_name('front_latest_title'); ?>" type="text" value="<?php echo $front_latest_title; ?>" /></p>
			
			<p><label for="<?php echo $this->get_field_id('front_number_latest_org'); ?>">Antall innlegg fra virk/org:</label><br/>
			<input id="<?php echo $this->get_field_id('front_number_latest_org'); ?>" name="<?php echo $this->get_field_name('front_number_latest_org'); ?>" type="text" value="<?php echo $front_number_latest_org; ?>" size="3" /></p>
			
			<p><label for="<?php echo $this->get_field_id('front_number_latest_priv'); ?>">Antall innlegg fra private:</label><br/>
			<input id="<?php echo $this->get_field_id('front_number_latest_priv'); ?>" name="<?php echo $this->get_field_name('front_number_latest_priv'); ?>" type="text" value="<?php echo $front_number_latest_priv; ?>" size="3" /></p>
			
			<h3>Utvalgte innlegg</h3>
			
			<p><label for="<?php echo $this->get_field_id('selected_title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('selected_title'); ?>" name="<?php echo $this->get_field_name('selected_title'); ?>" type="text" value="<?php echo $selected_title; ?>" /></p>
			
			<p><label for="<?php echo $this->get_field_id('selected_description'); ?>">Intro-tekst:</label>
			<textarea name="<?php echo $this->get_field_name('selected_description'); ?>" id="<?php echo $this->get_field_id('selected_description'); ?>" cols="20" rows="4" class="widefat"><?php echo $selected_description; ?></textarea></p>
			
<?php
			$this->listAllPosts($posts);
?>		
			
			<h3>Lenke</h3>
			
			<p><label for="<?php echo $this->get_field_id('link_title'); ?>">Tittel:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('link_title'); ?>" name="<?php echo $this->get_field_name('link_title'); ?>" type="text" value="<?php echo $link_title; ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('link_url'); ?>">URL:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('link_url'); ?>" name="<?php echo $this->get_field_name('link_url'); ?>" type="text" value="<?php echo $link_url; ?>" /></p>
		</div>
<?php
		// Topic page - Automaitc
		
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$title_latest = isset($instance['title_latest']) ? esc_attr($instance['title_latest']) : '';
		$title_commented = isset($instance['title_commented']) ? esc_attr($instance['title_commented']) : '';
		if ( !isset($instance['number_latest']) || !$number_latest = (int) $instance['number_latest'] )
			$number_latest = 5;
		if ( !isset($instance['number_commented']) || !$number_commented = (int) $instance['number_commented'] )
			$number_commented = 5;
?>
		<div id="selectedtopic<?php echo $random; ?>">
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Hovedtittel:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
			
			<h3>Siste innlegg</h3>
			
			<p><label for="<?php echo $this->get_field_id('title_latest'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title_latest'); ?>" name="<?php echo $this->get_field_name('title_latest'); ?>" type="text" value="<?php echo $title_latest; ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('number_latest'); ?>"><?php _e('Number of posts to show:'); ?></label>
			<input id="<?php echo $this->get_field_id('number_latest'); ?>" name="<?php echo $this->get_field_name('number_latest'); ?>" type="text" value="<?php echo $number_latest; ?>" size="3" /></p>
			
			<h3>Mest kommentert</h3>
			
			<p><label for="<?php echo $this->get_field_id('title_commented'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title_commented'); ?>" name="<?php echo $this->get_field_name('title_commented'); ?>" type="text" value="<?php echo $title_commented; ?>" /></p>

			<p><label for="<?php echo $this->get_field_id('number_commented'); ?>"><?php _e('Number of posts to show:'); ?></label>
			<input id="<?php echo $this->get_field_id('number_commented'); ?>" name="<?php echo $this->get_field_name('number_commented'); ?>" type="text" value="<?php echo $number_commented; ?>" size="3" /></p>
			
		</div>
	</div>
	<script type="text/javascript">
		jQuery("#selectedtabs<?php echo $random; ?>").tabs();
	</script>
	
<?php
	}

	function listAllPosts($posts) {
        query_posts(array('posts_per_page' => -1));
		if ( have_posts() ) {
			$i = 0;
			$priv_list = "";
			$corp_list = "";
			while ( have_posts() ) {
				the_post();
				$statement_meta = get_statement_meta(get_the_ID());
				$post_id = get_the_ID();
				$checked = (in_array($post_id, $posts)) ? "checked='checked'" : "";
				
				global $post;
				$user_type = get_user_meta($post->post_author, "user_type", true);
				$postid = get_the_ID();
				$theLi = sprintf('<li>
					<input type="checkbox" name="%s[]" id="sp_%s" value="%s" %s />
					<label for="sp_%s">
						%s<br/>
						<span style="font-size:10px;line-height:9px;">av %s i %s</span>
					</label>
				</li>', $this->get_field_name('posts'), $postid, $postid, $checked, $postid, get_the_title(), get_the_author(), $statement_meta->topic_title );
				
				if ($user_type == "privat") {
					$priv_list .= $theLi;
				}
				else {
					$corp_list .= $theLi;
				}
				$i++;
			}
		}
?>
		<div style="margin:0 10px">Virksomheter/organisasjoner:</div>
		<ul style="height:200px;overflow:auto;background-color:#fff;border:1px solid #DFDFDF;">
			<?php echo $corp_list ?>
		</ul>
		<div style="margin:0 10px">Privatpersoner:</div>
		<ul style="height:200px;overflow:auto;background-color:#fff;border:1px solid #DFDFDF;">
			<?php echo $priv_list ?>
		</ul>
<?php
	}
	
	function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		
		// Front
		$instance['front_latest_title'] = strip_tags($new_instance['front_latest_title']);
		$instance['front_number_latest_org'] = strip_tags($new_instance['front_number_latest_org']);
		$instance['front_number_latest_priv'] = strip_tags($new_instance['front_number_latest_priv']);
		$instance['selected_title'] = strip_tags($new_instance['selected_title']);
		$instance['selected_description'] = strip_tags($new_instance['selected_description']);
		$instance['posts'] = implode(',', $new_instance['posts']);
		
		// Topic
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['title_latest'] = strip_tags($new_instance['title_latest']);
		$instance['title_commented'] = strip_tags($new_instance['title_commented']);
		$instance['number_latest'] = (int) $new_instance['number_latest'];
		$instance['number_commented'] = (int) $new_instance['number_commented'];
		$instance['link_title'] = $new_instance['link_title'];
		$instance['link_url'] = esc_url( $new_instance['link_url'] );

		$this->flush_widget_cache();
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_activity']) )
			delete_option('widget_activity');

		return $instance;
	}

	function widget($args, $instance) {
		// Don't display on list pages, wp-page or write page:
		global $wp_query;
#		if ($wp_query->query_vars['show'] == "svar" || $wp_query->query_vars['show'] == "skriv" || is_home() || (!is_front_page() && is_page()) || is_author()) return;
//		if ($wp_query->query_vars['show'] == "svar" || $wp_query->query_vars['show'] == "skriv" || (!is_front_page() && is_page()) || is_author()) return;
		
		$temp_query = $wp_query;
		
		$cache_name = 'nhop_activity';
		if (is_front_page()) {
			$cache_name = 'nhop_activity_front';
		}
		else {
			global $post;
			$cache_name .= $post->ID;
		}
		$cache = wp_cache_get($cache_name, 'widget');
		
		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args );
		
		echo $before_widget;
		if (is_front_page()) {
			$this->print_latest_front($args, $instance);
			$this->print_selected($args, $instance);
			if ($instance['link_title'] && $instance['link_url']) {
?>
				<a class="buttonRoundLightSlim" href="<?php echo $instance['link_url']; ?>"><span><?php echo $instance['link_title']; ?></span></a>
<?php
			}
		}
		else {
			$this->print_latest_front($args, $instance);
			$this->print_commented($args, $instance);
		}
		
		echo $after_widget;
		
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set($cache_name, $cache, 'widget');
		
		$wp_query = $temp_query;
	}

	function print_selected($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['selected_title']) ? '' : $instance['selected_title'], $instance, $this->id_base);
		
		$posts = isset($instance['posts']) ? esc_attr($instance['posts']) : '';
		if ($posts) {
			$posts = explode(",", $posts);
			
			if ($title) echo "<h4>" . $title . "</h4>";
			
			if ($instance['selected_description']) {
?>
				<div class="entry-content"><p><?php echo $instance['selected_description']; ?></p></div>
<?php
			}
			
			$r = new WP_Query(array('post__in' => $posts));
			if ($r->have_posts()) {
?>
				<ul class="nhop_latest_posts dss_nhop_1">
<?php
				while ($r->have_posts()) {
					$r->the_post();
					
					// Get statement meta
					$statement_meta = get_statement_meta(get_the_ID());
					
					// Get user type
					global $post;
					$user_type = get_user_meta($post->post_author, "user_type", true);

					// Get author posts link
					global $authordata;
					if (strpos($authordata->display_name, "tdomf") === 0) {
						$author_posts_link = sprintf('<span class="author_name author_%2$s"><span class="a_alt">%1$s</span></span>',
							tdomf_get_the_submitter(),
							$user_type
						);
					}
					else {
						$link = sprintf(
							'<a href="%1$s" title="%2$s">%3$s</a>',
							get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
							esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
							get_the_author()
						);
						$author_posts_link = apply_filters( 'the_author_posts_link', $link );
					}
					// Get title
					$post_title = esc_attr(get_the_title() ? get_the_title() : get_the_ID());
					
					$theLi = sprintf('<li>
						<div class="author author_%s">%s</div>
						<div class="post"><a href="%s" title="%s">%s</a> %s</div>
						<div class="topic">Tema: <a href="%s" title="%s">%s</a></div>
					</li>',
						$user_type,
						$author_posts_link,
						$statement_meta->statement_url,
						$post_title,
						truncate($post_title, 80, $ending = ' (...)', false),
						get_comments_bubble(),
						$statement_meta->topic_url,
						$statement_meta->topic_title,
						$statement_meta->topic_title
					);
				
					if ($user_type == "privat") {
						$priv_list .= $theLi;
					}
					else {
						$corp_list .= $theLi;
					}
				} // endwhile

				echo $corp_list;
				echo $priv_list;
?>
				</ul>
<?php
			} // endif postsfound
		} // endif posts
	} // endfunction print_selected
	
	function print_latest($args, $instance) {
	
		global $wpdb;
		
		extract( $args );
		
        if ( !$number = (int) $instance['number_latest'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		if ($title) echo $before_title . $title . $after_title;
		
		// Filter on topic on topic page
		global $post;
		$querystr = array('showposts' => $number, 'nopaging' => 0, 
                    'post_status' => 'publish', 'caller_get_posts' => 1,
                    'cat' => '-9');
        // TODO: hack above. News category 11 removed. Move to config!!!
		if ($post->post_type == "topic") {
			$querystr = array_merge($querystr, array('meta_key' => get_theme_option('parent_topic_field_name'), 'meta_value' => $post->ID));
		}
		
		$r = new WP_Query($querystr);
		if ($r->have_posts()) :
		
		$title = apply_filters('widget_title', empty($instance['title_latest']) ? "Siste innspill" : $instance['title_latest'], $instance, $this->id_base);
		if ($title) echo "<h4>" . $title . "</h4>";
?>
		<ul class="nhop_latest_posts dss_nhop_2">
<?php
		while ($r->have_posts()) :
			$r->the_post();
			
			$statement_meta = get_statement_meta(get_the_ID());
			$post_title = esc_attr(get_the_title() ? get_the_title() : get_the_ID());
			
			// Get user type
			global $post;
			$user_type = get_user_meta($post->post_author, "user_type", true);
			
			$domf = false;
			if (strpos(get_the_author_meta('display_name'), "tdomf") === 0) {
				$domf = true;
//				$submitter_name = '<span class="a_alt">'.tdomf_get_the_submitter().'</span>';
			}
?>
		<li>
			<div class="author author_<?php echo $user_type; ?>">
				<span class="a_alt">
					<?php echo get_post_meta( $thepost->ID, 'Author Name', true ); ?>
				</span>
			</div>
			<div class="post"><a href="<?php echo get_permalink( $post->ID ); ?>" title="<?php echo $post_title; ?>"><?php echo truncate($post_title, 80, $ending = ' (...)', false); ?></a> <?php echo get_comments_bubble(); ?></div>
			<div class="topic">Tema: <a href="<?php echo $statement_meta->topic_url; ?>" title="<?php echo $statement_meta->topic_title; ?>"><?php echo $statement_meta->topic_title; ?></a></div>
		</li>
		<?php endwhile; ?>
		</ul>
<?php
		endif;
		
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();
	}
	
	function print_latest_front($args, $instance) {
		
		// Print x latest posts from organizations, then y latest posts from private users
		
		extract( $args );
		
		$front_number_latest_org = (int) $instance['front_number_latest_org'];
		$front_number_latest_priv = (int) $instance['front_number_latest_priv'];
		
		if ( $front_number_latest_org > 0 || $front_number_latest_priv > 0 ) {
		
			$title = apply_filters('front_latest_title', empty($instance['front_latest_title']) ? '' : $instance['front_latest_title'], $instance, $this->id_base);
			if ($title) echo "<h4>" . $title . "</h4>";
?>
			<ul class="nhop_latest_posts dss_nhop_3">
<?php
			$user_types = array('virksomhet' => $front_number_latest_org, 'privat' => $front_number_latest_priv);
			
			global $wpdb;
			foreach ($user_types as $user_type => $num_posts) {
			
				// BASE QUERY
				if (get_theme_option('version') != 'minimal') {
					$querystr  = "SELECT po.* from $wpdb->posts AS po, $wpdb->users AS us, $wpdb->usermeta AS um ";
					$querystr .= "WHERE po.post_status = 'publish' AND po.post_type = 'post' AND po.post_password = '' AND po.post_date_gmt < '".gmdate("Y-m-d H:i:s")."' ";
					$querystr .= "AND po.post_author = us.ID ";
					$querystr .= "AND us.ID = um.user_id ";
					$querystr .= "AND um.meta_key = 'user_type' ";
					$querystr .= "AND um.meta_value = '".$user_type."' ";
					$querystr .= "order by po.post_date_gmt DESC ";
					$querystr .= "LIMIT ".$num_posts." ";
					
					$querystr = $wpdb->prepare( $querystr ); 
					$pageposts = $wpdb->get_results($querystr);
				}
				else {
				/*	$querystr  = "SELECT po.* from $wpdb->posts AS po ";
					$querystr .= "WHERE po.post_status = 'publish' AND po.post_type = 'post' AND po.post_password = '' AND po.post_date_gmt < '".gmdate("Y-m-d H:i:s")."' ";
					$querystr .= "order by po.post_date_gmt DESC ";
					$querystr .= "LIMIT ".$num_posts." ";
					
					$pageposts = $wpdb->get_results($querystr);
                */
                  // TODO: 11 is the news category. Should be moved to
                  // a variable
                  $pageposts = get_posts('category=-9&numberposts='.$num_posts.'&order=DESC&orderby post_date_gmt');
				}
				
				if ($pageposts) {
					foreach ($pageposts as $thepost) {
						setup_postdata($thepost);
						
						$statement_meta = get_statement_meta($thepost->ID);
						$post_title = esc_attr($thepost->post_title);
						
						$domf = false;
						if (strpos(get_the_author_meta('display_name'), "tdomf") === 0) {

							// The following code was used in conjunction with TDO mini-forms
							// $domf = false was added to make it work without the TDO mini-forms plugin being present
							$domf = false;
//							$domf = true;
//							$submitter_name = '<span class="a_alt">'.tdomf_get_the_submitter($thepost->ID).'</span>';
						}
?>
						<li>
							<div class="author author_<?php echo $user_type; ?>">
								<span class="a_alt">
									<?php echo get_post_meta( $thepost->ID, 'Author Name', true ); ?>
								</span>
							</div>
							<div class="post">
								<a href="<?php echo get_permalink( $thepost->ID ); ?>" title="<?php echo $post_title; ?>">
									<?php echo truncate($post_title, 80, $ending = ' (...)', false); ?>
								</a> <?php echo get_comments_bubble(); ?>
							</div>
							<div class="topic">Tema: <a href="<?php echo $statement_meta->topic_url; ?>" title="<?php echo $statement_meta->topic_title; ?>"><?php echo $statement_meta->topic_title; ?></a></div>
						</li>
<?php
					} // endforeach posts
				} // endif pageposts
				
				if (get_theme_option('version') == 'minimal') break;
				
			} // endforeach user types
?>
			</ul>
<?php
			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();
		} // endif number larger than zero
	}
	
	function print_commented($args, $instance) {
	
		global $wpdb;
		
		extract( $args );
		
        if ( !$number = (int) $instance['number_commented'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		if ($title) echo $before_title . $title . $after_title;
		
		// Filter on topic on topic page
		global $post;
		$querystr = array('showposts' => $number, 'orderby' => 'comment_count', 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1);
		if ($post->post_type == "topic") {
			$querystr = array_merge($querystr, array('meta_key' => get_theme_option('parent_topic_field_name'), 'meta_value' => $post->ID));
		}
		$r = new WP_Query($querystr);
		$commentlist = "";
		if ( $r->have_posts() ) {
			while ( $r->have_posts() ) {
				$r->the_post();

				$bubble = get_comments_bubble();
				if (!$bubble) break;
				
				$the_post_url = home_url( '/?p=' . get_the_ID() );
				$post_title = esc_attr(get_the_title() ? get_the_title() : get_the_ID());
				
				// Get user type
				global $post;
				$user_type = get_user_meta($post->post_author, "user_type", true);
				
				// Get author posts link
				global $authordata;
				if (strpos($authordata->display_name, "tdomf") === 0) {
					$authorposts = sprintf('<span class="author_name author_%2$s"><span class="a_alt">%1$s</span></span>',
//						tdomf_get_the_submitter(),
						$user_type
					);
				}
				else {
					$authorposts = sprintf(
						'<a href="%1$s" title="%2$s">%3$s</a>',
						get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
						esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
						get_the_author()
					);
				}

				$authorposts = get_post_meta( get_the_ID(), 'Author Name', true );

				$commentlist .= sprintf(
					'<li>
						<div class="author author_%s">%s</div>
						<div class="post"><a href="%s" title="%s">%s</a> %s</div>
					</li>',
					$user_type,
					$authorposts,
					$the_post_url,
					$post_title,
					truncate($post_title, 80, $ending = ' (...)', false),
					get_comments_bubble()
				);
			}
		}
		
		if ($commentlist != "") {
			$title = apply_filters('widget_title', empty($instance['title_commented']) ? "Mest kommentert" : $instance['title_commented'], $instance, $this->id_base);
			if ($title) echo "<h4>" . $title . "</h4>";
?>
			<ul class="nhop_most_commented">
				<?php echo $commentlist; ?>
			</ul>
<?php
		}
		
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();
	}
	
	function flush_widget_cache() {
		wp_cache_delete('nhop_activity', 'widget');
	}

}
add_action('widgets_init', create_function('', 'return register_widget("NHOP_activity");'));
?>