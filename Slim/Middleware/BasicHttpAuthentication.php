<?php

class BasicHttpAuthentication extends \Slim\Middleware
{


	protected $allowedRoutes;
	
	protected $version = 'v0.1';

	public function __construct()
	{
		$this->allowedRoutes = array(
		"GET_/rest/api/$this->version/member/",
		"POST_/rest/auth/session",
		"POST_/rest/api/$this->version/member",		
		"GET_/rest/api/$this->version/facility",
		"POST_/rest/api/$this->version/place/search",
		"GET_/rest/api/$this->version/place",
		"GET_/rest/api/$this->version/comment/",	
		"GET_/specs"
		);	
	}	

	// Bad Request man !
	public function deny_access() {		
		$app = \Slim\Slim::getInstance();			
		$app->response()->status(401);
		echo ('{"error":"Bad Request"}');		
	}  

	
	
	// Check members in database to authenticate a user
	public static function authenticate($authString) {
		
		// Parse of the basic authorization header
		try
		{		
			$auth =  substr($authString,6);
			$auth = base64_decode($auth);			
			$array=explode(":",$auth);
			$user = $array[0];
			$pass = base64_encode($array[1]);
			
			
		}
		catch (Exception $e )
		{
			//Bad Header
			return -1;
		}
		return 	DalMySql::getInstance()->getAuthUserId($user,$pass);	
	}
	
	


	public function call()
	{	
		//$this->next->call();  
		
		
		$route = $this->app->request()->getPathInfo();
		$requMeth = $this->app->request()->getMethod();				
		
		foreach ($this->allowedRoutes as &$val) {
			if ( $val === "" || strpos($requMeth.'_'.$route, $val) === 0 )
			{
				$this->next->call();
				return;
			}
		}				
		
		// Get the Authorization header
		$headers = apache_request_headers();	

				
		// Check Authentication
		if ( isset($headers['Authorization']) && BasicHttpAuthentication::authenticate($headers['Authorization']) >= 0)				
			$this->next->call();
		else
			$this->deny_access();				
		
	}			
}