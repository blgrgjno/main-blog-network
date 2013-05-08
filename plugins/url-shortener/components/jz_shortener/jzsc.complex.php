<?php
/*
 * JZSC Name: Component Pack 2
 * JZSC Description: This component adds support for multiple shortlinks services
 * classname: jzsc_complex
 * version: 1.0
 * link: http://wiki.fusedthought.com
 * author: Gerald Yeo <contact@fusedthought.com>
 */



/*
	Full Description:
		Contains slightly more complex shortening services.
		Results by these services are either JSON or XML or requires special parsing
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

if ( !class_exists('jzsc_complex') ) :
	class jzsc_complex extends jzsc_shared {




/*
 *****************************************
 *
 *	Class Variables
 *
 *****************************************
 */


		private $pingfmAPIKEY = 'f51e33510d3cbe2ff1e16a4a4897f099';
		private $api_config = array(
								//1 = authuser

								//2 = authkey
								'cligs'			=> array(
														'name' 		=>	'Cli.gs',
														'endpoint' 	=> 	'http://cli.gs/api/v1/cligs/create?url=[url]&key=[key]&appid=ftsplugin',
														'format' 	=> 	'', 
														'ua' 		=> 	FALSE, 
														'method' 	=> 	'GET', 
														'type' 		=> 	2
														), 
			
								//3 = authuser/key
								'supr'			=> array(
														'name'		=>	'Su.pr (by StumbleUpon)',
														'endpoint'	=> 	'http://su.pr/api/shorten?longUrl=[url]&login=[user]&apiKey=[key]', 
														'format'	=> 	'json',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'GET',  
														'type' 		=> 	3,
														'sticky'	=> 	TRUE,
														),

								'interdose'			=> array(
														'name'		=>	'Interdose',
														'endpoint'	=> 	'http://api.interdose.com/api/shorturl/v1/shorten?service=[service]&url=[url]&user=[user]&pass=[key]', 
														'format'	=> 	'txt',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'GET',  
														'type' 		=> 	3,
														),
								//4 = requser

								//5 = reqkey
								'awesm'			=> array(
														'name' 		=>	'awe.sm',
														'endpoint' 	=> 	'http://create.awe.sm/url.txt|version=1&target=[url]&share_type=other&create_type=api&api_key=[key]', 
														'format'	=> 	'alt-txt',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'POST',  
														'type' 		=> 	5,
														),
								'pingfm'		=> array(
														'name' 		=>	'ping.fm',
														'endpoint' 	=> 	'http://api.ping.fm/v1/url.create|user_app_key=[key]&long_url=[url]', 
														'format' 	=> 	'pingfm-xml',
														'override'	=>	TRUE,  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'POST',  
														'type' 		=> 	NULL, //5, Developer Key Invalid... so suspended 
														),
								'dlvrit'			=> array(
														'name' 		=>	'dlvr.it',
														'endpoint' 	=> 	'http://api.dlvr.it/1/shorten.txt|key=[key]&url=[url]', 
														'format'	=> 	'alt-txt',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'POST',  
														'type' 		=> 	5,
														),
								//6 = requser/key
								'snipurl'		=> array(
														'name' 		=>	'Snipurl',
														'endpoint' 	=> 	'http://snipurl.com/site/getsnip|snipuser=[user]&snipapi=[key]&snipformat=simple&sniplink=[url]&snipaction=CREATE', 
														'format'	=> 	'alt-txt',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'POST',  
														'type' 		=> 	6,
														'sticky'	=> 	TRUE,
														'override' 	=>	TRUE,
														),
								'snurl'			=> array(
														'name' 		=>	'Snurl',
														'endpoint'	=> 	'http://snurl.com/site/getsnip|snipuser=[user]&snipapi=[key]&snipformat=simple&sniplink=[url]&snipaction=CREATE', 
														'format' 	=> 	'alt-txt',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'POST',  
														'type' 		=> 	6,
														'sticky'	=> 	TRUE,
														'override' 	=>	TRUE,
														),
								'snipr'			=> array(
														'name' 		=>	'Snipr',
														'endpoint' 	=> 	'http://snipr.com/site/getsnip|snipuser=[user]&snipapi=[key]&snipformat=simple&sniplink=[url]&snipaction=CREATE', 
														'format'	=> 	'alt-txt',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'POST',  
														'type' 		=> 	6,
														'sticky'	=> 	TRUE,
														'override' 	=>	TRUE,
														),
								'snim'			=> array(
														'name' 		=>	'Sn.im',
														'endpoint' 	=> 	'http://sn.im/site/getsnip|snipuser=[user]&snipapi=[key]&snipformat=simple&sniplink=[url]&snipaction=CREATE', 
														'format' 	=> 	'alt-txt',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'POST',  
														'type' 		=> 	6,
														'sticky'	=> 	TRUE,
														'override' 	=>	TRUE,
														),
								'cllk'			=> array(
														'name' 		=>	'Cl.lk',
														'endpoint' 	=> 	'http://cl.lk/site/getsnip|snipuser=[user]&snipapi=[key]&snipformat=simple&sniplink=[url]&snipaction=CREATE', 
														'format' 	=> 	'alt-txt',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'POST',  
														'type' 		=>	6,
														'sticky'	=> 	TRUE,
														'override' 	=>	TRUE,
														),
								'jmp' 			=> array(
														'name' 		=>	'j.mp',
														'endpoint' 	=> 	'http://api.j.mp/v3/shorten?format=json&longUrl=[url]&login=[user]&apiKey=[key]', 
														'format'	=> 	'json',  
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'GET',  
														'type' 		=> 	6,
														'sticky'	=> 	TRUE,
														),
								'bitly'			=> array(
														'name' 		=>	'bit.ly',
														'endpoint' 	=> 	'http://api.bit.ly/v3/shorten?login=[user]&apiKey=[key]&longUrl=[url]&format=json', 
														'format' 	=> 	'json', 
														'ua' 		=> 	FALSE,  
														'method' 	=> 	'GET',  
														'type' 		=> 	6,
														'sticky'	=> 	TRUE,
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
            $this->jzsc_complex($service, $status);
        }
       
        //backward compatibility
        public function jzsc_complex($service = NULL, $status = 'dev'){
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

		public function config($key, $user, $generic){
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
				case 'supr': 
					$nurl = $this->url;
					$result = $result->results->$nurl->shortUrl; 
					break;
				case 'pingfm': $result = $result->short_url; break;
				case 'bitly': $result = $result->data->url; break;
               	case 'jmp': $result = $result->data->url; break;
				case 'smsh': $result = $result->body; break;	
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
					
				$request_url = $this->loaded_api['endpoint'];
				$post_opt = array();
				$continue = TRUE;
				$result = NULL;

			/********************* 
			 * check 2,3
			 ********************/
				if ( ( $this->loaded_api['type'] == 2 && empty($this->key) ) ||
					 ( $this->loaded_api['type'] == 3 && ( empty($this->key) || empty($this->user) ) )
 				){	
						$continue = FALSE; 
				}
				

				//2,3 fail case.
				if ($continue == FALSE){
					if ( class_exists('jzsc_simple') ) {
						$new_shortener = new jzsc_simple($this->api_name, $this->status);
						$new_shortener->config($this->key, $this->user, $this->generic);
						$result = $new_shortener->generate($this->url);
					}

				}


			/********************* 
			 * check 5,6
			 ********************/

				if ( ( $this->loaded_api['type'] == 5 && empty($this->key) ) ||
					 ( $this->loaded_api['type'] == 6 && ( empty($this->key) || empty($this->user) ) )
	 			){	
						$continue = FALSE; 
				}



			/********************* 
			 * ALL CASES PASS
			 ********************/	

				if ($continue == TRUE){

					$body = '';
					$post_opt['useragent'] = ( $this->loaded_api['ua'] ) ? $ua_string : NULL ;


					//forming the request variables
					switch ($this->loaded_api['format']){
						case 'alt-txt' :
						case 'alt-json':
						case 'alt-xml':
							//separate query section from endpoint
							$query = explode("|", $request_url);

							//reassigning
							$request_url = $query[0];
							$body = $query[1];

							$body  = str_replace('[user]',$this->user, $body );
							$body  = str_replace('[key]',$this->key, $body );
							$body  = str_replace('[url]', $this->cleanurl(), $body);

							//assign result processing path
							$type = explode('-',$this->loaded_api['format']);
							$type = $type[1];

							break;

						case 'pingfm-xml':
							$query = explode("|", $request_url);
							$request_url = $query[0];

							$query = explode("&", $query[1]);

							foreach ($query as $item){
								$item = explode('=', $item);
								$body[$item[0]] = $item[1];
								$body[$item[0]] = str_replace('[url]',$this->url, $body[$item[0]]);
								$body[$item[0]] = str_replace('[key]',$this->key, $body[$item[0]]);
							}

							$body['api_key'] = $this->pingfmAPIKEY;
							$type = 'xml';
							break;

						default: 
							$request_url = str_replace('[user]',$this->user, $request_url);
							$request_url  = str_replace('[key]',$this->key, $request_url);
							$request_url = str_replace('[url]', $this->cleanurl(), $request_url);	

							//assign result processing path
							$type = $this->loaded_api['format'];

							break;				
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
					
					$result =  parent::request_gateway($request_url, $override, $this->loaded_api['method'], $post_opt, $body);	

					//result processing
					switch ($type){	
						case 'json': 
							$result = parent::json_process($result);
							$result = $this->select_result($result); 
							break;
						case 'xml' :
							$result = parent::xml_process($result);
							$result = $this->select_result($result); 
							break;
						default: 
							break;
					}



					/********************* 
					 * SOME RESULT 
					 * MODIFICATION
					 ********************/
					switch ($this->api_name){
						case 'snipurl':
						case 'snurl':
						case 'cllk':
						case 'snipr':
						case 'snim':
							if (trim($result) == 'ERROR: PLEASE SPECIFY THE ACTION. CHECK <a href="http://snipurl.com/site/api">http://snipurl.com/site/api</a> FOR INSTRUCTIONS.'
								|| trim($result) == 'ERROR: PLEASE MAKE SURE THE URL YOU ENTER IS A VALID ONE.  CHECK <a href="http://snipurl.com/site/api">http://snipurl.com/site/api</a> FOR INSTRUCTIONS.'
							){
								$result = '';
							}
							break;
						default: break;
					}




				}//end PASS section






			/********************* 
			 *    RETURN OUT
			 ********************/

				//remove url request
				$this->url = '';

				return $result;
			}
		}







//end class  
    }
endif;
 
?>
