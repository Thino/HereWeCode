<?php

require_once 'DB/Dal.php';


class MemberModel
{

   protected static $instance;   
   protected function __construct() { }  
   protected function __clone() { }    
  
  public static function getInstance()
  {	
    if (!isset(self::$instance)) 
    {
      self::$instance = new self; 
    }    
    return self::$instance;
  }
	
	
   // Return basics informations of a user
   public function getMemberWithId($id)
   {		
		$app = \Slim\Slim::getInstance();		
		$result = Dal::getInstance()->getMemberWithId($id);	
		if ( $result->rowCount() > 0 )
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(200);			
			echo json_encode($result->fetch(PDO::FETCH_ASSOC));	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(404);	
			echo ('{"error":"No user were found"}');	
		} 
   }
   
    // Update basics info of a member
   public function  updateMemberWithId($id)
   {		
		$app = \Slim\Slim::getInstance();	
		// Get the Authorization header to verify if user modify his own profile ( or if he is admin )
		$headers = apache_request_headers();
		$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
		if ( $authid == $id || Dal::getInstance()->isAdmin($authid)  )
		{
			$body = $app->request()->getBody();
			$input = json_decode($body); 		
			if ( isset($input->password) && isset($input->picture) && Dal::getInstance()->updateMember($id,$input->password,$input->picture) )
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(204);					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(520);	
				echo ('{"error":"Internal problem with the update"}');			
			}	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( You can only modify YOUR profile )"}');	
		}		
   }
   
     // Update basics info of a member
   public function  deleteMemberWithId($id)
   {		
		$app = \Slim\Slim::getInstance();	
		// Get the Authorization header to verify if user delete his own profile ( or if he is admin )
		$headers = apache_request_headers();
		$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
		if ( $authid == $id || Dal::getInstance()->isAdmin($authid)  )
		{
			if ( Dal::getInstance()->deleteMember($id) )
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(204);					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(520);	
				echo ('{"error":"Internal problem with the delete"}');			
			}	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( You can only delete YOUR profile )"}');	
		}		
   }
   
 
   
   
   
   
   // Allow to auth a member with username and password in JSON body
    public function authMember()
   { 
		$app = \Slim\Slim::getInstance();
		$body = $app->request()->getBody();
		$input = json_decode($body); 		
		if ( isset($input->username) && !empty($input->username) &&  isset($input->password) && !empty($input->password) )
		{
		    $result = Dal::getInstance()->authMember($input->username,$input->password);
			if ( $result->rowCount() > 0 )
			{
					$app->response()->header('Content-Type', 'application/json');	
					$app->response()->status(201);			
					echo json_encode($result->fetch(PDO::FETCH_ASSOC));	
					return;
			}			
		}	
		$app->response()->header('Content-Type', 'application/json');	
		$app->response()->status(401);			
		echo ('{"error":"Bad Credentials"}');	
   }
   
   // Create a new user ! Anyone can do it ( we must improve that later maybe with admin moderation )
    public function addMember()
   { 
		$app = \Slim\Slim::getInstance();
		$body = $app->request()->getBody();
		$input = json_decode($body); 		
		if ( isset($input->username) && !empty($input->username) &&  isset($input->password) && !empty($input->password) &&  isset($input->picture) )
		{
		    $result = Dal::getInstance()->addMember($input->username,$input->password,$input->picture);
			if ( $result >= 0 )
			{
					$app->response()->header('Content-Type', 'application/json');	
					$app->response()->status(201);			
					echo ("{\"id\":\"$result\"}");	
					return;
			}			
		}	
		$app->response()->header('Content-Type', 'application/json');	
		$app->response()->status(409);			
		echo ('{"error":"Bad json input ( Bad values or login maybe already in use )"}');	
   }

}

	


