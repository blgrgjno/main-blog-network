<?php  
/**
 * Display images from a stream
 */
class Instagrabber_Published_Images extends WP_Widget {

	// Constructor //

		function Instagrabber_Published_Images() {
			parent::__construct(
		 		'Instagrabber_published_images', // Base ID
				'Instagrabber images', // Name
				array( 'description' => __( 'Display images from a stream', 'instagrabber' ) ) // Args
			)	;
		}

	// Extract Args //

		function widget($args, $instance) {
			extract( $args );
			$title 		= apply_filters('widget_title', $instance['title']); // the widget title

	// Before widget //

			echo $before_widget;

	// Title of widget //

			if ( $title ) { echo $before_title . $title . $after_title; }

	// Widget output //

			?>
			
			<?php
			if ($instance['cache'] == "true") {
				if ( false === ( $images = get_transient( $this->id) ) ) {
				    // It wasn't there, so regenerate the data and save the transient
				     $images = instagrabber_get_images($instance['stream'], array('limit' => $instance['limit'], 'images' => $instance['images'], 'random' => $instance['random']));
				     set_transient( $this->id, $images, 60 );
				}
			}else{
				$images = instagrabber_get_images($instance['stream'], array('limit' => $instance['limit'], 'images' => $instance['images'], 'random' => $instance['random']));
			}
			
			
			
			foreach ($images as $key => $image) { ?>
				<div class="instagrabber_image">
					<?php if ($instance['username'] == 'true'): ?>
						<h3><?php echo $image->user_name ?></h3>
					<?php endif ?>
					
					<?php 
						$imgsrc = $instance['imagesize'] == 'full' ? $image->pic_url : $image->pic_thumbnail;

						if ($instance['images'] == 'published' && $instance['publishedlinks'] == 'post') {
							$attachement = get_post($image->media_id);
							$imglink = get_permalink($attachement->post_parent);
						}else{
							$imglink = $image->pic_link;
						}
					 ?>

					<p><a href="<?php echo $imglink ?>" title=""><img src="<?php echo $imgsrc ?>" alt=""></a></p>
					<?php if ($instance['captions'] == 'true'): ?>
						<p><?php echo $image->caption ?></p>
					<?php endif ?>
					
				</div>
			<?php }
	// After widget //

			echo $after_widget;
		}

	// Update Settings //

		function update($new_instance, $old_instance) {
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['stream'] = strip_tags($new_instance['stream']);
			$instance['images'] = strip_tags($new_instance['images']);
			$instance['publishedlinks'] = strip_tags($new_instance['publishedlinks']);
			$instance['imagesize'] = strip_tags($new_instance['imagesize']);
			$instance['limit'] = strip_tags($new_instance['limit']);
			$instance['username'] = strip_tags($new_instance['username']);
			$instance['captions'] = strip_tags($new_instance['captions']);
			$instance['random'] = strip_tags($new_instance['random']);
			$instance['cache'] = strip_tags($new_instance['cache']);

			delete_transient( $this->id );
			return $instance;
		}

	// Widget Control Panel //

		function form($instance) {

		$defaults = array( 'title' => 'Instagram Images', 'stream' => '', 'images' => 'published', 'publishedlinks' => 'instagram', 'imagesize' => 'full', 'limit' => 5, 'username' => 'true', 'captions' => 'true', 'random' => 'false', 'cache' => 'true');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('stream'); ?>">Stream:</label>
			<select id="<?php echo $this->get_field_id('stream'); ?>" name="<?php echo $this->get_field_name('stream'); ?>" class="widefat">
			<?php 
				$streams = Database::get_streams();
				foreach ($streams as $key => $stream) { ?>
					<option value="<?php echo $stream->id ?>" <?php selected($stream->id , $instance['stream']) ?>><?php echo $stream->name; ?></option>
				<?php }
			 ?>
			 </select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('images'); ?>">Images:</label>
			<select id="<?php echo $this->get_field_id('images'); ?>" name="<?php echo $this->get_field_name('images'); ?>" class="widefat">
					<option value="published" <?php selected("published" , $instance['images']) ?>>Published</option>
					<option value="unpublished" <?php selected("unpublished" , $instance['images']) ?>>Unpublished</option>
					<option value="all" <?php selected("all" , $instance['images']) ?>>All</option>
			 </select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('publishedlinks'); ?>">Links <em>(only used if published)</em>:</label>
			<select id="<?php echo $this->get_field_id('publishedlinks'); ?>" name="<?php echo $this->get_field_name('publishedlinks'); ?>" class="widefat">
					<option value="instagram" <?php selected("instagram" , $instance['publishedlinks']) ?>>Link to Instagram</option>
					<option value="post" <?php selected("post" , $instance['publishedlinks']) ?>>Link to post</option>
			 </select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('imagesize'); ?>">Image size:</label>
			<select id="<?php echo $this->get_field_id('imagesize'); ?>" name="<?php echo $this->get_field_name('imagesize'); ?>" class="widefat">
					<option value="full" <?php selected("full" , $instance['imagesize']) ?>>Full size</option>
					<option value="thumb" <?php selected("thumb" , $instance['imagesize']) ?>>Thumbnail</option>
			 </select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>">Number of images to display:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo $instance['limit']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('username'); ?>">Display username:</label>
			<select class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>">
					<option value="true" <?php selected("true" , $instance['username']) ?>>Yes</option>
					<option value="false" <?php selected("false" , $instance['username']) ?>>No</option>
			 </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('captions'); ?>">Display captions:</label>
			<select class="widefat" id="<?php echo $this->get_field_id('captions'); ?>" name="<?php echo $this->get_field_name('captions'); ?>">
					<option value="true" <?php selected("true" , $instance['captions']) ?>>Yes</option>
					<option value="false" <?php selected("false" , $instance['captions']) ?>>No</option>
			 </select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('random'); ?>">Get random images:</label>
			<select class="widefat" id="<?php echo $this->get_field_id('random'); ?>" name="<?php echo $this->get_field_name('random'); ?>">
					<option value="false" <?php selected("false" , $instance['random']) ?>>No</option>
					<option value="true" <?php selected("true" , $instance['random']) ?>>Yes</option>
			 </select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('cache'); ?>">Use 5 minutes cache <em>(recommended)</em>:</label>
			<select class="widefat" id="<?php echo $this->get_field_id('cache'); ?>" name="<?php echo $this->get_field_name('cache'); ?>">
					<option value="true" <?php selected("true" , $instance['cache']) ?>>Yes</option>
					<option value="false" <?php selected("false" , $instance['cache']) ?>>No</option>
			 </select>
		</p>
		
        <?php }

}

?>