<?php
/*
Plugin Name: Metronet Profile Picture
Plugin URI: http://wordpress.org/extend/plugins/metronet-profile-picture/
Description: Use the native WP uploader on your user profile page.
Author: Metronet
Version: 1.0.3
Requires at least: 3.3
Author URI: http://www.metronet.no
Contributors: ronalfy, metronet
*/ 

class Metronet_Profile_Picture	{	
	
	//private
	private $admin_options = array();
	private $errors = '';
	private $plugin_url = '';
	private $plugin_dir = '';
	private $plugin_path = '';
	private $plugin_slug = 'metronet_profile_picture';
	
	/**
	* __construct()
	* 
	* Class constructor
	*
	*/
	function __construct(){
				
		$this->admin_options = $this->get_admin_options();
		$this->plugin_path = plugin_basename( __FILE__ );
		$this->plugin_url = rtrim( plugin_dir_url(__FILE__), '/' );
		$this->plugin_dir = rtrim( plugin_dir_path(__FILE__), '/' );
		
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'personal_options', array( &$this, 'insert_upload_form' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menus' ) );
		
		//Scripts
		add_action( 'admin_print_scripts-user-edit.php', array( &$this, 'print_media_scripts' ) );
		add_action( 'admin_print_scripts-profile.php', array( &$this, 'print_media_scripts' ) );
		
		//Styles
		add_action( 'admin_print_styles-user-edit.php', array( &$this, 'print_media_styles' ) );
		add_action( 'admin_print_styles-profile.php', array( &$this, 'print_media_styles' ) );
		
		//Ajax
		add_action( 'wp_ajax_metronet_add_thumbnail', array( &$this, 'ajax_add_thumbnail' ) );
		
		//User update action
		add_action( 'edit_user_profile_update', array( &$this, 'save_user_profile' ) );
		add_action( 'personal_options_update', array( &$this, 'save_user_profile' ) );
		
		//User Avatar override
		add_filter( 'get_avatar', array( &$this, 'avatar_override' ), 10, 5 );
	} //end constructor
	
	/**
	* admin_menus
	* Helper method for initializes all admin menus and registering settings
	*/
	public function admin_menus() {

	} //end admin_menus
	
	/**
	* ajax_add_thumbnail()
	*
	* Adds a thumbnail to user meta and returns thumbnail html
	*
	*/
	public function ajax_add_thumbnail() {
		$post_id = isset( $_POST[ 'post_id' ] ) ? absint( $_POST[ 'post_id' ] ) : 0;
		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : 0;
		$thumbnail_id = isset( $_POST[ 'thumbnail_id' ] ) ? absint( $_POST[ 'thumbnail_id' ] ) : 0;
		if ( $post_id == 0 || $user_id == 0 || $thumbnail_id == 0 ) die( '' );
		
		//Save user meta
		update_user_meta( $user_id, 'metronet_post_id', $post_id );
		
		//Form upload link
		$upload_url = admin_url( 'media-upload.php' );
		$query_args = array(
			'user_id' => $user_id,
			'post_id' => $post_id,
			'tab' => 'gallery',
			'TB_iframe' => "1",
			'width' => "640",
			'height' => "425"
		);
		$upload_url = esc_url( add_query_arg( $query_args, $upload_url ) );
		if ( has_post_thumbnail( $post_id ) ) {
			$post_thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail' );
			$return_html = sprintf( "<a href='%s' class='thickbox add_media'>%s</a>", $upload_url, $post_thumbnail );
			die( $return_html );
		}
		die( '' );
	} //end ajax_add_thumbnail
	
	/**
	* avatar_override()
	*
	* Overrides an avatar with a profile image
	*
	* @param string $avatar SRC to the avatar
	* @param mixed $id_or_email 
	* @param int $size Size of the image
	* @param string $default URL to the default image
	* @param string $alt Alternative text
	**/
	public function avatar_override( $avatar, $id_or_email, $size, $default, $alt ) {
		//Get user data
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', ( int )$id_or_email );
		} elseif( is_object( $id_or_email ) )  {
			$comment = $id_or_email;
			if ( empty( $comment->user_id ) ) {
				$user = get_user_by( 'id', $comment->user_id );
			} else {
				$user = get_user_by( 'email', $comment->comment_author_email );
			}
			if ( !$user ) return $avatar;
		} elseif( is_string( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		} else {
			return $avatar;
		}
		if ( !$user ) return $avatar;
		$user_id = $user->ID;
				
		//Determine if user has an avatar override
		$avatar_override = get_user_meta( $user_id, 'metronet_avatar_override', true );
		if ( !$avatar_override || $avatar_override != 'on' ) return $avatar;
		
		//Determine if the user has a profile image
		
		$custom_avatar = mt_profile_img( $user_id, array( 
			'size' => array( $size, $size ), 
			'attr' => array( 'alt' => $alt, 'class' => "avatar avatar-{$size} photo" ), 
			'echo' => false )
		);
		
		if ( !$custom_avatar ) return $avatar; 
		return $custom_avatar;	
	} //end avatar_override
	
	/**
	* get_admin_option()
	* 
	* Returns a localized admin option
	*
	* @param   string    $key    Admin Option Key to Retrieve
	* @return   mixed                The results of the admin key.  False if not present.
	*/
	public function get_admin_option( $key = '' ) {			
		$admin_options = $this->get_admin_options();
		if ( array_key_exists( $key, $admin_options ) ) {
			return $admin_options[ $key ];
		}
		return false;
	}
	
	/**
	* get_admin_options()
	* 
	* Initialize and return an array of all admin options
	*
	* @return   array					All Admin Options
	*/
	public function get_admin_options( ) {
		
		if (empty($this->admin_options)) {
			$admin_options = $this->get_plugin_defaults();
			
			$options = get_option( $this->plugin_slug );
			if (!empty($options)) {
				foreach ($options as $key => $option) {
					if (array_key_exists($key, $admin_options)) {
						$admin_options[$key] = $option;
					}
				}
			}
			
			//Save the options
			$this->admin_options = $admin_options;
			$this->save_admin_options();								
		}
		return $this->admin_options;
	} //end get_admin_options
	
	/**
	* get_all_admin_options()
	* 
	* Returns an array of all admin options
	*
	* @return   array					All Admin Options
	*/
	public function get_all_admin_options() {
		return $this->admin_options;
	}
		
	/**
	* get_plugin_defaults()
	* 
	* Returns an array of default plugin options (to be stored in the options table)
	*
	* @return		array               Default plugin keys
	*/
	public function get_plugin_defaults() {
		if ( isset( $this->defaults ) ) {
			return $this->defaults;
		} else {
			$this->defaults = array(
			);
			return $this->defaults;
		}
	} //end get_plugin_defaults
	
	/**
	* get_plugin_dir()
	* 
	* Returns an absolute path to a plugin item
	*
	* @param		string    $path	Relative path to make absolute (e.g., /css/image.png)
	* @return		string               An absolute path (e.g., /htdocs/ithemes/wp-content/.../css/image.png)
	*/
	public function get_plugin_dir( $path = '' ) {
		$dir = $this->plugin_dir;
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;		
	} //end get_plugin_dir
	
	
	/**
	* get_plugin_url()
	* 
	* Returns an absolute url to a plugin item
	*
	* @param		string    $path	Relative path to plugin (e.g., /css/image.png)
	* @return		string               An absolute url (e.g., http://www.domain.com/plugin_url/.../css/image.png)
	*/
	public function get_plugin_url( $path = '' ) {
		$dir = $this->plugin_url;
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;	
	} //get_plugin_url
		
	/**
	* init()
	* 
	* Initializes plugin localization, post types, updaters, plugin info, and adds actions/filters
	*
	*/
	function init() {		
		
		//* Localization Code */
		load_plugin_textdomain( 'metronet_profile_picture', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		//Register post types
		$post_type_args = array(
			'public' => false,
			'publicly_queryable' => true,
			'show_ui' => false,
			'show_in_menu' => false,
			'query_var' => true,
			'rewrite' => false,
			'has_archive' => false,
			'hierarchical' => false,
			'supports' => array( 'thumbnail' )
		);
		register_post_type( 'mt_pp', $post_type_args );
		
	}//end function init
	
	/**
	* insert_upload_form
	*
	* Adds an upload form to the user profile page and outputs profile image if there is one
	*/
	public function insert_upload_form() {
		//Get user ID
		$user_id = isset( $_GET[ 'user_id' ] ) ? absint( $_GET[ 'user_id' ] ) : 0;
		if ( $user_id == 0 && IS_PROFILE_PAGE ) {
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
		}
		
		//Get/Create Profile Picture Post
		$post_args = array(
			'post_type' => 'mt_pp',
			'author' => $user_id, 
			'post_status' => 'publish'
		);
		$posts = get_posts( $post_args );
		if ( !$posts ) {
			$post_id = wp_insert_post( array(
				'post_author' => $user_id,
				'post_type' => 'mt_pp',
				'post_status' => 'publish',
			) );
		} else {
			$post = end( $posts );
			$post_id = $post->ID;
		}
		
		//Form upload link
		$upload_url = admin_url( 'media-upload.php' );
		$query_args = array(
			'user_id' => absint( $user_id ),
			'post_id' => $post_id,
			'tab' => 'gallery',
			'TB_iframe' => "1",
			'width' => "640",
			'height' => "425"
		);
		$upload_url = esc_url( add_query_arg( $query_args, $upload_url ) );
		
		//Create upload link
		$upload_link = sprintf( "<a href='%s' class='thickbox add_media'>%s</a>", $upload_url, esc_html__( "Upload or Change Profile Picture", 'metronet_profile_picture' ) );
		?>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( "Profile Image", "metronet_profile_picture" ); ?></th>
			<td>
				<input type="hidden" name="metronet_profile_id" id="metronet_profile_id" value="<?php echo esc_attr( $user_id ); ?>" />
				<input type="hidden" name="metronet_post_id" id="metronet_post_id" value="<?php echo esc_attr( $post_id ); ?>" />
				<div id="metronet-profile-image">
				<?php
					if ( has_post_thumbnail( $post_id ) ) {
						$post_thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail' );
						printf( "<a href='%s' class='thickbox add_media'>%s</a>", $upload_url, $post_thumbnail );
					}
				?>
				</div><!-- #metronet-profile-image -->
				<div id="metronet-upload-link"><?php echo $upload_link; ?> - <span class="description"><?php esc_html_e( 'Select "Use as featured image" after uploading to choose the profile image', 'metronet_profile_picture' ); ?></span></div><!-- #metronet-upload-link -->
				<div id="metronet-override-avatar">
					<input type="hidden" name="metronet-user-avatar" value="off" />
					<input type="checkbox" name="metronet-user-avatar" id="metronet-user-avatar" value="on" <?php checked( "on", get_user_meta( $user_id, 'metronet_avatar_override', true ) ); ?> /><label for="metronet-user-avatar"> <?php esc_html_e( "Override Avatar?", "metronet_profile_picture" ); ?></label>
				</div><!-- #metronet-override-avatar -->
			</td>
		</tr>
		<?php
	} //end insert_upload_form
	
	/**
	* print_media_scripts
	*
	* Output media scripts for thickbox and media uploader
	**/
	public function print_media_scripts() {
		wp_enqueue_script( 'mt-pp', $this->get_plugin_url( '/js/mpp.js' ), array( 'media-upload', 'thickbox' ) );
	} //end print_media_scripts
	
	public function print_media_styles() {
		wp_enqueue_style( 'thickbox' );
	} //end print_media_styles
	
	/**
	* save_admin_option()
	* 
	* Saves an individual option to the options array
	* @param		string    	$key		Option key to save
	* @param		mixed		$value	Value to save in the option	
	*/
	public function save_admin_option( $key = '', $value = '' ) {
		$this->admin_options[ $key ] = $value;
		$this->save_admin_options();
		return $value;
	} //end save_admin_option
	
	/**
	* save_admin_options()
	* 
	* Saves a group of admin options to the options table
	* @param		array    	$admin_options		Optional array of options to save (are merged with existing options)
	*/
	public function save_admin_options( $admin_options = false ){
		if (!empty($this->admin_options)) {
			if ( is_array( $admin_options ) ) {
				$this->admin_options = wp_parse_args( $admin_options, $this->admin_options );
			}
			update_option( $this->plugin_slug, $this->admin_options);
		}
	} //end save_admin_options
	
	/**
	* save_user_profile()
	*
	* Saves user profile fields
	* @param int $user_id 
	**/
	public function save_user_profile( $user_id ) {
		if ( !isset( $_POST[ 'metronet-user-avatar' ] ) ) return;
		check_admin_referer( 'update-user_' . $user_id );
		
		$user_avatar = $_POST[ 'metronet-user-avatar' ];
		if ( $user_avatar == 'on' ) {
			update_user_meta( $user_id, 'metronet_avatar_override', 'on' );
		} else {
			delete_user_meta( $user_id, 'metronet_avatar_override' );
		}
	} //end save_user_profile
	
} //end class
//instantiate the class
global $mt_pp;
if (class_exists('Metronet_Profile_Picture')) {
	if (get_bloginfo('version') >= "3.0") {
		add_action( 'plugins_loaded', 'mt_mpp_instantiate' );
	}
}
function mt_mpp_instantiate() {
	global $mt_pp;
	$mt_pp = new Metronet_Profile_Picture();
}
/**
* mt_profile_img
* 
* Adds a profile image
*
@param $user_id INT - The user ID for the user to retrieve the image for
@ param $args mixed
	size - string || array (see get_the_post_thumbnail)
	attr - string || array (see get_the_post_thumbnail)
	echo - bool (true or false) - whether to echo the image or return it
*/



function mt_profile_img( $user_id, $args = array() ) {
	$profile_post_id = absint( get_user_meta( $user_id, 'metronet_post_id', true ) );
	
	$defaults = array(
		'size' => 'thumbnail',
		'attr' => '',
		'echo' => true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	//Return false or echo nothing if there is no post thumbnail
	if( !has_post_thumbnail( $profile_post_id ) ) {
		if ( $echo ) echo '';
		else return false;
		return;
	}
	$post_thumbnail = get_the_post_thumbnail( $profile_post_id, $size, $attr );
	if ( $echo ) {
		echo $post_thumbnail;
	} else {
		return $post_thumbnail;
	}
} //end mt_profile_img
?>