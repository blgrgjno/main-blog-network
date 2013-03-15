<?php

class igpPluginUpdater {

  var $api_url;
  var $plugin_slug;

  function __construct( $api_url, $plugin_slug = '' ) {
    $this->api_url = $api_url;
    $this->plugin_slug = basename(dirname(__FILE__)); // Assumes dir name == file name
    if( $plugin_slug ) $this->plugin_slug = $plugin_slug;

    add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_for_plugin_update'));
    add_filter('plugins_api', array(&$this, 'plugin_api_call'), 10, 3);
    
    // TEMP: Enable update check on every request. Normally you don't need this! This is for testing only!
    //set_site_transient('update_plugins', null);

    // TEMP: Show which variables are being requested when query plugin API
    //add_filter('plugins_api_result', array(&$this, 'debug_result'), 10, 3);
  }

  function check_for_plugin_update( $checked_data ) {
    if(empty($checked_data->checked))
      return $checked_data;
    
    $request_args = array(
      'slug' => $this->plugin_slug,
      'version' => $checked_data->checked[$this->plugin_slug .'/'. $this->plugin_slug .'.php'],
    );
    
    $request_string = $this->prepare_request('basic_check', $request_args);
    
    // Start checking for an update
    $raw_response = wp_remote_post($this->api_url, $request_string);
    
    $response = null;
    if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
      $response = unserialize($raw_response['body']);
    
    if( is_object($response) && !empty($response) ) // Feed the update data into WP updater
      $checked_data->response[$this->plugin_slug .'/'. $this->plugin_slug .'.php'] = $response;

    return $checked_data;
  }

  function plugin_api_call( $def, $action, $args ) {
    if( !isset($args->slug) || $args->slug != $this->plugin_slug )
		return $def; 
	
	// Get the current version
    $plugin_info = get_site_transient('update_plugins');
    $current_version = $plugin_info->checked[$this->plugin_slug .'/'. $this->plugin_slug .'.php'];
    $args->version = $current_version;
    
    $request_string = $this->prepare_request($action, $args);
    
    $request = wp_remote_post($this->api_url, $request_string);
    
    if( is_wp_error($request) ){
      $res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
    } else {
      $res = unserialize($request['body']);
      
      if ($res === false)
        $res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
    }
    
    return $res;
  }


  function prepare_request( $action, $args ) {
    global $wp_version;
    
    return array(
      'body' => array(
        'action' => $action, 
        'request' => serialize($args),
        'api-key' => md5(get_bloginfo('url'))
      ),
      'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
    );  
  }

  function debug_result( $res, $action, $args ) {
    echo '<pre>'.print_r($res,true).'</pre>';
    return $res;
  }

}

?>