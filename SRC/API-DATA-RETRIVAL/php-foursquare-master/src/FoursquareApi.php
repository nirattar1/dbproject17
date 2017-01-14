<?php
/**
 * FoursquareApi
 * A PHP-based Foursquare client library with a focus on simplicity and ease of integration
 * 
 * @package php-foursquare 
 * @author Stephen Young <me@hownowstephen.com>, @hownowstephen
 * @version 1.2.0
 * @license GPLv3 <http://www.gnu.org/licenses/gpl.txt>
 */

// Set the default version
// @TODO: Warning when the version becomes too out of date
define("DEFAULT_VERSION", "20170101");

// I have no explanation as to why this is necessary
define("HTTP_GET","GET");
define("HTTP_POST","POST");

/**
 * FoursquareApi
 * Provides a wrapper for making both public and authenticated requests to the
 * Foursquare API, as well as the necessary functionality for acquiring an 
 * access token for a user via Foursquare web authentication
 */

class FoursquareApiException extends Exception {}

class FoursquareApi {
	
	/** @var String $BaseUrl The base url for the foursquare API */
	private $BaseUrl = "https://api.foursquare.com/";
	/** @var String $AuthUrl The url for obtaining the auth access code */
	private $AuthUrl = "https://foursquare.com/oauth2/authenticate";
	/** @var String $AuthorizeUrl The url for obtaining an auth token, reprompting even if logged in */
	private $AuthorizeUrl = "https://foursquare.com/oauth2/authorize";
	/** @var String $TokenUrl The url for obtaining an auth token */
	private $TokenUrl = "https://foursquare.com/oauth2/access_token";
	
	// Edited Petr Babicka (babcca@gmail.com) https://developer.foursquare.com/overview/versioning
	/** @var String $Version YYYYMMDD */
	private $Version;

	/** @var String $ClientID */
	private $ClientID;
	/** @var String $ClientSecret */
	private $ClientSecret;
	/** @var String $RedirectUri */
	protected $RedirectUri;
	/** @var String $AuthToken */
	private $AuthToken;
	/** @var String $ClientLanguage */
	private $ClientLanguage;
    /** @var String[] $ResponseHeaders */
    public $ResponseHeaders = array();
    /** @var String last url sent */
    public $LastUrl;
    /** @var file handle for writing successul requests */
	private $requestsOutput;
	/** @var file handle for writing (appending) failed requests */
	private $failsOutput;
	/** @var boolean rate_limit_exceeded */
	public $rate_limit_exceeded;

    /**
     * Constructor for the API
     * Prepares the request URL and client api params
     * @param bool|String $client_id
     * @param bool|String $client_secret
     * @param string $redirect_uri
     * @param String $version Defaults to v2, appends into the API url
     * @param string $language
     * @param string $api_version https://developer.foursquare.com/overview/versioning
     */
	public function  __construct($client_id = false,$client_secret = false,$requestsOutputFile = false, $failsOutputFile=false, $redirect_uri='', $version='v2', $language='en', $api_version=DEFAULT_VERSION){
		$this->BaseUrl = "{$this->BaseUrl}$version/";
		$this->ClientID = $client_id;
		$this->ClientSecret = $client_secret;
		$this->ClientLanguage = $language;
		$this->RedirectUri = $redirect_uri;
        $this->Version = $api_version;
        $this->requestsOutput = fopen($requestsOutputFile,'a') or die ("can't open file: $requestsOutputFile");
		$this->failsOutput = fopen($failsOutputFile,'a') or die ("can't open file: $failsOutputFile");
		$this->rate_limit_exceeded = false;
	}
    
	public function setRedirectUri( $uri ) {
		$this->RedirectUri = $uri;
	}
	
	// Request functions
	
	/** 
	 * GetPublic
	 * Performs a request for a public resource
	 * @param String $endpoint A particular endpoint of the Foursquare API
	 * @param Array $params A set of parameters to be appended to the request, defaults to false (none)
	 * @return json string or null
	 */
	public function GetPublic($endpoint,$params=false){
		// Build the endpoint URL
		$url = $this->BaseUrl . trim($endpoint,"/");
		
		// Append the client details
		//$params['oauth_token'] = $this->ClientID; // oauth_token
		$params['client_id'] = $this->ClientID;
		$params['client_secret'] = $this->ClientSecret;
		$params['v'] = $this->Version;
		$params['locale'] = $this->ClientLanguage;
		
		$isFS = 1;
		$response = $this->Request($url,$params,$isFS); 
		
		return $response; // notice: this might be null
	}
	

	/**
	 * GetMulti
	 * Performs a request for up to 5 private or public resources
	 * @param Array $requests An array of arrays containing the endpoint and a set of parameters
	 * to be appended to the request, defaults to false (none)
	 * @param bool $POST whether or not to use a POST request, e.g.  for large request bodies.
	 * It does not allow you to call endpoints that mutate data.
	 */
	public function GetMulti($requests=false,$POST=false){
		$url = $this->BaseUrl . "multi";		
		$params = array();
		$params['oauth_token'] = $this->AuthToken;
		$params['v'] = $this->Version;		
		if (is_array($requests)){
			$request_queries = array();
			foreach($requests as $request) {
				$endpoint = $request['endpoint'];
				unset($request['endpoint']);
				$query = '/' . $endpoint;
					if (!empty($request)) $query .= '?' . http_build_query($request);
				$request_queries[] = $query;
			}
			$params['requests'] = implode(',', $request_queries);
		}
				if(!$POST) return $this->GET($url,$params);
		else return $this->POST($url,$params);
	}
    
	public function getResponseFromJsonString($json) {
		$json = json_decode( $json );
		if ( !isset( $json->response ) ) {
			throw new FoursquareApiException( 'Invalid response' );
		}

		// Better to check status code and fail gracefully, but not worried about it
		// ... REALLY, we should be checking the HTTP status code as well, not 
		// just what the API gives us in it's microformat
		/*
		if ( !isset( $json->meta->code ) || 200 !== $json->meta->code ) {
			throw new FoursquareApiException( 'Invalid response' );
		}
		*/
		return $json->response;
	}
	

	private function Request($url, $params=false,$isFS){ //$isFS = 0 -> google api, $isFS = 1-> fourSquare api
	
		$url = $this->MakeUrl($url,$params);
        $this->LastUrl = $url;

		return $this->requstOrFail($url,$isFS); // if fails 5 times returns null, else - return json string
	}

    /**
     * Callback function to handle header strings as they are returned by cUrl in the $this->Request() function
     * Parses header strings into key/value pairs and stores them in $ResponseHeaders array
     *
     * @param $ch
     * @param $header
     * @return int
     */
    private function ParseHeaders($ch, $header) {
        if (strpos($header, ':') !== false) {
            $header_split = explode(':', $header);
            $this->ResponseHeaders[strtolower(trim($header_split[0]))] = trim($header_split[1]);
        }
        return strlen($header);
    }

	/**
	 * GET
	 * Abstraction of the GET request
	 */
	private function GET($url,$params=false){
		return $this->Request($url,$params,HTTP_GET);
	}

	/**
	 * POST
	 * Abstraction of a POST request
	 */
	private function POST($url,$params=false){
		return $this->Request($url,$params,HTTP_POST);
	}

	
	
	
	/**
	 * getBoundingBox
	 * Leverages the google maps api to generate a bounding box (by northeast+southwest) for a given address
	 * packaged with FoursquareApi to facilitate locality searches.
	 * @param String $addr An address string accepted by the google maps api
	 * @return array(north, south, east, west) || NULL
	 */
	public function getBoundingBox($addr,$key){
		$geoapi = "https://maps.googleapis.com/maps/api/geocode/json";
		$params = array("address"=>$addr,"key"=>$key);
		$response = $this->Request($geoapi,$params,0);
		//TODO
		file_put_contents("bb_".$addr.".json",$response);

		if($response==null)
			return null;
		
		// good response
		$json = json_decode($response,true);

		$boundsArr = $json['results'][0]['geometry']['bounds'];
			
		$boundingBox = array('north_lat' => $boundsArr['northeast']['lat'],
							'south_lat' => $boundsArr['southwest']['lat'],
							'east_lon' => $boundsArr['northeast']['lng'],
							'west_lon' => $boundsArr['southwest']['lng']);
					 
		// make sure non of them non null
		foreach($boundingBox as $key=>$val){
			if(empty($val))
								
				return null;
		}
		
		// good
		return $boundingBox;
		
	}	
	
	/**
	 * MakeUrl
	 * Takes a base url and an array of parameters and sanitizes the data, then creates a complete
	 * url with each parameter as a GET parameter in the URL
	 * @param String $url The base URL to append the query string to (without any query data)
	 * @param Array $params The parameters to pass to the URL
	 */	
	private function MakeUrl($url,$params){
	    return trim($url) . '?' . http_build_query($params); 
	}
	
	// Access token functions
	
	/**
	 * SetAccessToken
	 * Basic setter function, provides an authentication token to GetPrivate requests
	 * @param String $token A Foursquare user auth_token
	 */
	public function SetAccessToken($token){
		$this->AuthToken = $token;
	}

	/**
	 * AuthenticationLink
	 * Returns a link to the Foursquare web authentication page.
	 * @param String $redirect The configured redirect_uri for the provided client credentials
	 */
	public function AuthenticationLink($redirect=''){
		if ( 0 === strlen( $redirect ) ) {
			$redirect = $this->RedirectUri;
		}
		$params = array("client_id"=>$this->ClientID,"response_type"=>"code","redirect_uri"=>$redirect);
		return $this->MakeUrl($this->AuthUrl,$params);
	}
	
  /**
   * AuthorizeLink
   * Returns a link to the Foursquare web authentication page. Using /authorize will ask the user to
   * re-authenticate their identity and reauthorize your app while giving the user the option to
   * login under a different account.
   * @param String $redirect The configured redirect_uri for the provided client credentials
   */
  public function AuthorizeLink($redirect=''){
    if ( 0 === strlen( $redirect ) ) {
      $redirect = $this->RedirectUri;
    }
    $params = array("client_id"=>$this->ClientID,"response_type"=>"code","redirect_uri"=>$redirect);
    return $this->MakeUrl($this->AuthorizeUrl,$params);
  }
  
	/**
	 * GetToken
	 * Performs a request to Foursquare for a user token, and returns the token, while also storing it
	 * locally for use in private requests
	 * @param $code The 'code' parameter provided by the Foursquare webauth callback redirect
	 * @param $redirect The configured redirect_uri for the provided client credentials
	 */
	public function GetToken($code,$redirect=''){
		if ( 0 === strlen( $redirect ) ) {
			// If we have to use the same URI to request a token as we did for 
			// the authorization link, why are we not storing it internally?
			$redirect = $this->RedirectUri;
		}
		$params = array("client_id"=>$this->ClientID,
						"client_secret"=>$this->ClientSecret,
						"grant_type"=>"authorization_code",
						"redirect_uri"=>$redirect,
						"code"=>$code);
		$result = $this->GET($this->TokenUrl,$params);
		$json = json_decode($result);
		
		// Petr Babicka Check if we get token
		if (property_exists($json, 'access_token')) {
			$this->SetAccessToken($json->access_token);
			return $json->access_token;
		}
		else {
			return 0;
		}
	}
	
	// TODO: delete this, we don't use it but just in case
	function getContent($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		// headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                       
			"Host:api.foursquare.com",                                                                                
			"Connection:keep-alive",
			"Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
			//"Accept-Encoding:gzip, deflate, sdch, br",
			"Accept-Language:en-US,en;q=0.8,he;q=0.6",
			"Cache-Control:max-age=0",
			"Upgrade-Insecure-Requests:1",
			"User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36",
			'Cookie:bbhive="XNY23PVORHRNEJTYJ11F5TXVISPADC%3A%3A1545561560"; __utma=51454142.580543967.1482489562.1483282174.1483622177.7; __utmc=51454142; __utmz=51454142.1482491951.2.2.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided)'
			));
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		
		$fp = fopen(dirname(__FILE__).'/errorlog.txt', 'w');
	   curl_setopt($ch, CURLOPT_STDERR, $fp);

		$result = curl_exec($ch);
		curl_close ($ch);
		
		
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($result, 0, $header_size);
		$body = substr($result, $header_size);
		
		$headers = explode( "\n",$result);
		
		
		$response = substr($headers[0], 9, 3);
		
		if ($response !== '200')
			return false;
		else return $body; 
			
	}
	
	function isValidJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	function requstOrFail($url,$isFS){
		$src = ($isFS ? 'fourSquareAPI' : 'googleMapsAPI');
		fwrite($this->requestsOutput,$src.','.date("H:i:s").','.$this->LastUrl."\r\n");
		$response = file_get_contents($url);
		
		$cntFails = 0;
		while($this->isValidResponse($response,$isFS)===false and $cntFails <5){ //if fails - keep asking, 5 times total
			sleep(2);
			
			// write echo request done
			fwrite($this->requestsOutput,$src.','.$this->LastUrl."\r\n");
			$response = file_get_contents($url);
			$cntFails++;
			
			//to fail list after 5 attempts
			if ($cntFails==5){ // give up
				fwrite($this->failsOutput,$src.','.$this->LastUrl."\r\n");
				return null;
			}
		}
		
		return $response;
	}
	
	function isValidResponse($response,$isFS){
		if($isFS)
			return $this->isValidFSresponse($response);
		else
			return $this->isValidGoogleAPIresponse($response);
	}
	
	function isValidFSresponse($response){
		if($this->isValidJson($response)){
			// good json
			$json = json_decode($response);
			if ( !isset($json->meta->code) || $json->meta->code !== 200){
				// bad response
				if( isset($json->meta->errorType) && $json->meta->errorType === "rate_limit_exceeded")
					$this->rate_limit_exceeded = true;
				
				return false;
			}else{
				// good response
				return true;
			}
		}else{
			// bad json
			return false;
		}
	}

	
	function isValidGoogleAPIresponse($response){
		
		if($this->isValidJson($response)){
			// good json
			$json = json_decode($response,true);
			if (!isset($json['status']) || $json['status'] !== "OK"){
				// bad response 
				return false;
			}else{
				// good response
				return true;
			}
		}else{
			// bad json
			return false;
		}
	}
}
