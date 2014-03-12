<?php

require_once 'DB/DalMySql.php';


class FacilityModel
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
   public function getFacilities()
   {		
		$app = \Slim\Slim::getInstance();		
		$result = DalMySql::getInstance()->getFacilities();		
		$app->response()->header('Content-Type', 'application/json');	
		$app->response()->status(200);			
		echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));		
   }
   
   
    // Create a new facility. ( Admin authentication )
   public function addFacility()
   { 
		$app = \Slim\Slim::getInstance();
		$headers = apache_request_headers();
		$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
		if ( DalMySql::getInstance()->isAdmin($authid)  )
		{
			$body = $app->request()->getBody();
			$input = json_decode($body); 		
			if ( isset($input->name) && !empty($input->name) &&  isset($input->iconNoItem) && isset($input->iconRed) &&  isset($input->iconOrange) &&  isset($input->iconGreen))
			{
				$result = DalMySql::getInstance()->addFacility($input->name,$input->iconNoItem,$input->iconRed,$input->iconOrange,$input->iconGreen);
				if ( $result >= 0 )
				{
						$app->response()->header('Content-Type', 'application/json');	
						$app->response()->status(201);			
						echo ("{\"id\":\"$result\"}");	
						return;
				}
				else
				{
					$app->response()->header('Content-Type', 'application/json');	
					$app->response()->status(400);			
					echo ('{"error":"Bad json input ( Bad values or facility name maybe already in use )"}');					
				}				
			}	
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(400);			
				echo ('{"error":"Bad json input"}');
			}				
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(401);	
			echo ('{"error":"Only an admin can add Facilities"}');		
		}
   }
   
   
   // Return basics informations of a facility
   public function getFacilityWithId($id)
   {		
		$app = \Slim\Slim::getInstance();		
		$result = DalMySql::getInstance()->getFacilityWithId($id);	
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
			echo ('{"error":"No facility were found"}');	
		} 
   }
   
   // Update facility
   public function  updateFacilityWithId($id)
   {		
		$app = \Slim\Slim::getInstance();	
		// Get the Authorization header to verify if client is admin
		$headers = apache_request_headers();
		$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
		if ( DalMySql::getInstance()->isAdmin($authid)  )
		{
			$body = $app->request()->getBody();
			$input = json_decode($body); 		
			if (  isset($input->name) && !empty($input->name) &&  isset($input->iconNoItem) && isset($input->iconRed) &&  isset($input->iconOrange) &&  isset($input->iconGreen)
			&& DalMySql::getInstance()->updateFacility($id,$input->name,$input->iconNoItem,$input->iconRed,$input->iconOrange,$input->iconGreen))
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(204);					
			}
			else
			{
				$app->response()->header('Content-Type', 'application/json');	
				$app->response()->status(400);	
				echo ('{"error":"Empty name or name already exists"}');			
			}	
		}
		else
		{
			$app->response()->header('Content-Type', 'application/json');	
			$app->response()->status(401);	
			echo ('{"error":"Bad Authorization ( Only an admin can modify a facility )"}');	
		}		
   }
   
   
   // Delete a facility
   public function  deleteFacilityWithId($id)
   {		
		$app = \Slim\Slim::getInstance();	
		// Get the Authorization header to verify if the user in an admin
		$headers = apache_request_headers();
		$authid = BasicHttpAuthentication::authenticate($headers['Authorization']);
		if ( DalMySql::getInstance()->isAdmin($authid)  )
		{
			if ( DalMySql::getInstance()->deleteFacility($id) )
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
			echo ('{"error":"Bad Authorization ( Only an admin can delete a facility )"}');	
		}		
   }
   
  
}