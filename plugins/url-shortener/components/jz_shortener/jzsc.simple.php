<?php
/*
 * JZSC Name: Component Pack 1	
 * JZSC Description: This component adds support for multiple shortlinks services
 * classname: jzsc_simple
 * version: 1.0
 * link: http://wiki.fusedthought.com
 * author: Gerald Yeo <contact@fusedthought.com>
 */



/*
	Full Description:
		Contains the more straight forward shortening services... 
		Mostly GET request based services
		Results by these services are mostly plain text
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

if ( !class_exists('jzsc_simple') ) :
	class jzsc_simple extends jzsc_shared{




/*
 *****************************************
 *
 *	Class Variables
 *
 *****************************************
 */

		private $api_config = array(
								'isgd' 		=> array(
												'name' 		=> 	'is.gd',
												'endpoint' 	=> 	'http://is.gd/api.php?longurl=[url]', 
												'format' 	=>	'', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	0,
												'sticky'	=> 	TRUE,
												),
								'supr'		=> array(
												'name' 		=> 	'Su.pr (by StumbleUpon)',
												'endpoint' 	=> 	'http://su.pr/api/shorten?longUrl=[url]', 
												'format' 	=>	'json', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	'sub',
												),
								'tinyurl' 	=> array(
												'name' 		=> 	'TinyURL',
												'endpoint' 	=> 	'http://tinyurl.com/api-create.php?url=[url]', 
												'format' 	=>	'', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	0,
												'sticky'	=> 	TRUE,
												),
								'interdose'		=> array(
												'name' 		=> 	'Interdose',
												'endpoint' 	=> 	'http://api.interdose.com/api/shorturl/v1/shorten?service=[service]&url=[url]', 
												'format' 	=>	'txt', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	'sub',
												),
								'shortie'	=> array(
												'name' 		=> 'short.ie',
												'endpoint' 	=> 'http://short.ie/api?url=[url]', 
												'format' 	=>	'', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	NULL, //0, Service seems down
												),
								'chilpit' 	=> array(
												'name' 		=> 	'Chilp it',
												'endpoint' 	=> 	'http://chilp.it/api.php?url=[url]', 
												'format' 	=>	'', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	0,
												),
								'smsh' 		=> array(
												'name' 		=> 	'sm00sh / smsh.me',
												'endpoint' 	=> 	'http://smsh.me/?api=json&url=[url]', 
												'format' 	=>	'json', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	0,
												),
								'unfakeit' 	=> array(
												'name' 		=> 	'unfake.it',
												'endpoint' 	=> 	'http://unfake.it/?a=api&url=[url]', 
												'format' 	=>	'', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	0,
												),
								'voizle' 	=> array(
												'name' 		=> 	'Voizle',
												'endpoint' 	=> 	'http://api.voizle.com/?crawl=no&type=all&property=voizleurl&u=[url]', 
												'format' 	=>	'', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	0,
												),
								'urlinl' 	=> array(
												'name' 		=> 	'urli.nl',
												'endpoint' 	=> 	'http://urli.nl/api.php?format=simple&action=shorturl&url=[url]', 
												'format' 	=>	'', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	0,
												),
								'tynie' 	=> array(
												'name' 		=> 	'tynie.net',
												'endpoint' 	=> 	'http://tynie.net/maketynie.php?api=[url]', 
												'format' 	=>	'', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	0,
												),
								'cligs'		=> array(
												'name' 		=> 	'Cli.gs',
												'endpoint' 	=> 	'http://cli.gs/api/v1/cligs/create?url=[url]', 
												'format' 	=>	'', 
												'ua'		=>	FALSE, 
												'method'	=>	'GET',
												'type'		=> 	'sub',
												),
							);
		private $ua_string = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';


/*
 *****************************************
 *
 *	Constructors
 *
 *****************************************
 */


        //php 5.3.3
        public function __construct($service = NULL, $status = 'dev') {
            $this->jzsc_simple($service, $status);
        }
       
        //backward compatibility
        public function jzsc_simple($service = NULL, $status = 'dev'){
			if ( !empty($service) ){
            	$this->api_name = $service;
				$this->loaded_api =  $this->api_config[$service];
			}
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
			parent::set_service($service, $this->api_config[$service]);
		}


		public function config($key = '', $user = '', $generic = ''){
			$loaded_api = $this->api_config[$this->api_name];
			parent::config($key, $user, $loaded_api, $generic);
		}


		public function api_list(){
			return parent::api_list($this->api_config);
		}






        /*
         *****************************************
         * 	Component to Main Generator 
         *****************************************
         */
		private function select_result($result){
			switch ($this->api_name){
                case 'digg': $result = $result->shorturls[0]->short_url; break;
				case 'supr': 
					$nurl = $this->url;
					$result = $result->results->$nurl->shortUrl; 
					break;
				default: break;
			}
			return $result;
		}






        /*
         *****************************************
         * 	Main Generator 
         *****************************************
         */
		public function generate($url){
			$this->url = $url;

			if ($this->url){
				$request_url = str_replace('[url]', $this->cleanurl(), $this->loaded_api['endpoint']);
				$post_opt = array();

				if ( $this->loaded_api['ua'] ){
					$post_opt['useragent'] = $ua_string; 
				}

				//special cases using generic
				switch($this->api_name){
					case 'interdose':
						$request_url = str_replace('[service]',$this->generic['service'], $request_url);
						break;
					default: break;
				}




				//If Plugin not Live, terminate here.
				if ($this->status == 'dev'){
					return $request_url;
				}


				$override = ( empty($this->loaded_api['override']) ) ? false : true;
					
				$result = parent::request_gateway($request_url, $override, $this->loaded_api['method'], $post_opt);


				//result processing
				switch ($this->loaded_api['format']){
					case 'json': 
						$result = parent::json_process($result);					
						$result = $this->select_result($result); 
						break;
					default: break;
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
