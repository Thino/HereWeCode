<?php
 
class BasicHttpAuthentication extends \Slim\Middleware
{


	protected $allowedRoutes;
	


    public function __construct()
	{
		$this->allowedRoutes = array(
		'GET_/rest/api/version/membr/',
		'brown',
		'caffeine'
		);	
	}
	
	
	
	
	
 
    // Bad Request man !
    public function deny_access() {
	
		$this->app->response()->status(401);     
              
    }   
	
	// Check members in database to authenticate a user
    public function authenticate($authString) {
		
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
		// No user or/and no password 
		if ( !isset($user) || !isset($pass) )				
			return -2;		
		
		$app = \Slim\Slim::getInstance();
		$result = Dal::getInstance()->select('MEMBER',"username = '$user' AND password = '$pass'",'idMember');		
		if ( $result->rowCount() > 0 )
		{
			$row = $result->fetch(PDO::FETCH_ASSOC);		
			return $row['idMember'];
			}
		// Bad user/password combinaison
		return -3;			
    }
	
	
  
  
    public function call()
    {		
	
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
			if ( isset($headers['Authorization']) && $this->authenticate($headers['Authorization']) >= 0)				
				$this->next->call();
			else
				$this->deny_access();	
					
	}			
}