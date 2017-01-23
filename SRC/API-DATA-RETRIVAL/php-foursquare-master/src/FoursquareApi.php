<?php
/**
 * This file has been changed to serve better the needs of dbproject17
 */

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
     * @param bool|string $requestsOutputFile
     * @param bool|string $failsOutputFile
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
        $this->requestsOutput = fopen($requestsOutputFile,'a');
		$this->failsOutput = fopen($failsOutputFile,'a');
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
		$params['client_id'] = $this->ClientID;
		$params['client_id'] = $this->ClientID;
		$params['client_secret'] = $this->ClientSecret;
		$params['v'] = $this->Version;
		$params['locale'] = $this->ClientLanguage;
		
		$isFS = 1;
		$response = $this->Request($url,$params,$isFS); 
		
		return $response; // notice: this might be null
	}

	private function Request($url, $params=false,$isFS){ //$isFS = 0 -> google api, $isFS = 1-> fourSquare api
	
		$url = $this->MakeUrl($url,$params);
        $this->LastUrl = $url;

		return $this->requestOrFail($url,$isFS); // if fails 5 times returns null, else - return json string
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
		
		// bad response
		if($response==null)
			return null;
		
		$json = json_decode($response,true);
		
		// zero results
		if($json['status'] === "ZERO_RESULTS")
			return 0;
		
		// good response
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

	
	// verifirs that the string has legal JSON syntax
	function isValidJson($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	// try to make request unlit gets good response, limited to 5 attemps
	function requestOrFail($url,$isFS){
		$src = ($isFS ? 'fourSquareAPI' : 'googleMapsAPI');
		if(! empty($this->requestsOutput))
			fwrite($this->requestsOutput,$src.','.date("H:i:s").','.$this->LastUrl."\r\n");
		$response = file_get_contents($url);
		
		$cntFails = 0;
		while($this->isValidResponse($response,$isFS)===false and $cntFails <5){ //if fails - keep asking, 5 times total
			sleep(2);
			
			// write request
			if(!empty($this->requestsOutput))
				fwrite($this->requestsOutput,$src.','.$this->LastUrl."\r\n");
			
			// try again
			$response = file_get_contents($url);
			$cntFails++;
			
			//to fail list after 5 attempts
			if ($cntFails==5){ // give up
				if(!empty($this->failsOutput))
					fwrite($this->failsOutput,$src.','.$this->LastUrl."\r\n");
				return null;
			}
		}
		
		return $response;
	}
	
	// validates response by its type
	function isValidResponse($response,$isFS){
		if($isFS)
			return $this->isValidFSresponse($response);
		else
			return $this->isValidGoogleAPIresponse($response);
	}
	
	// validate FS response
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

	// validate google API response
	function isValidGoogleAPIresponse($response){
		
		if($this->isValidJson($response)){
			// good json
			$json = json_decode($response,true);
			if ( !isset($json['status']) || ($json['status'] !== "OK" && $json['status'] !== "ZERO_RESULTS")){
				// bad response 
				return false;
			}else{
				// good response (we'll consider zero results here)
				return true;
			}
		}else{
			// bad json
			return false;
		}
	}
}
