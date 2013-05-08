<?php
/*
 * JZSC Name: Yourls Support
 * JZSC Description: This component adds support for Yourls
 * classname: jzsc_yourls
 * version: 1.0
 * link: http://wiki.fusedthought.com
 * author: Gerald Yeo <contact@fusedthought.com>
 */






/*
 *****************************************
 *
 *	Dependencies
 *
 *****************************************
 */
if ( !class_exists('jzsc_shared') ) :
	include( dirname(__FILE__) . '/lib/jzsc.shared.php' );
endif;





/*
 *****************************************
 *
 *	Main Class Declaration
 *
 *****************************************
 */

if ( !class_exists('jzsc_yourls') ) :
	class jzsc_yourls extends jzsc_shared{




/*
 *****************************************
 *
 *	Class Variables
 *
 *****************************************
 */
		private $api_pause = false;
		private $api_config = array(
								'yourls' 	=> array(
												'name' 		=> 	'Yourls',
												'endpoint'	=> 	'',
												'format' 	=>	'simple', 
												'method'	=>	'GET',
												'type'		=> 	101,
												'sticky'	=> 	TRUE,
												),
							);


/*
 *****************************************
 *
 *	Constructors
 *
 *****************************************
 */


        //php 5.3.3
        public function __construct($service = NULL, $status = 'dev') {
            $this->jzsc_yourls($service, $status);
        }
       
        //backward compatibility
        public function jzsc_yourls($service = NULL, $status = 'dev'){
            $this->api_name = 'yourls';
			$this->loaded_api =  $this->api_config['yourls'];
			$this->status = $status;
        }	
	



/*
 *****************************************
 *
 *	Methods
 *
 *****************************************
 */



        /*
         *****************************************
         * 	Config Requirements
         *****************************************
         */
		public function set_service($service){
			parent::set_service('yourls', $this->api_config['yourls']);
		}


		public function config($key = '', $user = '', $generic = ''){
			$loaded_api = $this->api_config['yourls'];
			$loaded_api['endpoint'] = $generic['endpoint'];
			parent::config($key, $user, $loaded_api, $generic);
		}


		public function api_list(){
			return parent::api_list($this->api_config);
		}






        /*
         *****************************************
         * 	Main Generator 
         *****************************************
         */
		public function generate($url){
			$this->url = $url;

			if ($this->url){
				$request_url = $this->loaded_api['endpoint'];
				$request_url .= '?username='.$this->user.
								'&password='.$this->key.
								'&action=shorturl'.
								'&url='.rawurlencode($this->url).
								'&format='.$this->loaded_api['format'];

				$result = parent::request_gateway($request_url, FALSE, $this->loaded_api['method']);	

				//remove url request
				$this->url = '';		
				

				return $result;
			}
		}







//end class  
    }
endif;
 
?>
