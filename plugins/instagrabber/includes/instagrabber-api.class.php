<?php
class InstagrabberApi
{

	function __construct(){

	}

	static function instagrabber_getInstagramRedirectURI($stream = false)
	{
			$stream = !$stream ? '' : '&streamid='.$stream;
			if (get_option('instagrabber_authtype') && get_option('instagrabber_authtype') == 'xauth') {
				$url = self::built_in_auth_url($stream, get_bloginfo('wpurl'));
			}else{
				$url = get_bloginfo('wpurl').'/wp-admin/admin-ajax.php?action='.INSTAGRABBER_PLUGIN_CALLBACK_ACTION.$stream;	
			}
			
			return $url;
	}

	static function built_in_auth_url($stream, $siteurl){
		$stream = !$stream ? '' : '&streamid='.$stream;
		$url = 'http://johan-ahlback.com/wp-admin/admin-ajax.php?action=oauth'.$stream.'&site_url='.$siteurl;
		return apply_filters('instagrabber_external_auth_url', $url, $stream, $siteurl);
	}

		// gets Instagram login/authorization page URI
		static function instagrabber_getAuthorizationPageURI($stream = false)
		{
			if (get_option('instagrabber_authtype') && get_option('instagrabber_authtype') == 'xauth') {
				
				
				return self::built_in_auth_url($stream, get_bloginfo('wpurl'));
			}
			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');
			$InstagramRedirectURI = self::instagrabber_getInstagramRedirectURI($stream);
			
			if (empty($InstagramClientID) || empty($InstagramClientSecret) || empty($InstagramRedirectURI))
				return null;

			
			
			// API: http://instagr.am/developer/auth/
			return 'https://api.instagram.com/oauth/authorize/?client_id='.$InstagramClientID.'&redirect_uri='.urlencode($InstagramRedirectURI).'&response_type=code';
		}

		// handler for Integram redirect URI
		static function instagrabber_deal_with_instagram_auth_redirect_uri()
		{
			// API: http://instagr.am/developer/auth/
			global $wpdb;

			//if using instagrabber auth
			if( (get_option('instagrabber_authtype') && get_option('instagrabber_authtype') == 'xauth') && (isset($_REQUEST['oauth']) && $_REQUEST['oauth'] == 'external')){
				Database::update_access_token($_REQUEST['access_token'], $_REQUEST['user_id'], $_REQUEST['stream']);
				?>
				
				<script type="text/javascript">
					window.opener.location = window.opener.location;
					self.close();
				</script>
				
				<?php
				die();
			}
			$InstagramClientID = get_option('instagrabber_instagram_app_id');
			$InstagramClientSecret = get_option('instagrabber_instagram_app_secret');
			$stream = isset($_GET['streamid']) ? $_GET['streamid'] : false;
			$InstagramRedirectURI = self::instagrabber_getInstagramRedirectURI($stream);
			
			if (empty($InstagramClientID) || empty($InstagramClientSecret) || empty($InstagramRedirectURI))
				return;
				
			$auth_code = $_GET['code'];
			
			if (empty($auth_code))
			{
				print('<p>&nbsp;<br />There was a problem requesting the authorization code:</p>');
				
				$error = $_GET['error'];
				$error_reason = $_GET['error_reason'];
				$error_description = $_GET['error_description'];
				if (!empty($error) && !empty($error_reason) && !empty($error_description))
					print('<p><strong>'.$error_description.'</strong></p>');
				
				return;
				die();
			}
			
			// CURL POST request for getting the user access token from the code
			$request_uri = 'https://api.instagram.com/oauth/access_token';
			
			$data = array(	'client_id' => $InstagramClientID,
							'client_secret' => $InstagramClientSecret,
							'grant_type' => 'authorization_code',
							'redirect_uri' => $InstagramRedirectURI,
							'code' => $auth_code
							);

			$response = wp_remote_post( $request_uri, array(
				'method' => 'POST',
				'body' => $data
			    )
			);
			
			$decoded_response = json_decode($response['body']);

			if(isset($decoded_response->error_type)){
				print('<p>&nbsp;<br />There was a problem requesting the authorization code:</p>');
				print('<p>'. $decoded_response->error_message .'</p>');
				return;
				die();
			}
			// 	die();
			// get user data from the response
			$access_token = $decoded_response->access_token;
			$username = $decoded_response->user->username;
			$bio = $decoded_response->user->bio;
			$website = $decoded_response->user->website;
			$profile_picture = $decoded_response->user->profile_picture;
			$full_name = $decoded_response->user->full_name;
			$id = $decoded_response->user->id;
			
			if (!empty($access_token))
			{
				if(!isset($_GET['streamid'])){
					update_option('instagrabber_instagram_user_accesstoken', $access_token);
					update_option('instagrabber_instagram_user_username', $username);
					update_option('instagrabber_instagram_user_userid', $id);
					update_option('instagrabber_instagram_user_profilepicture', $profile_picture);
				}else{
					Database::update_access_token($access_token, $id, $_GET['streamid']);
				}
				
				?>
				
				<script type="text/javascript">
					window.opener.location = window.opener.location;
					self.close();
				</script>
				
				<?php
			}
			else{
				print('<p>There was a problem getting the required authorization!</p>');
				
			}
				

			return;
			die();
			
			// accessible with URL:
			// http://[HOST]/wp-admin/admin-ajax.php?action=instagrabber_redirect_uri
		}


		static function curl_file_get_contents($url)
		{
			
			$contents = wp_remote_get($url, array('sslverify' => false));
			return apply_filters('response_content', $contents['body']);
		}

		//this is a recursive function.
		// its a bit scray
		function get_images($url, $stream_id){
			 $file_contents = self::curl_file_get_contents($url);
			 
			 $photo_data = json_decode($file_contents);

			 if (empty($photo_data->data)) {
			 	return $photo_data;
			 }

			 $last_image = end($photo_data->data);


			 $last_image_id = $last_image->id;
			 $time = strtotime(Database::get_stream_oldest_date($stream_id));

			 if (!Database::image_in_db($last_image_id, $stream_id) && isset($photo_data->pagination->next_url) && $time <= $last_image->created_time) {
			 	$next_url = $photo_data->pagination->next_url;
			 	$images = self::get_images($next_url, $stream_id);
			 	if(is_array($images->data))
			 		$photo_data->data = array_merge($photo_data->data, $images->data);
			 }

			 return $photo_data;
		}

		static function instagrabber_getInstagramUserStream($stream)
		{

			$accessToken = $stream->access_token;
			$userid = $stream->userid;

			if (empty($accessToken))
				return null;
			$lastid = $stream->last_id != NULL ? '&min_id='.$stream->last_id : '';
			$photo_data = self::get_images('https://api.instagram.com/v1/users/'.$userid.'/media/recent/?access_token='.$accessToken.$lastid, $stream->id);

			if (empty($photo_data))
				return null;

			

			
			return $photo_data;
		}

		static function instagrabber_getInstagramUserLikeStream($stream)
		{

			$accessToken = $stream->access_token;

			if (empty($accessToken))
				return null;

			$lastid = $stream->last_id != NULL ? '&min_like_id='.$stream->last_id : '';
			$photo_data = self::get_images('https://api.instagram.com/v1/users/self/media/liked?access_token='.$accessToken.$lastid, $stream->id);

			if (empty($photo_data))
				return null;

			
			return $photo_data;
		}

		static function instagrabber_TagStream($stream)
		{
			$accessToken = $stream->access_token;
			$tag = $stream->tag;

			if (empty($accessToken))
				return null;
			$lastid = $stream->last_id != NULL ? '&min_tag_id='.$stream->last_id : '';
			$photo_data = self::get_images('https://api.instagram.com/v1/tags/'.$tag.'/media/recent?access_token='.$accessToken.$lastid, $stream->id);


			if (empty($photo_data))
				return null;
			return $photo_data;
		}
}
?>