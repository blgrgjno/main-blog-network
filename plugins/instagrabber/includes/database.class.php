<?php
class Database
{
	function __construct(){

	}

	function insert_stream($args){
		global $wpdb;
		$defaults = array(
			'name'                     => '',
			'type'                     => '',
			'tag'                      => '',
			'access_token'             => '',
			'auto_post'                => 0,
			'save_images'              => 0,
			'auto_tags'                => 0,
			'post_type'                => 'post',
			'post_status'              => 'draft',
			'placeholder_title'        => 'Instagram image',
			'backup_placeholder_title' => '',
			'allow_hashtags'            => 0,
			'created_by'               => NULL,
			'image_attachment'         => 'content',
			'image_link'				=> 'instagram',
			'customlink_url'			=> '',
			'taxonomy'                 => 'none',
			'tax_term'                 => 'none',
			'tags_tax'                 => 'none',
			'local_tags'				=> '',
			'post_format'              => 'standard',
			'image_size'               => 'full',
			'administrators'      		=> serialize(array(-1)),
			'get_to_date'				=> date("Y-m-d") . ' 00:00:00'
		);
		$args = wp_parse_args( $args, $defaults );

		extract( $args, EXTR_SKIP );
		$userid = $type == 'user' ? get_option('instagrabber_instagram_user_userid') : NULL;
		$data = array(
				'name'                     => esc_attr($name),
				'type'                     => esc_attr($type),
				'tag'                      => esc_attr($tag),
				'access_token'             => esc_attr($access_token),
				'auto_post'                => esc_attr($auto_post),
				'save_images'              => esc_attr($save_images),
				'auto_tags'                => esc_attr($auto_tags),
				'post_type'                => esc_attr($post_type),
				'post_status'              => esc_attr($post_status),
				'placeholder_title'        => esc_attr($placeholder_title),
				'backup_placeholder_title' => esc_attr($backup_placeholder_title),
				'allow_hashtags'           => esc_attr($allow_hashtags),
				'created_by'               => esc_attr($created_by),
				'image_attachment'         => esc_attr($image_attachment),
				'image_link'               => esc_attr($image_link),
				'customlink_url'			=> esc_attr($customlink_url),
				'taxonomy'                 => esc_attr($taxonomy),
				'tax_term'                 => esc_attr($tax_term),
				'tags_tax'                 => esc_attr($tags_tax),
				'local_tags'				=> esc_attr($local_tags),
				'post_format'              => esc_attr($post_format),
				'image_size'               => esc_attr($image_size),
				'administrators'           => serialize($administrators),
				'get_to_date'				=> esc_attr($get_to_date) . ' 00:00:00'
				);

		$format = array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
			);

		$wpdb->insert($wpdb->prefix . 'instagrabber_streams', $data, $format);
		return $wpdb->insert_id;
	}

	function update_stream($args, $id){
		global $wpdb;
		$defaults = array(
			'name'                     => '',
			'type'                     => '',
			'tag'                      => '',
			'access_token'             => '',
			'auto_post'                => 0,
			'save_images'              => 0,
			'auto_tags'                => 0,
			'post_type'                => 'post',
			'post_status'              => 'draft',
			'placeholder_title'        => 'Instagram image',
			'backup_placeholder_title' => '',
			'allow_hashtags'           => 0,
			'created_by'               => NULL,
			'image_attachment'         => 'content',
			'image_link'               => 'instagram',
			'customlink_url'			=> '',
			'taxonomy'                 => 'none',
			'tax_term'                 => 'none',
			'tags_tax'                 => 'none',
			'local_tags'				=> '',
			'post_format'              => 'standard',
			'image_size'               => 'full',
			'administrators'           => serialize(array(-1)),
			'error'                    => 0,
			'error_message'            => '',
			'get_to_date'				=> date("Y-m-d") . ' 00:00:00'
		);
		$args = wp_parse_args( $args, $defaults );

		extract( $args, EXTR_SKIP );

		$data = array(
				'name'                     => $name,
				'type'                     => $type,
				'tag'                      => $tag,
				'access_token'             => esc_attr($access_token),
				'auto_post'                => esc_attr($auto_post),
				'save_images'              => esc_attr($save_images),
				'auto_tags'                => esc_attr($auto_tags),
				'post_type'                => esc_attr($post_type),
				'post_status'              => esc_attr($post_status),
				'placeholder_title'        => esc_attr($placeholder_title),
				'backup_placeholder_title' => esc_attr($backup_placeholder_title),
				'allow_hashtags'           => esc_attr($allow_hashtags),
				'created_by'               => esc_attr($created_by),
				'image_attachment'         => esc_attr($image_attachment),
				'image_link'               => esc_attr($image_link),
				'customlink_url'			=> esc_attr($customlink_url),
				'taxonomy'                 => esc_attr($taxonomy),
				'tax_term'                 => esc_attr($tax_term),
				'tags_tax'                 => esc_attr($tags_tax),
				'local_tags'				=> esc_attr($local_tags),
				'post_format'              => esc_attr($post_format),
				'image_size'               => esc_attr($image_size),
				'administrators'           => serialize($administrators),
				'get_to_date'				=> esc_attr($get_to_date) . ' 00:00:00',
				'error'                    => esc_attr($error),
				'error_message'            => esc_attr($error_message)
				);

		$format = array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%d'
			);

		$where = array(
				'id' => $id
			);
		$whereformat = array(
				'%d'
			);
		$wpdb->update($wpdb->prefix . 'instagrabber_streams', $data, $where, $format, $whereformat);
	}

	function get_streams($args = array(), $limit_user = false ){
		global $wpdb, $current_user;
        $current_user = wp_get_current_user();
		$defaults = array(
			'order_by' => 'name',
			'order' => 'ASC',
			'return' => OBJECT
		);
		$args = wp_parse_args( $args, $defaults );
		$table = $wpdb->prefix . 'instagrabber_streams';
		$order_by = isset($_GET['orderby']) ? $_GET['orderby'] : $args['order_by'];
		$where = '';
		if ($limit_user && !current_user_can('manage_options')) {
			$where = " WHERE created_by = $current_user->ID OR administrators LIKE '%\"$current_user->ID\"%'";
		}
		$order = isset($_GET['order']) ? $_GET['order'] : $args['order'];
		$streams = $wpdb->get_results("SELECT * FROM {$table}{$where} ORDER BY $order_by $order",$args['return']);
		
		return $streams;

	}

	function get_stream($id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_streams';
		$streams = $wpdb->get_results("SELECT * FROM $table WHERE id = $id");
		return $streams[0];
	}

	function get_stream_oldest_date($id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_streams';
		$streams = $wpdb->get_results("SELECT get_to_date FROM $table WHERE id = $id");
		return $streams[0]->get_to_date;
	}

	function delete_stream($id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_streams';
		$where = is_array($id) ? 'IN (' . implode(', ', $id) . ')' : '= '. $id;
		$wpdb->query("DELETE FROM $table
			 WHERE id $where" );

		$imgtable = $wpdb->prefix . 'instagrabber_images';
		$wpdb->query("DELETE FROM $imgtable
			 WHERE stream $where");
	}
	function get_access_token($stream_id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_streams';
		$streams = $wpdb->get_results("SELECT access_token FROM $table WHERE id = $stream_id");
		return $streams[0]->access_token;
	}
	function update_access_token($token, $userid, $stream){
		global $wpdb;

		$data = array(
				'access_token' => $token,
				'userid' => $userid
				);

		$format = array(
				'%s',
				'%d'
			);

		$where = array(
				'id' => $stream
			);
		$whereformat = array(
				'%d'
			);
		$wpdb->update($wpdb->prefix . 'instagrabber_streams', $data, $where, $format, $whereformat);
	}

	function image_in_db($image_id, $stream_id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_images';
		$imageindb = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE stream = %d AND pic_id = '$image_id';",
				$stream_id ) );
			
			if($imageindb)
				return true;

			return false;
	}

	function save_images_in_database($stream,$images){
		global $wpdb;
		$last_id = 0;
		$stream_id = $stream->id;
		//$images = array_reverse($images);
		$table = $wpdb->prefix . 'instagrabber_images';

		$images = !is_array($images) ? array() : $images;

		//reverse images
		$images = array_reverse($images);

		foreach ($images as $key => $image) {
			
			//if this is a user stream with a tag and the image doesnt has that tag.
			// need to make this check better! 
			if(($stream->type == 'user' && $stream->tag != '') && (!in_array($stream->tag, $image->tags))){
				//set last id
				$last_id = $image->id;
				
				continue;
			}

			$imageindb = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table WHERE stream = %d AND pic_id = '$image->id';",
				$stream_id
			 ) );
			
			if($imageindb)
				continue;

			if(empty($image->link))
				continue;
			
			$caption = isset($image->caption) && !empty($image->caption) ? $image->caption->text : '';
			
			$location = empty($image->location) ? new stdClass() : $image->location;
			
			$filter = empty($image->filter) ? '' : $image->filter;

				if(!isset($location->latitude))
					$location->latitude = '';
				if(!isset($location->longitude))
					$location->longitude = '';
				if(!isset($location->id))
					$location->id = '';
				if(!isset($location->street_address))
					$location->street_address = '';
				if(!isset($location->name))
					$location->name = '';
			
			
			
			$data = array(
					'stream'        => $stream_id,
					'pic_id'        => $image->id,
					'pic_url'       => $image->images->standard_resolution->url,
					'pic_thumbnail' => $image->images->thumbnail->url,
					'pic_link'      => $image->link,
					'pic_timestamp' => date( 'Y-m-d H:i:s', $image->created_time),
					'caption'       => $caption,
					'tags'          => trim(serialize($image->tags)),
					'comment_count' => $image->comments->count,
					'like_count'    => $image->likes->count,
					'published'     => 0,
					'media_id'      => 0,
					'user_name'		=> $image->user->username,
					'user_id'		=> $image->user->id,
					'filter'		=> $filter,
					'location'		=> serialize($location),
				);

			$format = array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%s',
					'%s'
				);
			//$wpdb->hide_errors();
			$wpdb->insert($table, $data, $format);
			//$wpdb->show_errors();
			$last_id = $image->id;
		}
		
		if($last_id){
			
			$wpdb->update( 
			$wpdb->prefix . 'instagrabber_streams', 
				array( 
					'last_id' => $last_id
				), 
				array( 'id' => $stream_id ), 
				array( 
					'%s'
				), 
				array( '%d' ) 
			);
		}
		
	}

	function get_not_saved_images($stream_id,$args = array()){
		global $wpdb;
		$defaults = array(
			'order_by' => 'id',
			'order' => 'ASC',
			'return' => OBJECT
		);
		$args = wp_parse_args( $args, $defaults );
		$table = $wpdb->prefix . 'instagrabber_images';
		$order_by = isset($_GET['orderby']) ? $_GET['orderby'] : $args['order_by'];
		$order = isset($_GET['order']) ? $_GET['order'] : $args['order'];
		$streams = $wpdb->get_results("SELECT * FROM $table WHERE stream = $stream_id AND media_id = 0 ORDER BY $order_by $order",$args['return']);

		return $streams;
	}

	function get_unpublished_images($stream_id,$args = array()){
		global $wpdb;
		$defaults = array(
			'order_by' => 'id',
			'order' => 'ASC',
			'return' => OBJECT
		);
		$args = wp_parse_args( $args, $defaults );
		$table = $wpdb->prefix . 'instagrabber_images';
		$order_by = isset($_GET['orderby']) ? $_GET['orderby'] : $args['order_by'];
		$order = isset($_GET['order']) ? $_GET['order'] : $args['order'];
		$streams = $wpdb->get_results("SELECT * FROM $table WHERE stream = $stream_id AND published = 0 ORDER BY $order_by $order",$args['return']);

		return $streams;
	}

	function update_image_to_published($id, $image_id){
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'instagrabber_images', array('published' => 1, 'media_id' => $image_id), array('id' => $id), array('%d','%d'), array('%d'));
	}

	function update_media_id($id, $media_id){
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'instagrabber_images', array('media_id' => $media_id), array('id' => $id), array('%d'), array('%d'));
	}

	function remove_media_id($media_id){
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'instagrabber_images', array('media_id' => 0), array('media_id' => $media_id), array('%d'), array('%d'));
	}

	function get_images_by_id($id){

		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_images';

		$where = is_array($id) ? 'IN (' . implode(', ', $id) . ')' : '= '. $id;
		$images = $wpdb->get_results("SELECT * FROM $table WHERE id ".$where." ORDER BY id DESC");

		if(is_array($id)){
			return $images;
		}else{
			return $images[0];
		}
	}

	function get_image_by_pic_id($pic_id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_images';
		$image = $wpdb->get_results("SELECT * FROM $table WHERE pic_id = '". $pic_id ."' ORDER BY id DESC");
		return $image[0];
	}

	function get_images($stream_id, $args = array()){
		global $wpdb;
		$defaults = array(
			'order_by' => 'id',
			'order' => 'DESC',
			'where' => array(),
			'return' => OBJECT,
			'images' => 'all',
			'random' => false,
			'limit' => NULL
		);
		$args = wp_parse_args( $args, $defaults );
		$table = $wpdb->prefix . 'instagrabber_images';
		$order_by = isset($_GET['orderby']) ? $_GET['orderby'] : $args['order_by'];
		$order = isset($_GET['order']) ? $_GET['order'] : $args['order'];

		if($args["random"] === true || $args["random"] === "true" ){
			$order_by = "RAND()";
			$order = "";
		}

		$where = $args['where'];
		$published = '';
		
		if($args['images'] != 'all' && $args['images'] == 'published'){
			$published = 'AND published = 1';
		}else if($args['images'] != 'all' && $args['images'] == 'unpublished'){
			$published = 'AND published = 0';
		}

		$limit = '';
		if($args['limit'] != NULL){
			$limit = 'LIMIT ' . $args['limit'];
		}

		$streams = $wpdb->get_results("SELECT * FROM $table WHERE stream = $stream_id $published ORDER BY $order_by $order $limit",$args['return']);
		return $streams;
	}

	function count_images($stream){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_images';
		return $wpdb->query("SELECT id, count(stream) FROM $table WHERE stream = $stream->id GROUP BY id");
	}

	function count_published_images($stream){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_images';
		return $wpdb->query("SELECT id, count(stream) FROM $table WHERE stream = $stream->id AND published = 1 GROUP BY id");
	}

	function count_new_images($stream){
		global $wpdb;
		$current_user = wp_get_current_user();
		$lastseen = get_user_meta($current_user->ID, 'instagrabber_last_seen_'.$stream->id, true);
		
		if (!$lastseen) {
			$lastseen = date("Y-m-d H:i:s");
		}
		$table = $wpdb->prefix . 'instagrabber_images';
		return $wpdb->query("SELECT time_added, count(time_added), date_format(time_added, '%r') FROM $table WHERE stream = $stream->id AND time_added > '$lastseen' GROUP BY id");
	}

	function delete_image($id){
		global $wpdb;
		$table = $wpdb->prefix . 'instagrabber_images';
		$wpdb->query("DELETE FROM $table WHERE id = $id");
	}

	function drop_tables(){
		global $wpdb;
		$streamtable = $wpdb->prefix . 'instagrabber_streams';
		$imagetable = $wpdb->prefix . 'instagrabber_images';
		$wpdb->query("DROP TABLE $streamtable;");
		$wpdb->query("DROP TABLE $imagetable;");
	}

	function delete_images(){
		global $wpdb;
		$imagetable = $wpdb->prefix . 'instagrabber_images';
		$wpdb->query("DELETE FROM $table WHERE id = $id");
	}
}
?>