<?php
class Admin_pages
{
	function __construct()
	{
		//add pages
		add_action('admin_menu', array( $this, 'admin_menu' ) );
		
	}

	/**
		 * Call on admin pages pages
		 */
		function admin_menu(){
			$page = add_menu_page( 'Instagrabber', 'Instagrabber', 'edit_published_posts', 'instagrabber', array($this, 'stream_page'), INSTAGRABBER_PLUGIN_URL.'icon.png' );
			add_action( "load-$page", array($this, 'add_option') );
			
			$streampage = add_submenu_page( 'instagrabber', __('New Stream', 'instagrabber'), __('New Stream', 'instagrabber'), 'edit_published_posts', 'instagram-add', array($this, 'admin_stream_page') );

			add_submenu_page( 'instagrabber', __('Instagrabber settings', 'instagrabber'), __('Instagrabber settings', 'instagrabber'), 'manage_options', 'instagram-settings', array($this, 'admin_settings_page') );

			add_submenu_page( 'instagrabber', __('Instagrabber help', 'instagrabber'), __('Instagrabber help', 'instagrabber'), 'manage_options', 'instagram-help', array($this, 'admin_help_page') );

			add_action( 'admin_print_styles-' . $streampage, array($this, 'add_styles') );
			/*add_submenu_page( 'instagrabber', __('Instagrabber debuger', 'instagrabber'), __('Instagrabber debug', 'instagrabber'), 'manage_options', 'instagram-debug', array($this, 'admin_debug_page') );*/

		}

		function add_styles(){
			wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		}

		function add_option(){
			if (isset($_GET['stream'])) {
				$option = 'per_page';
 
				$args = array(
					'label' => __('Images', 'instagrabber'),
					'default' => 10,
					'option' => 'instagrabber_per_page'
				);
				 
				add_screen_option( $option, $args );
			}
			
		}

		/**
		 * Display Streams or a single stream if $_GET['stream'] is set
		 */
		function stream_page(){
		
			global $current_user;

			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');

			$current_user = wp_get_current_user();
			
			?>
				<div class="wrap">
					<div id="instagrabber-icon" class="icon32"><br></div>
					<h2  class=""><?php _e('Instagrabber Streams', 'instagrabber') ?></h2>
					<?php if (!isset($_GET['stream'])):
						//List streams
						//Create an instance of our package class...
					    $testListTable = new List_Streams();
					    //Fetch, prepare, sort, and filter our data...
					    $testListTable->prepare_items();
					?>
						<form id="" method="get" action="">
			            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
			            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']?>" />
			            <!-- Now we can render the completed list table -->
			            <?php $testListTable->display() ?>
			        </form>
			        <script type="text/javascript">
				        jQuery(document).ready(function() {
							
							jQuery('.deletestream').click(function() {
								
								var answer = confirm('<?php _e('Are you sure you want to delete this stream?', 'instagrabber') ?>');
								return answer;

							});
						});
			        </script>
			    	<?php else:
			    		$stream = Database::get_stream($_GET['stream']);
			    		
			    		if ($stream->error != 1):
				    		//Create an instance of our package class...
						    $testListTable = new List_Stream($stream);
						    //Fetch, prepare, sort, and filter our data...
						    $testListTable->prepare_items();
				    	?>
					    	<?php 
					    	// check if Authorize is done
					    	if (!Database::get_access_token($_GET['stream'])): ?>
					    	
					    	<?php 
					    	$authtype = get_option('instagrabber_authtype');
					    	?>
					    	
					    		<p><?php _e('You must Authorize this stream', 'instagrabber') ?></p>
						    	<input type="button" value="<?php _e('Authorization', 'instagrabber') ?>" id="authsubmit" >
						    	<script type="text/javascript">
										var InstagramAuthWindow = null;
										
										jQuery(document).ready(function() {

											jQuery('authform').attr('action', '');
											
											jQuery('#authsubmit').click(function() {
												
												InstagramAuthWindow = window.open('<?php print(InstagrabberApi::instagrabber_getAuthorizationPageURI($_GET["stream"])); ?>', 'InstagramAuthorization', 'width=800,height=400');

											});
										});
									</script>	
					    	
						    	
					    	<?php else: ?>
					    	<?php $current_user = wp_get_current_user();
        					
        					?>
					    	<?php $stream = Database::get_stream($_GET['stream']);
					    			$streamadmins = is_array(unserialize($stream->administrators)) ? unserialize($stream->administrators) : array();
					    			// Do not show streams to other users
					    			if( !($stream->created_by == $current_user->ID || in_array($current_user->ID, $streamadmins)) && !current_user_can('manage_options'))
					    				wp_die('You do not have sufficient permissions to access this page.', 'You do not have sufficient permissions to access this page.');
					    	?>
					    		<form id="movies-filter" method="get">
						            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
						            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
						            <input type="hidden" name="stream" value="<?php echo $_REQUEST['stream']?>" />
						            <!-- Now we can render the completed list table -->
						            <?php 
						            $testListTable->search_box(__('search','instagrabber'), 'search_id');
						            $testListTable->display() ?>
						        </form>
					    	<?php endif ?>
					    <?php else: ?>
			    		<div id="message" class="error"><p><strong>Error:</strong> <?php echo $stream->error_message; ?></p>
			    			<p><a href="?page=instagram-add&stream=<?php echo $_GET['stream'] ?>" title="Update stream"><?php _e('Please update your stream here to avoid this error', 'instagrabber') ?></a></p></div>
					<?php endif; ?>
				<?php endif; ?>
					
				</div>
			<?php
		}

		function admin_help_page(){
			$tabs = array('settings' =>  __('Settings page', 'instagrabber'), 'add' => __('Add a stream', 'instagrabber'), 'crontab' => __('Get images automaticly with cron', 'instagrabber') );
			$currenttab = !isset($_GET['tab']) ? 'settings' : $_GET['tab'];
			?>
				<div class="wrap">
					<div id="instagrabber-icon" class="icon32"><br></div>
					<h2  class="nav-tab-wrapper"><?php _e('Instagrabber help', 'instagrabber') ?>
						<?php 
							 foreach( $tabs as $tab => $name ){
						        $class = ( $tab == $currenttab ) ? ' nav-tab-active' : '';
						        echo "<a class='nav-tab$class' href='?page=instagram-help&tab=$tab'>$name</a>";

						    }
						 ?>
					</h2>
						<div class="instagrabberhelp">
					<?php if ($currenttab == 'settings'): ?>
						<h4 class="instagrabbertitle"><?php _e('The settings page', 'instagrabber') ?></h4>
						<p class="instaimage">
							<img src="<?php echo INSTAGRABBER_PLUGIN_URL ?>images/screen_3.png" alt="">
							<?php _e('Add client id and client secret in instagrabber settings page', 'instagrabber'); ?>
						</p>
						<p class="instaimage">
							<img src="<?php echo INSTAGRABBER_PLUGIN_URL ?>images/screen_4.png" alt="">
							<?php _e('Authorization button will be displayed after you saved', 'instagrabber'); ?>
						</p>
						<p><?php _e('The first two fields is information from your app that you created above. The third setting is a time schedule feature that will get your images and, depending on your stream settings, publish them. Choose a intervall and save. If this is set to never you will have to update the streams manually.', 'instagrabber') ?></p>

						<p><?php _e('The last option is optional but will help me. Show a after a post that shows a link to this plugin. This is not saved whit the post so you can turn it on and off and the link will dissapear.', 'instagrabber') ?></p>

						<p><?php _e('Click on save and a Authorization buton will appear. Click on it to Authorize.', 'instagrabber') ?></p>
					<?php elseif ($currenttab == 'add'): ?>
						<h4 class="instagrabbertitle"><?php _e('How to add a stream', 'instagrabber') ?></h4>
						<p class="instaimage">
							<img src="<?php echo INSTAGRABBER_PLUGIN_URL ?>images/screen_5.png" alt="">
							<?php _e('Add a new stream', 'instagrabber'); ?>
						</p>
						<p class="instaimage">
							<img src="<?php echo INSTAGRABBER_PLUGIN_URL ?>images/screen_6.png" alt="">
							<?php _e('Authorize the stream', 'instagrabber'); ?>
						</p>
						<p class="instaimage">
							<img src="<?php echo INSTAGRABBER_PLUGIN_URL ?>images/screen_7.png" alt="">
							<?php _e('Click on update stream to display the images', 'instagrabber'); ?>
						</p>
						<p><?php _e('Now you will add a stream. This is a stream of images that follows a set of rules. There are two mayor types of streams, user streams and tag streams. A user stream only contains images from one user (the one who activates the stream) and a tag streams contains public images that contains a specific tag. Here is an explanation to the fields on the page:', 'instagrabber') ?></p>

						<ul>
						<li><?php _e('Name: the name of the stream', 'instagrabber') ?></li>
						<li><?php _e('Placeholder title for post: This will be the title. you can use these placeholders. %user% - the user that uploaded the image, %tag% - the tag for the stream and %caption% - tha caption for the image. So a title can look like this:  "%user%: %caption%". But when you publish the image it will look like this: "ferenyl: I love my job".', 'instagrabber') ?></li>
						<li><?php _e('Type: There are three different types now, user, tag and likes. The user type will get all images from a user, the tag type will get all images with a tag and likes type will get all images that a user has liked (the user is the one who has activated the stream).', 'instagrabber') ?></li>
						<li><?php _e('Tag: The tag to use in a tag stream. Or filter a user stream with a tag.', 'instagrabber') ?></li>
						<li><?php _e('Auto create post: Do you want to publish a image automaticly or do you want to do it manually?', 'instagrabber') ?></li>
						<li><?php _e('Post Type: Do you have more than one post type? choose a post type to save in.', 'instagrabber') ?></li>
						<li><?php _e('Post status: You can publish it right away or make it a draft if you want to edit them first.', 'instagrabber') ?></li>
						<li><?php _e('Post format: if your theme support post formats you can choose one here (Image is ideal).', 'instagrabber') ?></li>
						<li><?php _e('Taxonomy: Choose a taxonomy if you have more than one.', 'instagrabber') ?></li>
						<li><?php _e('Term: Choose a term in that taxonomy', 'instagrabber') ?></li>
						<li><?php _e('Taxonomy for tags: if you want to tag your image', 'instagrabber') ?></li>
						<li><?php _e('Auto set tag: Convert the image instagram tags to wordpress tags', 'instagrabber') ?></li>
						<li><?php _e('Attachment type: Choose how to attach the image to the post. Make sure your theme supports featured images before choosing that. Both is a great choice', 'instagrabber') ?></li>
						</ul>
						<p><?php _e('You will be directed to the stream page when you have clicked on save. You must authorize the stream before you can use it. The stream will be connected with the user who authorizes it.', 'instagrabber') ?></p>
					<?php elseif ($currenttab == 'crontab'): ?>
						<p><?php _e('Url to cron file:') ?> <strong><?php echo plugin_dir_url( __FILE__ ) . 'instagrabber-cron.php' ?></strong></p>

						<h4 class="instagrabbertitle"><?php _e('Crontab' ,'instagrabber') ?></h4>
						<p><strong><?php _e("You need ssh access  for this one. Go to the next section if you don't have that." ,'instagrabber') ?></strong></p>

						<p><strong><?php _e('Important: Remember to set Auto get photos to never first!' ,'instagrabber') ?></strong></p>

						<p><?php _e('Write this in the terminal to create a new crontab:' ,'instagrabber') ?></p>

						<pre><code>crontab -e</code></pre>

						<p><?php _e('The syntax for the cron job looks like this:' ,'instagrabber') ?></p>

						<pre><code>[minutes] [Hours] [date] [month] [day of the veek] script param</code></pre>

						<p><?php _e('If i want to run a cron job the fifth minute every hour it looks like this:' ,'instagrabber') ?></p>

						<pre><code>5 * * * * script param</code></pre>

						<p><?php _e('"*" means every.' ,'instagrabber') ?></p>

						<p><?php _e("But i want to run my cronjob every 5 minutes so i'm using this:" ,'instagrabber') ?></p>

						<pre><code>*/5 * * * * script param</code></pre>

						<p><?php _e('So now we know how to setup a cron job. But to run the cron file in instagrabber another thing is needed. A function to run and a url to use. We will be using wget for this. And the url is in the top of this page.' ,'instagrabber') ?></p>

						<p><?php _e('This is how my cron job would look like:' ,'instagrabber') ?></p>

						<pre><code>*/5 * * * * /usr/bin/wget -q -o /dev/null http://plugins.ll/wp-content/plugins/instagrabber/instagrabber-cron.php /dev/null 2>&1</code></pre>

						<p><?php _e('A little breakdown:' ,'instagrabber') ?></p>

<pre><code>
# run every five minutes
*/5 * * * *
# start wget in quiet mode
/usr/bin/wget -q
# choose null file to keep wget from saving the file
-o /dev/null
# The url
http://plugins.ll/wp-content/plugins/instagrabber/instagrabber-cron.php
# write output to null file
/dev/null 2>&1
</code></pre>

						<p><?php _e('Save the file and your cron job will run.' ,'instagrabber') ?></p>
						<h4 class="instagrabbertitle"><?php _e('Setup cron with external service.' ,'instagrabber') ?></h4>
						<p class="instaimage">
							<img src="<?php echo INSTAGRABBER_PLUGIN_URL ?>images/screen_8.png" alt="">
							<?php _e('Add a new check', 'instagrabber'); ?>
						</p>
						<p class="instaimage">
							<img src="<?php echo INSTAGRABBER_PLUGIN_URL ?>images/screen_9.png" alt="">
							<?php _e('Fill the form out like this', 'instagrabber'); ?>
						</p>
						<p><strong><?php _e('Important: Remember to set Auto get photos to never first!' ,'instagrabber') ?></strong></p>

						<p><?php _e("So you don't have access to ssh on a server. Don't worry, you can always use a webservice. Im going to show you how to setup a pingdom check for this." ,'instagrabber') ?></p>

						<p><?php _e('First create an account and log in. Klick on Add new check an fill it in like the image.' ,'instagrabber') ?></p>

						<p><?php _e('Write the cron url that you find on the top of this page in the url field and test it. Now you have a cron job for instagrabber.' ,'instagrabber') ?></p>
						<?php endif ?>
						<div class="clear"></div>
						
						</div>
				</div>
			<?php
		}



		/**
		 * Create/edit stream page
		 */
		function admin_stream_page(){
			
			wp_enqueue_script( 'jquery' );
    		wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script( 'intagrabber_stream' );

			$xauth = get_option('instagrabber_authtype');
			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');
			?>
				<div class="wrap">
					<div id="instagrabber-icon" class="icon32"><br></div>
					<h2  class="">
						<?php if(!isset($_GET['stream'])){
							_e('Instagrabber Add Stream', 'instagrabber');
						}else{
							_e('Instagrabber Update Stream', 'instagrabber');
						}
						?>
					</h2>
					<?php if ( $xauth != 'xauth' && (!$InstagramClientID || !$InstagramClientSecret)): ?>
						<p><?php _e('You must configure this plugin. Go to settings page', 'instagrabber') ?></p>
					<?php else: ?>
						<?php
							$name                = false;
							$placeholder         = false;
							$backup_placeholder  = false;
							$allow_hashtags      = 0;
							$user                = get_current_user_id();
							$image_attachment    = false;
							$image_link          = 'instagram';
							$customlink_url		 = '';
							$type                = 'tag';
							$tag                 = false;
							$createpost          = 0;
							$saveimages          = 0;
							$post_type           = 'post';
							$status              = false;
							$autotag             = 0;
							$taxonomy            = 'category';
							$term                = 'none';
							$tag_tax             = 'post_tag';
							$local_tags          = '';
							$post_format         = 'standard';
							$image_size          = 'full';
							$instagrabber_admins = array(-1);
							$getToDate			= date("Y-m-d");

							

							if (isset($_GET['stream'])) {
								$stream = Database::get_stream($_GET['stream']);
								if ($stream) {
									$name                = $stream->name;
									$placeholder         = $stream->placeholder_title;
									$backup_placeholder  = $stream->backup_placeholder_title;
									$allow_hashtags      = $stream->allow_hashtags;
									$user                = $stream->created_by;
									$image_attachment    = $stream->image_attachment;
									$image_link          = $stream->image_link;
									$customlink_url		 = $stream->customlink_url;
									$type                = $stream->type;
									$tag                 = $stream->tag;
									$createpost          = $stream->auto_post;
									$saveimages          = $stream->save_images;
									$post_type           = $stream->post_type;
									$status              = $stream->post_status;
									$autotag             = $stream->auto_tags;
									$taxonomy            = $stream->taxonomy;
									$term                = $stream->tax_term;
									$tag_tax             = $stream->tags_tax;
									$local_tags          = $stream->local_tags;
									$post_format         = $stream->post_format;
									$image_size          = $stream->image_size;
									$instagrabber_admins = unserialize($stream->administrators);
									$getToDate 			= str_replace('00:00:00', "", $stream->get_to_date);
								}
							}

							$instagrabber_admins = !is_array($instagrabber_admins) ? array(-1) : $instagrabber_admins;
							$allow_hashtags = $allow_hashtags == null ? 0 : $allow_hashtags;
							$image_size = $image_size == null ? 'full' : $image_size;
							$image_size_custom = intval($image_size) != 0 ? $image_size : '';

						?>
						<form id="authform" method="post" action="admin.php?page=instagram-add">
							<input type="hidden" name="action" value="instagrabber_new_stream">
							<input type="hidden" name="instagram-add-streams" value="instagram-add-streams">
							<?php if (isset($_GET['stream'])): ?>
							<input type="hidden" name="instagram-update-stream" value="<?php echo $_GET['stream']; ?>">
							<?php endif; ?>
							<input type="hidden" name="instagram-user" value="<?php echo $user ?>">
							<table class="form-table instagrabber-edit-stream">
							<tbody>
									
									<tr valign="top">
										<th scope="row" colspan="2">
											<h2><?php _e('Stream settings', 'instagrabber') ?></h2>
											<hr>
										</th>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="name"><?php _e('Name', 'instagrabber') ?>: </label>
										</th>
										<td>
											<input type="text" class="regular-text" name="name" id="name" value="<?php echo $name ?>">
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="placeholder"><?php _e('Placeholder title for post', 'instagrabber') ?>: </label>
										</th>
										<td>
											<input type="text" class="regular-text" name="placeholder" id="placeholder" value="<?php echo $placeholder ?>"><br>
											<p><?php _e('You can also use these tags as placeholders', 'instagrabber') ?>: %user%, %tag%, %caption%, %date%</p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="backup_placeholder"><?php _e('Backup Placeholder title for post', 'instagrabber') ?><em>(<?php _e("If %caption% placeholder is used by itself.", 'instagrabber') ?>.)</em>: </label>
										</th>
										<td>
											<input type="text" class="regular-text" name="backup_placeholder" id="backup_placeholder" value="<?php echo $backup_placeholder ?>"><br>
											<p><?php _e('You can also use these tags as placeholders', 'instagrabber') ?>: %user%, %tag%, %date%</p>
										</td>
									</tr>

									<tr valign="top" class="">
										<th scope="row">
											<label for="allow_hashtags"><?php _e('Allow hashtags in titles', 'instagrabber') ?>:</label>
										</th>
										<td>
											<input type="radio" value="true" name="allow_hashtags" <?php checked(1, $allow_hashtags) ?>> <?php _e('Yes', 'instagrabber') ?>
											<br>
											<input type="radio" value="false" name="allow_hashtags" <?php checked(0, $allow_hashtags) ?>> <?php _e('No', 'instagrabber') ?>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="type">Type: </label>
										</th>
										<td>
											<select name="type" id="type">
												<option value="tag" <?php selected('tag', $type) ?>><?php _e('Tag', 'instagrabber') ?></option>
												<option value="user" <?php selected('user', $type) ?>><?php _e('User', 'instagrabber') ?></option>
												<option value="like" <?php selected('like', $type) ?>><?php _e('Likes', 'instagrabber') ?></option>
											</select>
										</td>
									</tr>
									
									<tr valign="top" class="instagrabber-tag">
										<th scope="row">
											<label for="tag"><?php _e('Tag', 'instagrabber') ?>: <em>(<?php _e('Get images by tag. Works with user streams too', 'instagrabber') ?>.)</em></label>
										</th>
										<td>
											<input type="text" class="regular-text" name="tag" id="tag" value="<?php echo $tag ?>">
										</td>
									</tr>
									
									<tr valign="top" class="instagrabber-tag">
										<th scope="row">
											<label for="getfromdate"><?php _e('Get images posted later than', 'instagrabber') ?>: </label>
										</th>
										<td>
											<input type="text" class="regular-text" name="getfromdate" id="datepicker" value="<?php echo $getToDate ?>">
										</td>
									</tr>
									
									<tr valign="top" class="instagrabber-tag">
										<th scope="row">
											<label for="createpost"><?php _e('Auto create post', 'instagrabber') ?>: </label>
										</th>
										<td>
											<input type="radio" value="true" name="createpost" <?php checked(1, $createpost) ?>> Yes
											<br>
											<input type="radio" value="false" name="createpost" <?php checked(0, $createpost) ?>> No
										</td>
									</tr>

									<?php if (get_option('instagrabber_allow_save_images') && get_option('instagrabber_allow_save_images') != "false"): ?>
									<tr valign="top" class="instagrabber-tag">
										<th scope="row">
											<label for="saveimages"><?php _e('Save images in library', 'instagrabber') ?> <em><?php _e("Only if Auto create post is set to No", 'instagrabber') ?></em>: </label>
										</th>
										<td>
											<input type="radio" value="true" name="saveimages" <?php checked(1, $saveimages) ?>> Yes
											<br>
											<input type="radio" value="false" name="saveimages" <?php checked(0, $saveimages) ?>> No
										</td>
									</tr>
									<?php endif ?>
									
									<tr valign="top">
										<th scope="row" colspan="2">
											<h2><?php _e('Post settings', 'instagrabber') ?></h2>
											<hr>
										</th>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="post_type">Post Type: </label>
										</th>
										<td id="post_type_container">
											<select name="post_type" id="post_type">
												<?php foreach (Utils::post_types() as $key => $value): ?>
													<option value="<?php echo $value ?>" <?php selected($value, $post_type) ?>><?php echo $value ?></option>
												<?php endforeach ?>
											</select>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="status"><?php _e('Post status', 'instagrabber') ?>: </label>
										</th>
										<td>
											<select name="status" id="status">
												<option value="draft" <?php selected('draft', $status) ?>><?php _e('Draft', 'instagrabber') ?></option>
												<option value="published" <?php selected('published', $status) ?>><?php _e('Publish', 'instagrabber') ?></option>
											</select>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="format"><?php _e('Post format', 'instagrabber') ?>: </label>
										</th>
										<td>
											<?php 
												//define formats
												$formats = array(
														'standard' => __('Standard', 'instagrabber'),
														'aside'    => __('Aside', 'instagrabber'),
														'gallery'  => __('Gallery', 'instagrabber'),
														'link'     => __('Link', 'instagrabber'),
														'image'    => __('Image', 'instagrabber'),
														'quote'    => __('Quote', 'instagrabber'),
														'status'   => __('Status', 'instagrabber'),
														'video'    => __('Video', 'instagrabber'),
														'audio'    => __('Audio', 'instagrabber'),
														'chat'    => __('Chat', 'instagrabber'),
													);
											 ?>
											<select name="format" id="format">
												<?php foreach ($formats as $format => $name): ?>
													<option value="<?php echo $format ?>" <?php selected($format, $post_format) ?>><?php echo $name ?></option>	
												<?php endforeach ?>
												
											</select>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="taxonomy"><?php _e('Taxonomy', 'instagrabber') ?>: </label>
										</th>
										<td id="taxonomy_container">
											<?php Utils::get_categories($post_type, $taxonomy); ?>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="term"><?php _e('Term', 'instagrabber') ?>: </label>
										</th>
										<td id="taxonomy_term_container">
											<?php Utils::get_categories_terms($taxonomy, $term); ?>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="taxonomy_tag"><?php _e('Taxonomy for tags', 'instagrabber') ?>: </label>
										</th>
										<td id="taxonomy_tag_container">
											<?php Utils::get_tags($post_type, $tag_tax); ?>
										</td>
									</tr>

									<tr valign="top" class="instagrabber-tag">
										<th scope="row">
											<label for="autotag"><?php _e('Auto set tag', 'instagrabber') ?>: <em>(<?php _e('Uses the tags from instagram', 'instagrabber') ?>)</em></label>
										</th>
										<td>
											<input type="radio" value="true" name="autotag" <?php checked(1, $autotag) ?>> <?php _e('Yes', 'instagrabber') ?>
											<br>
											<input type="radio" value="false" name="autotag" <?php checked(0, $autotag) ?>> <?php _e('No', 'instagrabber') ?>
										</td>
									</tr>
									
									<tr valign="top" class="instagrabber-tag">
										<th scope="row">
											<label for="local_tags"><?php _e('Local tags', 'instagrabber') ?> <em><?php _e('Comma separated list', 'instagrabber') ?></em>: </label>
										</th>
										<td>
											<input type="text" class="regular-text" name="local_tags" value="<?php echo $local_tags ?>">
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="image_attachment"><?php _e('Attachment type', 'instagrabber') ?>: <em>(<?php _e('How to attach the image to the post', 'instagrabber') ?>)</em></label>
										</th>
										<td>
											<select name="image_attachment" id="image_attachment">
												<option value="content" <?php selected('content', $image_attachment) ?>><?php _e('Content', 'instagrabber') ?></option>
												<option value="featured" <?php selected('featured', $image_attachment) ?>><?php _e('Featured', 'instagrabber') ?></option>
												<option value="both" <?php selected('both', $image_attachment) ?>><?php _e('Both', 'instagrabber') ?></option>
											</select>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="image_link"><?php _e('Image link', 'instagrabber') ?>: <em>(<?php _e('What link should be used for the image?', 'instagrabber') ?>)</em></label>
										</th>
										<td>
											<select name="image_link" id="image_link">
												<option value="instagram" <?php selected('instagram', $image_link) ?>><?php _e('Instagram Image', 'instagrabber') ?></option>
												<option value="user" <?php selected('user', $image_link) ?>><?php _e('Instagram user', 'instagrabber') ?></option>
												<option value="post" <?php selected('post', $image_link) ?>><?php _e('WordPress post', 'instagrabber') ?></option>
												<option value="image" <?php selected('image', $image_link) ?>><?php _e('Image link', 'instagrabber') ?></option>
												<option value="customlink" <?php selected('customlink', $image_link) ?>><?php _e('Custom link', 'instagrabber') ?></option>
												<option value="none" <?php selected('none', $image_link) ?>><?php _e('None', 'instagrabber') ?></option>
											</select>
											<p><input type="text" class="regular-text" id="customlink_url" name="customlink_url" value="<?php echo $customlink_url ?>" placeholder="<?php _e("Add your custom url here", "instagrabber") ?>"></p>
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="image_size"><?php _e('Image width in content', 'instagrabber') ?></label>
										</th>
										<td>
											<?php $sizes = Utils::get_images_sizes_width(); ?>
											<select name="image_size" id="image_size">
												<option value="0" ><?php _e('Choose a size...', 'instagrabber') ?></option>
												  <?php foreach ($sizes as $size_name => $width): ?>
												    <option value="<?php echo $size_name ?>" <?php selected($size_name, $image_size) ?>><?php echo $size_name ?> (<?php echo $width ?>px)</option>
												<?php endforeach; ?>
											</select>
											 or a custom width: <input type="number" name="image_size_custom" id="image_size_custom" size="3" min="0" max="612" value="<?php echo $image_size_custom ?>">px
										</td>
									</tr>

									<tr valign="top">
										<th scope="row" colspan="2">
											<h2><?php _e('User settings', 'instagrabber') ?></h2>
											<hr>
										</th>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="instagrabber_user"><?php _e('Change stream author', 'instagrabber') ?>: </label>
										</th>
										<td>
											<?php wp_dropdown_users(array(
											    'orderby'                 => 'display_name',
											    'order'                   => 'ASC',
											    'show'                    => 'display_name',
											    'echo'                    => true,
											    'selected'                => $user,
											    'include_selected'        => true,
											    'name'                    => 'instagrabber_user', // string
											    'id'                      => 'instagrabber_user', // integer
											    'who'                     => 'authors' // string
											)); ?>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="instagrabber_admins"><?php _e('Add stream admins', 'instagrabber') ?>: </label>
										</th>
										<td>
											<?php Utils::checkboxes_users(array(
											    'orderby'                 => 'display_name',
											    'order'                   => 'ASC',
											    'show'                    => 'display_name',
											    'echo'                    => true,
											    'selected'                => $instagrabber_admins,
											    'include_selected'        => true,
											    'name'                    => 'instagrabber_admins[]', // string
											    'id'                      => 'instagrabber_admins', // integer
											    'who'                     => 'authors', // string,
											    'class'					=> 'instagrabber_admins',
											    'show_option_none'		=> __('No extra administrators', 'instagrabber')
											)); 
											
											?>
										</td>
									</tr>
															
							</tbody>
						</table>
							<p class="">
								<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save', 'instagrabber') ?>">
							</p>
						</form>
					<?php endif ?>
				</div>
			<?php
		}


		
		function admin_settings_page(){
			$authtype = get_option('instagrabber_authtype');
			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');
			$scheduled_publication_period = get_option('instagrabber_instagram_app_scheduled');

			$tabs = array('settings' => __('Settings', 'instagrabber'), 'uninstall' =>  __('Fix or uninstall', 'instagrabber') );
			$currenttab = !isset($_GET['tab']) ? 'settings' : $_GET['tab'];

			if($authtype == false && $InstagramClientID != false){
				$authtype = 'oauth';
			}elseif($authtype == false && $InstagramClientID == false){
				$authtype = 'xauth';
			}

			?>
				<div class="wrap">
					<div id="instagrabber-icon" class="icon32"><br></div>
					<h2  class="nav-tab-wrapper"><?php _e('Instagrabber Settings', 'instagrabber') ?>
						<?php 
							 foreach( $tabs as $tab => $name ){
						        $class = ( $tab == $currenttab ) ? ' nav-tab-active' : '';
						        echo "<a class='nav-tab$class' href='?page=instagram-settings&tab=$tab'>$name</a>";

						    }
						 ?>
					</h2>
					<?php if ($currenttab == 'settings'): ?>
						
					
					<h3><?php _e('Instagram configuration', 'instagrabber') ?></h3>
					<p><?php _e('You can Use your own instagram application. Se more at the instagrabber help page.', 'instagrabber') ?></p>
										
					<form id="authform" method="post" action="admin.php?page=instagram-settings">
					<input type="hidden" name="action" value="instagrabber_auth">
					<input type="hidden" name="clientauthinfo" value="clientauthinfo">
					<table class="form-table">
					<tbody>
						
						<tr valign="top" class="authtype">
							<th scope="row">
								<label for="authorizationtype">Instagram <?php _e('Authorization type', 'instagrabber') ?></label>
							</th>
							<td>
								<select name="authorizationtype" id="authorizationtype">
									<option value="xauth" <?php selected('xauth', $authtype) ?>><?php _e('Instagrabber', 'instagrabber') ?></option>
									<option value="oauth"<?php selected('oauth', $authtype) ?>><?php _e('Your custom app', 'instagrabber') ?></option>
								</select>
							</td>
						</tr>
						
						<tr valign="top" class="oauthfield">
							<th scope="row">
								<label for="clientid">Instagram <em><?php _e('Client ID', 'instagrabber') ?></em></label>
							</th>
							<td>
								<input type="text" class="regular-text" name="clientid" id="clientid" value="<?php echo  $InstagramClientID ?>">
							</td>
						</tr>
						<tr valign="top" class="oauthfield">
							<th scope="row">
								<label for="clientsecret">Instagram <em><?php _e('Client Secret', 'instagrabber') ?></em></label>
							</th>
							<td>
								<input type="text" class="regular-text" name="clientsecret" id="clientsecret" value="<?php echo  $InstagramClientSecret ?>">
							</td>
						</tr>
				
					<tr>
							<th scope="row">
								<label><?php _e('Auto get photos', 'instagrabber') ?> <em>(<?php _e('And publish depending on stream setting', 'instagrabber') ?>)</em></label>
							</th>
							<td>
								<select name="scheduled">
									<option value="never"<?php if ($scheduled_publication_period === 'never') echo ' selected=selected'; ?>><?php _e('never', 'instagrabber') ?></option>
                                    <option value="instagrabber_fiveminutes"<?php if ($scheduled_publication_period === 'instagrabber_fiveminutes') echo ' selected=selected'; ?>><?php _e('every 5 minutes', 'instagrabber') ?></option>
                                    <option value="instagrabber_tenminutes"<?php if ($scheduled_publication_period === 'instagrabber_tenminutes') echo ' selected=selected'; ?>><?php _e('every 10 minutes', 'instagrabber') ?></option>
                                    <option value="instagrabber_twentynminutes"<?php if ($scheduled_publication_period === 'instagrabber_twentynminutes') echo ' selected=selected'; ?>><?php _e('every 20 minutes', 'instagrabber') ?></option>
                                    <option value="instagrabber_twicehourly"<?php if ($scheduled_publication_period === 'instagrabber_twicehourly') echo ' selected=selected'; ?>><?php _e('every 30 minutes', 'instagrabber') ?></option>
                                    <option value="hourly"<?php if ($scheduled_publication_period === 'hourly') echo ' selected=selected'; ?>><?php _e('hourly', 'instagrabber') ?></option>
                                    <option value="twicedaily"<?php if ($scheduled_publication_period === 'twicedaily') echo ' selected=selected'; ?>><?php _e('twice a day', 'instagrabber') ?></option>
                                    <option value="daily"<?php if ($scheduled_publication_period === 'daily') echo ' selected=selected'; ?>><?php _e('daily', 'instagrabber') ?></option>
                                    <option value="instagrabber_weekly"<?php if ($scheduled_publication_period === 'instagrabber_weekly') echo ' selected=selected'; ?>><?php _e('weekly', 'instagrabber') ?></option>
                                    <option value="instagrabber_monthly"<?php if ($scheduled_publication_period === 'instagrabber_monthly') echo ' selected=selected'; ?>><?php _e('monthly', 'instagrabber') ?></option>
                                </select>
							</td>
						</tr>

						<tr valign="top">
								<th scope="row">
									<label for="allowsave_images"><?php _e('Allow users to save images wthout creating posts?', 'instagrabber') ?></label>
								</th>
								<td>
									<?php 
										$allowsave_images = get_option('instagrabber_allow_save_images');
										$allowsave_images = $allowsave_images == false || $allowsave_images == "false" ? false : true;
									?>
									<input type="radio" name="allowsave_images" value="false" <?php checked($allowsave_images, false) ?>> <?php _e('No', 'instagrabber') ?> <br />
									<input type="radio" name="allowsave_images" value="true" <?php checked($allowsave_images, true) ?>> <?php _e('Yes', 'instagrabber') ?>
								</td>
							</tr>
						<tr valign="top">
								<th scope="row">
									<label for="titlelimit"><?php _e('The word limit for titles', 'instagrabber') ?></label>
								</th>
								<td>
									<?php 
										$titlelimit = get_option('instagrabber_title_limit');
										$titlelimit = $titlelimit == false || $titlelimit == "false" ? 10 : $titlelimit;
									?>
									<input type="number" name="titlelimit" value="<?php echo $titlelimit ?>">
								</td>
							</tr>
						<tr valign="top">
								<th scope="row">
									<label for="instagrabberlove"><?php _e('Show me some love and display a link to the plugin page for instagrabber?', 'instagrabber') ?></em></label>
								</th>
								<td>
									<?php 
										$love = get_option('instagrabberlove');
										$love = $love == false || $love == "false" ? false : true;
									?>
									<input type="radio" name="instagrabberlove" value="false" <?php checked($love, false) ?>> <?php _e('No', 'instagrabber') ?> <br>
									<input type="radio" name="instagrabberlove" value="true" <?php checked($love, true) ?>> <?php _e('Yes', 'instagrabber') ?>
								</td>
							</tr>
					</tbody>
				</table>
					<p class="submit">
						<input type="submit" name="submit" id="submit" class="button-primary" value="<?php _e('Save', 'instagrabber') ?>">
					</p>
				</form>
				
					<p><?php _e('If you want to use crontab for this plugin you can use this url', 'instagrabber') ?>: <strong><?php echo plugin_dir_url( __FILE__ ) . 'instagrabber-cron.php' ?></strong><br><strong><?php _e('Remember to set Auto get photos to never first!', 'instagrabber') ?></strong></p>
				<?php elseif ($currenttab == 'uninstall'): ?>
					<h3><?php _e('Unlock instagrabber', 'instagrabber') ?></h3>
					<p><?php _e('This will solve problems that is blocking images. Press this button to remove these locks.') ?></p>
					<p><a class="button primary" href="?page=instagram-settings&remove_locks=true" title="Removelocks"><?php _e('Remove Locks', 'instagrabber') ?></a></p>
					<h3><?php _e('Reset auto get images', 'instagrabber') ?></h3>
					<p><?php _e("Click on the button below to reset the auto get images function. Do this if your stream isn't updating", 'instagrabber') ?></p>
					<p><a class="button primary" href="?page=instagram-settings&reset_instagrabber=true" title="Reset"><?php _e('Reset auto get images', 'instagrabber') ?></a></p>
					<h3><?php _e('Uninstall instagrabber', 'instagrabber') ?></h3>
					<p><?php _e('This will remove all settings and datbases that this plugin has created.', 'instagrabber') ?></p>
					<p><a class="button primary" href="?page=instagram-settings&uninstall_instagrabber=true" title="Reset"><?php _e('Uninstall Instagrabber', 'instagrabber') ?></a></p>
					<?php endif ?>
				</div>
				<script>
					jQuery(function($){
						displayExtraFields();

						$(".authtype").on('change', '#authorizationtype', function(){
							displayExtraFields();
						});

						function displayExtraFields(){
							var fieldvalue = $("#authorizationtype").val(),
							hidden = $(".oauthfield");

							if (fieldvalue == "oauth") {
								hidden.show();
							}else{
								hidden.hide();
							}
						}
					})
				</script>
			<?php
		}


		function admin_debug_page(){
			?>
			<div class="wrap">
					<div id="instagrabber-icon" class="icon32"><br></div>
					<h2  class=""><?php _e('Instagrabber Debugger', 'instagrabber') ?></h2>
					<p>This page wont do you any good.</p>

			</div>
			<?php
		}
}

$Admin_pages = new Admin_pages;
?>