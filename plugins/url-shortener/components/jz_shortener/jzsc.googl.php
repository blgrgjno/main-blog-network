<?php
/*
 * JZSC Name: Goo.gl Support	
 * JZSC Description: This component adds support for Google's URL Shortening Service
 * classname: jzsc_googl
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

if ( !class_exists('jzsc_googl') ) :
	class jzsc_googl extends jzsc_shared{




/*
 *****************************************
 *
 *	Class Variables
 *
 *****************************************
 */

		private $api_config = array(
								'googl' 	=> array(
												'name' 		=> 	'Goo.gl (Beta)',
												'endpoint' 	=> 	'https://www.googleapis.com/urlshortener/v1/url', 
												'format' 	=>	'json', 
												'ua'		=>	FALSE, 
												'override'	=> 	TRUE,
												'method'	=>	'POST',
												'type'		=> 	2,
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
            $this->jzsc_googl($service, $status);
        }
       
        //backward compatibility
        public function jzsc_googl($service = NULL, $status = 'dev'){
            $this->api_name = 'googl';
			$this->loaded_api =  $this->api_config['googl'];
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
			parent::set_service('googl', $this->api_config['googl']);
		}

		public function config($key = '', $user = '', $generic = ''){
			$loaded_api = $this->api_config['googl'];
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

				if ($this->key){
					$request_url = $request_url.'?key='.$this->key;
				}

				$body = '{"longUrl": "'.$this->url.'"}';	
				$post_opt['httpheader'] = array('Content-Type: application/json');
				
				$result = parent::request_gateway($request_url, TRUE, $this->loaded_api['method'], $post_opt, $body);

				if ($result){
					$result = parent::json_process($result);
					$result = $result->id;
				}

				//remove url request
				$this->url = '';

				return $result;
			}
		}




//end class  
    }
endif;
 
?>
